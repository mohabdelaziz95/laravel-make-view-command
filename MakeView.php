<?php

namespace App\Console\Commands;

use App\Console\Commands\Src\ViewMaker;
use Illuminate\Console\Command;

class MakeView extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:view {view} {--resource}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new blade file';

    /**
     * Create a new command instance.
     *
     * @return void
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
        (new ViewMaker())
            ->setViewName($this->argument("view"))
            ->setResource($this->option("resource"))
            ->generate()
            ->exit($this);
    }

}
