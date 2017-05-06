<?php 
require_once '../shared/PDO_Connector.php';
require_once '../model/DBMultyinsert0.2.php';




//echo($custom_params."<br />");


// create the connection to the db
$dbh = PDO_Connector::get_connection ();

// create the object DBManager
$dbm = new DBManager ( $dbh );

// create two array for save the data for axis x and y
$id			= array ();
$data 			= array ();
$volt			= array ();
$temperature		= array ();
$brightness		= array ();
$humidity 		= array ();
$pressure 		= array ();
$total_weight 		= array ();
$middleweight 		= array ();
$scale0 		= array ();
$scale1 		= array ();
$scale2 		= array ();
$scale3 		= array ();
$scale4 		= array ();
$scale5 		= array ();
$norm1			= array ();
$norm2			= array ();
$norm3			= array ();
$norm4			= array ();
$norm5			= array ();

try {
	// recovery the temperature values
	$query_select = $dbm->custom_select($custom_params);
} catch ( Exception $ex ) {
	print "Error";
}

// read the result of the select and save

foreach ( $query_select as $row ) {
	array_push ( $id, $row ['ID'] );
	array_push ( $data, $row ['date_time'] );
	array_push ( $volt, $row ['voltage'] );
	array_push ( $temperature, $row ['temperature'] );
	array_push ( $brightness, $row ['luminosity'] );
	array_push ( $humidity, $row ['humidity'] );
	array_push ( $pressure, $row ['pressure'] );
	array_push ( $total_weight, $row ['total_weight'] );
	array_push ( $middleweight, $row ['middleweight'] );
	array_push ( $scale0, $row ['weight0'] );
	array_push ( $scale1, $row ['weight1'] );
	array_push ( $scale2, $row ['weight2'] );
	array_push ( $scale3, $row ['weight3'] );
	array_push ( $scale4, $row ['weight4'] );
	array_push ( $scale5, $row ['weight5'] );
	array_push ( $norm1, $row ['Normal1'] );
	array_push ( $norm2, $row ['Normal2'] );
	array_push ( $norm3, $row ['Normal3'] );
	array_push ( $norm4, $row ['Normal4'] );
	array_push ( $norm5, $row ['Normal5'] );
	
}

Echo("data: " .end($data)."<br />");
	$wtop=intval(99999999999999999);
	$wtarget = "x";		
	$top=intval(99999999999999999);
	$target = "x";	
for($i = 0; $i < count($data); ++$i) {
// echo gettype($data[$i])."<br />";
$hms=explode(' ',$data[$i]);
echo strtotime($data[$i]."<br />");
$nextWeek = time() + (7 * 24 * 60 * 60);
$yesterday = time() - (1 * 24*  60 * 60);
$targetmidday = time() - (1 * 30*  60 * 60);

                   // 7 days; 24 hours; 60 mins; 60 secs
echo 'Now:       '. date('Y-m-d') ."\n";
echo 'Next Week: '. date('Y-m-d', $nextWeek) ."\n";
echo 'yesterday midday: '. date('Y-m-d h-m-s', $yesterday) ."\n";
echo '$targetmidday: '. date('Y-m-d h-m-s ', $targetmidday) ."\n";
 echo ('       END'."<br />");

	$day=86400;
	$week = 604800;
	$ora =  time();

	$a =intval(abs(86400-($ora-strtotime($data[$i]))));
	$w =intval(abs($week-($ora-strtotime($data[$i]))));
	if ($a < $top){
		$top = $a;
		$target = $i;
	}
	if ($w < $wtop){
		$wtop = $w;
		$wtarget = $i;
	}

}
$startnight = false;
$current = end($brightness);
	$night= false;
	$search =false;
$daynight='x';
for($i=count($brightness);$i>0;--$i){
	//echo($brightness[$i])."<br />";

// 	if ($current == 0){
// 		echo $current."<br />";
// 		$night = true;
// 	}
// 	if  (intval($brightness[$i]==0) and $night==false){
// 	echo('bright '.$brightness[$i]."<br />");
// 		$search=true;
// 	}
// 	if ( intval($brightness[$i] > 80)){
// 	echo('bright '.$brightness[$i]."<br />");
// 	//echo 'search '.$search."<br />";
// 	
// // 		$startnight = $i;
// // 		echo($startnight."<br />");
// 		//break;
// 	}
// 	elseif ( intval($brightness[$i] < 20)){
// 		echo('notte '."<br />");
// 	}
// 	else {
// 		echo('transizione '."<br />");
// 	}
}
echo('startnight ' . $date[$startnight]."<br />");

	$s1top = 0;
	$s1topid = "x";
	$s1min = 9999;
	$s1minId = 'x';
	
