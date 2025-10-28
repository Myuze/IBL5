<?php

if (!defined('BLOCK_FILE')) {
    Header("Location: ../index.php");
    die();
}

$content = '
<center>
    <a href="https://discord.com/invite/QXwBQxR"><img border="0" src="images/discord.png"></a><a href="https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=YP2VUUQ7MYQTW&lc=US%C2%A4cy_code=USD&bn=PP-DonationsBF:btn_donateCC_LG.gif:NonHosted"><img border="0" src="images/ibl/donate.png"></a>
</center>
<br><br>

<strong>Community</strong>
<ul>
    <li><a href="https://discord.com/invite/QXwBQxR">Discord Server</a></li>
    <li><a href="/ibl5/pages/contactList.php">GM Contact List</a></li>
</ul>

<strong>Associated Leagues</strong>
<ul>
    <li><a href="http://www.thakfu.com/ptf/index.php">Prime Time Football</a></li>
</ul>

<strong>Current Season</strong>
<ul>
    <li><a href="modules.php?name=Content&pa=showpage&pid=4">Standings</a></li>
    <li><a href="modules.php?name=Schedule">Schedule</a></li>
    <li><a href="modules.php?name=Team&op=injuries">Injuries</a></li>
    <li><a href="modules.php?name=Team&op=team&tid=0">Waiver Wire</a></li>
    <li><a href="modules.php?name=Player_Search">Player Database</a></li>
    <li><a href="modules.php?name=Draft_Pick_Locator">Draft Pick Locator</a></li>
    <li><a href="modules.php?name=Cap_Info">Cap Space</a></li>
    <li><a href="/ibl5/pages/freeAgencyPreview.php">Free Agency Preview</a></li>
    <li><a href="ibl/IBL">JSB Export</a></li>
</ul>

<strong>Statistics</strong>
<ul>
    <li><a href="modules.php?name=Chunk_Stats&op=season">League Leaders</a></li>
    <li><a href="modules.php?name=League_Starters"><span style="color:red;">League Starters (NEW!)</span></a></li>
    <li><a href="modules.php?name=Chunk_Stats&op=chunk">Sim Leaders</a></li>
    <li><a href="modules.php?name=Compare_Players">Compare Players</a></li>
    <li><a href="/ibl5/pages/seasonHighs.php">Season Highs</a></li>
    <li><a href="modules.php?name=Series_Records">Series Records</a></li>
    <li><a href="modules.php?name=League_Stats">Team Off/Def Stats</a></li>
</ul>

<strong>IBL History</strong>
<ul>
    <li><a href="modules.php?name=Content&pa=showpage&pid=5">Season Archive</a></li>
    <li><a href="modules.php?name=Franchise_History">Franchise History</a></li>
    <li><a href="modules.php?name=Content&pa=showpage&pid=8">Record Holders</a></li>
    <li><a href="modules.php?name=Stories_Archive">Transaction History</a></li>
    <li><a href="modules.php?name=Player_Awards">Award History</a></li>
    <li><a href="/ibl5/pages/allStarAppearances.php">All-Star Appearances</a></li>
    <li><a href="modules.php?name=Season_Leaders">Season Leaderboards</a></li>
    <li><a href="modules.php?name=Leaderboards">Career Leaderboards</a></li>
    <li><a href="/ibl5/pages/draftHistory.php">Draft History</a></li>
    <li><a href="../iblforum/forum.php">Forums [archived]</a></li>
    <li><a href="../previous-ibl-archive">v2/v3 Archive</a></li>
</ul>

<strong>1 vs 1 Games</strong>
<ul>
    <li><a href="modules.php?name=One-on-One">1-On-1 Game</a></li>
</ul>
';
