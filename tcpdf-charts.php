<?php
//******************************************************************************
//  Copyright (C) 2010  Matthew Frederico - ultrize.com
//
//  This program is free software: you can redistribute it and/or modify
//  it under the terms of the GNU Lesser General Public License as published by
//  the Free Software Foundation, either version 2.1 of the License, or
//  (at your option) any later version.
//
//  This program is distributed in the hope that it will be useful,
//  but WITHOUT ANY WARRANTY; without even the implied warranty of
//  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
//  GNU Lesser General Public License for more details.
//
//  You should have received a copy of the GNU Lesser General Public License
//  along with this program.  If not, see <http://www.gnu.org/licenses/>.
//******************************************************************************
/*
 * Developing a class extension for tcpdf that allows for drawing of easy charts and graphs
 * @package com.ultrize.tcpdfcharts extends com.tecnick.tcpdf
 * @abstract Class for generating PDF charts extended from tcpdf 
 * @author Matthew Frederico
 * @copyright 2010 Matthew Frederico - ultrize.com - mfrederico@gmail.com
 * @link http://www.ultrize.com/tcpdfcharts/
 * @license http://www.gnu.org/copyleft/lesser.html LGPL
 * @version 1.000
 */

class TcpdfCharts extends TCPDF
{
	var $dbg		= 0;

	/**
	* Just a simple heading for a graph
	* @param string $text
	* @access public
	* @since 1.0 
	* @version 1.0
	* @todo set customized colors/background
	**/
	function graphHeading($text)
	{
		$this->setTextColor(120,166,14);
		$this->SetFont($this->BASEFONT,'',12);
		$this->ln($this->getLastH());
		$this->cell(0,15,$text);
		$this->ln($this->getLastH());
	}

	/**
	* Draws a checkmark at x,y
	* @see FancyTable2
	* @param int $x
	* @param int $y
	* @access private
	* @since 1.0 
	* @version 1.0
	* @todo set customized colors/background
	**/
	function checkMark($x,$y)
	{
		// would be nice to set "radius" :(
		$this->SetLineStyle(array('width' => 1, 'cap' => 'square', 'join' => 'bevel', 'dash' => 0, 'color' => array(255, 255, 255))); 
		$this->setFillColor(120, 166, 14);
		$this->Circle($x+9, $y+9, 6, 0, 360, 'DF', null, array(162,194,89));
		$this->SetLineStyle(array('width' => .5, 'cap' => 'square', 'join' => 'bevel', 'dash' => 0, 'color' => array(255, 255, 255))); 
		$this->Polygon(array($x+6,$y+9,$x+9,$y+13,$x+14,$y+2,$x+13,$y+1,$x+9,$y+10,$x+7,$y+8),'DF',null,array(255,255,255)); 
	}

