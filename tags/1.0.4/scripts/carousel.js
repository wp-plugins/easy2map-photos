;(function(jQuery) {
    jQuery.preloadImages = function(images, func) {        
        var i = 0;
        var cache = [];
        var loaded = 0;
        var num = images.length;
        
        for ( ; i < num; i++ ) (function(i) {
            
            var new_image = jQuery('<img/>').attr('src', images[i]).load(function(){
                loaded++;
                
                if(loaded == num)
                {                                                
                    func();                   
                }
            });
            cache.push(new_image);
        })(i);
        
        return true;
    };
    
    jQuery.fn.imgSlider = function(images) {        
        if (!jQuery(this).length || jQuery(this).length>1) {            
            return this;
        }
        
        var direction = 'right';
        var e = this;
        var timeout_id = 0;
        var in_progress = false
        var i = 0;
        var num_slides = jQuery(e).find('.easy2mapholder > li').length;
        var slide_widths = jQuery(e).find('.easy2mapholder > li:first').width();
        var speed = 200;
        
        for ( ; i < num_slides; i++ ) (function(i) {
            jQuery(e).find('.easy2mapholder > li').eq(i).css('background', 'url('+images[i]+') no-repeat');
        })(i);
        
        function slider_animate(new_dir)
        {
            //clearTimeout(timeout_id);
            //timeout_id = setTimeout(auto_animate, 5000);
            in_progress = true;
            
            var dir = direction;
            
            if(new_dir)
            {
                dir = new_dir;
            }
            
            if(dir == 'right')
            {
                var toMove = jQuery(e).find('.easy2mapholder').children('li:first');
                var oldMargin = jQuery(toMove).css('margin-right');
                jQuery(toMove).animate({'margin-left':'-'+slide_widths+'px', 'margin-right':'0px'}, speed, null, function(){                    
                    jQuery(this).appendTo(jQuery(this).parent()).css({'margin-left':'0px', 'margin-right':oldMargin});                    
                    in_progress = false;
                }); 
            }
            else
            {
                jQuery(e).find('.easy2mapholder').children('li:eq(2)').animate({}, speed, null, function(){});
                jQuery(e).find('.easy2mapholder').children('li:last').css('margin-left', '-'+slide_widths+'px').prependTo('.easy2mapholder').animate({'margin-left':'0px'}, speed, null, function(){                        
                    in_progress = false;                    
                });                
            }
        }
        
       /*jQuery(e).find('.easy2mapholder > li').hover(function(){
            //clearTimeout(timeout_id);            
            jQuery(this).find('.easy2mapcaption').stop().fadeTo(500, 0.8);            
        },function(){
            jQuery(this).find('.easy2mapcaption').stop().fadeTo(500, 0);            
            //timeout_id = setTimeout(auto_animate, 3000);            
        });*/
        
        function auto_animate()
        {
            slider_animate('right');            
        }
        
        jQuery(e).find('.easy2mapnext').click(function(){
            if(!in_progress)
            {
                slider_animate('right');
            }
            
            return false;
        });
        
        jQuery(e).find('.easy2mapprev').click(function(){
            if(!in_progress)
            {
                slider_animate('left');
            }
            
            return false;
        });
        
        //timeout_id = setTimeout(auto_animate, 3000);        
      
        return true;
    };
})(jQuery);