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

$access = check_access();
global $xoopUser;

if (isset($_GET['bid'])) {
    $bid = $_GET['bid'];
}
if (isset($_GET['bhid'])) {
    $bhid = $_GET['bhid'];
}

if ('yes' != $access['bans_delete']) {
    MsgUser('Permission denied', 'You do not have the required credentials to view this page.', 'back', 'javascript:window.history.back()');

    exit();
}

if (isset($_REQUEST['permdelete'])) {
    foreach ($_POST as $key => $value) {
        if ('bid' == $key) {
            RemoveBan($key, $value);

            if ('yes' == $CFG->use_logfile) {
                $remote_address = $_SERVER['REMOTE_ADDR'];

                $user = $_SESSION['uid'];

                AddLogEntry("$remote_address | $user | BanID $value was removed");
            }
        } elseif ('bhid' == $key) {
            RemoveBan($key, $value);

            if ('yes' == $CFG->use_logfile) {
                $remote_address = $_SERVER['REMOTE_ADDR'];

                $user = $_SESSION['uid'];

                AddLogEntry("$remote_address | $user | BanhistoryID $value was removed");
            }
        }

        if ('on' == $value) {
            RemoveBan('bhid', $key);

            if ('yes' == $CFG->use_logfile) {
                $remote_address = $_SERVER['REMOTE_ADDR'];

                $user = $_SESSION['uid'];

                AddLogEntry("$remote_address | $user | BanhistoryID $key was removed");
            }
        }
    }

    MsgUser('Deleting ban(s) from Database...', 'Ban(s) successfully removed from database...', 'back to banlist', 'index.php');

    require __DIR__ . '/footer.php';
}

if (isset($_GET[bid])) {
    $ban_details = GetBanDetails($_GET[bid]);

    $banhist = GetPrevBans((string) $ban_details[player_steamid]);
} elseif (isset($_GET[bhid])) {
    $ban_details = GetBanHistDetails($_GET[bhid]);
} else {
    MsgUser('Error displaying bandetails...', 'No BanID / BanHistoryID found. Something went terribly wrong!', 'back', 'javascript:window.history.back()');

    include (string) $CFG->footer;

    exit();
}

?>

<table border="1" cellpadding="4" cellspacing="0" width="100%">
	<tr>
		<td colspan="4"><b>Going to delete...</b></td>
	</tr>

	<form name="delban" method="post" action="index.php?mode=delete_ban">

<?php

if (isset($_GET[bid])) {
    echo "<input type=\"hidden\" name=\"bid\" value=\"$bid\">\n";
} elseif (isset($_GET[bhid])) {
    echo "<input type=\"hidden\" name=\"bhid\" value=\"$bhid\">\n";
}

?>

	<tr>
		<td width="30%" >Nickname: </td>
		<td ><?=$ban_details[player_nick] ?></td>
	</tr>

	<tr>
		<td width="30%" >SteamID: </td>
		<td ><?=$ban_details[player_steamid] ?></td>
	</tr>

	<tr>
		<td width="30%" >Effective per:</td>
		<td ><?=$ban_details[ban_start] ?></td>
	</tr>

<?php

$date_and_ban = $ban_details[ban_start_tstamp] + ($ban_details[ban_length] * 60);
$expire_date = getdate((string) $date_and_ban);
$now = date('U');
$timetester = $date_and_ban - $now;
$timeleft = timeleft($now, $date_and_ban);

$month = $expire_date[mon];
$day = $expire_date[mday];
$hours = $expire_date[hours];
$minutes = $expire_date[minutes];
$seconds = $expire_date[seconds];

if (1 == mb_strlen((string) $month)) {
    $month = '0' . $month;
}

if (1 == mb_strlen((string) $day)) {
    $day = '0' . $day;
}

if (1 == mb_strlen((string) $hours)) {
    $hours = '0' . $hours;
}

if (1 == mb_strlen((string) $minutes)) {
    $minutes = '0' . $minutes;
}

if (1 == mb_strlen((string) $seconds)) {
    $seconds = '0' . $seconds;
}

