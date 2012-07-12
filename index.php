<?php 
$fd = fopen("count.txt","a+"); fputs($fd,$_SERVER['HTTP_REFERER']."->".$_SERVER['REMOTE_ADDR']."\n"); fclose($fd); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title id="">Tcpdf Charts</title>
<!--jQuery References-->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js" type="text/javascript"></script>
        <script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.18/jquery-ui.min.js" type="text/javascript"></script>

    <!--Theme-->
    <link href="http://cdn.wijmo.com/themes/aristo/jquery-wijmo.css" rel="stylesheet" type="text/css" title="rocket-jqueryui" />

    <!--Wijmo Widgets CSS-->
    <link href="http://cdn.wijmo.com/jquery.wijmo-complete.all.2.0.5.min.css" rel="stylesheet" type="text/css" />

    <!--Wijmo Widgets JavaScript-->
    <script src="http://cdn.wijmo.com/jquery.wijmo-open.all.2.0.5.min.js" type="text/javascript"></script>
    <script src="http://cdn.wijmo.com/jquery.wijmo-complete.all.2.0.5.min.js" type="text/javascript"></script>

<script>
  //  $j = jQuery.noConflict();
</script>
<style>
a:visited,a:link,a:hover { color:lightgrey; }
html, body
{
  background: #3f3f3f;
  margin: 0;
  padding: 7px;
  color: #fafafa;
  font-size: 12px;
  height: 100%;
  font-family: Arial, Helvetica, sans-serif;
}
</style>
<body class="light-color-scheme">
  <h1 class="header">TcpdfCharts Charting for TCPDF</h1>
	<div id="master-offset">
	<div id="container">
			<div id="page-header">
				<h1 id="product-mark" class="placeholder"></h1>
			</div>
			<div class="content-block">
	<h2>Prepare to be awestruck with the example! ;-)</h2>
	<blockquote>
	Thrill at the sight of the pie-chart and line-chart in action! 
		<div><a href="pdf_test.php">[SHOW EXAMPLE PDF]</a></div>
	</blockquote>

	<h2>Download and enjoy TcpdfCharts Source Code</h2>
	<ul>
		<li><a href="#example">Source for the example</a></li>
		<li><a href="#source">Source for the TcpdfCharts class</a></li>
		<li><a href="https://github.com/mfrederico/tcpdf-charts">GITHUB for the TcpdfCharts class</a></li>
	</ul>

	<h2>Documentation</h2>
		<blockquote><a href="doc/default/TcpdfCharts.html">Documentation</a></blockquote>

	<h2>Some things to note</h2>
	<ul>
		<li>Compatible with tcpdf 5.1 as of this writing</li>
		<li>3 Graph Types
			<ul>
				<li>Horizontal Line chart</li>
				<li>Pie chart - with horrible collision detection, but works great in most cases (please help here)</li>
				<li>Horizontal Matrix/rank Chart</li>
			</ul>
		</li>
		<li>It's all open source, and I just hope people will contribute, make it better and enjoy!</li>
	</ul>
	<h2>Contact the author</h2>
	<blockquote>mfrederico at gmail</blockquote>
	<div id="accordion">
		<h2 id="example">Example Source</h2>
		<div style="height:300px;overflow:scroll">
			<?php highlight_file('pdf_test.php'); ?>
		</div>
		<h2 id="source">Class Source</h2>
		<div style="height:300px;overflow:scroll">
			<?php highlight_file('tcpdf-charts.php'); ?>
		</div>
	</div>
</div>

<script id="scriptInit" type="text/javascript">
$(document).ready(function () 
{
  $("#accordion").wijaccordion({requireOpenedPane: false});

});
	
</script>
			</div>
		</div>
	</div>
</body>
</html>


