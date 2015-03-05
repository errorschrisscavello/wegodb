/*
* main.js
*/

var WegoDB = function(){};

WegoDB.csrf = function(){};
WegoDB.csrf.tokenName = $('meta[name="csrf_token_name"]').attr('content');
WegoDB.csrf.hash = $('meta[name="csrf_hash"]').attr('content');

WegoDB.ajax = function(){};
WegoDB.ajax.data = {};
WegoDB.ajax.links = [];
WegoDB.ajax.data[WegoDB.csrf.tokenName] = WegoDB.csrf.hash;
WegoDB.ajax.config = function(url, method){
    if(method != 'post')
    {
        WegoDB.ajax.data['REQUEST_METHOD'] = method;
    }
    return {
        url:url,
        data:WegoDB.ajax.data,
        complete:function(jqxhr, status){
            console.log('Complete jqxhr: ', jqxhr);
            console.log('Complete status: ', status);
        },
        success:function(data, status, jqxhr){
            console.log('Success data: ', data);
            console.log('Success status: ', status);
            console.log('Success jqxhr: ', jqxhr);
        },
        error:function(jqxhr, status, error){
            console.log('Error jqxhr: ', jqxhr);
            console.log('Error status: ', status);
            console.log('Error error: ', error);
        }
    };
};
WegoDB.ajax.send = function(config){
    console.log(config);
    $.ajax(config);
};
WegoDB.ajax.append = function(links, method){
    WegoDB.ajax.links[method] = links;
};
WegoDB.ajax.events = function(){
    var links = WegoDB.ajax.links;
    for(var method in links)
    {
        var link = links[method];
        $(link).bind('click', {
            method:method
        }, function(e){
            e.preventDefault();
            var config = WegoDB.ajax.config(this.href, e.data.method);
            WegoDB.ajax.send(config);
            return false;
        });
    }
};

WegoDB.ready = function(){};
WegoDB.ready.tasks = [];
WegoDB.ready.run = function(){
    var tasks = WegoDB.ready.tasks;
    for(var i = 0; i < tasks.length; i++)
    {
        var task = tasks[i];
        task();
    }
};
WegoDB.ready.tasks.push(function(){
    $(document).ready(function(){

        WegoDB.ajax.append($('a[class="post"]'), 'post');
        WegoDB.ajax.append($('a[class="put"]'), 'put');
        WegoDB.ajax.append($('a[class="delete"]'), 'delete');

        WegoDB.ajax.events();
    });
});

WegoDB.ready.run();