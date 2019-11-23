$(document).ready(function(){

    $.ajax({
        type:'GET',
        url:'/bill/getDataLayerForGA/'+bill_id,
        dataType:'json',
        success: function (res) {
            console.log(res);
            dataLayer.push(res);

        },
        error: function (error) {
            console.log(error);
        }
    });



});


