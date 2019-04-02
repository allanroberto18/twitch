<?php

namespace App\Http\Controllers;

use App\Events\PusherEvent;
use App\Providers\Interfaces\StreamsProviderInterface;
use App\Providers\StreamsProvider;
use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class StreamsController extends Controller
{
    /**
     * @var StreamsProviderInterface $streamsProvider
     */
    private $streamsProvider;

    /**
     * StremsController constructor.
     */
    public function __construct()
    {
        $container = app();
        $container->bind(ClientInterface::class, Client::class);

        $this->streamsProvider = $container->make(StreamsProvider::class);
    }

    /**
     * @return Response
     */
    public function getMostPopular(): Response
    {
        $response = $this->streamsProvider->getMostPopularStreams();

        return response(json_decode($response->getBody(), true), 200);
    }

    /**
     * @return Response
     */
    public function getChannel(Request $request): Response
    {
        try {
            $channel = $request->get('channel');
            $response = $this->streamsProvider->getStreamByUserLogin($channel);

            $streamer = json_decode($response->getBody(), true);

            $this->dispatchEvent($streamer);

        } catch (RequestException $ex) {
            $exception = json_decode($ex->getResponse()->getBody(), true) ;

            Log::error($exception['message']);

            return response($exception, $ex->getCode());
        }

        return response($streamer, $response->getStatusCode());
    }

    private function dispatchEvent(array $streamer): void
    {
        if (array_key_exists('data', $streamer) === false) {
            return;
        }

        if (sizeof($streamer['data']) === 0) {
            return;
        }

        $data = $streamer['data'][0];

        $userId = (int) $data['user_id'];
        $userName = $data['user_name'];
        $title = $data['title'];
        $viewers = (int) $data['viewer_count'];
        $language = $data['language'];
        $type = $data['type'];

        event(new PusherEvent($userId, $userName, $title, $viewers, $language, $type));
    }
}
