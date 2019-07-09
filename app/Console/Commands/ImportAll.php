<?php

namespace App\Console\Commands;

use App\Models\Attachment;
use App\Models\User;
use Illuminate\Console\Command;

class ImportAll extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:all';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import ALL';

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
     *
     * @return mixed
     */
    public function handle()
    {
        $this->info('deleting all attachments');
        Attachment::all()->each(function($m) {$m->delete(); });
        $this->info('deleted all attachments');

        $this->info('deleting all users');
        User::where('role', config('role.user'))->each(function($m) {$m->delete(); });
        $this->info('deleted all users');

        $this->info('importing all users');
        $this->call('import:users');
        $this->info('imported all users');

        $this->info('importing all articles');
        $this->call('import:articles');
        $this->info('imported all articles');

        $this->info('importing all ga');
        $this->call('import:ga');
        $this->info('imported all ga');
    }
}
