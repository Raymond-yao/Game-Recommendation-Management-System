
$(function () {
  var recommendations = undefined;
  var state = "Card";

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
    },
    error: function(xhr) {
      alert("avatar request failed with status: " + xhr.status());
    }
  });

  var setupRecommendationList = function(data) {
    recommendations = data["recommendations"];
    cardView();
  };

  function cardView() {
    $("#list-container").empty();
    $.each(recommendations, function( index, rec ) {
      var id = rec["id"];
      var url = rec["cover"];
      var title = rec["title"];
      var desc = rec["desc"];
      var card = '<div class="card recommendation-list"> <div class="card-img-top overview-img-top" id=card-'+ id +'></div> <div class="card-body"> <h5 class="card-title">' + title + '</h5> <p class="card-text">' + desc + '</p> <a href="/rl/' + id + '" class="btn btn-primary">More</a> </div> </div>';
      $("#list-container").append(card);
      if (url) {
        $("#list-container #card-" + id).css("background-image", "url(" + url + ")");
      }
    })
  }

  function listView() {
    $("#list-container").empty();
    $.each(recommendations, function( index, rec ) {
      var id = rec["id"];
      var title = rec["title"];
      var list = '<a class="list-view-link" href="/rl/' + id + '"><div class="alert alert-dark list-view" id="list-' + id + '" role="alert">' + title + '</div></a>'
      $("#list-container").append(list);
    });
  }

  $.ajax({
    method: "GET",
    url: "/listinfo",
    success: function(data) {
      setupRecommendationList(data);
    }
  });

  $(".view-setting").on("click", function(ev) {
    $(".view-setting.selected").removeClass("selected");
    $(ev.target).addClass("selected");
    state = $(ev.target).text();
    $("#list-container").empty();
    if (state === "Card") {
      cardView();
    } else {
      listView();
    }
  });
  
})