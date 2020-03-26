$(document).ready(function(){
  $('h5').css('cursor','pointer');
  $('h5').nextUntil('h5,h4,h3,h2,h1').slideToggle();
  $('h5').click(function() {$(this).nextUntil('h5,h4,h3,h2,h1').slideToggle();} );
});
