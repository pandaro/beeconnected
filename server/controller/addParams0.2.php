<?php 
require_once '../shared/PDO_Connector0.1.php';
require_once '../model/DBMultyinsert0.2.php';

// $time = time();
// echo($time."<br />");
// require_once '../shared/Temperature_Config0.1.php';
// class ADBManager {
// private $dbh;
// 
// 	public function custom_query(){
// 		$query="SELECT * FROM `apidata`";
// 		echo($query);
// 	}
// 
// };
// custom_query();

/*HIPOTESIS OF CONVENTIONS FOR MULTIPLE PARAMS MANAGE
data=123t19td0dh36hp5pb4bv0vr1.380r|s:1w117137s:2w119691s:3w82763s:4w102769s:5w360350
*/
echo "ADD PARAMS <br />";
//save in var the string with received data

$data = filter_input(INPUT_GET, 'data');

// extract the security CODE value from the carrier GET
$code = explode('t',$data);
$code = $code[0];
echo "code= ".$code."<br />";
if ($code != "123") {
	$data = "";
};

// extract the weights value from the carrier GET
list($data, $weights) = explode('|',$data);
$weights = explode('s',$weights);


echo 'data= '.$data.' weights= '.$weights."<br />";
//divide the data in subdata part




// extract the TEMPERATURE value from the carrier GET

$db = explode('d',$data);
echo($db."<br />");
$db = $db[1];
echo($db."<br />");
if ($db == "") {
	$db = '0';
};
echo "db= ".$db."<br />";

$temperature = explode('t',$data);
$temperature = $temperature[1];
echo "temperature= ".$temperature."<br />";

// extract the luminosity value from the carrier GET
$brightness = explode('b',$data);
$brightness = $brightness[1];
echo "brightness= ".$brightness."<br />";

// extract the humidity value from the carrier GET
$humidity = explode('h',$data);
$humidity = $humidity[1];
echo "humidity= ".$humidity."<br />";

// extract the pressure value from the carrier GET
$pressure = explode('p',$data);
$pressure = $pressure[1];
echo "pressure= ".$pressure."<br />";

// extract the VOLTAGE value from the carrier GET
$voltage = explode('v',$data);
$voltage = $voltage[1];
echo "voltage= ".$voltage."<br />";

// extract the REFERENCE CELL value from the carrier GET
$weight0 = explode('r',$data);
$weight0 = $weight0[1];
echo "weight0= ".$weight0."<br />";

//reinsert atmospheric data inside new array
$atmospheric = array("temperature" => $temperature ,"brightness" => $brightness, "humidity" => $humidity, "pressure" => $pressure, "voltage" => $voltage, "weight0" => $weight0 );


// create the connection to the db
$dbh = PDO_Connector::get_connection();
// create the object DBManager
$dbm = new DBManager($dbh);


try {
	// save the temperature value on the db
	$dbm->save_data($db,$temperature,$brightness,$humidity,$pressure,$voltage,$weight0,$atmospheric,$weights);
} catch (Exception $ex) {
	print "Error";
};



?> 
