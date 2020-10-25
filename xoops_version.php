<?php
/************************************************************************/

/* This program is free software; you can redistribute it and/or modify */
/* it under the terms of the GNU General Public License as published by */
/* the Free Software Foundation; either version 2 of the License.       */
/*                                                                      */
/* This program is distributed in the hope that it will be useful, but  */
/* WITHOUT ANY WARRANTY; without even the implied warranty of           */
/* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU     */
/* General Public License for more details.                             */
/*                                                                      */
/* You should have received a copy of the GNU General Public License    */
/* along with this program; if not, write to the Free Software          */
/* Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307  */
/* USA                                                                  */
/************************************************************************/

$modversion['name'] = 'xAMXBans';
$modversion['version'] = '2.2';
$modversion['description'] = 'AMXBans for Xoops';
$modversion['author'] = "<a href='mailto:rboyleiii@hotmail.com'>Richard Boyle III aka RB3 aka rboyleiii</a><br>Original version by <a href='http://www.xs4all.nl/~yomama/amxbans/'>YoMama/LuX<a>";
$modversion['credits'] = "Based on AMXBans v2.2 Beta.<br>Ported to Xoops2 with enhancements/fixes by RB3<br>Xoops2 port maintained by <a href='mailto:admin@csmapcentral.com'>Jon Langevin aka bd_csmc aka intel352</a> & RB3.";
$modversion['help'] = '';
$modversion['license'] = 'GNU General Public License (GPL)';
$modversion['official'] = 0;
$modversion['image'] = 'images/amxbans_slogo.png';
$modversion['dirname'] = 'xamxbans';

// Sql file (must contain sql generated by phpMyAdmin or phpPgAdmin)
// All tables should not have any prefix!
$modversion['sqlfile']['mysql'] = 'sql/xamxbans.sql';
$modversion['onInstall'] = 'motd_script.php';

// Tables created by sql file (without prefix!)
$modversion['tables'][0] = 'amx_bans';
$modversion['tables'][1] = 'amx_banhistory';
$modversion['tables'][2] = 'amx_amxadmins';
$modversion['tables'][3] = 'amx_admins_servers';
$modversion['tables'][4] = 'amx_levels';
$modversion['tables'][5] = 'amx_serverinfo';
$modversion['tables'][6] = 'amx_webadmins';
$modversion['tables'][7] = 'amx_config';

$modversion['hasAdmin'] = 1;
$modversion['adminindex'] = 'admin/index.php';
$modversion['adminmenu'] = 'admin/menu.php';

// Templates
$modversion['templates'][1]['file'] = 'ban_list_template.html';
$modversion['templates'][1]['description'] = '';
$modversion['templates'][2]['file'] = 'ban_details_template.html';
$modversion['templates'][2]['description'] = '';
$modversion['templates'][3]['file'] = 'edit_ban_template.html';
$modversion['templates'][3]['description'] = '';
$modversion['templates'][4]['file'] = 'msguser_template.html';
$modversion['templates'][4]['description'] = '';
$modversion['templates'][5]['file'] = 'delete_ban_template.html';
$modversion['templates'][5]['description'] = '';
$modversion['templates'][6]['file'] = 'unban_ban_template.html';
$modversion['templates'][6]['description'] = '';
$modversion['templates'][7]['file'] = 'add_ban_template.html';
$modversion['templates'][7]['description'] = '';

//Blocks
$modversion['blocks'][1]['file'] = 'xamxbans_block.php';
$modversion['blocks'][1]['name'] = 'Recent Bans';
$modversion['blocks'][1]['description'] = 'Shows most recent bans';
$modversion['blocks'][1]['show_func'] = 'block_xamsbans_show';
$modversion['blocks'][1]['template'] = 'xamxbans_block.html'; // Blocks

// Menu

$count = 1;
$modversion['hasMain'] = 1;

include 'includes/access.inc.php';
$access = check_access();

if ('yes' == $access['bans_add']) {
    $modversion['sub'][$count]['name'] = 'Add Ban';

    $modversion['sub'][$count]['url'] = 'index.php?mode=add_ban';

    $count++;
}

$modversion['sub'][$count]['name'] = 'Search';
$modversion['sub'][$count]['url'] = 'index.php?mode=search';
$count++;
$modversion['sub'][$count]['name'] = 'Stats';
$modversion['sub'][$count]['url'] = 'index.php?mode=stats';
