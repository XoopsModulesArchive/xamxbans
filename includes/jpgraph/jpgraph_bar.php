<?php
/*=======================================================================
// File:	JPGRAPH_BAR.PHP
// Description:	Bar plot extension for JpGraph
// Created: 	2001-01-08
// Author:	Johan Persson (johanp@aditus.nu)
// Ver:		$Id: jpgraph_bar.php,v 1.1 2004/08/25 01:12:53 rboyleiii Exp $
//
// License:	This code is released under QPL
// Copyright (C) 2001,2002,2003 Johan Persson
//========================================================================
*/

require_once __DIR__ . '/jpgraph_plotband.php';

// Pattern for Bars
define('PATTERN_DIAG1', 1);
define('PATTERN_DIAG2', 2);
define('PATTERN_DIAG3', 3);
define('PATTERN_DIAG4', 4);
define('PATTERN_CROSS1', 5);
define('PATTERN_CROSS2', 6);
define('PATTERN_CROSS3', 7);
define('PATTERN_CROSS4', 8);
define('PATTERN_STRIPE1', 9);
define('PATTERN_STRIPE2', 10);

//===================================================
// CLASS BarPlot
// Description: Main code to produce a bar plot
//===================================================
class BarPlot extends Plot
{
    public $width = 0.4; // in percent of major ticks
    public $abswidth = -1; // Width in absolute pixels
    public $fill = false;

    public $fill_color = 'lightblue'; // Default is to fill with light blue
    public $ybase = 0; // Bars start at 0
    public $align = 'center';

    public $grad = false;

    public $grad_style = 1;

    public $grad_fromcolor = [50, 50, 200];

    public $grad_tocolor = [255, 255, 255];

    public $bar_shadow = false;

    public $bar_shadow_color = 'black';

    public $bar_shadow_hsize = 3;

    public $bar_shadow_vsize = 3;

    public $valuepos = 'top';

    public $iPattern = -1;

    public $iPatternDensity = 80;

    public $iPatternColor = 'black';

    //---------------

    // CONSTRUCTOR

    public function __construct($datay, $datax = false)
    {
        parent::__construct($datay, $datax);

        ++$this->numpoints;
    }

    //---------------

    // PUBLIC METHODS

    // Set a drop shadow for the bar (or rather an "up-right" shadow)

    public function SetShadow($color = 'black', $hsize = 3, $vsize = 3, $show = true)
    {
        $this->bar_shadow = $show;

        $this->bar_shadow_color = $color;

        $this->bar_shadow_vsize = $vsize;

        $this->bar_shadow_hsize = $hsize;

        // Adjust the value margin to compensate for shadow

        $this->value->margin += $vsize;
    }

    // DEPRECATED use SetYBase instead

    public function SetYMin($aYStartValue)
    {
        //die("JpGraph Error: Deprecated function SetYMin. Use SetYBase() instead.");

        $this->ybase = $aYStartValue;
    }

    // Specify the base value for the bars

    public function SetYBase($aYStartValue)
    {
        $this->ybase = $aYStartValue;
    }

    public function Legend(&$graph)
    {
        if ($this->grad && '' != $this->legend && !$this->fill) {
            $color = [$this->grad_fromcolor, $this->grad_tocolor, $this->grad_style];

            $graph->legend->Add(
                $this->legend,
                $color,
                '',
                0,
                $this->legendcsimtarget,
                $this->legendcsimalt
            );
        } elseif ($this->fill_color && '' != $this->legend) {
            if (is_array($this->fill_color)) {
                $graph->legend->Add(
                    $this->legend,
                    $this->fill_color[0],
                    '',
                    0,
                    $this->legendcsimtarget,
                    $this->legendcsimalt
                );
            } else {
                $graph->legend->Add(
                    $this->legend,
                    $this->fill_color,
                    '',
                    0,
                    $this->legendcsimtarget,
                    $this->legendcsimalt
                );
            }
        }
    }

    // Gets called before any axis are stroked

