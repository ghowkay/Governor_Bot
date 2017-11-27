<?php

require_once __DIR__.'/vendor/autoload.php';

// Instantiate the app
$settings = require __DIR__ . '/config.php';
$app = new \Slim\App($settings);
// Set up dependencies
require __DIR__ . '/dependencies.php';
require __DIR__ . '/DbHandler.php';
require __DIR__ . '/gov_scraper.php';



$app->post('/dialog_flow', function ($request, $response, $args) {
    

    $parsedBody = $request->getParsedBody();

    if(!$parsedBody['originalRequest']['source'] == 'facebook'){
        return;
    }
    

    //instantiate db class
     $db = new DbHandler($this->db);
    
    $resp = array();

// check if action is default welcome message
    if ($parsedBody['result']['action'] == 'input.welcome'){

        //instantiate guzzle for consuming apis
        $client = new GuzzleHttp\Client();

        //get username from facebook first
        $uid = $parsedBody['originalRequest']['data']['sender']['id'];

       $res = $client->request('GET', 'https://graph.facebook.com/v2.6/'.$uid,  ['query' => 'fields=first_name,last_name,profile_pic&access_token='.$this->get('settings')['facebook']['access_token']]);

       $user_obj = json_decode($res->getBody());
           //insert user
        if (!$db->checkUser($parsedBody['originalRequest']['data']['sender']['id'])) {
            $db->createUser($parsedBody['originalRequest']['data']['sender']['id'], $user_obj->first_name." ".$user_obj->last_name);
        }

        //insert BOT user
        if (!$db->checkUser($parsedBody['originalRequest']['data']['recipient']['id'])) {
            $db->createUser($parsedBody['originalRequest']['data']['recipient']['id'],"BOT");
        }


        //insert default welcome message and bot response as first messages

        $db->insertMessage( $parsedBody['originalRequest']['data']['sender']['id'], $parsedBody['originalRequest']['data']['recipient']['id'], $parsedBody['originalRequest']['data']['postback']['title']);      
           
        //inserts bot message into db
            $db->insertMessage( $parsedBody['originalRequest']['data']['recipient']['id'], $parsedBody['originalRequest']['data']['sender']['id'], $parsedBody['result']['fulfillment']['speech']);
         
    
    }


    //this action means the user has asked for a governor and we need to search the database to retrieve the information
    if ($parsedBody['result']['action'] == 'Govsearch.Govsearch-custom' && !$parsedBody['result']['actionIncomplete']){

        // insert customer message into db
         //inserts user message into db
     $db->insertMessage( $parsedBody['originalRequest']['data']['sender']['id'], $parsedBody['originalRequest']['data']['recipient']['id'], $parsedBody['originalRequest']['data']['message']['text']);
    

        //database query for governor
      $state = $parsedBody['result']['parameters']['State'];
       $todos =  $db->getGovernor($state);
       
        $resp = array(
            "speech"=> "The Governor of ".$todos->state." is ".$todos->governor.". He assumed office in ".$todos->elected." and is a member of ".$todos->party,
            "displayText" => "The Governor of ".$todos->state." is ".$todos->governor.". He assumed office in ".$todos->elected." and is a member of ".$todos->party,
            "data"=> array(),
            "contextOut"=> array(),
            "source"=> "facebook"
        );

          //inserts bot message into db
          $db->insertMessage( $parsedBody['originalRequest']['data']['recipient']['id'], $parsedBody['originalRequest']['data']['sender']['id'], $resp['speech']);
               

    return $response->withJson($resp, 201);
    }


    //check if action is to get todays date
    if ($parsedBody['result']['action'] == 'date.get'){

           // insert customer message into db
         //inserts user message into db
     $db->insertMessage( $parsedBody['originalRequest']['data']['sender']['id'], $parsedBody['originalRequest']['data']['recipient']['id'], $parsedBody['originalRequest']['data']['message']['text']);
     
       
         $resp = array(
             "speech"=> "Today's date is ". date("F j, Y"),
             "displayText" => "Today's date is ". date("F j, Y"),
             "data"=> array(),
             "contextOut"=> array(),
             "source"=> "facebook"
         );
 
           //inserts bot message into db
           $db->insertMessage( $parsedBody['originalRequest']['data']['recipient']['id'], $parsedBody['originalRequest']['data']['sender']['id'], $resp['speech']);
                
 
     return $response->withJson($resp, 201);

    }

    else{
    //inserts user message into db
    if(isset($parsedBody['originalRequest']['data']['message']['text'])){
        $db->insertMessage( $parsedBody['originalRequest']['data']['sender']['id'], $parsedBody['originalRequest']['data']['recipient']['id'], $parsedBody['originalRequest']['data']['message']['text']);
       
              //inserts bot message into db
           $db->insertMessage( $parsedBody['originalRequest']['data']['recipient']['id'], $parsedBody['originalRequest']['data']['sender']['id'], $parsedBody['result']['fulfillment']['speech']);
           }
   
   

    }

 //   print_r($parsedBody);
   
});



$app->get('/user_chat_history/{user_id}', function ($request, $response, $args) {

$bot_id = "1534352380226592";

$user_id = (string) $args['user_id'];

//print_r($user_id);

$db = new DbHandler($this->db);

$resp = $db->fetchChat($user_id,$bot_id);

return $response->withJson($resp, 200);

});

$app->get('/all_users', function ($request, $response, $args) {
    
    //print_r($user_id);
    
    $db = new DbHandler($this->db);
    
    $resp = $db->allUsers();
    
    return $response->withJson($resp, 200);
    
    });


    //calls the scraper class to get governors from wikipedia
    $app->get('/governor_list_wiki', function ($request, $response, $args) {
        
        //print_r($user_id);
        
        $scraper = new Scraper();
        
       $resp =  $scraper->init();

         $db = new DbHandler($this->db);

         foreach($resp as $gov){
            if (!$db->checkGov($gov['state'])){
          $db->insertGov($gov['state'],$gov['governor'],$gov['year'],$gov['party']);
         }
        }
        
        return $response->withJson($resp, 200);
        
        });
    

$app->run();

?>