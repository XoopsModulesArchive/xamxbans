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
include '../includes/jpgraph/jpgraph_bar.php';

$daysthismonth = DaysInCurMonth();

$this_month = date('m');
$this_year = date('Y');
$nrofbanslst = GetBansForMonth($this_month, $this_year);

$datax = $daysthismonth;
$datay = $nrofbanslst;

// Size of graph
$width = 800;
$height = 350;

// Set the basic parameters of the graph
$graph = new Graph($width, $height, 'auto');
$graph->SetScale('textlin');

$top = 10;
$bottom = 30;
$left = 60;
$right = 15;
//$graph->Set90AndMargin($left,$right,$top,$bottom);
$graph->SetMarginColor('lightblue');

// Setup X-axis
$graph->xaxis->SetTickLabels($datax);

// Some extra margin looks nicer
$graph->xaxis->SetLabelMargin(5);

// Label align for X-axis
$graph->xaxis->SetLabelAlign('right', 'center');

// Add some grace to y-axis so the bars doesn't go
// all the way to the end of the plot area
$graph->yaxis->scale->SetGrace(10);

// Setup the Y-axis to be displayed in the bottom of the
// graph. We also finetune the exact layout of the title,
// ticks and labels to make them look nice.
$graph->yaxis->SetPos('max');

// First make the labels look right
$graph->yaxis->SetLabelAlign('left', 'top');
$graph->yaxis->SetLabelSide(SIDE_RIGHT);
$graph->xaxis->SetLabelAlign('center', 'top');

// The fix the tick marks
$graph->yaxis->SetTickSide(SIDE_LEFT);

// Finally setup the title
$graph->yaxis->SetTitleSide(SIDE_RIGHT);
$graph->yaxis->SetTitleMargin(25);

// To align the title to the right use :
//$graph->yaxis->SetTitle('Bans per admin','high');
$graph->yaxis->title->Align('right');

$graph->yaxis->title->SetAngle(0);

// Now create a bar pot
$bplot = new BarPlot($datay);
$bplot->SetFillColor('orange');

//You can change the width of the bars if you like
$bplot->SetWidth(0.5);

// We want to display the value of each bar at the top
$bplot->value->Show();
$bplot->value->SetAlign('center', 'bottom');
$bplot->value->SetColor('black', 'darkred');
$bplot->value->SetFormat('%.0f');

//Gradients rule!
$bplot->SetFillGradient('darkgray', 'orange', GRAD_HOR);

// Add the bar to the graph
$graph->Add($bplot);

$graph->Stroke();
