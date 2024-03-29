<?php

namespace Application\Services;


use Application\DTOs\distribuicao\DistribuicaoDto;
use Application\DTOs\distribuicao\DistribuicaoImportadaNaoImportadaDto;
use Application\DTOs\distribuicao\QuantitativoDistribuicaoDto;
use Domain\User;
use Infrastructure\Persistence\DAO\DistribuicaoDAO;
use Infrastructure\Utils\Utils;
use Exception;

class DistribuicaoService
{

    private $distribuicaoDao;

    public function __construct()
    {
        $this->distribuicaoDao = new DistribuicaoDAO();
    }

    public function getDistribuicoesNaoImportadas(User $user){
        $idCliente = $user->getId();
        $qtdDiasConsomeRetroativo = $user->getDiasConsomeRetroativo();

        $filtros['qtdDiasConsomeRetroativo'] = $qtdDiasConsomeRetroativo;
        $filtros['tipo'] = 'nao_importadas';

        $distribuicoesRawData = $this->distribuicaoDao->getListaDistribuicoesNaoImportadas($idCliente, $filtros);

        $listaDistribuicoesNaoImportadas = $this->getListaDistribuicoesOrganizadas($distribuicoesRawData, DistribuicaoDto::class);

        return DistribuicaoDto::toArrayList($listaDistribuicoesNaoImportadas);
    }

    public function getDistribuicoesPorData($idCliente, $filtros){
        $this->validaFiltrosDistribuicao($filtros);
        $distribuicoesRawData = $this->distribuicaoDao->getListaDistribuicoesNaoImportadas($idCliente, $filtros);

        $listaDistribuicoesNaoImportadas = $this->getListaDistribuicoesOrganizadas($distribuicoesRawData, DistribuicaoImportadaNaoImportadaDto::class);

        return DistribuicaoImportadaNaoImportadaDto::toArrayList($listaDistribuicoesNaoImportadas);
    }

    private function getListaDistribuicoesOrganizadas($distribuicoesRawData, $dtoType){
        $listaDistribuicoesNaoImportadas = [];

        if(!empty($distribuicoesRawData)) {
            foreach ($distribuicoesRawData as $distribuicaoData) {
                $distribuicaoData = $this->ajustarDadosDistribuicao($distribuicaoData);

                $listaDistribuicoesNaoImportadas[] = $dtoType::fromArray($distribuicaoData);
            }
        }

        return $listaDistribuicoesNaoImportadas;
    }

    private function ajustarDadosDistribuicao(array $distribuicaoData)
    {
        $dataAudiencia                      = Utils::extractDate($distribuicaoData['dataAudiencia']);
        $linkAcessoInicial                  = $this->gerarLinkDonwloadArquivoDistribuicao($distribuicaoData['linkAcessoInicial']);

        $distribuicaoData['dataDistribuicao']  = date('Y-m-d\TH:i:s', strtotime($distribuicaoData['dataDistribuicao']));
        $distribuicaoData['dataCaptura']       = date('Y-m-d\TH:i:s', strtotime($distribuicaoData['dataCaptura']));
        $distribuicaoData['dataAudiencia']     = date('Y-m-d\TH:i:s', strtotime($dataAudiencia));
        $distribuicaoData['dataImportada']     = date('Y-m-d\TH:i:s', strtotime($distribuicaoData['dataImportada']));
        $distribuicaoData['tipoOcorrencia']    = Utils::explodeString($distribuicaoData['tipoOcorrencia'], ['|', '«']);
        $distribuicaoData['reu']               = Utils::explodeString($distribuicaoData['reu'], ['|', '«']);
        $distribuicaoData['advogadoReu']       = Utils::explodeString($distribuicaoData['advogadoReu'], ['|', '«']);
        $distribuicaoData['autor']             = Utils::explodeString($distribuicaoData['autor'], ['|', '«']);
        $distribuicaoData['advogadoAutor']     = Utils::explodeString($distribuicaoData['advogadoAutor'], ['|', '«']);
        $distribuicaoData['linkAcessoInicial'] = $linkAcessoInicial;

        return $distribuicaoData;
    }

    private function validaFiltrosDistribuicao($filtros){
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

    private function gerarLinkDonwloadArquivoDistribuicao($linkAcessoInicial){

        return 'linkDonwloadAcessoInicial';
    }

    public function doConfirmarLeituraDistribuicoes($idCliente, $listaIdDistribuicoes){
        $listaIdDistribuicoesConcatenadas = $this->getListaIdsDistribuicoesConcatenadas($listaIdDistribuicoes);

        $listaMd5DistribuicoesMarcarConfirmada = $this->distribuicaoDao->getListaMd5DistribuicaoByIdDistribuicao($listaIdDistribuicoesConcatenadas);

        $this->distribuicaoDao->confirmarRecebimentoDistribuicaoByIdClienteAndDisMd5($idCliente, $listaMd5DistribuicoesMarcarConfirmada);
    }

    private function getListaIdsDistribuicoesConcatenadas($listaIdDistribuicoes){
        if(is_array($listaIdDistribuicoes)){
            return implode(",", $listaIdDistribuicoes);
        }else{
            return $listaIdDistribuicoes;
        }
    }

    public function getQuantidadeDistribuicoesData($idCliente, $dataFiltro){
        $quantitativosDistribuicoes = $this->distribuicaoDao->getQuantidadeDistribuicoesData($idCliente, $dataFiltro);

        return QuantitativoDistribuicaoDto::toArray($quantitativosDistribuicoes);
    }
}