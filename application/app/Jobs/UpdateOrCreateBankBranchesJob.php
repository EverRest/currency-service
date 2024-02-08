<?php
declare(strict_types=1);

namespace App\Jobs;

use App\Services\Eloquent\BankBranchService;
use App\Services\Eloquent\BankService;
use App\Services\Http\FinanceUaService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Arr;

class UpdateOrCreateBankBranchesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Execute the job.
     */
    public function handle(BankBranchService $bankBranchService, BankService $bankService, FinanceUaService $financeUaService,): void
    {
        $bankService->query()->each(
            function ($bank) use ($financeUaService, $bankBranchService) {
                $financeUaService->getBankBranchList($bank->code)->each(
                    function ($bankBranch) use ($bank, $bankBranchService) {
                        $branch = Arr::get($bankBranch, 'data.0');
                        if (!empty($branch)) {
                            $bankBranchService->firstOrCreate(
                                [
                                    'bank_id' => $bank->id,
                                    'external_id' => Arr::get($bankBranch, 'id'),
                                    'department_name' => Arr::get($branch, 'branch_name'),
                                    'address' => Arr::get($branch, 'address'),
                                    'phone_number' => Arr::get($branch, 'phone'),
                                    'lat' => Arr::get($branch, 'lat'),
                                    'lng' => Arr::get($branch, 'lng'),
                                ]
                            );
                        }
                    }
                );
            }
        );
    }
}
