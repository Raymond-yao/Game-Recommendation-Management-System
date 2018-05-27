
$(function() {
   // random background image
  var images=['/assets/image/bg1',
              '/assets/image/bg2',
              '/assets/image/bg3',
              '/assets/image/bg4',
              '/assets/image/bg5',
              '/assets/image/bg6',
              '/assets/image/bg7',
              '/assets/image/bg8'
              ];

  var randomNumber = Math.floor(Math.random() * images.length);
  var bgImg = 'url(' + images[randomNumber] + ')';

  $('body').css({'background':bgImg, 'background-size':'cover', });
});