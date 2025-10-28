<?php

declare(strict_types=1);

require_once __DIR__ . '/../bootstrap.php';

use PHPUnit\Framework\TestCase;
use Voting\VotingResultsController;
use Voting\VotingResultsService;
use Voting\VotingResultsTableRenderer;

final class VotingResultsControllerTest extends TestCase
{
    public function testRenderUsesAllStarResultsDuringRegularSeason(): void
    {
        $service = new StubVotingResultsService();
        $renderer = new StubVotingResultsRenderer();
        $season = $this->createSeason('Regular Season');

        $controller = new VotingResultsController($service, $renderer, $season);
        $output = $controller->render();

        $this->assertSame(1, $service->allStarCalls);
        $this->assertSame(0, $service->endOfYearCalls);
        $this->assertSame('All-Star', $output);
        $this->assertSame($service->allStarResponse, $renderer->lastRenderedTables);
    }

    public function testRenderUsesEndOfYearResultsOutsideRegularSeason(): void
    {
        $service = new StubVotingResultsService();
        $renderer = new StubVotingResultsRenderer();
        $season = $this->createSeason('Playoffs');

        $controller = new VotingResultsController($service, $renderer, $season);
        $output = $controller->render();

        $this->assertSame(0, $service->allStarCalls);
        $this->assertSame(1, $service->endOfYearCalls);
        $this->assertSame('End-Of-Year', $output);
        $this->assertSame($service->endOfYearResponse, $renderer->lastRenderedTables);
    }

    public function testExplicitRenderMethodsBypassSeasonPhase(): void
    {
        $service = new StubVotingResultsService();
        $renderer = new StubVotingResultsRenderer();
        $season = $this->createSeason('Free Agency');

        $controller = new VotingResultsController($service, $renderer, $season);

        $allStar = $controller->renderAllStarView();
        $endOfYear = $controller->renderEndOfYearView();

        $this->assertSame('All-Star', $allStar);
        $this->assertSame('End-Of-Year', $endOfYear);
        $this->assertSame(1, $service->allStarCalls);
        $this->assertSame(1, $service->endOfYearCalls);
    }

    private function createSeason(string $phase): Season
    {
        $season = new Season(new MockDatabase());
        $season->phase = $phase;

        return $season;
    }
}

final class StubVotingResultsService extends VotingResultsService
{
    public array $allStarResponse = [['title' => 'All-Star', 'rows' => []]];
    public array $endOfYearResponse = [['title' => 'End-Of-Year', 'rows' => []]];
    public int $allStarCalls = 0;
    public int $endOfYearCalls = 0;

    public function __construct()
    {
        // Don't call parent constructor - we don't need a database for stub
    }

    public function getAllStarResults(): array
    {
        $this->allStarCalls++;
        return $this->allStarResponse;
    }

    public function getEndOfYearResults(): array
    {
        $this->endOfYearCalls++;
        return $this->endOfYearResponse;
    }
}

final class StubVotingResultsRenderer extends VotingResultsTableRenderer
{
    /** @var array<int, array{title: string, rows: array<int, array{name: string, votes: int}>}> */
    public array $lastRenderedTables = [];

    public function renderTables(array $tables): string
    {
        $this->lastRenderedTables = $tables;
        return $tables[0]['title'] ?? '';
    }
}
