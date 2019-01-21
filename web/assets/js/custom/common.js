$(document).ready(function(){
    if($('.label-notifications').text() == 0){
        $('.label-notifications').addClass('hidden');
    }else{
        $('.label-notifications').removeClass('hidden');
    }
    
    notifications();
    setInterval(function(){
       notifications() 
    }, 60000);
});

function notifications(){
    $.ajax({
        url: URL + '/notifications/get',
        type: 'GET',
        success: function(response){
            $('.label-notifications').html(response);
            
            if(response == 0){
                $('.label-notifications').addClass('hidden');
            }else{
                $('.label-notifications').removeClass('hidden');
            }
        }
    });
}