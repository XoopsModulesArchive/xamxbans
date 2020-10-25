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

include '../../../mainfile.php';
define('INCLUDE_PATH', '../includes');

include INCLUDE_PATH . '/mylib.inc.php';
include INCLUDE_PATH . '/config.inc.php';
global $xoopsDB;
global $xoopsConfig;
include '../includes/jpgraph/jpgraph.php';
include '../includes/jpgraph/jpgraph_pie.php';
include '../includes/jpgraph/jpgraph_pie3d.php';

$adminlst = GetServerNames();
$tellertje = 0;
$nrofbanslst = [];

reset($adminlst);

while (list($key, $val) = each($adminlst)) {
    //echo "Calculating ban_amount for $val ...\n";

    $nrofbanslst[$tellertje] = GetBansPerServer($val);

    //echo "$nrofbanslst[$tellertje]<br>\n";

    $tellertje++;
}

$data = $nrofbanslst;
$legends = $adminlst;

//$data = array(15,25,35,65);
//$legends = array("XS4ALL CSTRIKE-01", "XS4ALL CSTRIKE-02", "XS4ALL CSTRIKE-03", "XS4ALL CSTRIKE-04");

$graph = new PieGraph(800, 350, 'auto');
$graph->SetMarginColor('lightblue');
$graph->img->SetAntiAliasing();

$p1 = new PiePlot3D($data);
//$p1->ExplodeSlice(0);
$p1->ExplodeAll();
$p1->SetTheme('pastel');
$p1->SetCenter(0.30);
$p1->SetLegends($legends);

$graph->Add($p1);
$graph->Stroke();
