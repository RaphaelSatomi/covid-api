<?php
    $param = json_decode($_POST['param']);
    $param = explode("#", $param);

    switch ($param[0]){
        case 1:
            countries();
            break;
        case 2:
            select_country($param[1]);
            break;
        case 3:
            data_countries();
            break;
        default:
            print_r($param);
            echo "errp";
            break;
    }

    function countries(){
        $url = 'https://covid-193.p.rapidapi.com/countries';
        $options = array(
            'http' => array(
                'method'  => 'GET',
                'header'  => "Content-Type: application/json\r\n".
                            "x-rapidapi-host: covid-193.p.rapidapi.com\r\n".
                            "x-rapidapi-key: 2a4a2c70f1mshdc1b952fb832919p12b64ajsn95f193253864\r\n",
            )
        );
        $context  = stream_context_create($options);
        $response = @file_get_contents($url, false, $context);
    
        print_r($response);
    }

    function select_country($country){
        $url = 'https://covid-193.p.rapidapi.com/statistics?country='.$country;
        $options = array(
            'http' => array(
                'method'  => 'GET',
                'header'  => "Content-Type: application/json\r\n".
                            "x-rapidapi-host: covid-193.p.rapidapi.com\r\n".
                            "x-rapidapi-key: 2a4a2c70f1mshdc1b952fb832919p12b64ajsn95f193253864\r\n",
            )
        );
        $context  = stream_context_create($options);                                                     
        $response = @file_get_contents($url, false, $context);  
    
        print_r($response);
    }

    function data_countries(){
        $url = 'https://covid-193.p.rapidapi.com/statistics';
        $options = array(
            'http' => array(
                'method'  => 'GET',
                'header'  => "Content-Type: application/json\r\n".
                            "x-rapidapi-host: covid-193.p.rapidapi.com\r\n".
                            "x-rapidapi-key: 2a4a2c70f1mshdc1b952fb832919p12b64ajsn95f193253864\r\n",
            )
        );
        $context  = stream_context_create($options);                                                     
        $response = @file_get_contents($url, false, $context);  
        $json_array = json_decode($response);

        $array_countries = array();
        $array_continents = array();
        for($i = 0; $i < sizeof($json_array->response); $i++){
            if($json_array->response[$i]->continent !== $json_array->response[$i]->country){
                array_push($array_countries, $json_array->response[$i]);
            }else{
                array_push($array_continents, $json_array->response[$i]);
            }
        }
        $json_array->response = $array_countries;
        //print_r(json_encode($json_array->response));
        $arr = calc_deaths($json_array->response);
        $arr = calc_death_case($arr);

        $json_array->response = $arr;
        print_r(json_encode($json_array));
    }

    function calc_deaths($json_array){

        // PAÍSES COM MAIS MORTES
        function cmp($a, $b) {
            return $a->deaths->total - $b->deaths->total;
        }
        usort($json_array, 'cmp');

        $num = 1;
        for($i = (sizeof($json_array) - 1); $i > -1; $i--){
            if($i == (sizeof($json_array) - 1)){
                $json_array[$i]->pos_death_all = $num."#".$json_array[$i]->deaths->total;
            }else{
                if($json_array[$i]->deaths->total !== $json_array[$i + 1]->deaths->total){
                    $num++;
                    $json_array[$i]->pos_death_all = $num."#".$json_array[$i]->deaths->total;
                }else{
                    $json_array[$i]->pos_death_all = $num."#".$json_array[$i]->deaths->total;
                }
            }
        }

        // PAÍSES COM MAIS MORTES ÚLTIMO DIA
        function cmp2($a, $b) {
            return $a->deaths->new - $b->deaths->new;
        }
        usort($json_array, 'cmp2');

        $num = 1;
        for($i = (sizeof($json_array) - 1); $i > -1; $i--){
            if($i == (sizeof($json_array) - 1)){
                $json_array[$i]->pos_death_new = $num."#".$json_array[$i]->deaths->new;
            }else{
                if($json_array[$i]->deaths->total !== $json_array[$i + 1]->deaths->new){
                    $num++;
                    $json_array[$i]->pos_death_new = $num."#".$json_array[$i]->deaths->new;
                }else{
                    $json_array[$i]->pos_death_new = $num."#".$json_array[$i]->deaths->new;
                }
            }
        }
        return $json_array;
    }

    function calc_death_case($json_array){
        // PAÍSES COM MAIS CASOS
        function cmp3($a, $b) {
            return $a->cases->total - $b->cases->total;
        }
        usort($json_array, 'cmp3');

        $num = 1;
        for($i = (sizeof($json_array) - 1); $i > -1; $i--){
            if($i == (sizeof($json_array) - 1)){
                $json_array[$i]->pos_cases_all = $num."#".$json_array[$i]->cases->total;
            }else{
                if($json_array[$i]->cases->total !== $json_array[$i + 1]->cases->total){
                    $num++;
                    $json_array[$i]->pos_cases_all = $num."#".$json_array[$i]->cases->total;
                }else{
                    $json_array[$i]->pos_cases_all = $num."#".$json_array[$i]->cases->total;
                }
            }
        }
        
        
        // PAÍSES COM MAIS CASOS ÚLTIMO DIA
        function cmp4($a, $b) {
            return $a->cases->new - $b->cases->new;
        }
        usort($json_array, 'cmp4');

        $num = 1;
        for($i = (sizeof($json_array) - 1); $i > -1; $i--){
            if($i == (sizeof($json_array) - 1)){
                $json_array[$i]->pos_cases_new = $num."#".$json_array[$i]->cases->new;
            }else{
                if($json_array[$i]->cases->total !== $json_array[$i + 1]->cases->new){
                    $num++;
                    $json_array[$i]->pos_cases_new = $num."#".$json_array[$i]->cases->new;
                }else{
                    $json_array[$i]->pos_cases_new = $num."#".$json_array[$i]->cases->new;
                }
            }
        }
        return $json_array;
    }