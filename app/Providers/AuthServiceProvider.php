<?php

declare(strict_types=1);

namespace App\Providers;

use App\Models\Article;
use App\Policies\ArticlePolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Laravel\Passport\Passport;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     */
    protected $policies = [
        Article::class => ArticlePolicy::class,
    ];

    public function boot(): void
    {
        $this->registerPolicies();

        Passport::tokensCan([
            'mcp:use' => 'Use MCP tools on behalf of the user.',
            'read' => 'Read access to MCP resources.',
            'write' => 'Write access to MCP resources.',
        ]);

        Passport::setDefaultScope(['mcp:use']);
    }
}
