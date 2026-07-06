<?php

namespace App\Console\Commands;

use App\Actions\CleaningJobs\GenerateCleaningJobsAction;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('cleaning-jobs:generate')]
#[Description('Generate cleaning jobs for all active schedules due today or earlier')]
class GenerateCleaningJobs extends Command
{
    public function handle(GenerateCleaningJobsAction $action): int
    {
        $jobs = $action->execute();

        $this->info("Generated {$jobs->count()} cleaning job(s).");

        return self::SUCCESS;
    }
}
