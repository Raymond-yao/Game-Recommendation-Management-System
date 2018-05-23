
$(function () {
  $('#avatar').popover({
    trigger: 'hover',
    container: 'body',
    offset: 0,
    html: true,
    content:'<div class="popover-bg"><div class="avatar-bg"></div><div class="avatar-title">Raymond</div><div class="list-count">Recommendation List: 0</div></div>'
  });

  var stub_data = {
    "username": "raymond",
    "count": 7,
    "recommendations": [
    {
      "id": "1",
      "cover":null,
      "title":"Steam 10 best games",
      "desc": "Here are 10 games which I think is the best in Steam"
    },
    {
      "id": "2",
      "cover":null,
      "title":"2016 Winter Sales recommendations",
      "desc": "What Game should you play in 2016 winter!"
    },
    {
      "id": "3",
      "cover":null,
      "title":"2017 Summer Sales recommendations",
      "desc": "What Game should you play in 2017 Summer!"
    },
    {
      "id": "4",
      "cover":"http://dailypost.ng/wp-content/uploads/2016/05/nbc-fires-donald-trump-after-he-calls-mexicans-rapists-and-drug-runners-1024x768.jpg",
      "title":"Games about Trump",
      "desc": "Love Trump? Then you might also love these games!"
    },
    {
      "id": "5",
      "cover":"https://thecdm.ca/files/ea-timeline_0.png",
      "title":"EA Games collection",
      "desc": "Best games EA has made ever"
    },
    {
      "id": "6",
      "cover":"https://www.telegraph.co.uk/content/dam/gaming/2017/06/06/ubisoft_new_2017_logo_2400-0_trans_NvBQzQNjv4BqZgEkZX3M936N5BQK4Va8RWtT0gK_6EfZT336f62EI5U.jpg?imwidth=450",
      "title":"Ubisoft Potato",
      "desc": "We all know Ubisoft's server is shitty, which is always called as a potato. However, this company did make some really cool games in the world! Therefore this recommendation list introduces best games held on Potato server ever."
    },
    {
      "id": "7",
      "cover":"http://teamcherry.com.au/wp-content/uploads/banner-2.jpg",
      "title":"Best indie games",
      "desc": "Best indie games I ever play!"
    },
    ]
  }

  $("#list-container").empty();
  var recommendations = stub_data["recommendations"];
  $.each(recommendations, function( index, rec ) {
    var id = rec["id"];
    var url = rec["cover"];
    var title = rec["title"];
    var desc = rec["desc"];
    var card = '<div class="card recommendation-list" style="width: 18rem;"> <div class="card-img-top overview-img-top" id="'+ id +'"></div> <div class="card-body"> <h5 class="card-title">' + title + '</h5> <p class="card-text">' + desc + '</p> <a href="#" class="btn btn-primary">More</a> </div> </div>';
    $("#list-container").append(card);
    if (url) {
      $("#list-container #" + id).css("background-image", "url(" + url + ")");
    }
  });



})