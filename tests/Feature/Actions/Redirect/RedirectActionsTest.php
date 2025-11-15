<?php

declare(strict_types=1);

namespace Tests\Feature\Actions\Redirect;

use App\Actions\Redirect\AddRedirect;
use App\Actions\Redirect\DeleteRedirect;
use App\Actions\Redirect\DoRedirectIfExists;
use App\Actions\Redirect\FindMyRedirects;
use App\Models\Redirect;
use App\Models\User;
use App\Repositories\RedirectRepository;
use Mockery\MockInterface;
use Tests\Feature\TestCase;

final class RedirectActionsTest extends TestCase
{
    public function test_add_redirect_calls_repository_store(): void
    {
        $user = User::factory()->create(['nickname' => uniqid('alice_')]);
        $old = 'old-slug';
        $new = 'new-slug';

        $this->mock(RedirectRepository::class, function (MockInterface $mock) use ($user, $old, $new): void {
            $expectedFromSuffix = '/users/' . $user->nickname . '/' . $old;
            $expectedToSuffix = '/users/' . $user->nickname . '/' . $new;

            $mock
                ->shouldReceive('store')
                ->once()
                ->withArgs(function (array $arg) use ($user, $expectedFromSuffix, $expectedToSuffix): bool {
                    return (
                        isset($arg['user_id'])
                        && $arg['user_id'] === $user->id
                        && isset($arg['from'])
                        && str_ends_with($arg['from'], $expectedFromSuffix)
                        && isset($arg['to'])
                        && str_ends_with($arg['to'], $expectedToSuffix)
                    );
                });
        });

        // Ensure app.url is set so route() returns full URL (it will be stripped in AddRedirect)
        config(['app.url' => 'http://localhost']);

        $sut = app(AddRedirect::class);
        $sut($user, $old, $new);

        // satisfy PHPUnit that this test performed an assertion (mock expectations cover behavior)
        $this->assertTrue(true);
    }

    public function test_delete_redirect_deletes_model(): void
    {
        $redirect = Redirect::factory()->create();

        $sut = new DeleteRedirect();
        $sut($redirect);

        $this->assertNull(Redirect::find($redirect->id));
    }

    public function test_do_redirect_if_exists_returns_permanent_redirect(): void
    {
        config(['app.url' => 'http://localhost']);

        $user = User::factory()->create(['nickname' => uniqid('bob_')]);
        $from = '/users/' . $user->nickname . '/old';
        $to = '/users/' . $user->nickname . '/new';

        $redirect = Redirect::factory()->create(['user_id' => $user->id, 'from' => $from, 'to' => $to]);

        $sut = app(DoRedirectIfExists::class);

        $full = config('app.url') . $from;
        $response = $sut($full);

        $this->assertSame(301, $response->getStatusCode());
        $location = $response->headers->get('Location');
        $this->assertIsString($location);
        $this->assertStringEndsWith($to, $location);
    }

    public function test_find_my_redirects_returns_user_redirects(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create();

        Redirect::factory()->create(['user_id' => $user->id]);
        Redirect::factory()->create(['user_id' => $user->id]);
        Redirect::factory()->create(['user_id' => $other->id]);

        $sut = app(FindMyRedirects::class);
        $collection = $sut($user);

        $this->assertCount(2, $collection);
        $this->assertContainsOnlyInstancesOf(Redirect::class, $collection->all());
    }
}
