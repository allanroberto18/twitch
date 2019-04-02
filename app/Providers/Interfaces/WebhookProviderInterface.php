<?php
declare(strict_types=1);

namespace App\Providers\Interfaces;

use Illuminate\Http\Request;

interface WebhookProviderInterface
{
    public function handler(Request $request): array;
}
