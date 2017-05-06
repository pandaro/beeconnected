<?php 

require_once '../shared/Logger.php';
require_once '../shared/Temperature_Config0.1.php';

class DBManager {
	private $dbh;
	//print(":weigth1");
    
    const _SAVE_DATA = "INSERT INTO 
    apidata(temperature,luminosity,humidity,pressure,total_weight,middleweight,weight1,weight2,weight3,weight4,weight5,weight6,weight7,weight8,weight9,weight10,weight11,weight12,weight13,Normal1,Normal2,Normal3,Normal4,) 
    VALUES(:temperature,:brightness,:humidity,:pressure,:tw,:mw,:1,:2,:3,:4,:5,:6,:7,:8,:9,:10,:11,:12,:13,:1N,:2N,3N,4N)";
    
    
	//origconst _SELECT_TEMPERATURE = "SELECT weight1,weight2,luminosity,temperature,date_format(date_time, '%k:%i') date_time FROM `apidata`";
	const _SELECT_TEMPERATURE = "SELECT * FROM `apidata`";
	//rangedate const _SELECT_TEMPERATURE = "SELECT * FROM `apidata` WHERE date_time BETWEEN '2017-01-13 00:00:00' and '2017-01-13 06:00:00' ";
	//in a hour const _SELECT_TEMPERATURE = "SELECT * FROM `apidata` WHERE  HOUR(`date_time`) =  9 ";
	//all data between hour range const _SELECT_TEMPERATURE = "SELECT * FROM `apidata` WHERE HOUR(date_time) between 12 and 18 ";

	public function __construct($conn) {
		$this->dbh = $conn;
	}
	public function custom_query($custom_params){
		$query="SELECT * FROM `apidata`".$custom_params;
		return $query;
	}
	
	
	public function ins_into_string($atmospheric,$weights,$db){
	$Values = '';
	$Colums = '';
	//echo $Values . "<br />";
	foreach ($atmospheric as $k => $value){
		//echo($value."<br />");
		$Values = $Values .":". $k . ",";
		if ($k == "brightness"){
			$k = "luminosity";
		};
		$Colums = $Colums .$k . ",";
	};
	//echo $Values . "<br />";
	echo $Colums."<br />";
	$Values = $Values . ':tw,:mw';
	$Colums = $Colums . 'total_weight,middleweight,';
	//echo $Values . "<br />";
	echo $Colums."1"."<br />";
	ECHO $Values."2"."<br />";
	foreach ($weights as $k => $value){
		list($index, $weight) = explode('w',$value);
		$Values = $Values . $index . ",";
		$Str = explode(':',$index);
		if (!empty($Str[1])){
			$Str = "weight".$Str[1].",";
			//echo $Str[1];
			$Colums = $Colums . $Str;
		};
	};
	ECHO $Values."<br />";
	$Values = rtrim($Values,',');//togle una virgola in più
	foreach ($weights as $k => $value){
		list($index, $weight) = explode('w',$value);
		echo "index = ".$index ;

		echo $Values."<br />";
		$Str = explode(':',$index);
		if (!empty($Str[1])){
		$nindex = ", ".$index ."N";
		$Values = $Values . $nindex ;
		$Str = "Normal".$Str[1].",";
			//echo $Str[1];
			$Colums = $Colums . $Str;
			ECHO $Colums."<br />";
		};
	};
	

	//echo $Values . "<br />";
	echo rtrim($Values,',')."<br />";
	$Values = rtrim($Values,',');//togle una virgola in più in fondo
	$Colums = rtrim($Colums,',');//togle una virgola in più in fondo
	echo $Colums."<br />";
	echo $Values."<br />";
	$now = time();
	echo("<br />".$db."<br />");
	$database = array('0' => 'apidata','1' => 'apiary2');
	
	$String = "INSERT INTO ".$database[$db]."(".$Colums.") VALUES(".$Values.")";
	
	
	echo("STRING " .$String ."<br />");
    return $String;
	}

	/**
	 * This function saves the temperature value on the db 
	 * @param string $temperature
	 * @throws Exception is used in case of exception
	 */
	public function save_data($db,$temperature,$brightness,$humidity,$pressure,$voltage,$weight0,$atmospheric,$weights) {
		try 	{
			echo("SAVE_DATA <br />");
			//self::ins_into_string($weights);
			$stmt = $this->dbh->prepare(self::ins_into_string($atmospheric,$weights,$db));
			$scale_number = 0;
			$totoal_weight = 0;
			$middleweight = 0;
			foreach( $weights as $K => $value){
				list($index, $weight) = explode('w',$value);
				if (!empty($index)){
				$weight = number_format(($weight) * 0.000037,3) ;
				$weight = $weight-19.0;
				echo($weight."<br />");
					$stmt->bindValue($index, $weight,PDO::PARAM_STR);
					echo('insert -> '.$index." ".$weight."<br />");
					//if (( $weight) > 0) { 
						$scale_number += 1;
						$total_weight += $weight;
					//}
				}
			}
			$middleweight = $total_weight / $scale_number;
			foreach( $weights as $K => $value){ //NORMALIZZAZIONE!!!!!!
				list($index, $weight) = explode('w',$value);
				$weight = $weight-19.0;
				if (!empty($index)){
				$nindex = $index."N";
				$weight = number_format((($weight * 0.000037) / $middleweight),3) ;
				
				echo($weight."<br />");
					$stmt->bindValue($nindex, $weight,PDO::PARAM_STR);
					echo('insert Normals -> '.$nindex.$weight."<br />");
					//if (( $weight) > 0) { 
						$scale_number += 1;
						$total_weight += $weight;
					//}
				}
			}	
			echo($middleweight. ' ' .$total_weight. ' ' ."<br />");
			$stmt->bindParam(":tw",$total_weight,PDO::PARAM_STR);
			$stmt->bindParam(":mw",$middleweight,PDO::PARAM_STR);
			$stmt->bindParam(":temperature",$temperature,PDO::PARAM_STR);
			$stmt->bindParam(":brightness",$brightness,PDO::PARAM_STR);
			$stmt->bindParam(":humidity",$humidity,PDO::PARAM_STR);
			$stmt->bindParam(":pressure",$pressure,PDO::PARAM_STR);
			$stmt->bindParam(":voltage",$voltage,PDO::PARAM_STR);
			$stmt->bindParam(":weight0",$weight0,PDO::PARAM_STR);
			echo("w0".$weight0."<br />");
			$stmt->execute();
			} 	
			catch (PDOException $ex) {
			Echo("ERRORERRORERRORERRORERRORERROR");
			Logger::log($ex->getCode(), $ex->getMessage());
			throw new Exception("Failed save data");
		}
	}
    

	/**
	 * This function recovers the temperature values
	 * @throws Exception is used in case of exception
	 * @return $result is a array with the result of the query
	 */
	public function select_temperature() {
		try {
		$stmt = $this->dbh->prepare(self::_SELECT_TEMPERATURE);
		$stmt->execute();
		$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
		return $result;
		} catch (PDOException $ex) {
			Logger::log($ex->getCode(), $ex->getMessage());
			throw new Exception("Impossible load last 5 posts, there was an error");
		}
	}
	public function custom_select($custom_params) {
		try {
		$stmt = $this->dbh->prepare(self::custom_query($custom_params));
		$stmt->execute();
		$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
		return $result;
		} catch (PDOException $ex) {
			Logger::log($ex->getCode(), $ex->getMessage());
			throw new Exception("Impossible load last 5 posts, there was an error");
		}
	}
}
?>