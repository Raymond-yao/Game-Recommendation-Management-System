
$(function () {

  var setup = function(data) {

    var profile_avatar = data["profile_avatar"] || 'https://www.socabelec.co.ke/wp-content/uploads/no-photo-14.jpg';
    $('#avatar').popover({
      trigger: 'hover',
      container: 'body',
      offset: 0,
      html: true,
      content:'<div class="popover-bg"><div class="avatar-bg" style="background-image: url(' + profile_avatar + ')"></div><div class="avatar-title">' + data["username"] + '</div><div class="list-count">Recommendation List: ' + data["count"] + '</div></div>'
    });


    $("#list-container").empty();
    var recommendations = data["recommendations"];
    $.each(recommendations, function( index, rec ) {
      var id = rec["id"];
      var url = rec["cover"];
      var title = rec["title"];
      var desc = rec["desc"];
      var card = '<div class="card recommendation-list" style="width: 18rem;"> <div class="card-img-top overview-img-top" id="'+ id +'"></div> <div class="card-body"> <h5 class="card-title">' + title + '</h5> <p class="card-text">' + desc + '</p> <a href="/rl/' + id + '" class="btn btn-primary">More</a> </div> </div>';
      $("#list-container").append(card);
      if (url) {
        $("#list-container #" + id).css("background-image", "url(" + url + ")");
      }
    });
  };

  $.ajax({
    method: "GET",
    url: "/overviewinfo",
    success: function(data) {
      setup(data);
    }
  })
  



})