	/**
	* Draws a horizontal graph at current position on page
	* @param array $data an array of text/row data in the form of array('text'=>'This is my data','rows'=>array('0','10))
	* @param int $lineHeight (optional) Height of the text cell
	* @param int $padding (optional) padding between rows
	* @param int $barHeight (optional) thickness/height of the bars/lines
	* @param int $gridTextPt (optional) font size in PT for the grid
	* @param bool $showGrid default true (optional) whether or not to display the grid lines for the chart
	* @access private
	* @since 1.0 
	* @version 1.0
	* @todo set customized colors/background
	**/
	function buildHorizGraph($data,$lineHeight=8,$padding=0,$barHeight=6,$gridTextPt=8,$showGrid=true)
	{
		$MAXSW=200;	// Max string width
		$GR=100; 	// Graph ratio percentage
		$OW=200;	// Option cell width
		$GW=350;	// Graph bar max Width
		$CW=20;		// Grap CELL width;

		// Retain my starting point
		$oldX = $this->GetX();
		$oldY = $this->GetY();

		$colors[] = array('r'=>94,'g'=>146,'b'=>7);
		$colors[] = array('r'=>128,'g'=>202,'b'=>0);
		$colors[] = array('r'=>120,'g'=>120,'b'=>120);
		$colors[] = array('r'=>160,'g'=>160,'b'=>160);
		$colors[] = array('r'=>200,'g'=>200,'b'=>200);

		// Sort these in order (high to low) 
		foreach($data as $d) $sums[] = array_sum($d['rows']);
		arsort($sums);
		foreach($sums as $idx=>$d) $final_data[] = $data[$idx];

		$GR = 100; // Use 100%
		//$GR = $final_data[0]['rows'][0];

		foreach($final_data as $idx=>$d)
		{
			// The option text
			$this->setTextColor(0,0,0);
			$this->SetFont($this->BASEFONT,'',$lineHeight);
			$this->cell($OW,0,$this->stringReduce($d['text'],$MAXSW),0,0,'R');

			// For each of my row/datas
			foreach($d['rows'] as $ridx=>$v) 
			{
				// Width of the bar based on ratios
				$dcw = ($v / $GR) * ($GW);
				$ctr = ($this->GetLastH() / 2) - ($barHeight / 2);

				// Linear Gradient is BROKEN!!!
				//$this->LinearGradient($this->GetX() + 1,$this->GetY() + $ctr,$dcw,$barHeight,$colors[$ridx],$colors[$ridx+1],array(0,1,0,0));
				$this->SetLineStyle(array('width' => 1, 'cap' => 'square', 'join' => 'bevel', 'color' => array(255, 255, 255))); 
				$this->setFillColor(94,146,7);
				if ($dcw == 0) $dcw = .05; // don't want to FILL it out .. SHEESH
				$this->cell($dcw,$barHeight,'',1,0,'',1);

				$sw = $this->GetStringWidth($d['rows'][$ridx].'%');
				// Add the numerical value inside the graph
				$ofs = (($dcw + $sw) < $GW) ? 0 : ($sw + 2);

				$this->SetFont($this->BASEFONT,'',$barHeight - 2);
				$this->SetX($this->GetX());// + $dcw - $ofs);
				$this->Cell($sw,$this->GetLastH(),$d['rows'][$ridx].'%',0);
			}
			//This sets the padding gap between lines
			$this->ln($this->getLastH() + $padding);
		}

		$newY = $this->GetY();
		$this->SetY($this->GetY());

		/*****************************/
		/* Create my percentile grid */
		/*****************************/
		if ($showGrid)
		{
			$GFS		= $gridTextPt; // Grid Font Size

			$this->setlinestyle(array('width' => .5, 'cap' => 'square', 'join' => 'bevel', 'dash' => 0, 'color' => array(0, 0, 0))); 

			$this->SetFont($this->BASEFONT,'',$GFS);
			$this->setTextColor(0,0,0);
			$this->setFillColor(255,255,255);
			$this->SetLineStyle(array('width' => .5, 'cap' => 'square', 'join' => 'bevel', 'dash' => "3,3", 'color' => array(0, 0, 0))); 
			$this->cell($OW,$GFS,'',0,0,'R');

			for($x = 10;$x <= 100;$x++) if ($x %10 == 0) 
			{
				// The numbers at the bottom
				$this->setAlpha(.20);
				$this->SetLineStyle(array('width' => .5, 'cap' => 'square', 'join' => 'bevel', 'dash' => 0, 'color' => array(0, 0, 0))); 
				$this->setAlpha(.70);
				$this->cell($GW / 10,$GFS,round($GR / 100 * $x,1).'%','',0,'R',0);
				$this->setAlpha(.20);

				// Vertical Lines going UP
				$this->SetLineStyle(array('width' => .5, 'cap' => 'square', 'join' => 'bevel', 'dash' => "3,3", 'color' => array(0, 0, 0))); 
				$this->Line($this->GetX(),$this->GetY() + $lineHeight,$this->GetX(),$oldY);
			}

			// Put a top line on chart
			$this->SetLineStyle(array('width' => .5, 'cap' => 'square', 'join' => 'bevel', 'dash' => 0, 'color' => array(0, 0, 0))); 
			$this->Line($oldX + $OW,$oldY,$GW + $OW + $this->lMargin,$oldY);
			$this->Line($oldX + $OW,$this->GetY(),$GW + $OW + $this->lMargin,$this->GetY());

			// Put a left line on chart
			//$this->SetLineStyle(array('width' => .5, 'cap' => 'square', 'join' => 'bevel', 'dash' => 0, 'color' => array(0, 0, 0))); 
			$this->Line($oldX + $OW,$this->GetY(),$oldX + $OW,$oldY);

			$this->setAlpha(1);

			//$this->SetX($oldX);
			//$this->SetY($oldY);
		}
		/***************************/
		$this->ln($lineHeight);
	}


