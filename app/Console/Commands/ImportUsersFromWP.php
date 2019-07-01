<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\Profile;
use App\Models\Article;
use App\Models\Attachment;
use App\Models\Categories;
use App\Models\Redirect;
use App\Traits\WPImportable;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

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
        DB::beginTransaction();
        foreach ($this->fetchWPUsers() as $wp_user) {
            $this->info('creating: '.$wp_user->display_name);

            $user = self::createUser($wp_user);

            $wp_usermeta = collect($this->fetchWPUsermeta($wp_user->ID));
            self::updateProfile($user, $wp_user, $wp_usermeta);
            self::createRedirect($user, $wp_user);
            self::updateCreatedAt($user->id, $wp_user);
            $this->info('created: '.$user->name);
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
    private static function updateProfile($user, $wp_user, $wp_usermeta)
    {
        $user->profile->setContents('description', self::searchMetaItem($wp_usermeta, 'description'));
        $user->profile->setContents('twitter', self::searchMetaItem($wp_usermeta, 'twitter'));
        $user->profile->setContents('website',  $wp_user->user_url ?? null);

        // add avatar
        if($serialized = self::searchMetaItem($wp_usermeta, 'simple_local_avatar')) {
            try {
                $avatar_urls = unserialize($serialized);
                $avatar_url = $avatar_urls['full'];
            } catch(\ErrorException $e) {
                $serialized = self::fixSerializedStr($serialized);
                $avatar_urls = unserialize($serialized);
                $avatar_url = self::recoverURL($avatar_urls['full']);
            }

            $path = self::saveFromUrl($user->id, $avatar_url);

            $attachment = $user->profile->attachments()->create([
                'user_id'       => $user->id,
                'original_name' => basename($avatar_url),
                'path'          => $path
            ]);
            $user->profile->setContents('avatar',  $attachment->id);
        }
        $user->profile->save();
    }

    /**
     * 作成日を引き継ぐ
     */
    private static function updateCreatedAt($id, $wp_user)
    {
        return DB::update('UPDATE users SET created_at = ? WHERE id = ?', [$wp_user->user_registered, $id]);
    }


    private static function createRedirect($user, $wp_user)
    {
        $from = '/author/'.$wp_user->user_nicename;
        $to   = route('user', $user, false);
        return Redirect::firstOrCreate([
            'from' => $from,
            'to'   => $to,
        ]);
    }
}
