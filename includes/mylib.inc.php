<?php

function DaysInCurMonth()
{
    $this_month = date('m');
    $this_year  = date('Y');

    $days     = mktime(0, 0, 0, $month, 1, $year);
    $nrofdays = date('t', $days);

    $count       = 1;
    $arraycount  = 0;
    $nrofdayslst = [];

    while ($count <= $nrofdays) {
        $nrofdayslst[$arraycount] = $count;
        $count++;
        $arraycount++;
    }

    return $nrofdayslst;
}

function GetGametypeNames()
{
    global $xoopsDB;

    $list_names = $xoopsDB->query('SELECT DISTINCT gametype FROM ' . $xoopsDB->prefix('amx_servers'));
    $numrows    = $xoopsDB->getRowsNum($list_names);

    if ($numrows == 0) {
        return 0;
        exit();
    }

    while (false !== ($myadmins = $xoopsDB->fetchArray($list_names))) {
        $data[] = $myadmins[gametype];
    }

    return $data;
}

function GetServerNames()
{
    global $xoopsDB;

    $list_servers = $xoopsDB->query('SELECT hostname FROM ' . $xoopsDB->prefix('amx_servers'));
    $numrows      = $xoopsDB->getRowsNum($list_servers);

    //	if ($numrows == 0) {
    //		return 0;
    //		exit();
    //	}

    $data    = [];
    $counter = 0;

    while (false !== ($myservers = $xoopsDB->fetchArray($list_servers))) {
        $data[$counter] = $myservers['hostname'];
        $counter++;
    }

    return $data;
}

function GetBansPerAdmin($admin)
{
    global $xoopsDB;

    $get_admin_id = $xoopsDB->query('SELECT steamid FROM ' . $xoopsDB->prefix('amx_amxadmins') . " WHERE nickname = '$admin'");

    while (false !== ($myadmins = $xoopsDB->fetchArray($get_admin_id))) {
        $steamid = $myadmins['steamid'];
    }

    $list_bans = $xoopsDB->query('SELECT * FROM ' . $xoopsDB->prefix('amx_bans') . " WHERE admin_id = '$steamid'");
    $numrows   = $xoopsDB->getRowsNum($list_bans);

    $bancount = $numrows;

    $list_histbans = $xoopsDB->query('SELECT * FROM ' . $xoopsDB->prefix('amx_banhistory') . " WHERE admin_id = '$steamid'");
    $numrows       = $xoopsDB->getRowsNum($list_histbans);

    $bancount += $numrows;

    return $bancount;
}

function GetBansPerGametype($gametype)
{
    global $xoopsDB;

    $get_servers = $GLOBALS['xoopsDB']->queryF('SELECT hostname FROM ' . $xoopsDB->prefix('amx_servers') . " WHERE gametype = '$gametype'");
    $numrows     = $xoopsDB->getRowsNum($get_servers);

    if ($numrows == 0) {
        return 0;
        exit();
    }

    $totalbans = 0;

    while (false !== ($myservers = $GLOBALS['xoopsDB']->fetchBoth($get_servers))) {
        $get_bancount = $GLOBALS['xoopsDB']->queryF('SELECT bid FROM ' . $xoopsDB->prefix('amx_bans') . " WHERE server_name = '$myservers[hostname]' AND ban_created != '1009839660'");
        $numrows      = $xoopsDB->getRowsNum($get_bancount);

        $totalbans += $numrows;
    }

    return $totalbans;
}

function GetBansForMonth($month, $year)
{
    global $xoopsDB;

    $days        = mktime(0, 0, 0, $month, 1, $year);
    $nrofdays    = date('t', $days);
    $count       = 1;
    $arraycount  = 0;
    $nrofbanslst = [];

    while ($count <= $nrofdays) {
        if (strlen((string)$count) == 1) {
            $count = '0' . $count;
        }

        if (strlen((string)$month) == 1) {
            $month = '0' . $month;
        }

        $date = "$count$month$year";

        $list_bans = $xoopsDB->query('SELECT * FROM ' . $xoopsDB->prefix('amx_bans') . " WHERE FROM_UNIXTIME(ban_created,'%d%m%Y') LIKE '$date'");
        $numrows   = $xoopsDB->getRowsNum($list_bans);

        $nrofbanslst[$arraycount] = $numrows;
        $count++;
        $arraycount++;
    }

    return $nrofbanslst;
}

function GetAdminNames()
{
    global $xoopsDB;

    $list_names = $xoopsDB->query('SELECT DISTINCT steamid, nickname FROM ' . $xoopsDB->prefix('amx_amxadmins'));
    $numrows    = $xoopsDB->getRowsNum($list_names);

    while (false !== ($myadmins = $xoopsDB->fetchArray($list_names))) {
        $data[] = $myadmins['nickname'];
    }

    return $data;
}

