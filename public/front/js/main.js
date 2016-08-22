

$(document).ready(function () {
  
$('a.pravno').click(function (){
        setTimeout(function(){ $('#pravno form').validator(); }, 10);
        
    });
    
    
      $(".shopingCart a").click(function () {
        $("#shopingCartSmall").slideToggle();
    });
    $('.filtr-container').filterizr();
    
    $('.carousel').carousel({
        interval: 5000 //changes the speed
    });
    $('.slider-product').carousel({
        interval: 5000 //changes the speed
    });



    







  



    $("#hideRow").click(function () {
        $("#hiddenRow").toggle();
    });




    $("#hideRow1").click(function () {
        $("#hiddenRow1").toggle();
    
});


});











