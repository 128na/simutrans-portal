<?php

declare(strict_types=1);

namespace App\Mcp\Servers;

use Laravel\Mcp\Server;

class SimutransAddonPortalGuestServer extends Server
{
    /**
     * The MCP server's name.
     */
    protected string $name = 'Simutrans Addon Portal Guest Server';

    /**
     * The MCP server's version.
     */
    protected string $version = '0.0.1';

    /**
     * The MCP server's instructions for the LLM.
     */
    protected string $instructions = <<<'MARKDOWN'
        ログイン不要で使用可能なサーバーです。
    MARKDOWN;

    /**
     * The tools registered with this MCP server.
     *
     * @var array<int, class-string<\Laravel\Mcp\Server\Tool>>
     */
    protected array $tools = [
        \App\Mcp\Tools\GuestArticleSearchOptionsTool::class,
        \App\Mcp\Tools\GuestArticleSearchTool::class,
        \App\Mcp\Tools\GuestArticleShowTool::class,
        \App\Mcp\Tools\GuestLatestArticlesTool::class,
        \App\Mcp\Tools\GuestTagCategoryAggregateTool::class,
        \App\Mcp\Tools\GuestUserArticlesTool::class,
    ];

    /**
     * The resources registered with this MCP server.
     *
     * @var array<int, class-string<\Laravel\Mcp\Server\Resource>>
     */
    protected array $resources = [
        //
    ];

    /**
     * The prompts registered with this MCP server.
     *
     * @var array<int, class-string<\Laravel\Mcp\Server\Prompt>>
     */
    protected array $prompts = [
        //
    ];
}
