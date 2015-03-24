/*====================================================================
Delete Example
====================================================================*/

//Set your post data
var destroy = {

    //Include the csrf and app tokens
    csrf:csrf,
    token:token,

    //Set the table for the row you wish to delete
    table:'highscores',

    //Set the action to delete
    action:'delete',

    //Set the id of the row you wish to delete
    where:1
};

//Send the AJAX request
$.ajax({

    //Set the target URL
    url:'http://yourdomainhere.com/api/',

    //Set the request type to POST
    type:'POST',

    //You must send the your POST data to the API
    data:destroy,

    //Add event handlers for processing the response
    success:function(response, status, request){console.log('success: ', response);},
    complete:function(request, status){console.log('complete: ', status);},
    error:function(request, status, error){console.log('error: ', error);}
});