jQuery(document).ready(function($){
	var spfp = $('.spfilter-products');
	if (spfp.length){
		spfp.each(function(){
			var _el = $(this) , _cf = $('.spfp-wrap'),
				_cl1 = _cf.data('spfp_column1'),
				_cl2 = _cf.data('spfp_column2'),
				_cl3 = _cf.data('spfp_column3'),
				_cl4 = _cf.data('spfp_column4');
			if ($(".carousel-slider-3").length){
				$('.owl-carousel', _el).owlCarousel({
					autoplay: _cf.data('spfp_autoplay') ? true : false,
					loop:false,
					margin:0,
					nav:true,
					dots:true,
					responsive:{
						0: {
							items: 1
						},
						500: {
							items: 1
						},
						768: {
							items: 1
						},
						1024: {
							items: 3
						},						
						1366: {
							items: 4
						}
					}
				});
			}else{
				$('.owl-carousel', _el).owlCarousel({
					autoplay: _cf.data('spfp_autoplay') ? true : false,
					loop:false,
					margin:0,
					nav:true,
					dots:true,
					responsive:{
						0: {
							touchDrag:false,
							mouseDrag:false,
							items: 1
						},
						500: {
							touchDrag:false,
							mouseDrag:false,
							items: _cl4
						},
						768: {
							touchDrag:false,
							mouseDrag:false,
							items: _cl3
						},
						1024: {
							items: _cl2
						},						
						1366: {
							items: _cl1
						}
					}
				});
			}
			_showCountDown(_el);
			function _showCountDown(_el){
				var _countdown = $('.product-countdown', _el);	
				if (_countdown.length){
					var lbl_day = _countdown.data('number_day'),
						lbl_days = _countdown.data('number_days'),
						lbl_hour = _countdown.data('number_hour'),
						lbl_hours = _countdown.data('number_hours'),
						lbl_minute = _countdown.data('number_minute'),
						lbl_minutes = _countdown.data('number_minutes'),
						lbl_secon = _countdown.data('number_secon'),
						lbl_secons = _countdown.data('number_secons'),
						nb_timer = _countdown.data('time');
					_countdown.each(function(){
						var _clock_timer = $('.clock-time', this).length ? $('.clock-time', this) : $(this);
						var hasCountdown = $(this).data('time');
						if (hasCountdown && hasCountdown != 'ulimited'){
							var pluralize = function(c, vs, vp){
								return c>1 ? vp : vs;
							};
							_clock_timer.countdown({
								date: new Date(hasCountdown),
								render: function(data) {
									var format = '';
										format += '<span class="day">'  + data.days  + '<span>' + pluralize(
											data.days,  lbl_day,   lbl_days)    +'</span></span>';
										format += '<span class="hour">' + this.leadingZeros(data.hours, 2) + '<span>' + pluralize(
											data.hours, lbl_hour,  lbl_hours)   +'</span></span>';
										format += '<span class="min">'  + this.leadingZeros(data.min, 2)   + '<span>' + pluralize(
											data.min,   lbl_minute, lbl_minutes) +'</span></span>';
										format += '<span class="sec">'  + this.leadingZeros(data.sec, 2)   + '<span>' + pluralize(
											data.sec,   lbl_secon, lbl_secons) +'</span></span>';
									$(this.el).html(format);
								}
							});
						}
					});
				}
			}
		});
	}
});
jQuery(document).ready(function($){
var owl = $('.marqueeslider');
owl.owlCarousel({
    items:1,
    loop:true,
    margin:10,
    autoplay:true,
    autoplayTimeout:3000,
    autoplayHoverPause:true,
    nav:true,
	dots:false
});
});





