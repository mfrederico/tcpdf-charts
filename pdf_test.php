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

// LET THE FUN BEGIN!

/**********************************************/
//Ugly Dataset
/**********************************************/
$data[] = array('text'=>'here is a ton of text to ','rows'=>array(0=>40));
$data[] = array('text'=>'test how well or poorly','rows'=>array(0=>2));
$data[] = array('text'=>'collision detection and rebuilding','rows'=>array(0=>0));
$data[] = array('text'=>'of the lines and other such stuff','rows'=>array(0=>1));
$data[] = array('text'=>'should be working inside my','rows'=>array(0=>1));
$data[] = array('text'=>'pie chart function.  Have to get','rows'=>array(0=>1));
$data[] = array('text'=>'this working right or stuff','rows'=>array(0=>1));
$data[] = array('text'=>'wont look correct as it is displayed','rows'=>array(0=>39));
$data[] = array('text'=>'on the','rows'=>array(0=>5));
$data[] = array('text'=>'page / screen / layout','rows'=>array(0=>0));

/**********************************************/
// Pretty Dataset
/**********************************************/
$data2[] = array('text'=>'Nice charts eh?','rows'=>array(0=>20));
$data2[] = array('text'=>'Simple to set up','rows'=>array(0=>30));
$data2[] = array('text'=>'Decent looking','rows'=>array(0=>40));
$data2[] = array('text'=>'Whatchew lookin\' at!','rows'=>array(0=>40));


/**********************************************/
// Load my Classes
/**********************************************/
require_once('../tcpdf/tcpdf.php');  // Latest tcpdf 
require_once('tcpdf-charts.php'); // Extends tcpdf

$pdf=new TcpdfCharts('P','pt','Letter',true,'UTF-8',false);
$pdf->SetCompression(false);
$pdf->AddPage();

$md['Them'] = $data;
$md['Us']	= $data2;
$pdf->FancyTable2('radio','FancyTable II',array("Makes for a decent",'table to present','data and information','side by side'),$md);
$pdf->graphHeading('Colliding text - help if you can!');
$pdf->buildPieGraph($data,50);

$pdf->graphHeading('Nice pie charts are in store!');
$pdf->buildPieGraph($data2,50);

$pdf->graphHeading('Interchangable data array!');
$pdf->buildHorizGraph($data2);
$pdf->Output();
exit();
?>
