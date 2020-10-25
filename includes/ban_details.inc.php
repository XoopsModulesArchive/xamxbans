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

$banhist = [];
$access = check_access();
if (isset($_GET['bid'])) {
    $ban_details = GetBanDetails($_GET['bid']);

    $banhist = GetPrevBans((string)$ban_details[player_steamid]);
} elseif (isset($_GET['bhid'])) {
    $ban_details = GetBanHistDetails($_GET['bhid']);
} else {
    MsgUser('Displaying ban...', "No BanID / BanHistoryID found to display details for. Please return to <a href=\"index.php\">main page</a> and try again...\n", 'back', 'index.php');

    exit();
}

$date_and_ban = $ban_details['ban_start_tstamp'] + ($ban_details['ban_length'] * 60);
$expire_date = getdate((string)$date_and_ban);
$now = date('U');
$timetester = $date_and_ban - $now;
$timeleft = timeleft($now, $date_and_ban);
$month = $expire_date['mon'];
$day = $expire_date['mday'];
$hours = $expire_date['hours'];
$minutes = $expire_date['minutes'];
$seconds = $expire_date['seconds'];

if (1 == mb_strlen((string)$month)) {
    $month = '0' . $month;
}

if (1 == mb_strlen((string)$day)) {
    $day = '0' . $day;
}

if (1 == mb_strlen((string)$hours)) {
    $hours = '0' . $hours;
}

if (1 == mb_strlen((string)$minutes)) {
    $minutes = '0' . $minutes;
}

if (1 == mb_strlen((string)$seconds)) {
    $seconds = '0' . $seconds;
}

if (('I' == $ban_details['ban_type'] || 'SI' == $ban_details['ban_type']) && 'no' == $access['bans_edit']) {
    $ban_details['player_ip'] = '<hidden>';
}

if ('S' == $ban_details['ban_type'] || 'SI' == $ban_details['ban_type']) {
    if ('yes' == $CFG->use_xhlstats) {
        global $xoopsDB;

        $req = $xoopsDB->query('SELECT * FROM ' . $xoopsDB->prefix('hlstats_PlayerUniqueIds') . " WHERE uniqueId = '" . $ban_details['player_steamid'] . "'");

        $numrows = $xoopsDB->getRowsNum($req);

        if ($numrows > 0) {
            $row = $xoopsDB->fetchArray($req);

            $xoopsTpl->assign('stats_link', $row['playerId']);
        }
    }
}

$bhid = $_GET['bhid'] ?? -1;
if ($date_and_ban > $now) {
    $expired = '0';
} else {
    $expired = '1';
}
$xoopsTpl->assign('ban_details', $ban_details);
$xoopsTpl->assign('expired', $expired);
$xoopsTpl->assign('bhid', $bhid);
$xoopsTpl->assign('access', $access);
$xoopsTpl->assign('timeleft', $timeleft);
$xoopsTpl->assign('banhist', $banhist);
$xoopsTpl->assign('expire_date', $day . '/' . $month . '/' . $expire_date[year] . ' ' . $hours . ':' . $minutes . ':' . $seconds);

//$xoopsTpl->assign('day',$day);
//$xoopsTpl->assign('month',$month);
//$xoopsTpl->assign('now',$now);
//$xoopsTpl->assign('hours',$hours);
//$xoopsTpl->assign('seconds',$seconds);