function SearchByServer($server)
{
    global $xoopsDB;

    $date = substr_replace($date, '', 2, 1);
    $date = substr_replace($date, '', 4, 1);

    $list_bans = $xoopsDB->query("SELECT *, FROM_UNIXTIME(ban_created,'%d/%m/%Y %H:%i:%s') AS fban_created FROM " . $xoopsDB->prefix('amx_bans') . " WHERE server_name = '$server'");
    $numrows   = $xoopsDB->getRowsNum($list_bans);

    if ($numrows == 0) {
        return 0;
        exit();
    }

    while (false !== ($mysearchedbans = $xoopsDB->fetchArray($list_bans))) {
        $data[] = $mysearchedbans;
    }

    return $data;
}

function SearchByDate($date)
{
    global $xoopsDB;

    $date = substr_replace($date, '', 2, 1);
    $date = substr_replace($date, '', 4, 1);

    $list_bans = $xoopsDB->query("SELECT *, FROM_UNIXTIME(ban_created,'%d/%m/%Y %H:%i:%s') AS fban_created FROM " . $xoopsDB->prefix('amx_bans') . " WHERE FROM_UNIXTIME(ban_created,'%d%m%Y') LIKE '$date'");
    $numrows   = $xoopsDB->getRowsNum($list_bans);

    if ($numrows == 0) {
        return 0;
        exit();
    }

    while (false !== ($mysearchedbans = $xoopsDB->fetchArray($list_bans))) {
        $data[] = $mysearchedbans;
    }

    return $data;
}

function SearchBySteamid2($steamid)
{
    global $xoopsDB;

    $list_ban = $xoopsDB->query('SELECT bid FROM ' . $xoopsDB->prefix('amx_bans') . " WHERE player_id = '$steamid'");
    $numrows  = $xoopsDB->getRowsNum($list_ban);

    if ($numrows == 0) {
        return 0;
    } else {
        while (false !== ($mysearchedban = $xoopsDB->fetchArray($list_ban))) {
            $bid = $mysearchedban[bid];
        }
    }

    return $bid;
}

function SearchByAdmin($steamid)
{
    // Searches all bans done by a particular admin
    global $xoopsDB;

    $date = substr_replace($date, '', 2, 1);
    $date = substr_replace($date, '', 4, 1);

    $list_bans = $xoopsDB->query("SELECT *, FROM_UNIXTIME(ban_created,'%d/%m/%Y %H:%i:%s') AS fban_created FROM " . $xoopsDB->prefix('amx_bans') . " WHERE admin_id = '$steamid'");
    $numrows   = $xoopsDB->getRowsNum($list_bans);

    if ($numrows == 0) {
        return 0;
        exit();
    }

    $data = [];

    while (false !== ($mysearchedbans = $xoopsDB->fetchArray($list_bans))) {
        $data[] = $mysearchedbans;
    }

    return $data;
}

function GetNROfActiveBans()
{
    global $xoopsDB;

    $get_bans = $xoopsDB->query('SELECT * from ' . $xoopsDB->prefix('amx_bans'));
    $numrows  = $xoopsDB->getRowsNum($get_bans);

    return $numrows;
}

function GetNROfExpiredBans()
{
    global $xoopsDB;

    $get_hist_bans = $xoopsDB->query('SELECT * from ' . $xoopsDB->prefix('amx_banhistory'));
    $numrows       = $xoopsDB->getRowsNum($get_hist_bans);

    return $numrows;
}

function GetNROfPermBans()
{
    global $xoopsDB;

    $get_bans = $xoopsDB->query('SELECT * FROM ' . $xoopsDB->prefix('amx_bans') . ' WHERE ban_length = 0');
    $numrows  = $xoopsDB->getRowsNum($get_bans);

    return $numrows;
}

function GetNROfTempBans()
{
    global $xoopsDB;

    $get_bans = $xoopsDB->query('SELECT * FROM ' . $xoopsDB->prefix('amx_bans') . ' WHERE ban_length != 0');
    $numrows  = $xoopsDB->getRowsNum($get_bans);

    return $numrows;
}

function GetNROfImportedBans()
{
    global $xoopsDB;

    $get_bans = $xoopsDB->query('SELECT * from ' . $xoopsDB->prefix('amx_bans') . " WHERE player_nick = 'unknown (imported)'");
    $numrows  = $xoopsDB->getRowsNum($get_bans);

    return $numrows;
}

function GetNROfImportedActBans()
{
    global $xoopsDB;

    $get_bans = $xoopsDB->query('SELECT * from ' . $xoopsDB->prefix('amx_bans') . " WHERE player_nick = 'unknown (imported)'");
    $numrows  = $xoopsDB->getRowsNum($get_bans);

    return $numrows;
}

