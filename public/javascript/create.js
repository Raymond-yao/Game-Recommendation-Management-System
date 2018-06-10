$(function () {
  var file = undefined;
  $("#pic").on('change', function() {
    file = this.files[0];
    if (file.size > 10485760) {
      file = undefined;
      alert('max upload size is 10M')
    }
  });

  $('#overview-bg-img').on("click", function() {
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
});