	/**
	* Get sin/cosine xy factors for pie chart  - returns x,y in array.
	* @param float $degrees any point in degrees between 0 and 360
	* @return array returns xy coordinates
	* @access private
	* @since 1.0 
	* @version 1.0
	**/
	function get_xy_factors ($degrees)
	{
		$x = cos(deg2rad($degrees));
		$y = sin(deg2rad($degrees));
		return (array($x, $y));
	}

	/**
	* Draw a simple pie chart similar to google-charts pie chart
	* @see data format
	* @param array $cdata chart data serie
	* @param float $r radius of pie chart
	* @param int $xc the x coord of the chart (optional)
	* @param int $yc the y coord of the chart (optional)
	* @access public
	* @since 1.0 
	* @version 1.0
	* @todo Text collision detection, line collision detection, custom color pallette (setPiePallette method?),slice pulling
	**/
	function buildPieGraph($cdata,$r=50,$xc=null,$yc=null)
	{
		$MAXSW		= 250;  // Max String/font width (Should be font width)
		$LINELEN	= 10;	// straight line segment length

		if ($xc == null) $xc = $this->getPageWidth() / 2;
		if ($yc == null) $yc = $r + $this->y;

		$colors[] = $this->hex2rgb('70A100');
		$colors[] = $this->hex2rgb('005CAB');
		$colors[] = $this->hex2rgb('E07804');
		$colors[] = $this->hex2rgb('247B84');
		$colors[] = $this->hex2rgb('8951AA');
		$colors[] = $this->hex2rgb('9A112B');
		$colors[] = $this->hex2rgb('E38A0A');
		$colors[] = $this->hex2rgb('525252');
		$colors[] = $this->hex2rgb('FFFC82');

		// Go through chart data and reduce string sizes
		foreach($cdata as $idx=>$d)
		{
			$data['row'][] = $d['rows'][0];
			$d['text'] = $this->stringReduce($d['text'],$MAXSW);
			$data['text'][] = $d['text'];
		}

		$sdeg = 0;
		$ldeg = 0;
		$lowestY = 0;
		$coords = array();

		$this->SetFont('','',6);
		$sum = array_sum($data['row']);
		// Create all my pie sectors first
		// You can tell if text will collide HERE BIAA!
		foreach($data['row'] as $c=>$val)
		{
			if ($val > 0)
			{
				$pctDeg = ($val / $sum) * 360;
				$ldeg += $pctDeg;

				// put my text
				$ctr = round(($ldeg - $sdeg) / 2,2);

				// Based on the radius of the angle, calculate x & y coords
				list($tx,$ty) = $this->get_xy_factors(($sdeg-90) + $ctr);

				// This is the "diagonal" 
				$tx1 = round($xc +($tx * $r),2);
				$ty1 = round($yc +($ty * $r),2);

				// This is the "straight" and subsequent "text"
				$tx2 = round($xc +($tx * ($r + $this->getFontSize())),2);
				$ty2 = round($yc +($ty * ($r + $this->getFontSize())),2);

				// Determin which side of the circle we're drawing on
				$lineSide = ($tx2 < $xc) ? 'left' : 'right';

				// Use previous coordinates (if applicable)
				if (is_array($coords[$c -1]))
				{
					// Use previous coordinates
					$prev = $coords[$c - 1];

					// Descent
					if ($sdeg < 180 && $prev['ctr'] < 5)
					{
						{
							if ($ty2 - $prev['ty2'] < 7)// && $sdeg > $prev['sdeg'])
							{
								$ty2 = $prev['ty2'] + 6; 
								$tx2 = 10 + $xc + $r;
							}
						}
					}
					// Ascent
					else if ($sdeg > 180 && $ctr < 5)
					{
						if ($prev['ty2'] - $ty2 < 7)// && $sdeg > $prev['sdeg'])
						{
							$tx2 = $xc - $r;
							$ty2 = $prev['ty2'] - 6;
						}
					}

				}

				// Set up my text
				//$string = "{$c}) ".round($ty2)." vs ".round($coords[$c - 1]['ty2'])."  {$ctr} ".$data['text'][$c];
				$string = $data['text'][$c];

				// Save my previous "Y" coordinates
				$coords[$c] = array('ctr'=>$ctr,'ty1'=>$ty1,'ty2'=>$ty2,'tx1'=>$tx1,'tx2'=>$tx2,'ldeg'=>$ldeg,'sdeg'=>$sdeg);

				// Draw my diagonal line from the "slice"
				$this->SetLineStyle(array('width'=>.5,'color'=>array('100','100','100')));


				$this->Line($tx1,$ty1,$tx2,$ty2);

				// Now draw my lines
				// Left side of graph
				if ($lineSide == 'left')
				{
					$this->Line($tx2,$ty2,$tx2-$LINELEN,$ty2);
					$textX = $tx2 - ($this->GetStringWidth($string) + 10 + $LINELEN);
				}
				// Right side of graph
				else if ($lineSide == 'right')
				{
					$this->Line($tx2,$ty2,$tx2+$LINELEN,$ty2);
					$textX = $tx2 + $LINELEN;
				}

				// print my chart callout
				$this->setTextColor(100,100,100);
				$m = -2; // Just a little padding .. 
				$textY = ($ty2 - ($this->getFontSize() / 2 - $m));

				$this->text($textX,$textY,$string);

				// maintain appropriate spacing at bottom for chart
				if ($lowestY < $textY) $lowestY = $textY;

				// Make my pie sector happen
				$this->SetFillColor($colors[$c]['r'],$colors[$c]['g'],$colors[$c]['b']);
				$this->SetLineStyle(array('width' => 1, 'cap' => 'square', 'join' => 'bevel', 'dash' => 0, 'color' => array(255, 255, 255))); 
				//$this->setAlpha(.3);
				$this->PieSector($xc, $yc, $r, $sdeg, $ldeg);
				$this->setAlpha(1);


				$sdeg = $ldeg;
			}
			else unset($data['row'][$c]);
		}
		$this->SetY($lowestY);
		$this->ln();
		$this->multiCell(0,0,$cd);
	}