    public function PreStrokeAdjust($graph)
    {
        parent::PreStrokeAdjust($graph);

        // If we are using a log Y-scale we want the base to be at the

        // minimum Y-value unless the user have specifically set some other

        // value than the default.

        if ('log' == mb_substr($graph->axtype, -3, 3) && 0 == $this->ybase) {
            $this->ybase = $graph->yaxis->scale->GetMinVal();
        }

        // For a "text" X-axis scale we will adjust the

        // display of the bars a little bit.

        if ('tex' == mb_substr($graph->axtype, 0, 3)) {
            // Position the ticks between the bars

            $graph->xaxis->scale->ticks->SetXLabelOffset(0.5, 0);

            // Center the bars

            if ('center' == $this->align) {
                $graph->SetTextScaleOff(0.5 - $this->width / 2);
            } elseif ('right' == $this->align) {
                $graph->SetTextScaleOff(1 - $this->width);
            }
        } else {
            // We only set an absolute width for linear and int scale

            // for text scale the width will be set to a fraction of

            // the majstep width.

            if (-1 == $this->abswidth) {
                // Not set

                // set width to a visuable sensible default

                $this->abswidth = $graph->img->plotwidth / (2 * count($this->coords[0]));
            }
        }
    }

    public function Min()
    {
        $m = parent::Min();

        if ($m[1] >= $this->ybase) {
            $m[1] = $this->ybase;
        }

        return $m;
    }

    public function Max()
    {
        $m = parent::Max();

        if ($m[1] <= $this->ybase) {
            $m[1] = $this->ybase;
        }

        return $m;
    }

    // Specify width as fractions of the major stepo size

    public function SetWidth($aFractionWidth)
    {
        $this->width = $aFractionWidth;
    }

    // Specify width in absolute pixels. If specified this

    // overrides SetWidth()

    public function SetAbsWidth($aWidth)
    {
        $this->abswidth = $aWidth;
    }

    public function SetAlign($aAlign)
    {
        $this->align = $aAlign;
    }

    public function SetNoFill()
    {
        $this->grad = false;

        $this->fill_color = false;

        $this->fill = false;
    }

    public function SetFillColor($aColor)
    {
        $this->fill = true;

        $this->fill_color = $aColor;
    }

    public function SetFillGradient($from_color, $to_color, $style)
    {
        $this->grad = true;

        $this->grad_fromcolor = $from_color;

        $this->grad_tocolor = $to_color;

        $this->grad_style = $style;
    }

    public function SetValuePos($aPos)
    {
        $this->valuepos = $aPos;
    }

    public function SetPattern($aPattern, $aColor = 'black')
    {
        $this->iPatternColor = $aColor;

        switch ($aPattern) {
            case PATTERN_DIAG1:
                $this->iPattern = 1;
                $this->iPatternDensity = 90;
                break;
            case PATTERN_DIAG2:
                $this->iPattern = 1;
                $this->iPatternDensity = 75;
                break;
            case PATTERN_DIAG3:
                $this->iPattern = 2;
                $this->iPatternDensity = 90;
                break;
            case PATTERN_DIAG4:
                $this->iPattern = 2;
                $this->iPatternDensity = 75;
                break;
            case PATTERN_CROSS1:
                $this->iPattern = 8;
                $this->iPatternDensity = 90;
                break;
            case PATTERN_CROSS2:
                $this->iPattern = 8;
                $this->iPatternDensity = 78;
                break;
            case PATTERN_CROSS3:
                $this->iPattern = 8;
                $this->iPatternDensity = 65;
                break;
            case PATTERN_CROSS4:
                $this->iPattern = 7;
                $this->iPatternDensity = 90;
                break;
            case PATTERN_STRIPE1:
                $this->iPattern = 5;
                $this->iPatternDensity = 90;
                break;
            case PATTERN_STRIPE2:
                $this->iPattern = 5;
                $this->iPatternDensity = 75;
                break;
            default:
                JpGraphError::Raise('Unknown pattern specified in call to BarPlot::SetPattern()');
        }
    }

