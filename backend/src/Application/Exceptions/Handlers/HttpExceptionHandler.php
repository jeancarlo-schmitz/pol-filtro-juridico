<?php

namespace Application\Exceptions\Handlers;

use Application\Exceptions\Constants\HttpExceptionConstants;
use Application\Exceptions\MethodNotAllowedException;
use Application\Exceptions\NotFoundException;
use Application\Exceptions\UnauthenticatedUserException;
use Exception;
use Http\Response;

class HttpExceptionHandler
{

    public function canHandle(Exception $e)
    {
        if ($e instanceof NotFoundException) {
            return true;
        }
        if ($e instanceof MethodNotAllowedException) {
            return true;
        }

        return false;
    }

    public function handle(Exception $e)
    {
        if ($e instanceof NotFoundException) {
            return new Response('', $e->getMessage(), $e->getCode());
        }
        if ($e instanceof MethodNotAllowedException) {
            return new Response('', $e->getMessage(), $e->getCode());
        }

        if ($e instanceof UnauthenticatedUserException) {
            return new Response($e->getMessage(), HttpExceptionConstants::UNAUTHORIZED_MESSAGE, $e->getCode());
        }
//
//        if ($e instanceof UnauthorizedException) {
//            return new Response('', ErrorMessage::UNAUTHORIZED, HttpStatus::UNAUTHORIZED);
//        }
//
//        if ($e instanceof ForbiddenException) {
//            return new Response('', ErrorMessage::FORBIDDEN, HttpStatus::FORBIDDEN);
//        }
//
//        if ($e instanceof ConflictException) {
//            return new Response('', ErrorMessage::CONFLICT, HttpStatus::CONFLICT);
//        }
    }
}