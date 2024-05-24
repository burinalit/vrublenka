(function ($) {
    $(document).ready(function () {
		document.addEventListener( 'wpcf7mailsent', function( event ) {
			location = 'https://vr.techwebs.ru/thanks/';
		}, false );
    });
})(jQuery);
jQuery(document).ready(function($){	
	var a = jQuery(".order_tabs");
	if (0 < a.length) {
		var n = "order_tab_active"
		  , i = "tab_content_active"
		  , s = a.find(".order_tab")
		  , r = a.find(".tab_content");
		r.each(function() {
			var e = jQuery(this).find("[data-content]");
			if (e.length) {
				var t = e.data("content");
				e.removeAttr("data-content"),
				e.html(t)
			}
		}),
		jQuery(document).trigger("form_loaded"),
		s.on("click", function() {
			var e = jQuery(this)
			  , t = e.data("id")
			  , a = r.filter('[data-id="' + t + '"]');
			s.removeClass(n),
			r.removeClass(i),
			e.addClass(n),
			a.addClass(i)
		})
	}
});

jQuery(document).ready(function () {
	jQuery( '#dl-menu' ).dlmenu({
		animationClasses : { in : 'dl-animate-in-2', out : 'dl-animate-out-2' }
	});
	jQuery(".vertical_menu").find('.active_term').addClass('selected');
    jQuery('.parent').click(function () {
    	var parent = jQuery(this).parent();
        var parents = jQuery(this).parentsUntil(".vertical_menu",".selected");
        if (parent.hasClass('selected')) {
            parent.removeClass('selected');
            jQuery(this).nextAll('ul').slideUp('fast',function(){
            	parent.find(".child").addClass("hidden").hide();
            })
        } else {            
            jQuery('.vertical_menu .selected').removeClass('selected');
			parents.addClass('selected');
            parent.addClass('selected');
            parent.parent().find(".child").addClass("hidden");
            parent.find('.child:first').removeClass("hidden").slideDown('fast'); 
        }
        jQuery(".child.hidden").slideUp('fast');
        return false;
    }); 
});
jQuery(document).ready(function($){
    $('.quest_faq_item').click(function () {
      $(this).toggleClass('active').next().slideToggle();
      $('.quest_faq_item').not(this).removeClass('active').next().slideUp();
    });
});
jQuery(document).ready(function($){
    $('.accordion .accordion__toggle').click(function () {
      $(this).toggleClass('active').next().slideToggle();
      $('.accordion .accordion__toggle').not(this).removeClass('active').next().slideUp();
    });
});
jQuery(document).ready(function($){
	 $(".tabs_item .cat_info").hover(
          function () {
              $(this).toggleClass('active').next().show();
          },
          function () {
              $(this).removeClass('active').next().hide();
          }
      );
});




function variChange(winWidth) {
  if (winWidth > 780) {
	$(document).ready(function(){
	  var scrolling = $(".mainmenu_block, main");
	  $(window).scroll(function(){
		if ( $(this).scrollTop() >= 147 && scrolling.hasClass("loading") ){
		  scrolling.removeClass("loading").addClass("scrolling");
		} else if($(this).scrollTop() <= 147 && scrolling.hasClass("scrolling")) {
		  scrolling.removeClass("scrolling").addClass("loading");
		}
	  });
	});
  }
  if (winWidth < 780) {
	$(document).ready(function(){
	  var scrolling = $(".header_mobile, main");
	  $(window).scroll(function(){
		if ( $(this).scrollTop() >= 147 && scrolling.hasClass("loading") ){
		  scrolling.removeClass("loading").addClass("scrolling");
		} else if($(this).scrollTop() <= 147 && scrolling.hasClass("scrolling")) {
		  scrolling.removeClass("scrolling").addClass("loading");
		}
	  });
	});
  }
}
//variChange(jQuery(window).width());

/* main menu */
var main = function() {
    jQuery('.header__menu-btn').click(function() { 
        jQuery('.menu_mobile').animate({
            left: '0px'
        }, 500);
    });
    jQuery('.menu_close_btn').click(function() {
        jQuery('.menu_mobile').animate({ 
            left: '-5000px'
        }, 500);
    });
};
jQuery(document).ready(main);

jQuery(document).ready(function($) {
	if (getCookie('wt-ask-about-location') != 1){
        jQuery('#hascity').trigger('click'); 
		jQuery('#hascity').show();		
        setCookie('wt-ask-about-location', 1);
    }
	$(function(){
		jQuery("#nocity").click(function(e) {
            e.preventDefault();
			e.stopPropagation();
			//jQuery('#hascity').hide();
			jQuery(this).parent().hide();
			jQuery('#listcity').fancybox().trigger('click'); 
		});
		jQuery("#mycity").click(function(e) {
			e.preventDefault();
			e.stopPropagation();
			//jQuery('#hascity').hide();
			jQuery(this).parent().hide();
		});
	});
    jQuery(".listcity_button").click(function() {
			jQuery('#listcity').fancybox().trigger('click'); 
		}); 
});