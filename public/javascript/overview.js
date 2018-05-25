
$(function () {

  var setup = function(data) {

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
      setup(data);
    }
  });
  
})