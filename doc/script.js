$(document).ready(function(){
  $('h5').css('cursor','pointer');
  $('h5').nextUntil('h2').slideToggle();
  $('h5').click(function() {$(this).nextUntil('h5').slideToggle();} );
});
