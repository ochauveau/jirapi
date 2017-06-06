<?php

include('inc/inc.config.php');
include('class/class.Divers.php');
include('class/class.Dates.php');
include('class/class.Jira.php');

$file_name = Divers::fileName();

$team_label 	= $project_list['label']; 
$tab_sprintId 	= Jira::getSprintIdAll(616); 

//print_r(Jira::getSprintIdAll());
//print_r(Jira::getSprintIdAll(-1));
$tab_var 		= array();

foreach ($tab_sprintId as $key => $sprint) {

	$tab_var[$key]['team'] 						= $team_label[$key];
	$tab_var[$key]['result'] 					= 'NC';
	$tab_var[$key]['sprintName'] 				= '';
	$tab_var[$key]['velocity_and_engagement'] 	= '<small>Aucun sprint en cours sur Jira</small>';
	$tab_var[$key]['bv_done'] 					= '';
	$tab_var[$key]['bv_total'] 					= '';
	$tab_var[$key]['bug_and_debt'] 				= "";
	$tab_var[$key]['bv_done_and_total'] 		= "";
	$tab_var[$key]['caf'] 						= "";
	$tab_var[$key]['label_item_effotfull']		= $tab_var[$key]['label_item_effotless'] = "";
	$affiche									= array();

	foreach ($sprint as $key1 => $sprintId) {
		$affiche					= array();
		$start 						= $sprintId['startDate'];
		$end   						= $sprintId['endDate'];
		$tab_var[$key]['startDate'] = $start;
		$tab_var[$key]['endDate'] 	= $end;
		$Issues 					= Jira::getIssues($sprintId['id']);
		
		$OutOfSprint 				= @Jira::getIssuesOutOfSprint($start, $end, $project_list['name'][$key]);
		$details 					= @Jira::getIssueDetails($Issues);
		$detailsOutOfSprint			= @Jira::getIssueDetails($OutOfSprint);  

		$velocity_and_engagement 	= Jira::count_velocity_and_engagement($details);

		$count_bug_and_debt			= Jira::count_bug_and_debt($detailsOutOfSprint);
		$isSprintOK 				= Jira::isSprintOK($end, $start, $velocity_and_engagement['engagement'], $velocity_and_engagement['velocity']);

		$tab_var[$key]['details']					= $details;
		$tab_var[$key]['list_item_effotfull']		= $velocity_and_engagement['list_item_effotfull'];
		$tab_var[$key]['list_item_effotless']		= $velocity_and_engagement['list_item_effotless'];

		$tab_var[$key]['detailsOutOfSprint'] 		= $detailsOutOfSprint;
		$tab_var[$key]['bv_total'] 					= $velocity_and_engagement['bv_total'];
		$tab_var[$key]['bv_done'] 					= $velocity_and_engagement['bv_done'];
		$tab_var[$key]['sprintName']				= $sprintId['name'];
		$tab_var[$key]['result'] 					= $isSprintOK['result'];
		$tab_var[$key]['resultall'] 				= $isSprintOK;
		$tab_var[$key]['velocity_and_engagement'] 	= "Done : ".$velocity_and_engagement['velocity']."<span class='mini'>pt".Divers::pluriel($velocity_and_engagement['velocity'])."</span> / ".$velocity_and_engagement['engagement'];

		if($count_bug_and_debt['bug']>0){ 
			$affiche[] = $count_bug_and_debt['bug']." <span class='mini'>bug".Divers::pluriel($count_bug_and_debt['bug'])."</span>"; 
		}
		if($count_bug_and_debt['debt']>0){ 
			$affiche[] = $count_bug_and_debt['debt']." <span class='mini'>tâche".Divers::pluriel($count_bug_and_debt['debt'])."</span>"; 
		}

		$tab_var[$key]['bug_and_debt'] 		=  implode(" / ", $affiche);
		if ($tab_var[$key]['bug_and_debt']){ 
			$tab_var[$key]['bug_and_debt'] = "Vie courante : ".$tab_var[$key]['bug_and_debt'];
		}
		if ($velocity_and_engagement['bv_total']>0){ 
			$tab_var[$key]['bv_done_and_total'] = "BV : ".$velocity_and_engagement['bv_done']." / ".$velocity_and_engagement['bv_total']; 
		}
		if ($tab_var[$key]['list_item_effotfull']){
			$tab_var[$key]['label_item_effotfull'] .= "Le sprint contient ".Jira::format_sprint_content($tab_var[$key]['list_item_effotfull']);
		}
		if (!empty($tab_var[$key]['list_item_effotless'])){
			$tab_var[$key]['label_item_effotless'] .= "(non estimé : ".Jira::format_sprint_content($tab_var[$key]['list_item_effotless']).")";
		}

	}
}

Divers::print_array($tab_var, 1);
include('view/'.$file_name);

?>