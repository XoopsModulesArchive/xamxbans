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

//print_r($_REQUEST);
if (isset($_REQUEST['remove'])) {
    RemoveServer($_REQUEST['address']);
    echo '<center>Removing server...<br>Server ' . $_REQUEST['address'] . ' successfully removed!<br><br></center>';

    /*
  if ($CFG->use_logfile == "yes") {
      $remote_address = $_SERVER['REMOTE_ADDR'];
      $user						= $_SESSION['uid'];
      $srvr						= $_REQUEST['address'];

      AddLogEntry("$remote_address | $user | Removed server $srvr");
  }
  */
    //include "$CFG->footer";
    //exit();
} elseif (isset($_REQUEST['apply'])) {
    SetRCON($_REQUEST['rcon'], $_REQUEST['address']);
    if ($_REQUEST['rcon'] == '') {
        echo '<center>Setting RCON...<br>' . 'RCON for server ' . $_REQUEST['address'] . ' successfully removed!<br><br></center>';
        /*
        if ($CFG->use_logfile == "yes") {
            $remote_address = $_SERVER['REMOTE_ADDR'];
            $user						= $_SESSION['uid'];
            $srvr						= $_REQUEST['address'];

            AddLogEntry("$remote_address | $user | Removed RCON for server $srvr");
        }
        */
    } else {
        echo '<center>Setting RCON...<br>RCON for server ' . $_REQUEST['address'] . ' successfully set to ' . $_REQUEST['rcon'] . '!</center><br><br>';
        /*
        if ($CFG->use_logfile == "yes") {
            $remote_address = $_SERVER['REMOTE_ADDR'];
            $user						= $_SESSION['uid'];
            $srvr						= $_REQUEST['address'];

            AddLogEntry("$remote_address | $user | Added RCON for server $srvr");
        }
        */
    }
    //include "$CFG->footer";
    //exit();
} elseif (isset($_REQUEST['permedit'])) {
    UpdateServerInfo('gametype', $_REQUEST['gametype'], $_REQUEST['address']);
    MsgUser('Applying gametype to server...', 'Gametype of server ' . $_REQUEST['address'] . ' successfully set to ' . $_REQUEST['gametype'], 'ok', $_SERVER['PHP_SELF']);
    /*
    if ($CFG->use_logfile == "yes") {
        $remote_address = $_SERVER['REMOTE_ADDR'];
        $user						= $_SESSION['uid'];
        $srvr						= $_REQUEST['address'];
        $gtype					= $_REQUEST['gametype'];

        AddLogEntry("$remote_address | $user | Gametype for server $srvr set to $gtype");
    }
*/
    //include "$CFG->footer";
    //exit();
} elseif ((isset($_REQUEST['motd'])) || (isset($_REQUEST['motd_apply']))) {
    if (isset($_REQUEST['motd_apply'])) {
        ApplyMOTDDetails($_REQUEST['address'], $_REQUEST['motd_delay'], $_REQUEST['amxban_motd']);
        echo '<center>Applying AMXBan MOTD settings...<br>MOTD settings successfully applied<br></center><br>';
        /*
        if ($CFG->use_logfile == "yes") {
            $remote_address = $_SERVER['REMOTE_ADDR'];
            $user						= $_SESSION['uid'];
            $srvr						= $_REQUEST['address'];

            AddLogEntry("$remote_address | $user | Edited MOTD-settings for server $srvr");
        }
        */
        //include "$CFG->footer";
        //exit();
    }
    $mymotd = GetMOTDDetails($_REQUEST['address']);

    ?>
    <center><b>AMXBan MOTD editor...<?= $_REQUEST['address'] ?></b></center>
    <table border="1" cellpadding="4" cellspacing="0" width="100%">


        <form name="apply" method="post" action="index.php?task=server_mgmt" onSubmit="return check_input();">
            <input type="hidden" name="address" value="<?= $_REQUEST['address'] ?>">
            <tr>
                <td bgcolor="#f0ffff">AMXBan MOTD display time (secs):</td>
                <td bgcolor="#f0ffff">

                    <select name="motd_delay" style="font: 8pt bold; width: 100px">

                        <?php

                        //$existing_levels = GetAdminLevels();

                        for ($i = 1; $i < 61; $i++) {
                            ?>

                            <option value="<?= $i ?>"<?php if ($i == $mymotd['delay']) {
                                echo 'selected';
                            } ?>><?= $i ?></option>

                            <?php
                        }

                        ?>

                    </select>

                </td>
            </tr>

            <tr>
                <td bgcolor="#f0ffff">AMXBan MOTD URL:</td>
                <td bgcolor="#f0ffff"><input type="text" name="amxban_motd" value="<?= $mymotd['motd'] ?>" style="font: 8pt bold; width: 400px"></td>
            </tr>
            <tr>

            <tr>
                <td colspan="2" align="right"><input type="submit" name="motd_apply" value=" apply " style="font: 8pt bold"></td>
            </tr>
    </table>

    <?php
    //exit();

}
////////////////////////////////}
if ((!isset($_REQUEST['motd'])) && (!isset($_REQUEST['motd_apply']))) {
    ?>
    <center><b>Server Managment<br><br>Participating server(s)...</b></center>
    <table border="1" cellpadding="4" cellspacing="0">

        <tr>
            <td width="1%"><i>&nbsp;</i></td>
            <td><i>Hostname</i></td>
            <td><i>Server IP</i></td>
            <td width="4%"><i>Gametype</i></td>
            <?php if ($CFG->use_rcon == 'yes') {
                echo "<td width=\"4%\"><i>RCON</i></td>\n";
            } ?>
            <td width="10%"><i>Last Registered</i></td>
            <td width="5%"><i>Plugin</i></td>
            <td width="5%"><i>Ban msg</i></td>
            <td width="7%"><i>Action</i></td>
        </tr>

        <?php

        $server = GetServers();

        if ($server == 0) {
            echo '<br>Currently no servers have registered with the bansystem.<br>Make sure the plugin amxbans.amx is properly installed on your gameserver(s).<br>Consult readme.txt for more information.<br><br>';
        } else {
        foreach ($server

        as $row) {
        ?>

        <tr>
            <td bgcolor="#f0ffff"><?php if (!empty($row['gametype']) && $row['gametype'] != 'xxx') {
                    echo '<img src="../images/' . $row['gametype'] . '.gif">';
                } else {
                    echo "<img src=\"$CFG->imagedir/huh.gif\">";
                } ?></td>
            <td bgcolor="#f0ffff"><?= $row['hostname'] ?></td>
            <td bgcolor="#f0ffff"><?= $row['address'] ?></td>
            <?php

            if ((empty($row['gametype']) || ($row['gametype'] == 'xxx'))) {
                echo "<form name=\"edit\" method=\"post\" action=\"index.php?task=server_mgmt\">\n";
                echo "<input type=\"hidden\" name=\"permedit\" value=\"yes\">\n";
                echo '<input type="hidden" name="address" value="' . $row['address'] . "\">\n";
                echo "<td bgcolor=\"#f0ffff\">\n";
                echo "<select name=\"gametype\" onChange=\"this.form.submit();\"><option value=\"xxx\">select gametype...</option><option value=\"cstrike\">cstrike</option><option value=\"tfc\">tfc</option><option value=\"dod\">dod</option><option value=\"ns\">ns</option></select>\n";
                echo "</td>\n";
                echo "</form>\n";
            } else {
                echo '<td bgcolor="#f0ffff">' . $row['gametype'] . "</td>\n";
            }

            ?>

            <form name="remove" method="post" action="index.php?task=server_mgmt">
                <input type="hidden" name="address" value="<?= $row['address'] ?>">

                <?php

                if ($CFG->use_rcon == 'yes') {
                    ?>

                    <td bgcolor="#f0ffff"><input type="text" name="rcon" <?php echo 'value="' . $row['rcon'] . '"'; ?> style="font: 8pt bold; width: 100px"></td>

                    <?php
                }

                ?>

                <td bgcolor="#f0ffff"><?= $row['time'] ?></td>

                <?php

                /*		if ($CFG->version_checking == "enable") {

                            $this_plug_version					= $row["amxban_version"];
                            $plugvers										= explode("_", $this_plug_version);
                            $mod												= $plugvers[0];
                            $version										= $plugvers[1];
                            $new_plugversion_available	= CheckAMXPlugVersion($version,$mod);

                            if ($new_plugversion_available == 1) {
                                $amxversion = "<font color=\"#660000\">".$plugvers[1]." ($mod)</font>";
                            } else {
                                $amxversion = "<font color=\"#006600\">".$plugvers[1]." ($mod)</font>";
                            }

                            echo "<td bgcolor=\"#f0ffff\"><b>$amxversion</b></td>\n";
                        } else {
                */
                echo '<td bgcolor="#f0ffff">' . $row['amxban_version'] . "</td>\n";
                //		}

                ?>

                <td bgcolor="#f0ffff" align="center"><input type="submit" name="motd" value=" edit... " style="font: 8pt bold"></td>

                <td bgcolor="#f0ffff" align="right">

                    <?php

                    if ($CFG->use_rcon == 'yes') {
                        ?>

                        <input type="submit" name="apply" value=" apply " style="font: 8pt bold">

                        <?php
                    }

                    ?>

                    <input type="submit" name="remove" value=" remove " style="font: 8pt bold" onclick="javascript:return confirm('Are you sure you want to remove this server ?')"></td>
            </form>
            <?php

            echo "</tr>\n";
            }
            }

            ?>

    </table>
<?php } ?>

