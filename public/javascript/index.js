
(function ($) {

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
        url: "/login",
        data: state,
        success: function(data) {
          if (data["status"] === "success") {
            window.location = "/overview";
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
} (jQuery));
