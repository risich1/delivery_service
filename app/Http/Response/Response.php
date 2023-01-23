<?php

namespace App\Http\Response;

class Response {

    protected int $statusCode;
    protected array $headers = [
        "Content-Type" => "application/json; charset=utf-8"
    ];

    protected array $body = [];

    public function __construct(array $body = [], array $headers = [], int $statusCode = 200)
    {
        $this->statusCode = $statusCode;
        if ($headers) $this->headers = $headers;
        if ($body) $this->body = $body;
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }

    public function hasHeader($name): bool
    {
        return isset($this->headers[$name]);
    }

    public function getHeader($name): string
    {
        return $this->headers[$name];
    }

    public function getHeaderLine($name): string
    {
        return $name . ": " . $this->headers[$name];
    }

    public function withHeader($name, $value): self
    {
        $this->headers[$name] = $value;
        return $this;
    }

    public function getBody(): array
    {
        return $this->body;
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

}
