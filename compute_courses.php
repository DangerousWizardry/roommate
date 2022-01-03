<?php
/*
* COMPUTE COURSES
*/
function get4BIMcourses($csv_data,$param){
	$courses = array();
	for ($row=$param['beginRowCourses']; $row < $param['endRowCourses']+1; $row=$row+$param['gap']+1) {
		$rowCourse = [];
		for ($col=$param['hour']+1; $col < $param['endColCourses']+1; $col++) { 
			$rowCourse[] = $csv_data[$row][$col];
		}
		$courses[] = $rowCourse;
	}
	return $courses;
}

function get4BBcourses($csv_data,$param){
	$courses = array();
	for ($row=$param['begin4BBRowCourses']; $row < $param['end4BBRowCourses']+1; $row=$row+$param['gap']+1) {
		$rowCourse = [];
		for ($col=$param['hour']+1; $col < $param['endColCourses']+1; $col++) { 
			$rowCourse[] = $csv_data[$row][$col];
			//echo $csv_data[$row][$col]. "($row,$col)";
		}
		$courses[] = $rowCourse;
	}
	return $courses;
}
