<?php

namespace Application\Services;


use Application\DTOs\movimentacao\ListaMovimentacoesDto;
use Application\DTOs\movimentacao\MovimentacaoDto;
use Application\DTOs\movimentacao\MovimentacaoImportadaNaoImportadaDto;
use Application\DTOs\movimentacao\QuantitativoMovimentacaoDto;
use Domain\User;
use Infrastructure\Persistence\DAO\MovimentacaoDAO;
use Infrastructure\Utils\JsonHelper;
use Infrastructure\Utils\Utils;
use Exception;

class MovimentacaoService
{

    private $movimentacaoDao;
    private $listaTodosMovimentos = [];
    private $listaOrigemPrefixoNumeroProcesso = [];

    public function __construct()
    {
        $this->movimentacaoDao = new MovimentacaoDAO();
    }

    public function getMovimentacoesNaoImportadas(User $user){
        $idCliente = $user->getId();
        $qtdDiasConsomeRetroativo = $user->getDiasConsomeRetroativo();

        $filtros['qtdDiasConsomeRetroativo'] = $qtdDiasConsomeRetroativo;
        $filtros['tipo'] = 'nao_importadas';

        $movimentacoesRawData = $this->movimentacaoDao->getListaMovimentacoesNaoImportadas($idCliente, $filtros);
        $this->getListaOrigensPrefixoNumeroProcesso();

        $listaMovimentacoesNaoImportadas = $this->getListaMovimentacoesOrganizadas($movimentacoesRawData, MovimentacaoDto::class);

        return [ListaMovimentacoesDto::toArrayList($listaMovimentacoesNaoImportadas, MovimentacaoDto::class), $this->listaTodosMovimentos];
    }

    public function getMovimentacoesPorData($idCliente, $filtros){
        $this->validaFiltrosMovimentacaoPorData($filtros);

        $movimentacoesRawData = $this->movimentacaoDao->getListaMovimentacoesNaoImportadas($idCliente, $filtros);
        $this->getListaOrigensPrefixoNumeroProcesso();

        $listaMovimentacoesNaoImportadas = $this->getListaMovimentacoesOrganizadas($movimentacoesRawData, MovimentacaoImportadaNaoImportadaDto::class);

        return [ListaMovimentacoesDto::toArrayList($listaMovimentacoesNaoImportadas, MovimentacaoImportadaNaoImportadaDto::class), $this->listaTodosMovimentos];
    }

    public function getMovimentacoesPorNumeroProcesso($idCliente, $filtros){
        $this->validaFiltrosMovimentacaoPorNumeroProcesso($filtros);

        $movimentacoesRawData = $this->movimentacaoDao->getListaMovimentacoesNaoImportadas($idCliente, $filtros);
        $this->getListaOrigensPrefixoNumeroProcesso();

        $listaMovimentacoesNaoImportadas = $this->getListaMovimentacoesOrganizadas($movimentacoesRawData, MovimentacaoDto::class);

        return [ListaMovimentacoesDto::toArrayList($listaMovimentacoesNaoImportadas, MovimentacaoDto::class), $this->listaTodosMovimentos];
    }

    private function getListaOrigensPrefixoNumeroProcesso(){
        $listaOrigemPrefixoNumeroProcesso = $this->movimentacaoDao->getListaOrigensPrefixoNumeroProcesso();

        foreach ($listaOrigemPrefixoNumeroProcesso as $dadosOrigem){
            $this->listaOrigemPrefixoNumeroProcesso[$dadosOrigem['ori_prefixo']] = $dadosOrigem['ori_nome'];
        }
    }

    private function getListaMovimentacoesOrganizadas($movimentacoesRawData, $dtoType){
        $listaMovimentacoesNaoImportadas = [];

        if(!empty($movimentacoesRawData)) {
            foreach ($movimentacoesRawData as $movimentacaoData) {
                $movimentacaoAuxiliar = [];

                $movimentos = $this->getListaMovimentos($movimentacaoData['movimentos'], $dtoType);

                $movimentacaoAuxiliar['numeroProcesso']       = Utils::ajustarMascaraNumeroProcessoCNJ($movimentacaoData['numeroProcesso']);
                $movimentacaoAuxiliar['origem']               = $this->getOrigemByNumeroProcesso($movimentacaoData['numeroProcesso']);
                $movimentacaoAuxiliar['quantidadeMovimentos'] = count($movimentos);
                $movimentacaoAuxiliar['listaMovimentos']      = $movimentos;

                $listaMovimentacoesNaoImportadas[] = ListaMovimentacoesDto::fromArray($movimentacaoAuxiliar);
            }
        }

        return $listaMovimentacoesNaoImportadas;
    }