	/**
	* Sorts a matrix/rank order to create the "descending" order
	* @see data format
	* @param array $data dataset/serie
	* @param int $sort 
	* @access private
	* @since 1.0 
	* @version 1.0
	* @todo use defines for sort type numerics
	**/
	function sortMatrix($data,$sort=1)
	{
		// Sort these by summed percentage - create a "slope"
		if ($sort == 1)
		{ 
			foreach($data as $i=>$d) 
			{
				$xdata = array();
				for($idx = 0;$idx < 3;$idx++)
				{
					$xdata[$idx] = 1 + $data[$i]['rows'][$idx];
				}
				$data[$i]['rows'] = $xdata;
			}

			// Change all these to percentages
			foreach($data as $i=>$d) 
			{
				$dsum[$i] = array_sum($d['rows']);
				foreach($d['rows'] as $didx=>$val) 
					$data[$i]['rows'][$didx] = round(($val / $dsum[$i]) * 100);
			}
			usort($data,array($this,'cmp'));
			$final_data = $data;
			krsort($final_data);
		}
		// Sort on the FIRST column value
		elseif ($sort == 2)
		{        
			foreach($data as $i=>$d) 
			{
				/// Having problems with array_sum stuff.. ??
				if (!is_array($d['rows'])) 
				{ 
					$data[$i]['rows'][0]	= 0;
					$d['rows'][0]			= 0;
				}

				$dsum[$i] = array_sum($d['rows']);

				// Change all these to percentages
				foreach($d['rows'] as $didx=>$val)
				{
					// Make sure we don't divide by zero
					if ($dsum[$i] > 0) 
						$data[$i]['rows'][$didx] = round(($val / $dsum[$i]) * 100);
				}
			}

			// Now set their sortation indext.. Uh .. 
			foreach($data as $i=>$d) $sums[] = $d['rows'][0];
			arsort($sums);

			// Resort them by their sums
			foreach($sums as $idx=>$d) 
			{
				//ksort($data[$idx]['rows'],SORT_NUMERIC);
				ksort($data[$idx]['rows']);
				$final_data[] = $data[$idx];
			}
		}
		else $final_data = $data;
		return($final_data);
	}

