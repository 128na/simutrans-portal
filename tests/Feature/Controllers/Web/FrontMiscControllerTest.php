<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Web;

use App\Enums\CategoryType;
use App\Models\Article;
use App\Models\Category;
use App\Models\Redirect;
use App\Models\Tag;
use App\Models\User;
use Tests\Feature\TestCase;

final class FrontMiscControllerTest extends TestCase
{
    public function test_social(): void
    {
        $testResponse = $this->get(route('social'));

        $testResponse->assertOk();
    }
    public function test_redirect(): void
    {
        $testResponse = $this->get(route('redirect', ['name' => 'test']));
        $testResponse->assertRedirectToRoute('index');

        $testResponse = $this->get(route('redirect', ['name' => 'simutrans-interact-meeting']));
        $testResponse->assertRedirectToRoute('articles.fallbackShow', ['id' => 1212]);
    }
}
