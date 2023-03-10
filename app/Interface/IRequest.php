<?php

namespace App\Interface;

interface IRequest {

    public function getHeaders(): array;

    public function hasHeader(string $name): bool;

    public function getHeader(string $name): ?string;

    public function getBody(): array;

    public function getMethod(): string;

    public function getUri(): string;

    public function getClientIp(): string;

    public function getUriParams(): array;

    public function setUriParams(array $uriParams): void;
}
