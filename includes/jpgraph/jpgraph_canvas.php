<?php
/*=======================================================================
// File: 	JPGRAPH_CANVAS.PHP
// Description:	Canvas drawing extension for JpGraph
// Created: 	2001-01-08
// Author:	Johan Persson (johanp@aditus.nu)
// Ver:		$Id: jpgraph_canvas.php,v 1.1 2004/08/25 01:12:53 rboyleiii Exp $
//
// License:	This code is released under QPL
// Copyright (C) 2001,2002 Johan Persson
//========================================================================
*/

//===================================================
// CLASS CanvasGraph
// Description: Creates a simple canvas graph which
// might be used together with the basic Image drawing
// primitives. Useful to auickoly produce some arbitrary
// graphic which benefits from all the functionality in the
// graph liek caching for example.
//===================================================
class CanvasGraph extends Graph
{
    //---------------

    // CONSTRUCTOR

    public function __construct($aWidth = 300, $aHeight = 200, $aCachedName = '', $timeout = 0, $inline = 1)
    {
        parent::__construct($aWidth, $aHeight, $aCachedName, $timeout, $inline);
    }

    //---------------

    // PUBLIC METHODS

    public function InitFrame()
    {
        $this->StrokePlotArea();
    }

    // Method description

    public function Stroke($aStrokeFileName = '')
    {
        if (null != $this->texts) {
            for ($i = 0, $iMax = count($this->texts); $i < $iMax; ++$i) {
                $this->texts[$i]->Stroke($this->img);
            }
        }

        $this->StrokeTitles();

        // Should we do any final image transformation

        if ($this->iImgTrans) {
            if (!class_exists('ImgTrans')) {
                require_once __DIR__ . '/jpgraph_imgtrans.php';
            }

            $tform = new ImgTrans($this->img->img);

            $this->img->img = $tform->Skew3D(
                $this->iImgTransHorizon,
                $this->iImgTransSkewDist,
                $this->iImgTransDirection,
                $this->iImgTransHighQ,
                $this->iImgTransMinSize,
                $this->iImgTransFillColor,
                $this->iImgTransBorder
            );
        }

        // If the filename is given as the special _IMGHandler

        // then the image handler is returned and the image is NOT

        // streamed back

        if (_IMGHandler == $aStrokeFileName) {
            return $this->img->img;
        }  

        // Finally stream the generated picture

        $this->cache->PutAndStream(
                $this->img,
                $this->cache_name,
                $this->inline,
                $aStrokeFileName
            );
    }
} // Class
/* EOF */
