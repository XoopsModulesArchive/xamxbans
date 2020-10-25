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

global $xoopsConfig;
$xoopsTpl->assign('msg_include_dir', 'file:' . $xoopsConfig['root_path'] . 'modules/xamxbans/templates/msguser_template.html');
$access = check_access();

if ('yes' != $access['bans_edit']) {
    MsgUser('Permission denied', 'You do not have the required credentials to view this page.', 'back', 'javascript:window.history.back()');

    include 'footer.php';

    exit();
}

if (isset($_REQUEST['permedit'])) {
    $info_changed = 0;

    if (('S' != $_REQUEST['ban_type']) && (empty($_REQUEST['player_ip']))) {
        MsgUser('Editing ban...', "Can't change ban_type to 'ban by ip' if no IP address is given...", 'back', 'javascript:window.history.back()');

        include 'footer.php';

        exit();
    }

    if ($_REQUEST['player_nick'] != $_REQUEST['orig_player_nick']) {
        if (isset($_REQUEST['bid'])) {
            EditBan('bid', $_REQUEST['bid'], 'player_nick', $_REQUEST['player_nick']);

            if ('yes' == $CFG->use_logfile) {
                $remote_address = $_SERVER['REMOTE_ADDR'];

                $user = $_SESSION['uid'];

                $origtarget = $_REQUEST['orig_player_nick'];

                $target = $_REQUEST['player_nick'];

                $baneyedee = $_REQUEST['bid'];

                AddLogEntry("$remote_address | $user | BanID $baneyedee was edited (player_nick $origtarget was changed to $target)");
            }
        } elseif (isset($_REQUEST['bhid'])) {
            EditBan('bhid', $_REQUEST['bhid'], 'player_nick', $_REQUEST['player_nick']);

            if ('yes' == $CFG->use_logfile) {
                $remote_address = $_SERVER['REMOTE_ADDR'];

                $user = $_SESSION['uid'];

                $origtarget = $_REQUEST['orig_player_nick'];

                $target = $_REQUEST['player_nick'];

                $baneyedee = $_REQUEST['bhid'];

                AddLogEntry("$remote_address | $user | BanhistoryID $baneyedee was edited (player_nick $origtarget was changed to $target)");
            }
        }

        $info_changed = 1;
    }

    if ($_REQUEST['player_id'] != $_REQUEST['orig_player_id']) {
        if (isset($_REQUEST['bid'])) {
            EditBan('bid', $_REQUEST['bid'], 'player_id', $_REQUEST['player_id']);

            if ('yes' == $CFG->use_logfile) {
                $remote_address = $_SERVER['REMOTE_ADDR'];

                $user = $_SESSION['uid'];

                $origtarget = $_REQUEST['orig_player_id'];

                $target = $_REQUEST['player_id'];

                $baneyedee = $_REQUEST['bid'];

                AddLogEntry("$remote_address | $user | BanID $baneyedee was edited (player_id $origtarget was changed to $target)");
            }
        } elseif (isset($_REQUEST['bhid'])) {
            EditBan('bhid', $_REQUEST['bhid'], 'player_id', $_REQUEST['player_id']);

            if ('yes' == $CFG->use_logfile) {
                $remote_address = $_SERVER['REMOTE_ADDR'];

                $user = $_SESSION['uid'];

                $origtarget = $_REQUEST['orig_player_id'];

                $target = $_REQUEST['player_id'];

                $baneyedee = $_REQUEST['bhid'];

                AddLogEntry("$remote_address | $user | BanhistoryID $baneyedee was edited (player_id $origtarget was changed to $target)");
            }
        }

        $info_changed = 1;
    }

    if ($_REQUEST['player_ip'] != $_REQUEST['orig_player_ip']) {
        if (isset($_REQUEST['bid'])) {
            EditBan('bid', $_REQUEST['bid'], 'player_ip', $_REQUEST['player_ip']);

            if ('yes' == $CFG->use_logfile) {
                $remote_address = $_SERVER['REMOTE_ADDR'];

                $user = $_SESSION['uid'];

                $origtarget = $_REQUEST['orig_player_ip'];

                $target = $_REQUEST['player_ip'];

                $baneyedee = $_REQUEST['bid'];

                AddLogEntry("$remote_address | $user | BanID $baneyedee was edited (player_ip $origtarget was changed to $target)");
            }
        } elseif (isset($_REQUEST['bhid'])) {
            EditBan('bhid', $_REQUEST['bhid'], 'player_ip', $_REQUEST['player_ip']);

            if ('yes' == $CFG->use_logfile) {
                $remote_address = $_SERVER['REMOTE_ADDR'];

                $user = $_SESSION['uid'];

                $origtarget = $_REQUEST['orig_player_ip'];

                $target = $_REQUEST['player_ip'];

                $baneyedee = $_REQUEST['bhid'];

                AddLogEntry("$remote_address | $user | BanhistoryID $baneyedee was edited (player_ip $origtarget was changed to $target)");
            }
        }

        $info_changed = 1;
    }

    if ($_REQUEST['ban_length'] != $_REQUEST['orig_ban_length']) {
        if (isset($_REQUEST['bid'])) {
            EditBan('bid', $_REQUEST['bid'], 'ban_length', $_REQUEST['ban_length']);

            if ('yes' == $CFG->use_logfile) {
                $remote_address = $_SERVER['REMOTE_ADDR'];

                $user = $_SESSION['uid'];

                $origtarget = $_REQUEST['orig_ban_length'];

                $target = $_REQUEST['ban_length'];

                $baneyedee = $_REQUEST['bid'];

                AddLogEntry("$remote_address | $user | BanID $baneyedee was edited (ban_length $origtarget was changed to $target)");
            }
        } elseif (isset($_REQUEST['bhid'])) {
            EditBan('bhid', $_REQUEST['bhid'], 'ban_length', $_REQUEST['ban_length']);

            if ('yes' == $CFG->use_logfile) {
                $remote_address = $_SERVER['REMOTE_ADDR'];

                $user = $_SESSION['uid'];

                $origtarget = $_REQUEST['orig_ban_length'];

                $target = $_REQUEST['ban_length'];

                $baneyedee = $_REQUEST['bhid'];

                AddLogEntry("$remote_address | $user | BanhistoryID $baneyedee was edited (ban_length $origtarget was changed to $target)");
            }
        }

        $info_changed = 1;
    }

    if ($_REQUEST['ban_type'] != $_REQUEST['orig_ban_type']) {
        if (isset($_REQUEST['bid'])) {
            EditBan('bid', $_REQUEST['bid'], 'ban_type', $_REQUEST['ban_type']);

            if ('yes' == $CFG->use_logfile) {
                $remote_address = $_SERVER['REMOTE_ADDR'];

                $user = $_SESSION['uid'];

                $origtarget = $_REQUEST['orig_ban_type'];

                $target = $_REQUEST['ban_type'];

                $baneyedee = $_REQUEST['bid'];

                AddLogEntry("$remote_address | $user | BanID $baneyedee was edited (ban_type $origtarget was changed to $target)");
            }
        } elseif (isset($_REQUEST['bhid'])) {
            EditBan('bhid', $_REQUEST['bhid'], 'ban_type', $_REQUEST['ban_type']);

            if ('yes' == $CFG->use_logfile) {
                $remote_address = $_SERVER['REMOTE_ADDR'];

                $user = $_SESSION['uid'];

                $origtarget = $_REQUEST['orig_ban_type'];

                $target = $_REQUEST['ban_type'];

                $baneyedee = $_REQUEST['bhid'];

                AddLogEntry("$remote_address | $user | BanhistoryID $baneyedee was edited (ban_type $origtarget was changed to $target)");
            }
        }

        $info_changed = 1;
    }

    if ($_REQUEST['ban_reason'] != $_REQUEST['orig_ban_reason']) {
        if (isset($_REQUEST['bid'])) {
            EditBan('bid', $_REQUEST['bid'], 'ban_reason', $_REQUEST['ban_reason']);

            if ('yes' == $CFG->use_logfile) {
                $remote_address = $_SERVER['REMOTE_ADDR'];

                $user = $_SESSION['uid'];

                $origtarget = $_REQUEST['orig_ban_reason'];

                $target = $_REQUEST['ban_reason'];

                $baneyedee = $_REQUEST['bid'];

                AddLogEntry("$remote_address | $user | BanID $baneyedee was edited (ban_reason $origtarget was changed to $target)");
            }
        } elseif (isset($_REQUEST['bhid'])) {
            EditBan('bhid', $_REQUEST['bhid'], 'ban_reason', $_REQUEST['ban_reason']);

            if ('yes' == $CFG->use_logfile) {
                $remote_address = $_SERVER['REMOTE_ADDR'];

                $user = $_SESSION['uid'];

                $origtarget = $_REQUEST['orig_ban_reason'];

                $target = $_REQUEST['ban_reason'];

                $baneyedee = $_REQUEST['bhid'];

                AddLogEntry("$remote_address | $user | BanhistoryID $baneyedee was edited (ban_reason $origtarget was changed to $target)");
            }
        }

        $info_changed = 1;
    }

    if (0 == $info_changed) {
        $xoopsTpl->assign('msg', "Nothing changed. Use the 'back'-button of your browser to change some details...");

        $xoopsTpl->assign('$url', 'javascript:window.history.back()');

        $xoopsTpl->assign('linkname', 'Back');
    } elseif (1 == $info_changed) {
        $xoopsTpl->assign('msg', 'Ban successfully edited...');

        $xoopsTpl->assign('$url', 'index.php');

        $xoopsTpl->assign('linkname', 'Ban List');
    }

    $xoopsTpl->assign('header', 'Editing ban...');

    include 'footer.php';

    exit;
}

$bhid = $bid = -1;
if (isset($_GET[bid])) {
    $ban_details = GetBanDetails($_GET[bid]);

    $bid = $_GET[bid];
} elseif (isset($_GET[bhid])) {
    $bhid = $_GET[bhid];

    $ban_details = GetBanHistDetails($_GET[bhid]);
} else {
    $xoopsTpl->assign('msg', "No BanID / BanHistoryID found to display details for. Please return to <a href=\"$xoopsConfig[url]\">main page</a> and try again...\n");

    $xoopsTpl->assign('$url', $xoopsConfig['url']);

    $xoopsTpl->assign('linkname', 'Ban List');

    $xoopsTpl->assign('header', 'Displaying ban...');

    include 'footer.php';

    exit();
}

$xoopsTpl->assign('ban_details', $ban_details);
$xoopsTpl->assign('bhid', $bhid);
$xoopsTpl->assign('bid', $bid);
