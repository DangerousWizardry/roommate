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
<!DOCTYPE html>
<html>
<head>
	<title>WellTimetable</title>
	<link rel="stylesheet" href="css/foundation.css">
    <link rel="stylesheet" href="css/app.css">
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>
<header>
<div class="title-bar" data-responsive-toggle="responsive-menu" data-hide-for="medium">
  <button class="menu-icon" type="button" data-toggle="responsive-menu"></button>
  <div class="title-bar-title">Menu</div>
</div>

<div class="top-bar" id="responsive-menu">
  <div class="top-bar-left">
    <ul class="menu">
      <li class="menu-text"><a href="/">Roommate 🚪</a></li>
    </ul>
  </div>
  <div class="top-bar-right">
    <ul class="menu">
      <li><a href="#4BB">4BB</a></li>
      <li><a href="#4BIM">4BIM</a></li>
      <li><a href="about.php" class="button secondary clear">À propos</a></li>
    </ul>
  </div>
</div>

  <nav class="circular-menu dimmeable" id="navigator">
	   <a href="#" class="circular-menu-item primary-circle">3BIM</a>
	   <a href="#4BIM" class="circular-menu-item primary-circle">4BIM</a>
	   <a href="#" class="circular-menu-item primary-circle">5BIM</a>
	   <a href="#" class="circular-menu-item secondary-circle">3BB</a>
	   <a href="#4BB" class="circular-menu-item secondary-circle">4BB</a>
	   <a href="#" class="circular-menu-item secondary-circle">5BB</a>
	   <label>Choisissez votre groupe</label>
	</nav>

<div class="flex-container">
	<div class="group-box dimmeable" id="4BB">
	<div class="title">4BB</div>
	<div class="infos"><p><b>Cours actuel <br><i>(<?php echo date("d/m/Y H:i",$currentWeek[$hourIndex]); ?>)</i></b></p><p><?php echo $currentCourse["4BB"]; ?></p><p><?php echo $currentRoom["4BB"]; ?></p>
		</div>
	<div class="infos"><p><b>Cours précédent</b></p>
		<p><?php echo $previousCourse["4BB"]; ?></p><p><?php echo $previousRoom["4BB"]; ?></p></div>
	<div class="infos"><p><b>Cours suivant</b></p>
	<p><?php echo $nextCourse["4BB"]; ?></p><p><?php echo $nextRoom["4BB"]; ?></p></div>
</div>

<div class="group-box dimmeable" id="4BIM">
	<div class="title">4BIM</div>
	<div class="infos"><p><b>Cours actuel <br><i>(<?php echo date("d/m/Y H:i",$currentWeek[$hourIndex]); ?>)</i></b></p><p><?php echo $currentCourse["4BIM"]; ?></p><p><?php echo $currentRoom["4BIM"]; ?></p>
		</div>
	<div class="infos"><p><b>Cours précédent</b></p>
		<p><?php echo $previousCourse["4BIM"]; ?></p><p><?php echo $previousRoom["4BIM"]; ?></p></div>
	<div class="infos"><p><b>Cours suivant</b></p>
	<p><?php echo $nextCourse["4BIM"]; ?></p><p><?php echo $nextRoom["4BIM"]; ?></p></div>
</div>
</div>

</header>


    <script src="js/vendor/jquery.js"></script>
    <script src="js/vendor/what-input.js"></script>
    <script src="js/vendor/foundation.js"></script>
    <script src="js/app.js"></script>
</body>
</html>