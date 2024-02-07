<?php
declare(strict_types=1);

namespace App\Services\Eloquent;

use App\Models\Bank;
use App\Services\Abstracts\ServiceWithEloquentModel;

class BankService extends ServiceWithEloquentModel
{
    /**
     * @var string $model
     */
    protected string $model = Bank::class;
}
