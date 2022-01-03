<?php
$param['date'] = 9;
$param['beginRowCourses'] = 10;
$param['endRowCourses'] = 44;

$param['begin4BBRowCourses'] = 5;
$param['end4BBRowCourses'] = 39;

$param['beginColCourses'] = 2;
$param['endColCourses'] = 21;
$param['hour'] = 1;
$param['gap'] = 1;
$param['countTimezone'] = 18;
$param['beginDate'] = strtotime("2021-09-13");
$param['beginRowCM'] = 7;
$param['beginRowTPI'] = 8;
$param['beginRowTPL'] = 13;
/*
* IMPORT DATA
*/
$row = 1;
if (($handle = fopen("edt.csv", "r")) !== FALSE) {
    $array = [];
    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
    	$array[] = $data;
        $num = count($data);
        //echo "<p> $num champs à la ligne $row: <br /></p>\n";
        $row++;
        for ($c=0; $c < $num; $c++) {
            //echo $data[$c] . "<br />\n";
        }
    }
    fclose($handle);
}

$row = 1;
if (($handle = fopen("edt_4bb.csv", "r")) !== FALSE) {
    $csv_4bb_data = [];
    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
    	$csv_4bb_data[] = $data;
        $num = count($data);
        //echo "<p> $num champs à la ligne $row: <br /></p>\n";
        $row++;
        for ($c=0; $c < $num; $c++) {
            //echo $data[$c] . "<br />\n";
        }
    }
    fclose($handle);
}

$row = 1;
if (($handle = fopen("edt_cm.csv", "r")) !== FALSE) {
    $array_cm = [];
    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
    	$array_cm[] = $data;
        $num = count($data);
        //echo "<p> $num champs à la ligne $row: <br /></p>\n";
        $row++;
        for ($c=0; $c < $num; $c++) {
            //echo $data[$c] . "<br />\n";
        }
    }
    fclose($handle);
}

$row = 1;
if (($handle = fopen("edt_tpi.csv", "r")) !== FALSE) {
    $array_tpi = [];
    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
    	$array_tpi[] = $data;
        $num = count($data);
        //echo "<p> $num champs à la ligne $row: <br /></p>\n";
        $row++;
        for ($c=0; $c < $num; $c++) {
            //echo $data[$c] . "<br />\n";
        }
    }
    fclose($handle);
}

$row = 1;
if (($handle = fopen("edt_tpp.csv", "r")) !== FALSE) {
    $array_tpl = [];
    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
    	$array_tpl[] = $data;
        $num = count($data);
        //echo "<p> $num champs à la ligne $row: <br /></p>\n";
        $row++;
        for ($c=0; $c < $num; $c++) {
            //echo $data[$c] . "<br />\n";
        }
    }
    fclose($handle);
}

/*
* IMPORT FUNCTIONS
*/

include("compute_courses.php");
include("compute_time.php");
include("compute_rooms.php");
include("helpers.php");

$courses = get4BIMcourses($array,$param);

$courses4BB = get4BBcourses($csv_4bb_data,$param);

$rooms_cm = getCMRooms($array_cm,$param);

$rooms_tpi = getTPIRooms($array_tpi,$param);

$rooms_tpl = getTPLRooms($array_tpl,$param);

$dayTimestampTemplate = getDayTimestampTemplate($param);

$beginWeekTimestamp = getBeginWeekTimestamp($param);




//Finding the current course
$date = new DateTime(null, new DateTimeZone('Europe/Paris'));
$currentTime = ($date->getTimestamp() + $date->getOffset());//1637676001;//1638885601;//1636444801;//1637676001;//time();
$weekIndex = getClosestIndex($currentTime,$beginWeekTimestamp);
//echo "found week :".$timestampDate[$weekIndex];

$currentWeek = $dayTimestampTemplate;
for ($i=0; $i < count($currentWeek); $i++) { 
	$currentWeek[$i] += $beginWeekTimestamp[$weekIndex];
}
$hourIndex = getClosestIndex($currentTime,$currentWeek);

$all_rooms = array_merge($rooms_cm,$rooms_tpi,$rooms_tpl);


/*
echo "Le cours actuel est :".$courses[$hourIndex][$weekIndex];
echo "Il se situe en salle ".findCurrentRoom($all_rooms,$courses,"4BIM",$hourIndex,$weekIndex)."<br>";
*/

$currentCourse["4BIM"] = $courses[$hourIndex][$weekIndex];
$currentRoom["4BIM"] = findCurrentRoom($all_rooms,$courses,$hourIndex,$weekIndex);

$currentCourse["4BB"] = $courses4BB[$hourIndex][$weekIndex];
$currentRoom["4BB"] = findCurrentRoom($all_rooms,$courses4BB,$hourIndex,$weekIndex);

list($previousHourIndex,$previousWeekIndex) = getRelativeIndex($hourIndex,$weekIndex,-1,$param);

