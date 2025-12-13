<?php

declare(strict_types=1);

namespace Tests\Feature\Repositories\User;

use App\Models\User;
use App\Repositories\User\ProfileRepository;
use Tests\Feature\TestCase;

class ProfileRepositoryTest extends TestCase
{
    private ProfileRepository $profileRepository;

    #[\Override]
    protected function setUp(): void
    {
        parent::setUp();
        $this->profileRepository = app(ProfileRepository::class);
    }

    public function test_store_update_delete(): void
    {
        $user = User::factory()->create();
        // Delete any existing profile to avoid unique constraint
        $user->profile?->delete();

        $data = [
            'user_id' => $user->id,
            'data' => ['description' => 'test'],
        ];

        $profile = $this->profileRepository->store($data);

        $this->assertSame($user->id, $profile->user_id);

        $this->profileRepository->update($profile, ['data' => ['description' => 'updated']]);
        $profile->refresh();

        // data is cast to ProfileData object, not array
        $this->assertSame('updated', $profile->data->description);

        $this->profileRepository->delete($profile);

        $this->assertNull($this->profileRepository->find($profile->id));
    }
}
