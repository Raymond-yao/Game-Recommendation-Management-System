
$(function () {
  var recommendations = undefined;
  var RecommendationViewState = "Card";
  var viewState = "recommendation";

  $.ajax({
    method: "GET",
    url: "/accountinfo/" + visit_id,
    success: function(data) {
      var url = data["avatar"];
      if (url){
        $("img.big-avatar").attr("src", url);
      }
      var cover = data["cover"];
      if(cover) {
        $("#overview-bg-img").css("background-image", 'url(' + cover + ')')
      }
      $(".username-container").text(data["username"]);
      $(".list-value").text(data["list_count"]);
      $(".friend-value").text(data["friend_count"]);
      $(".item-list").addClass("selected");
      if (document.cookie.match(visit_id)) {
        $(".overview-follow-button").hide();
      } else {
        $(".overview-follow-button").on("click", toggleFollow);
        $(".overview-follow-button").data("followUser", visit_id)
        if (is_friend) {
          $(".overview-follow-button").addClass("following");
        }
      }
    },
    error: function(xhr) {
      alert("avatar request failed with status: " + xhr.status());
    }
  });

  function viewSwitch() {
    if (viewState === "recommendation") {
      recommendationViewSwitch();
    } else {
      friendView();
    }
  }

  function recommendationViewSwitch() {
    $("div.settings-group").show();
    $("#content-container").empty();
    if (recommendations.length === 0) {
      noContent();
    } else if (RecommendationViewState === "Card") {
      cardView();
    } else {
      listView();
    }
  };

  function cardView() {  
    $.each(recommendations, function( index, rec ) {
      var id = rec["id"];
      var url = rec["cover"];
      var title = rec["title"];
      var desc = rec["desc"];
      var card = '<div class="card recommendation-list"> <div class="card-img-top overview-img-top" id=card-'+ id +'></div> <div class="card-body"> <h5 class="card-title">' + title + '</h5> <p class="card-text">' + desc + '</p> <a href="/rl/' + id + '" class="btn btn-primary">More</a> </div> </div>';
      $("#content-container").append(card);
      if (url) {
        $("#content-container #card-" + id).css("background-image", "url(" + url + ")");
      }
    });
    
  }

  function listView() {
    $.each(recommendations, function( index, rec ) {
      var id = rec["id"];
      var title = rec["title"];
      var list = '<a class="list-view-link" href="/rl/' + id + '"><div class="alert alert-dark list-view" id="list-' + id + '" role="alert">' + title + '</div></a>'
      $("#content-container").append(list);
    });
  }

  function friendView() {
    $("div.settings-group").hide();
    $("#content-container").empty();
    var template = $('script[data-template="profile-card"]').text();
    var setupFriendsAvatar = function (data) {
      var friends = data["friends"];
      $.each(friends, function( index, fri ) {
        var card = template;
        var params = {
          '${card-id}': "card-" + fri["id"],
          '${url-1}': "/overview/" + fri["id"],
          '${url-2}': "/overview/" + fri["id"],
          '${url-3}': "/overview/" + fri["id"],
          '${avatar}': fri["avatar"] || "/assets/image/no_photo",
          '${username}': fri["username"],
          '${email}': fri["email"]
        };
        $.each(params, function(key, value) {
          card = card.replace(key, value);
        });
        $("#content-container").append(card);
        if (fri["cover"]){
          $("#card-" + fri["id"] + " .profile-card-bg").css("background-image", 'url(' + fri["cover"] + ')');
        }
        if (document.cookie.match(fri["id"])) {
          $("#card-" + fri["id"] + " .follow-button").hide();
        } else {
          $("#card-" + fri["id"] + " .follow-button").data("followUser" ,fri["id"]);
          $("#card-" + fri["id"] + " .follow-button").on("click", toggleFollow);
          if(fri["following"]) {
            $("#card-" + fri["id"] + " .follow-button").addClass("following");
          }
      }
      });

    };
    $.ajax({
      method: "GET",
      url: "/friendinfo/" + visit_id,
      success: function(data) {
        setupFriendsAvatar(data);
      }
    });
  }

  function toggleFollow(ev) {
    var follow_button = $(ev.currentTarget);
    $.ajax({
      method: "POST",
      url: "/manage_friend",
      data: {
        "action": follow_button.hasClass("following") ? "unfollow" : "follow",
        "followee": follow_button.data("followUser")
      },
      success: function(data) {
        if (data["status"] === "success") {
          if (follow_button.hasClass("following")) {
            follow_button.removeClass("following");
          } else {
            follow_button.addClass("following");
          }
        } else {
          alert("sorry, an error has occured, please try again later");
        }
      },
      error: function(){
        alert("sorry, an error has occured, please try again later");
      }
    });
  }

  function noContent() {
    var reminder = '<div class="empty-reminder text-muted">Oops, you haven\'t create any list yet</div>';
    $("#content-container").append(reminder);
  }

  $.ajax({
    method: "GET",
    url: "/listinfo",
    success: function(data) {
      recommendations = data["recommendations"];
      viewSwitch();
    }
  });

  $(".stat-item").on("click", function(ev) {
    $(".stat-item.selected").removeClass("selected");
    var elem = $(ev.currentTarget);
    elem.addClass("selected");
    viewState = elem.data("type");
    viewSwitch();
  })

  $(".view-setting").on("click", function(ev) {
    $(".view-setting.selected").removeClass("selected");
    $(ev.target).addClass("selected");
    RecommendationViewState = $(ev.target).text();
    recommendationViewSwitch();
  });
  
})