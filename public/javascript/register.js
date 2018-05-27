
(function ($) {

  var state = {
    registerEmail: $("#registerEmail").val(),
    registerUsername: $("#registerUsername").val(),
    registerPassword: $("#registerPassword").val(),
    repeatPassword: $("#repeatPassword").val()
  };

  function validateEmail(email) {
  var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
  return re.test(email);
  }

  $("#registerEmail, #registerUsername, #registerPassword, #repeatPassword").on("change", function (ev) {
    var elem = $(ev.target);
    state[elem.attr("id")] = elem.val().trim();
    $(".warning-" + elem.attr("id")).css("display", elem.val().trim() === "" ? "block" : "none");
    $(".warning-registerEmailInvalid").css("display", !(validateEmail(state.registerEmail)) ? "block" : "none");    
  });
  $("#registerPassword, #repeatPassword").on("change", function (ev) {
    $(".warning-unmatchpassword").css("display", state["registerPassword"] === state["repeatPassword"] ? "none" : "block");    
  });
  $("#register-botton").on("click", function (ev) {
    ev.preventDefault();
    if ((state.registerPassword === state.repeatPassword !== "") && (validateEmail(state.registerEmail)) && (state.registerUsername !== "")) {
      $.ajax({
        method: "POST",
        cache: false,
        url: "/register",
        data: state,
        success: function(data) {
          if (data["status"] === "success") {
            window.location.href = "/";
          } else if (data["status"] === "success register"){
            alert("Thanks for register!");
          } 
          else {
            alert("illegal registration");
          }
        }
      });
    } else {
      $(".warning-registerEmail").css("display", state.registerEmail === "" ? "block" : "none");
      $(".warning-registerEmailInvalid").css("display", !(validateEmail(state.registerEmail)) ? "block" : "none");
      $(".warning-registerUsername").css("display", state.registerUsername === "" ? "block" : "none");
      $(".warning-registerPassword").css("display", state.registerPassword === "" ? "block" : "none");
      $(".warning-repeatPassword").css("display", state.repeatPassword === "" ? "block" : "none")       
    }
  })
} (jQuery));
