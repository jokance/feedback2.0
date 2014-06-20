<?php

require 'src/jpgraph.php';
require 'src/jpgraph_line.php';

$keys=file_get_contents('results/keys.txt');
$values=file_get_contents('results/values.txt');
$keys_arr=explode(',', $keys);
$values_arr=explode(',', $values);

$datay=array();
for($i=1;$i<count($values_arr);$i++){
	$datay[]= -($values_arr[$i]-$values_arr[$i-1]);
}


$width=1500;
$height=800;

$graph = new Graph($width,$height);
$graph->SetScale('textlin');


$lineplot=new LinePlot($datay);
$graph->Add($lineplot);
$graph->Stroke();
?>