function GetNROfImportedExpBans()
{
    global $xoopsDB;

    $get_hist_bans = $xoopsDB->query('SELECT * from ' . $xoopsDB->prefix('amx_banhistory') . " WHERE player_nick = 'unknown (imported)'");
    $numrows       = $xoopsDB->getRowsNum($get_hist_bans);

    return $numrows;
}

function UnBan($bid, $unban_reason, $unban_admin_nick)
{
    global $xoopsDB;

    $list_ban = $xoopsDB->query('SELECT * FROM ' . $xoopsDB->prefix('amx_bans') . " WHERE bid = '$bid'");
    //	$numrows	= $GLOBALS['xoopsDB']->getRowsNum($list_ban);

    //	if ($numrows == 0) {
    //		return 0;
    //		exit();
    //	}

    while (false !== ($myban = $xoopsDB->fetchArray($list_ban))) {
        $unban_created = date('U');
        $insert_ban    = $xoopsDB->query(
            'INSERT INTO '
            . $xoopsDB->prefix('amx_banhistory')
            . " (bhid, player_ip, player_id, player_nick, admin_ip, admin_id, admin_nick, ban_type, ban_reason, ban_created, ban_length, server_name, unban_created, unban_reason, unban_admin_nick) VALUES ( '', '$myban[player_ip]', '$myban[player_id]', '$myban[player_nick]', '$myban[admin_ip]', '$myban[admin_id]', '$myban[admin_nick]', '$myban[ban_type]', '$myban[ban_reason]', '$myban[ban_created]', '$myban[ban_length]', '$myban[server_name]', '$unban_created', '$unban_reason', '$unban_admin_nick')"
        );
        RemoveBan('bid', $bid);
    }
}

function EditBan($idtype, $id, $key, $value)
{
    global $xoopsDB;

    if ($idtype == 'bid') {
        $update_ban = $xoopsDB->query('UPDATE ' . $xoopsDB->prefix('amx_bans') . " SET $key = '$value' WHERE $idtype = '$id'");
    } elseif ($idtype == 'bhid') {
        $update_ban = $xoopsDB->query('UPDATE ' . $xoopsDB->prefix('amx_banhistory') . " SET $key = '$value' WHERE $idtype = '$id'");
    }
}

function AddImportBan($player_id, $player_nick, $admin_nick, $admin_ip, $ban_type, $ban_reason, $ban_length, $player_ip = '')
{
    global $xoopsDB;

    $check_steamid = $xoopsDB->query('SELECT player_id FROM ' . $xoopsDB->prefix('amx_bans') . " WHERE player_id = '$player_id'");
    $numrows       = $xoopsDB->getRowsNum($check_steamid);

    if ($numrows != 0) {
        $cow = 'SteamID allready exists!';
        return $cow;
        exit();
    }

    $ban_created = date('U');
    $server_name = 'website';

    $insert_ban = $xoopsDB->query(
        'INSERT INTO '
        . $xoopsDB->prefix('amx_bans')
        . " (bid, player_ip, player_id, player_nick, admin_ip, admin_id, admin_nick, ban_type, ban_reason, ban_created, ban_length, server_ip, server_name) VALUES ('', '$player_ip', '$player_id', '$player_nick', '$admin_ip', '$admin_id', '$admin_nick', '$ban_type', '$ban_reason', '$ban_created', '$ban_length', '', '$server_name')"
    );

    $cow = 'Ban added successfully!';
    return $cow;
}

function RemoveBan($idtype, $id)
{
    global $xoopsDB;

    if ($idtype == 'bid') {
        $del_ban = $xoopsDB->query('DELETE FROM ' . $xoopsDB->prefix('amx_bans') . " WHERE bid = '$id'");
    } elseif ($idtype == 'bhid') {
        $del_ban = $xoopsDB->query('DELETE FROM ' . $xoopsDB->prefix('amx_banhistory') . " WHERE bhid = '$id'");
    }

    return 1;
}

function AddBan($player_id, $player_nick, $admin_nick, $ban_type, $ban_reason, $ban_length, $player_ip = '')
{
    global $xoopsDB;
    $message = 'Ban successfully added...';

    //	$get_admin	= $xoopsDB->query("SELECT * FROM $CFG->webadmins WHERE username = '$admin_nick'") or die ($GLOBALS['xoopsDB']->error());
    //
    //	while (false !== ($my_admin	= $xoopsDB->fetchArray($get_admin))) {
    //		$admin_id			= "$my_admin[steamid]";
    //	}

    $check_steamid = $xoopsDB->query('SELECT player_id FROM ' . $xoopsDB->prefix('amx_bans') . " WHERE player_id = '$player_id'");
    $numrows       = $xoopsDB->getRowsNum($check_steamid);

    if ($numrows != 0) {
        $message = "An active ban with SteamID $player_id allready exists...";
    } else {
        $ban_created = date('U');
        $server_name = 'website';

        $insert_ban = $xoopsDB->query('INSERT INTO ' . $xoopsDB->prefix('amx_bans') . " VALUES ('', '$player_ip', '$player_id', '$player_nick', '$admin_ip', '$admin_id', '$admin_nick', '$ban_type', '$ban_reason', '$ban_created', '$ban_length', '', '$server_name')");
    }
    return $message;
}

