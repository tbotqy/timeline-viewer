<?php

/**
 * /Controller/Component/ParameterComponent.php
 * component to deal with request params
 */

class ParameterComponent extends Component{

    function getParamType($param){
        
        /**
         * define given $param's type ( day,month,year etc)
         * @param string $param
         * @return string if succeed in detecting the type
         * @return false if failed in detecting the type
         */

        $count_hyphen = substr_count($param,'-');
        $date_type = "";
           
        // detects the type of param by counting how many hyphens are contained
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
  
    public function termToTime($date,$date_type,$utc_offset){

        /**
         * convert given $date to unixtime with $utc_offset applied
         * @param string $date
         * @param string $date_type : such as year,month,day
         * @param int $utc_offset
         */

        $ret = "";

        switch($date_type){
        case 'year':
            $ret = $this->strToYearTerm($date,$utc_offset);
            break;
        case 'month':
            $ret = $this->strToMonthTerm($date,$utc_offset);
            break;
        case 'day':
            $ret = $this->strToDayTerm($date,$utc_offset);
            break;
        }
        
        return $ret;
    }

  
    private function strToYearTerm($strYear,$utc_offset){
        
        /**
         * given value is exected to be year format like 2012
         * convert given $strYear to unixtime with $utc_offset applied
         * returning array contains begin/end unixtime of given year
         * @param string $strYear like 2012
         * @param int $utc_offset
         * @return array if success , otherwise false
         */
        
        // create string representing the first day of year like 2012-1-1 00:00:00
        $strBegin = $strYear.'-1-1 00:00:00'; 
        $timeBegin = strtotime($strBegin) - $utc_offset;
        
        // create string representing the last moment of year like 2012-12-31 23:59:59
        $strEnd = $strYear.'-12-31 23:59:59';
        $timeEnd = strtotime($strEnd) - $utc_offset;

        $ret = array('begin'=>$timeBegin,'end'=>$timeEnd);
        return $ret;
    }

    private function strToMonthTerm($strMonth,$utc_offset){

        /**
         * given value is exected to be year format like 2012-2
         * convert given $strMonth to unixtime with $utc_offset applied
         * returning array contains begin/end unixtime of given month
         * @param string $strMonth like 2012-2
         * @param int $utc_offset
         * @return array if success , otherwise false
         */
         
        // create string representing the first day of month like 2012-2-1 00:00:00
        $strBegin = $strMonth.'-1 00:00:00'; 
        $timeBegin = strtotime($strBegin) - $utc_offset;
        
        // create string representing the last moment of month like 2012-2-29 23:59:59
        $last_day_of_month = date('t',strtotime($strMonth));
        $strMonth .= '-'.$last_day_of_month;
        $strEnd = $strMonth.' 23:59:59';
        $timeEnd = strtotime($strEnd) - $utc_offset;

        $ret = array('begin'=>$timeBegin,'end'=>$timeEnd);
        return $ret;
    }
    
    private function strToDayTerm($strDay,$utc_offset){
        
        /**
         * given value is exected to be day format like 2012-2-10
         * convert given $strDay to unixtime with $utc_offset applied
         * returning array contains begin/end unixtime of given day
         * @param string $strDay like 2012-2-10
         * @param int $utc_offset
         * @return array if success , otherwise false
         */
                 
        // create string representing the first monet of day like 2012-5-1 00:00:00
        $strBegin = $strDay.' 00:00:00'; 
        $timeBegin = strtotime($strBegin) - $utc_offset;
        
        // create string representing the last moment of day like 2012-5-1 23:59:59
        $strEnd = $strDay.' 23:59:59'; 
        $timeEnd = strtotime($strEnd) - $utc_offset;
        
        $ret = array('begin'=>$timeBegin,'end'=>$timeEnd);
        return $ret;
    }

}