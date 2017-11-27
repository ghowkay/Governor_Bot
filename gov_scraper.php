<?php
 /**
 *
 * Crawl wikipedia to get governors of states in nigeria
 *
 * @author Goke Odubanjo
 */

 require_once __DIR__.'/vendor/autoload.php';
 use Goutte\Client;


 class Scraper {
    
       private $conn;
       
       private $arr; 
       function __construct() {
        $client = new Client();
           $this->client = $client;
          // $this->init($this->client);
       }
 
public function init(){
    $array = array();
 // Go to the wikipedia.com website
$crawler =  $this->client->request('GET', 'https://en.wikipedia.org/wiki/List_of_state_governors_of_Nigeria');

// Get the latest post in this category and display the titles

$tbody = $crawler->filter('.wikitable tr');

//print_r($tbody->html());


$crawler->filter('.wikitable tr')->each(function($element, $i){

   // print_r($i);
  //  if($i == 1){
if($i > 0){

    try{
    
    $state_tokens = explode(' ',$element->filter('td')->eq(0)->text());
    $governor = $element->filter('td')->eq(1)->text();
    $party = $element->filter('td')->eq(3)->text();
    $year = $element->filter('td')->eq(4)->text();

    $this->arr[] = array(
        "state"=>$state_tokens[0],
        "governor"=>$governor,
        "party"=>$party,
        "year"=>$year
    );

    //print_r($array);

  //  return $array;
} catch(Exception $e) { // I guess its InvalidArgumentException in this case
    // Node list is empty
}


    //echo $state_tokens[0]."==".$governor."==".$party."==".$year."<br>";
    }
});
//print_r($this->arr);
return $this->arr;
}

}
?>