//echo "debug previous ".$previousHourIndex." ".$previousWeekIndex;

$previousCourse["4BIM"] = $courses[$previousHourIndex][$previousWeekIndex];
$previousRoom["4BIM"] = (!empty($previousCourse["4BIM"])?findCurrentRoom($all_rooms,$courses,$previousHourIndex,$previousWeekIndex):"Tu n'avais pas cours juste avant");

$previousCourse["4BB"] = $courses4BB[$previousHourIndex][$previousWeekIndex];
$previousRoom["4BB"] = (!empty($previousCourse["4BB"])?findCurrentRoom($all_rooms,$courses4BB,$previousHourIndex,$previousWeekIndex):"Tu n'avais pas cours juste avant");

list($nextHourIndex,$nextWeekIndex) = getRelativeIndex($hourIndex,$weekIndex,+1,$param);

$nextCourse["4BIM"] = $courses[$nextHourIndex][$nextWeekIndex];
$nextRoom["4BIM"] = (!empty($nextCourse["4BIM"])?findCurrentRoom($all_rooms,$courses,$nextHourIndex,$nextWeekIndex):"Tu n'as pas cours juste après (ou alors ton cours actuel dure plus de 2 heures)");

$nextCourse["4BB"] = $courses4BB[$nextHourIndex][$nextWeekIndex];
$nextRoom["4BB"] = (!empty($nextCourse["4BB"])?findCurrentRoom($all_rooms,$courses4BB,$nextHourIndex,$nextWeekIndex):"Tu n'as pas cours juste après (ou alors ton cours actuel dure plus de 2 heures)");


?>

<!DOCTYPE html>
<html>
<head>
	<title>WellTimetable</title>
	<style type="text/css">
		body{
			background: rgb(2,0,36);
			background: radial-gradient(circle, rgba(2,0,36,1) 0%, rgba(9,9,121,1) 35%, rgba(0,212,255,1) 100%);
			display: flex;
			justify-content: center;
			align-items: center;
			flex-direction: row;
			font-family: 'Roboto';
			height: 95vh;
		}
		.group-box{
			background-color: #fff;
			width: 300px;
			border-radius: 5px;
			padding: 10px;
			text-align: center;
			transition: all 0.5s;
			margin: 10px;
		}
		.group-box:hover{
			transform: scale(1.1);
			cursor: pointer;

		}
		.title{
			font-size: 36px;
			font-weight: bold;
		}
		.infos{
			border-top: 1px grey ridge;
			font-size: 20px;
			padding: 5px;
		}
	</style>
</head>
<body>

<?php
/*
echo "<tr>";
for ($i=0; $i < count($date); $i++) { 
	echo "<th>".$date[$i]."</th>";
}
echo "</tr>";

echo "<tr>";
for ($i=0; $i < count($date); $i++) { 
	echo "<th>".date("d/m/Y H:i",$timestampDate[$i])." ".$timestampDate[$i] ."</th>";
}
echo "</tr>";

for ($i=0; $i < count($courses); $i++) { 
	echo "<tr>";
	echo "<td>".$hours[$i]."</td>";
	for ($j=0; $j < count($courses[0]); $j++) { 
		echo "<td>".$courses[$i][$j].$i.$j."</td>";
	}
	echo "</tr>";
}
*/

?>
<div class="group-box">
	<div class="title">4BB</div>
	<div class="infos"><p><b>Cours actuel <br><i>(<?php echo date("d/m/Y H:i",$currentWeek[$hourIndex]); ?>)</i></b></p><p><?php echo $currentCourse["4BB"]; ?></p><p><?php echo $currentRoom["4BB"]; ?></p>
		</div>
	<div class="infos"><p><b>Cours précédent</b></p>
		<p><?php echo $previousCourse["4BB"]; ?></p><p><?php echo $previousRoom["4BB"]; ?></p></div>
	<div class="infos"><p><b>Cours suivant</b></p>
	<p><?php echo $nextCourse["4BB"]; ?></p><p><?php echo $nextRoom["4BB"]; ?></p></div>
</div>

<div class="group-box">
	<div class="title">4BIM</div>
	<div class="infos"><p><b>Cours actuel <br><i>(<?php echo date("d/m/Y H:i",$currentWeek[$hourIndex]); ?>)</i></b></p><p><?php echo $currentCourse["4BIM"]; ?></p><p><?php echo $currentRoom["4BIM"]; ?></p>
		</div>
	<div class="infos"><p><b>Cours précédent</b></p>
		<p><?php echo $previousCourse["4BIM"]; ?></p><p><?php echo $previousRoom["4BIM"]; ?></p></div>
	<div class="infos"><p><b>Cours suivant</b></p>
	<p><?php echo $nextCourse["4BIM"]; ?></p><p><?php echo $nextRoom["4BIM"]; ?></p></div>
</div>
</body>
</html>