<?php

namespace Application\Services;


use Application\DTOs\publicacao\PublicacaoDto;
use Application\DTOs\publicacao\QuantitativoPublicacaoDto;
use Domain\User;
use Infrastructure\Persistence\DAO\PublicacaoDAO;
use Infrastructure\Utils\Utils;

class PublicacaoService
{

    private $publicacaoDao;

    public function __construct()
    {
        $this->publicacaoDao = new PublicacaoDAO();
    }

    public function getListaPublicacoesNaoImportadas(User $user){
        $idCliente = $user->getId();
        $qtdDiasConsomeRetroativo = $user->getDiasConsomeRetroativo();

        $publicacoesRawData = $this->publicacaoDao->getListaPublicacoesNaoImportadasByIdCliente($idCliente, $qtdDiasConsomeRetroativo);

        $listaPublicacoesNaoImportadas = $this->getListaPublicacoesOrganizadas($publicacoesRawData);

        return PublicacaoDto::toArrayList($listaPublicacoesNaoImportadas);
    }

    public function getListaPublicacaoByData($idUsuario, $dataFiltro){
        $publicacoesRawData = $this->publicacaoDao->getListaPublicacaoByData($idUsuario, $dataFiltro);

        $listaPublicacoesNaoImportadas = $this->getListaPublicacoesOrganizadas($publicacoesRawData);

        return PublicacaoDto::toArrayList($listaPublicacoesNaoImportadas);
    }

    private function getListaPublicacoesOrganizadas($publicacoesRawData){
        $listaPublicacoesNaoImportadas = [];

        if(!empty($publicacoesRawData)) {
            foreach ($publicacoesRawData as $publicacaoData) {
                $publicacaoData = $this->ajustarDadosPublicacao($publicacaoData);

                $listaPublicacoesNaoImportadas[] = PublicacaoDto::fromArray($publicacaoData);
            }
        }

        return $listaPublicacoesNaoImportadas;
    }

    private function ajustarDadosPublicacao(array $publicacaoData)
    {
        $publicacaoData['dataPublicacao']       = date('Y-m-d\TH:i:s', strtotime($publicacaoData['dataPublicacao']));
        $publicacaoData['dataDisponibilizacao'] = date('Y-m-d\TH:i:s', strtotime($publicacaoData['dataDisponibilizacao']));
        $publicacaoData['numeroProcesso'] = Utils::ajustarMascaraNumeroProcessoCNJ($publicacaoData['numeroProcesso']);

        return $publicacaoData;
    }

    public function confirmarRecebimentoPublicacoes($idCliente, $listaIdPublicacoes){
        $listaIdPublicacoesConcatenadas = $this->getListaIdsPublicacoesConcatenadas($listaIdPublicacoes);

        $listaMd5PublicacoesMarcarConfirmada = $this->publicacaoDao->getListaMd5PublicacaoByIdPublicacao($listaIdPublicacoesConcatenadas);

        $this->publicacaoDao->confirmarRecebimentoPublicacoesByIdClienteAndIdPublicacao($idCliente, $listaMd5PublicacoesMarcarConfirmada);
    }

    private function getListaIdsPublicacoesConcatenadas($listaIdPublicacoes){
        if(is_array($listaIdPublicacoes)){
            return implode(",", $listaIdPublicacoes);
        }else{
            return $listaIdPublicacoes;
        }
    }

    public function getQuantitativoPublicacoesImportadasENaoImportadasByData($idCliente, $dataFiltro){
        $quantitativosPublicacoes = $this->publicacaoDao->getQuantitativoPublicacoesImportadasENaoImportadasByData($idCliente, $dataFiltro);

        return QuantitativoPublicacaoDto::toArray($quantitativosPublicacoes);
    }
}