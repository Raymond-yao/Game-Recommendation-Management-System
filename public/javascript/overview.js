
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
    var setupFriendsAvatar = function (data) {
      var friends = data["friends"];

    }
    $.ajax({
      method: "GET",
      url: "/friendinfo",
      success: function(data) {
        setupFriendsAvatar(data);
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
      recommendationViewSwitch();
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