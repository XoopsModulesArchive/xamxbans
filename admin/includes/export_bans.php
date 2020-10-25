<?php

/* 

    AMXBans, managing bans for Half-Life modifications
    Copyright (C) 2003, 2004  Ronald Renes / Jeroen de Rover

		web		: http://www.xs4all.nl/~yomama/amxbans/
		mail	: yomama@xs4all.nl
		ICQ		: 104115504
    
		This file is part of AMXBans.

    AMXBans is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    AMXBans is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with AMXBans; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

*/
require '../../../mainfile.php';
//include "inc/config.inc.php";
//include "inc/accesscontrol.inc.php";
//include "$CFG->header";
global $xoopsConfig;

if (!isset($_REQUEST['gametype'])) {
    $gametype = 'all';
}

if (!isset($_REQUEST['bantype'])) {
    $bantype = 'perm';
}

?>
    <center><b>Select Export Options...</b></center><br>
    <table border="1" cellpadding="4" cellspacing="0" width="100%">

    <form name="exportbans" method="post" action="index.php?task=export_bans">
    <tr>
    <td width="30%" bgcolor="#f0ffff" valign="top">Select bans from gametype(s):</td>
    <td bgcolor="#f0ffff"><input type="radio" name="gametype" value="all" <?php if ($gametype == 'all') {
    echo 'checked';
} ?>>all gametypes<br>

<?php

$gametype = GetGameTypes();

if ($gametype == 0) {
    echo "No gametypes found!\n";
} else {
    foreach ($gametype as $row) {
        ?>

        <input type="radio" name="gametype" value="<?= $row ?>" <?php if ($_REQUEST['gametype'] == $row) {
            echo 'checked';
        } ?>><?= $row ?><br>

        <?php
    }

    ?>

    </td>
    </tr>

    <tr>
        <td width="30%" bgcolor="#f0ffff" valign="top">Select bans from type(s):</td>
        <td bgcolor="#f0ffff">

            <input type="radio" name="bantype" value="perm" <?php if ($bantype == 'perm') {
                echo 'checked';
            } ?>>Permanent bans<br>
            <input type="radio" name="bantype" value="temp" <?php if ($bantype == 'temp') {
                echo 'checked';
            } ?>>Temp bans<br>
            <input type="radio" name="bantype" value="both" <?php if ($bantype == 'both') {
                echo 'checked';
            } ?>>Both Permanent and temp bans

        </td>
    </tr>

    <tr>
        <td colspan="2" align="right"><input type="submit" name="export" value=" export " style="font: 8pt bold"></td>
    </tr>
    </form>
    </table>

<?php
}

if (isset($_REQUEST['export'])) {
    //print_r($_POST );

    $ebans = ExportBans($_REQUEST['bantype'], $_REQUEST['gametype']);
    $now   = date('F j, Y, \a\t g:i A');

    if ($ebans == 0) {
        echo 'No bans found matching your criteria...</td></tr>';
        echo '<br>';
        exit();
    } else {
        echo "<br><br>\n";

        echo "Cut and past the lines below into your banned.cfg. Make sure you exec banned.cfg from your server.cfg<br><br>\n";
        echo '<hr>';

        echo "#<br>\n";
        echo "#<br>\n";
        echo "# $CFG->site_name Banlist. For info please contact " . $xoopsConfig['adminmail'] . "<br>\n";
        echo "# This banlist was generated on $now from " . $xoopsConfig['sitename'] . "<br>\n";

        echo "#<br>\n";
        echo "#<br>\n";

        $count = 0;

        foreach ($ebans as $row) {
            echo "banid 0.0 $row<br>\n";
            $count++;
        }

        echo "#<br>\n";
        echo "#<br>\n";
        echo "# Total nr of bans: $count<br>\n";
        echo "#<br>\n";
        echo "#<br>\n";

        //if ($CFG->use_logfile == "yes") {
        //	$remote_address = $_SERVER['REMOTE_ADDR'];
        //	$user						= $_SESSION['uid'];
        //	AddLogEntry("$remote_address | $user | An export was requested");
        //}
    }
}
?>
