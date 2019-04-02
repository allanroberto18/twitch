<?php
declare(strict_types=1);

namespace App\Providers\Interfaces;

use Psr\Http\Message\ResponseInterface;

interface StreamsProviderInterface
{
    /**
     * @return array
     */
    public function getMostPopularStreams(): ResponseInterface;

    /**
     * @param string $userName
     * @param string $token
     * @return ResponseInterface
     */
    public function getStreamByUserLogin(string $userName, string $token): ResponseInterface;
}
