/*

	Originally coded by Adam Mathes of http://forrst.com
	Link: http://forr.st/~1L3
	
	Modified to fit needs by Sawyer Altman or What's Your Beef

*/

function resizer() {
    this.resize = function(element, size) {
        this.init(element);
		var finalSize = this.growTo(size);
		if (finalSize == 59) { //1
			element.addClass('resized').addClass('one');
		} else if (finalSize == 71) { //10
			element.addClass('resized').addClass('ten');
		} else if (finalSize == 40) { //100
			element.addClass('resized').addClass('hundred');
		} else if (finalSize == 25) { //1,000
			element.addClass('resized').addClass('thousand');
		} else if (finalSize == 21) { //10,000
			element.addClass('resized').addClass('ten-thousand');
		} else if (finalSize == 17) { //100,000
			element.addClass('resized').addClass('hundred-thousand');
		} else if (finalSize == 15) { //1,000,000
			element.addClass('resized').addClass('million');
		}
		
		if (finalSize > 48) {
			finalSize = 48;
		}
        element.css('font-size', finalSize + 'px');
        this.tester.remove();
    }

    this.init = function(element) {
        $('#resizeroo').remove();
        this.tester = element.clone();
        this.tester.css('display', 'none');
        this.tester.css('height', 'auto');
        this.tester.css('width', 'auto');
        $('body').append(this.tester);
        this.size = 1;
        this.tester.css('font-size', this.size + 'px');
    }

    this.emitWidth = function() {
        console.log(this.tester.width());
    }

    this.grow = function() {
        this.size++;
        this.setSize();
    }

    this.setSize = function(size) {
        this.size = size;
        this.tester.css('font-size', this.size + 'px');
    }

    this.growTo = function(limit) {
        lower = 1;
        upper = limit-1;

        // do binary search going midway to determine 
        // the best size
        while( lower < upper ) {
            midpoint = Math.ceil((upper+lower)/2);
            this.setSize(midpoint);
            
            if( Math.abs(limit - this.tester.width()) <= 1) {
                // close enough
                break
            }

            if(this.tester.width() >= limit) {
                upper = this.size-1;
            }
            else {
                lower = this.size+1;
            }       
        }

        while(this.tester.width() > limit) {
            this.setSize(this.size-1);
        }

        return(this.size);

    }
}


(function( $ ){
  $.fn.widtherize = function( options ) {
      return this.each(function() {       
          var settings = {
              'width' : 79
          };      
          if ( options ) { 
              $.extend( settings, options );
          }
          r = new resizer();
          r.resize($(this), settings.width);
      });
  };
})( jQuery );