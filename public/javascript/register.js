
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
  // $("#login-botton").on("click", function (ev) {
  //   ev.preventDefault();
  //   if (state.password !== "" && state.account !== "") {
  //     $.ajax({
  //       method: "POST",
  //       cache: false,
  //       url: "/login",
  //       data: state,
  //       success: function(data) {
  //         if (data["status"] === "success") {
  //           window.location.href = "/overview";
  //         } else {
  //           alert("login failed");
  //         }
  //       }
  //     });
  //   } else {
  //     $(".warning-account").css("display", state.account === "" ? "block" : "none");
  //     $(".warning-password").css("display", state.password === "" ? "block" : "none")       
  //   }
  // })
} (jQuery));
