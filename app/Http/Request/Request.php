<?php

namespace App\Http\Request;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ServerRequestInterface;

class Request {

    protected array $headers;
    protected string $method;
    protected array $body;
    protected string $requestUri;

    public function __construct()
    {
        $this->headers = getallheaders();
        $this->method = $_SERVER['REQUEST_METHOD'];
        $this->body = json_decode(file_get_contents('php://input')) ?? [];
        $this->requestUri = $_SERVER['REQUEST_URI'];
    }

    protected array $user;

    public function getUser(): array {
        return $this->user;
    }

    public function setUser(array $user): self {
        $this->user = $user;
        return $this;
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

    public function getBody()
    {
        return $this->body;
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    public function getUri()
    {
        return $this->requestUri;
    }

}
