<?php

declare(strict_types=1);

namespace Voting;

/**
 * Retrieves aggregated voting results for All-Star and end-of-year awards
 */
class VotingResultsService
{
    private const ASG_TABLE = 'ibl_votes_ASG';
    private const EOY_TABLE = 'ibl_votes_EOY';
    public const BLANK_BALLOT_LABEL = '(No Selection Recorded)';

    /**
     * Ballot columns grouped by All-Star voting category
     * 
     * @var array
     */
    private const ALL_STAR_CATEGORIES = [
        'Eastern Conference Frontcourt' => ['East_F1', 'East_F2', 'East_F3', 'East_F4'],
        'Eastern Conference Backcourt' => ['East_B1', 'East_B2', 'East_B3', 'East_B4'],
        'Western Conference Frontcourt' => ['West_F1', 'West_F2', 'West_F3', 'West_F4'],
        'Western Conference Backcourt' => ['West_B1', 'West_B2', 'West_B3', 'West_B4'],
    ];

    /**
     * Ballot columns grouped by end-of-year award category and weighted score
     * 
     * @var array
     */
    private const END_OF_YEAR_CATEGORIES = [
        'Most Valuable Player' => ['MVP_1' => 3, 'MVP_2' => 2, 'MVP_3' => 1],
        'Sixth Man of the Year' => ['Six_1' => 3, 'Six_2' => 2, 'Six_3' => 1],
        'Rookie of the Year' => ['ROY_1' => 3, 'ROY_2' => 2, 'ROY_3' => 1],
        'GM of the Year' => ['GM_1' => 3, 'GM_2' => 2, 'GM_3' => 1],
    ];

    /**
     * Database connection implementing sql_* helpers
     * 
     * @var object
     */
    private $db;

    public function __construct(object $db)
    {
        $this->db = $db;
    }

    public function getAllStarResults(): array
    {
        $results = [];
        foreach (self::ALL_STAR_CATEGORIES as $title => $columns) {
            $results[] = [
                'title' => $title,
                'rows' => $this->fetchAllStarTotals($columns),
            ];
        }

        return $results;
    }

    public function getEndOfYearResults(): array
    {
        $results = [];
        foreach (self::END_OF_YEAR_CATEGORIES as $title => $ballots) {
            $results[] = [
                'title' => $title,
                'rows' => $this->fetchEndOfYearTotals($ballots),
            ];
        }

        return $results;
    }

    /**
     * Fetches All-Star voting totals for specified ballot columns
     * 
     * @param array $ballotColumns Array of ballot column names
     * @return array Array of rows with name and votes
     */
    private function fetchAllStarTotals(array $ballotColumns): array
    {
        $query = $this->buildAllStarQuery($ballotColumns);

        return $this->executeVoteQuery($query);
    }

    /**
     * Fetches end-of-year voting totals with weighted scores
     * 
     * @param array $ballotColumnsWithWeights Array of ballot columns and their point weights
     * @return array Array of rows with name and votes
     */
    private function fetchEndOfYearTotals(array $ballotColumnsWithWeights): array
    {
        $query = $this->buildEndOfYearQuery($ballotColumnsWithWeights);

        return $this->executeVoteQuery($query);
    }

    /**
     * Builds SQL query for All-Star voting totals
     * 
     * @param array $ballotColumns Array of ballot column names
     * @return string SQL query
     */
    private function buildAllStarQuery(array $ballotColumns): string
    {
        $selectStatements = [];
        foreach ($ballotColumns as $column) {
            $selectStatements[] = "SELECT {$column} AS name FROM " . self::ASG_TABLE;
        }

        $unionQuery = implode(' UNION ALL ', $selectStatements);

        $query = "SELECT COUNT(name) AS votes, name FROM ({$unionQuery}) AS ballot GROUP BY name HAVING COUNT(name) > 0 ORDER BY votes DESC, name ASC;";

        return $query;
    }

    /**
     * Builds SQL query for end-of-year voting totals with weighted scores
     * 
     * @param array $ballotColumnsWithWeights Array of ballot columns and their point weights
     * @return string SQL query
     */
    private function buildEndOfYearQuery(array $ballotColumnsWithWeights): string
    {
        $selectStatements = [];
        foreach ($ballotColumnsWithWeights as $column => $score) {
            $selectStatements[] = "SELECT {$column} AS name, {$score} AS score FROM " . self::EOY_TABLE;
        }

        $unionQuery = implode(' UNION ALL ', $selectStatements);

        $query = "SELECT SUM(score) AS votes, name FROM ({$unionQuery}) AS ballot GROUP BY name HAVING SUM(score) > 0 ORDER BY votes DESC, name ASC;";

        return $query;
    }

    /**
     * Executes a voting query and returns sorted results
     * 
     * @param string $query SQL query to execute
     * @return array Array of rows with name and votes
     */
    private function executeVoteQuery(string $query): array
    {
        $result = $this->db->sql_query($query);
        if ($result === false) {
            return [];
        }

        $rows = [];
        while ($record = $this->db->sql_fetch_assoc($result)) {
            $name = trim((string) ($record['name'] ?? ''));
            if ($name === '') {
                $name = self::BLANK_BALLOT_LABEL;
            }

            $votes = (int) ($record['votes'] ?? 0);
            $rows[] = [
                'name' => $name,
                'votes' => $votes,
            ];
        }

        return $rows;
    }
}
