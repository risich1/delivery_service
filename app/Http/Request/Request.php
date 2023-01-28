<?php

namespace App\Http\Request;

use App\Exceptions\BadRequestException;
use App\Interface\IRequest;

class Request implements IRequest {

    protected array $headers;
    protected string $method;
    protected array $body;
    protected string $requestUri;
    protected array $requireBodyFields = [];
    protected array $requireHeaders = [];

    public function __construct()
    {
        $this->headers = getallheaders();
        $this->method = $_SERVER['REQUEST_METHOD'];
        $this->body = json_decode(file_get_contents('php://input'), TRUE) ?? [];
        $this->requestUri = $_SERVER['REQUEST_URI'];
    }

    /**
     * @throws BadRequestException
     */
    public function validate() {
        $this->validateRequireHeaders();
        $this->validateRequireBodyFields();
    }

    /**
     * @throws BadRequestException
     */
    public function validateRequireBodyFields() {
        if (count($this->requireBodyFields)) {
            foreach ($this->requireBodyFields as $requireBodyField) {
                if (!$this->hasBodyField($requireBodyField)) {
                    throw new BadRequestException("Field '$requireBodyField' is required");
                }
            }
        }
    }

    /**
     * @throws BadRequestException
     */
    public function validateRequireHeaders() {
        if (count($this->requireHeaders)) {
            foreach ($this->requireHeaders as $requireHeader) {
                if (!$this->hasHeader($requireHeader)) {
                    throw new BadRequestException("Header '$requireHeader' is required");
                }
            }
        }
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }

    public function hasHeader($name): bool
    {
        return isset($this->headers[$name]) && !empty($this->headers[$name]);
    }

    public function getHeader($name): string
    {
        return $this->headers[$name];
    }

    public function getBody(): array
    {
        return $this->body;
    }

    public function hasBodyField($name): bool
    {
        return isset($this->body[$name]) && !empty($this->body[$name]);
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    public function getUri(): string
    {
        return $this->requestUri;
    }

}
