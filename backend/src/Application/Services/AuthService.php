<?php

namespace Application\Services;


use Application\Exceptions\UnauthenticatedUserException;
use Domain\User;
use Infrastructure\Persistence\DAO\UserDAO;

class AuthService
{
    private $userDao;

    public function __construct()
    {
        $this->userDao = new UserDAO();
    }

    public function authenticate($username, $password)
    {
        $this->validateCredentials($username, $password);

        $user = $this->userDao->findUserByUsername($username);

        $this->validateAuthentication($user, $password);

        return $user;
    }

    private function validateCredentials($username, $password)
    {
        if (empty($username)) {
            throw new UnauthenticatedUserException("'username' não informado");
        }

        if (empty($password)) {
            throw new UnauthenticatedUserException("'password' não informado");
        }
    }

    private function validateAuthentication(?User $user, $password){
        if($user === null){
            throw new UnauthenticatedUserException('Usuário Inválido');
        }

        $this->validatePassword($password, $user->getPassword());
    }

    private function validatePassword(string $providedPassword, string $storedPassword)
    {
        if ($providedPassword !== $storedPassword) {
            throw new UnauthenticatedUserException('Senha Inválida');
        }
    }

    public function verificaSeClientePodeConsumirPublicacao(User $user){

        if(!$user->getConsomePublicacao()){
            throw new UnauthenticatedUserException('Seu Usuário não pode consumir publicações');
        }
    }
}