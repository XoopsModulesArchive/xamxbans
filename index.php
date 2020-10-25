<?php
/*
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

////
//// Main
////
define('INCLUDE_PATH', 'includes');

$mode = $_GET['mode'] ?? 'ban_list';

if ('stats' != $mode && 'unban_ban' != $mode && 'add_ban' != $mode && 'edit_ban' != $mode && 'delete_ban' != $mode && 'edit_ban' != $mode && 'ban_list' != $mode && 'search' != $mode && 'ban_details' != $mode) {
    $mode = 'ban_list';
}

include 'header.php';

//this is for the templates that are not done yet.
if ('add_ban' == $mode || 'unban_ban' == $mode || 'ban_list' == $mode || 'ban_details' == $mode || 'edit_ban' == $mode) {
    $GLOBALS['xoopsOption']['template_main'] = $mode . '_template.html';
}

global $xoopsDB;

require XOOPS_ROOT_PATH . '/header.php';

include INCLUDE_PATH . '/mylib.inc.php';
include INCLUDE_PATH . '/config.inc.php';

if (!isset($_SESSION['access_set'])) {
    if ($xoopsUser) {
        check_access($xoopsUser->uid(), $xoopsUser->groups());
    }
}

require INCLUDE_PATH . "/$mode.inc.php";

require __DIR__ . '/footer.php';


	
