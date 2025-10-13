<?php

require $_SERVER['DOCUMENT_ROOT'] . '/ibl5/mainfile.php';

$offerId = $_POST['offer'];

if ($offerId != NULL) {
    $tradeProcessor = new Trading_TradeProcessor($db);
    $result = $tradeProcessor->processTrade($offerId);
    
    if ($result['success']) {
        // Trade processed successfully
        echo "Trade accepted!<p>";
    } else {
        echo "Error processing trade: " . ($result['error'] ?? 'Unknown error');
        exit;
    }
} else {
    echo "Nothing to see here!";
    exit;
}

?>

<HTML><HEAD><TITLE>Trade Offer Processing</TITLE>
<meta http-equiv="refresh" content="3;url=/ibl5/modules.php?name=Trading&op=reviewtrade">
</HEAD><BODY>
<a href="/ibl5/modules.php?name=Trading&op=reviewtrade">Click here to go back to the Trade Review page,</a><br>
or wait 3 seconds to be redirected automatically!
</BODY></HTML>
