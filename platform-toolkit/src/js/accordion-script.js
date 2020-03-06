(function(doc) {
  'use strict';
  let accordion = doc.getElementsByClassName("accordion");
  let accordionArray = Array.from(accordion)
  function sliding() {
    this.classList.toggle("active");
    let panel = this.nextElementSibling;
    panel.style.maxHeight ?
    panel.style.maxHeight = null :
    panel.style.maxHeight = panel.scrollHeight + "px"
  }
  accordionArray.map(item => {
    item.addEventListener("click", sliding)
    item.addEventListener("touchstart", sliding)
  })

  let containerDesktop = doc.querySelectorAll('.pt-chevron');
  let chevronDesktop = doc.querySelectorAll('.chevron-down');
  let containerArray = Array.from(containerDesktop)
  let chevronArray = Array.from(chevronDesktop)

  let addRotation = function( icon ) {
    icon.classList.toggle('open')
  }
  containerArray.map((item, index) => item.addEventListener('click', function(){ addRotation( chevronArray[index] )}));
  containerArray.map((item, index) => item.addEventListener('touchstart', function(){ addRotation( chevronArray[index] )}));
})(document);