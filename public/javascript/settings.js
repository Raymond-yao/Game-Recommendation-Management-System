
$(function () {

  var avatar = undefined;
  var cover = undefined;

  $.ajax({
    method: "GET",
    url: "/settings",
    success: function(data) {
      $(".setting-user-text.username").text(data["username"]);
      $(".setting-user-text.email").text(data["email"]);
      if (data["avatar"])
        $(".setting-avatar").attr("src", data["avatar"]);
      if (data["cover"])
        $(".user-cover").css("background-image", "url(" + data["cover"] + ")");
    }
  });

  $("input#avatar-image").on("change", function(ev) {
    var file = ev.currentTarget.files[0];
    if (file){
      avatar = file;
      $(".avatar-label").text(file.name);
      var reader  = new FileReader();

      reader.addEventListener("load", function () {
        $(".avatar-preview img").attr("src", reader.result);
        $(".avatar-preview").css("display", "block");
      }, false);
      reader.readAsDataURL(file);
    }
  });

  $("input#cover-image").on("change", function(ev) {
    var file = ev.currentTarget.files[0];
    if (file){
      cover = file;
      $(".cover-label").text(file.name);
      var reader  = new FileReader();

      reader.addEventListener("load", function () {
        $(".cover-preview img").attr("src", reader.result);
        $(".cover-preview").css("display", "block");
      }, false);
      reader.readAsDataURL(file);
    }
  });

  $("#image-submit-btn").on("click", function(ev) {
    ev.preventDefault();

    data = new FormData();
    data.append("updateType", "image");
    if (avatar){
      data.append("avatar", avatar);
    }
    if (cover) {
      data.append("cover", cover);
    }
    $.ajax({
      processData: false,  
      contentType: false,
      method: "POST",
      url:"/settings",
      data: data,
      success: function(data) {
        alert("picture update success!");
        window.location.reload();
      }
    });
  });

  $(".setting-opt, .avatar-change").on("click", function(ev) {
    ev.preventDefault();
    $(".setting-opt.selected").removeClass("selected");
    $(ev.currentTarget).addClass("selected");
    var type = $(ev.currentTarget).data("type");
    $(".setting-container-content").addClass("hidden");
    $(".setting-container-content." + type).removeClass("hidden");
  });

  function validateEmail(email) {
    var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    return re.test(email);
  }

  var state = {
    Password: $("#Password").val(),
    repeatPassword: $("#repeatPassword").val()
  };

  $("#Password, #repeatPassword").on("change", function (ev) {
    var elem = $(ev.target);
    state[elem.attr("id")] = elem.val().trim();
    $(".warning-" + elem.attr("id")).css("display", elem.val().trim() === "" ? "block" : "none");
  });
  $("#Password, #repeatPassword").on("change", function (ev) {
    $(".warning-unmatchpassword").css("display", state["Password"] === state["repeatPassword"] ? "none" : "block");    
  });
  $("#saveUsername-botton").on("click", function (ev) {
    ev.preventDefault();

    var username = $("#username").val();
    var email = $("#email").val();
    var account_info = {"updateType": "account"};
    if (email.trim() !== "" && validateEmail(email)) {
      account_info["email"] = email;
    }
    if (username.trim() !== "") {
      account_info["username"] = username;
    }
    $.ajax({
      method: "POST",
      cache: false,
      url:"/settings",
      data: account_info,
      success: function(data) {
        if (data["status"] === "success") {
          alert("successfully update your account info");
          window.location.reload();
        }
        else {
          alert(data["reason"]);
        }
      }
    });
  });

  $("#savePassword-botton").on("click", function (ev) {
    ev.preventDefault();
    if ((state.Password !== "") && (state.repeatPassword !== "") && (state.Password === state.repeatPassword)) {
      state["updateType"] = "password";
      $.ajax({
        method: "POST",
        cache: false,
        url:"/settings",
        data: state,
        success: function(data) {
          if (data["status"] === "success password") {
            alert("Successfully Change Your Password, Please Login Again");
            window.location.href = "/index";
          }
          else {
            alert(data["reason"]);
          }
        }
      });
    } else {
      $(".warning-repeatPassword").css("display", state.Password === "" ? "block" : "none"); 
      $(".warning-unmatchpassword").css("display", state.Password !== state.repeatPassword ? "block" : "none"); 
    }
  })
});
