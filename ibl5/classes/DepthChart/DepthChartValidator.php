<?php

namespace DepthChart;

/**
 * Validates depth chart submissions
 */
class DepthChartValidator
{
    private $errors = [];
    
    /**
     * Validates a depth chart submission
     * 
     * @param array $depthChartData Processed depth chart data
     * @param string $phase Season phase (e.g., 'Playoffs', 'Regular Season')
     * @return bool True if valid, false otherwise
     */
    public function validate(array $depthChartData, string $phase): bool
    {
        $this->errors = [];
        
        // Set requirements based on season phase
        if ($phase === 'Playoffs') {
            $minActivePlayers = 10;
            $maxActivePlayers = 12;
            $minPositionDepth = 2;
        } else {
            $minActivePlayers = 12;
            $maxActivePlayers = 12;
            $minPositionDepth = 3;
        }
        
        // Validate active player count
        $this->validateActivePlayerCount(
            $depthChartData['activePlayers'],
            $minActivePlayers,
            $maxActivePlayers
        );
        
        // Validate position depths
        $positions = \JSB::PLAYER_POSITIONS;
        $this->validatePositionDepth($depthChartData['pos_1'], $positions[0], $minPositionDepth);
        $this->validatePositionDepth($depthChartData['pos_2'], $positions[1], $minPositionDepth);
        $this->validatePositionDepth($depthChartData['pos_3'], $positions[2], $minPositionDepth);
        $this->validatePositionDepth($depthChartData['pos_4'], $positions[3], $minPositionDepth);
        $this->validatePositionDepth($depthChartData['pos_5'], $positions[4], $minPositionDepth);
        
        // Validate no player is starting at multiple positions
        $this->validateNoMultipleStartingPositions(
            $depthChartData['hasStarterAtMultiplePositions'],
            $depthChartData['nameOfProblemStarter'] ?? ''
        );
        
        return empty($this->errors);
    }
    
    /**
     * Validates active player count
     */
    private function validateActivePlayerCount(int $activePlayers, int $min, int $max): void
    {
        if ($activePlayers < $min) {
            $this->errors[] = [
                'type' => 'active_players_min',
                'message' => "You must have at least $min active players in your lineup; you have $activePlayers.",
                'detail' => "Please press the \"Back\" button on your browser and activate " . ($min - $activePlayers) . " player(s)."
            ];
        }
        
        if ($activePlayers > $max) {
            $this->errors[] = [
                'type' => 'active_players_max',
                'message' => "You can't have more than $max active players in your lineup; you have $activePlayers.",
                'detail' => "Please press the \"Back\" button on your browser and deactivate " . ($activePlayers - $max) . " player(s)."
            ];
        }
    }
    
    /**
     * Validates position depth
     */
    private function validatePositionDepth(int $count, string $position, int $min): void
    {
        if ($count < $min) {
            $this->errors[] = [
                'type' => 'position_depth',
                'message' => "You must have at least $min players entered in $position slot &mdash; you have $count.",
                'detail' => "Please click the \"Back\" button on your browser and activate " . ($min - $count) . " player(s)."
            ];
        }
    }
    
    /**
     * Validates that no player is starting at multiple positions
     */
    private function validateNoMultipleStartingPositions(bool $hasMultiple, string $playerName): void
    {
        if ($hasMultiple) {
            $this->errors[] = [
                'type' => 'multiple_starting_positions',
                'message' => "$playerName is listed at more than one position in the starting lineup.",
                'detail' => "Please click the \"Back\" button on your browser and ensure they are only starting at ONE position."
            ];
        }
    }
    
    /**
     * Gets validation errors
     * 
     * @return array Array of error arrays
     */
    public function getErrors(): array
    {
        return $this->errors;
    }
    
    /**
     * Gets formatted error messages for display
     * 
     * @return string HTML-formatted error messages
     */
    public function getErrorMessagesHtml(): string
    {
        $html = '';
        foreach ($this->errors as $error) {
            $html .= "<font color=red><b>{$error['message']}</b></font><p>{$error['detail']}</center><p>";
        }
        return $html;
    }
}
