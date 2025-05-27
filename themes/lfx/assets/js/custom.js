$(function(){
    //customer reg placeholder
    $('#customer-form input[type="text"], #customer-form input[type="email"], #customer-form input[type="password"]').each(function () {
        var input = $(this);
        var inputId = input.attr('id');
        
        if (inputId) {
        var label = $('label[for="' + inputId + '"]');
        var labelText = label.text().trim();
        
        if (labelText) {
            input.attr('placeholder', labelText);
            label.hide();
        }
        }
    });
    //login form placeholder
    $(document).ready(function () {
        $('#login-form input[type="text"], #login-form input[type="email"], #login-form input[type="password"]').each(function () {
            var $input = $(this);
            var inputId = $input.attr('id');

            if (inputId) {
            var $label = $('label[for="' + inputId + '"]');
            var labelText = $label.text().trim();

            if (labelText) {
                $input.attr('placeholder', labelText);
                $label.hide();
            }
            }
        });
    });


    $('#_desktop_user_info .dropdown-toggle').click(function (e) {
        e.stopPropagation();
        const $menu = $('#_desktop_user_info .dropdown-menu');
        const $overlay = $('#_desktop_user_info #screen-overlay');

        if ($menu.is(':visible')) {
        $menu.fadeOut(200);
        $overlay.fadeOut(200);
        } else {
        $menu.fadeIn(200);
        $overlay.fadeIn(200);
        }
    });

    $('#_desktop_user_info .dropdown-menu').click(function (e) {
        e.stopPropagation();
    });

    $(document).click(function (e) {
            if (!$(e.target).closest('#_desktop_user_info .dropdown-toggle, #_desktop_user_info .dropdown-menu').length) {
            $('#_desktop_user_info .dropdown-menu').fadeOut(200);
            $('#_desktop_user_info #screen-overlay').fadeOut(200);
            }
        });

    $('.blockcart.cart-preview.inactive .header').click(function () {
        $('#screen-overlay-cart').fadeIn(200);
        $('.cart-empty-message').fadeIn(200);
    });

    $(document).click(function (e) {
        if (!$(e.target).closest('.blockcart.cart-preview.inactive .header, .cart-empty-message').length) {
            $('#screen-overlay-cart').fadeOut(200);
            $('.cart-empty-message').fadeOut(200);
        }
    });

    $('.cart-empty-message').click(function (e) {
        e.stopPropagation(); // Prevent click from bubbling to document
    });

        
    $('.read-more-toggle').on('click', function () {
        var $container = $(this).closest('.cat-desc-section');
        var $short = $container.find('.cat-desc-short');
        var $full = $container.find('.cat-desc-full');
        var $btn = $(this);
    
        $short.toggle();
        $full.toggle();
    
        $btn.text($full.is(':visible') ? 'Réduire' : 'Lire la suite');
    });
    
    $(".p_box .color").hover(
        function () {
            var newImgSrc = $(this).data("img-str-label");
            var parentBox = $(this).closest(".p_box");
            var imgElement = parentBox.find("picture.product-img-default img");

            // Save the original src only once
            if (!imgElement.data("original-src")) {
            imgElement.data("original-src", imgElement.attr("src"));
            }

            // Change to the new image
            if (newImgSrc) {
            imgElement.attr("src", newImgSrc);
            }
        },
        function () {
            var parentBox = $(this).closest(".p_box");
            var imgElement = parentBox.find("picture.product-img-default img");
            var originalSrc = imgElement.data("original-src");

            if (originalSrc) {
            imgElement.attr("src", originalSrc);
            }
        }
    );

    $('.products_slider').owlCarousel({
        margin: 18,
        items: 4,
        nav:true,
        loop: false,
        responsive:{
        0:{
            items:1,
        },
        640:{
            items:2,
        },
        992:{
            items:3,
        },
        1600:{
            items:4,
        },
            }
    });

    $('.universe_slider').owlCarousel({
        margin:25,
        items: 4,
        nav:true,
        loop: false,
        responsive:{
            0:{
                items:1,
            },
            440:{
                items:2,
            },
            992:{
                items:3,
            },
            1600:{
                items:4,
            },
        }
    });

    $('.maylike_slider').owlCarousel({
        margin: 25,
        items: 4,
        nav:true,
        loop: false,
        responsive:{
            0:{
                items:1,
            },
            640:{
                items:2,
            },
            992:{
                items:3,
            },
            1200:{
                items:4,
            },
        }
    });

    $('.featured_carousel').owlCarousel({
        margin: 25,
        items: 4,
        nav:true,
        loop: false,
        autoplay:true,
autoplayTimeout:4000,
        responsive:{
            0:{
                items:1,
            },
            640:{
                items:1,
            },
            992:{
                items:2,
            },
            1200:{
                items:3,
            },
        }
    });

   
    $('.bannerslider').owlCarousel({
        items: 1,
        animateOut: 'fadeOut',
        loop: true,
        margin: 10,
        nav:false,
        autoplay: true,
        autoplayTimeout: 4000,
        autoplayHoverPause: true
    });

    
    $('.quad_slider').owlCarousel({
        items: 1,
        animateOut: 'fadeOut',
        loop: true,
        margin: 10,
        nav:false,
        responsive:{
            0:{
                items:1,
            },
            640:{
                items:2,
            },
            992:{
                items:0,
            },
            1200:{
                items:0,
            },
        }
      });


    $('.loop').owlCarousel({
    stagePadding: 50,
    loop:true,
    center: true,
    items:1,
    lazyLoad: true,
    autoplay: false,
    autoplaySpeed: 2000,
    autoplayTimeout: 5000,
    autoplayHoverPause: true,
    responsive:{
        0:{
          items: 1,
          stagePadding: 0,
          margin: 30,
        },
        767:{
          items: 1,
          stagePadding: 25,
        },
        1024:{
          items: 1,
          stagePadding: 170,
        },
        1300:{
          stagePadding: 220,
          items: 1,
        },
        1500:{
          stagePadding: 290,
          items: 1,
        },
        1700:{
          stagePadding: 420,
          items: 1,
        }
        
      }
})



    // custom accordion
    $(function () {
        if ($('.accordion-list').length) {
            $('.accordion-list').on('click', '.accordion-title', function (e) {
                e.preventDefault();
                // remove siblings activities
                $(this).closest('.accordion-list-item').siblings().removeClass('open').find('.accordion-desc').slideUp();
                $(this).closest('.accordion-list-item').siblings().find('.ni').addClass('ni-plus').removeClass('ni-minus');

                // add slideToggle into this
                $(this).closest('.accordion-list-item').toggleClass('open').find('.accordion-desc').slideToggle();
                $(this).find('.ni').toggleClass('ni-plus ni-minus');
            });
        }
    });

function myfcFunction() {
    document.getElementById("collapseOne").classList.add("in");
 }






    $('.HeaderMarquee').owlCarousel({
        items: 1,
        loop: true,
        margin: 0,
        nav:false,
        autoplay:true,
        autoplayTimeout:7000,
        autoplayHoverPause:false,
        nav:true,
    });

    // $('.category-thumb').owlCarousel({
    //     margin:10,
    //     loop:false,
    //     autoWidth:true,
    //     items:4
    // });


    if(prestashop.page.page_name == 'registration') {
        var lastnameContent = $('.form-lastname').contents().detach();
        $('.form-firstname').append(lastnameContent);    
    }
});


