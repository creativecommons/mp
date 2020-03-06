(function(doc) {
  'use strict';

  let popupBtns = doc.getElementById("popupBtns")
  let popupCollaboration = doc.getElementById("popupCollaboration")
  let openCollaboration = doc.querySelector(".collaborationPopup")
  let popupModelPlatform = doc.getElementById("popupModelPlatform")
  let openModelPlatform = doc.querySelector(".modalPlatformPopup")
  let closeBoxes = doc.querySelectorAll(".closebox")
  let modals = doc.querySelectorAll(".modal")
  
  const collaborationPopup = 
    {
    header :'<h3>How to help your community get on board</h3>',
    text : 'Here are some ways your platform can communicate and explain how the CC licenses work and their importance.',
    link1 : '<strong>Be transparent</strong><p>Explain the basics of CC, why your platform decided to add CC, and how it aligns with your platform’s values. <span class="text-small">Link https://creativecommons.org for users who want to learn more. Example: <a target="_blank" rel=”noopener noreferrer” href="https://support.google.com/youtube/answer/2797468?hl=en">YouTube’s page on CC.</span></a></p>',
    link2 : '<strong>Provide information</strong><p>Build a FAQ on how CC licenses work on your platform. You can adapt CC’s FAQ for your purposes. <span class="text-small">Example: <a target="_blank" rel=”noopener noreferrer” href="https://vimeo.com/help/faq/legal-stuff/creative-commons">Vimeo’s page on CC.</a></span></p>',
    link3 : '<strong>Have clear guidelines</strong><p>Implement community guidelines around attribution and other sharing practices. <span class="text-small">Example: <a target="_blank" rel=”noopener noreferrer” href="https://www.flickr.com/services/developer/attributions/">Flickr’s attribution</a> and <a target="_blank" rel=”noopener noreferrer” href="https://www.flickr.com/help/guidelines/">general community guidelines.</a></span></p>',
    link4 : '<strong>Show the content</strong><p>Have a dedicated area for CC works to be found. Build a CC portal for content discovery. <span class="text-small">Example: <a target="_blank" rel=”noopener noreferrer” href="https://flickr.com/creativecommons/">Flickr’s CC portal; </a> and <a target="_blank" rel=”noopener noreferrer” href="https://vimeo.com/creativecommons">Vimeo’s CC portal.</a></span></p>'
    }

  const btnMessages = [
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
  
  const modelPlatformImgs = [
    { url : 'src/img/mp01.jpg'},
    { url : 'src/img/mp02.jpg'},
    { url : 'src/img/mp03.jpg'},
    { url : 'src/img/mp04.png'},
    { url : 'src/img/mp05.jpg'},
    { url : 'src/img/mp06.jpg'},
    { url : 'src/img/mp07.jpg'},
    { url : 'src/img/mp08.jpg'},
    { url : 'src/img/mp09.png'},
    { url : 'src/img/mp10.jpg'},
    { url : 'src/img/mp11.jpg'},
    { url : 'src/img/mp12.jpg'},
    { url : 'src/img/mp13.jpg'},
    { url : 'src/img/mp14.jpg'}
  ]


  for (let modal of modals) {
    modal.addEventListener("click", function(e) {
      const boxNum = parseInt(e.target.dataset.box)
      const message = btnMessages[boxNum - 1]

      popupBtns.querySelector(".message-url").src = message.url 
      popupBtns.querySelector(".message").innerHTML = message.text
  
      popupBtns.style.display == "block" ?
      popupBtns.style.display = "none" :
      popupBtns.style.display = "block"

    });
  }

  openCollaboration.addEventListener('click',function() {
    popupCollaboration.querySelector(".message-header").innerHTML = collaborationPopup.header
    popupCollaboration.querySelector(".message-link1").innerHTML = collaborationPopup.link1
    popupCollaboration.querySelector(".message-link2").innerHTML = collaborationPopup.link2
    popupCollaboration.querySelector(".message-link3").innerHTML = collaborationPopup.link3
    popupCollaboration.querySelector(".message-link4").innerHTML = collaborationPopup.link4

    popupCollaboration.style.display == "block" ?
    popupCollaboration.style.display = "none" :
    popupCollaboration.style.display = "block"
  })

  openModelPlatform.addEventListener('click',function() {
    for (let img of modelPlatformImgs) {
      let imgTag = doc.createElement('img');
      imgTag.src = img.url
      imgTag.classList.add("margin-top-large")
      popupModelPlatform.querySelector(".mp-url").appendChild(imgTag)
      console.log('executed')
    }
    popupModelPlatform.style.display == "block" ?
    popupModelPlatform.style.display = "none" :
    popupModelPlatform.style.display = "block"    
  })
  

  for (let close of closeBoxes) {
  close.addEventListener('click',function(){
    let cleanMP = doc.querySelector(".mp-url")
    popupBtns.style.display = "none"
    popupCollaboration.style.display = "none"
    popupModelPlatform.style.display = "none"
    while (cleanMP.lastElementChild) {
      cleanMP.removeChild(cleanMP.lastElementChild);
    }
    })
  }
  
})(document);