<?php
/*
 * HLstats - Real-time player and clan rankings and statistics for Half-Life
 * http://sourceforge.net/projects/hlstats/
 *
 * Copyright (C) 2001  Simon Garner
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 */

define('INCLUDE_PATH', 'includes');
require dirname(__DIR__, 3) . '/include/cp_header.php';
include '../includes/config.inc.php';
include '../includes/mylib.inc.php';

xoops_cp_header();
echo '<center><b>xAMXBans Administration</b></center><br>';

$task = $_GET['task'] ?? 'menu';

if ('menu' != $task) {
    include INCLUDE_PATH . "/$task.php";

    echo '<br><center><a href="index.php">Return to Main Menu<a></center>';
} else {
    echo "<table width='100%' border='0' cellspacing='1' class='outer'><tr><td class=\"odd\">"; ?>
    <table border="0" cellpadding="4" cellspacing="1" width="100%">
        <tr class='bg1' align="left">
            <td><span class='fg2'><a href="index.php?task=options">Options</a></span></td>
            <td><span class='fg2'>Change settings for AMXBans</td>
        </tr>
        <tr class='bg1' align='left'>
            <td><span class='fg2'><a href="index.php?task=import_bans">Import</a></span></td>
            <td><span class='fg2'>Import bans into the database</span></td>
        </tr>
        <tr class='bg1' align='left'>
            <td><span class='fg2'><a href="index.php?task=export_bans">Export</a></span></td>
            <td><span class='fg2'>Export bans from database</span></td>
        </tr>
        <tr class='bg1' align='left'>
            <td><span class='fg2'><a href="index.php?task=admin_mgmt">Admins/Levels</a></span></td>
            <td><span class='fg2'>Set Admins & Levels</span></td>
        </tr>
        <tr class='bg1' align='left'>
            <td><span class='fg2'><a href="index.php?task=servr_admins">Server Admins</a></span></td>
            <td><span class='fg2'>Assign admins to game servers</span></td>
        </tr>
        <tr class='bg1' align='left'>
            <td><span class='fg2'><a href="index.php?task=prune_db">Prune DB</a></span></td>
            <td><span class='fg2'>Move expired bans to history</span></td>
        </tr>
        <tr class='bg1' align='left'>
            <td><span class='fg2'><a href="index.php?task=server_mgmt">Server Mgmt</a></span></td>
            <td><span class='fg2'>Add/Edit current game servers</span></td>
        </tr>
    </table>
    </td></tr></table>
    <?php
}

xoops_cp_footer();
?>
