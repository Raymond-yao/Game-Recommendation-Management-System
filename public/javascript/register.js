
(function ($) {

  var state = {
    registeraccount: $("#registeraccount").val(),
    registerpassword: $("#registerpassword").val(),
    repeatpassword: $("repeatpassword").val()
  };

  $("#registeraccount, #registerpassword, #repeatpassword").on("change", function (ev) {
    var elem = $(ev.target);
    state[elem.attr("id")] = elem.val().trim();
    $(".warning-" + elem.attr("id")).css("display", elem.val().trim() === "" ? "block" : "none");    
  });
  $("#registerpassword, #repeatpassword").on("change", function (ev) {
    $(".warning-unmatchpassword").css("display", state["registerpassword"] === state["repeatpassword"] ? "none" : "block");    
  });
  $("#register-botton").on("click", function (ev) {
    ev.preventDefault();
    if ((state.registerpassword === state.repeatpassword) && (state.registeraccount !== "")) {
      $.ajax({
        method: "POST",
        cache: false,
        url: "/register",
        data: state,
        success: function(data) {
          if (data["status"] === "success") {
            window.location.href = "/";
          } else {
            alert("illegal registration");
          }
        }
      });
    } else {
      $(".warning-registeraccount").css("display", state.registeraccount === "" ? "block" : "none");
      $(".warning-registerpassword").css("display", state.registerpassword === "" ? "block" : "none");
      $(".warning-repeatpassword").css("display", state.repeatpassword === "" ? "block" : "none")       
    }
  })
} (jQuery));
