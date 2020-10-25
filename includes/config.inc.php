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

class object
{
}

$CFG = new object();

$CFG->update_url = 'http://www.xs4all.nl/~yomama/amxbans';
$CFG->php_version = '2.2';

$result = $xoopsDB->query('SELECT * FROM ' . $xoopsDB->prefix('amx_config'));
$config = $xoopsDB->fetchArray($result);

$CFG->graphsdir = 'graphs';

$CFG->show_reason_in_list = $config['show_reason_in_list'];
$CFG->use_rcon = $config['use_rcon'];
$CFG->logfile = $config['logfile'];
$CFG->use_logfile = $config['use_logfile'];
$CFG->show_graphs = $config['show_graphs'];
$CFG->block_list_rows = $config['block_list_rows'];
$CFG->block_max_name_length = $config['block_max_name_length'];
$CFG->use_xhlstats = $config['use_xhlstats'];
