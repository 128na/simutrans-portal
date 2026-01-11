<?php

declare(strict_types=1);

namespace Tests\Unit\Enums;

use App\Enums\ArticleStatus;
use PHPUnit\Framework\Attributes\Test;
use Tests\Unit\TestCase;

class ArticleStatusTest extends TestCase
{
    #[Test]
    public function it_has_correct_publish_value(): void
    {
        $this->assertSame('publish', ArticleStatus::Publish->value);
    }

    #[Test]
    public function it_has_correct_reservation_value(): void
    {
        $this->assertSame('reservation', ArticleStatus::Reservation->value);
    }

    #[Test]
    public function it_has_correct_draft_value(): void
    {
        $this->assertSame('draft', ArticleStatus::Draft->value);
    }

    #[Test]
    public function it_has_correct_trash_value(): void
    {
        $this->assertSame('trash', ArticleStatus::Trash->value);
    }

    #[Test]
    public function it_has_correct_private_value(): void
    {
        $this->assertSame('private', ArticleStatus::Private->value);
    }

    #[Test]
    public function it_has_all_expected_cases(): void
    {
        $cases = ArticleStatus::cases();

        $this->assertCount(5, $cases);
        $this->assertContains(ArticleStatus::Publish, $cases);
        $this->assertContains(ArticleStatus::Reservation, $cases);
        $this->assertContains(ArticleStatus::Draft, $cases);
        $this->assertContains(ArticleStatus::Trash, $cases);
        $this->assertContains(ArticleStatus::Private, $cases);
    }

    #[Test]
    public function it_can_be_constructed_from_value(): void
    {
        $publish = ArticleStatus::from('publish');
        $this->assertSame(ArticleStatus::Publish, $publish);

        $reservation = ArticleStatus::from('reservation');
        $this->assertSame(ArticleStatus::Reservation, $reservation);

        $draft = ArticleStatus::from('draft');
        $this->assertSame(ArticleStatus::Draft, $draft);

        $trash = ArticleStatus::from('trash');
        $this->assertSame(ArticleStatus::Trash, $trash);

        $private = ArticleStatus::from('private');
        $this->assertSame(ArticleStatus::Private, $private);
    }

    #[Test]
    public function it_throws_exception_for_invalid_value(): void
    {
        $this->expectException(\ValueError::class);

        ArticleStatus::from('invalid-status');
    }

    #[Test]
    public function it_returns_null_for_invalid_value_with_try_from(): void
    {
        $result = ArticleStatus::tryFrom('invalid-status');

        $this->assertNull($result);
    }

    #[Test]
    public function it_can_be_compared(): void
    {
        $status1 = ArticleStatus::Publish;
        $status2 = ArticleStatus::Publish;
        $status3 = ArticleStatus::Draft;

        $this->assertTrue($status1 === $status2);
        $this->assertFalse($status1 === $status3);
    }

    #[Test]
    public function it_can_be_used_in_match_expression(): void
    {
        $status = ArticleStatus::Publish;

        $result = match ($status) {
            ArticleStatus::Publish => '公開',
            ArticleStatus::Reservation => '予約投稿',
            ArticleStatus::Draft => '下書き',
            ArticleStatus::Trash => 'ゴミ箱',
            ArticleStatus::Private => '非公開',
        };

        $this->assertSame('公開', $result);
    }
}
