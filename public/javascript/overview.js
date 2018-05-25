
$(function () {

  $.ajax({
    method: "GET",
    url: "/accountinfo",
    success: function(data) {
      var url = data["profile_avatar"];
      if (url){
        $("img.big-avatar").attr("src", url);
      }
      var cover = data["cover"];
      if(cover) {
        $("#overview-bg-img").css("background-image", 'url(' + cover + ')')
      }
      $(".username-container").text(data["username"]);
      $(".list-value").text(data["count"]);
    },
    error: function(xhr) {
      alert("avatar request failed with status: " + xhr.status());
    }
  });

  var setupRecommendationList = function(data) {

    $("#list-container").empty();
    var recommendations = data["recommendations"];
    $.each(recommendations, function( index, rec ) {
      var id = rec["id"];
      var url = rec["cover"];
      var title = rec["title"];
      var desc = rec["desc"];
      var card = '<div class="card recommendation-list"> <div class="card-img-top overview-img-top" id="'+ id +'"></div> <div class="card-body"> <h5 class="card-title">' + title + '</h5> <p class="card-text">' + desc + '</p> <a href="/rl/' + id + '" class="btn btn-primary">More</a> </div> </div>';
      $("#list-container").append(card);
      if (url) {
        $("#list-container #" + id).css("background-image", "url(" + url + ")");
      }
    });
  };

  $.ajax({
    method: "GET",
    url: "/listinfo",
    success: function(data) {
      setupRecommendationList(data);
    }
  });
  
})