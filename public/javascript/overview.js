
$(function () {
  var recommendations = undefined;
  var RecommendationViewState = "Card";
  var viewState = "recommendation";
  var list_id = undefined;

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
        $(".item-stat").hide();
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
    } else if (viewState === "friend") {
      friendView();
    } else {
      statView();
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
    $('[data-toggle="tooltip"]').tooltip();
    $(".delete-dropdown, .delete-article").on("click", function(ev) {
      list_id = $(ev.currentTarget).data("delete");
    });
  };

  function cardView() {  
    $.each(recommendations, function( index, rec ) {
      var id = rec["id"];
      var url = rec["cover"];
      var title = rec["title"];
      var desc = rec["desc"];
      var card = '<div class="card recommendation-list" data-toggle="tooltip" data-placement="top" title="' + title + '"> <div class="card-img-top overview-img-top" id=card-'+ id +'></div> <div class="card-body"> <h5 class="card-title">' + title + '</h5> <p class="card-text">' + desc + '</p> <a href="/list/' + id + '" class="btn btn-primary">Go to</a><span class="dropdown-toggle more-option" data-toggle="dropdown"></span><div class="dropdown-menu" aria-labelledby="dropdownMenuButton"><a class="dropdown-item" href="/edit/' + id + '">Edit</a><a class="dropdown-item delete-dropdown" href="javascript:;" data-toggle="modal" data-target="#deleteConfirm" data-delete="' + id + '">Delete</a></div></div> </div>';
      $("#content-container").append(card);
      if (url) {
        $("#content-container #card-" + id).css("background-image", "url(" + url + ")");
      }
    });
    if (document.cookie.match(visit_id)){
      var stub_card = $('script[data-template="stub-card"]').text();
      $("#content-container").append(stub_card);
    } else {
      $(".more-option").remove();
    }
  }

  function listView() {
    $("#content-container").append('<ul class="list-group"></ul>');
    $.each(recommendations, function( index, rec ) {
      var id = rec["id"];
      var title = rec["title"];
      var list = '<li class="list-group-item" id="list-' + id + '" data-toggle="tooltip" data-placement="top" title="' + title + '"><a class="list-view-link" href="/list/' + id + '">' + title + '</a><div class="list-more-options"> <a href="/edit/' + id + '" class="btn edit" style="height:21px;font-size:14px">Edit</a> <a href="javascript:;" class="btn delete-article" data-toggle="modal" data-target="#deleteConfirm" data-delete="' + id + '" style="height:21px;font-size:14px">Delete</a></div></li>'
      $("ul.list-group").append(list);
    });
    if (document.cookie.match(visit_id)){
      var stub_list_item = $('script[data-template="stub-list"]').text();
      $("ul.list-group").append(stub_list_item);
    } else {
      $(".list-more-options").remove();
    }
  }

  function friendView() {
    $("div.settings-group").hide();
    $("#content-container").empty();
    var setupFriendsAvatar = function (data) {
      var friends = data["friends"];
      $.each(friends, function( index, fri ) {
        profileCardAdder(fri);
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

  function statView() {
    $("div.settings-group").hide();
    $("#content-container").empty();
    stat_console = $('script[data-template="stat-console"]').text();
    $("#content-container").append(stat_console);

    $(".stat-type").on("click", function(ev) {
      $(".stat-type.selected").removeClass("selected");
      $(ev.currentTarget).addClass("selected");
      $("#stat-container").empty();
      $("#stat-container").append('<div id="chart-container"></div>');
      var type = $(ev.currentTarget).data("stat");
      var data = {
        type: type
      }
      if (type === "count"){
        $.ajax({
          method: "GET",
          url: "/stat",
          data: data,
          success: function(statData) {
            google.charts.load('current', {packages: ['corechart']});

            var drawChart = function() {
              var chartData = [
              ['Element', 'list count', { role: 'style' }]
              ];
              $.each(statData["users"], function(index, user) {
                chartData.push([user["username"], user["listcount"] * 1, 'skyblue']);
              });
              var data = google.visualization.arrayToDataTable(chartData);
              var chart = new google.visualization.BarChart($("#chart-container")[0]);

              var options = {
                title: "list count of friends",
                width: 600,
                height: 400,
                bar: {groupWidth: "95%"},
              };
              chart.draw(data, options);
            };

            google.charts.setOnLoadCallback(drawChart);
          }
        });
      } else {
       var extreme_container = $("script[data-template=average-list]").text();
       $("#stat-container").prepend(extreme_container);
       $(".extreme-control").on("click", function(ev) {
        $(".extreme-control.selected").removeClass("selected");
        $(ev.currentTarget).addClass("selected");
        var data = {
          type: type,
          extreme: $(ev.currentTarget).data("extreme")
        }
        $.ajax({
          method: "GET",
          url: "/stat",
          data: data,
          success: function(statData) {
            $(".extreme-title").text(statData["extreme"]["type"] + ":");
            $(".extreme-value").text(statData["extreme"]["value"]);
            google.charts.load('current', {packages: ['corechart']});

            var drawChart = function() {
              var chartData = [
              ['Element', 'average view count', { role: 'style' }]
              ];
              $.each(statData["users"], function(index, user) {
                chartData.push([user["username"], user["avgViewCount"] * 1, 'skyblue']);
              });
              var data = google.visualization.arrayToDataTable(chartData);
              var chart = new google.visualization.BarChart($("#chart-container")[0]);

              var options = {
                title: "Average view count of friends",
                width: 600,
                height: 400,
                bar: {groupWidth: "95%"},
              };
              chart.draw(data, options);
            };

            google.charts.setOnLoadCallback(drawChart);
          }
        })
      });
     }
   });
  }

  function profileCardAdder(profile) {
    var template = $('script[data-template="profile-card"]').text();
    var id = profile["id"];
    var params = {
      '${card-id}': "card-" + id,
      '${url-1}': "/overview/" + id,
      '${url-2}': "/overview/" + id,
      '${url-3}': "/overview/" + id,
      '${avatar}': profile["avatar"] || "/assets/image/no_photo",
      '${username}': profile["username"],
      '${email}': profile["email"]
    };
    $.each(params, function(key, value) {
      template = template.replace(key, value);
    });

    $("#content-container").append(template);
    if (profile["cover"]){
      $("#card-" + id + " .profile-card-bg").css("background-image", 'url(' + profile["cover"] + ')');
    }
    if (document.cookie.match(id)) {
      $("#card-" + id + " .follow-button").hide();
    } else {
      $("#card-" + id + " .follow-button").data("followUser" ,id);
      $("#card-" + id + " .follow-button").on("click", toggleFollow);
      if(profile["following"]) {
        $("#card-" + id + " .follow-button").addClass("following");
      }
    }
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
          popup();
          var curr = $("span.friend-value").text();
          if (follow_button.hasClass("following")) {
            follow_button.removeClass("following");
            if (document.cookie.match(visit_id)) {
              $("span.friend-value").text((curr * 1) - 1);
            }
          } else {
            follow_button.addClass("following");
            if (document.cookie.match(visit_id)) {
              $("span.friend-value").text((curr * 1) + 1);
            }
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
    if (document.cookie.match(visit_id)) {
      var stub_card = $('script[data-template="stub-card"]').text();
      $("#content-container").append(stub_card);
    } else {
      var reminder = '<div class="empty-reminder text-muted">Oops, seems like this user hasn\'t created any list yet</div>';
      $("#content-container").append(reminder);
    }
  }

  $.ajax({
    method: "GET",
    url: "/listinfo/" + visit_id,
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

  $("#confirmDelete").on("click", function(ev) {
    $.ajax({
      method: "GET",
      url: "/delete",
      data: {"listid": list_id},
      success: function(data) {
        window.location.reload();
      }
    })
  });
  
})