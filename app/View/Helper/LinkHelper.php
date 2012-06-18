<?php

App::uses('AppHelper','View/Helper');

class LinkHelper extends AppHelper{

    public $helpers = array('Text');

    public function addLinks($text,$entities){
        
        /*
         * This function adds anchor links to the given text.
         * Anchoring destination is hashtags(#hoge),medias(represented as url),and urls on $text.
         * Loads entities from given $entity and apply links to them.
         * @param string $text
         * @param array $entities
         * @return string if success, otherwise false
         */ 
        
        // return if $entities has no data
        if(count($entities) == 0){
            return $text;
        }
        
        // add link to urls using TextHelper
        $ret = $this->Text->autoLinkUrls($text,array('target'=>'_blank'));
  
        // add link to entities except urls
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

    public function addEntityLinks($text,$entity,$entity_type){
      
        /*
         * inserts anchor elements to given $text
         * @param string $text,$entity,$entity_type
         * @return string if seccess, otherwise false
         */

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
        $ret = str_replace($entity,$a_element,$text);
        
        return $ret;
    }
}
