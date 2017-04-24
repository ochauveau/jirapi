<?php

class Jira {

    public static function getSprint($projectId, $startId = 0){
        /* Récupère les id des sprints d'un board-équipe */
        global $url_prefixe;
        //$params = "/sprint?state=active&state=future";
        $params = "/sprint?state=active";
        $tablo = array();
        $sprints = Divers::curl($url_prefixe."agile/latest/board/".$projectId.$params, 4);
        foreach($sprints['values'] as $sprint){
            $originBoardId = $sprint['originBoardId'];
            $id = $sprint['id'];


            if(Jira::jiraDateIsPast($sprint['endDate'])){
    	        if(($originBoardId == $projectId)&&($id >= $startId)){
    	            
    	            $tab['id'] = $id;
    	            $tab['name'] = $sprint['name'];
    	            $tab['startDate'] = $sprint['startDate'];
    	            $tab['endDate'] = $sprint['endDate'];
    	            $tab['state'] = $sprint['state'];
    	            $tablo[] = $tab;
    	            
    	        }
    	    }
        }
        return $tablo;
    }


    public static function getSprintIdAll($startId = 0){
        global $project_list;
        foreach($project_list['rapidView'] as $projectId){
            $tab_sprintId = Jira::getSprint($projectId, $startId);
            $tab[] = $tab_sprintId;
        }
        return $tab;
    }

    public static function getIssues($sprintId){
        global $url_prefixe;
        $Issues = Divers::curl($url_prefixe."api/latest/search?jql=sprint=".$sprintId."&fields=customfield_11108,issuetype,status,customfield_11109", 12);
        return $Issues;
    }

    public static function getIssuesOutOfSprint($startDate, $endDate, $trigram){
        global $url_prefixe;
        $endDate    = date('Y-m-d', strtotime($endDate));
        $startDate  = date('Y-m-d', strtotime($startDate));

        $jql = "project = ".$trigram." and (status changed to (Done, 'Fermé(e)') during ('".$startDate."', '".$endDate."')) and issuetype not in ('Récit', 'Sous-tâche') AND Sprint = empty";
        $jql = urlencode($jql);

        $Issues = Divers::curl($url_prefixe.'api/latest/search?jql='.$jql.'&fields=issuetype,status', 25);
        return $Issues;
    }

    public static function getIssueDetails($issues){
        $tab = array();
        
        if($issues['issues']!=''){
            foreach ($issues['issues'] as $issue) {
                @$estimation = $issue['fields']['customfield_11108'];
                @$bv         = $issue['fields']['customfield_11109'];
                $etat        = $issue['fields']['status']['name'];
                $type        = $issue['fields']['issuetype']['name'];
                $key         = $issue['key'];

                if(!in_array($type, array('Sous-tâche'))){
                    $tab[] = array(
                        'etat' => $etat, 
                        'estim' => $estimation,
                        'bv' => $bv,
                        'type' => $type,
                        'key' => $key
                    );
                }
            }
            return $tab;
        }
    }

    public static function count_bug_and_debt($tab){
        $count_bug = 0;
        $count_debt = 0;
        if($tab){
            foreach ($tab as $item) {
                if ($item['type']=="Tâche"){
                    $count_debt++;
                }
                elseif ($item['type']=="Bogue"){
                    $count_bug++;
                }
            }
        }
        return array('bug' => $count_bug,'debt' => $count_debt);
    }

    public static function count_velocity_and_engagement($tab){
        $count_engagement = 0;
        $count_velocity = 0;
        $count_bv_total = 0;
        $count_bv_done = 0;
        foreach ($tab as $item) {
            $count_engagement += $item['estim'];
            $count_bv_total += $item['bv'];
            if(in_array($item['etat'], array('Fermé(e)','Fini'))){
                $count_velocity += $item['estim'];
                $count_bv_done += $item['bv'];
            }
        }
        return array(
            'velocity'=> $count_velocity, 
            'engagement' => $count_engagement,
            'bv_total'=> $count_bv_total,
            'bv_done'=> $count_bv_done
        );
    }

    public static function isSprintOK($startDate, $endDate, $storyPointsTotal, $storyPointsDone){
        $now        = date('Y-m-d');
        $endDate    = date('Y-m-d', strtotime($endDate));   //var_dump($endDate);
        $startDate  = date('Y-m-d', strtotime($startDate)); //var_dump($startDate);

        $diffDaysToNow  = Dates::diffWorkDay($now, $startDate);
        $diffDaysTotal  = Dates::diffWorkDay($endDate, $startDate);

        $percentDays    = (100-Divers::percent($diffDaysToNow, $diffDaysTotal));
        $percentPoints  = Divers::percent($storyPointsDone, $storyPointsTotal);

        $result = "OK";
        if ($percentPoints  < $percentDays){
            $result = "KO";
        }

        $tab_results = array(
            'result'        => $result,
            'end'           => $endDate, 
            'start'         => $startDate, 
            'now'           => $now,
            'diffDaysTotal' => $diffDaysToNow,
            'diffDaysToNow' => $diffDaysTotal,
            'percentDays'   => $percentDays,
            'percentPoints' => $percentPoints
        );
        return $tab_results;
    }

    public static function jiraDateIsPast($date){
    	$date 		= substr($date, 0, 10);
    	$date 		= new DateTime($date);
    	$now 		= new DateTime();
    	$interval 	= $date->diff($now);
    	$ecart 		= $interval->format('%R%a');
    	if($ecart<1){
    		return true;
    	}else{
    		return false;
    	}
    }
}

?>