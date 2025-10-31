# ************************************************************
# Sequel Ace SQL dump
# Version 20095
#
# https://sequel-ace.com/
# https://github.com/Sequel-Ace/Sequel-Ace
#
# Host: iblhoops.net (MySQL 5.5.5-10.6.20-MariaDB-cll-lve)
# Database: iblhoops_ibl5
# Generation Time: 2025-10-29 23:40:08 +0000
# ************************************************************


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
SET NAMES utf8mb4;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE='NO_AUTO_VALUE_ON_ZERO', SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


# Dump of table cache
# ------------------------------------------------------------

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table cache_locks
# ------------------------------------------------------------

CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table failed_jobs
# ------------------------------------------------------------

CREATE TABLE `failed_jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table ibl_awards
# ------------------------------------------------------------

CREATE TABLE `ibl_awards` (
  `year` int(11) NOT NULL DEFAULT 0,
  `Award` varchar(128) NOT NULL DEFAULT '',
  `name` varchar(32) NOT NULL DEFAULT '',
  `table_ID` int(11) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`table_ID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table ibl_banners
# ------------------------------------------------------------

CREATE TABLE `ibl_banners` (
  `year` int(11) NOT NULL DEFAULT 0,
  `currentname` varchar(16) NOT NULL DEFAULT '',
  `bannername` varchar(16) NOT NULL DEFAULT '',
  `bannertype` int(11) NOT NULL DEFAULT 0
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table ibl_box_scores
# ------------------------------------------------------------

CREATE TABLE `ibl_box_scores` (
  `Date` date NOT NULL,
  `name` varchar(16) DEFAULT '',
  `pos` varchar(2) DEFAULT '',
  `pid` int(11) DEFAULT NULL,
  `visitorTID` int(11) DEFAULT NULL,
  `homeTID` int(11) DEFAULT NULL,
  `gameMIN` int(11) DEFAULT NULL,
  `game2GM` int(11) DEFAULT NULL,
  `game2GA` int(11) DEFAULT NULL,
  `gameFTM` int(11) DEFAULT NULL,
  `gameFTA` int(11) DEFAULT NULL,
  `game3GM` int(11) DEFAULT NULL,
  `game3GA` int(11) DEFAULT NULL,
  `gameORB` int(11) DEFAULT NULL,
  `gameDRB` int(11) DEFAULT NULL,
  `gameAST` int(11) DEFAULT NULL,
  `gameSTL` int(11) DEFAULT NULL,
  `gameTOV` int(11) DEFAULT NULL,
  `gameBLK` int(11) DEFAULT NULL,
  `gamePF` int(11) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table ibl_box_scores_teams
# ------------------------------------------------------------

CREATE TABLE `ibl_box_scores_teams` (
  `Date` date NOT NULL,
  `name` varchar(16) DEFAULT '',
  `gameOfThatDay` int(11) DEFAULT NULL,
  `visitorTeamID` int(11) DEFAULT NULL,
  `homeTeamID` int(11) DEFAULT NULL,
  `attendance` int(11) DEFAULT NULL,
  `capacity` int(11) DEFAULT NULL,
  `visitorWins` int(11) DEFAULT NULL,
  `visitorLosses` int(11) DEFAULT NULL,
  `homeWins` int(11) DEFAULT NULL,
  `homeLosses` int(11) DEFAULT NULL,
  `visitorQ1points` int(11) DEFAULT NULL,
  `visitorQ2points` int(11) DEFAULT NULL,
  `visitorQ3points` int(11) DEFAULT NULL,
  `visitorQ4points` int(11) DEFAULT NULL,
  `visitorOTpoints` int(11) DEFAULT NULL,
  `homeQ1points` int(11) DEFAULT NULL,
  `homeQ2points` int(11) DEFAULT NULL,
  `homeQ3points` int(11) DEFAULT NULL,
  `homeQ4points` int(11) DEFAULT NULL,
  `homeOTpoints` int(11) DEFAULT NULL,
  `gameMIN` int(11) DEFAULT NULL,
  `game2GM` int(11) DEFAULT NULL,
  `game2GA` int(11) DEFAULT NULL,
  `gameFTM` int(11) DEFAULT NULL,
  `gameFTA` int(11) DEFAULT NULL,
  `game3GM` int(11) DEFAULT NULL,
  `game3GA` int(11) DEFAULT NULL,
  `gameORB` int(11) DEFAULT NULL,
  `gameDRB` int(11) DEFAULT NULL,
  `gameAST` int(11) DEFAULT NULL,
  `gameSTL` int(11) DEFAULT NULL,
  `gameTOV` int(11) DEFAULT NULL,
  `gameBLK` int(11) DEFAULT NULL,
  `gamePF` int(11) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table ibl_demands
# ------------------------------------------------------------

CREATE TABLE `ibl_demands` (
  `name` varchar(32) NOT NULL DEFAULT '',
  `dem1` int(11) NOT NULL DEFAULT 0,
  `dem2` int(11) NOT NULL DEFAULT 0,
  `dem3` int(11) NOT NULL DEFAULT 0,
  `dem4` int(11) NOT NULL DEFAULT 0,
  `dem5` int(11) NOT NULL DEFAULT 0,
  `dem6` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table ibl_draft
# ------------------------------------------------------------

CREATE TABLE `ibl_draft` (
  `draft_id` int(11) NOT NULL AUTO_INCREMENT,
  `year` int(11) NOT NULL DEFAULT 0,
  `team` varchar(255) NOT NULL DEFAULT '',
  `player` varchar(255) NOT NULL DEFAULT '',
  `round` int(11) NOT NULL DEFAULT 0,
  `pick` int(11) NOT NULL DEFAULT 0,
  `date` datetime DEFAULT NULL,
  UNIQUE KEY `draft_id` (`draft_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table ibl_draft_class
# ------------------------------------------------------------

CREATE TABLE `ibl_draft_class` (
  `name` varchar(32) NOT NULL DEFAULT '',
  `pos` char(2) NOT NULL DEFAULT '',
  `age` int(11) NOT NULL DEFAULT 0,
  `team` varchar(128) NOT NULL DEFAULT '',
  `fga` int(11) NOT NULL DEFAULT 0,
  `fgp` int(11) NOT NULL DEFAULT 0,
  `fta` int(11) NOT NULL DEFAULT 0,
  `ftp` int(11) NOT NULL DEFAULT 0,
  `tga` int(11) NOT NULL DEFAULT 0,
  `tgp` int(11) NOT NULL DEFAULT 0,
  `orb` int(11) NOT NULL DEFAULT 0,
  `drb` int(11) NOT NULL DEFAULT 0,
  `ast` int(11) NOT NULL DEFAULT 0,
  `stl` int(11) NOT NULL DEFAULT 0,
  `tvr` int(11) NOT NULL DEFAULT 0,
  `blk` int(11) NOT NULL DEFAULT 0,
  `offo` int(11) NOT NULL DEFAULT 0,
  `offd` int(11) NOT NULL DEFAULT 0,
  `offp` int(11) NOT NULL DEFAULT 0,
  `offt` int(11) NOT NULL DEFAULT 0,
  `defo` int(11) NOT NULL DEFAULT 0,
  `defd` int(11) NOT NULL DEFAULT 0,
  `defp` int(11) NOT NULL DEFAULT 0,
  `deft` int(11) NOT NULL DEFAULT 0,
  `tal` int(11) NOT NULL DEFAULT 0,
  `skl` int(11) NOT NULL DEFAULT 0,
  `int` int(11) NOT NULL DEFAULT 0,
  `ranking` float DEFAULT 0,
  `invite` text DEFAULT NULL,
  `drafted` int(11) DEFAULT 0,
  `sta` int(11) DEFAULT 0,
  `id` int(11) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table ibl_draft_picks
# ------------------------------------------------------------

CREATE TABLE `ibl_draft_picks` (
  `pickid` int(11) NOT NULL AUTO_INCREMENT,
  `ownerofpick` varchar(32) NOT NULL DEFAULT '',
  `teampick` varchar(32) NOT NULL DEFAULT '',
  `year` varchar(4) NOT NULL DEFAULT '',
  `round` char(1) NOT NULL DEFAULT '',
  `notes` varchar(280) DEFAULT NULL,
  PRIMARY KEY (`pickid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table ibl_fa_offers
# ------------------------------------------------------------

CREATE TABLE `ibl_fa_offers` (
  `name` varchar(32) NOT NULL DEFAULT '',
  `team` varchar(32) NOT NULL DEFAULT '',
  `offer1` int(11) NOT NULL DEFAULT 0,
  `offer2` int(11) NOT NULL DEFAULT 0,
  `offer3` int(11) NOT NULL DEFAULT 0,
  `offer4` int(11) NOT NULL DEFAULT 0,
  `offer5` int(11) NOT NULL DEFAULT 0,
  `offer6` int(11) NOT NULL DEFAULT 0,
  `modifier` float NOT NULL DEFAULT 0,
  `random` float NOT NULL DEFAULT 0,
  `perceivedvalue` float NOT NULL DEFAULT 0,
  `MLE` int(11) NOT NULL DEFAULT 0,
  `LLE` int(11) NOT NULL DEFAULT 0,
  `primary_key` int(11) unsigned NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`primary_key`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table ibl_gm_history
# ------------------------------------------------------------

CREATE TABLE `ibl_gm_history` (
  `year` varchar(35) NOT NULL,
  `name` varchar(50) NOT NULL,
  `Award` varchar(350) NOT NULL,
  `prim` int(11) NOT NULL,
  PRIMARY KEY (`prim`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table ibl_heat_career_avgs
# ------------------------------------------------------------

CREATE TABLE `ibl_heat_career_avgs` (
  `pid` int(11) NOT NULL DEFAULT 0,
  `name` varchar(32) NOT NULL DEFAULT '',
  `games` int(11) NOT NULL DEFAULT 0,
  `minutes` decimal(8,2) NOT NULL DEFAULT 0.00,
  `fgm` decimal(8,2) NOT NULL,
  `fga` decimal(8,2) NOT NULL,
  `fgpct` decimal(8,3) NOT NULL DEFAULT 0.000,
  `ftm` decimal(8,2) NOT NULL,
  `fta` decimal(8,2) NOT NULL,
  `ftpct` decimal(8,3) NOT NULL DEFAULT 0.000,
  `tgm` decimal(8,2) NOT NULL,
  `tga` decimal(8,2) NOT NULL,
  `tpct` decimal(8,3) NOT NULL DEFAULT 0.000,
  `orb` decimal(8,2) NOT NULL DEFAULT 0.00,
  `reb` decimal(8,2) NOT NULL DEFAULT 0.00,
  `ast` decimal(8,2) NOT NULL DEFAULT 0.00,
  `stl` decimal(8,2) NOT NULL DEFAULT 0.00,
  `tvr` decimal(8,2) NOT NULL DEFAULT 0.00,
  `blk` decimal(8,2) NOT NULL DEFAULT 0.00,
  `pf` decimal(8,2) NOT NULL DEFAULT 0.00,
  `pts` decimal(8,2) NOT NULL DEFAULT 0.00,
  `retired` int(11) NOT NULL DEFAULT 0
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table ibl_heat_career_totals
# ------------------------------------------------------------

CREATE TABLE `ibl_heat_career_totals` (
  `pid` int(11) NOT NULL DEFAULT 0,
  `name` varchar(32) NOT NULL DEFAULT '',
  `games` int(11) NOT NULL DEFAULT 0,
  `minutes` int(11) NOT NULL DEFAULT 0,
  `fgm` int(11) NOT NULL DEFAULT 0,
  `fga` int(11) NOT NULL DEFAULT 0,
  `ftm` int(11) NOT NULL DEFAULT 0,
  `fta` int(11) NOT NULL DEFAULT 0,
  `tgm` int(11) NOT NULL DEFAULT 0,
  `tga` int(11) NOT NULL DEFAULT 0,
  `orb` int(11) NOT NULL DEFAULT 0,
  `reb` int(11) NOT NULL DEFAULT 0,
  `ast` int(11) NOT NULL DEFAULT 0,
  `stl` int(11) NOT NULL DEFAULT 0,
  `tvr` int(11) NOT NULL DEFAULT 0,
  `blk` int(11) NOT NULL DEFAULT 0,
  `pf` int(11) NOT NULL DEFAULT 0,
  `pts` int(11) NOT NULL DEFAULT 0,
  `retired` int(11) NOT NULL DEFAULT 0
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table ibl_heat_stats
# ------------------------------------------------------------

CREATE TABLE `ibl_heat_stats` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `year` int(11) NOT NULL DEFAULT 0,
  `pos` char(2) NOT NULL DEFAULT '',
  `name` varchar(32) NOT NULL DEFAULT '',
  `team` varchar(32) NOT NULL DEFAULT '',
  `games` int(11) NOT NULL DEFAULT 0,
  `minutes` int(11) NOT NULL DEFAULT 0,
  `fgm` int(11) NOT NULL DEFAULT 0,
  `fga` int(11) NOT NULL DEFAULT 0,
  `ftm` int(11) NOT NULL DEFAULT 0,
  `fta` int(11) NOT NULL DEFAULT 0,
  `tgm` int(11) NOT NULL DEFAULT 0,
  `tga` int(11) NOT NULL DEFAULT 0,
  `orb` int(11) NOT NULL DEFAULT 0,
  `reb` int(11) NOT NULL DEFAULT 0,
  `ast` int(11) NOT NULL DEFAULT 0,
  `stl` int(11) NOT NULL DEFAULT 0,
  `tvr` int(11) NOT NULL DEFAULT 0,
  `blk` int(11) NOT NULL DEFAULT 0,
  `pf` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table ibl_heat_win_loss
# ------------------------------------------------------------

CREATE TABLE `ibl_heat_win_loss` (
  `year` int(4) unsigned NOT NULL DEFAULT 0,
  `currentname` varchar(16) NOT NULL DEFAULT '',
  `namethatyear` varchar(16) NOT NULL DEFAULT '',
  `wins` tinyint(2) unsigned NOT NULL DEFAULT 0,
  `losses` tinyint(2) unsigned NOT NULL DEFAULT 0,
  `table_ID` int(11) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`table_ID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table ibl_hist
# ------------------------------------------------------------

CREATE TABLE `ibl_hist` (
  `pid` int(11) NOT NULL DEFAULT 0,
  `name` varchar(32) NOT NULL DEFAULT '',
  `year` int(11) NOT NULL DEFAULT 0,
  `team` varchar(32) NOT NULL DEFAULT '',
  `teamid` int(11) NOT NULL DEFAULT 0,
  `games` int(11) NOT NULL DEFAULT 0,
  `minutes` int(11) NOT NULL DEFAULT 0,
  `fgm` int(11) NOT NULL DEFAULT 0,
  `fga` int(11) NOT NULL DEFAULT 0,
  `ftm` int(11) NOT NULL DEFAULT 0,
  `fta` int(11) NOT NULL DEFAULT 0,
  `tgm` int(11) NOT NULL DEFAULT 0,
  `tga` int(11) NOT NULL DEFAULT 0,
  `orb` int(11) NOT NULL DEFAULT 0,
  `reb` int(11) NOT NULL DEFAULT 0,
  `ast` int(11) NOT NULL DEFAULT 0,
  `stl` int(11) NOT NULL DEFAULT 0,
  `blk` int(11) NOT NULL DEFAULT 0,
  `tvr` int(11) NOT NULL DEFAULT 0,
  `pf` int(11) NOT NULL DEFAULT 0,
  `pts` int(11) NOT NULL DEFAULT 0,
  `r_2ga` int(11) NOT NULL DEFAULT 0,
  `r_2gp` int(11) NOT NULL DEFAULT 0,
  `r_fta` int(11) NOT NULL DEFAULT 0,
  `r_ftp` int(11) NOT NULL DEFAULT 0,
  `r_3ga` int(11) NOT NULL DEFAULT 0,
  `r_3gp` int(11) NOT NULL DEFAULT 0,
  `r_orb` int(11) NOT NULL DEFAULT 0,
  `r_drb` int(11) NOT NULL DEFAULT 0,
  `r_ast` int(11) NOT NULL DEFAULT 0,
  `r_stl` int(11) NOT NULL DEFAULT 0,
  `r_blk` int(11) NOT NULL DEFAULT 0,
  `r_tvr` int(11) NOT NULL DEFAULT 0,
  `r_oo` int(11) NOT NULL DEFAULT 0,
  `r_do` int(11) NOT NULL DEFAULT 0,
  `r_po` int(11) NOT NULL DEFAULT 0,
  `r_to` int(11) NOT NULL DEFAULT 0,
  `r_od` int(11) NOT NULL DEFAULT 0,
  `r_dd` int(11) NOT NULL DEFAULT 0,
  `r_pd` int(11) NOT NULL DEFAULT 0,
  `r_td` int(11) NOT NULL DEFAULT 0,
  `salary` int(11) NOT NULL DEFAULT 0,
  `nuke_iblhist` int(11) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`nuke_iblhist`),
  UNIQUE KEY `unique_composite_key` (`pid`,`name`,`year`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table ibl_olympics_career_avgs
# ------------------------------------------------------------

CREATE TABLE `ibl_olympics_career_avgs` (
  `pid` int(11) NOT NULL DEFAULT 0,
  `name` varchar(32) NOT NULL DEFAULT '',
  `games` int(11) NOT NULL DEFAULT 0,
  `minutes` decimal(8,2) NOT NULL DEFAULT 0.00,
  `fgm` decimal(8,2) NOT NULL,
  `fga` decimal(8,2) NOT NULL,
  `fgpct` decimal(8,3) NOT NULL DEFAULT 0.000,
  `ftm` decimal(8,2) NOT NULL,
  `fta` decimal(8,2) NOT NULL,
  `ftpct` decimal(8,3) NOT NULL DEFAULT 0.000,
  `tgm` decimal(8,2) NOT NULL,
  `tga` decimal(8,2) NOT NULL,
  `tpct` decimal(8,3) NOT NULL DEFAULT 0.000,
  `orb` decimal(8,2) NOT NULL DEFAULT 0.00,
  `reb` decimal(8,2) NOT NULL DEFAULT 0.00,
  `ast` decimal(8,2) NOT NULL DEFAULT 0.00,
  `stl` decimal(8,2) NOT NULL DEFAULT 0.00,
  `tvr` decimal(8,2) NOT NULL DEFAULT 0.00,
  `blk` decimal(8,2) NOT NULL DEFAULT 0.00,
  `pf` decimal(8,2) NOT NULL DEFAULT 0.00,
  `pts` decimal(8,2) NOT NULL DEFAULT 0.00,
  `retired` int(11) NOT NULL DEFAULT 0
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table ibl_olympics_career_totals
# ------------------------------------------------------------

CREATE TABLE `ibl_olympics_career_totals` (
  `pid` int(11) NOT NULL DEFAULT 0,
  `name` varchar(32) NOT NULL DEFAULT '',
  `games` int(11) NOT NULL DEFAULT 0,
  `minutes` int(11) NOT NULL DEFAULT 0,
  `fgm` int(11) NOT NULL DEFAULT 0,
  `fga` int(11) NOT NULL DEFAULT 0,
  `ftm` int(11) NOT NULL DEFAULT 0,
  `fta` int(11) NOT NULL DEFAULT 0,
  `tgm` int(11) NOT NULL DEFAULT 0,
  `tga` int(11) NOT NULL DEFAULT 0,
  `orb` int(11) NOT NULL DEFAULT 0,
  `reb` int(11) NOT NULL DEFAULT 0,
  `ast` int(11) NOT NULL DEFAULT 0,
  `stl` int(11) NOT NULL DEFAULT 0,
  `tvr` int(11) NOT NULL DEFAULT 0,
  `blk` int(11) NOT NULL DEFAULT 0,
  `pf` int(11) NOT NULL DEFAULT 0,
  `pts` int(11) NOT NULL DEFAULT 0,
  `retired` int(11) NOT NULL DEFAULT 0
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table ibl_olympics_stats
# ------------------------------------------------------------

CREATE TABLE `ibl_olympics_stats` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `year` int(11) NOT NULL DEFAULT 0,
  `pos` char(2) NOT NULL DEFAULT '',
  `name` varchar(32) NOT NULL DEFAULT '',
  `team` varchar(32) NOT NULL DEFAULT '',
  `games` int(11) NOT NULL DEFAULT 0,
  `minutes` int(11) NOT NULL DEFAULT 0,
  `fgm` int(11) NOT NULL DEFAULT 0,
  `fga` int(11) NOT NULL DEFAULT 0,
  `ftm` int(11) NOT NULL DEFAULT 0,
  `fta` int(11) NOT NULL DEFAULT 0,
  `tgm` int(11) NOT NULL DEFAULT 0,
  `tga` int(11) NOT NULL DEFAULT 0,
  `orb` int(11) NOT NULL DEFAULT 0,
  `reb` int(11) NOT NULL DEFAULT 0,
  `ast` int(11) NOT NULL DEFAULT 0,
  `stl` int(11) NOT NULL DEFAULT 0,
  `tvr` int(11) NOT NULL DEFAULT 0,
  `blk` int(11) NOT NULL DEFAULT 0,
  `pf` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table ibl_one_on_one
# ------------------------------------------------------------

CREATE TABLE `ibl_one_on_one` (
  `gameid` int(11) NOT NULL DEFAULT 0,
  `playbyplay` text NOT NULL,
  `winner` varchar(32) NOT NULL DEFAULT '',
  `loser` varchar(32) NOT NULL DEFAULT '',
  `winscore` int(11) NOT NULL DEFAULT 0,
  `lossscore` int(11) NOT NULL DEFAULT 0,
  `owner` varchar(25) NOT NULL DEFAULT '',
  PRIMARY KEY (`gameid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table ibl_playoff_career_avgs
# ------------------------------------------------------------

CREATE TABLE `ibl_playoff_career_avgs` (
  `pid` int(11) NOT NULL DEFAULT 0,
  `name` varchar(32) NOT NULL DEFAULT '',
  `games` int(11) NOT NULL DEFAULT 0,
  `minutes` decimal(8,2) NOT NULL DEFAULT 0.00,
  `fgm` decimal(8,2) NOT NULL,
  `fga` decimal(8,2) NOT NULL,
  `fgpct` decimal(8,3) NOT NULL DEFAULT 0.000,
  `ftm` decimal(8,2) NOT NULL,
  `fta` decimal(8,2) NOT NULL,
  `ftpct` decimal(8,3) NOT NULL DEFAULT 0.000,
  `tgm` decimal(8,2) NOT NULL,
  `tga` decimal(8,2) NOT NULL,
  `tpct` decimal(8,3) NOT NULL DEFAULT 0.000,
  `orb` decimal(8,2) NOT NULL DEFAULT 0.00,
  `reb` decimal(8,2) NOT NULL DEFAULT 0.00,
  `ast` decimal(8,2) NOT NULL DEFAULT 0.00,
  `stl` decimal(8,2) NOT NULL DEFAULT 0.00,
  `tvr` decimal(8,2) NOT NULL DEFAULT 0.00,
  `blk` decimal(8,2) NOT NULL DEFAULT 0.00,
  `pf` decimal(8,2) NOT NULL DEFAULT 0.00,
  `pts` decimal(8,2) NOT NULL DEFAULT 0.00,
  `retired` int(11) NOT NULL DEFAULT 0
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table ibl_playoff_career_totals
# ------------------------------------------------------------

CREATE TABLE `ibl_playoff_career_totals` (
  `pid` int(11) NOT NULL DEFAULT 0,
  `name` varchar(32) NOT NULL DEFAULT '',
  `games` int(11) NOT NULL DEFAULT 0,
  `minutes` int(11) NOT NULL DEFAULT 0,
  `fgm` int(11) NOT NULL DEFAULT 0,
  `fga` int(11) NOT NULL DEFAULT 0,
  `ftm` int(11) NOT NULL DEFAULT 0,
  `fta` int(11) NOT NULL DEFAULT 0,
  `tgm` int(11) NOT NULL DEFAULT 0,
  `tga` int(11) NOT NULL DEFAULT 0,
  `orb` int(11) NOT NULL DEFAULT 0,
  `reb` int(11) NOT NULL DEFAULT 0,
  `ast` int(11) NOT NULL DEFAULT 0,
  `stl` int(11) NOT NULL DEFAULT 0,
  `tvr` int(11) NOT NULL DEFAULT 0,
  `blk` int(11) NOT NULL DEFAULT 0,
  `pf` int(11) NOT NULL DEFAULT 0,
  `pts` int(11) NOT NULL DEFAULT 0,
  `retired` int(11) NOT NULL DEFAULT 0
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table ibl_playoff_results
# ------------------------------------------------------------

CREATE TABLE `ibl_playoff_results` (
  `year` int(11) NOT NULL DEFAULT 0,
  `round` int(11) NOT NULL DEFAULT 0,
  `winner` varchar(32) NOT NULL DEFAULT '',
  `loser` varchar(32) NOT NULL DEFAULT '',
  `loser_games` int(11) NOT NULL DEFAULT 0,
  `id` int(11) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table ibl_playoff_stats
# ------------------------------------------------------------

CREATE TABLE `ibl_playoff_stats` (
  `year` int(11) NOT NULL DEFAULT 0,
  `pos` char(2) NOT NULL DEFAULT '',
  `name` varchar(32) NOT NULL DEFAULT '',
  `team` varchar(32) NOT NULL DEFAULT '',
  `games` int(11) NOT NULL DEFAULT 0,
  `minutes` int(11) NOT NULL DEFAULT 0,
  `fgm` int(11) NOT NULL DEFAULT 0,
  `fga` int(11) NOT NULL DEFAULT 0,
  `ftm` int(11) NOT NULL DEFAULT 0,
  `fta` int(11) NOT NULL DEFAULT 0,
  `tgm` int(11) NOT NULL DEFAULT 0,
  `tga` int(11) NOT NULL DEFAULT 0,
  `orb` int(11) NOT NULL DEFAULT 0,
  `reb` int(11) NOT NULL DEFAULT 0,
  `ast` int(11) NOT NULL DEFAULT 0,
  `stl` int(11) NOT NULL DEFAULT 0,
  `tvr` int(11) NOT NULL DEFAULT 0,
  `blk` int(11) NOT NULL DEFAULT 0,
  `pf` int(11) NOT NULL DEFAULT 0
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table ibl_plr
# ------------------------------------------------------------

CREATE TABLE `ibl_plr` (
  `ordinal` int(11) DEFAULT 0,
  `pid` int(11) NOT NULL DEFAULT 0,
  `name` varchar(32) DEFAULT '',
  `nickname` varchar(64) DEFAULT '',
  `age` char(2) DEFAULT '',
  `peak` int(11) DEFAULT 0,
  `tid` int(11) DEFAULT 0,
  `teamname` varchar(32) DEFAULT '',
  `pos` varchar(4) DEFAULT '',
  `sta` int(11) DEFAULT 0,
  `oo` int(11) DEFAULT 0,
  `od` int(11) DEFAULT 0,
  `do` int(11) DEFAULT 0,
  `dd` int(11) DEFAULT 0,
  `po` int(11) DEFAULT 0,
  `pd` int(11) DEFAULT 0,
  `to` int(11) DEFAULT 0,
  `td` int(11) DEFAULT 0,
  `Clutch` varchar(32) DEFAULT '',
  `Consistency` varchar(32) DEFAULT '',
  `PGDepth` int(11) DEFAULT 0,
  `SGDepth` int(11) DEFAULT 0,
  `SFDepth` int(11) DEFAULT 0,
  `PFDepth` int(11) DEFAULT 0,
  `CDepth` int(11) DEFAULT 0,
  `active` int(11) DEFAULT 0,
  `dc_PGDepth` int(11) DEFAULT 0,
  `dc_SGDepth` int(11) DEFAULT 0,
  `dc_SFDepth` int(11) DEFAULT 0,
  `dc_PFDepth` int(11) DEFAULT 0,
  `dc_CDepth` int(11) DEFAULT 0,
  `dc_active` int(11) DEFAULT 1,
  `dc_minutes` int(11) DEFAULT 0,
  `dc_of` int(11) DEFAULT 0,
  `dc_df` int(11) DEFAULT 0,
  `dc_oi` int(11) DEFAULT 0,
  `dc_di` int(11) DEFAULT 0,
  `dc_bh` int(11) DEFAULT 0,
  `stats_gs` int(11) DEFAULT 0,
  `stats_gm` int(11) DEFAULT 0,
  `stats_min` int(11) DEFAULT 0,
  `stats_fgm` int(11) DEFAULT 0,
  `stats_fga` int(11) DEFAULT 0,
  `stats_ftm` int(11) DEFAULT 0,
  `stats_fta` int(11) DEFAULT 0,
  `stats_3gm` int(11) DEFAULT 0,
  `stats_3ga` int(11) DEFAULT 0,
  `stats_orb` int(11) DEFAULT 0,
  `stats_drb` int(11) DEFAULT 0,
  `stats_ast` int(11) DEFAULT 0,
  `stats_stl` int(11) DEFAULT 0,
  `stats_to` int(11) DEFAULT 0,
  `stats_blk` int(11) DEFAULT 0,
  `stats_pf` int(11) DEFAULT 0,
  `talent` int(11) DEFAULT 0,
  `skill` int(11) DEFAULT 0,
  `intangibles` int(11) DEFAULT 0,
  `coach` varchar(16) DEFAULT '',
  `loyalty` varchar(16) DEFAULT '',
  `playingTime` varchar(16) DEFAULT '',
  `winner` varchar(16) DEFAULT '',
  `tradition` varchar(16) DEFAULT '',
  `security` varchar(16) DEFAULT '',
  `exp` int(11) DEFAULT 0,
  `bird` int(11) DEFAULT 0,
  `cy` int(11) DEFAULT 0,
  `cyt` int(11) DEFAULT 0,
  `cy1` int(11) DEFAULT 0,
  `cy2` int(11) DEFAULT 0,
  `cy3` int(11) DEFAULT 0,
  `cy4` int(11) DEFAULT 0,
  `cy5` int(11) DEFAULT 0,
  `cy6` int(11) DEFAULT 0,
  `sh_pts` int(11) DEFAULT 0,
  `sh_reb` int(11) DEFAULT 0,
  `sh_ast` int(11) DEFAULT 0,
  `sh_stl` int(11) DEFAULT 0,
  `sh_blk` int(11) DEFAULT 0,
  `s_dd` int(11) DEFAULT 0,
  `s_td` int(11) DEFAULT 0,
  `sp_pts` int(11) DEFAULT 0,
  `sp_reb` int(11) DEFAULT 0,
  `sp_ast` int(11) DEFAULT 0,
  `sp_stl` int(11) DEFAULT 0,
  `sp_blk` int(11) DEFAULT 0,
  `ch_pts` int(11) DEFAULT 0,
  `ch_reb` int(11) DEFAULT 0,
  `ch_ast` int(11) DEFAULT 0,
  `ch_stl` int(11) DEFAULT 0,
  `ch_blk` int(11) DEFAULT 0,
  `c_dd` int(11) DEFAULT 0,
  `c_td` int(11) DEFAULT 0,
  `cp_pts` int(11) DEFAULT 0,
  `cp_reb` int(11) DEFAULT 0,
  `cp_ast` int(11) DEFAULT 0,
  `cp_stl` int(11) DEFAULT 0,
  `cp_blk` int(11) DEFAULT 0,
  `car_gm` int(11) DEFAULT 0,
  `car_min` int(11) DEFAULT 0,
  `car_fgm` int(11) DEFAULT 0,
  `car_fga` int(11) DEFAULT 0,
  `car_ftm` int(11) DEFAULT 0,
  `car_fta` int(11) DEFAULT 0,
  `car_tgm` int(11) DEFAULT 0,
  `car_tga` int(11) DEFAULT 0,
  `car_orb` int(11) DEFAULT 0,
  `car_drb` int(11) DEFAULT 0,
  `car_reb` int(11) DEFAULT 0,
  `car_ast` int(11) DEFAULT 0,
  `car_stl` int(11) DEFAULT 0,
  `car_to` int(11) DEFAULT 0,
  `car_blk` int(11) DEFAULT 0,
  `car_pf` int(11) DEFAULT 0,
  `car_pts` int(11) DEFAULT 0,
  `r_fga` int(11) DEFAULT 0,
  `r_fgp` int(11) DEFAULT 0,
  `r_fta` int(11) DEFAULT 0,
  `r_ftp` int(11) DEFAULT 0,
  `r_tga` int(11) DEFAULT 0,
  `r_tgp` int(11) DEFAULT 0,
  `r_orb` int(11) DEFAULT 0,
  `r_drb` int(11) DEFAULT 0,
  `r_ast` int(11) DEFAULT 0,
  `r_stl` int(11) DEFAULT 0,
  `r_to` int(11) DEFAULT 0,
  `r_blk` int(11) DEFAULT 0,
  `r_foul` int(11) DEFAULT 0,
  `draftround` int(11) DEFAULT 0,
  `draftedby` varchar(32) DEFAULT '',
  `draftedbycurrentname` varchar(32) DEFAULT '',
  `draftyear` int(11) DEFAULT 0,
  `draftpickno` int(11) DEFAULT 0,
  `injured` int(11) DEFAULT 0,
  `htft` varchar(8) DEFAULT '',
  `htin` varchar(8) DEFAULT '',
  `wt` varchar(8) DEFAULT '',
  `retired` smallint(6) DEFAULT 0,
  `college` varchar(48) DEFAULT '',
  `car_playoff_min` int(11) DEFAULT 0,
  `car_preseason_min` int(11) DEFAULT 0,
  `droptime` int(11) DEFAULT 0,
  `temp` int(11) DEFAULT 0 COMMENT '2028 Playoff Mins',
  PRIMARY KEY (`pid`),
  KEY `name` (`name`),
  KEY `teamname` (`teamname`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table ibl_plr_chunk
# ------------------------------------------------------------

CREATE TABLE `ibl_plr_chunk` (
  `active` int(11) NOT NULL DEFAULT 0,
  `pid` int(11) NOT NULL DEFAULT 0,
  `ordinal` int(11) NOT NULL,
  `name` varchar(32) NOT NULL,
  `tid` int(11) NOT NULL DEFAULT 0,
  `teamname` varchar(32) NOT NULL DEFAULT '',
  `pos` varchar(4) NOT NULL,
  `altpos` varchar(4) NOT NULL DEFAULT '',
  `stats_gs` int(11) NOT NULL DEFAULT 0,
  `stats_gm` int(11) NOT NULL DEFAULT 0,
  `stats_min` int(11) NOT NULL DEFAULT 0,
  `stats_fgm` int(11) NOT NULL DEFAULT 0,
  `stats_fga` int(11) NOT NULL DEFAULT 0,
  `stats_ftm` int(11) NOT NULL DEFAULT 0,
  `stats_fta` int(11) NOT NULL DEFAULT 0,
  `stats_3gm` int(11) NOT NULL DEFAULT 0,
  `stats_3ga` int(11) NOT NULL DEFAULT 0,
  `stats_orb` int(11) NOT NULL DEFAULT 0,
  `stats_drb` int(11) NOT NULL DEFAULT 0,
  `stats_ast` int(11) NOT NULL DEFAULT 0,
  `stats_stl` int(11) NOT NULL DEFAULT 0,
  `stats_to` int(11) NOT NULL DEFAULT 0,
  `stats_blk` int(11) NOT NULL DEFAULT 0,
  `stats_pf` int(11) NOT NULL DEFAULT 0,
  `chunk` int(11) DEFAULT NULL,
  `qa` decimal(11,2) NOT NULL DEFAULT 0.00,
  `Season` int(11) NOT NULL,
  KEY `pid` (`pid`),
  KEY `pid_2` (`pid`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table ibl_power
# ------------------------------------------------------------

CREATE TABLE `ibl_power` (
  `TeamID` smallint(6) NOT NULL DEFAULT 0,
  `Team` varchar(20) NOT NULL DEFAULT '',
  `Division` varchar(20) NOT NULL DEFAULT '',
  `Conference` varchar(20) NOT NULL DEFAULT '',
  `ranking` decimal(6,1) NOT NULL DEFAULT 0.0,
  `win` smallint(6) NOT NULL DEFAULT 0,
  `loss` smallint(6) NOT NULL DEFAULT 0,
  `gb` decimal(6,1) NOT NULL DEFAULT 0.0,
  `conf_win` int(11) NOT NULL,
  `conf_loss` int(11) NOT NULL,
  `div_win` int(11) NOT NULL,
  `div_loss` int(11) NOT NULL,
  `home_win` int(11) NOT NULL,
  `home_loss` int(11) NOT NULL,
  `road_win` int(11) NOT NULL,
  `road_loss` int(11) NOT NULL,
  `last_win` int(11) NOT NULL,
  `last_loss` int(11) NOT NULL,
  `streak_type` varchar(1) NOT NULL DEFAULT '',
  `streak` int(11) NOT NULL,
  PRIMARY KEY (`Team`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table ibl_schedule
# ------------------------------------------------------------

CREATE TABLE `ibl_schedule` (
  `Year` year(4) NOT NULL DEFAULT 0000,
  `BoxID` int(11) NOT NULL DEFAULT 0,
  `Date` date NOT NULL,
  `Visitor` int(11) NOT NULL DEFAULT 0,
  `VScore` int(11) NOT NULL DEFAULT 0,
  `Home` int(11) NOT NULL DEFAULT 0,
  `HScore` int(11) NOT NULL DEFAULT 0,
  `SchedID` int(11) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`SchedID`),
  KEY `BoxID` (`BoxID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table ibl_season_career_avgs
# ------------------------------------------------------------

CREATE TABLE `ibl_season_career_avgs` (
  `pid` int(11) NOT NULL DEFAULT 0,
  `name` varchar(32) NOT NULL DEFAULT '',
  `games` int(11) NOT NULL DEFAULT 0,
  `minutes` decimal(8,2) NOT NULL DEFAULT 0.00,
  `fgm` decimal(8,2) NOT NULL,
  `fga` decimal(8,2) NOT NULL,
  `fgpct` decimal(8,3) NOT NULL DEFAULT 0.000,
  `ftm` decimal(8,2) NOT NULL,
  `fta` decimal(8,2) NOT NULL,
  `ftpct` decimal(8,3) NOT NULL DEFAULT 0.000,
  `tgm` decimal(8,2) NOT NULL,
  `tga` decimal(8,2) NOT NULL,
  `tpct` decimal(8,3) NOT NULL DEFAULT 0.000,
  `orb` decimal(8,2) NOT NULL DEFAULT 0.00,
  `reb` decimal(8,2) NOT NULL DEFAULT 0.00,
  `ast` decimal(8,2) NOT NULL DEFAULT 0.00,
  `stl` decimal(8,2) NOT NULL DEFAULT 0.00,
  `tvr` decimal(8,2) NOT NULL DEFAULT 0.00,
  `blk` decimal(8,2) NOT NULL DEFAULT 0.00,
  `pf` decimal(8,2) NOT NULL DEFAULT 0.00,
  `pts` decimal(8,2) NOT NULL DEFAULT 0.00,
  `retired` int(11) NOT NULL DEFAULT 0
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table ibl_settings
# ------------------------------------------------------------

CREATE TABLE `ibl_settings` (
  `name` varchar(128) NOT NULL,
  `value` varchar(128) NOT NULL,
  PRIMARY KEY (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table ibl_sim_dates
# ------------------------------------------------------------

CREATE TABLE `ibl_sim_dates` (
  `Sim` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `Start Date` varchar(11) DEFAULT NULL,
  `End Date` varchar(11) DEFAULT NULL,
  PRIMARY KEY (`Sim`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;



# Dump of table ibl_standings
# ------------------------------------------------------------

CREATE TABLE `ibl_standings` (
  `tid` int(10) unsigned NOT NULL,
  `team_name` varchar(16) NOT NULL DEFAULT '',
  `pct` float(4,3) unsigned DEFAULT NULL,
  `leagueRecord` varchar(5) DEFAULT '',
  `conference` varchar(7) DEFAULT '',
  `confRecord` varchar(5) NOT NULL DEFAULT '',
  `confGB` decimal(3,1) DEFAULT NULL,
  `division` varchar(16) DEFAULT '',
  `divRecord` varchar(5) NOT NULL DEFAULT '',
  `divGB` decimal(3,1) DEFAULT NULL,
  `homeRecord` varchar(5) NOT NULL DEFAULT '',
  `awayRecord` varchar(5) NOT NULL DEFAULT '',
  `gamesUnplayed` int(10) unsigned DEFAULT NULL,
  `confWins` int(10) unsigned DEFAULT NULL,
  `confLosses` int(10) unsigned DEFAULT NULL,
  `divWins` int(10) unsigned DEFAULT NULL,
  `divLosses` int(10) unsigned DEFAULT NULL,
  `homeWins` int(10) unsigned DEFAULT NULL,
  `homeLosses` int(10) unsigned DEFAULT NULL,
  `awayWins` int(10) unsigned DEFAULT NULL,
  `awayLosses` int(10) unsigned DEFAULT NULL,
  `confMagicNumber` int(10) DEFAULT NULL,
  `divMagicNumber` int(10) DEFAULT NULL,
  `clinchedConference` tinyint(1) DEFAULT NULL,
  `clinchedDivision` tinyint(1) DEFAULT NULL,
  `clinchedPlayoffs` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`tid`),
  KEY `team_name` (`team_name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table ibl_team_awards
# ------------------------------------------------------------

CREATE TABLE `ibl_team_awards` (
  `year` varchar(35) NOT NULL,
  `name` varchar(35) NOT NULL,
  `Award` varchar(350) NOT NULL,
  `ID` int(11) NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table ibl_team_defense_stats
# ------------------------------------------------------------

CREATE TABLE `ibl_team_defense_stats` (
  `teamID` int(11) NOT NULL DEFAULT 0,
  `name` varchar(32) NOT NULL DEFAULT '',
  `games` int(11) NOT NULL DEFAULT 0,
  `fgm` int(11) NOT NULL DEFAULT 0,
  `fga` int(11) NOT NULL DEFAULT 0,
  `ftm` int(11) NOT NULL DEFAULT 0,
  `fta` int(11) NOT NULL DEFAULT 0,
  `tgm` int(11) NOT NULL DEFAULT 0,
  `tga` int(11) NOT NULL DEFAULT 0,
  `orb` int(11) NOT NULL DEFAULT 0,
  `reb` int(11) NOT NULL DEFAULT 0,
  `ast` int(11) NOT NULL DEFAULT 0,
  `stl` int(11) NOT NULL DEFAULT 0,
  `tvr` int(11) NOT NULL DEFAULT 0,
  `blk` int(11) NOT NULL DEFAULT 0,
  `pf` int(11) NOT NULL DEFAULT 0,
  `minutes` int(11) DEFAULT 0
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table ibl_team_history
# ------------------------------------------------------------

CREATE TABLE `ibl_team_history` (
  `teamid` int(11) NOT NULL DEFAULT 0,
  `team_city` varchar(24) NOT NULL DEFAULT '',
  `team_name` varchar(16) NOT NULL DEFAULT '',
  `color1` varchar(6) NOT NULL DEFAULT '',
  `color2` varchar(6) NOT NULL DEFAULT '',
  `depth` varchar(100) NOT NULL,
  `sim_depth` varchar(100) NOT NULL,
  `asg_vote` varchar(100) NOT NULL,
  `eoy_vote` varchar(100) NOT NULL,
  `totwins` int(11) NOT NULL,
  `totloss` int(11) NOT NULL,
  `winpct` float(4,3) NOT NULL,
  `playoffs` int(11) NOT NULL,
  `div_titles` int(11) NOT NULL,
  `conf_titles` int(11) NOT NULL,
  `ibl_titles` int(11) NOT NULL,
  `heat_titles` int(11) NOT NULL,
  PRIMARY KEY (`teamid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table ibl_team_info
# ------------------------------------------------------------

CREATE TABLE `ibl_team_info` (
  `teamid` int(11) NOT NULL DEFAULT 0,
  `team_city` varchar(24) NOT NULL DEFAULT '',
  `team_name` varchar(16) NOT NULL DEFAULT '',
  `color1` varchar(6) NOT NULL DEFAULT '',
  `color2` varchar(6) NOT NULL DEFAULT '',
  `arena` varchar(255) NOT NULL DEFAULT '',
  `owner_name` varchar(32) NOT NULL DEFAULT '',
  `owner_email` varchar(48) NOT NULL DEFAULT '',
  `discordID` bigint(20) unsigned DEFAULT NULL,
  `skype` varchar(16) NOT NULL,
  `aim` varchar(48) NOT NULL DEFAULT '',
  `msn` varchar(48) NOT NULL DEFAULT '',
  `formerly_known_as` varchar(255) DEFAULT NULL,
  `Contract_Wins` int(11) NOT NULL DEFAULT 0,
  `Contract_Losses` int(11) NOT NULL DEFAULT 0,
  `Contract_AvgW` int(11) NOT NULL DEFAULT 0,
  `Contract_AvgL` int(11) NOT NULL DEFAULT 0,
  `Contract_Coach` decimal(3,2) NOT NULL DEFAULT 0.00,
  `Used_Extension_This_Chunk` int(11) NOT NULL DEFAULT 0,
  `Used_Extension_This_Season` int(11) DEFAULT 0,
  `HasMLE` int(11) NOT NULL DEFAULT 0,
  `HasLLE` int(11) NOT NULL DEFAULT 0,
  `chart` char(2) NOT NULL DEFAULT '',
  PRIMARY KEY (`teamid`),
  KEY `team_name` (`team_name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table ibl_team_offense_stats
# ------------------------------------------------------------

CREATE TABLE `ibl_team_offense_stats` (
  `teamID` int(11) NOT NULL DEFAULT 0,
  `name` varchar(32) NOT NULL DEFAULT '',
  `games` int(11) NOT NULL DEFAULT 0,
  `fgm` int(11) NOT NULL DEFAULT 0,
  `fga` int(11) NOT NULL DEFAULT 0,
  `ftm` int(11) NOT NULL DEFAULT 0,
  `fta` int(11) NOT NULL DEFAULT 0,
  `tgm` int(11) NOT NULL DEFAULT 0,
  `tga` int(11) NOT NULL DEFAULT 0,
  `orb` int(11) NOT NULL DEFAULT 0,
  `reb` int(11) NOT NULL DEFAULT 0,
  `ast` int(11) NOT NULL DEFAULT 0,
  `stl` int(11) NOT NULL DEFAULT 0,
  `tvr` int(11) NOT NULL DEFAULT 0,
  `blk` int(11) NOT NULL DEFAULT 0,
  `pf` int(11) NOT NULL DEFAULT 0,
  `minutes` int(11) DEFAULT 0
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table ibl_team_win_loss
# ------------------------------------------------------------

CREATE TABLE `ibl_team_win_loss` (
  `year` varchar(75) NOT NULL DEFAULT '0',
  `currentname` varchar(16) NOT NULL DEFAULT '',
  `namethatyear` varchar(40) NOT NULL,
  `wins` varchar(75) NOT NULL DEFAULT '0',
  `losses` varchar(75) NOT NULL DEFAULT '0',
  `table_ID` int(11) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`table_ID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table ibl_trade_autocounter
# ------------------------------------------------------------

CREATE TABLE `ibl_trade_autocounter` (
  `counter` int(11) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`counter`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table ibl_trade_cash
# ------------------------------------------------------------

CREATE TABLE `ibl_trade_cash` (
  `tradeOfferID` int(11) NOT NULL,
  `sendingTeam` varchar(16) NOT NULL DEFAULT '',
  `receivingTeam` varchar(16) NOT NULL DEFAULT '',
  `cy1` int(11) DEFAULT NULL,
  `cy2` int(11) DEFAULT NULL,
  `cy3` int(11) DEFAULT NULL,
  `cy4` int(11) DEFAULT NULL,
  `cy5` int(11) DEFAULT NULL,
  `cy6` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table ibl_trade_info
# ------------------------------------------------------------

CREATE TABLE `ibl_trade_info` (
  `tradeofferid` int(11) NOT NULL DEFAULT 0,
  `itemid` int(11) NOT NULL DEFAULT 0,
  `itemtype` varchar(128) NOT NULL DEFAULT '',
  `from` varchar(128) NOT NULL DEFAULT '',
  `to` varchar(128) NOT NULL DEFAULT '',
  `approval` varchar(128) NOT NULL DEFAULT ''
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table ibl_trade_queue
# ------------------------------------------------------------

CREATE TABLE `ibl_trade_queue` (
  `query` tinytext NOT NULL,
  `tradeline` tinytext DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table ibl_votes_ASG
# ------------------------------------------------------------

CREATE TABLE `ibl_votes_ASG` (
  `teamid` int(11) NOT NULL DEFAULT 0,
  `team_city` varchar(24) NOT NULL DEFAULT '',
  `team_name` varchar(16) NOT NULL DEFAULT '',
  `East_F1` varchar(255) DEFAULT NULL,
  `East_F2` varchar(255) DEFAULT NULL,
  `East_F3` varchar(255) DEFAULT NULL,
  `East_F4` varchar(255) DEFAULT NULL,
  `East_B1` varchar(255) DEFAULT NULL,
  `East_B2` varchar(255) DEFAULT NULL,
  `East_B3` varchar(255) DEFAULT NULL,
  `East_B4` varchar(255) DEFAULT NULL,
  `West_F1` varchar(255) DEFAULT NULL,
  `West_F2` varchar(255) DEFAULT NULL,
  `West_F3` varchar(255) DEFAULT NULL,
  `West_F4` varchar(255) DEFAULT NULL,
  `West_B1` varchar(255) DEFAULT NULL,
  `West_B2` varchar(255) DEFAULT NULL,
  `West_B3` varchar(255) DEFAULT NULL,
  `West_B4` varchar(255) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table ibl_votes_EOY
# ------------------------------------------------------------

CREATE TABLE `ibl_votes_EOY` (
  `teamid` int(11) NOT NULL DEFAULT 0,
  `team_city` varchar(24) NOT NULL DEFAULT '',
  `team_name` varchar(16) NOT NULL DEFAULT '',
  `MVP_1` varchar(255) DEFAULT NULL,
  `MVP_2` varchar(255) DEFAULT NULL,
  `MVP_3` varchar(255) DEFAULT NULL,
  `Six_1` varchar(255) DEFAULT NULL,
  `Six_2` varchar(255) DEFAULT NULL,
  `Six_3` varchar(255) DEFAULT NULL,
  `ROY_1` varchar(255) DEFAULT NULL,
  `ROY_2` varchar(255) DEFAULT NULL,
  `ROY_3` varchar(255) DEFAULT NULL,
  `GM_1` varchar(255) DEFAULT NULL,
  `GM_2` varchar(255) DEFAULT NULL,
  `GM_3` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`teamid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table job_batches
# ------------------------------------------------------------

CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table jobs
# ------------------------------------------------------------

CREATE TABLE `jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) unsigned NOT NULL,
  `reserved_at` int(10) unsigned DEFAULT NULL,
  `available_at` int(10) unsigned NOT NULL,
  `created_at` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table migrations
# ------------------------------------------------------------

CREATE TABLE `migrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table nuke_antiflood
# ------------------------------------------------------------

CREATE TABLE `nuke_antiflood` (
  `ip_addr` varchar(48) NOT NULL DEFAULT '',
  `time` varchar(14) NOT NULL DEFAULT '',
  KEY `ip_addr` (`ip_addr`),
  KEY `time` (`time`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table nuke_authors
# ------------------------------------------------------------

CREATE TABLE `nuke_authors` (
  `aid` varchar(25) NOT NULL DEFAULT '',
  `name` varchar(50) DEFAULT NULL,
  `url` varchar(255) NOT NULL DEFAULT '',
  `email` varchar(255) NOT NULL DEFAULT '',
  `pwd` varchar(40) DEFAULT NULL,
  `counter` int(11) NOT NULL DEFAULT 0,
  `radminsuper` tinyint(1) NOT NULL DEFAULT 1,
  `admlanguage` varchar(30) NOT NULL DEFAULT '',
  PRIMARY KEY (`aid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table nuke_autonews
# ------------------------------------------------------------

CREATE TABLE `nuke_autonews` (
  `anid` int(11) NOT NULL AUTO_INCREMENT,
  `catid` int(11) NOT NULL DEFAULT 0,
  `aid` varchar(30) NOT NULL DEFAULT '',
  `title` varchar(80) NOT NULL DEFAULT '',
  `time` varchar(19) NOT NULL DEFAULT '',
  `hometext` text NOT NULL,
  `bodytext` text NOT NULL,
  `topic` int(11) NOT NULL DEFAULT 1,
  `informant` varchar(20) NOT NULL DEFAULT '',
  `notes` text NOT NULL,
  `ihome` int(11) NOT NULL DEFAULT 0,
  `alanguage` varchar(30) NOT NULL DEFAULT '',
  `acomm` int(11) NOT NULL DEFAULT 0,
  `associated` text NOT NULL,
  PRIMARY KEY (`anid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table nuke_banned_ip
# ------------------------------------------------------------

CREATE TABLE `nuke_banned_ip` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ip_address` varchar(15) NOT NULL DEFAULT '',
  `reason` varchar(255) NOT NULL DEFAULT '',
  `date` date NOT NULL DEFAULT '0000-00-00',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table nuke_banner
# ------------------------------------------------------------

CREATE TABLE `nuke_banner` (
  `bid` int(11) NOT NULL AUTO_INCREMENT,
  `cid` int(11) NOT NULL DEFAULT 0,
  `name` varchar(50) NOT NULL DEFAULT '',
  `imptotal` int(11) NOT NULL DEFAULT 0,
  `impmade` int(11) NOT NULL DEFAULT 0,
  `clicks` int(11) NOT NULL DEFAULT 0,
  `imageurl` varchar(100) NOT NULL DEFAULT '',
  `clickurl` varchar(200) NOT NULL DEFAULT '',
  `alttext` varchar(255) NOT NULL DEFAULT '',
  `date` datetime DEFAULT NULL,
  `dateend` datetime DEFAULT NULL,
  `position` int(11) NOT NULL DEFAULT 0,
  `active` tinyint(1) NOT NULL DEFAULT 1,
  `ad_class` varchar(5) NOT NULL DEFAULT '',
  `ad_code` text NOT NULL,
  `ad_width` int(11) DEFAULT 0,
  `ad_height` int(11) DEFAULT 0,
  PRIMARY KEY (`bid`),
  KEY `cid` (`cid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table nuke_banner_clients
# ------------------------------------------------------------

CREATE TABLE `nuke_banner_clients` (
  `cid` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(60) NOT NULL DEFAULT '',
  `contact` varchar(60) NOT NULL DEFAULT '',
  `email` varchar(60) NOT NULL DEFAULT '',
  `login` varchar(10) NOT NULL DEFAULT '',
  `passwd` varchar(10) NOT NULL DEFAULT '',
  `extrainfo` text NOT NULL,
  PRIMARY KEY (`cid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table nuke_banner_plans
# ------------------------------------------------------------

CREATE TABLE `nuke_banner_plans` (
  `pid` int(11) NOT NULL AUTO_INCREMENT,
  `active` tinyint(1) NOT NULL DEFAULT 0,
  `name` varchar(255) NOT NULL DEFAULT '',
  `description` text NOT NULL,
  `delivery` varchar(10) NOT NULL DEFAULT '',
  `delivery_type` varchar(25) NOT NULL DEFAULT '',
  `price` varchar(25) NOT NULL DEFAULT '0',
  `buy_links` text NOT NULL,
  PRIMARY KEY (`pid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table nuke_banner_positions
# ------------------------------------------------------------

CREATE TABLE `nuke_banner_positions` (
  `apid` int(11) NOT NULL AUTO_INCREMENT,
  `position_number` int(11) NOT NULL DEFAULT 0,
  `position_name` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`apid`),
  KEY `position_number` (`position_number`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table nuke_banner_terms
# ------------------------------------------------------------

CREATE TABLE `nuke_banner_terms` (
  `terms_body` text NOT NULL,
  `country` varchar(255) NOT NULL DEFAULT ''
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table nuke_bbauth_access
# ------------------------------------------------------------

CREATE TABLE `nuke_bbauth_access` (
  `group_id` mediumint(9) NOT NULL DEFAULT 0,
  `forum_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `auth_view` tinyint(1) NOT NULL DEFAULT 0,
  `auth_read` tinyint(1) NOT NULL DEFAULT 0,
  `auth_post` tinyint(1) NOT NULL DEFAULT 0,
  `auth_reply` tinyint(1) NOT NULL DEFAULT 0,
  `auth_edit` tinyint(1) NOT NULL DEFAULT 0,
  `auth_delete` tinyint(1) NOT NULL DEFAULT 0,
  `auth_sticky` tinyint(1) NOT NULL DEFAULT 0,
  `auth_announce` tinyint(1) NOT NULL DEFAULT 0,
  `auth_vote` tinyint(1) NOT NULL DEFAULT 0,
  `auth_pollcreate` tinyint(1) NOT NULL DEFAULT 0,
  `auth_attachments` tinyint(1) NOT NULL DEFAULT 0,
  `auth_mod` tinyint(1) NOT NULL DEFAULT 0,
  KEY `group_id` (`group_id`),
  KEY `forum_id` (`forum_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table nuke_bbbanlist
# ------------------------------------------------------------

CREATE TABLE `nuke_bbbanlist` (
  `ban_id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `ban_userid` mediumint(9) NOT NULL DEFAULT 0,
  `ban_ip` varchar(8) NOT NULL DEFAULT '',
  `ban_email` varchar(255) DEFAULT NULL,
  `ban_time` int(11) DEFAULT NULL,
  `ban_expire_time` int(11) DEFAULT NULL,
  `ban_by_userid` mediumint(9) DEFAULT NULL,
  `ban_priv_reason` text DEFAULT NULL,
  `ban_pub_reason_mode` tinyint(1) DEFAULT NULL,
  `ban_pub_reason` text DEFAULT NULL,
  PRIMARY KEY (`ban_id`),
  KEY `ban_ip_user_id` (`ban_ip`,`ban_userid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table nuke_bbcategories
# ------------------------------------------------------------

CREATE TABLE `nuke_bbcategories` (
  `cat_id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `cat_title` varchar(100) DEFAULT NULL,
  `cat_order` mediumint(8) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`cat_id`),
  KEY `cat_order` (`cat_order`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table nuke_bbconfig
# ------------------------------------------------------------

CREATE TABLE `nuke_bbconfig` (
  `config_name` varchar(255) NOT NULL DEFAULT '',
  `config_value` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`config_name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table nuke_bbdisallow
# ------------------------------------------------------------

CREATE TABLE `nuke_bbdisallow` (
  `disallow_id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `disallow_username` varchar(25) DEFAULT NULL,
  PRIMARY KEY (`disallow_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table nuke_bbforum_prune
# ------------------------------------------------------------

CREATE TABLE `nuke_bbforum_prune` (
  `prune_id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `forum_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `prune_days` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `prune_freq` tinyint(3) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`prune_id`),
  KEY `forum_id` (`forum_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table nuke_bbforums
# ------------------------------------------------------------

CREATE TABLE `nuke_bbforums` (
  `forum_id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `cat_id` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `forum_name` varchar(150) DEFAULT NULL,
  `forum_desc` text DEFAULT NULL,
  `forum_status` tinyint(4) NOT NULL DEFAULT 0,
  `forum_order` mediumint(8) unsigned NOT NULL DEFAULT 1,
  `forum_posts` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `forum_topics` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `forum_last_post_id` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `prune_next` int(11) DEFAULT NULL,
  `prune_enable` tinyint(1) NOT NULL DEFAULT 1,
  `auth_view` tinyint(4) NOT NULL DEFAULT 0,
  `auth_read` tinyint(4) NOT NULL DEFAULT 0,
  `auth_post` tinyint(4) NOT NULL DEFAULT 0,
  `auth_reply` tinyint(4) NOT NULL DEFAULT 0,
  `auth_edit` tinyint(4) NOT NULL DEFAULT 0,
  `auth_delete` tinyint(4) NOT NULL DEFAULT 0,
  `auth_sticky` tinyint(4) NOT NULL DEFAULT 0,
  `auth_announce` tinyint(4) NOT NULL DEFAULT 0,
  `auth_vote` tinyint(4) NOT NULL DEFAULT 0,
  `auth_pollcreate` tinyint(4) NOT NULL DEFAULT 0,
  `auth_attachments` tinyint(4) NOT NULL DEFAULT 0,
  PRIMARY KEY (`forum_id`),
  KEY `forums_order` (`forum_order`),
  KEY `cat_id` (`cat_id`),
  KEY `forum_last_post_id` (`forum_last_post_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table nuke_bbgroups
# ------------------------------------------------------------

CREATE TABLE `nuke_bbgroups` (
  `group_id` mediumint(9) NOT NULL AUTO_INCREMENT,
  `group_type` tinyint(4) NOT NULL DEFAULT 1,
  `group_name` varchar(40) NOT NULL DEFAULT '',
  `group_description` varchar(255) NOT NULL DEFAULT '',
  `group_moderator` mediumint(9) NOT NULL DEFAULT 0,
  `group_single_user` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`group_id`),
  KEY `group_single_user` (`group_single_user`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table nuke_bbposts
# ------------------------------------------------------------

CREATE TABLE `nuke_bbposts` (
  `post_id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `topic_id` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `forum_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `poster_id` mediumint(9) NOT NULL DEFAULT 0,
  `post_time` int(11) NOT NULL DEFAULT 0,
  `poster_ip` varchar(8) NOT NULL DEFAULT '',
  `post_username` varchar(25) DEFAULT NULL,
  `enable_bbcode` tinyint(1) NOT NULL DEFAULT 1,
  `enable_html` tinyint(1) NOT NULL DEFAULT 0,
  `enable_smilies` tinyint(1) NOT NULL DEFAULT 1,
  `enable_sig` tinyint(1) NOT NULL DEFAULT 1,
  `post_edit_time` int(11) DEFAULT NULL,
  `post_edit_count` smallint(5) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`post_id`),
  KEY `forum_id` (`forum_id`),
  KEY `topic_id` (`topic_id`),
  KEY `poster_id` (`poster_id`),
  KEY `post_time` (`post_time`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table nuke_bbposts_text
# ------------------------------------------------------------

CREATE TABLE `nuke_bbposts_text` (
  `post_id` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `bbcode_uid` varchar(10) NOT NULL DEFAULT '',
  `post_subject` varchar(60) DEFAULT NULL,
  `post_text` text DEFAULT NULL,
  PRIMARY KEY (`post_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table nuke_bbprivmsgs
# ------------------------------------------------------------

CREATE TABLE `nuke_bbprivmsgs` (
  `privmsgs_id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `privmsgs_type` tinyint(4) NOT NULL DEFAULT 0,
  `privmsgs_subject` varchar(255) NOT NULL DEFAULT '0',
  `privmsgs_from_userid` mediumint(9) NOT NULL DEFAULT 0,
  `privmsgs_to_userid` mediumint(9) NOT NULL DEFAULT 0,
  `privmsgs_date` int(11) NOT NULL DEFAULT 0,
  `privmsgs_ip` varchar(8) NOT NULL DEFAULT '',
  `privmsgs_enable_bbcode` tinyint(1) NOT NULL DEFAULT 1,
  `privmsgs_enable_html` tinyint(1) NOT NULL DEFAULT 0,
  `privmsgs_enable_smilies` tinyint(1) NOT NULL DEFAULT 1,
  `privmsgs_attach_sig` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`privmsgs_id`),
  KEY `privmsgs_from_userid` (`privmsgs_from_userid`),
  KEY `privmsgs_to_userid` (`privmsgs_to_userid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table nuke_bbprivmsgs_text
# ------------------------------------------------------------

CREATE TABLE `nuke_bbprivmsgs_text` (
  `privmsgs_text_id` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `privmsgs_bbcode_uid` varchar(10) NOT NULL DEFAULT '0',
  `privmsgs_text` text DEFAULT NULL,
  PRIMARY KEY (`privmsgs_text_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table nuke_bbranks
# ------------------------------------------------------------

CREATE TABLE `nuke_bbranks` (
  `rank_id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `rank_title` varchar(50) NOT NULL DEFAULT '',
  `rank_min` mediumint(9) NOT NULL DEFAULT 0,
  `rank_max` mediumint(9) NOT NULL DEFAULT 0,
  `rank_special` tinyint(1) DEFAULT 0,
  `rank_image` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`rank_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table nuke_bbsearch_results
# ------------------------------------------------------------

CREATE TABLE `nuke_bbsearch_results` (
  `search_id` int(10) unsigned NOT NULL DEFAULT 0,
  `session_id` varchar(32) NOT NULL DEFAULT '',
  `search_array` text NOT NULL,
  `search_time` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`search_id`),
  KEY `session_id` (`session_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table nuke_bbsearch_wordlist
# ------------------------------------------------------------

CREATE TABLE `nuke_bbsearch_wordlist` (
  `word_text` varchar(50) NOT NULL DEFAULT '',
  `word_id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `word_common` tinyint(3) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`word_text`),
  KEY `word_id` (`word_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table nuke_bbsearch_wordmatch
# ------------------------------------------------------------

CREATE TABLE `nuke_bbsearch_wordmatch` (
  `post_id` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `word_id` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `title_match` tinyint(1) NOT NULL DEFAULT 0,
  KEY `word_id` (`word_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table nuke_bbsessions
# ------------------------------------------------------------

CREATE TABLE `nuke_bbsessions` (
  `session_id` char(32) NOT NULL DEFAULT '',
  `session_user_id` mediumint(9) NOT NULL DEFAULT 0,
  `session_start` int(11) NOT NULL DEFAULT 0,
  `session_time` int(11) NOT NULL DEFAULT 0,
  `session_ip` char(8) NOT NULL DEFAULT '0',
  `session_page` int(11) NOT NULL DEFAULT 0,
  `session_logged_in` tinyint(1) NOT NULL DEFAULT 0,
  `session_admin` tinyint(4) NOT NULL DEFAULT 0,
  PRIMARY KEY (`session_id`),
  KEY `session_user_id` (`session_user_id`),
  KEY `session_id_ip_user_id` (`session_id`,`session_ip`,`session_user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table nuke_bbsmilies
# ------------------------------------------------------------

CREATE TABLE `nuke_bbsmilies` (
  `smilies_id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(50) DEFAULT NULL,
  `smile_url` varchar(100) DEFAULT NULL,
  `emoticon` varchar(75) DEFAULT NULL,
  PRIMARY KEY (`smilies_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table nuke_bbthemes
# ------------------------------------------------------------

CREATE TABLE `nuke_bbthemes` (
  `themes_id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `template_name` varchar(30) NOT NULL DEFAULT '',
  `style_name` varchar(30) NOT NULL DEFAULT '',
  `head_stylesheet` varchar(100) DEFAULT NULL,
  `body_background` varchar(100) DEFAULT NULL,
  `body_bgcolor` varchar(6) DEFAULT NULL,
  `body_text` varchar(6) DEFAULT NULL,
  `body_link` varchar(6) DEFAULT NULL,
  `body_vlink` varchar(6) DEFAULT NULL,
  `body_alink` varchar(6) DEFAULT NULL,
  `body_hlink` varchar(6) DEFAULT NULL,
  `tr_color1` varchar(6) DEFAULT NULL,
  `tr_color2` varchar(6) DEFAULT NULL,
  `tr_color3` varchar(6) DEFAULT NULL,
  `tr_class1` varchar(25) DEFAULT NULL,
  `tr_class2` varchar(25) DEFAULT NULL,
  `tr_class3` varchar(25) DEFAULT NULL,
  `th_color1` varchar(6) DEFAULT NULL,
  `th_color2` varchar(6) DEFAULT NULL,
  `th_color3` varchar(6) DEFAULT NULL,
  `th_class1` varchar(25) DEFAULT NULL,
  `th_class2` varchar(25) DEFAULT NULL,
  `th_class3` varchar(25) DEFAULT NULL,
  `td_color1` varchar(6) DEFAULT NULL,
  `td_color2` varchar(6) DEFAULT NULL,
  `td_color3` varchar(6) DEFAULT NULL,
  `td_class1` varchar(25) DEFAULT NULL,
  `td_class2` varchar(25) DEFAULT NULL,
  `td_class3` varchar(25) DEFAULT NULL,
  `fontface1` varchar(50) DEFAULT NULL,
  `fontface2` varchar(50) DEFAULT NULL,
  `fontface3` varchar(50) DEFAULT NULL,
  `fontsize1` tinyint(4) DEFAULT NULL,
  `fontsize2` tinyint(4) DEFAULT NULL,
  `fontsize3` tinyint(4) DEFAULT NULL,
  `fontcolor1` varchar(6) DEFAULT NULL,
  `fontcolor2` varchar(6) DEFAULT NULL,
  `fontcolor3` varchar(6) DEFAULT NULL,
  `span_class1` varchar(25) DEFAULT NULL,
  `span_class2` varchar(25) DEFAULT NULL,
  `span_class3` varchar(25) DEFAULT NULL,
  `img_size_poll` smallint(5) unsigned DEFAULT NULL,
  `img_size_privmsg` smallint(5) unsigned DEFAULT NULL,
  PRIMARY KEY (`themes_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table nuke_bbthemes_name
# ------------------------------------------------------------

CREATE TABLE `nuke_bbthemes_name` (
  `themes_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `tr_color1_name` char(50) DEFAULT NULL,
  `tr_color2_name` char(50) DEFAULT NULL,
  `tr_color3_name` char(50) DEFAULT NULL,
  `tr_class1_name` char(50) DEFAULT NULL,
  `tr_class2_name` char(50) DEFAULT NULL,
  `tr_class3_name` char(50) DEFAULT NULL,
  `th_color1_name` char(50) DEFAULT NULL,
  `th_color2_name` char(50) DEFAULT NULL,
  `th_color3_name` char(50) DEFAULT NULL,
  `th_class1_name` char(50) DEFAULT NULL,
  `th_class2_name` char(50) DEFAULT NULL,
  `th_class3_name` char(50) DEFAULT NULL,
  `td_color1_name` char(50) DEFAULT NULL,
  `td_color2_name` char(50) DEFAULT NULL,
  `td_color3_name` char(50) DEFAULT NULL,
  `td_class1_name` char(50) DEFAULT NULL,
  `td_class2_name` char(50) DEFAULT NULL,
  `td_class3_name` char(50) DEFAULT NULL,
  `fontface1_name` char(50) DEFAULT NULL,
  `fontface2_name` char(50) DEFAULT NULL,
  `fontface3_name` char(50) DEFAULT NULL,
  `fontsize1_name` char(50) DEFAULT NULL,
  `fontsize2_name` char(50) DEFAULT NULL,
  `fontsize3_name` char(50) DEFAULT NULL,
  `fontcolor1_name` char(50) DEFAULT NULL,
  `fontcolor2_name` char(50) DEFAULT NULL,
  `fontcolor3_name` char(50) DEFAULT NULL,
  `span_class1_name` char(50) DEFAULT NULL,
  `span_class2_name` char(50) DEFAULT NULL,
  `span_class3_name` char(50) DEFAULT NULL,
  PRIMARY KEY (`themes_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table nuke_bbtopics
# ------------------------------------------------------------

CREATE TABLE `nuke_bbtopics` (
  `topic_id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `forum_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `topic_title` char(60) NOT NULL DEFAULT '',
  `topic_poster` mediumint(9) NOT NULL DEFAULT 0,
  `topic_time` int(11) NOT NULL DEFAULT 0,
  `topic_views` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `topic_replies` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `topic_status` tinyint(4) NOT NULL DEFAULT 0,
  `topic_vote` tinyint(1) NOT NULL DEFAULT 0,
  `topic_type` tinyint(4) NOT NULL DEFAULT 0,
  `topic_last_post_id` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `topic_first_post_id` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `topic_moved_id` mediumint(8) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`topic_id`),
  KEY `forum_id` (`forum_id`),
  KEY `topic_moved_id` (`topic_moved_id`),
  KEY `topic_status` (`topic_status`),
  KEY `topic_type` (`topic_type`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table nuke_bbtopics_watch
# ------------------------------------------------------------

CREATE TABLE `nuke_bbtopics_watch` (
  `topic_id` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `user_id` mediumint(9) NOT NULL DEFAULT 0,
  `notify_status` tinyint(1) NOT NULL DEFAULT 0,
  KEY `topic_id` (`topic_id`),
  KEY `user_id` (`user_id`),
  KEY `notify_status` (`notify_status`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table nuke_bbuser_group
# ------------------------------------------------------------

CREATE TABLE `nuke_bbuser_group` (
  `group_id` mediumint(9) NOT NULL DEFAULT 0,
  `user_id` mediumint(9) NOT NULL DEFAULT 0,
  `user_pending` tinyint(1) DEFAULT NULL,
  KEY `group_id` (`group_id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table nuke_bbvote_desc
# ------------------------------------------------------------

CREATE TABLE `nuke_bbvote_desc` (
  `vote_id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `topic_id` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `vote_text` text NOT NULL,
  `vote_start` int(11) NOT NULL DEFAULT 0,
  `vote_length` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`vote_id`),
  KEY `topic_id` (`topic_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table nuke_bbvote_results
# ------------------------------------------------------------

CREATE TABLE `nuke_bbvote_results` (
  `vote_id` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `vote_option_id` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `vote_option_text` varchar(255) NOT NULL DEFAULT '',
  `vote_result` int(11) NOT NULL DEFAULT 0,
  KEY `vote_option_id` (`vote_option_id`),
  KEY `vote_id` (`vote_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table nuke_bbvote_voters
# ------------------------------------------------------------

CREATE TABLE `nuke_bbvote_voters` (
  `vote_id` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `vote_user_id` mediumint(9) NOT NULL DEFAULT 0,
  `vote_user_ip` char(8) NOT NULL DEFAULT '',
  KEY `vote_id` (`vote_id`),
  KEY `vote_user_id` (`vote_user_id`),
  KEY `vote_user_ip` (`vote_user_ip`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table nuke_bbwords
# ------------------------------------------------------------

CREATE TABLE `nuke_bbwords` (
  `word_id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `word` char(100) NOT NULL DEFAULT '',
  `replacement` char(100) NOT NULL DEFAULT '',
  PRIMARY KEY (`word_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table nuke_blocks
# ------------------------------------------------------------

CREATE TABLE `nuke_blocks` (
  `bid` int(11) NOT NULL AUTO_INCREMENT,
  `bkey` varchar(15) NOT NULL DEFAULT '',
  `title` varchar(60) NOT NULL DEFAULT '',
  `content` text NOT NULL,
  `url` varchar(200) NOT NULL DEFAULT '',
  `bposition` char(1) NOT NULL DEFAULT '',
  `weight` int(11) NOT NULL DEFAULT 1,
  `active` int(11) NOT NULL DEFAULT 1,
  `refresh` int(11) NOT NULL DEFAULT 0,
  `time` varchar(14) NOT NULL DEFAULT '0',
  `blanguage` varchar(30) NOT NULL DEFAULT '',
  `blockfile` varchar(255) NOT NULL DEFAULT '',
  `view` int(11) NOT NULL DEFAULT 0,
  `expire` varchar(14) NOT NULL DEFAULT '0',
  `action` char(1) NOT NULL DEFAULT '',
  `subscription` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`bid`),
  KEY `title` (`title`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table nuke_cities
# ------------------------------------------------------------

CREATE TABLE `nuke_cities` (
  `id` mediumint(9) NOT NULL DEFAULT 0,
  `local_id` mediumint(9) NOT NULL DEFAULT 0,
  `city` varchar(65) NOT NULL DEFAULT '',
  `cc` char(2) NOT NULL DEFAULT '',
  `country` varchar(35) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table nuke_comments
# ------------------------------------------------------------

CREATE TABLE `nuke_comments` (
  `tid` int(11) NOT NULL AUTO_INCREMENT,
  `pid` int(11) NOT NULL DEFAULT 0,
  `sid` int(11) NOT NULL DEFAULT 0,
  `date` datetime DEFAULT NULL,
  `name` varchar(60) NOT NULL DEFAULT '',
  `email` varchar(60) DEFAULT NULL,
  `url` varchar(60) DEFAULT NULL,
  `host_name` varchar(60) DEFAULT NULL,
  `subject` varchar(85) NOT NULL DEFAULT '',
  `comment` text NOT NULL,
  `score` tinyint(4) NOT NULL DEFAULT 0,
  `reason` tinyint(4) NOT NULL DEFAULT 0,
  `last_moderation_ip` varchar(15) DEFAULT '0',
  PRIMARY KEY (`tid`),
  KEY `pid` (`pid`),
  KEY `sid` (`sid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table nuke_comments_moderated
# ------------------------------------------------------------

CREATE TABLE `nuke_comments_moderated` (
  `tid` int(11) NOT NULL AUTO_INCREMENT,
  `pid` int(11) NOT NULL DEFAULT 0,
  `sid` int(11) NOT NULL DEFAULT 0,
  `date` datetime DEFAULT NULL,
  `name` varchar(60) NOT NULL DEFAULT '',
  `email` varchar(60) DEFAULT NULL,
  `url` varchar(60) DEFAULT NULL,
  `host_name` varchar(60) DEFAULT NULL,
  `subject` varchar(85) NOT NULL DEFAULT '',
  `comment` text NOT NULL,
  `score` tinyint(4) NOT NULL DEFAULT 0,
  `reason` tinyint(4) NOT NULL DEFAULT 0,
  `last_moderation_ip` varchar(15) DEFAULT '0',
  PRIMARY KEY (`tid`),
  KEY `pid` (`pid`),
  KEY `sid` (`sid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table nuke_config
# ------------------------------------------------------------

CREATE TABLE `nuke_config` (
  `sitename` varchar(255) NOT NULL DEFAULT '',
  `nukeurl` varchar(255) NOT NULL DEFAULT '',
  `site_logo` varchar(255) NOT NULL DEFAULT '',
  `slogan` varchar(255) NOT NULL DEFAULT '',
  `startdate` varchar(50) NOT NULL DEFAULT '',
  `adminmail` varchar(255) NOT NULL DEFAULT '',
  `anonpost` tinyint(1) NOT NULL DEFAULT 0,
  `Default_Theme` varchar(255) NOT NULL DEFAULT '',
  `overwrite_theme` tinyint(1) NOT NULL DEFAULT 1,
  `foot1` text NOT NULL,
  `foot2` text NOT NULL,
  `foot3` text NOT NULL,
  `commentlimit` int(11) NOT NULL DEFAULT 4096,
  `anonymous` varchar(255) NOT NULL DEFAULT '',
  `minpass` tinyint(1) NOT NULL DEFAULT 5,
  `pollcomm` tinyint(1) NOT NULL DEFAULT 1,
  `articlecomm` tinyint(1) NOT NULL DEFAULT 1,
  `broadcast_msg` tinyint(1) NOT NULL DEFAULT 1,
  `my_headlines` tinyint(1) NOT NULL DEFAULT 1,
  `top` int(11) NOT NULL DEFAULT 10,
  `storyhome` int(11) NOT NULL DEFAULT 10,
  `user_news` tinyint(1) NOT NULL DEFAULT 1,
  `oldnum` int(11) NOT NULL DEFAULT 30,
  `ultramode` tinyint(1) NOT NULL DEFAULT 0,
  `banners` tinyint(1) NOT NULL DEFAULT 1,
  `backend_title` varchar(255) NOT NULL DEFAULT '',
  `backend_language` varchar(10) NOT NULL DEFAULT '',
  `language` varchar(100) NOT NULL DEFAULT '',
  `locale` varchar(10) NOT NULL DEFAULT '',
  `multilingual` tinyint(1) NOT NULL DEFAULT 0,
  `useflags` tinyint(1) NOT NULL DEFAULT 0,
  `notify` tinyint(1) NOT NULL DEFAULT 0,
  `notify_email` varchar(255) NOT NULL DEFAULT '',
  `notify_subject` varchar(255) NOT NULL DEFAULT '',
  `notify_message` varchar(255) NOT NULL DEFAULT '',
  `notify_from` varchar(255) NOT NULL DEFAULT '',
  `moderate` tinyint(1) NOT NULL DEFAULT 0,
  `admingraphic` tinyint(1) NOT NULL DEFAULT 1,
  `httpref` tinyint(1) NOT NULL DEFAULT 1,
  `httprefmax` int(11) NOT NULL DEFAULT 1000,
  `httprefmode` tinyint(1) NOT NULL DEFAULT 1,
  `CensorMode` tinyint(1) NOT NULL DEFAULT 3,
  `CensorReplace` varchar(10) NOT NULL DEFAULT '',
  `copyright` text NOT NULL,
  `Version_Num` varchar(10) NOT NULL DEFAULT '',
  `gfx_chk` tinyint(1) NOT NULL DEFAULT 0,
  `nuke_editor` tinyint(1) NOT NULL DEFAULT 1,
  `display_errors` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`sitename`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table nuke_confirm
# ------------------------------------------------------------

CREATE TABLE `nuke_confirm` (
  `confirm_id` char(32) NOT NULL DEFAULT '',
  `session_id` char(32) NOT NULL DEFAULT '',
  `code` char(6) NOT NULL DEFAULT '',
  PRIMARY KEY (`session_id`,`confirm_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table nuke_counter
# ------------------------------------------------------------

CREATE TABLE `nuke_counter` (
  `type` varchar(80) NOT NULL DEFAULT '',
  `var` varchar(80) NOT NULL DEFAULT '',
  `count` int(10) unsigned NOT NULL DEFAULT 0
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table nuke_faqanswer
# ------------------------------------------------------------

CREATE TABLE `nuke_faqanswer` (
  `id` tinyint(4) NOT NULL AUTO_INCREMENT,
  `id_cat` tinyint(4) NOT NULL DEFAULT 0,
  `question` varchar(255) DEFAULT '',
  `answer` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `id_cat` (`id_cat`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table nuke_faqcategories
# ------------------------------------------------------------

CREATE TABLE `nuke_faqcategories` (
  `id_cat` tinyint(4) NOT NULL AUTO_INCREMENT,
  `categories` varchar(255) DEFAULT NULL,
  `flanguage` varchar(30) NOT NULL DEFAULT '',
  PRIMARY KEY (`id_cat`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table nuke_groups
# ------------------------------------------------------------

CREATE TABLE `nuke_groups` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL DEFAULT '',
  `description` text NOT NULL,
  `points` int(11) NOT NULL DEFAULT 0,
  KEY `id` (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table nuke_groups_points
# ------------------------------------------------------------

CREATE TABLE `nuke_groups_points` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `points` int(11) NOT NULL DEFAULT 0,
  KEY `id` (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table nuke_headlines
# ------------------------------------------------------------

CREATE TABLE `nuke_headlines` (
  `hid` int(11) NOT NULL AUTO_INCREMENT,
  `sitename` varchar(30) NOT NULL DEFAULT '',
  `headlinesurl` varchar(200) NOT NULL DEFAULT '',
  PRIMARY KEY (`hid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table nuke_links_categories
# ------------------------------------------------------------

CREATE TABLE `nuke_links_categories` (
  `cid` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(50) NOT NULL DEFAULT '',
  `cdescription` text NOT NULL,
  `parentid` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`cid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table nuke_links_editorials
# ------------------------------------------------------------

CREATE TABLE `nuke_links_editorials` (
  `linkid` int(11) NOT NULL DEFAULT 0,
  `adminid` varchar(60) NOT NULL DEFAULT '',
  `editorialtimestamp` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `editorialtext` text NOT NULL,
  `editorialtitle` varchar(100) NOT NULL DEFAULT '',
  PRIMARY KEY (`linkid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table nuke_links_links
# ------------------------------------------------------------

CREATE TABLE `nuke_links_links` (
  `lid` int(11) NOT NULL AUTO_INCREMENT,
  `cid` int(11) NOT NULL DEFAULT 0,
  `sid` int(11) NOT NULL DEFAULT 0,
  `title` varchar(100) NOT NULL DEFAULT '',
  `url` varchar(100) NOT NULL DEFAULT '',
  `description` text NOT NULL,
  `date` datetime DEFAULT NULL,
  `name` varchar(100) NOT NULL DEFAULT '',
  `email` varchar(100) NOT NULL DEFAULT '',
  `hits` int(11) NOT NULL DEFAULT 0,
  `submitter` varchar(60) NOT NULL DEFAULT '',
  `linkratingsummary` double(6,4) NOT NULL DEFAULT 0.0000,
  `totalvotes` int(11) NOT NULL DEFAULT 0,
  `totalcomments` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`lid`),
  KEY `cid` (`cid`),
  KEY `sid` (`sid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table nuke_links_modrequest
# ------------------------------------------------------------

CREATE TABLE `nuke_links_modrequest` (
  `requestid` int(11) NOT NULL AUTO_INCREMENT,
  `lid` int(11) NOT NULL DEFAULT 0,
  `cid` int(11) NOT NULL DEFAULT 0,
  `sid` int(11) NOT NULL DEFAULT 0,
  `title` varchar(100) NOT NULL DEFAULT '',
  `url` varchar(100) NOT NULL DEFAULT '',
  `description` text NOT NULL,
  `modifysubmitter` varchar(60) NOT NULL DEFAULT '',
  `brokenlink` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`requestid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table nuke_links_newlink
# ------------------------------------------------------------

CREATE TABLE `nuke_links_newlink` (
  `lid` int(11) NOT NULL AUTO_INCREMENT,
  `cid` int(11) NOT NULL DEFAULT 0,
  `sid` int(11) NOT NULL DEFAULT 0,
  `title` varchar(100) NOT NULL DEFAULT '',
  `url` varchar(100) NOT NULL DEFAULT '',
  `description` text NOT NULL,
  `name` varchar(100) NOT NULL DEFAULT '',
  `email` varchar(100) NOT NULL DEFAULT '',
  `submitter` varchar(60) NOT NULL DEFAULT '',
  PRIMARY KEY (`lid`),
  KEY `cid` (`cid`),
  KEY `sid` (`sid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table nuke_links_votedata
# ------------------------------------------------------------

CREATE TABLE `nuke_links_votedata` (
  `ratingdbid` int(11) NOT NULL AUTO_INCREMENT,
  `ratinglid` int(11) NOT NULL DEFAULT 0,
  `ratinguser` varchar(60) NOT NULL DEFAULT '',
  `rating` int(11) NOT NULL DEFAULT 0,
  `ratinghostname` varchar(60) NOT NULL DEFAULT '',
  `ratingcomments` text NOT NULL,
  `ratingtimestamp` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`ratingdbid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table nuke_main
# ------------------------------------------------------------

CREATE TABLE `nuke_main` (
  `main_module` varchar(255) NOT NULL DEFAULT ''
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table nuke_message
# ------------------------------------------------------------

CREATE TABLE `nuke_message` (
  `mid` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(100) NOT NULL DEFAULT '',
  `content` text NOT NULL,
  `date` varchar(14) NOT NULL DEFAULT '',
  `expire` int(11) NOT NULL DEFAULT 0,
  `active` int(11) NOT NULL DEFAULT 1,
  `view` int(11) NOT NULL DEFAULT 1,
  `mlanguage` varchar(30) NOT NULL DEFAULT '',
  PRIMARY KEY (`mid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table nuke_modules
# ------------------------------------------------------------

CREATE TABLE `nuke_modules` (
  `mid` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL DEFAULT '',
  `custom_title` varchar(255) NOT NULL DEFAULT '',
  `active` int(11) NOT NULL DEFAULT 0,
  `view` int(11) NOT NULL DEFAULT 0,
  `inmenu` tinyint(1) NOT NULL DEFAULT 1,
  `mod_group` int(11) DEFAULT 0,
  `admins` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`mid`),
  KEY `title` (`title`),
  KEY `custom_title` (`custom_title`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table nuke_optimize_gain
# ------------------------------------------------------------

CREATE TABLE `nuke_optimize_gain` (
  `gain` decimal(10,3) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table nuke_pages
# ------------------------------------------------------------

CREATE TABLE `nuke_pages` (
  `pid` int(11) NOT NULL AUTO_INCREMENT,
  `cid` int(11) NOT NULL DEFAULT 0,
  `title` varchar(255) NOT NULL DEFAULT '',
  `subtitle` varchar(255) NOT NULL DEFAULT '',
  `active` int(11) NOT NULL DEFAULT 0,
  `page_header` text NOT NULL,
  `text` text NOT NULL,
  `page_footer` text NOT NULL,
  `signature` text NOT NULL,
  `date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `counter` int(11) NOT NULL DEFAULT 0,
  `clanguage` varchar(30) NOT NULL DEFAULT '',
  PRIMARY KEY (`pid`),
  KEY `cid` (`cid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table nuke_pages_categories
# ------------------------------------------------------------

CREATE TABLE `nuke_pages_categories` (
  `cid` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL DEFAULT '',
  `description` text NOT NULL,
  PRIMARY KEY (`cid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table nuke_poll_desc
# ------------------------------------------------------------

CREATE TABLE `nuke_poll_desc` (
  `pollID` int(11) NOT NULL AUTO_INCREMENT,
  `pollTitle` varchar(100) NOT NULL DEFAULT '',
  `timeStamp` int(11) NOT NULL DEFAULT 0,
  `voters` mediumint(9) NOT NULL DEFAULT 0,
  `planguage` varchar(30) NOT NULL DEFAULT '',
  `artid` int(11) NOT NULL DEFAULT 0,
  `comments` int(11) DEFAULT 0,
  PRIMARY KEY (`pollID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table nuke_pollcomments
# ------------------------------------------------------------

CREATE TABLE `nuke_pollcomments` (
  `tid` int(11) NOT NULL AUTO_INCREMENT,
  `pid` int(11) NOT NULL DEFAULT 0,
  `pollID` int(11) NOT NULL DEFAULT 0,
  `date` datetime DEFAULT NULL,
  `name` varchar(60) NOT NULL DEFAULT '',
  `email` varchar(60) DEFAULT NULL,
  `url` varchar(60) DEFAULT NULL,
  `host_name` varchar(60) DEFAULT NULL,
  `subject` varchar(60) NOT NULL DEFAULT '',
  `comment` text NOT NULL,
  `score` tinyint(4) NOT NULL DEFAULT 0,
  `reason` tinyint(4) NOT NULL DEFAULT 0,
  `last_moderation_ip` varchar(15) DEFAULT '0',
  PRIMARY KEY (`tid`),
  KEY `pid` (`pid`),
  KEY `pollID` (`pollID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table nuke_pollcomments_moderated
# ------------------------------------------------------------

CREATE TABLE `nuke_pollcomments_moderated` (
  `tid` int(11) NOT NULL AUTO_INCREMENT,
  `pid` int(11) NOT NULL DEFAULT 0,
  `pollID` int(11) NOT NULL DEFAULT 0,
  `date` datetime DEFAULT NULL,
  `name` varchar(60) NOT NULL DEFAULT '',
  `email` varchar(60) DEFAULT NULL,
  `url` varchar(60) DEFAULT NULL,
  `host_name` varchar(60) DEFAULT NULL,
  `subject` varchar(60) NOT NULL DEFAULT '',
  `comment` text NOT NULL,
  `score` tinyint(4) NOT NULL DEFAULT 0,
  `reason` tinyint(4) NOT NULL DEFAULT 0,
  `last_moderation_ip` varchar(15) DEFAULT '0',
  PRIMARY KEY (`tid`),
  KEY `pid` (`pid`),
  KEY `pollID` (`pollID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table nuke_public_messages
# ------------------------------------------------------------

CREATE TABLE `nuke_public_messages` (
  `mid` int(11) NOT NULL AUTO_INCREMENT,
  `content` varchar(255) NOT NULL DEFAULT '',
  `date` varchar(14) DEFAULT NULL,
  `who` varchar(25) NOT NULL DEFAULT '',
  PRIMARY KEY (`mid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table nuke_queue
# ------------------------------------------------------------

CREATE TABLE `nuke_queue` (
  `qid` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `uid` mediumint(9) NOT NULL DEFAULT 0,
  `uname` varchar(40) NOT NULL DEFAULT '',
  `subject` varchar(100) NOT NULL DEFAULT '',
  `story` text DEFAULT NULL,
  `storyext` text NOT NULL,
  `timestamp` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `topic` varchar(20) NOT NULL DEFAULT '',
  `alanguage` varchar(30) NOT NULL DEFAULT '',
  PRIMARY KEY (`qid`),
  KEY `uid` (`uid`),
  KEY `uname` (`uname`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table nuke_referer
# ------------------------------------------------------------

CREATE TABLE `nuke_referer` (
  `rid` int(11) NOT NULL AUTO_INCREMENT,
  `url` varchar(100) NOT NULL DEFAULT '',
  PRIMARY KEY (`rid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table nuke_related
# ------------------------------------------------------------

CREATE TABLE `nuke_related` (
  `rid` int(11) NOT NULL AUTO_INCREMENT,
  `tid` int(11) NOT NULL DEFAULT 0,
  `name` varchar(30) NOT NULL DEFAULT '',
  `url` varchar(200) NOT NULL DEFAULT '',
  PRIMARY KEY (`rid`),
  KEY `tid` (`tid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table nuke_session
# ------------------------------------------------------------

CREATE TABLE `nuke_session` (
  `uname` varchar(25) NOT NULL DEFAULT '',
  `time` varchar(14) NOT NULL DEFAULT '',
  `host_addr` varchar(48) NOT NULL DEFAULT '',
  `guest` int(11) NOT NULL DEFAULT 0,
  KEY `time` (`time`),
  KEY `guest` (`guest`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table nuke_stats_date
# ------------------------------------------------------------

CREATE TABLE `nuke_stats_date` (
  `year` smallint(6) NOT NULL DEFAULT 0,
  `month` tinyint(4) NOT NULL DEFAULT 0,
  `date` tinyint(4) NOT NULL DEFAULT 0,
  `hits` bigint(20) NOT NULL DEFAULT 0
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table nuke_stats_hour
# ------------------------------------------------------------

CREATE TABLE `nuke_stats_hour` (
  `year` smallint(6) NOT NULL DEFAULT 0,
  `month` tinyint(4) NOT NULL DEFAULT 0,
  `date` tinyint(4) NOT NULL DEFAULT 0,
  `hour` tinyint(4) NOT NULL DEFAULT 0,
  `hits` int(11) NOT NULL DEFAULT 0
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table nuke_stats_month
# ------------------------------------------------------------

CREATE TABLE `nuke_stats_month` (
  `year` smallint(6) NOT NULL DEFAULT 0,
  `month` tinyint(4) NOT NULL DEFAULT 0,
  `hits` bigint(20) NOT NULL DEFAULT 0
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table nuke_stats_year
# ------------------------------------------------------------

CREATE TABLE `nuke_stats_year` (
  `year` smallint(6) NOT NULL DEFAULT 0,
  `hits` bigint(20) NOT NULL DEFAULT 0
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table nuke_stories
# ------------------------------------------------------------

CREATE TABLE `nuke_stories` (
  `sid` int(11) NOT NULL AUTO_INCREMENT,
  `catid` int(11) NOT NULL DEFAULT 0,
  `aid` varchar(30) NOT NULL DEFAULT '',
  `title` varchar(80) DEFAULT NULL,
  `time` datetime DEFAULT NULL,
  `hometext` text DEFAULT NULL,
  `bodytext` text DEFAULT NULL,
  `comments` int(11) DEFAULT 0,
  `counter` mediumint(8) unsigned DEFAULT NULL,
  `topic` int(11) NOT NULL DEFAULT 1,
  `informant` varchar(20) NOT NULL DEFAULT '',
  `notes` text DEFAULT NULL,
  `ihome` int(11) NOT NULL DEFAULT 0,
  `alanguage` varchar(30) NOT NULL DEFAULT '',
  `acomm` int(11) NOT NULL DEFAULT 0,
  `haspoll` int(11) NOT NULL DEFAULT 0,
  `pollID` int(11) NOT NULL DEFAULT 0,
  `score` int(11) NOT NULL DEFAULT 0,
  `ratings` int(11) NOT NULL DEFAULT 0,
  `rating_ip` varchar(15) DEFAULT '0',
  `associated` text DEFAULT NULL,
  PRIMARY KEY (`sid`),
  KEY `catid` (`catid`),
  KEY `counter` (`counter`),
  KEY `topic` (`topic`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table nuke_stories_cat
# ------------------------------------------------------------

CREATE TABLE `nuke_stories_cat` (
  `catid` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(20) NOT NULL DEFAULT '',
  `counter` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`catid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table nuke_subscriptions
# ------------------------------------------------------------

CREATE TABLE `nuke_subscriptions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userid` int(11) DEFAULT 0,
  `subscription_expire` varchar(50) NOT NULL DEFAULT '',
  KEY `id` (`id`,`userid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table nuke_topics
# ------------------------------------------------------------

CREATE TABLE `nuke_topics` (
  `topicid` int(11) NOT NULL AUTO_INCREMENT,
  `topicname` varchar(20) DEFAULT NULL,
  `topicimage` varchar(100) NOT NULL DEFAULT '',
  `topictext` varchar(40) DEFAULT NULL,
  `counter` int(11) NOT NULL DEFAULT 0,
  `id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `topicid` (`topicid`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table nuke_users
# ------------------------------------------------------------

CREATE TABLE `nuke_users` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `date_started` varchar(4) CHARACTER SET utf8mb3 COLLATE utf8mb3_swedish_ci NOT NULL DEFAULT '',
  `name` varchar(60) NOT NULL DEFAULT '',
  `username` varchar(25) NOT NULL DEFAULT '',
  `user_email` varchar(255) NOT NULL DEFAULT '',
  `user_ibl_team` varchar(32) NOT NULL DEFAULT '',
  `discordID` bigint(20) unsigned DEFAULT NULL,
  `femail` varchar(255) NOT NULL DEFAULT '',
  `user_website` varchar(255) NOT NULL DEFAULT '',
  `user_avatar` varchar(255) NOT NULL DEFAULT '',
  `user_regdate` varchar(20) NOT NULL DEFAULT '',
  `user_icq` varchar(15) DEFAULT NULL,
  `user_occ` varchar(100) DEFAULT NULL,
  `user_from` varchar(100) DEFAULT NULL,
  `user_interests` varchar(150) NOT NULL DEFAULT '',
  `user_sig` varchar(255) DEFAULT NULL,
  `user_viewemail` tinyint(4) DEFAULT NULL,
  `user_theme` int(11) DEFAULT NULL,
  `user_aim` varchar(18) DEFAULT NULL,
  `user_yim` varchar(25) DEFAULT NULL,
  `user_msnm` varchar(25) DEFAULT NULL,
  `user_password` varchar(40) NOT NULL DEFAULT '',
  `storynum` tinyint(4) NOT NULL DEFAULT 10,
  `umode` varchar(10) NOT NULL DEFAULT '',
  `uorder` tinyint(1) NOT NULL DEFAULT 0,
  `thold` tinyint(1) NOT NULL DEFAULT 0,
  `noscore` tinyint(1) NOT NULL DEFAULT 0,
  `bio` tinytext NOT NULL,
  `ublockon` tinyint(1) NOT NULL DEFAULT 0,
  `ublock` tinytext NOT NULL,
  `theme` varchar(255) NOT NULL DEFAULT '',
  `commentmax` int(11) NOT NULL DEFAULT 4096,
  `counter` int(11) NOT NULL DEFAULT 0,
  `newsletter` int(11) NOT NULL DEFAULT 0,
  `user_posts` int(11) NOT NULL DEFAULT 0,
  `user_attachsig` int(11) NOT NULL DEFAULT 0,
  `user_rank` int(11) NOT NULL DEFAULT 0,
  `user_level` int(11) NOT NULL DEFAULT 1,
  `broadcast` tinyint(1) NOT NULL DEFAULT 1,
  `popmeson` tinyint(1) NOT NULL DEFAULT 0,
  `user_active` tinyint(1) DEFAULT 1,
  `user_session_time` int(11) NOT NULL DEFAULT 0,
  `user_session_page` smallint(6) NOT NULL DEFAULT 0,
  `user_lastvisit` int(11) NOT NULL DEFAULT 0,
  `last_ip` varchar(25) DEFAULT NULL,
  `user_timezone` tinyint(4) NOT NULL DEFAULT 10,
  `user_style` tinyint(4) DEFAULT NULL,
  `user_lang` varchar(255) NOT NULL DEFAULT 'english',
  `user_dateformat` varchar(14) NOT NULL DEFAULT 'D M d, Y g:i a',
  `user_new_privmsg` smallint(5) unsigned NOT NULL DEFAULT 0,
  `user_unread_privmsg` smallint(5) unsigned NOT NULL DEFAULT 0,
  `user_last_privmsg` int(11) NOT NULL DEFAULT 0,
  `user_emailtime` int(11) DEFAULT NULL,
  `user_allowhtml` tinyint(1) DEFAULT 1,
  `user_allowbbcode` tinyint(1) DEFAULT 1,
  `user_allowsmile` tinyint(1) DEFAULT 1,
  `user_allowavatar` tinyint(1) NOT NULL DEFAULT 1,
  `user_allow_pm` tinyint(1) NOT NULL DEFAULT 1,
  `user_allow_viewonline` tinyint(1) NOT NULL DEFAULT 1,
  `user_notify` tinyint(1) NOT NULL DEFAULT 0,
  `user_notify_pm` tinyint(1) NOT NULL DEFAULT 0,
  `user_popup_pm` tinyint(1) NOT NULL DEFAULT 0,
  `user_avatar_type` tinyint(4) NOT NULL DEFAULT 3,
  `user_sig_bbcode_uid` varchar(10) DEFAULT NULL,
  `user_actkey` varchar(32) DEFAULT NULL,
  `user_newpasswd` varchar(32) DEFAULT NULL,
  `points` int(11) DEFAULT 0,
  `karma` tinyint(1) DEFAULT 0,
  PRIMARY KEY (`user_id`),
  KEY `uname` (`username`),
  KEY `user_session_time` (`user_session_time`),
  KEY `karma` (`karma`),
  KEY `user_email` (`user_email`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table nuke_users_temp
# ------------------------------------------------------------

CREATE TABLE `nuke_users_temp` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(25) NOT NULL DEFAULT '',
  `user_email` varchar(255) NOT NULL DEFAULT '',
  `user_password` varchar(40) NOT NULL DEFAULT '',
  `user_regdate` varchar(20) NOT NULL DEFAULT '',
  `check_num` varchar(50) NOT NULL DEFAULT '',
  `time` varchar(14) NOT NULL DEFAULT '',
  PRIMARY KEY (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table olympic_stats
# ------------------------------------------------------------

CREATE TABLE `olympic_stats` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `year` int(11) NOT NULL DEFAULT 0,
  `pos` char(2) NOT NULL DEFAULT '',
  `name` varchar(32) NOT NULL DEFAULT '',
  `team` varchar(32) NOT NULL DEFAULT '',
  `games` int(11) NOT NULL DEFAULT 0,
  `minutes` int(11) NOT NULL DEFAULT 0,
  `fgm` int(11) NOT NULL DEFAULT 0,
  `fga` int(11) NOT NULL DEFAULT 0,
  `ftm` int(11) NOT NULL DEFAULT 0,
  `fta` int(11) NOT NULL DEFAULT 0,
  `tgm` int(11) NOT NULL DEFAULT 0,
  `tga` int(11) NOT NULL DEFAULT 0,
  `orb` int(11) NOT NULL DEFAULT 0,
  `reb` int(11) NOT NULL DEFAULT 0,
  `ast` int(11) NOT NULL DEFAULT 0,
  `stl` int(11) NOT NULL DEFAULT 0,
  `tvr` int(11) NOT NULL DEFAULT 0,
  `blk` int(11) NOT NULL DEFAULT 0,
  `pf` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table online
# ------------------------------------------------------------

CREATE TABLE `online` (
  `username` text NOT NULL,
  `timeout` text NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table password_reset_tokens
# ------------------------------------------------------------

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table poll
# ------------------------------------------------------------

CREATE TABLE `poll` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table questions
# ------------------------------------------------------------

CREATE TABLE `questions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pid` int(11) NOT NULL,
  `question` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table responses
# ------------------------------------------------------------

CREATE TABLE `responses` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `qid` int(11) NOT NULL,
  `ip` varchar(16) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table sessions
# ------------------------------------------------------------

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table user_online
# ------------------------------------------------------------

CREATE TABLE `user_online` (
  `session` char(100) NOT NULL DEFAULT '',
  `time` int(11) NOT NULL DEFAULT 0
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table users
# ------------------------------------------------------------

CREATE TABLE `users` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;




/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
