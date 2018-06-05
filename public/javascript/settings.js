
(function ($) {

  var state = {
    Username: $("#Username").val(),
    Password: $("#Password").val(),
    repeatPassword: $("#repeatPassword").val()
  };

  $("#Username, #Password, #repeatPassword").on("change", function (ev) {
    var elem = $(ev.target);
    state[elem.attr("id")] = elem.val().trim();
    $(".warning-" + elem.attr("id")).css("display", elem.val().trim() === "" ? "block" : "none");
  });
  $("#Password, #repeatPassword").on("change", function (ev) {
    $(".warning-unmatchpassword").css("display", state["Password"] === state["repeatPassword"] ? "none" : "block");    
  });
  $("#saveUsername-botton").on("click", function (ev) {
    ev.preventDefault();
    if ((state.Username !== "")) {
      $.ajax({
        method: "POST",
        cache: false,
        url:"/updateUsername",
        data: state,
        success: function(data) {
          if (data["status"] === "success username") {
            alert("successfully Change Your Username");
            window.location.href = "/overview";
          }
          else {
            alert("illegal username");
          }
        }
      });
    } else {
      $(".warning-Username").css("display", state.Username === "" ? "block" : "none"); 
    }
  })

  $("#savePassword-botton").on("click", function (ev) {
    ev.preventDefault();
    if ((state.Password !== "") && (state.repeatPassword !== "") && (state.Password === state.repeatPassword)) {
      $.ajax({
        method: "POST",
        cache: false,
        url:"/updatePassword",
        data: state,
        success: function(data) {
          if (data["status"] === "success password") {
            alert("Successfully Change Your Password, Please Login Again");
            window.location.href = "/index";
          }
          else {
            alert("illegal password");
          }
        }
      });
    } else {
      $(".warning-repeatPassword").css("display", state.Password === "" ? "block" : "none"); 
      $(".warning-unmatchpassword").css("display", state.Password !== state.repeatPassword ? "block" : "none"); 
    }
  })
} (jQuery));
