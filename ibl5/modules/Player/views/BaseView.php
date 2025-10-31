<?php

use Player\Player;

abstract class BaseView {
    protected $db;
    protected $player;
    protected $playerStats;
    protected $currentYear;

    public function __construct($db, Player $player, PlayerStats $playerStats) {
        $this->db = $db;
        $this->player = $player;
        $this->playerStats = $playerStats;
        $this->currentYear = $player->draftYear + $player->yearsOfExperience;
    }
    
    abstract public function render();
}
