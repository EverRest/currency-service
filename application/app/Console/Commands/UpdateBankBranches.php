<?php
declare(strict_types=1);

namespace App\Console\Commands;

use App\Jobs\UpdateOrCreateBankBranchesJob;
use Illuminate\Console\Command;

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
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        UpdateOrCreateBankBranchesJob::dispatch();
        $this->info(self::SUCCESS_MESSAGE);

        return 1;
    }
}
