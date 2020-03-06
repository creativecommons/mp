(function(doc) {
  'use strict';

  let buttons = doc.querySelectorAll('.pt-button-navigation')
  let allButtons = Array.from(buttons)
  allButtons.map( (item, index) => {
    item.addEventListener('click', function() {search(index)})
    item.addEventListener('touchstart', function() {search(index)})
  })
  
  let cleanButton = doc.querySelector('.pt-button-white')
  function cleanBtnFunction() {
    Array.from(doc.querySelectorAll('.pt-menu-items')).map( item => item.classList.remove('hide'));
  }
  cleanButton.addEventListener('click', cleanBtnFunction )
  cleanButton.addEventListener('touchstart', cleanBtnFunction )

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