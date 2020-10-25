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

if (isset($_REQUEST['searchbysteamid'])) {
    $steam_id = $_REQUEST['steamid'];

    $ban_details = SearchBySteamid2($steam_id);

    if (0 == $ban_details) {
        include (string)$CFG->header;

        MsgUser('Query results...', "No bans found matching SteamID $steam_id.", 'back', 'javascript:window.history.back()');

        include 'footer.php';

        exit();
    }  

    header("Location: index.php?mode=ban_details&bid=$ban_details");
}

?>

<table border="1" cellpadding="4" cellspacing="0" width="100%">
    <tr>
        <td colspan="3"><b>Search Options...</b></td>
    </tr>

    <form name="searchsteamid" method="post" action="index.php?mode=search" method="post" onSubmit="return check_input();">
        <tr>
            <td width="30%">SteamID:</td>
            <td>

                <table border="0" cellpadding="0" cellspacing="0" width="100%">
                    <tr>
                        <td><input type="text" name="steamid" style="font: 8pt bold" style="font: 8pt bold; width: 200px"></td>
                        <td align="right">also search expired bans <input type="checkbox" name="expired" disabled></td>
                    </tr>
                </table>

            </td>
            <td align="right"><input type="submit" name="searchbysteamid" value=" search " style="font: 8pt bold"></td>
        </tr>
    </form>

    <script language="JavaScript" src="calendar1.js"></script>
    <form name="searchdate" method="post" action="index.php?mode=search">
        <tr>
            <td width="30%">Date:</td>
            <td>

                <table border="0" cellpadding="0" cellspacing="0" width="100%">
                    <tr>
                        <td><input type="text" name="date" value="<?= date('d-m-Y'); ?>" style="font: 8pt bold; width: 200px">&nbsp;<script language="JavaScript" src="calendar1.js"></script>
                            <a href="javascript:cal1.popup();"><img src="images/cal.gif" width="16" height="16" border="0" alt="Pick a date"></a></td>
                        <td align="right">also search expired bans <input type="checkbox" name="expired" disabled></td>
                    </tr>
                </table>

            </td>
            <td align="right"><input type="submit" name="searchbydate" value=" search " style="font: 8pt bold"></td>
        </tr>
    </form>
    <script language="JavaScript">
        <!--
        var cal1 = new calendar1(document.forms['searchdate'].elements['date']);
        cal1.year_scroll = true;
        cal1.time_comp = false;
        -->
    </script>

    <?php

    //if($CFG->admin_management == "yes") {

    ?>

    <form name="searchadmin" method="post" action="index.php?mode=search">
        <tr>
            <td width="30%">In Game Admin:</td>
            <td>

                <table border="0" cellpadding="0" cellspacing="0" width="100%">
                    <tr>
                        <td>

                            <select name="admin" style="font: 8pt bold; width: 200px">
                                <option value="XXX">Select an admin...</option>

                                <?php

                                $admins = GetAMXAdminsUnique();

                                foreach ($admins as $row) {
                                    echo '<option value="' . $row['steamid'] . '">' . $row['nickname'] . "</option>\n";
                                }

                                ?>

                            </select>

                        </td>
                        <td align="right">also search expired bans <input type="checkbox" name="expired" disabled></td>
                    </tr>
                </table>

            </td>
            <td align="right"><input type="submit" name="searchbyadmin" value=" search " style="font: 8pt bold"></td>
        </tr>
    </form>

    <?php

    //}

    ?>


    <form name="searchserver" method="post" action="index.php?mode=search">
        <tr>
            <td width="30%">Server:</td>
            <td>

                <table border="0" cellpadding="0" cellspacing="0" width="100%">
                    <tr>
                        <td>

                            <select name="server" style="font: 8pt bold; width: 200px">
                                <option value="XXX">Select a server...</option>

                                <?php

                                $servers = GetServers();

                                foreach ($servers as $row) {
                                    echo '<option value="' . $row['hostname'] . '">' . $row['hostname'] . "</option>\n";
                                }

                                ?>

                            </select>


                        </td>
                        <td align="right">also search expired bans <input type="checkbox" name="expired" disabled></td>
                    </tr>
                </table>

            </td>
            <td align="right"><input type="submit" name="searchbyserver" value=" search " style="font: 8pt bold"></td>
        </tr>
    </form>
</table>

<?php

