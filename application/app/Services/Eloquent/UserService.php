<?php
declare(strict_types=1);

namespace App\Services\Eloquent;

use App\Models\User;
use App\Services\Abstracts\ServiceWithEloquentModel;

class UserService extends ServiceWithEloquentModel
{
    /**
     * @var string $model
     */
    protected string $model = User::class;
}
