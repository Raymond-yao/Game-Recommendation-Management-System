
$(function () {

  var state = {
    account: $("#account").val(),
    password: $("#password").val()
  };

  $("#account, #password").on("change", function (ev) {
    var elem = $(ev.target);
    state[elem.attr("id")] = elem.val().trim();
    $(".warning-" + elem.attr("id")).css("display", elem.val().trim() === "" ? "block" : "none");    
  });
  $("#login-botton").on("click", function (ev) {
    ev.preventDefault();
    if (state.password !== "" && state.account !== "") {
      $.ajax({
        method: "POST",
        cache: false,
        url: "/login",
        data: state,
        success: function(data) {
          if (data["status"] === "success") {
            window.location.href = "/overview";
          } else {
            alert("login failed");
          }
        }
      });
    } else {
      $(".warning-account").css("display", state.account === "" ? "block" : "none");
      $(".warning-password").css("display", state.password === "" ? "block" : "none")       
    }
  })

  // random background image
  var images=['https://img.game.co.uk/ml2/7/2/4/4/724460_scr5_a.png',
              'https://www.ricedigital.co.uk/wp-content/uploads/2018/04/dark-souls-remastered-preview2.jpg',
              'https://41zxbw463fq733z1kl101n01-wpengine.netdna-ssl.com/wp-content/uploads/2016/04/Gamespot-1.jpg',
              'https://i.imgur.com/ZroVEXL.jpg',
              'https://deow9bq0xqvbj.cloudfront.net/dir-logo/233639/233639.jpg',
              'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQZ7ULdp4GylS-Pq1YsLc9kE3gvvUAmqYjkFUOKC5JEFIeKCrgl',
              'http://handeyesociety.com/wp-content/uploads/2017/02/ComicsXGames2017_CorySchmitz.png',
              'https://khouj.com/wp-content/uploads/2018/03/game.jpg',
              'http://escolabrasileiradegames.com.br/wp2016/wp-content/uploads/2017/07/escola-brasileira-de-games-producao-e-desenvolvimento-de-games-2.jpg',
              ];

  var randomNumber = Math.floor(Math.random() * images.length);
  var bgImg = 'url(' + images[randomNumber] + ')';

$('body').css({'background':bgImg, 'background-size':'cover', });
} );
