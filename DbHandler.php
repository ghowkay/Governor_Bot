<?php
 
/**
 * Class to handle all db operations
 * This class will have CRUD methods for database tables
 *
 * @author Goke Odubanjo
 */
class DbHandler {
 
    private $conn;
 
    function __construct($client) {
      
        $this->conn = $client;
    }

//create the user
   public function createUser($id,$username){

        $sql = "INSERT INTO users (user_id,username) VALUES (:id,:username)";
        $sth = $this->conn->prepare($sql);
       $sth->bindParam("id", $id);
       $sth->bindParam("username", $username);
       $sth->execute();

    }

    //check if user exists to avoid duplicates
    public function checkUser($id){
        
                $sql = "SELECT count(*) FROM users WHERE user_id = :id";
                $sth = $this->conn->prepare($sql);
               $sth->bindParam("id", $id);
               $sth->execute();
               $num_rows = $sth->fetchColumn(); 
               return $num_rows > 0;
            }

            //inserts messages into db
    public function insertMessage($sender,$receiver,$message){

        $sql = "INSERT INTO message (message,from_user,to_user,message_datetime) VALUES (:message,:from_user,:to_user,NOW())";
        $sth = $this->conn->prepare($sql);
       $sth->bindParam("from_user", $sender);
        $sth->bindParam("to_user", $receiver);
       $sth->bindParam("message", $message);
       $sth->execute();
    }

    //fetch the governor and other info by state
    public function getGovernor($state){
        $sth = $this->conn->prepare("SELECT * FROM state_governors WHERE state LIKE :state");
        $sth->bindParam("state", $state);
        $sth->execute();
        $gov = $sth->fetchObject();


        return $gov;

    }

//get all users
    public function allUsers(){
        
                $sth = $this->conn->prepare("SELECT * FROM users WHERE username !='BOT' "); 
             
                  $sth->execute();
                  $users = $sth->fetchAll();
          
                  return $users;
            }


            //fetch chat from bot to user
    public function fetchChat($uid,$bid){

      $sth = $this->conn->prepare("SELECT * FROM message  WHERE (from_user = :uid AND to_user = :bid ) OR (from_user = :bid AND to_user = :uid) ORDER BY message_datetime"); 
        $sth->bindParam("uid", $uid);
        $sth->bindParam("bid", $bid);
        $sth->execute();
        $gov = $sth->fetchAll();

        return $gov;

    }

       //check if governor exists to avoid duplicates
       public function checkGov($id){
        
                $sql = "SELECT count(*) FROM state_governors WHERE state = :id";
                $sth = $this->conn->prepare($sql);
               $sth->bindParam("id", $id);
               $sth->execute();
               $num_rows = $sth->fetchColumn(); 
               return $num_rows > 0;
            }


           //inserts governor lis into db
           public function insertGov($state,$gov,$year,$party){
            
                    $sql = "INSERT INTO state_governors (state,governor,elected,party) VALUES (:state,:gov,:elected,:party)";
                    $sth = $this->conn->prepare($sql);
                   $sth->bindParam("state", $state);
                    $sth->bindParam("gov", $gov);
                   $sth->bindParam("elected", $year);
                   $sth->bindParam("party", $party);
                   $sth->execute();
                }
 
}
 
?>