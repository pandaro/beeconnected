<?php 
require_once '../shared/PDO_Connector.php';
require_once '../model/DBMultyinsert0.2.php';

$after = $_GET['After']; 
$before = $_GET['Before'];
$sampling = $_GET['Sampling'];
$custom =$_GET['Custom'];
$choices = isset($_GET['choice']) ? $_GET['choice'] : array();
 //foreach($choices as $choice => $k) {


   //echo '<li>' . $choices . '</li>'; 
   //echo '<li>' . $k . '</li>';
   
 //};


$custom_params = 'WHERE 1';
if (!empty($custom)){
	echo "custom"."<br />";
	
	$custom_params = $custom;
}
else {

	if (!empty($after) ){
		//echo "after!"."<br />";
		$custom_params = $custom_params . " and date_time >  " ."'". $after."'";
	};

	if (!empty($before) ){
		//echo "before"."<br />";
		
		$custom_params = $custom_params . " and date_time <  " ."'". $before."'";
	};

	if (!empty($sampling)){
		//echo $sampling."<br />";
		
		$custom_params = $custom_params . " and HOUR(date_time) =  " . $sampling;
	};
};


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

// read the result of the select and save temperature and date

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
//echo($choices[0]);
// converts the array elements into a string
//$listed2 = array('Humidity' => $humidity,'Temperature'=>$temp_y)
$Id 		= implode ( ",", $id );
$Data 		= implode ( '","', $data );
$voltage	= implode ( ",", $volt);
$Temperature 	= implode ( ",", $temperature );
$Brightness 	= implode ( ",", $brightness);
$Humidity 	= implode ( ",", $humidity);
$Total_weight 	= implode ( ",", $total_weight );
$Middleweight 	= implode ( ",", $middleweight );
$Scale0		= implode ( ",", $scale0);
$Scale1		= implode ( ",", $scale1);
$Scale2		= implode ( ",", $scale2);
$Scale3		= implode ( ",", $scale3);
$Scale4		= implode ( ",", $scale4);
$Scale5		= implode ( ",", $scale5);
$Normal1	= implode ( ",", $norm1);
$Normal2	= implode ( ",", $norm2);
$Normal3	= implode ( ",", $norm3);
$Normal4	= implode ( ",", $norm4);
$Normal5	= implode ( ",", $norm5);


//$options = array($temperature,$brightness);
$options = array($temperature,$brightness);
$options2 = array(
	"id" => array($id,'ID',		'rgba(0,0,0,1)','rgba(250,250,250,0.0)'),			
	"date" => array($date,'date',	'rgba(0,0,0,0.5)','rgba(250,250,250,0.0)'),			
	"voltage" => array($volt,'volt',		'rgba(250,120,0,1)','rgba(0,250,250,0.0)'),
	
	"temperature" => array($temperature,	'temperature','rgba(250,0,0,0.5)','rgba(250,250,250,0.0)'),	
	"brightness" => array($brightness,	'brightness','rgba(240,0,220,0.5)','rgba(250,250,250,0.0)'),	
	"humidity" => array($humidity,	'humidity','rgba(0,250,0,1)','rgba(250,250,250,0.0)'),		
	"pressure" => array($pressure,	'pressure','rgba(0,0,250,1)','rgba(250,250,250,0.0)'),		
	"total_weight" => array($total_weight,	'total weight','rgba(250,250,0,1)','rgba(250,250,250,0.0)'),
	"middleweight" => array($middleweight,'	middleweight','rgba(0,250,250,1)','rgba(250,250,250,0.0)'),
	//SCALES
	
	"scale0" => array($scale0,'scale0',		'rgba(0,0,0,1)','rgba(250,250,250,0.0)'),		
	"scale1" => array($scale1,'scale1',		'rgba(120,250,250,1)','rgba(250,250,250,0.0)'),		
	"scale2" => array($scale2,'scale2',		'rgba(0,250,120,1)','rgba(0,250,250,0.0)'),		
	"scale3" => array($scale3,'scale3',		'rgba(120,250,120,1)','rgba(250,250,250,0.0)'),		
	"scale4" => array($scale4,'scale4',		'rgba(250,120,120,1)','rgba(250,250,250,0.0)'),		
	"scale5" => array($scale5,'scale5',		'rgba(250,120,0,1)','rgba(250,250,250,0.0)'),		
	"scale6" => array($scale6,'scale6',		'rgba(250,0,120,1)','rgba(250,250,250,0.0)'),		
	"scale7" => array($scale7,'scale7',		'rgba(0,120,250,1)','rgba(250,250,250,0.0)'),		
	"scale8" => array($scale8,'scale8',		'rgba(120,0,250,1)','rgba(250,250,250,0.0)'),		
	"scale9" => array($scale9,'scale9',		'rgba(120,120,250,1)','rgba(250,250,250,0.0)'),		
	"scale10" => array($scale10,'scale10',	'rgba(0,0,0,1)','rgba(250,250,250,0.0)'),	
	"scale11" => array($scale11,'scale11',	'rgba(0,0,0,1)','rgba(250,250,250,0.0)'),	
	"scale12" => array($scale12,'scale12',	'rgba(0,0,0,1)','rgba(250,250,250,0.0)'),	
	"scale13" => array($scale13,'scale13',	'rgba(0,0,0,1)','rgba(250,250,250,0.0)'),	
	"normal1" => array($norm1,'norn1',		'rgba(120,250,0,1)','rgba(250,250,250,0.0)'),		
	"normal2" => array($norm2,'norm2',		'rgba(0,250,120,1)','rgba(0,250,250,0.0)'),		
	"normal3" => array($norm3,'norm3',		'rgba(120,250,120,1)','rgba(0,250,250,0.0)'),		
	"normal4" => array($norm4,'norm4',		'rgba(250,120,120,1)','rgba(0,250,250,0.0)'),		
	"normal5" => array($norm5,'norm5',		'rgba(250,120,0,1)','rgba(0,250,250,0.0)'),		


	
);
//echo "fin chi";
?>
<!DOCTYPE html>
<html>
<head>

    <script src="../js/Chart.bundle.js"></script>
    <script src="../js/utils.js"></script>

    <style>
    canvas{
        -moz-user-select: none;
        -webkit-user-select: none;
        -ms-user-select: none;
    }
    </style>