if (isset($_REQUEST['searchbydate'])) {
    ?>

<br>

<table border="1" cellpadding="4" cellspacing="0" width="100%">
    <tr>
        <td colspan="4"><b>Displaying bans from <?= $_REQUEST['date'] ?>...</b></td>
    </tr>

    <tr>
        <td width="1%">&nbsp;</td>
        <td width="32%"><i>Date</i></td>
        <td width="23%"><i>Admin</i></td>
        <td width="44%"><i>Player</i></td>
    </tr>

    <?php

    $matchedbans = SearchByDate($_REQUEST['date']);

    if (0 == $matchedbans) {
        echo '<tr ><td colspan="4">No matches found for this query...</td></tr>';

        echo '</table>';

        include 'footer.php';

        exit();
    }

    foreach ($matchedbans as $row) {
        echo "<tr class=\"odd\" style=\"CURSOR:hand;\" onClick=\"document.location = 'index.php?mode=ban_details&bid=" . $row['bid'] . "';\" onMouseOver=\"this.className='even'\" onMouseOut=\"this.className='odd'\">\n";

        $svr_name = $row['server_name'];

        if ('DO' == mb_substr((string)$svr_name, 7, 2)) {
            echo "<td><img src=\"images/dod.gif\" alt=\"Day of Defeat\"></td>\n";
        } elseif ('NS' == mb_substr((string)$svr_name, 7, 2)) {
            echo "<td><img src=\"images/ns.gif\" alt=\"Natural Selection\"></td>\n";
        } elseif ('CS' == mb_substr((string)$svr_name, 7, 2)) {
            echo "<td><img src=\"images/cstrike.gif\" alt=\"Counter-Strike\"></td>\n";
        } else {
            echo "<td><img src=\"images/html.gif\" alt=\"Website\"></td>\n";
        }

        echo '<td>' . $row['fban_created'] . "</td>\n";

        echo '<td>' . $row['admin_nick'] . "</td>\n";

        echo '<td>' . $row['player_nick'] . "</td>\n";

        echo "</tr>\n";
    }

    echo "</table>\n";
} elseif (isset($_REQUEST['searchbysteamid'])) {
    ?>

        <br>

        <?php

        //$ban_details	= SearchBySteamID($_REQUEST['steamid']);
        $ban_details = SearchBySteamid2($_REQUEST['steamid']);

    if (0 == $ban_details) {
        echo 'No matches found for this query...';

        echo '</table>';

        include 'footer.php';

        exit();
    }

    /*
        $banhist = GetPrevBans($_REQUEST['steamid']);

        echo "<table border=\"1\" cellpadding=\"4\" cellspacing=\"0\" width=\"100%\">\n";
        echo "<tr>\n";
        echo "<td colspan=\"3\"><b>Displaying bandetails for Player with SteamID ".$_REQUEST['steamid']."...</b></td>\n";
        echo "</tr>\n";

        echo "<tr><td width=\"30%\" >Nickname: </td><td >$ban_details[player_nick]</td></tr>\n";
        echo "<tr><td width=\"30%\" >SteamID: </td><td >$ban_details[player_steamid]</td></tr>\n";
        echo "<tr><td width=\"30%\" >Effective per:</td><td >$ban_details[ban_start]</td></tr>\n";

        $date_and_ban		= $ban_details[ban_start_tstamp] + ($ban_details[ban_length] * 60);
        $expire_date		= getdate("$date_and_ban");
        $now						= date("U");
        $timetester			= $date_and_ban - $now;
        $timeleft				= timeleft($now,$date_and_ban);

        // since getdate() sucks, we need to unsuck it
        $month					= $expire_date[mon];
        $day						= $expire_date[mday];
        $hours					= $expire_date[hours];
        $minutes				= $expire_date[minutes];
        $seconds				= $expire_date[seconds];

        if (strlen("$month") == 1) {
            $month = "0".$month;
        }

        if (strlen("$day") == 1) {
            $day = "0".$day;
        }

        if (strlen("$hours") == 1) {
            $hours = "0".$hours;
        }

        if (strlen("$minutes") == 1) {
            $minutes = "0".$minutes;
        }

        if (strlen("$seconds") == 1) {
            $seconds = "0".$seconds;
        }

        if ($ban_details[ban_length] == 0) {
            echo "<tr><td width=\"30%\" >Ban Length: </td><td >Permanent</td></tr>\n";
        } else {
            echo "<tr><td width=\"30%\" >Ban Length: </td><td >$ban_details[ban_length] minutes</td></tr>\n";
            if ($date_and_ban > $now) {
                echo "<tr><td width=\"30%\" >Expires on: </td><td >$day/$month/$expire_date[year] $hours:$minutes:$seconds ($timeleft remaining)<br>\n";
            } else {
                echo "<tr><td width=\"30%\" >Expired on: </td><td >$day/$month/$expire_date[year] $hours:$minutes:$seconds (allready expired)<br>\n";
            }
        }

        if ($ban_details[ban_type] == "S") {
            echo "<tr><td width=\"30%\" >Bantype: </td><td >SteamID";
        } else if ($ban_details[ban_type] == "I") {

            if ($CFG->view_ip == "yes") {
                echo "<tr><td width=\"30%\" >IP address: </td><td >$ban_details[player_ip]</td></tr>\n";
            } else {

                if (IsLoggedIn() && GotAtleastPrivilege(3)) {
                    echo "<tr><td width=\"30%\" >IP address: </td><td >$ban_details[player_ip]</td></tr>\n";
                } else {
                    echo "<tr><td width=\"30%\" >IP address: </td><td >&lt;hidden&gt;</td></tr>\n";
                }
            }

            echo "<tr><td width=\"30%\" >Bantype: </td><td >IP";
        } else {

            if ($CFG->view_ip == "yes") {
                echo "<tr><td width=\"30%\" >IP address: </td><td >$ban_details[player_ip]</td></tr>\n";
            } else {

                if (IsLoggedIn() && GotAtleastPrivilege(3)) {
                    echo "<tr><td width=\"30%\" >IP address: </td><td >$ban_details[player_ip]</td></tr>\n";
                } else {
                    echo "<tr><td width=\"30%\" >IP address: </td><td >&lt;hidden&gt;</td></tr>\n";
                }
            }

            echo "<tr><td width=\"30%\" >Bantype: </td><td >SteamID + IP";
        }

        echo "<tr><td width=\"30%\" >Reason: </td><td >$ban_details[ban_reason]</td></tr>\n";

        if ($ban_details[admin_org] != $ban_details[admin_nick]) {
            echo "<tr><td width=\"30%\" >Banned by:</td><td >$ban_details[admin_nick] ($ban_details[admin_org])</td></tr>\n";
        } else {
            echo "<tr><td width=\"30%\" >Banned by:</td><td >$ban_details[admin_nick]</td></tr>\n";
        }

        echo "<tr><td width=\"30%\" >Ban invoked on:</td><td >$ban_details[server_name]</td></tr>\n";

        echo "<tr>";

        echo "<td colspan=\"2\" align=\"right\"><a href=\"javascript:window.history.back()\">back to search</a></td>";


        echo "</tr>";
        echo "</table>";

        ?>

        <br>

                <table border="1" cellpadding="4" cellspacing="0" width="100%">
                    <tr>
                        <td colspan="4"><b>Viewing previous bans for this user...</b></td>
                    </tr>

                    <tr>
                        <td width="1%">&nbsp;</td>
                        <td width="32%"><i>Date</i></td>
                        <td width="23%"><i>Admin</i></td>
                        <td width="44%"><i>Reason</i></td>
                    </tr>

        <?php

        if ($banhist == 0) {
            echo "<tr ><td colspan=\"4\">No previous bans found for this player...</td></tr>";
            echo "</table>";
            include "footer.php";
            exit();
        }

        foreach($banhist as $row) {
            echo "<tr class="odd" style=\"CURSOR:hand;\" onClick=\"document.location = 'index.php?mode=search&bhid=".$row["bhid"]."';\" onMouseOver=\"this.className='even'\" onMouseOut=\"this.className='odd'\">\n";

            $svr_name = $row["server_name"];

            if (substr("$svr_name",7,2) == "DO") {
                echo "<td><img src=\"images/dod.gif\" alt=\"".$row["server_name"]."\"></td>\n";
            } else if (substr("$svr_name",7,2) == "NS") {
                echo "<td><img src=\"images/ns.gif\" alt=\"".$row["server_name"]."\"></td>\n";
            } else if (substr("$svr_name",7,2) == "CS") {
                echo "<td><img src=\"images/cstrike.gif\" alt=\"".$row["server_name"]."\"></td>\n";
            } else {
                echo "<td><img src=\"images/html.gif\" alt=\"Website\"></td>\n";
            }

            echo "<td>".$row["fban_created"]."</td>\n";
            echo "<td>".$row["admin_nick"]."</td>\n";
            echo "<td>".$row["ban_reason"]."</td>\n";
            echo "</tr>\n";
        }

    echo "</table>";
    */
} elseif (isset($_REQUEST['searchbyadmin'])) {
    if ('XXX' == $admin) {
        echo "<br><br>Try actually selecting an admin before pressing 'search'";

        include 'footer.php';

        exit();
    } ?>

    <br>

    <table border="1" cellpadding="4" cellspacing="0" width="100%">
        <tr>
            <td colspan="4"><b>Displaying bans...</b></td>
        </tr>

        <tr>
            <td width="1%">&nbsp;</td>
            <td width="32%"><i>Date</i></td>
            <td width="23%"><i>Admin</i></td>
            <td width="44%"><i>Player</i></td>
        </tr>

        <?php

        $matchedbans = SearchByAdmin($_REQUEST['admin']);

    if (0 == $matchedbans) {
        echo '<tr ><td colspan="4">No matches found for this query...</td></tr>';

        echo '</table>';

        include 'footer.php';

        exit();
    }

    foreach ($matchedbans as $row) {
        echo "<tr class=\"odd\" style=\"CURSOR:hand;\" onClick=\"document.location = 'index.php?mode=ban_details&bid=" . $row['bid'] . "';\" onMouseOver=\"this.className='even'\" onMouseOut=\"this.className='odd'\">\n";

        $svr_name = $row['server_name'];

        if ('DO' == mb_substr((string)$svr_name, 7, 2)) {
            echo "<td><img src=\"images/dod.gif\" alt=\"Day of Defeat\"></td>\n";
        } elseif ('NS' == mb_substr((string)$svr_name, 7, 2)) {
            echo "<td><img src=\"images/ns.gif\" alt=\"Natural Selection\"></td>\n";
        } elseif ('CS' == mb_substr((string)$svr_name, 7, 2)) {
            echo "<td><img src=\"images/cstrike.gif\" alt=\"Counter-Strike\"></td>\n";
        } else {
            echo "<td><img src=\"images/html.gif\" alt=\"Website\"></td>\n";
        }

        echo '<td>' . $row['fban_created'] . "</td>\n";

        echo '<td>' . $row['admin_nick'] . "</td>\n";

        echo '<td>' . $row['player_nick'] . "</td>\n";

        echo "</tr>\n";
    }

    echo "</table>\n";
} elseif (isset($_REQUEST['searchbyserver'])) {
    if ('XXX' == $server) {
        echo "<br><br>Try actually selecting a server before pressing 'search'";

        include 'footer.php';

        exit();
    } ?>

        <br>

        <table border="1" cellpadding="4" cellspacing="0" width="100%">
            <tr>
                <td colspan="4"><b>Displaying bans made on <?= $_REQUEST['server'] ?>...</b></td>
            </tr>

            <tr>
                <td width="1%">&nbsp;</td>
                <td width="32%"><i>Date</i></td>
                <td width="23%"><i>Admin</i></td>
                <td width="44%"><i>Player</i></td>
            </tr>

            <?php

            $matchedbans = SearchByServer($_REQUEST['server']);

    if (0 == $matchedbans) {
        echo '<tr ><td colspan="4">No matches found for this query...</td></tr>';

        echo '</table>';

        include 'footer.php';

        exit();
    }

    foreach ($matchedbans as $row) {
        echo "<tr class=\"odd\" style=\"CURSOR:hand;\" onClick=\"document.location = 'index.php?mode=ban_details&bid=" . $row['bid'] . "';\" onMouseOver=\"this.className='even'\" onMouseOut=\"this.className='odd'\">\n";

        $svr_name = $row['server_name'];

        if ('DO' == mb_substr((string)$svr_name, 7, 2)) {
            echo "<td><img src=\"images/dod.gif\" alt=\"Day of Defeat\"></td>\n";
        } elseif ('NS' == mb_substr((string)$svr_name, 7, 2)) {
            echo "<td><img src=\"images/ns.gif\" alt=\"Natural Selection\"></td>\n";
        } elseif ('CS' == mb_substr((string)$svr_name, 7, 2)) {
            echo "<td><img src=\"images/cstrike.gif\" alt=\"Counter-Strike\"></td>\n";
        } else {
            echo "<td><img src=\"images/html.gif\" alt=\"Website\"></td>\n";
        }

        echo '<td>' . $row['fban_created'] . "</td>\n";

        echo '<td>' . $row['admin_nick'] . "</td>\n";

        echo '<td>' . $row['player_nick'] . "</td>\n";

        echo "</tr>\n";
    }

    echo "</table>\n";
}

            ?>