function UpdateWebAdmin($type, $id, $utype, $user_group_id, $level)
{
    global $xoopsDB;

    if ($type == 'insert') {
        $update_admin = $xoopsDB->query('INSERT INTO ' . $xoopsDB->prefix('amx_webadmins') . " VALUES ('','$utype','$user_group_id','$level')");
    } elseif ($type == 'update') {
        $update_admin = $xoopsDB->query('UPDATE ' . $xoopsDB->prefix('amx_webadmins') . " SET type = '$utype', level = '$level', user_group_id = '$user_group_id' WHERE id = '$id'");
    } elseif ($type == 'remove') {
        $update_admin = $xoopsDB->query('DELETE FROM ' . $xoopsDB->prefix('amx_webadmins') . " WHERE id = '$id'");
    }
}

function UpdateAMXAdmin($type, $id, $username, $password, $access, $flags, $steamid, $nickname)
{
    global $xoopsDB;

    if ($type == 'insert') {
        $update_admin = $xoopsDB->query('INSERT INTO ' . $xoopsDB->prefix('amx_amxadmins') . " VALUES ('','$username','$password','$access','$flags','$steamid','$nickname')") or die ($GLOBALS['xoopsDB']->error());
    } elseif ($type == 'update') {
        if ($password == '') {
            $update_admin = $xoopsDB->query('UPDATE ' . $xoopsDB->prefix('amx_amxadmins') . " SET username = '$username', access = '$access', flags = '$flags', steamid = '$steamid', nickname = '$nickname' WHERE id = '$id'") or die ($GLOBALS['xoopsDB']->error());
        } else {
            $update_admin = $xoopsDB->query('UPDATE ' . $xoopsDB->prefix('amx_amxadmins') . " SET username = '$username', password = '$password', access = '$access', flags = '$flags', steamid = '$steamid', nickname = '$nickname' WHERE id = '$id'") or die ($GLOBALS['xoopsDB']->error());
        }
    } elseif ($type == 'remove') {
        $update_admin = $xoopsDB->query('DELETE FROM ' . $xoopsDB->prefix('amx_amxadmins') . " WHERE id = '$id'") or die ($GLOBALS['xoopsDB']->error());
        $remove_admin = $xoopsDB->query('DELETE FROM ' . $xoopsDB->prefix('amx_admins_servers') . " WHERE admin_id = '$id'") or die ($GLOBALS['xoopsDB']->error());
    }
}

function GetWebAdmins()
{
    global $xoopsDB;

    $list_admins = $xoopsDB->query('SELECT * FROM ' . $xoopsDB->prefix('amx_webadmins') . ' ORDER BY id ASC') or die ($GLOBALS['xoopsDB']->error());

    while (false !== ($myadmins = $xoopsDB->fetchArray($list_admins))) {
        $data[] = $myadmins;
    }

    return $data;
}

function GetxoppsUsers()
{
    global $xoopsDB;

    $list_users = $xoopsDB->query('SELECT uid, uname FROM ' . $xoopsDB->prefix('users') . ' ORDER BY uname ASC');

    while (false !== ($xadmins = $xoopsDB->fetchArray($list_users))) {
        $data[] = $xadmins;
    }

    return $data;
}

function GetxoppsGroups()
{
    global $xoopsDB;

    $list_users = $xoopsDB->query('SELECT groupid, name FROM ' . $xoopsDB->prefix('groups') . ' ORDER BY name ASC');

    while (false !== ($xadmins = $xoopsDB->fetchArray($list_users))) {
        $data[] = $xadmins;
    }

    return $data;
}

function GetAMXAdmins()
{
    global $xoopsDB;

    $list_admins = $xoopsDB->query('SELECT * FROM ' . $xoopsDB->prefix('amx_amxadmins') . ' ORDER BY id ASC') or die ($GLOBALS['xoopsDB']->error());

    while (false !== ($myadmins = $xoopsDB->fetchArray($list_admins))) {
        $data[] = $myadmins;
    }

    return $data;
}

function GetAMXAdminsUnique()
{
    global $xoopsDB;

    $list_admins = $xoopsDB->query('SELECT DISTINCT steamid, nickname FROM ' . $xoopsDB->prefix('amx_amxadmins') . ' ORDER BY id ASC') or die ($GLOBALS['xoopsDB']->error());

    while (false !== ($myadmins = $xoopsDB->fetchArray($list_admins))) {
        $data[] = $myadmins;
    }

    return $data;
}

