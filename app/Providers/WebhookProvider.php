<?php
declare(strict_types=1);

namespace App\Providers;

use App\Providers\Interfaces\WebhookProviderInterface;
use GuzzleHttp\ClientInterface;
use Illuminate\Http\Request;

class WebhookProvider implements WebhookProviderInterface
{
    /**
     * @var ClientInterface $client
     */
    private $client;

    /**
     * WebhookProvider constructor.
     * @param ClientInterface $client
     */
    public function __construct(ClientInterface $client)
    {
        $this->client = $client;
    }

    public function handler(Request $request): array
    {
        $mode = $request->get('hub_mode');
        $response = [];
        switch ($mode) {
            case 'subscribe':
                $response = $this->subscriptionVerified($request);
                break;
            case 'denied':
                $response = [];
                break;
        }

        return $response;
    }

    public function subscriptionVerified(Request $request): array
    {
        return [
            'mode' => $request->get('hub_mode'),
            'topic' => $request->get('hub_topic'),
            'leaseSeconds' => $request->get('hub_lease_seconds'),
            'challenge' => $request->get('hub_challenge')
        ];
    }

}
