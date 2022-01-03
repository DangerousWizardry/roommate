<?php

/*
* COMPUTE CM ROOMS
*/
function getCMRooms($csv_data,$param){
	$rooms_cm = array();
	$rooms_row_starter = array();
	//Find row where a room table begin
	foreach ($csv_data as $key => $row) {
		if ($key>=$param['beginRowTPI'] && preg_match('/Salle/',$row[0])) {
			$rooms_row_starter[$key] = $row[0];
		}
	}

	foreach ($rooms_row_starter as $key => $room_name) {
		$current_cm=[];
		for ($row=$key+3; $row < $key + $param['endRowCourses'] - $param['beginRowCourses']+3; $row=$row+$param['gap']+1) {
			$rowRoom = [];
			for ($col=$param['hour']+1; $col < $param['endColCourses']+1; $col++) { 
				//echo "row : $row, col : $col";
				$rowRoom[] = $csv_data[$row][$col];
			}
			$current_cm[] = $rowRoom;
		}
		$rooms_cm[$room_name] = $current_cm;
	}
	return $rooms_cm;
}



/*
* COMPUTE IT TP ROOMS
*/
function getTPIRooms($csv_data,$param){
	$rooms_tpi = array();
	$rooms_row_starter = array();
	//Find row where a room table begin
	foreach ($csv_data as $key => $row) {
		if ($key>=$param['beginRowCM'] && preg_match('/Salle/',$row[0])) {
			$rooms_row_starter[$key] = $row[0];
		}
	}

	foreach ($rooms_row_starter as $key => $room_name) {
		$current_tpi=[];
		for ($row=$key+3; $row < $key + $param['endRowCourses'] - $param['beginRowCourses']+3; $row=$row+$param['gap']+1) {
			$rowRoom = [];
			for ($col=$param['hour']+1; $col < $param['endColCourses']+1; $col++) { 
				//echo "row : $row, col : $col";
				$rowRoom[] = $csv_data[$row][$col];
			}
			$current_tpi[] = $rowRoom;
		}
		$rooms_tpi[$room_name] = $current_tpi;
	}
	return $rooms_tpi;
}


/*
* COMPUTE LAB TP ROOMS
*/
function getTPLRooms($csv_data,$param){
	$rooms_tpl = array();
	$rooms_row_starter = array();
	//Find row where a room table begin
	foreach ($csv_data as $key => $row) {
		if ($key>=$param['beginRowTPL'] && preg_match('/Salle/',$row[0])) {
			$rooms_row_starter[$key] = $row[0];
		}
	}

	foreach ($rooms_row_starter as $key => $room_name) {
		$current_tpl=[];
		for ($row=$key+3; $row < $key + $param['endRowCourses'] - $param['beginRowCourses']+3; $row=$row+$param['gap']+1) {
			$rowRoom = [];
			for ($col=$param['hour']+1; $col < $param['endColCourses']+1; $col++) { 
				//echo "row : $row, col : $col";
				$rowRoom[] = $csv_data[$row][$col];
			}
			$current_tpl[] = $rowRoom;
		}
		$rooms_tpl[$room_name] = $current_tpl;
	}
	return $rooms_tpl;
}
?>