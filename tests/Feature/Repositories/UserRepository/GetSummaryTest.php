<?php

declare(strict_types=1);

namespace Tests\Feature\Repositories\UserRepository;

use App\Models\Article;
use App\Models\Attachment;
use App\Models\Tag;
use App\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\DB;
use Tests\Feature\TestCase;

final class GetSummaryTest extends TestCase
{
    private UserRepository $userRepository;

    #[\Override]
    protected function setUp(): void
    {
        parent::setUp();
        $this->userRepository = app(UserRepository::class);
    }

    public function test(): void
    {
        $user = User::factory()->create();

        $article = Article::factory()->for($user)->create();
        Attachment::factory()->for($user)->create(['size' => 123]);

        // insert conversion and view counts for the user/period
        $period = now()->format('Ym');
        DB::table('conversion_counts')->insert([
            'article_id' => $article->id,
            'type' => 2,
            'period' => $period,
            'count' => 5,
            'user_id' => $user->id,
        ]);
        DB::table('view_counts')->insert([
            'article_id' => $article->id,
            'type' => 2,
            'period' => $period,
            'count' => 7,
            'user_id' => $user->id,
        ]);

        Tag::factory()->create(['created_by' => $user->id]);

        $summary = $this->userRepository->getSummary($user);

        $this->assertSame(1, (int) $summary->article_count);
        $this->assertSame(1, (int) $summary->attachment_count);
        $this->assertSame(123, (int) $summary->total_attachment_size);
        $this->assertSame(5, (int) $summary->total_conversion_count);
        $this->assertSame(7, (int) $summary->total_view_count);
        $this->assertSame(1, (int) $summary->tag_count);
    }
}