	/**
	* builds a table for a matrix type dataset (multiple rows) 
	* @see buildMatrixTable
	* @access public
	* @since 1.0 
	* @version 1.0
	**/
	function buildRankTable($data,$lang,$lineHeight=8)
	{
		$this->buildMatrixTable(array($lang['1st'],$lang['2nd'],$lang['3rd']),$data,$lineHeight,2,1);
	}

	/**
	* Builds the matrix/rank table type 
	* @see data format
	* @param array $headers array array of keys to use from data array
	* @param array $dataset array containing serie data
	* @param int $lineHeight height of the lines / text pt (optional) 
	* @param int $pallette which pallette set to use (optional)
	* @param int $sort which sort type to use (optional)
	* @access public
	* @since 1.0 
	* @version 1.0
	**/
	function buildMatrixTable($headers,$data,$lineHeight=8,$pallette = 1,$sort=2)
	{
		$MAXSW	=200;	// Maximum option string width (pixels)
		$GR		=60; 	// Header column key cell width
		$OW		=200;	// Option cell width

		if (!is_array($data)) 
		{
			$this->cell($dcw,$lineHeight+4,"Data error in matrix table.",1,0,'R',true);
			return(false);		
		}

		$final_data = $this->sortMatrix($data,$sort);

		$m = $this->getMargins();

		$div = $this->getPageWidth() - $m['left'] - $m['right'] - $OW - 40;

		$this->SetFont($this->BASEFONT,'',$lineHeight);

		$this->setTextColor(255,255,255);
		$this->SetLineStyle(array('width' => 1, 'cap' => 'square', 'join' => 'bevel', 'dash' => 0, 'color' => array(255, 255, 255))); 
		$this->cell($OW,$lineHeight+4,'',1);

		if ($pallette == 1)
		{
			$colors[] = array('r'=>94,'g'=>146,'b'=>7);
			$colors[] = array('r'=>80,'g'=>80,'b'=>80);
			$colors[] = array('r'=>120,'g'=>120,'b'=>120);
			$colors[] = array('r'=>160,'g'=>160,'b'=>160);
			$colors[] = array('r'=>200,'g'=>200,'b'=>200);
		}

		// Caring for the brand since 2009
		if ($pallette == 2)
		{
			$colors[] = array('r'=>56,'g'=>90,'b'=>20);
			$colors[] = array('r'=>94,'g'=>146,'b'=>7);
			$colors[] = array('r'=>157,'g'=>186,'b'=>109);
			$colors[] = array('r'=>221,'g'=>232,'b'=>193);
			$colors[] = array('r'=>200,'g'=>200,'b'=>200);
		}

		// Build my header row
		$this->setTextColor(100,100,100);
		if (is_array($headers))
		{
			foreach($headers as $idx=>$h)
			{
				$this->SetFont($this->BASEFONT,'',$lineHeight-3);
				$this->setFillColor($colors[$idx]['r'],$colors[$idx]['g'],$colors[$idx]['b']);

				// Colored Cell
				if (strlen($h))
					$this->cell(10,$lineHeight+4,'',1,0,'C',true);

				if ($idx+1 == count($headers))
					if (strlen($h)) $this->cell(0,$lineHeight+4,$h,1,1,'L',false);
					else $this->cell(0,$lineHeight+4,$h,1,1,'L',false);
				else
					$this->cell($GR,$lineHeight+4,$h,1,0,'L',false);
			}
		}
		$this->SetFont($this->BASEFONT,'',$lineHeight);

		// Build my DATA row
		$this->setFillColor(255,255,255);
		$this->setTextColor(0,0,0);

		$this->setRightMargin(40 + $m['right']);
		foreach($final_data as $idx=>$d)
		{
			$this->setTextColor(0,0,0);
			
			if ($sort != 1) $d['rows'] = $this->fixPctg($d['rows'],count($headers));
			$d['rows'] = $this->fixPctg($d['rows'],count($headers));
			$d['text'] = $this->stringReduce($d['text'],$MAXSW);

			$this->cell($OW,$lineHeight+4,trim($d['text']),0,0,'R');

			if (is_array($d['rows']))
			{
				foreach($d['rows'] as $ridx=>$v) 
				{
					$this->SetFont($this->BASEFONT,'',$lineHeight-3);
					$this->setFillColor($colors[$ridx]['r'],$colors[$ridx]['g'],$colors[$ridx]['b']);
					if ($ridx == 4) 
						$this->setTextColor(100,100,100);
					else 
						$this->setTextColor(255,255,255);

					// Sets the SCALE for the data cell width
					$dcw = round(($v / 100) * $div);

					if ($v > 0) 
					{
						if ($sort == 2 && $ridx+1 == count($headers)) $dcw = 0;

						if ($v > 4) $this->cell($dcw,$lineHeight+4,"{$v}%",1,0,'R',true);
							else $this->cell($dcw,$lineHeight+4,"",1,0,'R',true);
					}

					$this->SetFont($this->BASEFONT,'',$lineHeight);
				}
			}

			$this->ln($lineHeight+4);
		}
		$this->setRightMargin($m['right']);
	}

