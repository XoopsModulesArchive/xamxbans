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

$servers = GetServers();
global $xoopsConfig;

if ($CFG->show_graphs != 'yes') {
    MsgUser('Stats/graphs disabled...', 'This feature has been disabled by the administrator.', 'back', 'javascript:window.history.back()');
    include 'footer.php';
    exit();
} else {
    if (!file_exists('includes/jpgraph/jpgraph.php') || !file_exists('includes/jpgraph/jpgraph_bar.php') || !file_exists('includes/jpgraph/jpgraph_gradient.php') || !file_exists('includes/jpgraph/jpgraph_pie.php') || !file_exists('includes/jpgraph/jpgraph_pie3d.php')
        || !file_exists(
            'includes/jpgraph/jpgraph_plotmark.inc'
        )) {
        MsgUser('Error displaying stats...', 'Some required files are missing. Stats/graphs cannot be displayed.', 'back', 'javascript:window.history.back()');
        include 'footer.php';
        exit();
    }

    $active_bans           = GetNROfActiveBans();
    $expired_bans          = GetNROfExpiredBans();
    $total_bans            = $active_bans + $expired_bans;
    $perm_bans             = GetNROfPermBans();
    $temp_bans             = GetNROfTempBans();
    $imported_active_bans  = GetNROfImportedActBans();
    $imported_expired_bans = GetNROfImportedExpBans();
    $imported_bans         = $imported_active_bans + $imported_expired_bans;
    ?>

    <table border="1" cellpadding="4" cellspacing="0" width="100%">
        <tr>
            <td colspan="999"><b>General statistics for <?= $xoopsConfig['sitename'] ?>...</b></td>
        </tr>

        <tr>
            <td width="30%">Total nr of bans</td>
            <td width="70%"><?= $total_bans ?></td>
        </tr>

        <tr>
            <td width="30%">Active bans</td>
            <td width="70%"><?= $active_bans ?></td>
        </tr>

        <tr>
            <td width="30%">Expired bans</td>
            <td width="70%"><?= $expired_bans ?></td>
        </tr>

        <tr>
            <td width="30%">Permanent bans</td>
            <td width="70%"><?= $perm_bans ?></td>
        </tr>

        <tr>
            <td width="30%">Temporary bans</td>
            <td width="70%"><?= $temp_bans ?></td>
        </tr>

        <tr>
            <td width="30%">Imported bans</td>
            <td width="70%"><?= $imported_bans ?></td>
        </tr>

    </table>

    <br>

    <table border="1" cellpadding="4" cellspacing="0" width="100%">
        <tr>
            <td><b>Bans per admin...</b></b></td>
        </tr>
        <tr>
            <td align="center"><br><img src="<?= $CFG->graphsdir ?>/draw1.php"><br><br></td>
        </tr>
    </table>

    <br>

    <table border="1" cellpadding="4" cellspacing="0" width="100%">
        <tr>
            <td><b>Bans per server (imported bans not included)...</b></b></td>
        </tr>
        <tr>
            <td align="center"><br><img src="<?= $CFG->graphsdir ?>/draw2.php"><br><br></td>
        </tr>
    </table>

    <br>

    <table border="1" cellpadding="4" cellspacing="0" width="100%">
        <tr>
            <td><b>Bans per Gametype (imported bans not included)...</b></b></td>
        </tr>
        <tr>
            <td align="center"><br><img src="<?= $CFG->graphsdir ?>/draw3.php"><br><br></td>
        </tr>
    </table>

    <br>

    <table border="1" cellpadding="4" cellspacing="0" width="100%">
        <tr>
            <td><b>Bans this month...</b></b></td>
        </tr>
        <tr>
            <td align="center"><br><img src="<?= $CFG->graphsdir ?>/draw4.php"><br><br></td>
        </tr>
    </table>

    <br>

    <?php

    if (!($given_month)) {
        if (date('m') == 1) {
            $prev_year   = 'true';
            $given_month = 12;
        } else {
            $given_month = date('m') - 1;
        }
    }

    if (!($given_year)) {
        if ($prev_year == 'true') {
            $given_year = date('Y') - 1;
        } else {
            $given_year = date('Y');
        }
    }

    ?>

    <table border="1" cellpadding="4" cellspacing="0" width="100%">
        <form name="searchadmin" method="post" action="index.php?mode=stats">
            <tr>
                <td><b>Bans for

                        <select name="given_month" style="font: 8pt bold">
                            <option value="1" <?php if ($given_month == 1) {
                                echo 'selected';
                            } ?>>January
                            </option>
                            <option value="2" <?php if ($given_month == 2) {
                                echo 'selected';
                            } ?>>February
                            </option>
                            <option value="3" <?php if ($given_month == 3) {
                                echo 'selected';
                            } ?>>March
                            </option>
                            <option value="4" <?php if ($given_month == 4) {
                                echo 'selected';
                            } ?>>April
                            </option>
                            <option value="5" <?php if ($given_month == 5) {
                                echo 'selected';
                            } ?>>May
                            </option>
                            <option value="6" <?php if ($given_month == 6) {
                                echo 'selected';
                            } ?>>June
                            </option>
                            <option value="7" <?php if ($given_month == 7) {
                                echo 'selected';
                            } ?>>July
                            </option>
                            <option value="8" <?php if ($given_month == 8) {
                                echo 'selected';
                            } ?>>August
                            </option>
                            <option value="9" <?php if ($given_month == 9) {
                                echo 'selected';
                            } ?>>September
                            </option>
                            <option value="10" <?php if ($given_month == 10) {
                                echo 'selected';
                            } ?>>October
                            </option>
                            <option value="11" <?php if ($given_month == 11) {
                                echo 'selected';
                            } ?>>November
                            </option>
                            <option value="12" <?php if ($given_month == 12) {
                                echo 'selected';
                            } ?>>December
                            </option>
                        </select>

                        &nbsp;

                        <select name="given_year" style="font: 8pt bold">
                            <option value="2001" <?php if ($given_year == 2001) {
                                echo 'selected';
                            } ?>>2001
                            </option>
                            <option value="2002" <?php if ($given_year == 2002) {
                                echo 'selected';
                            } ?>>2002
                            </option>
                            <option value="2003" <?php if ($given_year == 2003) {
                                echo 'selected';
                            } ?>>2003
                            </option>
                            <option value="2004" <?php if ($given_year == 2004) {
                                echo 'selected';
                            } ?>>2004
                            </option>
                        </select>

                        &nbsp;

                        <input type="submit" name="months" value=" go " style="font: 8pt bold">

                    </b>

                </td>
            </tr>
        </form>
        <tr>
            <td align="center"><br><img src="<?= $CFG->graphsdir ?>/draw5.php?given_month=<?= $given_month; ?>&given_year=<?= $given_year; ?>"><br><br></td>
        </tr>
    </table>

    <?php
}

?>
