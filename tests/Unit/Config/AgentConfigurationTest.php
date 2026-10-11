<?php

declare(strict_types=1);

namespace Tests\Unit\Config;

use Laravel\Boost\Install\Agents\ClaudeCode;
use Laravel\Boost\Install\Agents\Codex;
use Tests\Unit\TestCase;

class AgentConfigurationTest extends TestCase
{
    public function test_agents_use_the_same_guidelines_and_skill_sources(): void
    {
        $claude = $this->app->make(ClaudeCode::class);
        $codex = $this->app->make(Codex::class);

        $this->assertSame('AGENTS.md', $claude->guidelinesPath());
        $this->assertSame($claude->guidelinesPath(), $codex->guidelinesPath());
        $this->assertSame('.agents/skills', $claude->skillsPath());
        $this->assertSame($claude->skillsPath(), $codex->skillsPath());
    }

    public function test_claude_imports_the_shared_guidelines_without_a_copy(): void
    {
        $this->assertSame('@AGENTS.md', trim(file_get_contents(base_path('CLAUDE.md'))));
    }
}
