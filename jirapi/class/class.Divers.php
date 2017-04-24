<?php

class Divers {
    public static function percent($number, $total){
        $percent = round(($number/$total)*100);
        return $percent;
    }

    public static function curl($url, $depth = 512){
        global $username, $password;
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_USERPWD, "$username:$password");
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($curl, CURLOPT_FRESH_CONNECT, true);
        curl_setopt($curl, CURLOPT_TIMEOUT, 1);

        $issue_list = (curl_exec($curl));
        curl_close($curl);
        return json_decode($issue_list, true, $depth);
    }

    public static function pluriel($number, $suffixe = 's'){
        if($number>1){
            return $suffixe;
        }
    }

    public static function print_array($array, $comment = 0){
        $pre = "<pre>"; $post = "</pre>";
        if ($comment){ 
            $pre    = "<!--".$pre;
            $post   = $post."-->";
        }
        echo $pre; print_r($array); echo $post;
    }

	public  static function getURLParams(){
	    $tab_params = array();
	    parse_str($_SERVER['QUERY_STRING'], $tab_params);
	    return $tab_params;
	}

    public static function fileName(){
        $return = substr(strrchr($_SERVER['SCRIPT_NAME'], "/"), 1);
        return $return;
    }

}

?>