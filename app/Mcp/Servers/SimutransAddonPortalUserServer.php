<?php

declare(strict_types=1);

namespace App\Mcp\Servers;

use App\Mcp\Tools\UserAnalyticsTool;
use App\Mcp\Tools\UserArticleCreateAddonIntroductionTool;
use App\Mcp\Tools\UserArticleCreateAddonPostTool;
use App\Mcp\Tools\UserArticleCreatePageTool;
use App\Mcp\Tools\UserArticleCreateTool;
use App\Mcp\Tools\UserArticleUpdateStatusTool;
use App\Mcp\Tools\UserAttachmentListTool;
use App\Mcp\Tools\UserMyArticleListTool;
use App\Mcp\Tools\UserMyArticleShowTool;
use App\Mcp\Tools\UserMyListAddItemTool;
use App\Mcp\Tools\UserMyListCreateTool;
use App\Mcp\Tools\UserMyListDeleteTool;
use App\Mcp\Tools\UserMyListListTool;
use App\Mcp\Tools\UserMyListRemoveItemTool;
use App\Mcp\Tools\UserMyListShowTool;
use App\Mcp\Tools\UserMyListUpdateTool;
use Laravel\Mcp\Server;
use Laravel\Mcp\Server\Prompt;
use Laravel\Mcp\Server\Tool;

class SimutransAddonPortalUserServer extends Server
{
    /**
     * The MCP server's name.
     */
    protected string $name = 'Simutrans Addon Portal User Server';

    /**
     * The MCP server's version.
     */
    protected string $version = '0.0.1';

    /**
     * The MCP server's instructions for the LLM.
     */
    protected string $instructions = <<<'MARKDOWN'
        Sanctumトークンで認証されたユーザーが自分のマイページ情報を参照できるサーバーです。
        下書きや非公開記事・非公開マイリストなど、本人のみが閲覧できる情報を返します。
    MARKDOWN;

    /**
     * The tools registered with this MCP server.
     *
     * @var array<int, class-string<Tool>>
     */
    protected array $tools = [
        UserMyArticleListTool::class,
        UserMyArticleShowTool::class,
        UserArticleUpdateStatusTool::class,
        UserArticleCreateTool::class,
        UserArticleCreatePageTool::class,
        UserArticleCreateAddonIntroductionTool::class,
        UserArticleCreateAddonPostTool::class,
        UserAttachmentListTool::class,
        UserAnalyticsTool::class,
        UserMyListListTool::class,
        UserMyListShowTool::class,
        UserMyListCreateTool::class,
        UserMyListUpdateTool::class,
        UserMyListDeleteTool::class,
        UserMyListAddItemTool::class,
        UserMyListRemoveItemTool::class,
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
