<?php

namespace PragmaRX\Version\Package\Console\Commands;

class Show extends Base
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'version:show {--format= : Use a different format (default: full)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Show current app version and build';

    /**
     * Create a new command instance.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->displayAppVersion($this->option('format') ?: 'full');
    }
}