    public function Stroke(&$img, &$xscale, &$yscale)
    {
        $numpoints = count($this->coords[0]);

        if (isset($this->coords[1])) {
            if (count($this->coords[1]) != $numpoints) {
                die(
                    'JpGraph Error: Number of X and Y points are not equal.<br>
					Number of X-points:' . count($this->coords[1]) . "<br>
					Number of Y-points:$numpoints"
                );
            }  

            $exist_x = true;
        } else {
            $exist_x = false;
        }

        $numbars = count($this->coords[0]);

        // Use GetMinVal() instead of scale[0] directly since in the case

        // of log scale we get a correct value. Log scales will have negative

        // values for values < 1 while still not representing negative numbers.

        if ($yscale->GetMinVal() >= 0) {
            $zp = $yscale->scale_abs[0];
        } else {
            $zp = $yscale->Translate(0);
        }

        if ($this->abswidth > -1) {
            $abswidth = $this->abswidth;
        } else {
            $abswidth = round($this->width * $xscale->scale_factor, 0);
        }

        for ($i = 0; $i < $numbars; $i++) {
            // If value is NULL, or 0 then don't draw a bar at all

            if (null === $this->coords[0][$i]
                || '' === $this->coords[0][$i]
                || 0 === $this->coords[0][$i]) {
                continue;
            }

            if ($exist_x) {
                $x = $this->coords[1][$i];
            } else {
                $x = $i;
            }

            $x = $xscale->Translate($x);

            if (!$xscale->textscale) {
                if ('center' == $this->align) {
                    $x -= $abswidth / 2;
                } elseif ('right' == $this->align) {
                    $x -= $abswidth;
                }
            }

            // Stroke fill color and fill gradient

            $pts = [
                $x,
                $zp,
                $x,
                $yscale->Translate($this->coords[0][$i]),
                $x + $abswidth,
                $yscale->Translate($this->coords[0][$i]),
                $x + $abswidth,
                $zp,
            ];

            if ($this->grad) {
                $grad = new Gradient($img);

                $grad->FilledRectangle(
                    $pts[2],
                    $pts[3],
                    $pts[6],
                    $pts[7],
                    $this->grad_fromcolor,
                    $this->grad_tocolor,
                    $this->grad_style
                );
            } elseif (!empty($this->fill_color)) {
                if (is_array($this->fill_color)) {
                    $img->PushColor($this->fill_color[$i % count($this->fill_color)]);
                } else {
                    $img->PushColor($this->fill_color);
                }

                $img->FilledPolygon($pts);

                $img->PopColor();
            }

            // Remember value of this bar

            $val = $this->coords[0][$i];

            if (!empty($val) && !is_numeric($val)) {
                JpGraphError::Raise('All values for a barplot must be numeric. You have specified value[' . $i . '] == \'' . $val . '\'');
            }

            // Determine the shadow

            if ($this->bar_shadow && 0 != $val) {
                $ssh = $this->bar_shadow_hsize;

                $ssv = $this->bar_shadow_vsize;

                // Create points to create a "upper-right" shadow

                if ($val > 0) {
                    $sp[0] = $pts[6];

                    $sp[1] = $pts[7];

                    $sp[2] = $pts[4];

                    $sp[3] = $pts[5];

                    $sp[4] = $pts[2];

                    $sp[5] = $pts[3];

                    $sp[6] = $pts[2] + $ssh;

                    $sp[7] = $pts[3] - $ssv;

                    $sp[8] = $pts[4] + $ssh;

                    $sp[9] = $pts[5] - $ssv;

                    $sp[10] = $pts[6] + $ssh;

                    $sp[11] = $pts[7] - $ssv;
                } elseif ($val < 0) {
                    $sp[0] = $pts[4];

                    $sp[1] = $pts[5];

                    $sp[2] = $pts[6];

                    $sp[3] = $pts[7];

                    $sp[4] = $pts[0];

                    $sp[5] = $pts[1];

                    $sp[6] = $pts[0] + $ssh;

                    $sp[7] = $pts[1] - $ssv;

                    $sp[8] = $pts[6] + $ssh;

                    $sp[9] = $pts[7] - $ssv;

                    $sp[10] = $pts[4] + $ssh;

                    $sp[11] = $pts[5] - $ssv;
                }

                if (is_array($this->bar_shadow_color)) {
                    $numcolors = count($this->bar_shadow_color);

                    if (0 == $numcolors) {
                        JpGraphError::Raise('You have specified an empty array for shadow colors in the bar plot.');
                    }

                    $img->PushColor($this->bar_shadow_color[$i % $numcolors]);
                } else {
                    $img->PushColor($this->bar_shadow_color);
                }

                $img->FilledPolygon($sp);

                $img->PopColor();
            }

            // Stroke the pattern

            if ($this->iPattern > -1) {
                $f = new RectPatternFactory();

                $prect = $f->Create($this->iPattern, $this->iPatternColor, 1);

                $prect->SetDensity($this->iPatternDensity);

                $prect->SetPos(new Rectangle($pts[2], $pts[3], $pts[4] - $pts[0] + 1, $pts[1] - $pts[3] + 1));

                $prect->Stroke($img);
            }

            // Stroke the outline of the bar

            if (is_array($this->color)) {
                $img->SetColor($this->color[$i % count($this->color)]);
            } else {
                $img->SetColor($this->color);
            }

            $pts[] = $pts[0];

            $pts[] = $pts[1];

            if ($this->weight > 0) {
                $img->SetLineWeight($this->weight);

                $img->Polygon($pts);
            }

            // Determine how to best position the values of the individual bars

            $x = $pts[2] + ($pts[4] - $pts[2]) / 2;

            if ('top' == $this->valuepos) {
                $y = $pts[3];

                if (90 === $img->a) {
                    if ($val < 0) {
                        $this->value->SetAlign('right', 'center');
                    } else {
                        $this->value->SetAlign('left', 'center');
                    }
                }

                $this->value->Stroke($img, $val, $x, $y);
            } elseif ('max' == $this->valuepos) {
                $y = $pts[3];

                if (90 === $img->a) {
                    if ($val < 0) {
                        $this->value->SetAlign('left', 'center');
                    } else {
                        $this->value->SetAlign('right', 'center');
                    }
                } else {
                    $this->value->SetAlign('center', 'top');
                }

                $this->value->SetMargin(-3);

                $this->value->Stroke($img, $val, $x, $y);
            } elseif ('center' == $this->valuepos) {
                $y = ($pts[3] + $pts[1]) / 2;

                $this->value->SetAlign('center', 'center');

                $this->value->SetMargin(0);

                $this->value->Stroke($img, $val, $x, $y);
            } elseif ('bottom' == $this->valuepos || 'min' == $this->valuepos) {
                $y = $pts[1];

                if (90 === $img->a) {
                    if ($val < 0) {
                        $this->value->SetAlign('right', 'center');
                    } else {
                        $this->value->SetAlign('left', 'center');
                    }
                }

                $this->value->SetMargin(3);

                $this->value->Stroke($img, $val, $x, $y);
            } else {
                JpGraphError::Raise('Unknown position for values on bars :' . $this->valuepos);

                die();
            }

            // Create the client side image map

            $rpts = $img->ArrRotate($pts);

            $csimcoord = round($rpts[0]) . ', ' . round($rpts[1]);

            for ($j = 1; $j < 4; ++$j) {
                $csimcoord .= ', ' . round($rpts[2 * $j]) . ', ' . round($rpts[2 * $j + 1]);
            }

            if (!empty($this->csimtargets[$i])) {
                $this->csimareas .= '<area shape="poly" coords="' . $csimcoord . '" ';

                $this->csimareas .= ' href="' . $this->csimtargets[$i] . '"';

                if (!empty($this->csimalts[$i])) {
                    $sval = sprintf($this->csimalts[$i], $this->coords[0][$i]);

                    $this->csimareas .= " alt=\"$sval\" title=\"$sval\" ";
                }

                $this->csimareas .= ">\n";
            }
        }

        return true;
    }
} // Class

