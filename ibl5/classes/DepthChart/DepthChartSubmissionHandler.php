<?php

namespace DepthChart;

/**
 * Handles depth chart form submissions
 */
class DepthChartSubmissionHandler
{
    private $db;
    private $repository;
    private $processor;
    private $validator;
    private $view;
    
    public function __construct($db)
    {
        $this->db = $db;
        $this->repository = new DepthChartRepository($db);
        $this->processor = new DepthChartProcessor();
        $this->validator = new DepthChartValidator();
        $this->view = new DepthChartView($this->processor);
    }
    
    /**
     * Handles the depth chart submission
     * 
     * @param array $postData POST data
     * @return void Renders response
     */
    public function handleSubmission(array $postData): void
    {
        $season = new \Season($this->db);
        
        // Validate and sanitize team name
        $teamName = $this->sanitizeInput($postData['Team_Name'] ?? '');
        
        if (empty($teamName)) {
            echo "<font color=red><b>Error: Missing required team information.</b></font>";
            return;
        }
        
        // Process the submission data
        $processedData = $this->processor->processSubmission($postData);
        
        // Validate the submission
        $isValid = $this->validator->validate($processedData, $season->phase);
        
        if (!$isValid) {
            // Display errors
            $errorHtml = $this->validator->getErrorMessagesHtml();
            $this->view->renderSubmissionResult($teamName, $processedData['playerData'], false, $errorHtml);
            return;
        }
        
        // Update database
        $this->saveDepthChart($processedData['playerData'], $teamName);
        
        // Save to file and send email
        $this->saveDepthChartFile($teamName, $processedData['playerData']);
        
        // Display success
        $this->view->renderSubmissionResult($teamName, $processedData['playerData'], true);
    }
    
    /**
     * Sanitizes input string
     * 
     * @param string $input Input string
     * @return string Sanitized string
     */
    private function sanitizeInput(string $input): string
    {
        return trim(strip_tags($input));
    }
    
    /**
     * Saves depth chart data to database
     * 
     * @param array $playerData Array of player data
     * @param string $teamName Team name
     * @return void
     */
    private function saveDepthChart(array $playerData, string $teamName): void
    {
        foreach ($playerData as $player) {
            $this->repository->updatePlayerDepthChart($player['name'], $player);
        }
        
        $this->repository->updateTeamHistory($teamName);
    }
    
    /**
     * Saves depth chart to file and sends email
     * 
     * @param string $teamName Team name
     * @param array $playerData Player data
     * @return void
     */
    private function saveDepthChartFile(string $teamName, array $playerData): void
    {
        // Sanitize team name for file path (prevent directory traversal)
        $safeTeamName = preg_replace('/[^a-zA-Z0-9_\-\s]/', '', $teamName);
        $safeTeamName = str_replace(['..', '/', '\\'], '', $safeTeamName);
        
        if (empty($safeTeamName)) {
            echo "<font color=red>Invalid team name for file creation.</font>";
            return;
        }
        
        $csvContent = $this->processor->generateCsvContent($playerData);
        $filename = 'depthcharts/' . $safeTeamName . '.txt';
        
        // Verify the final path is within the expected directory
        $realPath = realpath(dirname($filename));
        $expectedPath = realpath('depthcharts');
        
        if ($realPath !== false && $expectedPath !== false && strpos($realPath, $expectedPath) === 0) {
            if (file_put_contents($filename, $csvContent)) {
                // Send email if not on localhost
                if (isset($_SERVER['SERVER_NAME']) && $_SERVER['SERVER_NAME'] != "localhost") {
                    // Sanitize email subject (PHP 8.1+ safe)
                    $rawSubject = $teamName . " Depth Chart";
                    // Remove HTML tags and encode special characters
                    $emailSubject = filter_var($rawSubject, FILTER_UNSAFE_RAW, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH);
                    $emailSubject = strip_tags($emailSubject);
                    $recipient = 'ibldepthcharts@gmail.com';
                    
                    // Use proper email headers
                    $headers = "From: ibldepthcharts@gmail.com\r\n";
                    $headers .= "Reply-To: ibldepthcharts@gmail.com\r\n";
                    $headers .= "X-Mailer: PHP/" . phpversion();
                    
                    mail($recipient, $emailSubject, $csvContent, $headers);
                }
            } else {
                echo "<font color=red>Depth chart failed to save properly; please contact the commissioner.</font>";
            }
        } else {
            echo "<font color=red>Invalid file path detected. Please contact the commissioner.</font>";
        }
    }
}
