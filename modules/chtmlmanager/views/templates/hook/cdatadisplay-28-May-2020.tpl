{if $c_html[$language.iso_code]}
<section class="emersya3D">
	<div class="tWrap 3dwrap">
		         
		<script src="https://code.jquery.com/jquery-3.5.1.min.js" integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script>

		<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/jquery-ui.min.js" type="text/javascript"></script>

		<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/smoothness/jquery-ui.css">


		<div class='configurationPanel mapping classHide'>

			<div class='configurationPanelSubSectionChoiceSelector play-btn-an pause-btn-an' id="pl-idn"></div>

			<div class="map-slider">
				<div class="buttons">
					<div id="plus">+</div>
						<div id="sliderr" style="width:400px; margin-top:100px;" sl-val="1" str="11" values="1"></div>
					<div id="minus">-</div>
				</div>
			</div>

			<div class='configurationPanelSubSectionChoiceSelector cam-look' id="cam-look-vs"></div>

		</div>

		<div class='configurationPanel classHide' id="preset-manager"></div>

		<iframe id='emersyaIframe' src='{$c_html[$language.iso_code] nofilter}' allow='camera; gyroscope; accelerometer; magnetometer;' frameborder=0 webkitallowfullscreen='' mozallowfullscreen='' allowfullscreen='' width='1040' height='757' style='border:none;display:block;'></iframe>

		<div class='configurationPanel-removed' style='--Example: 200px;display:none;position:absolute; width:calc(100% - var(--Example)/2); left:50px; background-color:white; padding:10px; box-sizing:border-box;border-radius: 30px;text-align: center;'>
			<div class="drag-section">
				<svg class="svg-3d">
				  <line x1="0" y1="0" x2="270" y2="0" style="stroke: rgb(140, 137, 137);stroke-width: 1px;"></line>
				</svg>
				<div class="arrow-left"></div>
				<div class="hand-drag"></div>
				<div class="arrow-right"></div>
				<svg class="svg-3d">
				  <line x1="0" y1="0" x2="270" y2="0" style="stroke: rgb(140, 137, 137);stroke-width: 1px;"></line>
				</svg>
			</div>
		</div>


		<script type='text/javascript'>


			var viewerIframe = null;
			var viewerActive = false;
			document.getElementById('emersyaIframe').onload = function() {
				viewerIframe = document.getElementById('emersyaIframe').contentWindow;

				window.removeEventListener('message', viewerEventListener ,false);

				viewerIframe.postMessage({
					action : 'registerCallback'
				}, '*');

				window.addEventListener('message', viewerEventListener, false);
				viewerIframe.postMessage({
					action:'getViewerState'
				}, '*');



				document.getElementById("pl-idn").addEventListener("click", function() {
				  	var element = document.getElementById("pl-idn");
						element.classList.toggle("pause-btn-an");
				});


				$('.play-btn-an').click(function(){

					viewerIframe.postMessage({
						action : 'play'
					},'*');
				});


				$(document).on("click", ".play-btn-an.pause-btn-an" , function(){
					viewerIframe.postMessage({
						action : 'pause'
					},'*');
				})



				var sl_val = parseInt($('#sliderr').attr('sl-val'));
				var sliderr = $( "#sliderr" ).slider({
				min: 1,
				range: false,
				step: .0001,
				max: 8,
				value: 1,
				animate:"slow",
				orientation: "vertical",
				slide: function( event, ui ) {

				$(".value").text("sliderr value: " + Math.round(ui.value));
				var slider_value = Math.round($("#sliderr").slider('value'));

				},
				stop: function( event, ui ) {
					
					var sl_val = parseInt($('#sliderr').attr('sl-val'));

					var values = Math.round($('#sliderr').attr('values'));
					

					if (values < Math.round(ui.value)) {
						
						viewerIframe.postMessage({
							action : 'startZoomIn'
						},'*');

						setTimeout(
							function() {
								viewerIframe.postMessage({
									action : 'stopZoomIn'
								},'*');
								$('#minus').css('pointer-events','all');
						}, 750);

						sl_val = (sl_val + 1);
						$('#sliderr').attr('sl-val',sl_val);
						

					}
					else if((values > Math.round(ui.value))){
						
						viewerIframe.postMessage({
							action : 'startZoomOut'
						},'*');

						setTimeout(function() {
							viewerIframe.postMessage({
								action : 'stopZoomOut'
							},'*');
							$('#plus').css('pointer-events','all');

						}, 400);

						sl_val = (sl_val - 1);
						$('#sliderr').attr('sl-val',sl_val);
					}

					

					$('#sliderr').attr('values',sl_val);
					//$("#sliderr").slider('value',Math.round(ui.value));
					Math.round($("#sliderr").slider('value',sl_val));
					
					}
				});

				$('#minus').on('click',function(){

					$('#minus').css('pointer-events','none');
					$('#plus').css('pointer-events','all');
					values = parseInt($('#sliderr').attr('values'));

					var cur_valm = Math.round($("#sliderr").slider('value'));
					var sl_valm = parseInt($('#sliderr').attr('sl-val'));

					if(cur_valm > 1) {
						inc_sl_valm = sl_valm-1;
						$('#sliderr').attr('sl-val',inc_sl_valm);

						values = values-1;
						$("#sliderr").slider('value',values);
						$('#sliderr').attr('values',values);
						

						viewerIframe.postMessage({
							action : 'startZoomOut'
						},'*');

						setTimeout(function() {
							viewerIframe.postMessage({
								action : 'stopZoomOut'
							},'*');

							if(cur_valm > 2){
								$('#minus').css('pointer-events','all');
							}
							

						}, 400);
					}
				
				});

				$('#plus').on('click',function(){

					cd = parseInt($('#sliderr').attr('values'));

					$('#plus').css('pointer-events','none');
					$('#minus').css('pointer-events','all');;

					values = parseInt($('#sliderr').attr('values'));

					var cur_valm = Math.round($("#sliderr").slider('value'));
					var sl_valm = parseInt($('#sliderr').attr('sl-val'));

					if(cur_valm < 8){
						inc_sl_valm = sl_valm+1;
						$('#sliderr').attr('sl-val',inc_sl_valm);

						values = values+1;
						$("#sliderr").slider('value',values);
						$('#sliderr').attr('values',values);
						

						viewerIframe.postMessage({
							action : 'startZoomIn'
						},'*');

						setTimeout(
							function() {
								viewerIframe.postMessage({
									action : 'stopZoomIn'
								},'*');

								if(cur_valm < 7){
									$('#plus').css('pointer-events','all');
								}
								
						}, 750);
					}
				});



				$('#cam-look-vs').on('click', function(){
					viewerIframe.postMessage({
						action : 'toggleAnnotationsPin'
					},'*');
					viewerIframe.postMessage({
						action : 'toggleAnimationTriggers'
					},'*');
				});

				$('#cam-look-vs').on('click',function(){
				    $('.draggable-button ui-draggable').css('top','170px');
				});

				$(document).on('click', '.configurationPanelSubSectionChoiceSelector', function(){

				var name = $(this).attr("data-preset");

					viewerIframe.postMessage({
						action : 'setPreset',
						presetName : name
					},'*');

				})

				  	
			};

			var viewerEventListener =  function(event){
				

				if (event.data.action == 'onEndCameraInteraction') {

					$( ".play-btn-an" ).addClass( "pause-btn-an" );

				}
				if (event.data.action == 'onStartCameraInteraction') {

					$( ".play-btn-an" ).removeClass( "pause-btn-an" );


				}
				
				if(event.data && event.data.action == 'onStateChange'){
					if(event.data.state.viewerState == 'loaded' || event.data.state.viewerState == 'fallbackloaded'){
						
						viewerActive = true;


						$.ajax({
					        url: 'https://ws.emersya.com/ws/0.3/presets/get?routingCode=0OQ48LMKD5'
					        , success: function(data){

					        	$('.configurationPanel').removeClass('classHide');

					        	offset = $('.3dwrap').offset();
								$('#preset-manager').css("top", (offset.top+150)+"px");

					        	$( "#preset-manager div" ).remove();

					        	var obj = data.resultSet.data[0];

					            $(obj).each(function( index, value ) {


								  $( "#preset-manager" ).append( "<div id='"+value.name+"-color' class='configurationPanelSubSectionChoiceSelector clr'  data-preset ='"+value.name+"' style='margin:10px 5px; background-color:"+value.color+";'></div>" );
								});
					        }
						})
					}
				}
				if(event.data && event.data.action == 'onError'){
					console.log(event);
				}
			};

			var switchConfiguration = function(configurationName){
				if(viewerActive){
					viewerIframe.postMessage({ action:'beginTransaction' }, '*');
					viewerIframe.postMessage({
						action:'setMaterialByName',
						materialSettings : 'logo/'+configurationName
					}, '*');
					viewerIframe.postMessage({ action:'endTransaction' }, '*');
				};
			};


		</script>

		<style type="text/css">

			.classHide {
				display: none!important;
			}

			.configurationPanelSubSectionChoiceSelector.play-btn-an {
				margin:10px;
				background:url("../../themes/takuma/assets/img/emersya/pause.png");
				width:25px;
				height:25px;
				cursor:pointer;
	    		background-repeat: no-repeat;		
			}
			.configurationPanelSubSectionChoiceSelector.play-btn-an.pause-btn-an {
				margin:10px;
				background:url("../../themes/takuma/assets/img/emersya/Nplay.png");
				width:25px;
				height:25px;
				cursor:pointer;
	    		background-repeat: no-repeat;		
			}
			.configurationPanelSubSectionChoiceSelector.plus-btn-add {
				margin:10px;
				background:url("../../themes/takuma/assets/img/emersya/plus.png");
				height:25px;
				cursor:pointer;
	    		background-repeat: no-repeat;
			}
			.configurationPanelSubSectionChoiceSelector.m-btn-minus {
				margin:10px;
				background:url("../../themes/takuma/assets/img/emersya/minus.png");
				height:25px;
				cursor:pointer;
	    		background-repeat: no-repeat;
			}
			.configurationPanelSubSectionChoiceSelector.cam-look {
				margin:10px;
				background:url("../../themes/takuma/assets/img/emersya/Neye.png");
				height:25px;
				cursor:pointer;
	    		background-repeat: no-repeat;
			}
			



			*, *:after, *:before {
				box-sizing: border-box;
			}

			.map-slider {
				 height: 330px;
				 background: transparent;
				 text-align: center;
				 position: relative;
			}
			
			.map-slider .fa-plus {
				 display: block;
				 padding-top: 16px;
				 height: 50px;
				 cursor: pointer;
			}
			.map-slider .fa-minus {
				 display: block;
				 height: 50px;
				 padding-top: 12px;
				 cursor: pointer;
			}
			.map-slider .drag-line {
				 width: 2px;
				 height: 500px;
				 background: #0c0c0c;
				 border-radius: 8px;
				 margin: 25px auto;
				 position: relative;
			}
			.map-slider .line {
				 width: 2px;
				 height: 198px;
				 background: #000000;
				 border-radius: 8px;
				 margin: 25px auto;
				 position: absolute;
				 margin-top: 0px;
				 margin-bottom: 0px;
				 padding-top: 10px;
				 clip: rect(0px, 8px, 183px, 0px);
			}
			.map-slider .draggable-button {
				 width: 12px;
				 height: 12px;
				 background: url('../../themes/takuma/assets/img/emersya/Ndot.png');
				 border-radius: 50%;
				 position: absolute;
				 margin-left: -5px;
				 cursor: pointer;
				 top :198px;
			}
	 		.mapping {
	 			display:block;
	 			position:absolute;  
	 			left:20px; 
	 			background-color:transparent; 
	 			padding:20px 0;
	 		}
	 		.svg-3d {
	 			width: 270px;
	 			height: 5px;
	 		}
	 		@media only screen and (max-width: 768px) {
	 			.mapping {  
		 			left:0px;
		 			display:flex;
		 		}
		 		iframe#emersyaIframe {
				    padding: 60px 0 0;
				}
				.svg-3d {
		 			width: 70px;
		 			height: 5px;
		 		}
		 		.configurationPanelSubSectionChoiceSelector.clr{
					width:16px; 
					height:16px; 
					border-radius:15px; 
					cursor:pointer;
					border: 1px solid #e8e3e3;
				}
				#preset-manager {
					display:flex;
					position:absolute; 
					right:0px; 
					background-color:transparent; 
					padding:20px;
					top:unset!important;
				}
				.cam-look {
					width: 30px
				}
				.map-slider {
					display: none;
				}
	 		}

	 		@media only screen and (min-width: 1200px) {

	 			.configurationPanelSubSectionChoiceSelector.clr{
					width:20px; 
					height:20px; 
					border-radius:15px; 
					cursor:pointer;
					border: 1px solid #e8e3e3;
				}
				#preset-manager {
					display:block;
					position:absolute; 
					right:20px; 
					background-color:transparent; 
					padding:10px;
				}
				
	 		}

			 div#sliderr {
				pointer-events: none;
			}
			a.ui-slider-handle.ui-state-default.ui-corner-all {
				transition: all .3s;
				border-radius: 15px;
				right: 0px;
				width: 13px;
				height: 13px;
				left: -6px;
				margin: 0!important;
				background: #202020!important;
			}
			.ui-slider-handle {
				pointer-events :all;
			}

			a.ui-slider-handle.ui-state-default.ui-corner-all.ui-state-active {
				transition: none;
			}
			.ui-slider-handle:focus {
				outline:none!important;
			}
			#sliderr {
				width: 0px!important;
				height: 264px!important;
				margin: 5px!important;
				border: 0.2px solid #202020;
			}
			div#minus {
				width: 15px;
			}
			div#plus {
				width: 15px;
			}
			.map-slider {
				padding-left:3px;
			}
		</style>        
	</div>
</section>
{/if}