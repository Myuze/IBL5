<?php
namespace Updater;

class ScheduleUpdater {
    private $db;
    private $sharedFunctions;
    private $season;

    public function __construct($db, $sharedFunctions, $season) {
        $this->db = $db;
        $this->sharedFunctions = $sharedFunctions;
        $this->season = $season;
    }

    private function extractDate($rawDate) {
        if ($rawDate != false) {
            if (substr($rawDate, 0, 4) === "Post") {
                $rawDate = substr_replace($rawDate, 'June', 0, 4);
            }
            
            $month = ltrim(date('m', strtotime($rawDate)), '0');
            $day = ltrim(date('d', strtotime($rawDate)), '0');
            $year = date('Y', strtotime($rawDate));

            if ($this->season->phase == "Preseason") {
                $month = \Season::IBL_PRESEASON_MONTH;
            } elseif ($this->season->phase == "HEAT") {
                if ($month == 11) {
                    $month = \Season::IBL_HEAT_MONTH;
                }
            }
            
            $date = $year . "-" . $month . "-" . $day;
            
            return array(
                "date" => $date,
                "year" => $year,
                "month" => $month,
                "day" => $day
            );
        }
        return null;
    }

    private function extractBoxID($boxHREF) {
        return ltrim(rtrim($boxHREF, '.htm'), 'box');
    }

    public function update() {
        echo 'Updating the ibl_schedule database table...<p>';

        $log = '';

        if ($this->db->sql_query('TRUNCATE TABLE ibl_schedule')) {
            $log .= 'TRUNCATE TABLE ibl_schedule<p>';
        }

        $scheduleFilePath = $_SERVER['DOCUMENT_ROOT'] . '/ibl5/ibl/IBL/Schedule.htm';
        $schedule = new \DOMDocument();
        $schedule->loadHTMLFile($scheduleFilePath);
        $schedule->preserveWhiteSpace = false;
        $rows = $schedule->getElementsByTagName('tr');

        foreach ($rows as $row) {
            $checkThirdCell = $row->childNodes->item(2)->nodeValue ?? null;
            $checkSecondCell = $row->childNodes->item(1)->nodeValue ?? null;
            $checkFirstCell = $row->childNodes->item(0)->nodeValue ?? null;

            if ($checkSecondCell === null) {
                $fullDate = $this->extractDate($row->textContent);
                $date = $fullDate['date'] ?? null;
                $year = $fullDate['year'] ?? null;
            }

            if ($this->season->phase == "HEAT" && isset($fullDate['month']) && $fullDate['month'] != \Season::IBL_HEAT_MONTH) {
                continue;
            }

            if ($checkThirdCell !== null && $checkThirdCell !== "" && $checkFirstCell !== "visitor") {
                $boxID = null;
                $firstCell = $row->childNodes->item(1);
                
                if ($firstCell instanceof \DOMElement) {
                    $links = $firstCell->getElementsByTagName('a');
                    if ($links->length > 0) {
                        $boxLink = $links->item(0)->getAttribute('href');
                        $boxID = $this->extractBoxID($boxLink);
                    }
                }

                $visitorName = rtrim($row->childNodes->item(0)->textContent);
                $vScore = $row->childNodes->item(1)->textContent;
                $homeName = rtrim($row->childNodes->item(2)->textContent);
                $hScore = $row->childNodes->item(3)->textContent;

                if ($row->childNodes->item(1)->nodeValue === null || $row->childNodes->item(1)->nodeValue === "") {
                    $vScore = 0;
                    $hScore = 0;
                    if ($boxID > 99999 || $boxID === null) {
                        $boxID = $boxID + 1;
                    } else {
                        $boxID = 100000;
                    }
                }

                $visitorTID = $this->sharedFunctions->getTidFromTeamname($visitorName);
                $homeTID = $this->sharedFunctions->getTidFromTeamname($homeName);

                if ($vScore != 0 && $hScore != 0 && $boxID == null) {
                    echo "<b><font color=red>Script Error: box scores for games haven't been generated.<br>
                        Please delete and reupload the JSB HTML export with the box scores, then try again.</font></b>";
                    die();
                }

                if ($visitorTID !== null && $homeTID !== null) {
                    $sqlQueryString = "INSERT INTO ibl_schedule (
                        Year,
                        BoxID,
                        Date,
                        Visitor,
                        Vscore,
                        Home,
                        Hscore
                    ) VALUES (
                        $year,
                        $boxID,
                        '$date',
                        $visitorTID,
                        $vScore,
                        $homeTID,
                        $hScore
                    )";

                    if ($this->db->sql_query($sqlQueryString)) {
                        $log .= $sqlQueryString . '<br>';
                    } else {
                        echo "<b><font color=red>Script Error: Failed to insert schedule data for game between $visitorName and $homeName.</font></b>";
                        die();
                    }
                }
            }
        }
        \UI::displayDebugOutput($log, 'ibl_schedule SQL Queries');

        echo 'The ibl_schedule database table has been updated.<p><br>';
    }
}
