<?php
function getBeginWeekTimestamp($param){
	$timestampDate = array();
	for ($i=$param['hour']+1; $i < $param['endColCourses']+1; $i++) { 
		$timestampDate[] = $param['beginDate'] + 604800 * ($i - $param['hour'] - 1);
	}
	return $timestampDate;
}

function getDayTimestampTemplate($param){
	$hours = array();
	$dayStatus=0;
	$currentTime = 28800;
	for ($row=$param['beginRowCourses']; $row < $param['endRowCourses']+1; $row=$row+$param['gap']+1) {
		$hours[] = $currentTime;
		$currentTime+=7200;
		if ($dayStatus==1) {
			$currentTime+=7200;
		}
		else if($dayStatus==3){
			$currentTime+=50400;
			$dayStatus=-1;
		}
		$dayStatus +=1;
	}
	return $hours;
}
?>