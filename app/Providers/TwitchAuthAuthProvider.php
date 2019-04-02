<?php
declare(strict_types=1);

namespace App\Providers;

use App\Providers\Interfaces\TwitchRequestInterface;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\RequestOptions;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use App\Providers\Interfaces\TokenProviderInterface;
use App\Providers\Interfaces\TwitchAuthProviderInterface;
use App\Providers\Interfaces\AuthUrlProviderInterface;

class TwitchAuthAuthProvider implements TwitchAuthProviderInterface, TokenProviderInterface, AuthUrlProviderInterface, TwitchRequestInterface
{
    /**
     * @var ClientInterface $httpClient
     */
    private $httpClient;

    /**
     * TwitchProvider constructor.
     * @param ClientInterface $httpClient
     */
    public function __construct(ClientInterface $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    /**
     * @return string
     */
    public function getClientId(): string
    {
        return env('TwitchClientId');
    }

    /**
     * @return string
     */
    public function getClientSecret(): string
    {
        return env('TwitchClientSecret');
    }

    /**
     * @return string
     */
    public function getScopes(): string
    {
        return env('TwitchScopes');
    }

    /**
     * @return string
     */
    public function getBaseUrl(): string
    {
        return env('TwitchBaseUrl');
    }

    /**
     * @return string
     */
    public function getRedirectUrl(): string
    {
        return env('TwitchRedirectUrl');
    }

    /**
     * @param RequestInterface $request
     * @param array $options
     * @return ResponseInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function makeRequest(RequestInterface $request, array $options = []): ResponseInterface
    {
        return $this->httpClient->send($request, $options);
    }

    /**
     * @return ResponseInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getAccessToken(): ResponseInterface
    {
        $uri = sprintf('%s/%s',  $this->getBaseUrl(), 'token');
        $request = new Request('POST', $uri);

        return $this->makeRequest($request, [
            RequestOptions::JSON => [
                'client_id' => $this->getClientId(),
                'client_secret' => $this->getClientSecret(),
                'grant_type' => 'authorization_code',
                'scope' => $this->getScopes()
            ]
        ]);
    }

    /**
     * @param string $code
     * @return ResponseInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getUserAccessToken(string $code): ResponseInterface
    {
        if (empty($code) === true) {
            throw new \Exception('Code is required to get token');
        }

        $uri = sprintf('%s/%s',  $this->getBaseUrl(), 'token');
        $request = new Request('POST', $uri);

        return $this->makeRequest($request, [
            RequestOptions::JSON => [
                'client_id' => $this->getClientId(),
                'client_secret' => $this->getClientSecret(),
                'code' => $code,
                'grant_type' => 'authorization_code',
                'redirect_uri' => $this->getRedirectUrl()
            ]
        ]);
    }

    /**
     * @return string
     */
    public function getAuthUrl(): string
    {
        $base = $this->getBaseUrl();
        $query = http_build_query([
            'client_id' => $this->getClientId(),
            'redirect_uri' => $this->getRedirectUrl(),
            'response_type' => 'token',
            'scope' => $this->getScopes(),
            'force_verify' => false,
            'state' => csrf_token()
        ]);

        return sprintf('%s/authorize?%s', $base, $query);
    }

    /**
     * @param string $refreshToken
     * @return ResponseInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getRefreshToken(string $refreshToken): ResponseInterface
    {
        $uri = sprintf('%s/%s',  $this->getBaseUrl(), 'token');
        $request = new Request('POST', $uri);

        return $this->makeRequest($request, [
            RequestOptions::JSON => [
                'grant_type' => 'refresh_token',
                'client_id' => $this->getClientId(),
                'client_secret' => $this->getClientSecret(),
                'scope' => $this->getScopes(),
                'refresh_token' => $refreshToken
            ]
        ]);
    }
}
