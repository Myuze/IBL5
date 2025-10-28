<?php

declare(strict_types=1);

require_once __DIR__ . '/../bootstrap.php';

use PHPUnit\Framework\TestCase;
use Voting\VotingResultsService;

final class VotingResultsServiceTest extends TestCase
{
    private FakeVotingDatabase $database;
    private VotingResultsService $service;

    protected function setUp(): void
    {
        $this->database = new FakeVotingDatabase();
        $this->service = new VotingResultsService($this->database);
    }

    public function testGetAllStarResultsReturnsSortedTotals(): void
    {
        $this->database->queueResults([
            [
                ['name' => '', 'votes' => '9'],
                ['name' => 'Mason Lee', 'votes' => '5'],
                ['name' => 'Aaron Smith', 'votes' => '2'],
                ['name' => 'Zeke Adams', 'votes' => '2'],
            ],
            [],
            [],
            [],
        ]);

        $results = $this->service->getAllStarResults();

        $this->assertCount(4, $results);
        $this->assertSame('Eastern Conference Frontcourt', $results[0]['title']);
        $this->assertSame([
            ['name' => VotingResultsService::BLANK_BALLOT_LABEL, 'votes' => 9],
            ['name' => 'Mason Lee', 'votes' => 5],
            ['name' => 'Aaron Smith', 'votes' => 2],
            ['name' => 'Zeke Adams', 'votes' => 2],
        ], $results[0]['rows']);

        $queries = $this->database->getExecutedQueries();
        $this->assertCount(4, $queries);
    $this->assertStringContainsString('East_F1', $queries[0]);
    $this->assertStringContainsString('East_B4', $queries[1]);
    $this->assertStringContainsString('West_F4', $queries[2]);
    $this->assertStringContainsString('West_B4', $queries[3]);
    }

    public function testGetEndOfYearResultsReturnsWeightedTotals(): void
    {
        $this->database->queueResults([
            [
                ['name' => 'Alana Cruz', 'votes' => '21'],
                ['name' => 'Bree Jones', 'votes' => '19'],
            ],
            [
                ['name' => 'Chris Owens', 'votes' => '13'],
            ],
            [
                ['name' => 'Dev Patel', 'votes' => '11'],
            ],
            [
                ['name' => 'Eddie Reed', 'votes' => '9'],
            ],
        ]);

        $results = $this->service->getEndOfYearResults();

        $this->assertCount(4, $results);
        $this->assertSame('Most Valuable Player', $results[0]['title']);
        $this->assertSame([
            ['name' => 'Alana Cruz', 'votes' => 21],
            ['name' => 'Bree Jones', 'votes' => 19],
        ], $results[0]['rows']);

        $queries = $this->database->getExecutedQueries();
        $this->assertCount(4, $queries);
        $this->assertStringContainsString('MVP_1', $queries[0]);
        $this->assertStringContainsString('3 AS score', $queries[0]);
        $this->assertStringContainsString('Six_2', $queries[1]);
        $this->assertStringContainsString('ROY_3', $queries[2]);
        $this->assertStringContainsString('GM_1', $queries[3]);
    }
}

/**
 * Lightweight in-memory database stub for deterministic testing.
 */
final class FakeVotingDatabase
{
    /** @var array<int, array<int, array{name: string, votes: string}>> */
    private array $resultsQueue = [];

    /** @var array<int, string> */
    private array $executedQueries = [];

    public function queueResults(array $resultsQueue): void
    {
        $this->resultsQueue = $resultsQueue;
        $this->executedQueries = [];
    }

    public function sql_query(string $query): FakeVotingResult
    {
        $this->executedQueries[] = $query;
        $data = array_shift($this->resultsQueue) ?? [];

        return new FakeVotingResult($data);
    }

    public function sql_fetch_assoc(FakeVotingResult $result): array|false
    {
        return $result->fetchAssoc();
    }

    public function getExecutedQueries(): array
    {
        return $this->executedQueries;
    }
}

final class FakeVotingResult
{
    /** @var array<int, array{name: string, votes: string}> */
    private array $rows;

    private int $position = 0;

    public function __construct(array $rows)
    {
        $this->rows = array_values($rows);
    }

    public function fetchAssoc(): array|false
    {
        if ($this->position >= count($this->rows)) {
            return false;
        }

        return $this->rows[$this->position++];
    }
}
