<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\Profile;
use App\Models\Article;
use App\Models\Attachment;
use App\Models\Category;
use App\Models\Tag;
use App\Traits\WPImportable;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ImportArticlesFromWP extends Command
{
    use WPImportable;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:articles';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import articles from WP Database';

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
            $user = User::where('email', $wp_user->user_email)->firstOrFail();
            foreach ($this->fetchWPPosts($wp_user->ID) as $wp_post) {
                if($wp_post->post_status !== 'publish') {
                    continue;
                }
                $this->info('creating:'.$wp_post->post_title);

                $article = $this->createArticle($user, $wp_post);

                $this->applyCategories($article, $wp_post->ID);
                $this->applyTags($article, $wp_post->ID);

                $wp_thumbnail = $this->fetchWPThumbnail($wp_post->ID);
                if($wp_thumbnail) {
                    $path = self::saveFromUrl($user->id, $wp_thumbnail->guid);

                    $attachment = $article->attachments()->create([
                        'user_id'       => $user->id,
                        'original_name' => basename($wp_thumbnail->guid),
                        'path'          => $path
                    ]);
                    $article->setContents('thumbnail', $attachment->id);
                }

                if ($article->post_type === 'addon-post') {
                    $article->setContents('author', $this->fetchWPPostmetaValueBy($wp_post->ID, 'addon-author'));
                    $article->setContents('description', $this->fetchWPPostmetaValueBy($wp_post->ID, 'addon-description'));
                    $article->setContents('thanks', $this->fetchWPPostmetaValueBy($wp_post->ID, 'addon-based'));
                    $article->setContents('license', null);

                    // addon file
                    $wp_addon_file = $this->fetchWPAddonFile($wp_post->ID);
                    $path = self::saveFromUrl($user->id, $wp_addon_file->guid);

                    $attachment = $article->attachments()->create([
                        'user_id'       => $user->id,
                        'original_name' => basename($wp_addon_file->guid),
                        'path'          => $path
                    ]);
                    $article->setContents('file', $attachment->id);

                }
                if ($article->post_type === 'addon-introduction') {
                    $article->setContents('author', $this->fetchWPPostmetaValueBy($wp_post->ID, 'addon-author'));
                    $article->setContents('description', $this->fetchWPPostmetaValueBy($wp_post->ID, 'addon-description'));
                    $article->setContents('thanks', $this->fetchWPPostmetaValueBy($wp_post->ID, 'addon-based'));
                    $article->setContents('license', null);
                    $article->setContents('link', $this->fetchWPPostmetaValueBy($wp_post->ID, 'site-url'));
                    $article->setContents('agreement',
                        $this->fetchWPPostmetaValueBy($wp_post->ID, 'addon-introduction-agreement') ? true : false);
                }
                $article->save();
                self::updateCreatedAt($article->id, $wp_post);
                $this->info('created:'.$article->title);
            }
        }
        DB::commit();
    }

    private function createArticle($user, $wp_post)
    {
        // post type
        $post_type = $this->fetchWPTerms($wp_post->ID, 'category')[0]->slug;
        return $user->articles()->create([
            'title'    => $wp_post->post_title,
            'slug'     => self::hasSlug($wp_post->post_name) ? $wp_post->post_name : $wp_post->post_title,
            'post_type' => $post_type,
            'status'   => config('status.publish'),
        ]);
    }

    private static function hasSlug($str)
    {
        return stripos($str, 'post-') === false;
    }

    private function applyCategories($article, $wp_post_id)
    {
        $categories = collect([]);

        // pak
        $wp_term_slugs = collect($this->fetchWPTerms($wp_post_id, 'pak'))->pluck('slug');
        $categories = $categories->merge(Category::pak()->whereIn('slug', $wp_term_slugs)->get());

        // addon(type)
        $wp_term_slugs = collect($this->fetchWPTerms($wp_post_id, 'type'))->pluck('slug');
        $categories = $categories->merge(Category::addon()->whereIn('slug', $wp_term_slugs)->get());

        // pak128_position
        $wp_term_slugs = collect($this->fetchWPTerms($wp_post_id, 'pak128_position'))->pluck('slug');
        $categories = $categories->merge(Category::pak128Position()->whereIn('slug', $wp_term_slugs)->get());

        return $article->categories()->sync($categories->pluck('id'));
    }
    private function applyTags($article, $wp_post_id)
    {
        $wp_terms = collect($this->fetchWPTerms($wp_post_id, 'post_tag'));
        $tags = $wp_terms->map(function($wp_term) {
            return Tag::firstOrCreate(['name' => $wp_term->name]);
        });

        return $article->tags()->sync($tags->pluck('id'));
    }
    /**
     * 作成日を引き継ぐ
     */
    private static function updateCreatedAt($id, $wp_post)
    {
        return DB::update('UPDATE articles SET created_at = ? WHERE id = ?', [$wp_post->post_date, $id]);
    }
}