function openNav() {
    document.getElementById("left-column").style.width = "100%";
}

function closeNav() {
    document.getElementById("left-column").style.width = "0%";
}


function TestsFunction() {
    var T = document.getElementById("left-column"),
        displayValue = "";
    if (T.style.display == "")
        displayValue = "block";

    T.style.display = displayValue;
}




            $(window).scroll(function() {
        var scroll = $(window).scrollTop();
        var navHeight = $('#header').height();
        if (scroll >= navHeight ) {
            $('#header').addClass('sticky');
        } else {
            $('#header').removeClass('sticky');
        }
    });
    $(".smenu").click(function (e) {
        e.stopPropagation(); // prevent this click from triggering the document click
        leoMenuIntialization();
        $(".smenu").toggleClass("active");
        $(".navbar-menu").toggleClass("active");
        $(".navbar-bg").toggleClass("active");
      });
      
      // Close the menu when clicking outside
      $(document).click(function (e) {
        if (!$(e.target).closest('.navbar-menu, .smenu').length) {
          $(".smenu").removeClass("active");
          $(".navbar-menu").removeClass("active");
          $(".navbar-bg").removeClass("active");
        }
      });
      
        
$(function(){
    //clickFaceAc();
    clickFace();
});
if (typeof prestashop !== 'undefined') {
    prestashop.on(
      'updateProductList',
      function (event) {
        setInterval(function () {
            //clickFaceAc();
            clickFace();
        }, 700);        
      }
    );
  }
