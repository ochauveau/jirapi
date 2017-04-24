<?php

// Compte Jira
$username 		= '..........'; 		
$password 		= '..........';

// URL of Jira
$url_jira 	= "http://...........";

// Don't touch !!!
$url_prefixe = $url_jira.'rest/';

// Informations sur les projets et boards
$project_list = array(
	'projectKey' 	=> array('....', '....', '....', '....'),	// projectKey est l'id alphabétique du projet dans JIRA (cf la variable "projectKey" dans l'URL de Jira)
	'rapidView' 	=> array(...., ...., ...., ....),			// rapidView est l'identifiant numérique du tableau dans JIRA (cf la variable "rapidView" dans l'URL de Jira)
	'label' 		=> array('....', '....','....','....')		// label est le nom de l'équipe mis en avant dans jirapi
);

?>