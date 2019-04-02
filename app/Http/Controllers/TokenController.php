<?php

namespace App\Http\Controllers;

use App\Providers\Interfaces\TokenProviderInterface;
use App\Providers\TwitchAuthAuthProvider;
use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class TokenController extends Controller
{
    /**
     * @var TokenProviderInterface $twitchAuthProvider
     */
    private $twitchAuthProvider;

    /**
     * TokenController constructor.
     */
    public function __construct()
    {
        $container = app();
        $container->bind(ClientInterface::class, Client::class);

        $this->twitchAuthProvider = $container->make(TwitchAuthAuthProvider::class);
    }

    /**
     * @return Response
     */
    public function accessToken(): Response
    {
        try {
            $response = $this->twitchAuthProvider->getAccessToken();

        } catch (RequestException $ex) {
            $exception = json_decode($ex->getResponse()->getBody(), true) ;

            Log::error($exception['message']);

            return response($exception, $ex->getCode());
        }

        return response(json_decode($response->getBody(), true), $response->getStatusCode()) ;
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function userAccessToken(Request $request): Response
    {
        try {
            $code = $request->get('code');

            $response = $this->twitchAuthProvider->getUserAccessToken($code);

        } catch (RequestException $ex) {
            $exception = json_decode($ex->getResponse()->getBody(), true) ;

            Log::error($exception['message']);

            return response($exception, $ex->getCode());
        }

        return response(json_decode($response->getBody(), true), $response->getStatusCode()) ;
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function refreshToken(Request $request): Response
    {
        try {
            $token = $request->get('token');

            $response = $this->twitchAuthProvider->getRefreshToken($token);
        } catch (RequestException $ex) {
            $exception = json_decode($ex->getResponse()->getBody(), true) ;

            Log::error($exception['message']);

            return response($exception, $ex->getCode());
        }

        return response(json_decode($response->getBody(), true), $response->getStatusCode()) ;
    }
}
