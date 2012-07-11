<?php

/**
 * View/Helper/LinkHelper.php
 */

App::uses('AppHelper','View/Helper');

class LinkHelper extends AppHelper{

    public $helpers = array('Text');

    public function addLinks($text){

        // linkify urls
        $text = preg_replace('/(https?:\/\/[-_.!~*\'()a-zA-Z0-9;\/?:@&=+$,%#]+)/', '<a href="$1" target="_blank">$1</a>', $text);
        
        // linkify user mentions
        $text= preg_replace("/@(\w+)/", "<a href=\"https://twitter.com/\\1\" target=\"_blank\">@\\1</a>", $text);
        
        // linkify hashtags
        $text= preg_replace("/#(\w+)/", "<a href=\"http://search.twitter.com/search?q=%23\\1\" target=\"_blank\">#\\1</a>", $text);
       
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
