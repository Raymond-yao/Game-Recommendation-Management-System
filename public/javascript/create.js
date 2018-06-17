$(function () {
  var file = undefined;
  $("#pic").on('change', function() {
    file = this.files[0];
    if (file.size > 10485760) {
      file = undefined;
      alert('max upload size is 10M')
    }
  });

  $('#overview-bg-overlay').on("click", function() {
    $('#pic').trigger("click");
  });

  $('#pic').on("change", function(ev) {
    var overview_img = ev.currentTarget.files[0];
    if (overview_img) {
      var reader = new FileReader();

      $(reader).on("load", function() {
        $('#overview-bg-img').css("background-image", "url(" + reader.result + ")")
      });

      reader.readAsDataURL(file);
    }
  });

  $()

  var chosenGameIDs =[];
  var recReasons = [];
  $("#Submit").on("click", function (ev) {
    ev.preventDefault();
    var info = {
      title: $("#title").val(),
      cover: file,
      desc: $("#desc").val(),
      gameID: chosenGameIDs,
      recReasons: recReasons
    };
    if (info.title !== "" && info.desc !== "") {
      data = new FormData();
      data.append("title", $("#title").val());
      data.append("cover", file);
      data.append("desc", $("#desc").val());
      data.append("gameID", JSON.stringify(chosenGameIDs));
      $(".recommendation-reason").each(function(i) {
        recReasons.push($(this).find("textarea").val());
      });
      data.append("recReasons", JSON.stringify(recReasons));
      $.ajax({
        method: "POST",
        url: "/create",
        data: data,
        contentType: false,
        processData: false,
        success: function(data) {
          if (data["status"] === "success") {
            window.location.href = "/list/" + data["id"]; 
          } else {
            alert("upload failed");
          }
        }
      });
    }
  });

  var container = $('#popup-container');

  var btn = $("#add-game");
  var gameData = {};
  $.ajax({
    method: "GET",
    url: "/gameList",
    success: function(data) {
      var category = $('script[data-template="category"]').text();
      $.each(data, function(init, games) {
        $(".initial-container").append('<a href="#category-' + init + '" class="available-init">' + init + '</a>');
        var categ = category;
        categ = categ.replace("${init}", init);
        categ = categ.replace("${init_id}", init);
        container.append(categ);
        $.each(games, function(index, game) {
          var game_pic = $('script[data-template="game-pic"]').text();
          gameData[game["id"]] = game;
          var params = {
            "${cover_url}": game["cover"],
            "${game_name}": game["name"],
            "${company}": game["company"],
            "${date}": game["date"],
            "${order}": game["id"]
          };
          $.each(params, function(key, value) {
            game_pic = game_pic.replace(key, value);
          });
          $("#category-" + init + " .category-container").append(game_pic);
        });
      });
      
      $(".game-container").on("click", function() {
        var id = this.id.substring(5);
        chosenGameIDs.push(id);
        var gameItem = $('script[data-template="game-recommendation"]').text();
        var para = {
          "${game-id}": gameData[id]["id"],
          "${game_name}": gameData[id]["name"],
          "${company}": gameData[id]["company"],
          "${sale_date}": gameData[id]["date"],
          "${remove-id}": gameData[id]["id"],
          "${gameRec-id}": gameData[id]["id"]
        };
        $.each(para, function(key,value) {
          gameItem = gameItem.replace(key, value);
        });
        $("#game-preview").append(gameItem);
        $("#game-preview-" + id + " .game-cover").css("background-image", "url(" + gameData[id]["cover"] + ")");
        $(".close-popup").trigger("click");
        
        $("#remove-game-" + id).on("click", function(){
          $("#game-preview-" + id).remove();
          var index = chosenGameIDs.indexOf(id);
          chosenGameIDs = jQuery.grep(chosenGameIDs, function(value){
            return value != id;
          });
          recReasons = recReasons.splice(index, 1);
        });
      });
    },
    error: function(xhr) {
      alert("avatar request failed with status: " + xhr.status());
    }
  });

});