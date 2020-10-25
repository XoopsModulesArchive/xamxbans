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

require '../../mainfile.php';
global $xoopsConfig;

include 'includes/config.inc.php';
include 'includes/mylib.inc.php';

$ban_details = GetBanDetails($_GET[bid]);
$banhist = GetPrevBans((string)$ban_details[player_steamid]);

?>

html>
<head>

    <style type="text/css">

        a:visited {
            color: #ffffff;
            font-weight: bold;
            font-size: 10px;
            text-decoration: none
        }

        a:link {
            color: #ffffff;
            font-weight: bold;
            font-size: 10px;
            font-family: Verdana, Arial, Helvetica, Geneva, Swiss, SunSans-Regular;
            text-decoration: none
        }

        a:hover {
            color: #cc0000;
            font-size: 10px;
            font-family: Verdana, Arial, Helvetica, Geneva, Swiss, SunSans-Regular
        }

        td {
            color: #ffcc00;
            font-size: 11px;
            font-family: Verdana, Arial, Helvetica, Geneva, Swiss, SunSans-Regular
        }

        .big {
            color: #ffcc00;
            font-size: 20px;
            font-family: Verdana, Arial, Helvetica, Geneva, Swiss, SunSans-Regular
        }

        .bold {
            color: #ffcc00;
            font-weight: bold;
            font-size: 11px;
            font-family: Verdana, Arial, Helvetica, Geneva, Swiss, SunSans-Regular
        }
    </style>

</head>

<body bgcolor="#000000">

<table border="0" width="100%" cellpadding="4" cellspacing="0">
    <tr>
        <td align="left" class="big"><b>You have been banned!</b></td>
        <?php
        //<td align="right"><a href="http://games.xs4all.nl"><img src="http://www.xs4all.nl/logo/60mm.gif" border="0"></a></td>
        ?>
        <td></td>
    </tr>

    <tr>
        <td colspan="2"><br>

            You have been banned from all <?= $xoopsConfig['sitename'] ?> servers. Details are listed below.<br>
            For more information on your ban, please visit <a href="<?= $xoopsConfig['xoops_url'] ?>"><?= $xoopsConfig['xoops_url'] ?></a><br>

        </td>
    </tr>

    <tr>
        <td colspan="2"><br>

            <table border="0" width="100%" cellpadding="2" cellspacing="0">
                <tr>
                    <td width="30%" class="bold">Nickname:</td>
                    <td width="70%" class="bold"><?= $ban_details[player_nick] ?></td>
                </tr>

                <tr>
                    <td width="30%" class="bold">SteamID:</td>
                    <td width="70%" class="bold"><?= $ban_details[player_steamid] ?>&nbsp;</td>
                </tr>

                <tr>
                    <td width="30%" class="bold">Effective per:</td>
                    <td width="70%" class="bold"><?= $ban_details[ban_start] ?></td>
                </tr>

                <?php

                $date_and_ban = $ban_details[ban_start_tstamp] + ($ban_details[ban_length] * 60);
                $expire_date = getdate((string)$date_and_ban);
                $now = date('U');
                $timetester = $date_and_ban - $now;
                $timeleft = timeleft($now, $date_and_ban);
                $month = $expire_date[mon];
                $day = $expire_date[mday];
                $hours = $expire_date[hours];
                $minutes = $expire_date[minutes];
                $seconds = $expire_date[seconds];

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

                if (0 == $ban_details[ban_length]) {
                    echo "<tr><td width=\"30%\" class=\"bold\">Ban Length: </td><td width=\"70%\" class=\"bold\">Permanent</td></tr>\n";
                } else {
                    echo "<tr><td width=\"30%\" class=\"bold\">Ban Length: </td><td width=\"70%\" class=\"bold\">$ban_details[ban_length] minutes</td></tr>\n";

                    if ($date_and_ban > $now) {
                        echo "<tr><td width=\"30%\" class=\"bold\">Expires on: </td><td width=\"70%\" class=\"bold\">$day/$month/$expire_date[year] $hours:$minutes:$seconds ($timeleft remaining)</td></tr>\n";
                    } else {
                        echo "<tr><td width=\"30%\" class=\"bold\">Expired on: </td><td width=\"70%\" class=\"bold\">$day/$month/$expire_date[year] $hours:$minutes:$seconds (allready expired)</td></tr>\n";
                    }
                }

                if ('S' == $ban_details[ban_type]) {
                    echo "<tr><td width=\"30%\" class=\"bold\">Bantype: </td><td width=\"70%\" class=\"bold\">SteamID</td></tr>\n";
                } elseif ('I' == $ban_details[ban_type]) {
                    echo "<tr><td width=\"30%\" class=\"bold\">Bantype: </td><td width=\"70%\" class=\"bold\">IP</td></tr>\n";

                    echo "<tr><td width=\"30%\" class=\"bold\">IP address: </td><td width=\"70%\" class=\"bold\">$ban_details[player_ip]</td></tr>\n";
                } else {
                    echo "<tr><td width=\"30%\" class=\"bold\">Bantype: </td><td width=\"70%\" class=\"bold\">SteamID & IP</td></tr>\n";

                    echo "<tr><td width=\"30%\" class=\"bold\">IP address: </td><td width=\"70%\" class=\"bold\">$ban_details[player_ip]</td></tr>\n";
                }

                echo "<tr><td width=\"30%\" class=\"bold\">Reason: </td><td width=\"70%\" class=\"bold\">$ban_details[ban_reason]</td></tr>\n";

                if (($ban_details[admin_org] != $ban_details[admin_nick]) && ('' != $ban_details[admin_org])) {
                    echo "<tr><td width=\"30%\" class=\"bold\">Banned by:</td><td width=\"70%\" class=\"bold\">$ban_details[admin_nick] ($ban_details[admin_org])</td></tr>\n";
                } else {
                    echo "<tr><td width=\"30%\" class=\"bold\">Banned by:</td><td width=\"70%\" class=\"bold\">$ban_details[admin_nick]</td></tr>\n";
                }

                echo "<tr><td width=\"30%\" class=\"bold\">Ban invoked on:</td><td width=\"70%\" class=\"bold\">$ban_details[server_name]</td></tr>\n";

                ?>

                <tr>
                    <td colspan="2" align="right" class="bold"><br>
                        <hr>
                        <a href="http://games.xs4all.nl/amxbans">AMXBans v<?= $CFG->php_version ?></a> by YoMama/LuX
                    </td>
                </tr>
            </table>
