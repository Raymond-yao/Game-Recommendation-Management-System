
$(function() {

  function setupListInfo(info) {
    $("#article-avatar").css("background-image", "url(" + info["creator"]["avatar"] + ")");
    $("#overview-bg-img").css("background-image", "url(" + info["cover"] + ")");
    $(".article-title-text").text(info["title"]);
    $(".description").text(info["description"]);
    $(".author-info-text.username").text(info["creator"]["username"]);
    $(".author-info-text.profile-email").text(info["creator"]["email"]);
    $(".creator-link").attr("href", "/overview/" + info["creator"]["id"]);
    $(".author-info-text.created-date").text(info["created_date"]);
  }

  function setupGames(info) {
    var template = $('script[data-template="game-recommendation"]').text();
    $.each(info, function(index, game) {
      var card = template;
      var params = {
        '${game-id}': "game-" + index,
        '${game_name}': game["name"],
        '${company}': game["company"],
        '${sale_date}': game["date"],
        '${reason}': game["reason"],
      };
      $.each(params, function(key, value) {
        card = card.replace(key, value);
      });
      $("#article-content-section").append(card);
      if (game["cover"]) {
        $("#game-" + index + " .game-cover").css("background-image", "url(" + game["cover"] + ")");
      }
    })
  }

  $.ajax({
    method: "GET",
    url: "/list/" + list_id,
    success: function(data) {
      setupListInfo(data["list_info"]);
      setupGames(data["games"]);
    },
    error: function() {
      alert("sorry, something goes wrong");
    }
  })
})