foreach ($scale1 as $key =>$value){

	//echo $key;
	if ($key >=$target){
	
		if (floatval($value > $s1top)){
			$s1top = $value;
			$s1topid = $key;
		}
	}
	if ($key >=$target){
	
		if (floatval($value < $s1min)){
		
			$s1min = $value;
			$s1minId = $key;
		}
	}
	
}
echo 'massimo di giornata: '.$s1top."<br />";
echo 'minimo di giornata: '.$s1min."<br />";  
echo 'stima importazione: '.floatval($s1top-$s1min)."<br />"; 

//echo "fin chi";
?>
<!DOCTYPE html>
<html>
<head>

    <script src="../js/Chart.bundle.js"></script>
    <script src="../js/utils.js"></script>

    <style>
    table, th, td {
    border: 1px solid black;
    border-collapse: collapse;
    padding: 10px;
}
/*    canvas{
        -moz-user-select: none;
        -webkit-user-select: none;
        -ms-user-select: none;
    }*/
    </style>
</head>
<body>
 <table style="width:100%">
  <tr>
    <th rowspan="2">Scala:</th>
    <th rowspan="2">attuale</th>
    <th colspan="2">Giornata</th>
    <th colspan="2">Settimana</th>
  </tr>
  <tr>
  <th>peso</th>
  <th>incremento</th>
  <th>peso</th>
  <th>incremento</th>
  </tr>
  
  <tr>
  <td bgcolor="#FF0000">Scala1</td>
    <td><?php echo((end($scale1))) ; ?></td>
    <td><?php echo(($scale1[$target])); ?></td>
    <td><?php echo(floatval(end($scale1)) - floatval($scale1[$target]))  ; ?></td>
    <td><?php echo(($scale1[$wtarget])); ?></td>
    <td><?php echo(floatval(end($scale1)) - floatval($scale1[$wtarget]))  ; ?></td>
  </tr>
  
  <tr>
  <td bgcolor="#FF0000">Scala2</td>
    <td><?php echo((end($scale2))) ; ?></td>
    <td><?php echo(($scale2[$target])); ?></td>
    <td><?php echo(floatval(end($scale2)) - floatval($scale2[$target]))  ; ?></td>
    <td><?php echo(($scale2[$wtarget])); ?></td>
    <td><?php echo(floatval(end($scale2)) - floatval($scale2[$wtarget]))  ; ?></td>
  </tr>

  
  <tr>
  <td bgcolor="#FF0000">Scala3</td>
    <td><?php echo((end($scale3))) ; ?></td>
    <td><?php echo(($scale3[$target])); ?></td>
    <td><?php echo(floatval(end($scale3)) - floatval($scale3[$target]))  ; ?></td>
    <td><?php echo(($scale3[$wtarget])); ?></td>
    <td><?php echo(floatval(end($scale3)) - floatval($scale3[$wtarget]))  ; ?></td>
  </tr>

  
  <tr>
  <td bgcolor="#FF0000">Scala4</td>
    <td><?php echo((end($scale4))) ; ?></td>
    <td><?php echo(($scale4[$target])); ?></td>
    <td><?php echo(floatval(end($scale4)) - floatval($scale4[$target]))  ; ?></td>
    <td><?php echo(($scale4[$wtarget])); ?></td>
    <td><?php echo(floatval(end($scale4)) - floatval($scale4[$wtarget]))  ; ?></td>
  </tr>

  
  <tr>
  <td bgcolor="#FF0000">Scala5</td>
    <td><?php echo((end($scale5))) ; ?></td>
    <td><?php echo(($scale5[$target])); ?></td>
    <td><?php echo(floatval(end($scale5)) - floatval($scale5[$target]))  ; ?></td>
    <td><?php echo(($scale5[$wtarget])); ?></td>
    <td><?php echo(floatval(end($scale5)) - floatval($scale5[$wtarget]))  ; ?></td>
  </tr>


</table> 
<!-- <div id="canvas-container"> -->
<!-- <canvas id="myChart" height=100% width=200% align="center"></canvas> -->
</div>
<script>
function drawGraph() {


drawGraph();
 //setInterval(drawGraph,5000);
</script>
</body>
</html>

 
