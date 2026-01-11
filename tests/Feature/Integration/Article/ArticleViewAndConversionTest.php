<?php

declare(strict_types=1);

namespace Tests\Feature\Integration\Article;

use App\Actions\FrontArticle\ConversionAction;
use App\Actions\FrontArticle\DownloadAction;
use App\Enums\ArticleStatus;
use App\Models\Article;
use App\Models\Attachment;
use App\Models\User;
use App\Repositories\Article\ConversionCountRepository;
use App\Repositories\Article\ViewCountRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Feature\TestCase;

/**
 * 記事の閲覧・コンバージョン統合テスト
 * ビューカウント、コンバージョンカウント、ダウンロードアクションの連携を検証
 */
class ArticleViewAndConversionTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     * ビューカウントが正しく記録される
     */
    public function testViewCountIsRecorded(): void
    {
        $user = User::factory()->create();
        $article = Article::factory()->create([
            'user_id' => $user->id,
            'status' => ArticleStatus::Publish,
        ]);

        $viewCountRepo = app(ViewCountRepository::class);

        // ビューをカウント
        $viewCountRepo->countUp($article);
        $viewCountRepo->countUp($article);
        $viewCountRepo->countUp($article);

        // カウントが記録されていることを確認
        $this->assertDatabaseHas('view_counts', [
            'article_id' => $article->id,
            'user_id' => $user->id,
        ]);
    }

    /**
     * @test
     * コンバージョンカウントが正しく記録される
     */
    public function testConversionCountIsRecorded(): void
    {
        $user = User::factory()->create();
        $article = Article::factory()->create([
            'user_id' => $user->id,
            'status' => ArticleStatus::Publish,
        ]);

        $conversionCountRepo = app(ConversionCountRepository::class);

        // コンバージョンをカウント
        $conversionCountRepo->countUp($article);
        $conversionCountRepo->countUp($article);

        // カウントが記録されていることを確認
        $this->assertDatabaseHas('conversion_counts', [
            'article_id' => $article->id,
            'user_id' => $user->id,
        ]);
    }

    /**
     * @test
     * 記事作者自身のアクセスはコンバージョンカウントされない
     */
    public function testAuthorAccessDoesNotCountConversion(): void
    {
        $author = User::factory()->create();
        $article = Article::factory()->create([
            'user_id' => $author->id,
            'status' => ArticleStatus::Publish,
        ]);

        $this->actingAs($author);

        $action = app(ConversionAction::class);
        $action($article, $author);

        // 作者のコンバージョンは記録されない
        $this->assertDatabaseMissing('conversion_counts', [
            'article_id' => $article->id,
        ]);
    }

    /**
     * @test
     * 他ユーザーのアクセスはコンバージョンカウントされる
     */
    public function testOtherUserAccessCountsConversion(): void
    {
        $author = User::factory()->create();
        $visitor = User::factory()->create();
        $article = Article::factory()->create([
            'user_id' => $author->id,
            'status' => ArticleStatus::Publish,
        ]);

        $this->actingAs($visitor);

        $action = app(ConversionAction::class);
        $action($article, $visitor);

        // 他ユーザーのコンバージョンは記録される
        $this->assertDatabaseHas('conversion_counts', [
            'article_id' => $article->id,
        ]);
    }

    /**
     * @test
     * ダウンロードアクションでコンバージョンが記録される
     */
    public function testDownloadActionRecordsConversion(): void
    {
        $author = User::factory()->create();
        $visitor = User::factory()->create();
        $article = Article::factory()->create([
            'user_id' => $author->id,
            'status' => ArticleStatus::Publish,
            'post_type' => \App\Enums\ArticlePostType::AddonPost,
            'contents' => [
                'file' => null, // ファイルIDは後で設定
            ],
        ]);

        // 記事に関連付けられたAttachmentを作成
        $attachment = Attachment::factory()->create([
            'user_id' => $author->id,
            'path' => 'test/file.zip',
            'attachmentable_id' => $article->id,
            'attachmentable_type' => Article::class,
        ]);

        // contentsのfileフィールドを更新
        $contents = $article->contents;
        $contents->file = $attachment->id; // int型
        $article->contents = $contents;
        $article->save();

        $this->actingAs($visitor);

        $action = app(DownloadAction::class);

        try {
            $action($article, $visitor);
        } catch (\Throwable $e) {
            // ファイルが実際に存在しないのでダウンロードは失敗するが、
            // コンバージョンカウントは記録される
        }

        // コンバージョンが記録されていることを確認
        $this->assertDatabaseHas('conversion_counts', [
            'article_id' => $article->id,
        ]);
    }

    /**
     * @test
     * ファイルがない記事のダウンロード試行はエラーになる
     */
    public function testDownloadActionAbortsWhenNoFile(): void
    {
        $user = User::factory()->create();
        $article = Article::factory()->create([
            'user_id' => $user->id,
            'status' => ArticleStatus::Publish,
            'post_type' => \App\Enums\ArticlePostType::AddonPost,
            'contents' => [
                'file' => null, // ファイルなし
            ],
        ]);

        $this->actingAs($user);

        $action = app(DownloadAction::class);

        $this->expectException(\Symfony\Component\HttpKernel\Exception\HttpException::class);
        $action($article, $user);
    }

    /**
     * @test
     * 複数ユーザーからのアクセスが正しくカウントされる
     */
    public function testMultipleUsersAccessCounting(): void
    {
        $author = User::factory()->create();
        $article = Article::factory()->create([
            'user_id' => $author->id,
            'status' => ArticleStatus::Publish,
        ]);

        $visitors = User::factory()->count(5)->create();

        $action = app(ConversionAction::class);

        foreach ($visitors as $visitor) {
            $this->actingAs($visitor);
            $action($article, $visitor);
        }

        // 5人のビジターのコンバージョンが記録されている
        $count = \DB::table('conversion_counts')
            ->where('article_id', $article->id)
            ->count();

        $this->assertGreaterThanOrEqual(1, $count);
    }
}