function UpdateAdminLevel($type, $level, $bans_add, $bans_edit, $bans_delete, $bans_unban)
{
    global $xoopsDB;

    if ($type == 'insert') {
        $update_level = $xoopsDB->query('INSERT INTO ' . $xoopsDB->prefix('amx_levels') . " VALUES ('$level','$bans_add','$bans_edit','$bans_delete','$bans_unban')");
    } elseif ($type == 'update') {
        $update_level = $xoopsDB->query('UPDATE ' . $xoopsDB->prefix('amx_levels') . " SET bans_add = '$bans_add', bans_edit = '$bans_edit', bans_delete = '$bans_delete', bans_unban = '$bans_unban' WHERE level='$level'");
    } elseif ($type == 'remove') {
        $update_level = $xoopsDB->query('DELETE FROM ' . $xoopsDB->prefix('amx_levels') . " WHERE level = '$level'");
    }
}

function GetAdminLevels()
{
    global $xoopsDB;

    $get_levels = $xoopsDB->query('SELECT level FROM ' . $xoopsDB->prefix('amx_levels') . ' ORDER BY level ASC');
    //$numrows		= $GLOBALS['xoopsDB']->getRowsNum($get_levels);

    //	if ($numrows = 0) {
    //		return 0;
    //	} else {
    while (false !== ($mylevels = $xoopsDB->fetchArray($get_levels))) {
        $data[] = $mylevels['level'];
    }
    return $data;
    //	}
}

function GetLevels()
{
    global $xoopsDB;

    $list_levels = $xoopsDB->query('SELECT * FROM ' . $xoopsDB->prefix('amx_levels') . ' ORDER BY level ASC');
    //	$numrows			= $GLOBALS['xoopsDB']->getRowsNum($list_levels);

    //	if ($numrows == 0) {
    //		return 0;
    //		exit();
    //	}

    while (false !== ($myusers = $xoopsDB->fetchArray($list_levels))) {
        $data[] = $myusers;
    }

    return $data;
}

function PruneBans()
{
    global $xoopsDB;
    global $xoopsUser;

    $list_exbans = $xoopsDB->query('SELECT * FROM ' . $xoopsDB->prefix('amx_bans') . ' WHERE ban_created + ban_length*60 < UNIX_TIMESTAMP() AND ban_length != 0');

    $unban_created = date('U');
    $counter       = 0;

    while (false !== ($myexbans = $xoopsDB->fetchArray($list_exbans))) {
        $cp_exbans = $xoopsDB->query(
            'INSERT INTO '
            . $xoopsDB->prefix('amx_banshistory')
            . " VALUES ('','$myexbans[player_ip]','$myexbans[player_id]','$myexbans[player_nick]','$myexbans[admin_ip]','$myexbans[admin_id]','$myexbans[admin_nick]','$myexbans[ban_type]','$myexbans[ban_reason]','$myexbans[ban_created]','$myexbans[ban_length]','$myexbans[server_ip]','$myexbans[server_name]','$unban_created','Bantime expired',$xoopsUser->getVar('uname'))"
        );
        $rm_ban    = $xoopsDB->query('DELETE FROM ' . $xoopsDB->prefix('amx_bans') . " WHERE bid = '$myexbans[bid]'");

        $counter++;
    }

    return $counter;
}

function GetBanHistDetails($bhid)
{
    global $xoopsDB;

    $list_prevban = $xoopsDB->query("SELECT *, FROM_UNIXTIME(ban_created,'%d/%m/%Y %H:%i:%s') AS fban_created, FROM_UNIXTIME(unban_created,'%d/%m/%Y %H:%i:%s') AS funban_created FROM " . $xoopsDB->prefix('amx_banhistory') . " WHERE bhid = '$bhid'");

    while (false !== ($myprevban = $xoopsDB->fetchArray($list_prevban))) {
        $get_orgadmin = $xoopsDB->query('SELECT steamid, username FROM ' . $xoopsDB->prefix('amx_amxadmins') . " WHERE steamid = '$myprevban[admin_id]'") or die ($GLOBALS['xoopsDB']->error());

        while (false !== ($my_orgadmin = $xoopsDB->fetchArray($get_orgadmin))) {
            $orgadmin = (string)$my_orgadmin[auth];
        }

        $this_bhid = [
            'ban_id'           => (string)$myban[bhid],
            'player_nick'      => (string)$myprevban[player_nick],
            'player_steamid'   => (string)$myprevban[player_id],
            'player_ip'        => (string)$myprevban[player_ip],
            'ban_start'        => (string)$myprevban[fban_created],
            'ban_start_tstamp' => (string)$myprevban[ban_created],
            'ban_length'       => (string)$myprevban[ban_length],
            'ban_type'         => (string)$myprevban[ban_type],
            'ban_reason'       => (string)$myprevban[ban_reason],
            'admin_nick'       => (string)$myprevban[admin_nick],
            'admin_org'        => (string)$orgadmin,
            'server_name'      => (string)$myprevban[server_name],
            'unban_admin'      => (string)$myprevban[unban_admin_nick],
            'unban_reason'     => (string)$myprevban[unban_reason],
            'unban_start'      => (string)$myprevban[funban_created],
            'unban_tstamp'     => (string)$myprevban[unban_created],
        ];
    }

    return $this_bhid;
}

