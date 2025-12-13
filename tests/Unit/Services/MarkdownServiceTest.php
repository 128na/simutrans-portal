<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Services\MarkdownService;
use cebe\markdown\GithubMarkdown;
use Mockery;
use Tests\Unit\TestCase;

class MarkdownServiceTest extends TestCase
{
    public function test_to_escaped_htm_l_adds_target_and_rel_to_links(): void
    {
        $md = '[link](http://example.com)';

        $github = new GithubMarkdown;
        $github->html5 = true;
        $github->enableNewlines = true;

        $parsed = $github->parse($md);

        $htmlPurifier = Mockery::mock('HTMLPurifier');
        $htmlPurifier->shouldReceive('purify')->once()->with($parsed)->andReturn($parsed);

        $sut = new MarkdownService($github, $htmlPurifier);

        $res = $sut->toEscapedHTML($md);

        $this->assertStringContainsString('target="_blank"', $res);
        $this->assertStringContainsString('rel="noopener noreferrer"', $res);
    }

    public function test_to_escaped_text_strips_html_and_returns_plain_text(): void
    {
        $md = '**bold**';

        $github = new GithubMarkdown;
        $github->html5 = true;
        $github->enableNewlines = true;

        $parsed = $github->parse($md);

        $htmlPurifier = Mockery::mock('HTMLPurifier');
        $htmlPurifier->shouldReceive('purify')
            ->once()
            ->with($parsed, Mockery::type(\HTMLPurifier_Config::class))
            ->andReturn('bold');

        $sut = new MarkdownService($github, $htmlPurifier);

        $res = $sut->toEscapedText($md);

        $this->assertSame('bold', $res);
    }
}
