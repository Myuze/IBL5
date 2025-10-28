<?php

namespace Nuke;

class Header
{
    public static function head()
    {
        global $sitename, $pagetitle;
        $ThemeSel = get_theme();
        
        // Calculate the correct base path for resources
        $scriptPath = $_SERVER['SCRIPT_NAME'];
        $documentRoot = $_SERVER['DOCUMENT_ROOT'];
        $currentFile = $_SERVER['SCRIPT_FILENAME'];
        
        // Calculate how many directory levels deep we are from the application root
        $iblRoot = $documentRoot . '/ibl5';
        $relativePath = '';
        
        // If we're in a subdirectory of ibl5, calculate the relative path back to root
        if (strpos($currentFile, $iblRoot) === 0) {
            $relativeFromIblRoot = substr(dirname($currentFile), strlen($iblRoot));
            if ($relativeFromIblRoot !== '') {
                // Count directory levels and build relative path
                $levels = substr_count(trim($relativeFromIblRoot, '/'), '/') + 1;
                $relativePath = str_repeat('../', $levels);
            }
        }
        
        include_secure("themes/$ThemeSel/theme.php");
        echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">';
        echo '<html xmlns="http://www.w3.org/1999/xhtml">';
        echo "<head>\n";
        echo "<title>$sitename $pagetitle</title>\n";
        echo '<meta name="google-site-verification" content="3y3xJYDHSYUitn7cbfFfI6C2BiK_q66dtRfykpzHW5w" />';
        echo "<script src=\"{$relativePath}jslib/sorttable.js\"></script>";

        include "includes/meta.php";
        include "includes/javascript.php";

        if (file_exists("themes/$ThemeSel/images/favicon.ico")) {
            echo "<link REL=\"shortcut icon\" HREF=\"{$relativePath}themes/$ThemeSel/images/favicon.ico\" TYPE=\"image/x-icon\">\n";
        }
        echo "<link rel=\"alternate\" type=\"application/rss+xml\" title=\"RSS\" href=\"{$relativePath}backend.php\">\n";
        echo "<LINK REL=\"StyleSheet\" HREF=\"{$relativePath}themes/$ThemeSel/style/style.css\" TYPE=\"text/css\">\n\n\n";
        if (file_exists("includes/custom_files/custom_head.php")) {
            include_secure("includes/custom_files/custom_head.php");
        }
        echo "\n\n\n</head>\n\n";
        if (file_exists("includes/custom_files/custom_header.php")) {
            include_secure("includes/custom_files/custom_header.php");
        }
        themeheader();
    }

    public static function header()
    {
        define('NUKE_HEADER', true);
        require_once $_SERVER['DOCUMENT_ROOT'] . '/ibl5/mainfile.php';

        online();
        Header::head();
        include "includes/counter.php";
        if (defined('HOME_FILE')) {
            message_box();
            blocks("Center");
        }
    }
}