<?php

class Dates {

	public static function getHolidays($year = null){
        if ($year === null){
        	$year = intval(strftime('%Y'));
        }

        $easterDate 	= easter_date($year);
        $easterDay 		= date('j', $easterDate);
        $easterMonth 	= date('n', $easterDate);
        $easterYear 	= date('Y', $easterDate);

        $holidays = array(
            // Jours feries fixes
            mktime(0, 0, 0, 1, 1, $year),// 1er janvier
            mktime(0, 0, 0, 5, 1, $year),// Fete du travail
            mktime(0, 0, 0, 5, 8, $year),// Victoire des allies
            mktime(0, 0, 0, 7, 14, $year),// Fete nationale
            mktime(0, 0, 0, 8, 15, $year),// Assomption
            mktime(0, 0, 0, 11, 1, $year),// Toussaint
            mktime(0, 0, 0, 11, 11, $year),// Armistice
            mktime(0, 0, 0, 12, 25, $year),// Noel

            // Jour feries qui dependent de paques
            mktime(0, 0, 0, $easterMonth, $easterDay + 1, $easterYear),// Lundi de paques
            mktime(0, 0, 0, $easterMonth, $easterDay + 39, $easterYear),// Ascension
            mktime(0, 0, 0, $easterMonth, $easterDay + 50, $easterYear), // Pentecote
        );

        sort($holidays);

        return $holidays;
	}


	public static function diffCalendar($firstDate, $secondDate){
        $oFirstDate = new DateTime($firstDate);
        $oSecondDate = new DateTime($secondDate);
        return (integer) ($oSecondDate->getTimestamp() - $oFirstDate->getTimestamp());
	}

	public static function diffWorkDay($firstDate, $secondDate){
		/*
	     * Pour calculer le nombre de jours ouvres,
	     * on calcule le nombre total de jours et
	     * on soustrait les jours fériés et les week end.
	     */
	    $iDiffCalendar = self::diffCalendar($firstDate, $secondDate);

	    $oFirstDate = new DateTime($firstDate);
	    $oSecondDate = new DateTime($secondDate);

	    $iFirstYear = $oFirstDate->format('Y');
	    $iSecondYear = $oSecondDate->format('Y');

	    $aHolidays = array();

	    /*
	     * Si l'interval demande chevauche plusieurs annees
	     * on doit avoir les jours feries de toutes ces annees
	     */
	    for ($iYear = $iFirstYear; $iYear <= $iSecondYear; $iYear++){
	    	$aHolidays = array_merge(self::getHolidays($iYear), $aHolidays);
	    }

	    /*
	     * On est oblige de convertir les timestamps en string a cause des decalages horaires.
	     */
	    $aHolidaysString = array_map(function ($value){
	    	return strftime('%Y-%m-%d', $value);
	    }, $aHolidays);

	    for ($i = $oFirstDate->getTimestamp(); $i < $oSecondDate->getTimestamp(); $i += 86400){
	        /* Numero du jour de la semaine, de 1 pour lundi a 7 pour dimanche */
	        $iDayNum = strftime('%u', $i);

	        if (in_array(strftime('%Y-%m-%d', $i), $aHolidaysString) OR $iDayNum == 6 OR $iDayNum == 7){
	            /* Si c'est ferie ou samedi ou dimanche, on soustrait le nombre de secondes dans une journee. */
	            $iDiffCalendar -= 86400;
	        }
	    }
	    
	    return (integer) $iDiffCalendar;
	}

	public static function openDays($start, $end){ 
		$secondes_ouvrees 	= self::diffWorkDay($start, $end); 
		$jours_ouvres		= round(self::seconds2days($secondes_ouvrees),1);
		return $jours_ouvres;
	}

	public static function seconds2days($seconds){
	    return $seconds/(60*60*24);
	}
}

?>
