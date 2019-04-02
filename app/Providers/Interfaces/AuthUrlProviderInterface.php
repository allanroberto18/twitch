<?php
declare(strict_types=1);

namespace App\Providers\Interfaces;

interface AuthUrlProviderInterface
{
    /**
     * @return string
     */
    public function getAuthUrl(): string;
}
