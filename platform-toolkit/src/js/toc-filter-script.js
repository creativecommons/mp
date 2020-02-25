(function(doc) {
  'use strict';

  let buttons = doc.querySelectorAll('.pt-button-navigation')
  let allButtons = Array.from(buttons)
  allButtons.map( (item, index) => {
    item.addEventListener("click", function() {search(index)})
  })
  
  let cleanButton = doc.querySelector('.pt-button-white')
  cleanButton.addEventListener("click", function() {
    Array.from(doc.querySelectorAll('.pt-menu-items')).map( item => item.classList.remove('hide'));
  })

  function search(arrayIndex) {
    let bars = doc.querySelectorAll('.pt-menu-items')
    let searchBars = Array.from(bars)

    searchBars.map( (item ) => {
      item.classList.remove('hide')
      item.classList.contains(`${arrayIndex}`) ?
        '' :
        item.classList.toggle('hide')
      }
    )
  }
  
})(document);