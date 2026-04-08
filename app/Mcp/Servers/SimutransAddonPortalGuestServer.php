<?php

declare(strict_types=1);

namespace App\Mcp\Servers;

use App\Mcp\Tools\GuestArticleSearchOptionsTool;
use App\Mcp\Tools\GuestArticleSearchTool;
use App\Mcp\Tools\GuestArticleShowTool;
use App\Mcp\Tools\GuestLatestArticlesTool;
use App\Mcp\Tools\GuestPublicMyListListTool;
use App\Mcp\Tools\GuestPublicMyListShowTool;
use App\Mcp\Tools\GuestSearchSuggestTool;
use App\Mcp\Tools\GuestTagCategoryAggregateTool;
use App\Mcp\Tools\GuestUserArticlesTool;
use Laravel\Mcp\Server;
use Laravel\Mcp\Server\Prompt;
use Laravel\Mcp\Server\Tool;

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
     * @var array<int, class-string<Tool>>
     */
    protected array $tools = [
        GuestArticleSearchOptionsTool::class,
        GuestArticleSearchTool::class,
        GuestArticleShowTool::class,
        GuestLatestArticlesTool::class,
        GuestPublicMyListListTool::class,
        GuestPublicMyListShowTool::class,
        GuestTagCategoryAggregateTool::class,
        GuestUserArticlesTool::class,
        GuestSearchSuggestTool::class,
    ];

    /**
     * The resources registered with this MCP server.
     *
     * @var array<int, class-string<Server\Resource>>
     */
    protected array $resources = [
        //
    ];

    /**
     * The prompts registered with this MCP server.
     *
     * @var array<int, class-string<Prompt>>
     */
    protected array $prompts = [
        //
    ];
}
