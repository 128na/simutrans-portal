<?php

declare(strict_types=1);

namespace App\Console\Commands\Article;

use App\Jobs\Article\JobUpdateRelated;
use Illuminate\Console\Command;

class GenerateStaticJson extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'json:generate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'フロント表示用のjsonを生成する';

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
    public function handle(): int
    {
        JobUpdateRelated::dispatchSync();

        return 0;
    }
}