//===================================================
// CLASS GroupBarPlot
// Description: Produce grouped bar plots
//===================================================
class GroupBarPlot extends BarPlot
{
    public $plots;

    public $width = 0.7;

    public $nbrplots = 0;

    public $numpoints;

    //---------------

    // CONSTRUCTOR

    public function __construct($plots)
    {
        $this->plots = $plots;

        $this->nbrplots = count($plots);

        if ($this->nbrplots < 1) {
            JpGraphError::Raise('You must have at least one barplot in the array to be able to create a Grouped Bar Plot.');
        }

        $this->numpoints = $plots[0]->numpoints;
    }

    //---------------

    // PUBLIC METHODS

    public function Legend(&$graph)
    {
        $n = count($this->plots);

        for ($i = 0; $i < $n; ++$i) {
            $c = get_class($this->plots[$i]);

            $sc = is_subclass_of($this->plots[$i], 'barplot');

            if ('barplot' !== $c && !$sc) {
                JpGraphError::Raise('One of the objects submitted to GroupBar is not a BarPlot. Make sure that you create the Group Bar plot from an array of BarPlot or AccBarPlot objects.');
            }

            $this->plots[$i]->DoLegend($graph);
        }
    }

    public function Min()
    {
        [$xmin, $ymin] = $this->plots[0]->Min();

        $n = count($this->plots);

        for ($i = 0; $i < $n; ++$i) {
            [$xm, $ym] = $this->plots[$i]->Min();

            $xmin = max($xmin, $xm);

            $ymin = min($ymin, $ym);
        }

        return [$xmin, $ymin];
    }

