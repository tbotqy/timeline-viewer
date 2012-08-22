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

        $countHyphen = substr_count($param,'-');
        $dateType = "";
           
        // detects the type of param by counting how many hyphens are contained
        switch($countHyphen){

        case 0:
            $dateType = "year";
            break;
        case 1:
            $dateType = "month";
            break;
        case 2:
            $dateType = "day";
            break;
        default:
            return false;
        }

        return $dateType;
    }
  
    public function termToTime($date,$dateType,$utcOffset){

        /**
         * convert given $date to unixtime with $utcOffset applied
         * @param string $date
         * @param string $dateType : such as year,month,day
         * @param int $utcOffset
         */

        $ret = "";

        switch($dateType){
        case 'year':
            $ret = $this->strToYearTerm($date,$utcOffset);
            break;
        case 'month':
            $ret = $this->strToMonthTerm($date,$utcOffset);
            break;
        case 'day':
            $ret = $this->strToDayTerm($date,$utcOffset);
            break;
        }
        
        return $ret;
    }

  
    private function strToYearTerm($strYear,$utcOffset){
        
        /**
         * given value is exected to be year format like 2012
         * convert given $strYear to unixtime with $utcOffset applied
         * returning array contains begin/end unixtime of given year
         * @param string $strYear like 2012
         * @param int $utcOffset
         * @return array if success , otherwise false
         */
        
        // create string representing the first day of year like 2012-1-1 00:00:00
        $strBegin = $strYear.'-1-1 00:00:00'; 
        $timeBegin = strtotime($strBegin) - $utcOffset;
        
        // create string representing the last moment of year like 2012-12-31 23:59:59
        $strEnd = $strYear.'-12-31 23:59:59';
        $timeEnd = strtotime($strEnd) - $utcOffset;

        $ret = array('begin'=>$timeBegin,'end'=>$timeEnd);
        return $ret;
    }

    private function strToMonthTerm($strMonth,$utcOffset){

        /**
         * given value is exected to be year format like 2012-2
         * convert given $strMonth to unixtime with $utcOffset applied
         * returning array contains begin/end unixtime of given month
         * @param string $strMonth like 2012-2
         * @param int $utcOffset
         * @return array if success , otherwise false
         */
         
        // create string representing the first day of month like 2012-2-1 00:00:00
        $strBegin = $strMonth.'-1 00:00:00'; 
        $timeBegin = strtotime($strBegin) - $utcOffset;
        
        // create string representing the last moment of month like 2012-2-29 23:59:59
        $lastDayOfMonth = date('t',strtotime($strMonth));
        $strMonth .= '-'.$lastDayOfMonth;
        $strEnd = $strMonth.' 23:59:59';
        $timeEnd = strtotime($strEnd) - $utcOffset;

        $ret = array('begin'=>$timeBegin,'end'=>$timeEnd);
        return $ret;
    }
    
    private function strToDayTerm($strDay,$utcOffset){
        
        /**
         * given value is exected to be day format like 2012-2-10
         * convert given $strDay to unixtime with $utcOffset applied
         * returning array contains begin/end unixtime of given day
         * @param string $strDay like 2012-2-10
         * @param int $utcOffset
         * @return array if success , otherwise false
         */
                 
        // create string representing the first monet of day like 2012-5-1 00:00:00
        $strBegin = $strDay.' 00:00:00'; 
        $timeBegin = strtotime($strBegin) - $utcOffset;
        
        // create string representing the last moment of day like 2012-5-1 23:59:59
        $strEnd = $strDay.' 23:59:59'; 
        $timeEnd = strtotime($strEnd) - $utcOffset;
        
        $ret = array('begin'=>$timeBegin,'end'=>$timeEnd);
        return $ret;
    }

}