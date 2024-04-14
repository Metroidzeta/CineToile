$(document).ready(function() {
	let arrete = false;
	$(".owl-carousel").owlCarousel({
		items:1,
		loop:true,
		nav:true,
		dots:false,
		autoplay:true,
		//responsive: true,
		autoplayTimeout:5000,
		autoplaySpeed:500,
	});

	$('.owl-next, .owl-prev').on('click',function() {
		if(arrete == false) {
			setTimeout(function(){ //setTimeout de 200 ms pour éviter le bug du click de la flèche précédente dès le début.
				$('.owl-carousel').data('owl.carousel').options.autoplay = false;
				$('.owl-carousel').trigger('refresh.owl.carousel');
			}, 200);
			arrete = true;
		}
	});

	$("#autocomplete").autocomplete({
		source: function(request,response){
			$.ajax({
				url: '/CineToile/util/autocompletion',
				type: 'GET',
				dataType: 'json',
				data: { rechercher: request.term },
				success: function(resp) {
					response( $.map(resp, function(item) {
						return {
							label: item.label,
							value: item.value
						}
					}));
				}
			});
		},
		minLength: 3
	});
});