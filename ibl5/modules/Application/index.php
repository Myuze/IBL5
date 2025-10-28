<?php

/************************************************************************/
/* PHP-NUKE: Web Portal System                                          */
/* ===========================                                          */
/*                                                                      */
/* Copyright (c) 2006 by Francisco Burzi                                */
/* http://phpnuke.org                                                   */
/*                                                                      */
/* This program is free software. You can redistribute it and/or modify */
/* it under the terms of the GNU General Public License as published by */
/* the Free Software Foundation; either version 2 of the License.       */
/************************************************************************/

if (!mb_eregi("modules.php", $_SERVER['PHP_SELF'])) {
    die("You can't access this file directly...");
}

$module_name = basename(dirname(__FILE__));
get_lang($module_name);
Nuke\Header::header();

?>

<b>IBL Prospective GM Application</b><br>
Please completely fill out the application. All fields are mandatory for submission.
<form name="contactform" method="post" action="/ibl5/modules/Application/send_form_email.php">
<table width="450px">
<tr>
 <td valign="top">
  <label for="first_name">First Name</label>
 </td>
 <td valign="top">
  <input  type="text" name="first_name" maxlength="50" size="104"><br><br>
 </td>
</tr>
<tr>
 <td valign="top"">
  <label for="last_name">Last Name</label>
 </td>
 <td valign="top">
  <input  type="text" name="last_name" maxlength="50" size="104"><br><br>
 </td>
</tr>
<tr>
 <td valign="top">
  <label for="email">Email Address</label>
 </td>
 <td valign="top">
  <input  type="text" name="email" maxlength="80" size="104"><br><br>
 </td>
</tr>
<tr>
 <td valign="top">
  <label for="skype">Skype Name</label>
 </td>
 <td valign="top">
  <input  type="text" name="skype" maxlength="80" size="104"><br><br>
 </td>
</tr>
<tr>
 <td valign="top">
  <label for="team">Preferred Team</label>
 </td>
 <td valign="top">
  <input  type="text" name="team" maxlength="80" size="104"><br><br>
 </td>
</tr>
<tr>
 <td valign="top">
  <label for="plan">What would your plan be for the team?</label>
 </td>
 <td valign="top">
  <textarea  name="plan" maxlength="1000" cols="100" rows="6"></textarea><br><br>
 </td>
</tr>

<tr>
 <td valign="top">
  <label for="mentor">Are you willing to have a GM mentor you in your first season?</label>
 </td>
 <td valign="top">
  <textarea  name="mentor" maxlength="1000" cols="100" rows="6"></textarea><br><br>
 </td>
</tr>
<br><br>
<tr>
 <td valign="top">
  <label for="assistant">Are you willing to be an assistant GM if there are no openings?</label>
 </td>
 <td valign="top">
  <textarea  name="assistant" maxlength="1000" cols="100" rows="6"></textarea><br><br>
 </td>
</tr>
<br><br>
<tr>
 <td valign="top">
  <label for="jsb">JSB League Experience</label>
 </td>
 <td valign="top">
  <textarea  name="jsb" maxlength="1000" cols="100" rows="6"></textarea><br><br>
 </td>
</tr>
<br><br>
<tr>
 <td valign="top">
  <label for="non_jsb">Non-JSB League Experience</label>
 </td>
 <td valign="top">
  <textarea  name="non_jsb" maxlength="1000" cols="100" rows="6"></textarea><br><br>
 </td>
</tr>
<tr>
 <td valign="top">
 <label for="referral">Referred by</label>
 </td>
 <td valign="top">
  <textarea  name="referral" maxlength="1000" cols="100" rows="6"></textarea>
 </td>
</tr>


<tr>
 <td colspan="2" style="text-align:center">
  <input type="submit" value="Submit">
 </td>
</tr>
</table>
</form>
