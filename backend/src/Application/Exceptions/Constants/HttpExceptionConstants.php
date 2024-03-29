<?php

namespace Application\Exceptions\Constants;

class HttpExceptionConstants
{
    const OK_CODE = 200;
    const CREATED_CODE = 201;
    const NO_CONTENT_CODE = 204;
    const BAD_REQUEST_CODE = 400;

    const UNAUTHORIZED_CODE = 401;
    const UNAUTHORIZED_MESSAGE = 'Unauthorized';

    const FORBIDDEN_CODE = 403;
    const FORBIDDEN_MESSAGE = 'Forbidden';

    const NOT_FOUND_CODE = 404;
    const NOT_FOUND_MESSAGE = 'Method not found';

    const ROUTE_DUPLICATED_MESSAGE = "The route '%s' is duplicated.";

    const METHOD_NOT_ALLOWED_CODE = 405;
    const METHOD_NOT_ALLOWED_MESSAGE = 'Method Not Allowed';

    const CONFLICT_CODE = 409;
    const CONFLICT_MESSAGE = 'Conflict';

    const INTERNAL_SERVER_ERROR_CODE = 500;
    const INTERNAL_SERVER_ERROR_MESSAGE = 'Internal server error';

    const INVALID_INPUT = 'Invalid input';
}