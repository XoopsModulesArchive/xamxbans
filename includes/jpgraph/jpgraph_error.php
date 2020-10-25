<?php
/*=======================================================================
// File: 	JPGRAPH_ERROR.PHP
// Description:	Error plot extension for JpGraph
// Created: 	2001-01-08
// Author:	Johan Persson (johanp@aditus.nu)
// Ver:		$Id: jpgraph_error.php,v 1.1 2004/08/25 01:12:53 rboyleiii Exp $
//
// License:	This code is released under QPL
// Copyright (C) 2001,2002 Johan Persson
//========================================================================
*/

//===================================================
// CLASS ErrorPlot
// Description: Error plot with min/max value for
// each datapoint
//===================================================
class ErrorPlot extends Plot
{
    public $errwidth = 2;

    //---------------

    // CONSTRUCTOR

    public function __construct(&$datay, $datax = false)
    {
        parent::__construct($datay, $datax);

        $this->numpoints /= 2;
    }

    //---------------

    // PUBLIC METHODS

    // Gets called before any axis are stroked

    public function PreStrokeAdjust($graph)
    {
        if ($this->center) {
            $a = 0.5;

            $b = 0.5;

            ++$this->numpoints;
        } else {
            $a = 0;

            $b = 0;
        }

        $graph->xaxis->scale->ticks->SetXLabelOffset($a);

        $graph->SetTextScaleOff($b);

        //$graph->xaxis->scale->ticks->SupressMinorTickMarks();
    }

    // Method description

    public function Stroke(&$img, &$xscale, &$yscale)
    {
        $numpoints = count($this->coords[0]) / 2;

        $img->SetColor($this->color);

        $img->SetLineWeight($this->weight);

        if (isset($this->coords[1])) {
            if (count($this->coords[1]) != $numpoints) {
                JpGraphError::Raise('Number of X and Y points are not equal. Number of X-points:' . count($this->coords[1]) . " Number of Y-points:$numpoints");
            } else {
                $exist_x = true;
            }
        } else {
            $exist_x = false;
        }

        if ($exist_x) {
            $xs = $this->coords[1][0];
        } else {
            $xs = 0;
        }

        for ($i = 0; $i < $numpoints; ++$i) {
            if ($exist_x) {
                $x = $this->coords[1][$i];
            } else {
                $x = $i;
            }

            $xt = $xscale->Translate($x);

            $yt1 = $yscale->Translate($this->coords[0][$i * 2]);

            $yt2 = $yscale->Translate($this->coords[0][$i * 2 + 1]);

            $img->Line($xt, $yt1, $xt, $yt2);

            $img->Line($xt - $this->errwidth, $yt1, $xt + $this->errwidth, $yt1);

            $img->Line($xt - $this->errwidth, $yt2, $xt + $this->errwidth, $yt2);
        }

        return true;
    }
} // Class

//===================================================
// CLASS ErrorLinePlot
// Description: Combine a line and error plot
// THIS IS A DEPRECATED PLOT TYPE JUST KEPT FOR
// BACKWARD COMPATIBILITY
//===================================================
class ErrorLinePlot extends ErrorPlot
{
    public $line = null;

    //---------------

    // CONSTRUCTOR

    public function __construct(&$datay, $datax = false)
    {
        parent::__construct($datay, $datax);

        // Calculate line coordinates as the average of the error limits

        for ($i = 0, $iMax = count($datay); $i < $iMax; $i += 2) {
            $ly[] = ($datay[$i] + $datay[$i + 1]) / 2;
        }

        $this->line = new LinePlot($ly, $datax);
    }

    //---------------

    // PUBLIC METHODS

    public function Legend($graph)
    {
        if ('' != $this->legend) {
            $graph->legend->Add($this->legend, $this->color);
        }

        $this->line->Legend($graph);
    }

    public function Stroke(&$img, &$xscale, &$yscale)
    {
        parent::Stroke($img, $xscale, $yscale);

        $this->line->Stroke($img, $xscale, $yscale);
    }
} // Class

//===================================================
// CLASS LineErrorPlot
// Description: Combine a line and error plot
//===================================================
class LineErrorPlot extends ErrorPlot
{
    public $line = null;

    //---------------

    // CONSTRUCTOR

    // Data is (val, errdeltamin, errdeltamax)

    public function __construct($datay, $datax = false)
    {
        $ly = [];

        $ey = [];

        $n = count($datay);

        if (0 != $n % 3) {
            JpGraphError::Raise(
                'Error in input data to LineErrorPlot.' . 'Number of data points must be a multiple of 3'
            );
        }

        for ($i = 0, $iMax = count($datay); $i < $iMax; $i += 3) {
            $ly[] = $datay[$i];

            $ey[] = $datay[$i] + $datay[$i + 1];

            $ey[] = $datay[$i] + $datay[$i + 2];
        }

        parent::__construct($ey, $datax);

        $this->line = new LinePlot($ly, $datax);
    }

    //---------------

    // PUBLIC METHODS

    public function Legend($graph)
    {
        if ('' != $this->legend) {
            $graph->legend->Add($this->legend, $this->color);
        }

        $this->line->Legend($graph);
    }

    public function Stroke(&$img, &$xscale, &$yscale)
    {
        parent::Stroke($img, $xscale, $yscale);

        $this->line->Stroke($img, $xscale, $yscale);
    }
} // Class

/* EOF */
