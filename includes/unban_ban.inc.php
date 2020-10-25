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
$xoopsTpl->assign('msg_include_dir', 'file:' . $xoopsConfig['root_path'] . 'modules/xamxbans/templates/msguser_template.html');
$access = check_access();
global $xoopsUser;

if ('yes' != $access['bans_unban']) {
    $xoopsTpl->assign('msg', 'You do not have the required credentials to view this page.');

    $xoopsTpl->assign('url', 'javascript:window.history.back()');

    $xoopsTpl->assign('linkname', 'back');

    $xoopsTpl->assign('header', 'Permission denied');

    include 'footer.php';

    exit;
}

if (isset($_REQUEST['permunban'])) {
    UnBan($_REQUEST['bid'], $_REQUEST['unban_reason'], $_REQUEST['unban_admin_nick']);

    if ('yes' == $CFG->use_logfile) {
        $remote_address = $_SERVER['REMOTE_ADDR'];

        $user = $_SESSION['uid'];

        $baneyedee = $_REQUEST['bid'];

        AddLogEntry("$remote_address | $user | Unbanned BanID $baneyedee");
    }

    $xoopsTpl->assign('msg', 'Ban successfully unbanned...');

    $xoopsTpl->assign('url', 'index.php');

    $xoopsTpl->assign('linkname', 'Ban List');

    $xoopsTpl->assign('header', 'Unbanning ban ' . $_REQUEST['bid'] . '...');

    include 'footer.php';

    exit;
}

$ban_details = GetBanDetails($_REQUEST['bid']);
$xoopsTpl->assign('ban_details', $ban_details);

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

if ($date_and_ban > $now) {
    $expired = '0';
} else {
    $expired = '1';
}
$xoopsTpl->assign('bid', $_REQUEST['bid']);
$xoopsTpl->assign('xoopsUser', $xoopsUser->getVar('uname'));
$xoopsTpl->assign('expired', $expired);
$xoopsTpl->assign('expire_date', $day . '/' . $month . '/' . $expire_date['year'] . ' ' . $hours . ':' . $minutes . ':' . $seconds);