function clickFace()
{
    $('#search_filters .facet .facet-title').on('click', function(){
        var target = $(this).data('target');
        $(target).toggleClass('face-sec-col');
        $(this)
          .toggleClass("active")
          .next(".collapse")
          .slideToggle()
          .parent()
          .siblings()
          .find(".collapse")
          .slideUp()
          .prev()
          .removeClass("active");
     });


        $('.GeometryTablerow').hover(function () {
        $(".menu-"+this.id).css("opacity", "1");
    },
    function () {
        $(".menu-"+this.id).css("opacity", "0");
    });

}


document.addEventListener('DOMContentLoaded', function () {
    if(prestashop.page.page_name == 'index') {
        const duration = 25000; //ms
            const directionAnimation = 'left';  //left or right  
        
            const marquee = document.querySelector('.marquee');
            const span = marquee.querySelector('span');

            const marqueeWidth = marquee.offsetWidth;
            const spanWidth = span.offsetWidth;
        
        let keyframes = [];
        if('left' == directionAnimation){
            keyframes = [        
                { transform: `translateX(${marqueeWidth}px)` },
                { transform: `translateX(${-spanWidth}px)` }
            ];
        }
        else if('right' == directionAnimation){
            keyframes = [        
                { transform:  `translateX(${-spanWidth}px)`},
                { transform: `translateX(${marqueeWidth}px)` }
            ];
        }
        
        let options = {
            duration: duration, // Durata dell'animazione in millisecondi
            iterations: Infinity,
            easing: "linear"
        };

        const marqueeAnimation = span.animate(keyframes, options);
        
        marquee.addEventListener('mouseenter', () => {
        marqueeAnimation.pause();
        });

        marquee.addEventListener('mouseleave', () => {
            marqueeAnimation.play();
        });
    }
    
});



$('.user-info a').click(function(){
  $('#body-overlay').toggleClass('overlayed');
});

// $('.leo-top-menu .navbar-nav .nav-item.parent').hover(function(){
//   $('.navbar_menusec').toggleClass('navhover');
// });

function leoMenuIntialization() {
    $('nav.navbar-menu.sidemainmenu.active a.nav-link.dropdown-toggle.has-category').each(function () {
        const href = $(this).attr('href');
        if (href && href.match(/#\.?$/)) {
            $(this).attr('href', 'javascript:void(0)');
        }
    });

    // Handle menu item click to show the submenu
    $('.sidemainmenu a.nav-link.dropdown-toggle.has-category').on('click', function (e) {
        e.preventDefault(); // Prevent default link action

        const $parentLi = $(this).closest('.nav-item.parent');
        const $submenu = $parentLi.find('.dropdown-sub').first();
        const menuTitle = $(this).find('.menu-title').text();

        // Hide all <a> elements in the main menu
        $('.nav-item > a.nav-link').hide();

        // Show submenu
        $submenu.show();

        // Add submenu title and back button if not already added
        if ($submenu.find('.submenu-header-wrapper').length === 0) {
            const backButton = $('<div class="back-button">← Retour</div>');
            const titleDiv = $('<div class="submenu-header"></div>').text(menuTitle);
            const headerWrapper = $('<div class="submenu-header-wrapper"></div>')
                .append(backButton)
                .append(titleDiv);
            $submenu.prepend(headerWrapper);

            // Add click event for back button inside this function
            backButton.on('click', handleBackButtonClick); // Attaching the function to the back button
        }
    });
}

// Separate function to handle the back button click
function handleBackButtonClick() {
    const $submenu = $(this).closest('.dropdown-sub'); // Find the closest submenu
    $submenu.hide(); // Hide the submenu

    // Show all <a> elements in the main menu again
    $('.nav-item > a.nav-link').show();
}

// Make sure to initialize after DOM is ready
$(document).ready(function () {
    leoMenuIntialization();
});


$(document).ready(function () {
  $('#toggleSearch').on('click', function (e) {
    e.stopPropagation(); // Prevent click from bubbling
    $('#searchBox').toggleClass('expanded');
    $('#searchBox .search-input').focus();
  });

  $('#searchBox').on('click', function (e) {
    e.stopPropagation(); // Prevent clicks inside the box from closing it
  });

  $(document).on('click', function () {
    $('#searchBox').removeClass('expanded');
  });
});

function selectCombination(combinationId, el) {
    if (combinationId && el) {
        
        const container = $(el).closest('.p_box');
        const hiddenInput = container.find('.pro-combination-sec');

        if (hiddenInput.length) {
            hiddenInput.val(combinationId);
        }

        const variantLinks = $(el).closest('.variant-links');
        variantLinks.find('.selected-combination').removeClass('selected-combination');
        $(el).addClass('selected-combination');
    }
}


$('.accordion .card-header h5').click(function () {
    $('body,html').animate({
        scrollTop: 800
    }, 1200);
});