if (0 == $ban_details[ban_length]) {
    echo "<tr><td width=\"30%\" >Ban Length: </td><td >Permanent</td></tr>\n";
} else {
    echo "<tr><td width=\"30%\" >Ban Length: </td><td >$ban_details[ban_length] minutes</td></tr>\n";

    if ($date_and_ban > $now) {
        echo "<tr><td width=\"30%\" >Expires on: </td><td >$day/$month/$expire_date[year] $hours:$minutes:$seconds ($timeleft remaining)<br>\n";
    } else {
        echo "<tr><td width=\"30%\" >Expired on: </td><td >$day/$month/$expire_date[year] $hours:$minutes:$seconds (allready expired)<br>\n";
    }
}

if ('S' == $ban_details[ban_type]) {
    echo '<tr><td width="30%" >Bantype: </td><td >SteamID';
} elseif ('I' == $ban_details[ban_type]) {
    echo '<tr><td width="30%" >Bantype: </td><td >IP';
} else {
    echo '<tr><td width="30%" >Bantype: </td><td >SteamID + IP';
}

echo "<tr><td width=\"30%\" >Reason: </td><td >$ban_details[ban_reason]</td></tr>\n";

if ($ban_details[admin_org] != $ban_details[admin_nick]) {
    echo "<tr><td width=\"30%\" >Banned by:</td><td >$ban_details[admin_nick] ($ban_details[admin_org])</td></tr>\n";
} else {
    echo "<tr><td width=\"30%\" >Banned by:</td><td >$ban_details[admin_nick]</td></tr>\n";
}

echo "<tr><td width=\"30%\" >Ban invoked on:</td><td >$ban_details[server_name]</td></tr>\n";

if ((isset($_GET[bid])) && (0 == $banhist)) {
    echo '<tr><td colspan="2" align="right"><input type="submit" name="permdelete" value=" delete " style="font: 8pt bold"></td></tr></form>';
} elseif (isset($_GET[bhid])) {
    echo '<td colspan="2" align="right"><input type="submit" name="permdelete" value=" delete " style="font: 8pt bold"></td></tr></form>';
}

echo '</table>';

if ((isset($_GET[bid])) && (0 != $banhist)) {
    ?>

<br>

			<table border="1" cellpadding="4" cellspacing="0" width="100%">
				<tr>
					<td colspan="5"><b>Remove previous bans for this user too?</b></td>
				</tr>
				<tr>
					<td width="1%">&nbsp;</td>
					<td width="32%"><i>Date</i></td>
					<td width="23%"><i>Admin</i></td>
					<td width="43%"><i>Reason</i></td>
					<td width="1%">&nbsp;</td>
				</tr>

<?php

    if (0 == $banhist) {
        echo '<tr ><td colspan="5">No previous bans found for this player...</td></tr>';

        echo '</table>';
    } else {
        foreach ($banhist as $row) {
            echo "<tr >\n";

            $svr_name = $row['server_name'];

            if ('DO' == mb_substr((string) $svr_name, 7, 2)) {
                echo "<td><img src=\"$CFG->imagedir/dod.gif\" alt=\"" . $row['server_name'] . "\"></td>\n";
            } elseif ('NS' == mb_substr((string) $svr_name, 7, 2)) {
                echo "<td><img src=\"$CFG->imagedir/ns.gif\" alt=\"" . $row['server_name'] . "\"></td>\n";
            } elseif ('CS' == mb_substr((string) $svr_name, 7, 2)) {
                echo "<td><img src=\"$CFG->imagedir/cstrike.gif\" alt=\"" . $row['server_name'] . "\"></td>\n";
            } else {
                echo "<td><img src=\"$CFG->imagedir/html.gif\" alt=\"Website\"></td>\n";
            }

            echo '<td>' . $row['fban_created'] . "</td>\n";

            echo '<td>' . $row['admin_nick'] . "</td>\n";

            echo '<td>' . $row['ban_reason'] . "</td>\n";

            echo '<td><input type="checkbox" name="' . $row['bhid'] . "\" style=\"font: 8pt bold\"></td>\n";

            echo "</tr>\n";
        }

        echo '<tr><td  align="right"><input type="submit" name="permdelete" value=" delete " style="font: 8pt bold"></td></tr></form>';
    } ?>


</table>

<?php
}
