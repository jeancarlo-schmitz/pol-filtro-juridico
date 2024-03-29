<?php

namespace Application\Services;


use Http\Request;
use Infrastructure\Persistence\DAO\ApiManagerDAO;
use Infrastructure\Utils\JsonHelper;
use Exception;
use Infrastructure\Utils\Utils;

class ApiManagerService
{
    private $apiManagerDAO;
    public function __construct()
    {
        $this->apiManagerDAO = new ApiManagerDAO();
    }

    public function salvarRequisicaoRecebida($idCliente, Request $request){
        $parametros = $request->getAllParameters();
        $parametrosJson = JsonHelper::toJson($parametros);
        $endpoint = str_replace("/", "", $request->getUri());
        $ip = Utils::getIpAddr();

        try {
            return $this->apiManagerDAO->salvarRequisicaoRecebida($idCliente, $parametrosJson, $endpoint, $ip);
        } catch (Exception $e) {
            return null;
        }
    }

    public function marcarListaIdsComoConsumidas($idCliente, $idConsumoWebService, $listaIds, $campoId){
        $listaIds = $this->extrairApenasId($listaIds, $campoId);
        $listaIds = JsonHelper::toJson($listaIds);

        try {
            $this->apiManagerDAO->marcarListaIdsComoConsumidas($idCliente, $idConsumoWebService, $listaIds);
        } catch (Exception $e) {
            //só para não dar erro no consumo caso ocorra erro nesse ponto
        }
    }

    private function extrairApenasId($listaPublicacoesNaoImportadas, $campoId){
        $listaIdPublicacoes = [];
        foreach ($listaPublicacoesNaoImportadas as $dadosPublicacao){
            $listaIdPublicacoes[] = $dadosPublicacao[$campoId];
        }

        return $listaIdPublicacoes;
    }

    public function marcarSucessoNaRequisicao($idConsumoWebService){
        if(!empty($idConsumoWebService)){
            $this->apiManagerDAO->marcarSucessoNaRequisicao($idConsumoWebService);
        }
    }
}