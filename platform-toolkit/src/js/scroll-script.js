(function(doc) {
  'use strict';

  let links = doc.getElementsByClassName('scroll-link');
  let linksArray = Array.from(links);
  linksArray.map(item => item.onclick = scroll);

  function scroll(e) {
    e.preventDefault();
    var id = this.getAttribute('href').replace('#', '');
    var target = document.getElementById(id).getBoundingClientRect().top;
    animateScroll(target);
  }
  
  function animateScroll(targetHeight) {
    targetHeight = document.body.scrollHeight - window.innerHeight > targetHeight + scrollY ? 
        targetHeight : document.body.scrollHeight - window.innerHeight;
    var initialPosition = window.scrollY;
    var SCROLL_DURATION = 30;
    var step_x = Math.PI / SCROLL_DURATION;
    var step_count = 0;
    requestAnimationFrame(step);
    function step() {
        if (step_count < SCROLL_DURATION) {
            requestAnimationFrame(step);
            step_count++;
            window.scrollTo(0, initialPosition + targetHeight * 0.25 * Math.pow((1 - Math.cos(step_x * ++step_count)), 2));
        }
    }
  }
})(document);