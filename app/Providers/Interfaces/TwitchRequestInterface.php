<?php
declare(strict_types=1);

namespace App\Providers\Interfaces;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

interface TwitchRequestInterface
{
    /**
     * @var RequestInterface $request
     * @var array $options
     * @return ResponseInterface
     */
    public function makeRequest(RequestInterface $request, array $options = []): ResponseInterface;
}
