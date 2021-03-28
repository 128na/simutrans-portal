<?php

namespace Tests\Feature\Jobs\Article;

use App\Jobs\Article\JobUpdateRelated;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class UpdateRelatedTest extends TestCase
{
    protected function setUp(): void
    {
    }

    public function testDispatch()
    {
        Queue::fake();
        Queue::assertNothingPushed();

        dispatch_now(app(JobUpdateRelated::class));

        Queue::assertPushed(JobUpdateRelated::class);
    }
}
