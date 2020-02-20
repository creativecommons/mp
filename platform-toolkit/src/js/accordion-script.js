(function(doc) {
  'use strict';
  let accordion = doc.getElementsByClassName("accordion");
  let accordionArray = Array.from(accordion)
  accordionArray.map(item =>
    item.addEventListener("click", function() {
      this.classList.toggle("active");
      let panel = this.nextElementSibling;
      panel.style.maxHeight ?
      panel.style.maxHeight = null :
      panel.style.maxHeight = panel.scrollHeight + "px"
    }))

  let containerDesktop = doc.querySelectorAll('.pt-chevron');
  let chevronDesktop = doc.querySelectorAll('.chevron-down');
  let containerArray = Array.from(containerDesktop)
  let chevronArray = Array.from(chevronDesktop)
  let open = true;

  let addRotation = function( icon ) {

    open ?
    icon.className = 'chevron-down open' : 
    icon.className = 'chevron-down';
    
    open = !open;
  }
  containerArray.map((item, index) => item.addEventListener('click', function(){ addRotation( chevronArray[index] )}));

})(document);