<?php
declare(strict_types=1);

namespace App\Traits;

use Illuminate\Support\Facades\Config;

trait HasIsMailEnabled
{
    /**
     * @return bool
     */
    protected function getIsMailEnabled(): bool
    {
        return Config::get('mail.enabled');
    }
}
