(function(doc) {
  'use strict';
  let accordion = doc.getElementsByClassName("accordion");
  let accordionArray = Array.from(accordion)
  accordionArray.map(item =>
    item.addEventListener("click", function() {
      /* Toggle between adding and removing the "active" class,
      to highlight the button that controls the panel */
      this.classList.toggle("active");
  
      /* Toggle between hiding and showing the active panel */
      let panel = this.nextElementSibling;
      if (panel.style.display === "block") {
        panel.style.display = "none";
      } else {
        panel.style.display = "block";
      }
      if (panel.style.maxHeight) {
        panel.style.maxHeight = null;
      } else {
        panel.style.maxHeight = panel.scrollHeight + "px";
      }
    }));


  let containerDesktop = doc.querySelectorAll('.pt-chevron');
  let chevronDesktop = doc.querySelectorAll('.chevron-down');
  let containerArray = Array.from(containerDesktop)
  let chevronArray = Array.from(chevronDesktop)
  let open = true;

  let addRotation = function( icon ) {

    if(open){
      icon.className = 'chevron-down open';  
    } else{
      icon.className = 'chevron-down';
    }
    open = !open;
  }

  containerArray.map((item, index) => item.addEventListener('click', function(){ addRotation( chevronArray[index] )}));

})(document);