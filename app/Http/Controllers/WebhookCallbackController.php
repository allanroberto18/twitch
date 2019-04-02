<?php

namespace App\Http\Controllers;

use App\Providers\Interfaces\WebhookProviderInterface;
use App\Providers\WebhookProvider;
use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
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


    public function handler(Request $request): string
    {
        $response = $this->webhookProvider->handler($request);
        if (empty($response) === null) {
            throw new Exception('something wrong happen');
        }

        $data = $response->get('data');
        if (empty($data) === false) {
            Log::info('primeiro valor recebido');
        }

        if (array_key_exists('challenge', $response) === false) {
            throw new \Exception('Challenge not found');
        }

        Log::info($response['challenge']);

        return $response['challenge'];
    }
}
