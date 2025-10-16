<?php

namespace Shieldforce\FederalFilamentLog\Commands;

use Illuminate\Console\Command;

class FederalFilamentLogCommand extends Command
{
    public $signature = 'federal-filament-log';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
