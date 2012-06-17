<?php

class UrlComponent extends Component{

    /**
     * functions to deal with URL params
     */

    function getParamType($param){
        
        /*
         * define given $param's type ( day,month,year etc)
         * returns string if succeed in detecting the type
         * returns false if failed in detecting the type
         */

        $count_hyphen = substr_count($param,'-');
        $date_type = "";
           
        switch($count_hyphen){

        case 0:
            $date_type = "year";
            break;
        case 1:
            $date_type = "month";
            break;
        case 2:
            $date_type = "day";
            break;
        default:
            return false;
        }

        return $date_type;
    }
}