</head>
<body>
<div id="canvas-container">
<canvas id="myChart" height=100% width=200% align="center"></canvas>
</div>
<script>
function drawGraph() {
y_axis=[];
choices = <?php echo json_encode($choices ); ?>;
options2 = <?php echo json_encode($options2 ); ?>;
//var i = 0 ;
for (var index in choices){
		
		y_axis[index] = { 
				
				label: options2[choices[index]][1], //cartellino
				fill: false, //riempie sotto
				backgroundColor:options2[choices[index]][2],//colore riempimento E DELLA LEGENDA SE NON LA SETTI
				//xAxisID:TODO
				//yAxisID:"asas",TODO
				//steppedLine:true,// fa le scalette
				lineTension:0.1, //curva
				borderJoinStyle:"round",//come ammorbidisce gli angoli
				//borderCapStyle:"square",TODO
				
				borderWidth:3,//spessore linea!!!!!
				borderColor: options2[choices[index]][2],//colore linea
				//borderDash:[], //lunghezza e spazio dei trattini
				//borderDashOffset:scostamento dei trattini
				pointBorderColor:options2[choices[index]][2],//colore bordo pallino
				pointBackgroundColor:options2[choices[index]][2], //colore sfondo del pallino
				pointBorderWidth:1, //dimensione bordo del pallino
				pointHoverRadius:5,//dimensione del punto quando ci passi sopra
				pointRadius:0, //dimensione del punto
				pointHitRadius:5,//non visibile, dimensione sensibile al passaggio//dimensiona su lunghezza dati 
				//pointHoverBackgroundColor://colore se ci passi sopra
				//pointHoverBorderColor://colore bordo punto se ci passi sopra
				//pointHoverBorderWidth://sempre quela menada
				pointStyle:"line",
				//showLine:true,//mostra o no la linea
				//spanGaps://riempie NULL e dati mancanti
				
				data:options2[choices[index]][0]
			};
	
//i = i + 1;
};

var data = { 

labels: [<?php echo '"'.$Data.'"'; ?>],
datasets: y_axis, 
}; 
var ctx = document.getElementById("myChart");
var myChart = new Chart(ctx, {
    type: 'line',
    data: data,

    options: {
	responsive: true,
	tooltips:{
		mode:"x-axis"
	},
	legend: {
		display:true,

	},
	title: {
            display: true,
            text: 'Custom overview'
        },
        scales: {
            yAxes: [{
                ticks: {
                    beginAtZero:false
                }
            }]
        }
    }
});
}
drawGraph();
 //setInterval(drawGraph,5000);
</script>
</body>
</html>

