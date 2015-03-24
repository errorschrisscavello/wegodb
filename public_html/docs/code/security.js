/*====================================================================
CSRF Example
====================================================================*/

//Declare global variable to hold the CSRF token
var csrf = '';

//Send AJAX request via jQuery
$.ajax({

    //Set the target URL
    url:'http://yourdomainhere.com/api/csrf/',

    //Set the request type
    type:'GET',

    //Add an onSuccess event handler
    success:function(response, status, request){

        //Parse the response to JSON
        var json = JSON.parse(response);

        //Set the CSRF variable to the reponse data property
        csrf = json.data;

        //Log the CSRF token to view the request was successful
        console.log('success: ', csrf);
    },

    //Always good practice to set error and complete event handlers for debugging etc...
    complete:function(request, status){console.log('complete: ', status);},
    error:function(request, status, error){console.log('error: ', error);}
});

/*====================================================================
App Token Example
====================================================================*/

//Store your app token in a variable
var appToken = '85323add6f4c2f548d9aadb1b42c5b39c247876237efac1100a407d7021f3bb7d846f1ce4ce93069c84fff4c8098b449658bc230c2e7bdb6a13e6ea4739b72fd';

//Set the POST data to contain keys named 'csrf' and 'token'
var data = {

    //The csrf is the csrf token you retrieve from http://yourdomainhere.com/api/csrf
    csrf:csrf,

    //The token is your app token from the WegoDB admin app table
    token:appToken
};

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