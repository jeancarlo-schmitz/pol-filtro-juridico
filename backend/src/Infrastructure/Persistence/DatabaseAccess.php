<?php
namespace Infrastructure\Persistence;

use Exception;

class DatabaseAccess {
    private $con;
    private $fetchMode = PGSQL_ASSOC;

    public function __construct() {
        $this->con = $this->doConnect();
    }

    public function BeginTrans(){
        pg_query($this->con, "BEGIN");
    }

    public function CommitTrans(){
        pg_query($this->con, "COMMIT");
    }


    public function RollbackTrans(){
        pg_query($this->con, "ROLLBACK");
    }

    public function Execute($sql, $params = []) {

        $res = pg_query_params($this->con, $sql, $params);

        if ($res === false) {
            $error_message = pg_result_error_field(pg_get_result($this->con), PGSQL_DIAG_MESSAGE_PRIMARY);
            $error_sqlstate = pg_result_error_field(pg_get_result($this->con), PGSQL_DIAG_SQLSTATE);

            if(IS_DEV) {
                throw new Exception("Erro no SQLSTATE: $error_sqlstate,\n Mensagem: $error_message,\n SQL: {$sql}");
            }else{
                throw new Exception('Erro ao executar a consulta: ' . pg_last_error($this->con));
            }
//            throw new Exception('Erro ao executar a consulta: ' . pg_last_error($this->con));
        }
        return $res;
    }

    public function GetOneRow($sql, $params = []) {
        $res = $this->execute($sql, $params);

        $numRows = pg_numrows($res);

        $array = array();
        if($numRows > 0) {
            $array = pg_fetch_array($res, 0, $this->fetchMode);
        }

        return $array;
    }

    public function GetArray($sql, $params = []) {
        $res = $this->execute($sql, $params);

        $array = pg_fetch_all($res,  $this->fetchMode);

        return $array;
    }

    public function GetOne($sql, $params = []) {
        $res = $this->execute($sql, $params);
        $row = pg_fetch_row($res);

        if(is_array($row) && !empty($row)) {
            return $row[0];
        }else{
            return '';
        }
    }

    public function setFetchMode($fetchMode){
        switch ($fetchMode) {
            case 'assoc':
                $this->fetchMode = PGSQL_ASSOC;
            case 'num':
                $this->fetchMode = PGSQL_NUM;
            case 'both':
                $this->fetchMode = PGSQL_BOTH;
            default:
                $this->fetchMode = PGSQL_ASSOC;
        }
    }

    private function doConnect() {
        $configFile = './config/database.ini';

        if (!file_exists($configFile)) {
            throw new Exception("Arquivo de configuração '$configFile' não encontrado.");
        }

        $config = parse_ini_file($configFile);
        if(IS_DEV) {
            $requiredKeys = ['host_dev', 'dbname_dev', 'user_dev', 'password_dev'];
        }else{
            $requiredKeys = ['host', 'dbname', 'user', 'password'];
        }

        foreach ($requiredKeys as $key) {
            if (!array_key_exists($key, $config)) {
                throw new Exception("Configuração '$key' não encontrada no arquivo '$configFile'.");
            }
        }

        if(IS_DEV) {
            $connectionString = "host={$config['host_dev']} port={$config['port']} dbname={$config['dbname_dev']} user={$config['user_dev']} password={$config['password_dev']}";
        }else{
            $connectionString = "host={$config['host']} port={$config['port']} dbname={$config['dbname']} user={$config['user']} password={$config['password']}";
        }

        $con = pg_connect($connectionString);
        if (!$con) {
            pred($con);
            throw new Exception("Falha na conexão com o banco de dados.");
        }

        return $con;
    }
}