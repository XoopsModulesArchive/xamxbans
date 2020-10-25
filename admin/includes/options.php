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

if (isset($_REQUEST['cancel'])) {
    header('Location: index.php');
}

if (isset($_REQUEST['apply'])) {
    global $xoopsDB;

    $rcon       = $_REQUEST['rcon'];
    $reason     = $_REQUEST['reason'];
    $uselog     = $_REQUEST['uselog'];
    $blockrows  = $_REQUEST['blockrows'];
    $namelength = $_REQUEST['namelength'];
    $showgraphs = $_REQUEST['showgraphs'];
    $logfile    = $_REQUEST['logfile'];

    $usexhlstats = $_REQUEST['use_xhlstats'];

    $res = $xoopsDB->query(
        'UPDATE ' . $xoopsDB->prefix('amx_config') . " 
		SET logfile = '$logfile', use_rcon='$rcon', show_reason_in_list='$reason', use_logfile='$uselog', 
			show_graphs='$showgraphs', block_list_rows='$blockrows', block_max_name_length='$namelength', use_xhlstats='$usexhlstats' 
		WHERE id=1"
    );

    if ($res > 0) {
        echo '<center><b>Settings saved...</b><br><br>';
    } else {
        echo '<center><b>ERROR - Settings NOT saved...</b><br><br>';
    }
    echo '<a href="index.php?task=options">Continue</a></center>';
} else {
    ?>
    <form name="options" method="post" action="index.php?task=options">
        <center><b>Set Options...</b>
            <br><br>
            <table border="1" cellpadding="4" cellspacing="0">

                <tr>
                    <td align="left"><i>Option</i></td>
                    <td align="left"><i>Value</i></td>
                    <td align="left"><i>Description</i></td>
                </tr>
                <tr>
                    <td align="left">Use RCON</td>
                    <td align="left">
                        <select name="rcon" size="1">
                            <option value="yes" <?php if ($CFG->use_rcon == 'yes') {
                                echo 'selected';
                            } ?>>yes
                            </option>
                            <option value="no" <?php if ($CFG->use_rcon == 'no') {
                                echo 'selected';
                            } ?>>no
                            </option>
                        </select>
                    </td>
                    <td align="left">Do you want to enter rcon passwords?</td>
                </tr>
                <tr>
                    <td align="left">Show Reason</td>
                    <td align="left">
                        <select name="reason" size="1">
                            <option value="yes" <?php if ($CFG->show_reason_in_list == 'yes') {
                                echo 'selected';
                            } ?>>yes
                            </option>
                            <option value="no" <?php if ($CFG->show_reason_in_list == 'no') {
                                echo 'selected';
                            } ?>>no
                            </option>
                        </select>
                    </td>
                    <td align="left">Do you want to show ban reason in ban list?</td>
                </tr>
                <tr>
                    <td align="left">Use Logfile</td>
                    <td align="left">
                        <select name="uselog" size="1" disabled>
                            <option value="yes" <?php if ($CFG->use_logfile == 'yes') {
                                echo 'selected';
                            } ?> >yes
                            </option>
                            <option value="no" <?php if ($CFG->use_logfile == 'no') {
                                echo 'selected';
                            } ?> >no
                            </option>
                        </select>
                    </td>
                    <td align="left">Do you want to log ban commands?(NOT FUNCTIONAL)</td>
                </tr>
                <tr>
                    <td align="left">Logfile</td>
                    <td align="left">
                        <input type="text" name="logfile" value="<?= $CFG->logfile; ?>">
                    </td>
                    <td align="left">Directory and file name for logfile.</td>
                </tr>
                <tr>
                    <td align="left">Bans in block</td>
                    <td align="left">
                        <select name="blockrows" size="1">
                            <?php for ($x = 3; $x < 51; $x++) { ?>
                                <option value="<?= $x ?>" <?php if ($CFG->block_list_rows == $x) {
                                    echo 'selected';
                                } ?>><?= $x ?></option>
                            <?php } ?>

                        </select>
                    </td>
                    <td align="left">Number of bans in new bans block</td>
                </tr>
                <tr>
                    <td align="left">Max name length</td>
                    <td align="left">
                        <select name="namelength" size="1">
                            <?php for ($x = 5; $x < 51; $x++) { ?>
                                <option value="<?= $x ?>" <?php if ($CFG->block_max_name_length == $x) {
                                    echo 'selected';
                                } ?>><?= $x ?></option>
                            <?php } ?>

                        </select>
                    </td>
                    <td align="left">Max name length to display in block</td>
                </tr>
                <tr>
                    <td align="left">Show Graphs</td>
                    <td align="left">
                        <select name="showgraphs" size="1">
                            <option value="yes" <?php if ($CFG->show_graphs == 'yes') {
                                echo 'selected';
                            } ?>>yes
                            </option>
                            <option value="no" <?php if ($CFG->show_graphs == 'no') {
                                echo 'selected';
                            } ?>>no
                            </option>
                        </select>
                    </td>
                    <td align="left">Do you want to display graphs in stats?</td>
                </tr>
                <tr>
                    <td align="left">Link to xhlstats</td>
                    <td align="left">
                        <select name="use_xhlstats" size="1">
                            <option value="yes" <?php if ($CFG->use_xhlstats == 'yes') {
                                echo 'selected';
                            } ?>>yes
                            </option>
                            <option value="no" <?php if ($CFG->use_xhlstats == 'no') {
                                echo 'selected';
                            } ?>>no
                            </option>
                        </select>
                    </td>
                    <td align="left">Do you want to display a link in ban to xhlstats?</td>
                </tr>
                <tr>
                    <td colspan="3" valign="bottom">

                        <input type="hidden" name="address" value="<?= $row['address'] ?>">
                        <input type="submit" name="apply" value=" apply " style="font: 8pt bold">
                        <input type="submit" name="cancel" value=" cancel " style="font: 8pt bold">

                    </td>
                </tr>
            </table>
        </center>
    </form>
<?php } ?>
