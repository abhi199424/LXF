/*
 * Custom code goes here.
 * This file is not loading, check custom.js
 */

$(document).ready(function() {
	$('.OwT').owlCarousel({
		loop:true,
		margin:10,
		autoplay:true,
		nav:true,
		responsive:{
			0:{
				items:1
			},
			600:{
				items:1
			},
			1000:{
				items:1
			}
		}
	})	
});