	/**
	* Neat "fancy table 2"
	* @see data format
	* @param string $q_type type of question - matrix|rank|xref|standard 
	* @param string $title set the title column (first column) of this table (optional)
	* @param array $options simple array of text strings defining options under the title column
	* @param array $data array containing serie data
	* @param bool $sortOpts whether or not to sort these
	* @param string $sortIdx the data serie key to sort these by
	* @access public
	* @since 1.0 
	* @version 1.0
	**/
	function FancyTable2($q_type='standard',$title='',$options,$data,$sortOpts = false,$sortIdx = 'You')
	{
		// These are my data columns
		$dataKeys = array_keys($data);

		foreach($dataKeys as $dkIdx=>$colTitle)
		{
			if (!$this->array_has_values($data[$colTitle])) unset($dataKeys[$dkIdx]);
		}	

		// Set up option column width
		$dim			= $this->getPageDimensions();
		$maxWidth		= $dim['w'] - ($dim['rm'] + $dim['lm']);
		$titleWidth		= 300;

		// data columns: this is the width in pixels I have to work with
		$maxDataColWidth	= $maxWidth - $titleWidth;
		if (count($dataKeys))
		{
			$dataWidth			= $maxDataColWidth / count($dataKeys);
		}
		else $titleWidth = 0; // Span to the end

		//Colors, line width and bold font
		$this->SetFont($this->BASEFONT,'','11');
		$this->SetTextColor(0);
		$this->SetFillColor(255,255,255);
		$this->SetDrawColor(255,255,255);
		$this->SetLineWidth(1);
		$this->SetFont('','B',10);

		// Title of this fancy table
		$this->Cell($titleWidth,11,$title,1,0,'L',true);

		// Builds my data header row
		foreach($dataKeys as $dkIdx=>$colTitle)
		{
			$this->Cell($dataWidth,11,$colTitle,1,0,'C',true);
		}

		$this->Ln();

		//Color and font restoration
		$this->SetFillColor(192,192,192);
		$this->SetTextColor(0);
		$this->SetFont($this->BASEFONT,'','8');

		// Flag to fill in background
		$fill=true;

		//Data
		if (is_array($options)) 
		{
			foreach($options as $idx=>$val)
			{
				// set my background fill color
				if (!$fill) $this->SetFillColor(229,229,229);
							else $this->SetFillColor(200,200,200);
				
				$ord += 1;
				// Print my option value
				$this->Cell($titleWidth,18,"{$ord} - {$val}",1,0,'L',true,false,0,false,'T','C');
				//$this->MultiCell($titleWidth,0,"{$ord} - {$val}",1,'L',true,0);


				$this->SetLineWidth(1);
				if (!$fill) $this->SetFillColor(229,229,229);
							else $this->SetFillColor(200,200,200);

				// Plug in my data!
				foreach($dataKeys as $dkIdx=>$colTitle)
				{
					$this->SetLineWidth(1);
					if (!$fill) $this->SetFillColor(229,229,229);
								else $this->SetFillColor(200,200,200);

					// Plug in a percent mark
					$iv = is_float($data[$colTitle][$idx]['rows'][0]);
					$pctSign = ($q_type != 'matrix' && $q_type != 'rank' && $iv) ? '%' : '';


					// If we have a "checked" / "starred" option
					if (substr($data[$colTitle][$idx]['rows'][0],0,1) == '*')
					{
						// keeping things current for the check mark
						$cmx = $this->x;
						$cmy = $this->y;

						$this->Cell($dataWidth,$this->getLastH(),'',1,0,'C',true);
						$this->checkMark($cmx + ($dataWidth / 2) - 10,$cmy);
					}
					else
					{
						$this->Cell($dataWidth,$this->getLastH(),$data[$colTitle][$idx]['rows'][0].$pctSign,1,0,'C',true,false,0,false,'T','C');
					}
				}

				$this->Ln();
				$fill=!$fill;
			}
		}
		if (!$this->array_has_values($data[$sortIdx]) && !isset($_REQUEST['report_id']))
		{
			$this->SetFont($this->BASEFONT,'',7);

			if ($q_type != 'xref')
				$msg = 'Due to your response to a previous question, you were not asked this one. There is no data to display for your answer.';
			else 
				$msg = '* See analysis below';

			// Probably draw this from some language file spec
			$this->Cell(0,11,$msg,1,0,'L',false);
			$this->Ln();
		}
	}

