<?php
declare(strict_types=1);

namespace App\Services\Eloquent;

use App\Models\BankBranch;
use App\Services\Abstracts\ServiceWithEloquentModel;

class BankBranchService extends ServiceWithEloquentModel
{
    /**
     * @var string $model
     */
    protected string $model = BankBranch::class;
}
