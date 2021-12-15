//Caroussel
$(".slider").owlCarousel({
    loop: true,
   // autoplay: true,
    //autoplayTimeout: 1000, //2000ms = s;
    autoplayHoverPause: true,
  });
 
  //Modal
  $(document).ready(function() { 
  $(function () {
    $('[data-toggle="tooltip"]').tooltip()
  })
});

//To display the file when you upload it
$('.custom-file-input').on('change', function(event) {
  var inputFile = event.currentTarget;
  $(inputFile).parent()
      .find('.custom-file-label')
      .html(inputFile.files[0].name);
});
