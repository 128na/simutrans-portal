<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\Profile;
use App\Models\Article;
use App\Models\Attachment;
use App\Models\Categories;
use App\Traits\WPImportable;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ImportUsersFromWP extends Command
{
    use WPImportable;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:users';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import users from WP Database';


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
        $wp_users = $this->fetchWPUsers();

        DB::beginTransaction();
        foreach ($wp_users as $wp_user) {
            // dump($wp_user);
            $user = self::createUser($wp_user);

            $wp_usermeta = collect($this->fetchWPUsermeta($wp_user->ID));
            // dump($wp_usermeta);
            $profile = self::createProfile($user, $wp_user, $wp_usermeta);
            $this->info('created:'.$user->name);
        }
        DB::commit();
    }
    private static function createUser($wp_user)
    {
        return User::create([
            'name'     => $wp_user->display_name,
            'email'    => $wp_user->user_email,
            'password' => \App::environment(['local', 'development'])
                ? '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'
                : Hash::make((string) Str::uuid()),
            'role'     => config('role.user'),
        ]);
    }
    private static function createProfile($user, $wp_user, $wp_usermeta)
    {
        $profile = $user->profile()->create([
            'data' => [
                'description' => self::searchMetaItem($wp_usermeta, 'description'),
                'twitter'     => self::searchMetaItem($wp_usermeta, 'twitter'),
                'website'     => $wp_user->user_url ?? null,
            ]
        ]);

        // add avater
        if($serialized = self::searchMetaItem($wp_usermeta, 'simple_local_avatar')) {
            try {
                $avater_urls = unserialize($serialized);
                $avater_url = $avater_urls['full'];
            } catch(\ErrorException $e) {
                $serialized = self::fixSerializedStr($serialized);
                $avater_urls = unserialize($serialized);
                $avater_url = self::recoverURL($avater_urls['full']);
            }

            $filename = $wp_user->user_nicename.'.'.self::getExtention($avater_url);
            $path = self::saveFromUrl($user->id, $avater_url, $filename);

            $profile->attachments()->create([
                'user_id'       => $user->id,
                'original_name' => basename($avater_url),
                'path'          => $path
            ]);
        }
        return $profile;
    }



}
