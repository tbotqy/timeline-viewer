<?php

App::uses('AppHelper','View/Helper');

class LinkHelper extends AppHelper{

    public $helpers = array('Text');

    public function addLinks($text,$entities){
        
        /*
         * This function adds anchor links to the given text.
         * Anchoring destination is hashtags(#hoge),medias(images),urls on $text.
         * Loads entities from given $entity and apply links to them.
         * Returns string.
         */ 
        
        // add link to urls
        $ret = $this->Text->autoLinkUrls($text);
  
        // add link to entities except urls
        foreach($entities as $entity){

            // check type of the entity
            $type = $entity['type'];
            
            // load entity body
            $entity_body = "";
            
            switch($type){
           
            case 'hashtags':
                $entity_body = $entity['hashtag'];
                break;
            case 'user_mentions':
                $entity_body = $entity['mention_to_screen_name'];
                break;
            default:
                break;
            }

            if($type != 'urls' || $type != 'media'){
                $ret = $this->addAnchorLinks($ret,$entity_body,$type);
            }
        }

        return $ret;
    }

    private function addAnchorLinks($tweet,$entity,$entity_type){
      
        /*
         * inserts anchor elements to given $tweet_body
         * returns string
         */

        // determine href 
        switch($entity_type){
        case 'urls':
        case 'media':
            $href = $entity;
            break;
        case 'hashtags':
            $entity = "#".$entity;
            $href = "https://twitter.com/search?q=#".urlencode($entity);
            break;
        case 'user_mentions':
            $href = "https://twitter.com/".$entity;
            $entity = "@".$entity;
            break;
        default:
            echo $entity_type;echo "<br/>";
        }

        // insert <a href=...></a>
        
        $a_element = "<a href=\"".$href."\" target=\"_blank\">".$entity."</a>";
        $ret = str_replace($entity,$a_element,$tweet);
        
        return $ret;
    }
}
