(function ($) {

    $.fn.appsgal = function (options) {

        var defaults = $.extend({
    		visibleItems: 4,
    		animationSpeed: 200,
    		autoPlay: false,
    		autoPlaySpeed: 3000,    		
    		pauseOnHover: true,
    		scrollForward: true,
    		showNav : false,
    		randomize: false,
    		divId: 'appsgal-'+$(this).attr('id'),
    		offSet: 0,
			setMaxWidthAndHeight: false,
    		enableResponsiveBreakpoints: false,
    		responsiveBreakpoints: { 
				phone: { 
	    			changePoint:479,
	    			visibleItems: 2
	    		},
	    		portrait: { 
	    			changePoint:579,
	    			visibleItems: 3
	    		},  
	    		secondportrait: { 
	    			changePoint:767,
	    			visibleItems: 4
	    		}, 
	    		landscape: { 
	    			changePoint:867,
	    			visibleItems: 5
	    		},
	    		tablet: { 
	    			changePoint:959,
	    			visibleItems: 6
	    		}
        	}
        }, options);
  
        
		/******************************
		Private Variables
		*******************************/         
        
        var object = $jq(this);
		var settings = $jq.extend(defaults, options);        
		var itemsWidth;
		var canNavigate = true; 
        var itemsVisible = settings.visibleItems; 
        var responsivePoints = [];
		var initialScroll = true;

		/******************************
		Public Methods
		*******************************/        

        var methods = {
        		
			init: function() {
				
        		return this.each(function () {
        			methods.appendHTML();
        			methods.setEventHandlers();      			
        			methods.initializeItems();
				});
			},			
			
			initializeItems: function() {
				
				var listParent = object.parent();
				var innerHeight = listParent.height(); 
				var childSet = object.children();
				methods.sortResponsiveObject(settings.responsiveBreakpoints);
				
    			var innerWidth = listParent.width(); // Set width of inner slider based on parent
    			itemsWidth = (innerWidth)/itemsVisible;
    			childSet.width(itemsWidth);
    			childSet.last().insertBefore(childSet.first());
    			childSet.last().insertBefore(childSet.first());
    			if(settings.scrollForward)
    				object.css({'left' : 0});
    			else
    				object.css({'left' : -itemsWidth}); 
    			
    			object.fadeIn();
				$jq(window).trigger("resize"); // needed to position arrows correctly
				
			},
			
			appendHTML: function() {
				
   			 	object.addClass("apps-gal-ul");
   			 	object.wrap("<div class='apps-gal-container'><div id='"+settings.divId+"' class='apps-gal-inner'></div></div>");
   			 	object.find("li").addClass("apps-gal-item");
 
   			 	if(settings.setMaxWidthAndHeight) {
	   			 	var baseWidth = $jq(".apps-gal-item img").width();
	   			 	var baseHeight = $jq(".apps-gal-item img").height();
	   			 	$jq(".apps-gal-item img").css("max-width", baseWidth);
	   			 	$jq(".apps-gal-item img").css("max-height", baseHeight);
   			 	}
 				if((settings.autoPlay == false || settings.autoPlaySpeed > 0) && settings.showNav == true)
 				{
   			 		$jq("<div class='apps-gal-nav-left'></div><div class='apps-gal-nav-right'></div>").insertAfter(object);
   			 	}
   			 	var cloneContent = object.children().clone();
   			 	object.append(cloneContent);
			},

			setEventHandlers: function() {
				
				var listParent = object.parent();
				var childSet = object.children();
				var leftArrow = listParent.find($jq(".apps-gal-nav-left"));
				var rightArrow = listParent.find($jq(".apps-gal-nav-right"));
				
				$jq(window).on("resize", function(event){
					
					methods.setResponsiveEvents();
					
					var innerWidth = $jq(listParent).width();
					var innerHeight = $jq(listParent).height(); 
					
					itemsWidth = (innerWidth)/itemsVisible;
					
					childSet.width(itemsWidth);
					if(settings.scrollForward)
    					object.css({'left' : -itemsWidth});
    				else
						object.css({'left' : -(itemsWidth*2)});
					
					if(settings.autoPlay == false || settings.autoPlaySpeed > 0)
					{

						var halfArrowHeight = (leftArrow.height())/2;
						var arrowMargin = (innerHeight/2) - halfArrowHeight;
						leftArrow.css("top", arrowMargin + "px");
						rightArrow.css("top", arrowMargin + "px");
						
						$jq(leftArrow).on({
						mouseenter: function () {
							canNavigate = false;
							$(this).animate({'opacity': '1.0'}, 1000);
							$jq(object).stop(true,false);
							
						}, 
						mouseleave: function () {
							canNavigate = true;
							$(this).animate({'opacity': '0.1'}, 1000);
						}
						});

						$jq(rightArrow).on({
						mouseenter: function () {
							canNavigate = false;
							$(this).animate({'opacity': '1.0'}, 1000);
							$jq(object).stop(true,false);
							
						}, 
						mouseleave: function () {
							canNavigate = true;
							$(this).animate({'opacity': '0.1'}, 1000);
						}
					 	});
					}
					else
					{
						console.log('autoplay | no delay');
					}
					
				});					
				
				$jq(leftArrow).on("click", function (event) {
					methods.scrollRight(true);
				});
				
				$jq(rightArrow).on("click", function (event) {
					methods.scrollLeft(true);
				});
				
				if(settings.pauseOnHover == true) {
					$jq(object).on({
						mouseenter: function () {
							canNavigate = false;
							//console.log(object[0].id);
							$jq(object).stop(true,false);
						}, 
						mouseleave: function () {
							canNavigate = true;
						}
					 });
				}

				if(settings.autoPlay == true) {
					
					setInterval(function () {
						if(canNavigate == true)
						{
							if(settings.scrollForward == true)
								methods.scrollRight(false);
							else
								methods.scrollLeft(false);
						}
					}, settings.autoPlaySpeed);
				}
			},
			
			setResponsiveEvents: function() {
				var contentWidth = $jq('html').width();
				
				if(settings.enableResponsiveBreakpoints == true) {
					
					var largestCustom = responsivePoints[responsivePoints.length-1].changePoint; // sorted array 
					
					for(var i in responsivePoints) {
						
						if(contentWidth >= largestCustom) { // set to default if width greater than largest custom responsiveBreakpoint 
							itemsVisible = settings.visibleItems;
							break;
						}
						else { // determine custom responsiveBreakpoint to use
						
							if(contentWidth < responsivePoints[i].changePoint) {
								itemsVisible = responsivePoints[i].visibleItems;
								break;
							}
							else
								continue;
						}
					}
				}
			},
			
			sortResponsiveObject: function(obj) {  //verify order of responsive breaks
				
				var responsiveObjects = [];
				
				for(var i in obj) {
					responsiveObjects.push(obj[i]);
				}
				
				responsiveObjects.sort(function(a, b) {
					return a.changePoint - b.changePoint;
				});
			
				responsivePoints = responsiveObjects;
			},				

			scrollLeft:function(force) {

				if(canNavigate == true || force == true) {
					canNavigate = false;
					console.log('scrolling left and force is '+force);
					var listParent = object.parent();
					var innerWidth = listParent.width();
					
					itemsWidth = (innerWidth)/itemsVisible;

					if(object.position().left > -itemsWidth && initialScroll == false) //if 0 element is less than one image width length under zero, reset to 2 images lenths less than zero
	    			{	
	    					object.stop(true,false);
	    					object.css({'left' : -(itemsWidth*2)});
	    			}
	    			initialScroll = false;

					var childSet = object.children();

					if(settings.randomize)
					{
						if((typeof childSet.eq(settings.visibleItems+1)[0].firstChild.href != undefined) && (typeof childSet.eq(0)[0].firstChild.href != undefined))
						{			
							if($jq.inArray(childSet.eq(0)[0].firstChild.href,visibleCollection))
							{
								var rand_app = Math.floor(Math.random() * hiddenCollection.length);
								childSet.eq(0)[0].innerHTML = '<a href="'+hiddenCollection[rand_app]+'" target="_blank" title="'+hiddenCollectionTitle[rand_app]+'"><img src="'+hiddenCollectionImage[rand_app]+'" border="0" alt="'+hiddenCollectionTitle[rand_app]+'"/></a>';
								
								visibleCollection.push(hiddenCollection[rand_app]);
								visibleCollectionImage.push(hiddenCollectionImage[rand_app]);
								visibleCollectionTitle.push(hiddenCollectionTitle[rand_app]);
								hiddenCollection.splice(rand_app,1);
								hiddenCollectionImage.splice(rand_app,1);
								hiddenCollectionTitle.splice(rand_app,1);
							}

							if($jq.inArray(childSet.eq(settings.visibleItems+1)[0].firstChild.href,visibleCollection))
							{
								var this_app = $jq.inArray(childSet.eq(settings.visibleItems+1)[0].firstChild.href,visibleCollection)
								
								if (this_app != -1)  //make sure app is in visible array before switching
								{
									hiddenCollection.push(visibleCollection[this_app]);
									hiddenCollectionImage.push(visibleCollectionImage[this_app]);
									hiddenCollectionTitle.push(visibleCollectionTitle[this_app]);
									visibleCollection.splice(this_app,1);
									visibleCollectionImage.splice(this_app,1);
									visibleCollectionTitle.splice(this_app,1);
								}
							}
						}
					}
					ease_type = settings.autoPlaySpeed > 0 ? 'swing' : 'linear';
					animationSpeed = settings.animationSpeed;
					if(object.position().left > (itemsWidth*-2)+2)  // If container farther right than reset position, change animation distance / speed to fix
					{
						animationWidth = -(object.position().left%itemsWidth);  // modified distance to travel						
						animationSpeedOffset = parseFloat(animationWidth/itemsWidth);  // modified speed offset based on distance to travel
						animationSpeed = settings.animationSpeed*animationSpeedOffset; // modified speed
					}
					else
					{
						animationWidth = itemsWidth;  //Move items based on full icon width
					}

					object.animate({
							'left' : "+=" + animationWidth
						},
						{
							queue:false, 
							duration:animationSpeed,
							easing: ease_type,
							complete: function() {  
								childSet.last().insertBefore(childSet.first()); // Get the last list item and put it before the first list item (infinite scroll)
								methods.adjustScroll();  // Toggle animation fix for next interval if stopped in between full icon widths
								canNavigate = true; // List can animate again
							}
						}
					);
				}
			},				
			
			scrollRight:function(force) {  //scrolling from right to left
				
				if(canNavigate == true || force == true) {
					canNavigate = false;	
					console.log('scrolling right and force is '+force);
					var listParent = object.parent();
					var innerWidth = listParent.width();
					
					itemsWidth = (innerWidth)/itemsVisible;
					
					if(object.position().left < -(itemsWidth*2) && initialScroll == false) //if 0 element is more than two image widths below 0, reset to 1 less than zero
	    			{	
	    					object.stop(true,false);
	    					object.css({'left' : -itemsWidth});
	    			}
	    			initialScroll = false;
					var childSet = object.children();

					if(settings.randomize)
					{
						if((typeof childSet.eq(settings.visibleItems+2)[0].firstChild.href !== undefined) && (typeof childSet.eq(0)[0].firstChild.href !== undefined))
						{	
							if($jq.inArray(childSet.eq(settings.visibleItems+2)[0].firstChild.href,visibleCollection)) //if # of visible+2 (next to scroll in) is already visible 
							{
								var rand_app = Math.floor(Math.random() * hiddenCollection.length)
								
								childSet.eq(settings.visibleItems+2)[0].innerHTML = '<a href="'+hiddenCollection[rand_app]+'" target="_blank" title="'+hiddenCollectionTitle[rand_app]+'"><img src="'+hiddenCollectionImage[rand_app]+'" border="0" alt="'+hiddenCollectionTitle[rand_app]+'"/></a>';
								visibleCollection.push(hiddenCollection[rand_app]);  //add to visible array
								visibleCollectionImage.push(hiddenCollectionImage[rand_app]);
								visibleCollectionTitle.push(hiddenCollectionTitle[rand_app]);
								hiddenCollection.splice(rand_app,1);  //remove from hidden array
								hiddenCollectionImage.splice(rand_app,1);
								hiddenCollectionTitle.splice(rand_app,1);
							}
							if($jq.inArray(childSet.eq(0)[0].firstChild.href,visibleCollection)) //if 0 element is in visible
							{
								var this_app = $jq.inArray(childSet.eq(0)[0].firstChild.href,visibleCollection);
								
								if (this_app != -1)
								{
									hiddenCollection.push(visibleCollection[this_app]);  //add to hidden array
									hiddenCollectionImage.push(visibleCollectionImage[this_app]);
									hiddenCollectionTitle.push(visibleCollectionTitle[this_app]);
									visibleCollection.splice(this_app,1);  //remove from visible array
									visibleCollectionImage.splice(this_app,1);
									visibleCollectionTitle.splice(this_app,1);
								}
							}
						}
					}

					ease_type = settings.autoPlaySpeed > 0 ? 'swing' : 'linear';
					animationSpeed = settings.animationSpeed;
					if(object.position().left < -itemsWidth) //If container farther left than reset position, change animation distance / speed to fix
					{
						animationWidth = itemsWidth+(object.position().left%itemsWidth);
						animationSpeedOffset = parseFloat(animationWidth/itemsWidth);
						animationSpeed = settings.animationSpeed*animationSpeedOffset;
					}
					else
					{
						animationWidth = itemsWidth; //Animate the full width of the item
					}
					object.animate({
							'left' : "-=" + animationWidth
						},
						{
							queue:false, 
							duration:animationSpeed,
							easing: ease_type,
							complete: function() {  
								childSet.first().insertAfter(childSet.last()); // Get the first list item and put it after the last list item (infinite scroll)   
								methods.adjustScroll();
								canNavigate = true; 
							}
						}
					);
				}
			},
			
			adjustScroll: function() {

				var listParent = object.parent();
				var childSet = object.children();				
				
				var innerWidth = listParent.width(); 
				itemsWidth = (innerWidth)/itemsVisible;
				childSet.width(itemsWidth);
				if(settings.scrollForward)
				{
					object.css({'left' : -itemsWidth});
				}
				else
				{
					object.css({'left' : -itemsWidth*2});  //if scrolling left to right, start position is a second image width to the left (for image replacement)
				}
			} 
        };
        
        if (methods[options]) { 	// $("#element").appsgal('methodName', 'arg1', 'arg2');
            return methods[options].apply(this, Array.prototype.slice.call(arguments, 1));
        } else if (typeof options === 'object' || !options) { 	// $("#element").appsgal({ option: 1, option:2 });
            return methods.init.apply(this);  
        } else {
            $jq.error( 'Method "' +  method + '" does not exist in plugin!');
        }        
};

})(jQuery);
