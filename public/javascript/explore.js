$(document).ready(function() {
    $("#panel-admin").css("display", "none");

    $('.open').click(function() {
        $("#panel-admin").animate({ width: 'toggle' }, 100);
    });

    if (!document.getElementById('wrapper').className && !localStorage.getItem("selectedColor")) {
        console.log('in if');
        document.getElementById('wrapper').classList.add('blue');
    } else {
        console.log('else');
        var colorClass = localStorage.getItem("selectedColor");
        document.getElementById('wrapper').classList.add(colorClass);
    }


    $('.panel-group').on('hidden.bs.collapse', toggleIcon);
    $('.panel-group').on('shown.bs.collapse', toggleIcon);

});




$(window).scroll(function() {

    if ($(this).scrollTop() > 50) {
        $('header').addClass("sticky");
    } else {
        $('header').removeClass("sticky");
    }
});



function toggleIcon(e) {
    $(e.target)
        .prev('.panel-heading')
        .find(".more-less")
        .toggleClass('fa-plus fa-minus');
}


$(function(){
    
    var random_list_id = Math.floor(Math.random() * 8) + 1;        
        $.ajax({
            method: "GET",
            url: "/toplists/" + random_list_id,
            success: function(data) {
               var i = 1;
                data.forEach(element => {
                    var url = element["list_info"]["cover"];
                    if(url == null){
                        url = "/assets/image/no_photo";
                    }
                    var title = element["list_info"]["title"];
                    var description = element["list_info"]["description"];
                    $("#top"+i+"-bg").css("background-image", 'url(' + url + ')');
                    $("#top"+i+"-title").text(title);
                    $("#top"+i+"-des").text(description);
                    $("#top"+i).on("click", function () {
                        window.location.href = "/list/" + element["list_info"]["id"];
                    })
                    i++;
                });
            },
            error: function() {
              alert("sorry, something goes wrong");
            }
          } )
    
          

})




