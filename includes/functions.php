<?php
function sanitize_data($data){//take care of SQL injection
	$data = stripslashes(trim($data));
	return $data;
}

function semesterInterpreter($num=null)
{
	if ($num==1) {
		$meaning = "harmatan";
	}
	elseif ($num==2) {
		$meaning = "rain";
	}
	else{
		$meaning = "unavailable";
	}
	return $meaning;
}

function dayInterpreter($day_num=null)
{
	switch ($day_num) {
		case 1:
			$the_day = "Monday";
			break;

		case 2:
			$the_day = "Tuesday";
			break;
		
		case 3:
			$the_day = "Wednesday";
			break;

		case 4:
			$the_day = "Thursday";
			break;

		case 5:
			$the_day = "Friday";
			break;

		case 6:
			$the_day = "Saturday";
			break;

		default:
			$the_day = "Sunday";
			break;
	}
	return $the_day;
}

function check_dataset($data)
{
	if (empty($data)) {
		return "not set";
	}
	else{
		return $data;
	}
}

function unexpectedError(){
	$msg = "An unexpected error occured while trying to process your request. Click the 'back' button on your browser menu and retry. If the problem continues, please contact the site administrator.";
	return $msg;
}

//get returned input data
function get_data($var) {
	if (isset($_POST[$var]))
	{
		echo htmlspecialchars($_POST[$var]);
	}
	else {
		echo htmlspecialchars("");
	}
}

//pluralize value based on data supplied
function pluralize($data,$suffix){
	if(!is_numeric($data)) {
		$suffix = " unavailable";
	}
	else {
		if($data <= 1){
			$suffix = $data . " " . $suffix;
		}
		else {
			$suffix = $data . " " . $suffix . "s";
		}
	}
return $suffix;
}

//time difference function
function gettimediff($timestamp1, $timestamp2) {
	$dateconvert1 = date("Y-m-d H:i:s",$timestamp1);
	$dateconvert2 = date("Y-m-d H:i:s",$timestamp2);
	$date1 = new DateTime($dateconvert1);
	$date2 = new DateTime($dateconvert2);
	$interval = date_diff($date1, $date2);
	$second = $interval -> format('%s');
	$minute = $interval -> format('%i');
	$hour = $interval -> format('%h');
	$day = $interval -> format('%d');
	$month = $interval -> format('%m');
	$year = $interval -> format('%y');
	if($year>0 && $month>0){
		$timediff = pluralize($year,"yr") . " " . pluralize($month,"month");
	}
	elseif($year>0 && $month<1){
		$timediff = pluralize($year,"yr");
	}
	elseif($month>0 && $day>0){
		$timediff = pluralize($month,"month") . " " . pluralize($day,"day");
	}
	elseif($month>0 && $day<1){
		$timediff = pluralize($month,"month");
	}
	elseif($day>0 && $hour>0){
		$timediff = pluralize($day,"day") . " " . pluralize($hour,"hr");
	}
	elseif($day>0 && $hour<1){
		$timediff = pluralize($day,"day");
	}
	elseif($hour>0 && $minute>0){
		$timediff = pluralize($hour,"hr") . " " . pluralize($minute,"min");
	}
	elseif($hour>0 && $minute<1){
		$timediff = pluralize($hour,"hr");
	}
	elseif($minute>0 && $second>0){
		$timediff = pluralize($minute,"min") . " " . pluralize($second,"sec");
	}
	elseif($minute>0 && $second<1){
		$timediff = pluralize($minute,"min");
	}
	else {
		$timediff = pluralize($second,"sec");
	}
	return $timediff;
}

function ratingOnProgressBar($rate=0)
{
	//calculate percentage
	//for rating upon 5
	if (!is_numeric($rate)) {
		$reply = "Supplied rate value is not numeric!";
	}
	else{
		$reply = ($rate/5)*100;
	}
	return $reply;
}

function AVERAGE($values,$round_figure=0)
{
	$values = explode(",", $values);
	$count = count($values);
	$total = 0;
	foreach ($values as $value) {
		$total += $value;
	}
	$total;
	$average = $total/$count;
	if ($round_figure > 0) {
		$average = round($average,$round_figure);
	}
	return $average;
}

function showTimes($interval) {

	 if($interval<1) {

		$interval = 5;

	 }

	 $start = 00;

	 $end = 23;

	 $starts = 00;

	 $ends = 59;

	 for ($i=$start; $i<=$end; $i++)

	 {

		if(strlen($i)<2) {

			$i = '0' . $i;

		}

		

		for ($s=$starts; $s<=$ends; $s++)

		{

			if($s!==00) {

				$s = $s+$interval-1;

			}

			if(strlen($s)<2) {

				$s = '0' . $s;

			}

			if($s!==60) {

				echo '<option value=' . $i . ':' . $s . '>' . $i . ':' . $s . '</option>';

			}

		}

	 }

}
?>