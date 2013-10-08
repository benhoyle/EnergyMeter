<?php

	$db = new SQLite3('EnergyDB/energymonitor.db');
	
	if (!$db) die ($error);
	
	/*$results = $db->query('SELECT * FROM readings');*/
	
	$statement = $db->prepare('SELECT * FROM readings WHERE r_datetime > (SELECT DATETIME("now", "-12 hour"))');
	$results = $statement->execute();
	if (!$results) die("Cannot execute statement.");
	$i = 0;
	while ($row = $results->fetchArray()) 
	{
		//var_dump($row);
		//echo $row['r_temp']  . " : " . $row['r_watts'];
		//echo "<br>";
		$watts[$i] = $row['r_watts'];
		$temp[$i] = $row['r_temp'];
		$i=$i+1;
	}
	$maxwatts = max($watts);
	$maxtemp = max($temp);
	
	//Set parameters for width & height of chart
	$width = 600;
	$height = 600;
	$tickerlength = 5;
	$scalefactorwatts = $height / $maxwatts;
	$scalefactortemp = $height / $maxtemp;
	$totalheight= $height + (2*$tickerlength);
	$xticker = floor($width/count($watts));
	
	echo '<svg xmlns="http://www.w3.org/2000/svg" version="1.1" width="'.$width.'" height="'.$totalheight.'" >
		<line x1="0" y1="'.$height.'" x2="'.$width.'" y2="'.$height.'" 
					           style="stroke:black;stroke-width:2"/>
		<line x1="0" y1="0" x2="0" y2="'.$height.'" 
					           style="stroke:black;stroke-width:2"/>';
	for ($j = 0; $j < count($watts); $j++) {
		$x = $j*$xticker;
		$tickerheight = $height+$tickerlength;
		echo '<line x1="'.$x.'" y1="'.$height.'" x2="'.$x.'" y2="'.$tickerheight.'" style="stroke:black;stroke-width:1"/>';
		
		$ywatts = $height - $watts[$j]*$scalefactorwatts;
		$polystringwatts = $polystringwatts." ".$x.",".$ywatts;
		
		$ytemp = $height - $temp[$j]*$scalefactortemp;
		$polystringtemp = $polystringtemp." ".$x.",".$ytemp;
		
	}
	echo '<polyline points="'.$polystringwatts.'" style="fill:none;stroke:red;stroke-width:4"/>';
    echo '<polyline points="'.$polystringtemp.'"
         style="fill:none;stroke:green;stroke-width:4"/>';    
	echo '</svg>';
?>


