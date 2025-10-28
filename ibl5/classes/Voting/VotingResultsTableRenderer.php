<?php

declare(strict_types=1);

namespace Voting;

/**
 * Renders voting results tables using the legacy module styling
 */
class VotingResultsTableRenderer
{
    private const METRIC_LABEL = 'Votes';
    private const TABLE_STYLE = 'width: min(100%, 420px); border-collapse: collapse; margin: 0 auto 1.5rem;';
    private const HEADER_CELL_STYLE = 'border-bottom: 2px solid #ccc; text-align: left; padding: 0.4rem 0.75rem; font-weight: 600;';
    private const ROW_CELL_STYLE = 'border-bottom: 1px solid #eee; padding: 0.35rem 0.75rem;';
    private const ROW_CELL_ALT_STYLE = 'border-bottom: 1px solid #eee; padding: 0.35rem 0.75rem; background-color: #f8f9fb;';

    /**
     * Renders multiple voting result tables
     * 
     * @param array $tables Array of tables with title and rows
     * @return string HTML output
     */
    public function renderTables(array $tables): string
    {
        $output = '';
        foreach ($tables as $table) {
            $title = $table['title'] ?? '';
            $rows = $table['rows'] ?? [];
            $output .= $this->renderTable($title, $rows);
        }

        return $output;
    }

    /**
     * Renders a single voting result table
     * 
     * @param string $title Table title
     * @param array $rows Table rows with name and votes
     * @return string HTML output
     */
    private function renderTable(string $title, array $rows): string
    {
        $escapedTitle = htmlspecialchars($title, \ENT_QUOTES, 'UTF-8');

        $rowsHtml = [];
        foreach ($rows as $index => $row) {
            $name = htmlspecialchars((string) ($row['name'] ?? ''), \ENT_QUOTES, 'UTF-8');
            $votes = (int) ($row['votes'] ?? 0);
            $cellStyle = ($index % 2 === 0) ? self::ROW_CELL_STYLE : self::ROW_CELL_ALT_STYLE;
            $rowsHtml[] = sprintf(
                '    <tr><td style="%s">%s</td><td style="%s">%d</td></tr>',
                $cellStyle,
                $name,
                $cellStyle,
                $votes
            );
        }

        $tableRowsHtml = $rowsHtml ? "\n" . implode("\n", $rowsHtml) . "\n" : "\n";
        
        $html =
            '<h2 style="text-align: center;">' . $escapedTitle . '</h2>' .
            '<table class="sortable" style="' . self::TABLE_STYLE . '">' .
            '<tr>' .
            '<th style="' . self::HEADER_CELL_STYLE . '">Player</th>' .
            '<th style="' . self::HEADER_CELL_STYLE . '">' . self::METRIC_LABEL . '</th>' .
            '</tr>' .
            $tableRowsHtml .
            '</table>';

        return $html;
    }
}