    private function getListaMovimentos($movimentacaoData, $dtoType)
    {
        $movimentacaoData = JsonHelper::jsonToArrayUtf8($movimentacaoData);
        $listaMovimentos = [];

        foreach ($movimentacaoData as $key => $dadosMovimento) {

            $dadosMovimento = $this->ajustarDadosMovimentacao($dadosMovimento);
            $this->listaTodosMovimentos[] = $dadosMovimento;
            $listaMovimentos[] = $dtoType::fromArray($dadosMovimento);
        }

        return $listaMovimentos;
    }

    private function ajustarDadosMovimentacao(array $movimentacaoData){
        $movimentacaoData['descricao'] = utf8_decode($movimentacaoData['descricao']);
        $movimentacaoData['evento'] = utf8_decode($movimentacaoData['evento']);
        $movimentacaoData['movimentadoPor'] = utf8_decode($movimentacaoData['movimentadoPor']);
        $movimentacaoData['numeroProcesso'] = Utils::ajustarMascaraNumeroProcessoCNJ($movimentacaoData['numeroProcesso']);

        return $movimentacaoData;
    }

    private function getOrigemByNumeroProcesso($numeroProcesso){
        $prefixo = substr($numeroProcesso, -4);
//        pre($this->listaOrigemPrefixoNumeroProcesso);
//        pre($prefixo);
//        pred($this->listaOrigemPrefixoNumeroProcesso[$prefixo]);
        return $this->listaOrigemPrefixoNumeroProcesso[$prefixo];
    }

    private function validaFiltrosMovimentacaoPorData($filtros){
        if(!$this->isFiltroTipoValido($filtros['tipo'])){
            throw new Exception("O Tipo '{$filtros['tipo']}' enviando não é um tipo valido!");
        }
    }

    private function isFiltroTipoValido($tipo){
        switch ($tipo){
            case 'importados':
                return true;
            case 'nao_importados':
                return true;
            case 'todos':
                return true;
            default:
                return false;
        }
    }

    private function validaFiltrosMovimentacaoPorNumeroProcesso($filtros){
        $dataInicio = $filtros['dataInicio'];
        $dataFinal  = $filtros['dataFinal'];

        if (!empty($dataInicio) && empty($dataFinal)) {
            throw new Exception("A data final não pode estar vazia quando a data de início está preenchida.");
        }

        if (empty($dataInicio) && !empty($dataFinal)) {
            throw new Exception("A data de início não pode estar vazia quando a data final está preenchida.");
        }

        if (!empty($dataInicio) && !empty($dataFinal)) {
            $dataInicioTimestamp = strtotime($dataInicio);
            $dataFinalTimestamp = strtotime($dataFinal);

            if ($dataInicioTimestamp > $dataFinalTimestamp) {
                throw new Exception("A data de início não pode ser maior que a data final.");
            }
        }
    }

    public function doConfirmarLeituraMovimentacoes($idCliente, $listaIdMovimentacoes){
        $listaIdMovimentacoesConcatenadas = $this->getListaIdsMovimentacoesConcatenadas($listaIdMovimentacoes);

        $this->movimentacaoDao->confirmarRecebimentoMovimentacaoByIdClienteAndIdMovimento($idCliente, $listaIdMovimentacoesConcatenadas);
    }

    private function getListaIdsMovimentacoesConcatenadas($listaIdDistribuicoes){
        if(is_array($listaIdDistribuicoes)){
            return implode(",", $listaIdDistribuicoes);
        }else{
            return $listaIdDistribuicoes;
        }
    }

    public function getQuantidadeMovimentacoesData($idCliente, $dataFiltro){
        $quantitativosMovimentacoes = $this->movimentacaoDao->getQuantidadeMovimentacoesData($idCliente, $dataFiltro);

        return QuantitativoMovimentacaoDto::toArray($quantitativosMovimentacoes);
    }
}