<?php
declare(strict_types=1);

namespace App\Jobs;

use App\Models\Bank;
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
     * @param BankBranchService $bankBranchService
     * @param BankService $bankService
     * @param FinanceUaService $financeUaService
     *
     * @return void
     */
    public function handle(BankBranchService $bankBranchService, BankService $bankService, FinanceUaService $financeUaService): void
    {
        foreach ($bankService->query()->get() as $bank) {
            $this->updateOrCreateBranches($bank, $financeUaService, $bankBranchService);
        }
    }

    /**
     * Update or create branches for a specific bank.
     *
     * @param Bank $bank
     * @param FinanceUaService $financeUaService
     * @param BankBranchService $bankBranchService
     *
     * @return void
     */
    private function updateOrCreateBranches(Bank $bank, FinanceUaService $financeUaService, BankBranchService $bankBranchService): void
    {
        $financeUaService->getBankBranchList($bank->code)->each(
            function ($bankBranch) use ($bank, $bankBranchService) {
                $branch = Arr::get($bankBranch, 'data.0');
                if (!empty($branch)) {
                    $this->updateOrCreateBranch($bank, $bankBranch, $branch, $bankBranchService);
                }
            }
        );
    }

    /**
     * Update or create a branch for a specific bank.
     *
     * @param Bank $bank
     * @param array $bankBranch
     * @param array $branch
     * @param BankBranchService $bankBranchService
     *
     * @return void
     */
    private function updateOrCreateBranch(Bank $bank, array $bankBranch, array $branch, BankBranchService $bankBranchService): void
    {
        $bankBranchService->firstOrCreate([
            'bank_id' => $bank->id,
            'external_id' => Arr::get($bankBranch, 'id'),
            'department_name' => Arr::get($branch, 'branch_name'),
            'address' => Arr::get($branch, 'address'),
            'phone_number' => Arr::get($branch, 'phone'),
            'lat' => Arr::get($branch, 'lat'),
            'lng' => Arr::get($branch, 'lng'),
        ]);
    }
}
