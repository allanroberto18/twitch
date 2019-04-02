<?php

namespace App\Http\Controllers;

use App\Events\PusherEvent;
use App\Providers\Interfaces\WebhookProviderInterface;
use App\Providers\WebhookProvider;
use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use Illuminate\Http\Request;
use mysql_xdevapi\Exception;

class WebhookCallbackController extends Controller
{
    /**
     * @var WebhookProviderInterface $webhookProvider
     */
    private $webhookProvider;

    /**
     * WebhookCallbackController constructor.
     */
    public function __construct()
    {
        $container = app();
        $container->bind(ClientInterface::class, Client::class);

        $this->webhookProvider = $container->make(WebhookProvider::class);
    }


    public function handler(Request $request): ?string
    {
        $method = $request->method();
        if ($method === 'GET') {
            return $this->checkValidationResponse($request);
        }

        if (empty($request->json()->get('data')) === true) {
            return null;
        }

        $data = $request->json()->get('data');
        $keys = array_keys($data[0]);
        if (in_array('user_id', $keys) === true) {
            event(
                new PusherEvent(
                    $data[0]['user_id'],
                    $data[0]['user_name'],
                    $data[0]['title'],
                    $data[0]['viewer_count'],
                    $data[0]['language'],
                    $data[0]['type']
                )
            );
        }

        return null;

    }

    /**
     * @param Request $request
     * @return array
     * @throws \Exception
     */
    private function checkValidationResponse(Request $request): string
    {
        $response = $this->webhookProvider->handler($request);
        if (empty($response) === null) {
            throw new Exception('something wrong happen');
        }

        if (array_key_exists('challenge', $response) === false) {
            throw new \Exception('Challenge not found');
        }

        return $response['challenge'];
    }
}
