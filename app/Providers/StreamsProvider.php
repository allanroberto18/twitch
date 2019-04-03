<?php
declare(strict_types=1);

namespace App\Providers;

use App\Providers\Interfaces\StreamsProviderInterface;
use App\Providers\Interfaces\TwitchRequestInterface;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\RequestOptions;
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
        $options =  $options = [
            'headers' => [
                'Client-ID' => env('TwitchClientId'),
                'Accept' => 'application/vnd.twitchtv.v5+json'
            ]
        ];

        $query = [
            'first' => 10
        ];

        $url = sprintf('https://api.twitch.tv/helix/streams?%s', http_build_query($query));
        $request = new Request('GET', $url);

        return $this->makeRequest($request, $options);
    }

    /**
     * @param string $userName
     * @param string $token
     * @return ResponseInterface
     * @throws \Exception
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getStreamByUserLogin(string $userName, string $token): ResponseInterface
    {
        if (empty($userName)) {
            throw new \Exception('Channel not selected');
        }

        $options = [
            'headers' => [
                'Client-ID' => env('TwitchClientId'),
                'Accept' => 'application/vnd.twitchtv.v5+json'
            ]
        ];

        $query = [
            'user_login' => $userName
        ];

        $url = sprintf('https://api.twitch.tv/helix/streams?%s', http_build_query($query));
        $request = new Request('GET', $url);
        $response = $this->makeRequest($request, $options);

        // Subscribe webhook hub
        $this->subscribeStreamerOnWebhookHub($response, $token);

        return $response;
    }

    /**
     * @param ResponseInterface $response
     * @param string $token
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    private function subscribeStreamerOnWebhookHub(ResponseInterface $response, string $token): void
    {
        $params = json_decode($response->getBody()->getContents(), true);
        if (empty($params) === true) {
            return;
        }

        $userId = $params['data'][0]['user_id'];
        $webhookUrl = 'https://api.twitch.tv/helix/webhooks/hub';
        $url = sprintf('https://api.twitch.tv/helix/users?id=%d', $userId);

        $headers = [
            'Authorization' => sprintf('Bearer %s', $token),
            'Client-ID' => env('TwitchClientId'),
            'Content-Type' => 'application/json'
        ];

        $body = [
            'hub.mode' => 'subscribe',
            'hub.topic' => $url,
            'hub.callback' => 'https://alr-twitch.herokuapp.com/api/callback/handler',
            'hub.lease_seconds' => 864000,
            'hub.secret' => csrf_token(),
        ];

        $request = new Request('POST', $webhookUrl, [ 'headers' => $headers, 'body' => json_encode($body) ]);
        $this->makeRequest($request, ['headers' => $headers, 'body' => json_encode($body) ]);
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
}
