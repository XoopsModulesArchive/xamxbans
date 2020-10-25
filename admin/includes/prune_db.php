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

//include "../inc/config.inc.php";
//include "../inc/accesscontrol.inc.php";
//include "$CFG->header";

//if($_SESSION['prune_db'] != "yes") {
//	MsgUser("Permission denied","You do not have the required credentials to view this page.","back","javascript:window.history.back()");
//	include "$CFG->footer";
//	exit();
//}

if (isset($_REQUEST['prune_db'])) {
    $count = PruneBans();

    echo '<center><b>Pruning Database...</b><br><br>' . $count . ' expired bans have successfully been moved to ban history table.<br><br>';
    echo '<a href="index.php">Continue</a></center>';
    /*
        if ($CFG->use_logfile == "yes") {
            $remote_address = $_SERVER['REMOTE_ADDR'];
            $user						= $_SESSION['uid'];

            AddLogEntry("$remote_address | $user | Pruned the database");
        }
    */
} else {
    ?>

    <table border="1" cellpadding="4" cellspacing="0" width="100%">
        <tr>
            <td><b>Moving expired bans to ban history...</b></b></td>
        </tr>

        <form name="prune" method="post" action="<?= $PHP_SELF ?>">
            <tr>
                <td bgcolor="#f0ffff" align="center"><br><br>

                    Usually Database pruning is handled by the included perl-script.<br>If you are for some reason unable to use the perl-script, you can manually prune the database by clicking the button below:<br><br><br>

                </td>
            </tr>

            <tr>
                <td colspan="2" align="right"><input type="submit" name="prune_db" value=" Prune Database " style="font: 8pt bold"></td>
            </tr>
        </form>
    </table>

<?php } ?>
