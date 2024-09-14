<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ClearAll extends Command
{
    protected $signature = 'clear:all';
    protected $description = 'Clear all caches and other components';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $this->call('cache:clear');
        $this->call('config:clear');
        $this->call('route:clear');
        $this->call('view:clear');
        $this->call('config:cache');
        $this->call('route:cache');
        $this->call('view:cache');

        $this->info('All caches and configurations cleared and re-cached successfully.');
    }
}