function ExportBans($bantype, $gametype)
{
    global $xoopsDB;

    if ($gametype != 'all') {
        $table = $xoopsDB->prefix('amx_bans') . ', ' . $xoopsDB->prefix('amx_serverinfo');
    } else {
        $table = $xoopsDB->prefix('amx_bans');
    }

    if ($bantype == 'temp') {
        $list_exportbans = "SELECT player_id FROM $table WHERE (ban_length != '0' AND ban_length != '')";
    } elseif ($bantype == 'both') {
        $list_exportbans = "SELECT player_id FROM $table WHERE 1";
    } else {
        $list_exportbans = "SELECT player_id FROM $table WHERE ban_length = '0' OR ban_length = ''";
    }

    if ($gametype != 'all') {
        $gt = GetGameTypes();

        foreach ($gt as $row) {
            if ($gametype == (string)$row) {
                $list_exportbans .= ' AND ' . $xoopsDB->prefix('amx_bans') . '.server_ip = ' . $xoopsDB->prefix('amx_bans') . '.address AND ' . $xoopsDB->prefix('amx_bans') . ".gametype = '$row'";
            }
        }
    }

    $list_exportbans .= ' ORDER BY ' . $xoopsDB->prefix('amx_bans') . '.player_id ASC';
    $list_exportbans_result = $xoopsDB->query($list_exportbans) or die ('Error fetching bans for export...');
    //$numrows								= $GLOBALS['xoopsDB']->getRowsNum($list_exportbans_result);

    //if ($numrows == 0) {
    //	return 0;
    //	exit();
    //}

    while (false !== ($myexportbans = $xoopsDB->fetchArray($list_exportbans_result))) {
        $data[] = $myexportbans['player_id'];
    }

    return $data;
}

function GetGameTypes()
{
    global $xoopsDB;

    $get_gametypes = $xoopsDB->query('SELECT DISTINCT gametype FROM ' . $xoopsDB->prefix('amx_serverinfo') . ' ');
    //$numrows				= $GLOBALS['xoopsDB']->getRowsNum($get_gametypes);

    //	if ($numrows == 0) {
    //		return 0;
    //		exit();
    //	}

    while (false !== ($mygametypes = $xoopsDB->fetchArray($get_gametypes))) {
        $data[] = $mygametypes['gametype'];
    }

    return $data;
}

function ApplyMOTDDetails($address, $delay, $motd)
{
    global $xoopsDB;

    $set_motd = $xoopsDB->query('UPDATE ' . $xoopsDB->prefix('amx_serverinfo') . " SET motd_delay = '$delay', amxban_motd = '$motd' WHERE address = '$address'") or die ($GLOBALS['xoopsDB']->error());
}

function GetMOTDDetails($address)
{
    global $xoopsDB;

    $get_motd = $xoopsDB->query('SELECT amxban_motd, motd_delay FROM ' . $xoopsDB->prefix('amx_serverinfo') . " WHERE address = '$address'") or die ($GLOBALS['xoopsDB']->error());
    //$numrows	= $GLOBALS['xoopsDB']->getRowsNum($get_motd);

    //if ($numrows == 0) {
    //	return 0;
    //	exit();
    //}

    while (false !== ($mymotd = $xoopsDB->fetchArray($get_motd))) {
        $data['motd']  = $mymotd[amxban_motd];
        $data['delay'] = $mymotd[motd_delay];
    }

    return $data;
}

function RemoveServer($address)
{
    global $xoopsDB;

    $get_server_id = $xoopsDB->query('SELECT id FROM ' . $xoopsDB->prefix('amx_serverinfo') . " WHERE address = '$address'") or die ($GLOBALS['xoopsDB']->error());

    while (false !== ($myserver_id = $xoopsDB->fetchArray($get_server_id))) {
        $delete_svr_record = $xoopsDB->query('DELETE FROM ' . $xoopsDB->prefix('amx_admins_servers') . " WHERE server_id = '$myserver_id[id]'") or die ($GLOBALS['xoopsDB']->error());
        $delete_server = $xoopsDB->query('DELETE FROM ' . $xoopsDB->prefix('amx_serverinfo') . " WHERE id = '$myserver_id[id]'") or die ($GLOBALS['xoopsDB']->error());
    }
}

