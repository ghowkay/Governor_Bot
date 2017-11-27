angular.module('BlankApp',['ngMaterial', 'ngMessages'])

.controller('TitleController', function($scope) {
  $scope.title = 'Chat history';
})
.controller('AppCtrl', function($scope,$http) {

    // determine where to place to chat
    $scope.leftOrRight = function(from, currentUser)
    {
        return (from == currentUser) ? "self" : "other";
    }

//determine the avatar that corresponds to a particular message
    $scope.avatar =  function(from, currentUser)
    {
        return (from == currentUser) ? "https://i.imgur.com/DY6gND0.png" : "https://az705183.vo.msecnd.net/dam/skype/media/concierge-assets/avatar/avatarcnsrg-800.png";
    }


//Get all users
    $http.get("../all_users")
    .then(function(response) {
        //console.log(response);
        $scope.users = response.data;

        
    });




//get user chat history for display
    $scope.fetchChat = function(id){
        
        $http.get("../user_chat_history/"+id)
    .then(function(response) {
        console.log(response);
        $scope.chats = response.data;
    });
    }
});
