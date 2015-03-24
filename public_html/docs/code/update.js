/*====================================================================
Update Example
====================================================================*/

//Set your POST data
var update = {

    //Include the csrf and app tokens
    csrf:csrf,
    token:token,

    //Set the table on which to update the row
    table:'highscores',

    //Set the action to update
    action:'update',

    //Set your data to update the row with
    data:{
        player:'George Updated'
    },

    //Set the id of the row to update
    where:1
};

//Send the AJAX request
$.ajax({

    //Set the target URL
    url:'http://yourdomainhere.com/api/',

    //Set the request type to POST
    type:'POST',

    //You must send the your POST data to the API
    data:update,

    //Add event handlers for processing the response
    success:function(response, status, request){console.log('success: ', response);},
    complete:function(request, status){console.log('complete: ', status);},
    error:function(request, status, error){console.log('error: ', error);}
});