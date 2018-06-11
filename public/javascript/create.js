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

  $("#Submit").on("click", function (ev) {
    ev.preventDefault();
    var info = {
      title: $("#title").val(),
      cover: file,
      desc: $("#desc").val()
    };
    if (info.title !== "" && info.desc !== "") {
      data = new FormData();
      data.append("title", $("#title").val());
      data.append("cover", file);
      data.append("desc", $("#desc").val())
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
          var params = {
            "${cover_url}": game["cover"],
            "${game_name}": game["name"],
            "${company}": game["company"],
            "${date}": game["date"]
          };

          $.each(params, function(key, value) {
            game_pic = game_pic.replace(key, value);
          });
          $("#category-" + init + " .category-container").append(game_pic);
        });
      });
    },
    error: function(xhr) {
      alert("avatar request failed with status: " + xhr.status());
    }
  });

});