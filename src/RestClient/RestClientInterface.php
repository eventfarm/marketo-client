<?php
namespace EventFarm\Marketo\RestClient;

use Psr\Http\Message\ResponseInterface;

interface RestClientInterface
{
    public function request(string $method, string $uri = '', array $options = []): ResponseInterface;
}
