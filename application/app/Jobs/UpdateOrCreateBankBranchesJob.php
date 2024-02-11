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
        foreach ($bankService->getNotNbuBanks() as $bank) {
            $dtoArray = $financeUaService->getBankBranchesDtoArray($bank);
            foreach($dtoArray as $bankBranch) {
                $bankBranchService->firstOrCreate($bankBranch->toArray());
            }
        }
    }
}
