<?php

App::uses('Controller','Controller');
App::uses('View','View');
App::uses('LinkHelper','View/Helper');

class LinkHelperTest extends CakeTestCase{

    public function setUp(){

        parent::setUp();

        $Controller = new Controller();
        $View = new View($Controller);
        $this->Link = new LinkHelper($View);

    }

    public function testAddLinks(){
        
        $text = "@hogehoge Sitting in the bench all day looks kind of #boring http://www.example.com/boringman";
        
        // define cases
        $cases['hashtags'] = array(
                                   'Entity'=>array(
                                                   
                                                   'hashtag'=>'boring',
                                                   'type'=>'hashtags'
                                                   )
                                   );
        $cases['urls'] = array(
                               'Entity'=>array(
                                               'url' => 'http://www.example.com/boringman',
                                               'type'=>'urls'
                                               )
                               );
                                   
        $cases['user_mentions'] = array(
                                        'Entity'=>array(
                                                        'mention_to_screen_name' => 'hogehoge',
                                                        'type'=>'user_mentions'
                                                        )
                                        );
        $cases['exception'] = array(
                                    'Entity'=>array(
                                                    'name'=>'bench',
                                                    'type'=>'object'
                                                    )
                                    );
        
        //
        // test for case that entity only contains url
        //        
        $entities = $cases['urls'];
        $result = $this->Link->addLinks($text,$entities);
        $needle = "<a href=\"http://www.example.com/boringman\" target=\"_blank\">".$entities['Entity']['url']."</a>";
        $this->assertContains($needle,$result,$result);
    }

    public function testEntityLinks(){
        /*
         * case for url
         */

        $text = "Visit my website";
        $entity = "http://mywebsite.com";
        $entity_type = "urls";

        $result = $this->Link->addEntityLinks($text,$entity,$entity_type);

        $this->assertEquals($result,$text);
      
        /*
         * case for hashtag
         */
        $text = "Every day is a winding load #phrase";
        $entity = "phrase";
        $entity_type = "hashtags";
         
        $result = $this->Link->addEntityLinks($text,$entity,$entity_type);
        // create assertions
        $entity = "#".$entity;
        $href = "https://twitter.com/search?q=".urlencode($entity);
        $shouldContain = "<a href=\"".$href."\" target=\"_blank\">".$entity."</a>";
        $this->assertContains($shouldContain,$result,$result);

        /*
         * case for user mention
         */
        $text = "Every day is a winding load @me";
        $entity = "me";
        $entity_type = "user_mentions";

        $result = $this->Link->addEntityLinks($text,$entity,$entity_type);
        // create assertion
        $href = "https://twitter.com/".$entity;
        $entity = "@".$entity;
        $shouldContain = "<a href=\"".$href."\" target=\"_blank\">".$entity."</a>";
        $this->assertContains($shouldContain,$result,$entity_type);

        /*
         * case for unknown type
         */
        $text = "Google,Apple,MicroSoft";
        $entity = "Apple";
        $entity_type = "fruit";

        $result = $this->Link->addEntityLinks($text,$entity,$entity_type);
        $this->assertFalse($result,$entity_type);
    }
}