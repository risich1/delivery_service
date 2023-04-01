<?php

namespace App\Http\Handlers;

use App\Http\Response\Response;
use JetBrains\PhpStorm\NoReturn;

class ResponseHandler {

    public function __construct(protected Response $response) {}

    #[NoReturn]
     public function handle(): string {
        foreach ($this->response->getHeaders() as $name =>  $value) {
            header($this->response->getHeaderLine($name));
        }
        http_response_code($this->response->getStatusCode());
        echo json_encode($this->response->getBody());
        exit();
    }

}
