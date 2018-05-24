$(function () {
  $.ajax({
    method: 'GET',
    url: '/header',
    dataType: 'text',
    success: function(data) {
      $("div#header").html(data);
    }
  });
});