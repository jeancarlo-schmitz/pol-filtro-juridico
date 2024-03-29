<?php
/**
 * Created by PhpStorm.
 * User: jean.schmitz
 * Date: 18/02/2024
 * Time: 16:25
 */

namespace Infrastructure\Persistence\DAO;


use Application\DTOs\UserDto;
use Infrastructure\Persistence\DatabaseAccess;

class UserDAO extends DatabaseAccess
{
    public function findUserByUsername($username){
        $sql = "SELECT cw_cli_id as id,
                       cw_login as username,
                       cw_senha as password,
                       cw_consome_publicacao as \"consomePublicacao\",
                       cw_consome_distribuicao as \"consomeDistribuicao\",
                       cw_consome_movimentacao as \"consomeMovimentacao\",
                       cw_dias_retroativo as \"diasConsomeRetroativo\"
                FROM web_service.cliente_webservice
                WHERE cw_login = $1";

        $params = [$username];

        $userData = $this->GetOneRow($sql, $params);

        return $userData ? UserDto::fromArray($userData) : null;
    }

}