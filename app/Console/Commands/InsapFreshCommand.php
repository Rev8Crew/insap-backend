<?php

namespace App\Console\Commands;

use App\Modules\Processing\Models\Process;
use App\Modules\Processing\Services\ProcessAppService;
use Illuminate\Console\Command;
use Schema;

class InsapFreshCommand extends Command
{
    protected $signature = 'insap:fresh';

    protected $description = 'Command description';

    public function handle(ProcessAppService $processAppService): void
    {
        foreach (Process::all() as $process) {
            $processAppService->delete($process);
        }
        foreach (Process::all() as $process) {
            $processAppService->delete($process);
        }

        $this->info('Delete all processes DONE');

        Schema::connection('mongodb')->dropAllTables();

        \Artisan::call('migrate:fresh', [
            '--database' => 'mongodb'
        ]);

        $this->info('Fresh mongodb DONE');

        \Artisan::call('migrate:fresh', [ '--seed' => true]);

        $this->info('Fresh & seed MySQL DONE');
    }
}