	/**
	* Make sure our data = 100% .. :-D
	* @see data format
	* @param array $a rows to adjust to 100%
	* @returns array 
	* @access public
	* @since 1.0 
	* @version 1.0
	**/
	function fixPctg($a)
	{
		// Is this my problem?
		$i = 0;
		if (array_sum($a) != 100)
		{
			$k = array_keys($a);
			if (array_sum($a) > 100) $p = -1;
			else $p = 1;

			while(array_sum($a) != 100)
			{
				if ($i+1 > count($a)) $i = 0;
				$idx = $k[$i++];
				$a[$idx] += $p;
			}
		}
		return($a);
	}

	/**
	* Convert a hex string to  array(r,g,b) values
	* @see data format
	* @param string $color hexidecimal/HTML color value
	* @returns array  (r=>?,g=>?,b=>?)
	* @access public
	* @since 1.0 
	* @version 1.0
	**/
    function hex2rgb($color)
	{
        $color = str_replace('#', '', $color);
        if (strlen($color) != 6){ return array(100,100,100); }
        $rgb = array();
        $rgb['r'] = hexdec(substr($color,(0),2));
        $rgb['g'] = hexdec(substr($color,(2),2));
        $rgb['b'] = hexdec(substr($color,(4),2));
        return $rgb;
    }

	/**
	* Reduce the size of a string small enough to fit inside charts
	* @param string $str string to "reduce"
	* @param int $len maximum length for this string to reduce to
	* @param string $elip ellipses or text to show string is cut/reduced
	* @returns string 
	* @access public
	* @since 1.0 
	* @version 1.0
	**/
    function stringReduce($str,$len,$elip = '...')
    {
        if ($this->GetStringWidth($str) > $len)
        {
            while($this->GetStringWidth($str.$elip) > $len)
            {
                $str = substr($str,0,strlen($str)-1);
            }
            $str .= $elip;
        }
        return(trim($str));
    }

	/**
	* Callback for comparing 2 data rows series - sorting matrices
	* @see sortMatrix
	* @param array $dat1 Compare these rows
	* @param array $dat2 Compare these rows
	* @returns int 
	* @access private
	* @since 1.0 
	* @version 1.0
	**/
	function cmp($dat1,$dat2)
	{
		$ret = 0;
		$c = count($dat1['rows']);
		$c2 = count($dat2['rows']);
		if ($c2 > $c) $c = $c2;

		for ($x = 0; $x < $c;$x++)
		{
			if ($dat1['rows'][$x] > $dat2['rows'][$x])
			{
				$ret = $x+1;
				break;
			}
			else if ($dat1['rows'][$x] < $dat2['rows'][$x])
			{
				$ret = -1;
				break;
			}
		}

		//print "it is decided: {$ret}";

		return($ret);
	}

	/**
	* Determine whether or not a multidimensional serie array has values at all
	* @see FancyTable2
	* @param array $you 
	* @returns bool 
	* @access private
	* @since 1.0 
	* @version 1.0
	**/
    function array_has_values($you)
    {
        for ($x = 0;$x < count($you);$x++)
        {
            if (is_array($you[$x]))
            {
                for ($y = 0;$y < count($you[$x]['rows']);$y++)
                {
                    $v .= $you[$x]['rows'][$y];
                }
            }
            else return(false);
        }
        return((strlen($v) > 0) ? true : false);
    }
}
