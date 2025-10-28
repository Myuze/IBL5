<?php

declare(strict_types=1);

namespace Voting;

/**
 * Coordinates fetching and rendering of voting results based on the season phase
 */
class VotingResultsController
{
    public function __construct(
        private readonly VotingResultsService $service,
        private readonly VotingResultsTableRenderer $renderer,
        private readonly \Season $season
    ) {
    }

    /**
     * Renders the appropriate voting results for the active season phase
     * 
     * @return string HTML output
     */
    public function render(): string
    {
        if ($this->isRegularSeason()) {
            return $this->renderAllStarView();
        }

        return $this->renderEndOfYearView();
    }

    /**
     * Renders All-Star voting results regardless of season phase
     * 
     * @return string HTML output
     */
    public function renderAllStarView(): string
    {
        return $this->renderer->renderTables($this->service->getAllStarResults());
    }

    /**
     * Renders end-of-year awards voting results regardless of season phase
     * 
     * @return string HTML output
     */
    public function renderEndOfYearView(): string
    {
        return $this->renderer->renderTables($this->service->getEndOfYearResults());
    }

    private function isRegularSeason(): bool
    {
        return $this->season->phase === 'Regular Season';
    }
}
