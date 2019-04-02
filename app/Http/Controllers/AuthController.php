<?php

namespace App\Http\Controllers;

use App\Providers\Interfaces\AuthUrlProviderInterface;
use App\Providers\TwitchAuthAuthProvider;
use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;

class AuthController extends Controller
{
    /**
     * @var AuthUrlProviderInterface $twitchProvider
     */
    private $twitchAuthProvider;

    /**
     * AuthController constructor.
     */
    public function __construct()
    {
        $container = app();
        $container->bind(ClientInterface::class, Client::class);

        $this->twitchAuthProvider = $container->make(TwitchAuthAuthProvider::class);
    }


    public function getAuthUrl(): array
    {
        return [
            'auth_url' => $this->twitchAuthProvider->getAuthUrl()
        ];
    }
}
