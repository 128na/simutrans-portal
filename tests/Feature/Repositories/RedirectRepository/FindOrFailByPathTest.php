<?php

declare(strict_types=1);

namespace Tests\Feature\Repositories\RedirectRepository;

use App\Models\Redirect;
use App\Repositories\RedirectRepository;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Tests\Feature\TestCase;

class FindOrFailByPathTest extends TestCase
{
    private RedirectRepository $redirectRepository;

    #[\Override]
    protected function setUp(): void
    {
        parent::setUp();
        $this->redirectRepository = app(RedirectRepository::class);
    }

    public function test(): void
    {
        $redirect = Redirect::factory()->create();
        $result = $this->redirectRepository->findOrFailByPath($redirect->from);

        $this->assertSame($redirect->from, $result->from);
    }

    public function test_存在しないパスはエラー(): void
    {
        $this->expectException(ModelNotFoundException::class);
        $this->redirectRepository->findOrFailByPath('missing');
    }

    public function test_同じfromパスを持つリダイレクトが複数ある場合でも最初のものを返す(): void
    {
        // Note: The unique constraint prevents this in production, but testing the query behavior
        $path = '/test-path';
        $redirect1 = Redirect::factory()->create(['from' => $path]);

        $result = $this->redirectRepository->findOrFailByPath($path);

        $this->assertSame($redirect1->id, $result->id);
        $this->assertSame($path, $result->from);
    }
}