    public function Max()
    {
        [$xmax, $ymax] = $this->plots[0]->Max();

        $n = count($this->plots);

        for ($i = 0; $i < $n; ++$i) {
            [$xm, $ym] = $this->plots[$i]->Max();

            $xmax = max($xmax, $xm);

            $ymax = max($ymax, $ym);
        }

        return [$xmax, $ymax];
    }

    public function GetCSIMareas()
    {
        $n = count($this->plots);

        $csimareas = '';

        for ($i = 0; $i < $n; ++$i) {
            $csimareas .= $this->plots[$i]->csimareas;
        }

        return $csimareas;
    }

    // Stroke all the bars next to each other

    public function Stroke(&$img, &$xscale, &$yscale)
    {
        $tmp = $xscale->off;

        $n = count($this->plots);

        $subwidth = $this->width / $this->nbrplots;

        for ($i = 0; $i < $n; ++$i) {
            $this->plots[$i]->ymin = $this->ybase;

            $this->plots[$i]->SetWidth($subwidth);

            // If the client have used SetTextTickInterval() then

            // major_step will be > 1 and the positioning will fail.

            // If we assume it is always one the positioning will work

            // fine with a text scale but this will not work with

            // arbitrary linear scale

            $xscale->off = $tmp + $i * round(/*$xscale->ticks->major_step* */ $xscale->scale_factor * $subwidth);

            $this->plots[$i]->Stroke($img, $xscale, $yscale);
        }

        $xscale->off = $tmp;
    }
} // Class

//===================================================
// CLASS AccBarPlot
// Description: Produce accumulated bar plots
//===================================================
class AccBarPlot extends BarPlot
{
    public $plots = null;

    public $nbrplots = 0;

    public $numpoints = 0;

    //---------------

    // CONSTRUCTOR

    public function __construct($plots)
    {
        $this->plots = $plots;

        $this->nbrplots = count($plots);

        $this->numpoints = $plots[0]->numpoints;

        $this->value = new DisplayValue();
    }

    //---------------

    // PUBLIC METHODS

    public function Legend(&$graph)
    {
        $n = count($this->plots);

        for ($i = $n - 1; $i >= 0; --$i) {
            $c = get_class($this->plots[$i]);

            if ('barplot' !== $c) {
                JpGraphError::Raise('One of the objects submitted to AccBar is not a BarPlot. Make sure that you create the AccBar plot from an array of BarPlot objects.');
            }

            $this->plots[$i]->DoLegend($graph);
        }
    }

    public function Max()
    {
        [$xmax] = $this->plots[0]->Max();

        $nmax = 0;

        for ($i = 0, $iMax = count($this->plots); $i < $iMax; ++$i) {
            $n = count($this->plots[$i]->coords[0]);

            $nmax = max($nmax, $n);

            [$x] = $this->plots[$i]->Max();

            $xmax = max($xmax, $x);
        }

        for ($i = 0; $i < $nmax; $i++) {
            // Get y-value for bar $i by adding the

            // individual bars from all the plots added.

            // It would be wrong to just add the

            // individual plots max y-value since that

            // would in most cases give to large y-value.

            $y = 0;

            if ($this->plots[0]->coords[0][$i] > 0) {
                $y = $this->plots[0]->coords[0][$i];
            }

            for ($j = 1; $j < $this->nbrplots; $j++) {
                if ($this->plots[$j]->coords[0][$i] > 0) {
                    $y += $this->plots[$j]->coords[0][$i];
                }
            }

            $ymax[$i] = $y;
        }

        $ymax = max($ymax);

        // Bar always start at baseline

        if ($ymax <= $this->ybase) {
            $ymax = $this->ybase;
        }

        return [$xmax, $ymax];
    }

