<?php
declare(strict_types=1);

namespace App\Providers\Interfaces;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

interface TokenProviderInterface
{
    /**
     * @return ResponseInterface
     */
    public function getAccessToken(): ResponseInterface;

    /**
     * @param string $code
     * @return ResponseInterface
     */
    public function getUserAccessToken(string $code): ResponseInterface;

    /**
     * @param string $token
     * @return ResponseInterface
     */
    public function getRefreshToken(string $token): ResponseInterface;
}
