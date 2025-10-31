<?php

use Services\DatabaseService;

require_once __DIR__ . '/BaseView.php';

class HeatAveragesView extends BaseView {
    public function render() {
        echo "<table border=1 cellspacing=0 class=\"sortable\" style='margin: 0 auto;'>
            <tr>
                <td colspan=15 style='font-weight:bold;text-align:center;background-color:#00c;color:#fff;'>H.E.A.T. Averages</td>
            </tr>
            <tr>
                <th>year</th>
                <th>team</th>
                <th>g</th>
                <th>min</th>
                <th>FGP</th>
                <th>FTP</th>
                <th>3GP</th>
                <th>orb</th>
                <th>reb</th>
                <th>ast</th>
                <th>stl</th>
                <th>to</th>
                <th>blk</th>
                <th>pf</th>
                <th>pts</th>
            </tr>";

        $car_gm = $car_min = $car_fgm = $car_fga = $car_ftm = $car_fta = $car_3gm = $car_3ga = 0;
        $car_orb = $car_reb = $car_ast = $car_stl = $car_blk = $car_tvr = $car_pf = $car_pts = 0;

        $escapedName = DatabaseService::escapeString($this->db, $this->player->name);
        $resultplayoff4 = $this->db->sql_query("SELECT * FROM ibl_heat_stats WHERE name='" . $escapedName . "' ORDER BY year ASC");
        while ($rowplayoff4 = $this->db->sql_fetchrow($resultplayoff4)) {
            $hist_year = stripslashes(check_html($rowplayoff4['year'], "nohtml"));
            $hist_team = stripslashes(check_html($rowplayoff4['team'], "nohtml"));
            $hist_gm = stripslashes(check_html($rowplayoff4['games'], "nohtml"));
            $hist_min = stripslashes(check_html($rowplayoff4['minutes'], "nohtml"));
            $hist_fgm = stripslashes(check_html($rowplayoff4['fgm'], "nohtml"));
            $hist_fga = stripslashes(check_html($rowplayoff4['fga'], "nohtml"));
            $hist_fgp = ($hist_fga) ? ($hist_fgm / $hist_fga) : "0.000";
            $hist_ftm = stripslashes(check_html($rowplayoff4['ftm'], "nohtml"));
            $hist_fta = stripslashes(check_html($rowplayoff4['fta'], "nohtml"));
            $hist_ftp = ($hist_fta) ? ($hist_ftm / $hist_fta) : "0.000";
            $hist_tgm = stripslashes(check_html($rowplayoff4['tgm'], "nohtml"));
            $hist_tga = stripslashes(check_html($rowplayoff4['tga'], "nohtml"));
            $hist_tgp = ($hist_tga) ? ($hist_tgm / $hist_tga) : "0.000";
            $hist_orb = stripslashes(check_html($rowplayoff4['orb'], "nohtml"));
            $hist_reb = stripslashes(check_html($rowplayoff4['reb'], "nohtml"));
            $hist_ast = stripslashes(check_html($rowplayoff4['ast'], "nohtml"));
            $hist_stl = stripslashes(check_html($rowplayoff4['stl'], "nohtml"));
            $hist_tvr = stripslashes(check_html($rowplayoff4['tvr'], "nohtml"));
            $hist_blk = stripslashes(check_html($rowplayoff4['blk'], "nohtml"));
            $hist_pf = stripslashes(check_html($rowplayoff4['pf'], "nohtml"));
            $hist_pts = $hist_fgm + $hist_fgm + $hist_ftm + $hist_tgm;

            $hist_mpg = ($hist_gm) ? ($hist_min / $hist_gm) : "0.0";
            $hist_opg = ($hist_gm) ? ($hist_orb / $hist_gm) : "0.0";
            $hist_rpg = ($hist_gm) ? ($hist_reb / $hist_gm) : "0.0";
            $hist_apg = ($hist_gm) ? ($hist_ast / $hist_gm) : "0.0";
            $hist_spg = ($hist_gm) ? ($hist_stl / $hist_gm) : "0.0";
            $hist_tpg = ($hist_gm) ? ($hist_tvr / $hist_gm) : "0.0";
            $hist_bpg = ($hist_gm) ? ($hist_blk / $hist_gm) : "0.0";
            $hist_fpg = ($hist_gm) ? ($hist_pf / $hist_gm) : "0.0";
            $hist_ppg = ($hist_gm) ? ($hist_pts / $hist_gm) : "0.0";

            echo "<td><center>$hist_year</center></td>
                <td><center>$hist_team</center></td>
                <td><center>$hist_gm</center></td>
                <td><center>";
            printf('%01.1f', $hist_mpg);
            echo "</center></td><td><center>";
            printf('%01.3f', $hist_fgp);
            echo "</center></td><td><center>";
            printf('%01.3f', $hist_ftp);
            echo "</center></td><td><center>";
            printf('%01.3f', $hist_tgp);
            echo "</center></td><td><center>";
            printf('%01.1f', $hist_opg);
            echo "</center></td><td><center>";
            printf('%01.1f', $hist_rpg);
            echo "</center></td><td><center>";
            printf('%01.1f', $hist_apg);
            echo "</center></td><td><center>";
            printf('%01.1f', $hist_spg);
            echo "</center></td><td><center>";
            printf('%01.1f', $hist_tpg);
            echo "</center></td><td><center>";
            printf('%01.1f', $hist_bpg);
            echo "</center></td><td><center>";
            printf('%01.1f', $hist_fpg);
            echo "</center></td><td><center>";
            printf('%01.1f', $hist_ppg);
            echo "</center></td></tr>";

            $car_gm = $car_gm + $hist_gm;
            $car_min = $car_min + $hist_min;
            $car_fgm = $car_fgm + $hist_fgm;
            $car_fga = $car_fga + $hist_fga;
            $car_ftm = $car_ftm + $hist_ftm;
            $car_fta = $car_fta + $hist_fta;
            $car_3gm = $car_3gm + $hist_tgm;
            $car_3ga = $car_3ga + $hist_tga;
            $car_orb = $car_orb + $hist_orb;
            $car_reb = $car_reb + $hist_reb;
            $car_ast = $car_ast + $hist_ast;
            $car_stl = $car_stl + $hist_stl;
            $car_blk = $car_blk + $hist_blk;
            $car_tvr = $car_tvr + $hist_tvr;
            $car_pf = $car_pf + $hist_pf;
            $car_pts = $car_pts + $hist_pts;
        }

        $car_fgp = ($car_fga) ? $car_fgm / $car_fga : "0.000";
        $car_ftp = ($car_fta) ? $car_ftm / $car_fta : "0.000";
        $car_tgp = ($car_3ga) ? $car_3gm / $car_3ga : "0.000";
        $car_avgm = ($car_gm) ? $car_min / $car_gm : "0.0";
        $car_avgo = ($car_gm) ? $car_orb / $car_gm : "0.0";
        $car_avgr = ($car_gm) ? $car_reb / $car_gm : "0.0";
        $car_avga = ($car_gm) ? $car_ast / $car_gm : "0.0";
        $car_avgs = ($car_gm) ? $car_stl / $car_gm : "0.0";
        $car_avgb = ($car_gm) ? $car_blk / $car_gm : "0.0";
        $car_avgt = ($car_gm) ? $car_tvr / $car_gm : "0.0";
        $car_avgf = ($car_gm) ? $car_pf / $car_gm : "0.0";
        $car_avgp = ($car_gm) ? $car_pts / $car_gm : "0.0";

        echo "<tr><td colspan=2 style='font-weight:bold;'>H.E.A.T. Averages</td>
            <td><center>$car_gm</center></td>
            <td><center>";
        printf('%01.1f', $car_avgm);
        echo "</center></td><td><center>";
        printf('%01.3f', $car_fgp);
        echo "</center></td><td><center>";
        printf('%01.3f', $car_ftp);
        echo "</center></td><td><center>";
        printf('%01.3f', $car_tgp);
        echo "</center></td><td><center>";
        printf('%01.1f', $car_avgo);
        echo "</center></td><td><center>";
        printf('%01.1f', $car_avgr);
        echo "</center></td><td><center>";
        printf('%01.1f', $car_avga);
        echo "</center></td><td><center>";
        printf('%01.1f', $car_avgs);
        echo "</center></td><td><center>";
        printf('%01.1f', $car_avgt);
        echo "</center></td><td><center>";
        printf('%01.1f', $car_avgb);
        echo "</center></td><td><center>";
        printf('%01.1f', $car_avgf);
        echo "</center></td><td><center>";
        printf('%01.1f', $car_avgp);
        echo "</center></td></tr></table>";
    }
}