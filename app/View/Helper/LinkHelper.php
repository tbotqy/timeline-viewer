<?php

/**
 * View/Helper/LinkHelper.php
 */

App::uses('AppHelper','View/Helper');

class LinkHelper extends AppHelper{

    public $helpers = array('Text');

    public function removeNumParam($path){
        
        /**
         * remove numeric params from given path
         */

        // check the type of path
        $typeList = array(
                          'tweets',
                          'home_timeline',
                          'public_timeline'
                          );
        // check if given path includes any value in $typeLits
        $isDestType = false;
        $destType = "";
        foreach($typeList as $type){
        
            if(strpos($path,$type) != false){
                $isDestType = true;
                $destType = $type;
                break;
            }

        }

        if(!$isDestType){
            return $path;
        }

        switch($destType){
        case 'tweets':
        case 'home_timeline':
            $ret = $this->removeThirdParam($path);
            break;
        case 'public_timeline':
            $ret = $this->removeSecondParam($path);
            break;
        default:
            return "unknown type detected";
        }
                
        return $ret;
    }

    public function removeSecondParam($path){
        /**
         * remove second param from given path
         */
        // check if given path includes two slashes
        $countSlashes = substr_count($path,'/');
        if($countSlashes < 2){
            return $path;
        }

        // remove second param from given path
        $posFirst = strpos($path,'/');
        $posSecond = strpos($path,'/',$posFirst+1);
        $ret = substr($path,$posFirst,$posSecond);

        return $ret;

    }

    public function removeThirdParam($path){
        /**
         * remove third param from given path
         */

        // check if given path includes three slashes
        $countSlashes = substr_count($path, '/');
        if($countSlashes < 3){
            return $path;
        }

        // remove third param from given path
        $posFirst = strpos($path,'/');
        $posSecond = strpos($path,'/',$posFirst+1);
        $posThird = strpos($path,'/',$posSecond+1);
        $ret = substr($path,$posFirst,$posThird);

        return $ret;

    }

    public function addLinks($text,$entities){
        
        // linkify urls
        //$text = preg_replace('/(https?:\/\/[-_.!~*\'()a-zA-Z0-9;\/?:@&=+$,%#]+)/', '<a href="$1" target="_blank">$1</a>', $text);
        
        foreach($entities as $entity){
            if( $entity['url'] != "" ){
                $text = str_replace($entity['url'],"<a href='".$entity['url']."' target='_blank'>".$entity['display_url']."</a>",$text);
            }
        }

        // linkify user mentions
        $text= preg_replace("/@(\w+)/", "<a href=\"https://twitter.com/\\1\" target=\"_blank\">@\\1</a>", $text);
        
        // linkify hashtags
        $text= preg_replace("/#(\w+)/", "<a href=\"http://twitter.com/search?q=%23\\1\" target=\"_blank\">#\\1</a>", $text);
       
        return $text;        
       
    }

    //public function addLinks($text,$entities){
        
    /*
     * This function adds anchor links to the given text.
     * Anchoring destination is hashtags(#hoge),medias(represented as url),and urls on $text.
     * Loads entities from given $entity and apply links to those on given $text.
     * @param string $text
     * @param array $entities
     * @return string if success, otherwise false
     */ 

    /*
    // add links to urls using TextHelper
    $ret = $this->autoLink($text);
        
    // add links to entities except urls
    foreach($entities as $entity){

    // check type of the entity
    $type = $entity['type'];
            
    // load entity body
    $entity_body = "";
            
    switch($type){
    case 'urls':
    case 'media':
    break;
    case 'hashtags':
    $entity_body = $entity['hashtag'];
    break;
    case 'user_mentions':
    $entity_body = $entity['mention_to_screen_name'];
    break;
    default:
    return false;
    }

    if($type != 'urls' || $type != 'media'){
    // since links for urls are already added,here just deal with hashtags or user mentions
    $ret = $this->addEntityLinks($ret,$entity_body,$type);
    }
    }

    return $ret;
    }
    */

    //private function addEntityLinks($text,$entity,$entity_type){
      
    /*
     * inserts anchor elements to given $text
     * @param string $text,$entity,$entity_type
     * @return string if seccess, otherwise false
     */
    /*
    // determine href 
    switch($entity_type){
    case 'urls':
    case 'media':
    // return with nothing done
    return $text;
    case 'hashtags':
    $entity = "#".$entity;
    $href = "https://twitter.com/search?q=".urlencode($entity);
    break;
    case 'user_mentions':
    $href = "https://twitter.com/".$entity;
    $entity = "@".$entity;
    break;
    default:
    return false;
    }

    // insert <a href=...></a>
    $a_element = "<a href=\"".$href."\" target=\"_blank\">".$entity."</a>";
    $ret = str_ireplace($entity,$a_element,$text);
        
    return $ret;
    }
    */
}
