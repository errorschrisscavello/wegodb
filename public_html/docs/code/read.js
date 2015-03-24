/*====================================================================
Read Example
====================================================================*/

//Set the table to read from
var table = 'highscores';

//Set the data object for a general read action
var read = {
    csrf:csrf,
    token:token,
    table:table,
    action:'read'
};

//Set the data object for a read action with a 'LIMIT' statement
var readLimit = {
    csrf:csrf,
    token:token,
    table:table,
    action:'read',
    limit:2
};

//Set a data object for a read action with an 'ORDER BY' statement
var readOrderBy = {
    csrf:csrf,
    token:token,
    table:table,
    action:'read',
    order_by:'score,DESC'
};

//Set a data object for a read action with a 'LIMIT' and 'ORDER BY' statement
var readLimitOrderBy = {
    csrf:csrf,
    token:token,
    table:table,
    action:'read',
    limit:1,
    order_by:'score,ASC'
};

//Create a function wrapper for the ajax request
function ajax(data)
{
    //Send the AJAX request
    $.ajax({

        //Set the target URL
        url:'http://yourdomainhere.com/api/',

        //Set the request type to POST
        type:'POST',

        //You must send the your POST data to the API
        data:data,

        //Add event handlers for processing the response
        success:function(response, status, request){console.log('success: ', response);},
        complete:function(request, status){console.log('complete: ', status);},
        error:function(request, status, error){console.log('error: ', error);}
    });
}

//Send an ajax request for each data object
ajax(read);
ajax(readLimit);
ajax(readOrderBy);
ajax(readLimitOrderBy);