function SetRCON($rcon, $address)
{
    global $xoopsDB;

    if ($rcon == '') {
        $set_rcon = $xoopsDB->query('UPDATE ' . $xoopsDB->prefix('amx_serverinfo') . " SET rcon = NULL WHERE address = '$address'");
    } else {
        $set_rcon = $xoopsDB->query('UPDATE ' . $xoopsDB->prefix('amx_serverinfo') . " SET rcon = '$rcon' WHERE address = '$address'");
    }
}

function GetServers()
{
    global $xoopsDB;

    $list_servers = $xoopsDB->query("SELECT *, FROM_UNIXTIME(timestamp,'%d/%m/%Y %H:%i:%s') AS time FROM " . $xoopsDB->prefix('amx_serverinfo') . ' ORDER BY hostname ASC');
    $numrows      = $xoopsDB->getRowsNum($list_servers);

    //	if ($numrows == 0) {
    //		return 0;
    //		exit();
    //	}

    while (false !== ($myservers = $xoopsDB->fetchArray($list_servers))) {
        $data[] = $myservers;
    }

    return $data;
}

function AddAdmin2Server($admin_id, $server_id)
{
    global $xoopsDB;

    $check_link = $xoopsDB->query('SELECT * FROM ' . $xoopsDB->prefix('amx_admins_servers') . " WHERE admin_id = '$admin_id' AND server_id = '$server_id'");
    $numrows    = $xoopsDB->getRowsNum($check_link);

    if ($numrows == 0) {
        $insert_link = $xoopsDB->query('INSERT INTO ' . $xoopsDB->prefix('amx_admins_servers') . " (admin_id, server_id) VALUES ('$admin_id', '$server_id')");
        return 1;
    }

    return 0;
}

function RemoveAdminFromServer($admin_id, $server_id)
{
    global $xoopsDB;
    $check_link = $xoopsDB->query('SELECT * FROM ' . $xoopsDB->prefix('amx_admins_servers') . " WHERE admin_id = '$admin_id' AND server_id = '$server_id'");
    $numrows    = $xoopsDB->getRowsNum($check_link);

    if ($numrows != 0) {
        $insert_link = $xoopsDB->query('DELETE FROM ' . $xoopsDB->prefix('amx_admins_servers') . " WHERE admin_id = '$admin_id' AND server_id = '$server_id'");
    }

    return $numrows;
}

function AdminServerMatch($admin_id, $server_id)
{
    global $xoopsDB;

    $resultCount = $xoopsDB->query('SELECT COUNT(*) FROM ' . $xoopsDB->prefix('amx_admins_servers') . " WHERE admin_id = '$admin_id' AND server_id = '$server_id'");
    [$numrows] = $xoopsDB->fetchRow($resultCount);

    if ($numrows == 0) {
        return 0;
    } else {
        return 1;
    }
}

function GetServerAdmins()
{
    global $xoopsDB;

    $list_admins = $xoopsDB->query('SELECT * FROM ' . $xoopsDB->prefix('amx_amxadmins') . " WHERE username IS NOT NULL AND username != '' ORDER BY id ASC");

    while (false !== ($myadmins = $xoopsDB->fetchArray($list_admins))) {
        $data[] = $myadmins;
    }

    return $data;
}

function GetServerInfo()
{
    global $xoopsDB;

    $list_servers = $xoopsDB->query('SELECT id, hostname FROM ' . $xoopsDB->prefix('amx_serverinfo'));

    $counter = 0;
    while (false !== ($myservers = $xoopsDB->fetchArray($list_servers))) {
        $data[$counter]['id']       = $myservers['id'];
        $data[$counter]['hostname'] = $myservers['hostname'];
        $counter++;
    }

    return $data;
}

function GetPrevBans($steamid)
{
    global $xoopsDB;

    $list_banhist = $xoopsDB->query(
        "SELECT bhid, player_nick, admin_nick, FROM_UNIXTIME(ban_created,'%d/%m/%Y %H:%i:%s') AS fban_created, ban_reason, server_name FROM " . $xoopsDB->prefix('amx_banhistory') . " WHERE player_id = '$steamid' ORDER BY ban_created DESC"
    );
    $numrows      = $xoopsDB->getRowsNum($list_banhist);
    if ($numrows == 0) {
        return 0;
        exit();
    }

    while (false !== ($mybanhist = $xoopsDB->fetchArray($list_banhist))) {
        $data[] = $mybanhist;
    }

    return $data;
}

