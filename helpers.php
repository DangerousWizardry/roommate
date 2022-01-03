<?php
function getClosestIndex($search, $arr) {
   $closest = null;
   for ($i=0; $i < count($arr); $i++) {
   		$item = $arr[$i]; 
      if ($closest === null || (abs($search - $closest) > abs($item - $search) && $item - $search < 0)) {
      	 
         $closest = $item;
         $closestIndex = $i;
      }
   }
   //echo date("d/m/Y H:i",$closest). "is closer ".$closestIndex." !";
   return $closestIndex;
}

function findCurrentRoom($rooms,$courses,$hourIndex,$weekIndex){
	$similarity = [];
	foreach ($rooms as $room_name => $room_edt) {
		//echo "room_ame = $room_name hourIndex : $hourIndex, weekIndex : $weekIndex";
		similar_text($courses[$hourIndex][$weekIndex], $room_edt[$hourIndex][$weekIndex], $perc);
		$similarity[$perc] = $room_name;
		//echo $room_name;
		
	}
	if (max(array_keys($similarity))>10) {
		return $similarity[max(array_keys($similarity))];
	}
	else{
		return "Aucune salle n'a été trouvée pour ce cours";
	}
	
}

function getRelativeIndex($hourIndex,$weekIndex,$delta,$param){
	$sign = ($delta>0 ? 1 : -1);
	for ($i=0; $i < abs($delta); $i++) { 
		$hourIndex += $sign;
		if ($hourIndex == -1) {
			$hourIndex = $param['countTimezone']-1;
			$weekIndex = $weekIndex -1;
		}
		else if($hourIndex == $param['countTimezone']){
			$hourIndex = 0;
			$weekIndex = $weekIndex +1;
		}
	}
	return array($hourIndex,$weekIndex);
}
?>