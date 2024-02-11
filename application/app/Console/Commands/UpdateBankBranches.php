<?php
declare(strict_types=1);

namespace App\Console\Commands;

use App\Jobs\UpdateOrCreateBankBranchesJob;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

class UpdateBankBranches extends Command
{
    private const SUCCESS_MESSAGE = 'Update bank branches';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:update-bank-branches';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Store BankBranches';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        UpdateOrCreateBankBranchesJob::dispatch();
        $this->info(get_class($this) . ' : ' . Carbon::now()->toDateTimeString() . ' - ' . self::SUCCESS_MESSAGE);

        return 1;
    }
}
