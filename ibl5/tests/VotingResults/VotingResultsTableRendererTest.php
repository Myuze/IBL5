<?php

declare(strict_types=1);

require_once __DIR__ . '/../bootstrap.php';

use PHPUnit\Framework\TestCase;
use Voting\VotingResultsTableRenderer;

final class VotingResultsTableRendererTest extends TestCase
{
    public function testRenderTablesEscapesContentAndPreservesStructure(): void
    {
        $renderer = new VotingResultsTableRenderer();

        $html = $renderer->renderTables([
            [
                'title' => 'Test & Title',
                'rows' => [
                    ['name' => 'Alice <One>', 'votes' => 10],
                    ['name' => 'Bob "Two"', 'votes' => 5],
                ],
            ],
        ]);

        $this->assertStringContainsString('<h2 style="text-align: center;">Test &amp; Title</h2>', $html);
        $this->assertStringContainsString('style="width: min(100%, 420px); border-collapse: collapse; margin: 0 auto 1.5rem;"', $html);
        $this->assertStringContainsString('style="border-bottom: 1px solid #eee; padding: 0.35rem 0.75rem;">Alice &lt;One&gt;</td><td style="border-bottom: 1px solid #eee; padding: 0.35rem 0.75rem;">10</td>', $html);
        $this->assertStringContainsString('style="border-bottom: 1px solid #eee; padding: 0.35rem 0.75rem; background-color: #f8f9fb;">Bob &quot;Two&quot;</td><td style="border-bottom: 1px solid #eee; padding: 0.35rem 0.75rem; background-color: #f8f9fb;">5</td>', $html);
        $this->assertStringContainsString('<th style="border-bottom: 2px solid #ccc; text-align: left; padding: 0.4rem 0.75rem; font-weight: 600;">Votes</th>', $html);
    }

    public function testRenderTablesOutputsEmptyTableWhenNoRows(): void
    {
        $renderer = new VotingResultsTableRenderer();

        $html = $renderer->renderTables([
            [
                'title' => 'Empty Category',
                'rows' => [],
            ],
        ]);

        $this->assertStringContainsString('<h2 style="text-align: center;">Empty Category</h2>', $html);
        $this->assertStringContainsString('<th style="border-bottom: 2px solid #ccc; text-align: left; padding: 0.4rem 0.75rem; font-weight: 600;">Player</th>', $html);
        $this->assertStringNotContainsString('<td></td><td></td>', $html);
    }
}
