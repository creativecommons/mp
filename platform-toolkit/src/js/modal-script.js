(function(doc) {
  'use strict';

  let popup = doc.getElementById("popup");
  let close = doc.getElementById("closebox");
  let boxes = doc.querySelectorAll(".box");
  
  const messages = [
    { text : 'this is text 1'
    },
    { text : 'WikiMedia Commons Terms of Service',
      url : 'src/img/tos-wikimedia.png'
    },
    { text : 'Internet Archive Terms of Service',
      url : 'src/img/tos-internetarchive.png'
    },
    { text : 'Jamendo Terms of Service',
      url : 'src/img/tos-jamendo.png'
    },
    { text : 'Search by License',
      url : 'src/img/01-search-by-license.png'
    },
    { text : 'Search by Use',
      url : 'src/img/01-search-by-use.png'
    },
    { text : 'Content Portal',
      url : 'src/img/01-content-portal.png'
    },
    { text : 'At the point of download',
      url : 'src/img/01-at-point-of-download.png'
    },
    { text : 'At the point of download',
      url : 'src/img/02-at-point-of-download.png'
    },
    { text : 'Third Party contributions',
      url : 'src/img/01-third-party-work-distinction.png'
    }
  ];

  for (let box of boxes) {
    box.addEventListener("click", function(e) {
      const boxNum = parseInt(e.target.dataset.box);
      const message = messages[boxNum - 1];
      popup.querySelector(".message").innerHTML = message.text;

      message.url ? 
      popup.querySelector(".message-url").src = message.url :
      popup.querySelector(".message-url").classList.add = "none"
  
      popup.style.display == "block" ?
      popup.style.display = "none" :
      popup.style.display = "block"

    });
  }
  
  close.addEventListener('click',function(){
    popup.style.display = "none"
  })

})(document);