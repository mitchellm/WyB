window.log = function(){
  log.history = log.history || [];  
  log.history.push(arguments);
  arguments.callee = arguments.callee.caller;  
  if(this.console) console.log( Array.prototype.slice.call(arguments) );
};
(function(b){function c(){}for(var d="assert,count,debug,dir,dirxml,error,exception,group,groupCollapsed,groupEnd,info,log,markTimeline,profile,profileEnd,time,timeEnd,trace,warn".split(","),a;a=d.pop();)b[a]=b[a]||c})(window.console=window.console||{});

$(function() {
	$("#formPost textarea").autoResize({
	
		onResize : function() {
			$(this).css({opacity:0.92});
		},
		animateCallback : function() {
			$(this).css({opacity:1});
		},
		animateDuration : 300,
		extraSpace : 20
		
	});
	
	$("#formPost textarea").counter();
	$('.number span').widtherize({'width': 60});
});