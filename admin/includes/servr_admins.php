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
if (isset($_REQUEST['update'])) {
    $things_changed = 0;

    foreach ($_POST as $key => $value) {
        if ('off' == $value) {
            $splitstring = explode('_', $key);

            $things_changed += RemoveAdminFromServer($splitstring[0], $splitstring[1]);
        } elseif ('on' == $value) {
            $splitstring = explode('_', $key);

            $things_changed += AddAdmin2Server($splitstring[0], $splitstring[1]);
        }
    }

    if (0 == $things_changed) {
        echo '<center>No changes were made.</center>';
    } elseif (1 == $things_changed) {
        echo '<center>Assigning admin(s) to server(s)...<br>Changes succesfully implemented.<br>Your server(s) might need a mapchange/restart for the changes to take effect.</center>';

        //if ($CFG->use_logfile == "yes") {
        //	$remote_address = $_SERVER['REMOTE_ADDR'];
        //	$user						= $_SESSION['uid'];

        //AddLogEntry("$remote_address | $user | Made changes to serveradmins");
        //}
    }
}

$server = GetServerInfo();

if (0 == $server) {
    echo '<center>Server registration pending...';

    echo 'No servers have currently registered with the bansystem. Please refer to the readme on how to install the plugins on your server(s).</center>';
} else { ?>
    <form name="manage" method="post" action="index.php?task=servr_admins">
        <center><b>Server admins...</b></center>
        <br>
        <table border="1" cellspacing="0" class="outer">
            <tr>
                <td><i>Nickname / SteamID / IP</i></td>
                <?php foreach ($server as $row) {
    echo '<td align="center"><i>' . $row['hostname'] . '</i></td>';
} ?>
            </tr>
            <?php
            $admin = GetServerAdmins();
            foreach ($admin
            as $row) {
                ?>
            <tr>
                <?php
                if (('' == $row['nickname']) || ($row['username'] == $row['nickname'])) {
                    echo '<td>' . $row['username'] . '</td>';
                } else {
                    echo '<td>' . $row['username'] . ' (' . $row['nickname'] . ')</td>';
                }

                $servers = GetServerInfo();

                foreach ($servers as $row2) {
                    $match_found = AdminServerMatch($row['id'], $row2['id']); ?>
                    <td align="center"><input type="hidden" name="<?php echo $row['id']; ?>_<?php echo $row2['id']; ?>" value="off">
                        <input type="checkbox" name="<?php print $row['id'] ?>_<?php print $row2['id'] ?>" <?php if (1 == $match_found) {
                        echo 'checked';
                    } ?>></td>
                    <?php
                }

                echo '</tr>';
            }
                ?>
        </table>
        <br>
        <center><input type="submit" name="update" value=" update permissions "></center>
    </form>
<?php } ?>