function GetBanDetails($bid)
{
    // Returns an array containing all details from a single entry in the bandatabase.
    global $xoopsDB;

    $list_ban = $xoopsDB->query(
        "SELECT *, FROM_UNIXTIME(ban_created,'%d/%m/%Y %H:%i:%s') AS fban_created FROM " . $xoopsDB->prefix('amx_bans') . " WHERE bid = '$bid'"
    );
    $numrows  = $xoopsDB->getRowsNum($list_ban);

    if ($numrows == 0) {
        return 0;
    }

    while (false !== ($myban = $xoopsDB->fetchArray($list_ban))) {
        $get_orgadmin = $xoopsDB->query('SELECT steamid, nickname FROM ' . $xoopsDB->prefix('amx_bans') . " WHERE bid = '$bid'");

        while (false !== ($my_orgadmin = $xoopsDB->fetchArray($get_orgadmin))) {
            $orgadmin = (string)$my_orgadmin[nickname];
        }
        $this_bid = [
            'ban_id'           => (string)$myban[bid],
            'player_nick'      => (string)$myban[player_nick],
            'player_steamid'   => (string)$myban[player_id],
            'player_ip'        => (string)$myban[player_ip],
            'ban_start'        => (string)$myban[fban_created],
            'ban_start_tstamp' => (string)$myban[ban_created],
            'ban_length'       => (string)$myban[ban_length],
            'ban_type'         => (string)$myban[ban_type],
            'ban_reason'       => (string)$myban[ban_reason],
            'admin_nick'       => (string)$myban[admin_nick],
            //					'admin_org'		=> "$orgadmin",
            'server_name'      => (string)$myban[server_name],
        ];
    }
    return $this_bid;
}

function CountBans()
{
    global $xoopsDB;

    $resultCount = $xoopsDB->query('SELECT COUNT(*) FROM ' . $xoopsDB->prefix('amx_bans') . ' ');
    [$numrows] = $xoopsDB->fetchRow($resultCount);
    return $numrows;
}

function GetBans($page, $limit)
{
    global $xoopsDB;
    $list_bans = $xoopsDB->query(
        "SELECT bid, player_nick, admin_nick,ban_reason, FROM_UNIXTIME(ban_created,'%d/%m/%Y %H:%i:%s') AS fban_created, server_ip 
				FROM " . $xoopsDB->prefix('amx_bans') . " ORDER BY ban_created DESC LIMIT $page, $limit"
    );
    while (false !== ($mybans = $xoopsDB->fetchArray($list_bans))) {
        $data[] = $mybans;
    }

    return $data;
}

function GetGametype1($server_ip)
{
    return 'dod';
}

function GetGametype($server_ip)
{
    global $xoopsDB;

    $get_gametype = $xoopsDB->query('SELECT gametype FROM ' . $xoopsDB->prefix('amx_serverinfo') . " WHERE address = '$server_ip'");

    $gametype = 'html';

    while (false !== ($mygametype = $xoopsDB->fetchArray($get_gametype))) {
        $gametype = $mygametype['gametype'];
    }

    return $gametype;
}

function timeleft($begin, $end)
{
    $dif = $end - $begin;

    $years = (int)($dif / (60 * 60 * 24 * 365));
    $dif   -= ($years * (60 * 60 * 24 * 365));

    $months = (int)($dif / (60 * 60 * 24 * 30));
    $dif    -= ($months * (60 * 60 * 24 * 30));

    $weeks = (int)($dif / (60 * 60 * 24 * 7));
    $dif   -= ($weeks * (60 * 60 * 24 * 7));

    $days = (int)($dif / (60 * 60 * 24));
    $dif  -= ($days * (60 * 60 * 24));

    $hours = (int)($dif / (60 * 60));
    $dif   -= ($hours * (60 * 60));

    $minutes = (int)($dif / (60));
    $seconds = $dif - ($minutes * 60);

    $s = '';

    //if ($years<>0) $s.= $years." years ";
    //if ($months<>0) $s.= $months." months ";
    if ($weeks <> 0) {
        $s .= $weeks . 'wks ';
    }
    if ($days <> 0) {
        $s .= $days . 'days ';
    }
    if ($hours <> 0) {
        $s .= $hours . 'hrs ';
    }
    if ($minutes <> 0) {
        $s .= $minutes . 'mins ';
    }
    //if ($seconds<>0) $s.= $seconds."secs ";

    return $s;
}

function MsgUser($header, $msg, $linkname, $url)
{
    ?>

    <table border="1" cellpadding="4" cellspacing="0" width="100%">
        <tr>
            <td colspan="2"><b><?php echo (string)$header; ?></td>
        </tr>

        <tr>
            <td width="30%" align="center"><br><br><?php echo (string)$msg; ?><br><br><br></td>
        </tr>

        <tr>
            <td colspan="2" align="right"><a href="<?php print $url; ?>"><?php print $linkname; ?></a></td>
        </tr>
    </table>

    <?php
}

?>
