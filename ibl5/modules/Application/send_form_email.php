<?php
if (isset($_POST['email'])) {

    // EDIT THE 2 LINES BELOW AS REQUIRED
    $email_to = "ibldepthcharts@gmail.com";
    $email_subject = "IBL Prospective GM Application";

    function died($error)
    {
        // your error code can go here
        echo "We are very sorry, but there were error(s) found with the form you submitted. ";
        echo "These errors appear below.<br/><br/>";
        echo $error . "<br/><br/>";
        echo "Please go back and fix these errors.<br/><br/>";
        die();
    }

    // validation expected data exists
    if (!isset($_POST['first_name']) ||
        !isset($_POST['last_name']) ||
        !isset($_POST['email']) ||
        !isset($_POST['skype']) ||
        !isset($_POST['team']) ||
        !isset($_POST['plan']) ||
        !isset($_POST['mentor']) ||
        !isset($_POST['assistant']) ||
        !isset($_POST['jsb']) ||
        !isset($_POST['non_jsb']) ||
        !isset($_POST['referral'])) {
        died('We are sorry, but there appears to be a problem with the form you submitted.');
    }

    $first_name = $_POST['first_name']; // required
    $last_name = $_POST['last_name']; // required
    $email_from = $_POST['email']; // required
    $skype = $_POST['skype']; // required
    $team = $_POST['team']; // required
    $plan = $_POST['plan']; // required
    $mentor = $_POST['mentor']; // required
    $assistant = $_POST['assistant']; // required
    $jsb = $_POST['jsb']; // required
    $non_jsb = $_POST['non_jsb']; // required
    $referral = $_POST['referral']; // required

    $error_message = "";
    $email_exp = '/^[A-Za-z0-9._%-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,4}$/';
    if (!preg_match($email_exp, $email_from)) {
        $error_message .= 'The Email Address you entered does not appear to be valid.<br/>';
    }
    $string_exp = "/^[A-Za-z .'-]+$/";
    if (!preg_match($string_exp, $first_name)) {
        $error_message .= 'The First Name you entered does not appear to be valid.<br/>';
    }
    if (!preg_match($string_exp, $last_name)) {
        $error_message .= 'The Last Name you entered does not appear to be valid.<br/>';
    }
    if (strlen($skype) < 2) {
        $error_message .= 'The skype info you entered does not appear to be valid.<br/>';
    }
    if (strlen($team) < 2) {
        $error_message .= 'The team info you entered does not appear to be valid.<br/>';
    }
    if (strlen($plan) < 2) {
        $error_message .= 'The plan info you entered does not appear to be valid.<br/>';
    }
    if (strlen($mentor) < 2) {
        $error_message .= 'The mentor info you entered does not appear to be valid.<br/>';
    }
    if (strlen($assistant) < 2) {
        $error_message .= 'The assistant info you entered does not appear to be valid.<br/>';
    }
    if (strlen($jsb) < 2) {
        $error_message .= 'The JSB info you entered does not appear to be valid.<br/>';
    }
    if (strlen($non_jsb) < 2) {
        $error_message .= 'The non-JSB info you entered does not appear to be valid.<br/>';
    }
    if (strlen($referral) < 2) {
        $error_message .= 'The referral info you entered does not appear to be valid.<br/>';
    }
    if (strlen($error_message) > 0) {
        died($error_message);
    }
    $email_message = "";

    function clean_string($string)
    {
        $bad = array("content-type", "bcc:", "to:", "cc:", "href");
        return str_replace($bad, "", $string);
    }

    $email_message .= "First Name: " . clean_string($first_name) . "\n";
    $email_message .= "Last Name: " . clean_string($last_name) . "\n";
    $email_message .= "Email: " . clean_string($email_from) . "\n";
    $email_message .= "Skype: " . clean_string($skype) . "\n";
    $email_message .= "Team Requested: " . clean_string($team) . "\n";
    $email_message .= "Plan: " . clean_string($plan) . "\n";
    $email_message .= "Mentor: " . clean_string($mentor) . "\n";
    $email_message .= "Assistant: " . clean_string($assistant) . "\n";
    $email_message .= "JSB Experience: " . clean_string($jsb) . "\n";
    $email_message .= "Non-JSB Experience: " . clean_string($non_jsb) . "\n";
    $email_message .= "Referred by: " . clean_string($referral) . "\n";

// create email headers
    $headers = 'From: ' . $email_from . "\r\n" .
    'Reply-To: ' . $email_from . "\r\n" .
    'X-Mailer: PHP/' . phpversion();
    @mail($email_to, $email_subject, $email_message, $headers);
    ?>

<!-- include your own success html here -->


Thank you for your interest in the IBL. Please post a message in the <a href='https://discord.com/invite/QXwBQxR'>IBL Discord</a> to confirm your interest and officially join the waiting list. We will review your application and be in touch with you very soon about current and/or future openings.

<?php
}
?>