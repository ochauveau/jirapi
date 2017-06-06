<?

include('inc/inc.config.php');
include('class/class.Divers.php');
include('class/class.Dates.php');
include('class/class.Jira.php');

$tab_sprintId 	= Jira::getSprintX(617); 
$tab_var 		= array();

function dateByColumn($array){
	$datesList2 = [];
	foreach ($array as $key => $detailSprintList){
		foreach ($detailSprintList as $key2 => $detailSprint){
			$startDate 			= $detailSprint['startDate'];
			$endDate 			= $detailSprint['endDate'];
			$val 				= "Du ".$startDate."<br>au ".$endDate;
			$datesList2[$val][$key]	= array($detailSprint['sprintName'], $detailSprint['velocity_and_engagement'], $detailSprint['velocity'], $detailSprint['engagement']);
		}
	}
	return $datesList2;
}

foreach ($tab_sprintId as $key => $sprint) {
	foreach ($sprint as $key1 => $sprintId) {
		$start 										= $sprintId['startDate'];
		$end   										= $sprintId['endDate'];
		$affiche									= array();
		$Issues 									= Jira::getIssues($sprintId['id']);
		$details 									= Jira::getIssueDetails($Issues);
		$velocity_and_engagement 					= Jira::count_velocity_and_engagement($details);
		$isSprintOK 								= Jira::isSprintOK($start, $end, $velocity_and_engagement['engagement'], $velocity_and_engagement['velocity']);
		$tab_var[$key][$key1]['id'] 				= $sprintId['id'];
		$tab_var[$key][$key1]['startDate'] 			= $isSprintOK['start'];
		$tab_var[$key][$key1]['endDate'] 			= $isSprintOK['end'];
		$tab_var[$key][$key1]['sprintName']			= $sprintId['name'];
		$tab_var[$key][$key1]['velocity_and_engagement'] 	= $velocity_and_engagement['velocity']." / ".$velocity_and_engagement['engagement'];
		$tab_var[$key][$key1]['velocity'] 			= $velocity_and_engagement['velocity'];
		$tab_var[$key][$key1]['engagement'] 		= $velocity_and_engagement['engagement'];
	}
}

Divers::print_array($tab_var, 1);
///////////////////
$arrayToTable = dateByColumn($tab_var);
Divers::print_array($arrayToTable, 1);
///////////////////
ksort($arrayToTable);
Divers::print_array($arrayToTable, 1);
///////////////////



$result = "<th class='unvisible'></th>";
$tr = array();
foreach($arrayToTable as $head => $body){
	$result .= "<th>".$head."</th>";
	foreach($body as $key => $val){
		$tr[$key][$head] = $val[1];
	}
}
$dates = array_keys($arrayToTable);
Divers::print_array($dates, 1);
///////////////////
$ligne ="";
//Divers::print_array($tr, 0);
foreach($tr as $date => $velo){
	$ligne .= "<tr><th>".$project_list['label'][$date]."</th>";
	foreach($dates as $index => $val){
		@$ligne .= "<td>".$velo[$val]."</td>";
	}
	$ligne .= "</tr>";
}

?>
<html>
<head></head>
<body>
<style>
	body 		{font-family: arial;  }
	table 		{border-collapse:collapse;}
	th, td 		{border:1px solid grey; width: 120px; font-size: 12px; text-align: center; padding:10px; }
	.fail 		{background-color: #EC7063; }
	th 			{background-color: #DCDCDC; }
	.unvisible 	{border:0px; background-color: white;}
	caption 	{font-size: 18px; padding:10px; }
</style>
<table>
 	<caption>Vélocité des équipes</caption>
	<tr><?=$result?></tr>
	<?=$ligne?>
</table>

</body>
</html>