    public function Min()
    {
        $nmax = 0;

        [$xmin, $ysetmin] = $this->plots[0]->Min();

        for ($i = 0, $iMax = count($this->plots); $i < $iMax; ++$i) {
            $n = count($this->plots[$i]->coords[0]);

            $nmax = max($nmax, $n);

            [$x, $y] = $this->plots[$i]->Min();

            $xmin = min($xmin, $x);

            $ysetmin = min($y, $ysetmin);
        }

        for ($i = 0; $i < $nmax; $i++) {
            // Get y-value for bar $i by adding the

            // individual bars from all the plots added.

            // It would be wrong to just add the

            // individual plots max y-value since that

            // would in most cases give to large y-value.

            $y = $this->plots[0]->coords[0][$i];

            for ($j = 1; $j < $this->nbrplots; $j++) {
                $y += $this->plots[$j]->coords[0][$i];
            }

            $ymin[$i] = $y;
        }

        $ymin = min($ysetmin, min($ymin));

        // Bar always start at baseline

        if ($ymin >= $this->ybase) {
            $ymin = $this->ybase;
        }

        return [$xmin, $ymin];
    }

    // Stroke acc bar plot

    public function Stroke(&$img, &$xscale, &$yscale)
    {
        $pattern = null;

        $img->SetLineWeight($this->weight);

        for ($i = 0; $i < $this->numpoints - 1; $i++) {
            $accy = 0;

            $accy_neg = 0;

            for ($j = 0; $j < $this->nbrplots; ++$j) {
                $img->SetColor($this->plots[$j]->color);

                if ($this->plots[$j]->coords[0][$i] >= 0) {
                    $yt = $yscale->Translate($this->plots[$j]->coords[0][$i] + $accy);

                    $accyt = $yscale->Translate($accy);

                    $accy += $this->plots[$j]->coords[0][$i];
                } else {
                    //if ( $this->plots[$j]->coords[0][$i] < 0 || $accy_neg < 0 ) {

                    $yt = $yscale->Translate($this->plots[$j]->coords[0][$i] + $accy_neg);

                    $accyt = $yscale->Translate($accy_neg);

                    $accy_neg += $this->plots[$j]->coords[0][$i];
                }

                $xt = $xscale->Translate($i);

                if ($this->abswidth > -1) {
                    $abswidth = $this->abswidth;
                } else {
                    $abswidth = round($this->width * $xscale->scale_factor, 0);
                }

                $pts = [$xt, $accyt, $xt, $yt, $xt + $abswidth, $yt, $xt + $abswidth, $accyt];

                if ($this->bar_shadow) {
                    $ssh = $this->bar_shadow_hsize;

                    $ssv = $this->bar_shadow_vsize;

                    // We must also differ if we are a positive or negative bar.

                    if (0 === $j) {
                        // This gets extra complicated since we have to

                        // see all plots to see if we are negative. It could

                        // for example be that all plots are 0 until the very

                        // last one. We therefore need to save the initial setup

                        // for both the negative and positive case

                        // In case the final bar is positive

                        $sp[0] = $pts[6] + 1;

                        $sp[1] = $pts[7];

                        $sp[2] = $pts[6] + $ssh;

                        $sp[3] = $pts[7] - $ssv;

                        // In case the final bar is negative

                        $nsp[0] = $pts[0];

                        $nsp[1] = $pts[1];

                        $nsp[2] = $pts[0] + $ssh;

                        $nsp[3] = $pts[1] - $ssv;

                        $nsp[4] = $pts[6] + $ssh;

                        $nsp[5] = $pts[7] - $ssv;

                        $nsp[10] = $pts[6] + 1;

                        $nsp[11] = $pts[7];
                    }

                    if ($j === $this->nbrplots - 1) {
                        // If this is the last plot of the bar and

                        // the total value is larger than 0 then we

                        // add the shadow.

                        if (is_array($this->bar_shadow_color)) {
                            $numcolors = count($this->bar_shadow_color);

                            if (0 == $numcolors) {
                                JpGraphError::Raise('You have specified an empty array for shadow colors in the bar plot.');
                            }

                            $img->PushColor($this->bar_shadow_color[$i % $numcolors]);
                        } else {
                            $img->PushColor($this->bar_shadow_color);
                        }

                        if ($accy > 0) {
                            $sp[4] = $pts[4] + $ssh;

                            $sp[5] = $pts[5] - $ssv;

                            $sp[6] = $pts[2] + $ssh;

                            $sp[7] = $pts[3] - $ssv;

                            $sp[8] = $pts[2];

                            $sp[9] = $pts[3] - 1;

                            $sp[10] = $pts[4] + 1;

                            $sp[11] = $pts[5];

                            $img->FilledPolygon($sp, 4);
                        } elseif ($accy_neg < 0) {
                            $nsp[6] = $pts[4] + $ssh;

                            $nsp[7] = $pts[5] - $ssv;

                            $nsp[8] = $pts[4] + 1;

                            $nsp[9] = $pts[5];

                            $img->FilledPolygon($nsp, 4);
                        }

                        $img->PopColor();
                    }
                }

                // If value is NULL or 0, then don't draw a bar at all

                if (0 == $this->plots[$j]->coords[0][$i]) {
                    continue;
                }

                if ($this->plots[$j]->grad) {
                    $grad = new Gradient($img);

                    $grad->FilledRectangle(
                        $pts[2],
                        $pts[3],
                        $pts[6],
                        $pts[7],
                        $this->plots[$j]->grad_fromcolor,
                        $this->plots[$j]->grad_tocolor,
                        $this->plots[$j]->grad_style
                    );
                } else {
                    if (is_array($this->plots[$j]->fill_color)) {
                        $numcolors = count($this->plots[$j]->fill_color);

                        $img->SetColor($this->plots[$j]->fill_color[$i % $numcolors]);
                    } else {
                        $img->SetColor($this->plots[$j]->fill_color);
                    }

                    $img->FilledPolygon($pts);

                    $img->SetColor($this->plots[$j]->color);
                }

                // Stroke the pattern

                if ($this->plots[$j]->iPattern > -1) {
                    if (null === $pattern) {
                        $pattern = new RectPatternFactory();
                    }

                    $prect = $pattern->Create($this->plots[$j]->iPattern, $this->plots[$j]->iPatternColor, 1);

                    $prect->SetDensity($this->plots[$j]->iPatternDensity);

                    $prect->SetPos(new Rectangle($pts[2], $pts[3], $pts[4] - $pts[0] + 1, $pts[1] - $pts[3] + 1));

                    $prect->Stroke($img);
                }

                // CSIM array

                if ($i < count($this->plots[$j]->csimtargets)) {
                    // Create the client side image map

                    $rpts = $img->ArrRotate($pts);

                    $csimcoord = round($rpts[0]) . ', ' . round($rpts[1]);

                    for ($k = 1; $k < 4; ++$k) {
                        $csimcoord .= ', ' . round($rpts[2 * $k]) . ', ' . round($rpts[2 * $k + 1]);
                    }

                    if (!empty($this->plots[$j]->csimtargets[$i])) {
                        $this->csimareas .= '<area shape="poly" coords="' . $csimcoord . '" ';

                        $this->csimareas .= ' href="' . $this->plots[$j]->csimtargets[$i] . '"';

                        if (!empty($this->plots[$j]->csimalts[$i])) {
                            $sval = sprintf($this->plots[$j]->csimalts[$i], $this->plots[$j]->coords[0][$i]);

                            $this->csimareas .= " alt=\"$sval\" title=\"$sval\" ";
                        }

                        $this->csimareas .= ">\n";
                    }
                }

                $pts[] = $pts[0];

                $pts[] = $pts[1];

                $img->Polygon($pts);
            }

            // Draw labels for each acc.bar

            $x = $pts[2] + ($pts[4] - $pts[2]) / 2;

            $y = $yscale->Translate($accy);

            if ($this->bar_shadow) {
                $x += $ssh;
            }

            $this->value->Stroke($img, $accy, $x, $y);

            $accy = 0;

            $accy_neg = 0;

            for ($j = 0; $j < $this->nbrplots; ++$j) {
                // We don't print 0 values in an accumulated bar plot

                if (0 == $this->plots[$j]->coords[0][$i]) {
                    continue;
                }

                if ($this->plots[$j]->coords[0][$i] > 0) {
                    $yt = $yscale->Translate($this->plots[$j]->coords[0][$i] + $accy);

                    $accyt = $yscale->Translate($accy);

                    $y = $accyt - ($accyt - $yt) / 2;

                    $accy += $this->plots[$j]->coords[0][$i];
                } else {
                    $yt = $yscale->Translate($this->plots[$j]->coords[0][$i] + $accy_neg);

                    $accyt = $yscale->Translate($accy_neg);

                    //$y=0;

                    $accy_neg += $this->plots[$j]->coords[0][$i];

                    $y = $accyt - ($accyt - $yt) / 2; // TODO : Check this fix
                }

                $this->plots[$j]->value->SetAlign('center', 'center');

                $this->plots[$j]->value->SetMargin(0);

                $this->plots[$j]->value->Stroke($img, $this->plots[$j]->coords[0][$i], $x, $y);
            }
        }

        return true;
    }
} // Class

/* EOF */
