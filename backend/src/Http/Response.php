<?php

namespace Http;

use Infrastructure\Utils\JsonHelper;

class Response
{
    private $body;
    private $message;
    private $statusCode;
    private $justBody;
    private $headers = array();

    public function __construct($body, string $message, int $statusCode = 200, $justBody = false, $headers = array())
    {
        $this->body = $body;
        $this->message = $message;
        $this->statusCode = $statusCode;
        $this->justBody = $justBody;

        $this->headers[] = JsonHelper::getHeader();
        $this->setHeader($headers);
    }

    public function getBody(): string
    {
        return $this->body;
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }

    public function setHeader($value)
    {
        if(!empty($value)) {
            if(is_array($value)){
                $this->headers = array_merge($this->headers, array_values($value));
            }else{
                $this->headers[] = $value;
            }
        }
    }

    public function send()
    {
        http_response_code($this->statusCode);

        $this->putHeaders();

        $this->generateResponse();
        exit;
    }

    public function generateResponse()
    {
        $response                        = array();
        if(!$this->justBody) {
            $response['success']     = $this->statusCode !== 200 ? false : true;
            $response['status_code'] = $this->statusCode;

            if(!empty($this->message)) {
                $response['response']['message'] = $this->message;
            }

            if(!empty($this->body)) {
                $response['response']['data'] = $this->body;
            }
        }else{
            $response = $this->body;
        }


        echo JsonHelper::toJsonUtf8($response);
    }

    private function putHeaders(){
        if(!empty($this->headers)) {
            foreach ($this->headers as $aux) {
                header("$aux");
            }
        }
    }
}