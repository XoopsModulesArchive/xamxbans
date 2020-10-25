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

if (('yes' == $access['bans_add']) && (isset($_REQUEST['add']))) {
    //print_r($_POST );

    if (('I' == $_REQUEST['ban_type']) && (empty($_REQUEST['player_ip']))) {
        echo 'You need to specify an IP-address when banning by IP';

        exit();
    }

    if (empty($_REQUEST['player_ip'])) {
        $message = AddBan($_REQUEST['player_id'], $_REQUEST['player_nick'], $xoopsUser->uname(), $_REQUEST['ban_type'], $_REQUEST['ban_reason'], $_REQUEST['ban_length']);
    } else {
        $message = AddBan($_REQUEST['player_id'], $_REQUEST['player_nick'], $xoopsUser->uname(), $_REQUEST['ban_type'], $_REQUEST['ban_reason'], $_REQUEST['ban_length'], $_REQUEST['player_ip']);
    }

    if ('yes' == $CFG->use_logfile) {
        $remote_address = $_SERVER['REMOTE_ADDR'];

        $user = $_SESSION['uid'];

        $playa = $_REQUEST['player_nick'];

        $steam = $_REQUEST['player_id'];

        AddLogEntry("$remote_address | $user | Player $playa ($steam) got banned via webinterface");
    }

    $xoopsTpl->assign('msg', $message);

    $xoopsTpl->assign('url', 'index.php');

    $xoopsTpl->assign('linkname', 'Ban List');

    $xoopsTpl->assign('header', 'Adding ban...');

    include 'footer.php';

    exit;
}
