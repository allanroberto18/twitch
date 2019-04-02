<?php
declare(strict_types=1);

namespace App\Providers;

use App\Providers\Interfaces\StreamsProviderInterface;
use App\Providers\Interfaces\TwitchRequestInterface;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Psr7\Request;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class StreamsProvider implements StreamsProviderInterface, TwitchRequestInterface
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
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getMostPopularStreams(): ResponseInterface
    {
        $this->getOptions();

        $options = $this->getOptions();
        $query = [
            'first' => 4
        ];

        $url = sprintf('https://api.twitch.tv/helix/streams?%s', http_build_query($query));
        $request = new Request('GET', $url);

        return $this->makeRequest($request, $options);
    }

    /**
     * @param string $userLogin
     * @return array
     * @throws \Exception
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getStreamByUserLogin(string $userLogin): ResponseInterface
    {
        if (empty($userLogin)) {
            throw new \Exception('Channel not selected');
        }

        $options = $this->getOptions();
        $query = [
            'user_login' => $userLogin
        ];

        $url = sprintf('https://api.twitch.tv/helix/streams?%s', http_build_query($query));
        $request = new Request('GET', $url);

        return $this->makeRequest($request, $options);
    }

    /**
     * @return ResponseInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @var RequestInterface $request
     * @var array $options
     */
    public function makeRequest(RequestInterface $request, array $options = []): ResponseInterface
    {
        return $this->httpClient->send($request, $options);
    }

    /**
     * @return array
     */
    private function getOptions(): array
    {
        return [
            'headers' => [
                'Client-ID' => env('TwitchClientId'),
                'Accept' => 'application/vnd.twitchtv.v5+json'
            ]
        ];
    }
}
