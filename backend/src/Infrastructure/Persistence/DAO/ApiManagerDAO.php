<?php

namespace Infrastructure\Persistence\DAO;


use Infrastructure\Persistence\DatabaseAccess;

class ApiManagerDAO extends DatabaseAccess
{

    public function marcarListaIdsComoConsumidas($idCliente, $idConsumoWebService, $listaIds){

        $sql = "INSERT INTO 
                  web_service.log_entrega_webservice
                (
                  lew_cli_id,
                  lew_dados,
                  lew_lcw_id
                )
                VALUES (
                  {$idCliente},
                  '{$listaIds}',
                  {$idConsumoWebService}
                );";

        $this->Execute($sql);
    }

    public function salvarRequisicaoRecebida($idCliente, $parametrosJson, $endpoint, $ip){
        $sql = "INSERT INTO 
                  web_service.log_consumo_webservice
                (
                  lcw_cli_id,
                  lcw_endpoint,
                  lcw_filtros,
                  lcw_ip
                )
                VALUES (
                  {$idCliente},
                  '{$endpoint}',
                  '{$parametrosJson}',
                  '{$ip}'
                ) RETURNING lcw_id;";

        return $this->GetOne($sql);
    }

    public function marcarSucessoNaRequisicao($idConsumoWebService){
        $sql = "UPDATE web_service.log_consumo_webservice
                 SET lcw_sucesso = true
                WHERE lcw_id = {$idConsumoWebService};";

        $this->Execute($sql);
    }

}