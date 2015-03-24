/*====================================================================
Create Example
====================================================================*/

//Set the data object
var create = {

    //Include the csrf and app token
    csrf:csrf,
    token:token,

    //Set the table on which to create the new row
    table:'highscores',

    //Set the action to 'create'
    action:'create',

    //Set the data to be inserted into the new row
    data:{
        player: 'George',
        score: 1234
    }
};

//Send the AJAX request
$.ajax({

    //Set the target URL
    url:'http://yourdomainhere.com/api/',

    //Set the request type to POST
    type:'POST',

    //You must send the your POST data to the API
    data:create,

    //Add event handlers for processing the response
    success:function(response, status, request){console.log('success: ', response);},
    complete:function(request, status){console.log('complete: ', status);},
    error:function(request, status, error){console.log('error: ', error);}
});