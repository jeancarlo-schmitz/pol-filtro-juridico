<?php

namespace Domain;

class User
{

    private $id;
    private $username;
    private $password;
    private $consomePublicacao;
    private $consomeDistribuicao;
    private $consomeMovimentacao;
    private $diasConsomeRetroativo;

    /**
     * User constructor.
     * @param $id
     * @param $username
     * @param $password
     * @param $consomePublicacao
     * @param $consomeDistribuicao
     * @param $consomeMovimentacao
     * @param $diasConsomeRetroativo
     */
    public function __construct($id, $username, $password, $consomePublicacao, $consomeDistribuicao, $consomeMovimentacao, $diasConsomeRetroativo)
    {
        $this->id                    = $id;
        $this->username              = $username;
        $this->password              = $password;
        $this->consomePublicacao     = $consomePublicacao;
        $this->consomeDistribuicao   = $consomeDistribuicao;
        $this->consomeMovimentacao   = $consomeMovimentacao;
        $this->diasConsomeRetroativo = $diasConsomeRetroativo;
    }


    /**
     * @return mixed
     */
    public function getDiasConsomeRetroativo()
    {
        return $this->diasConsomeRetroativo;
    }

    /**
     * @param mixed $diasConsomeRetroativo
     */
    public function setDiasConsomeRetroativo($diasConsomeRetroativo)
    {
        $this->diasConsomeRetroativo = $diasConsomeRetroativo;
    }


    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @param mixed $username
     */
    public function setUsername($username)
    {
        $this->username = $username;
    }

    /**
     * @return mixed
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param mixed $password
     */
    public function setPassword($password)
    {
        $this->password = $password;
    }

    /**
     * @return mixed
     */
    public function getConsomePublicacao()
    {
        return $this->consomePublicacao === 't';
    }

    /**
     * @param mixed $consomePublicacao
     */
    public function setConsomePublicacao($consomePublicacao)
    {
        $this->consomePublicacao = $consomePublicacao;
    }

    /**
     * @return mixed
     */
    public function getConsomeDistribuicao()
    {
        return $this->consomeDistribuicao === 't';
    }

    /**
     * @param mixed $consomeDistribuicao
     */
    public function setConsomeDistribuicao($consomeDistribuicao)
    {
        $this->consomeDistribuicao = $consomeDistribuicao;
    }

    /**
     * @return mixed
     */
    public function getConsomeMovimentacao()
    {
        return $this->consomeMovimentacao === 't';
    }

    /**
     * @param mixed $consomeMovimentacao
     */
    public function setConsomeMovimentacao($consomeMovimentacao)
    {
        $this->consomeMovimentacao = $consomeMovimentacao;
    }


}