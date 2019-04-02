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
     * @param string $userLogin
     * @return array
     */
    public function getStreamByUserLogin(string $userLogin): ResponseInterface;
}
