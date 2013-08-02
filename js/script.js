$(function(){

	$('div.fbConnect').live('click', function () {
		window.location.href = "facebook/login_facebook.php";
	});

	$('div.fbLogin').live('click', function() {
		window.location.href = "facebook/login_facebook.php";
	});
	
	//Click Refresh
	$('.reload').click(function() {
		location.reload();
	});

	$('.content p').each(function() {
		if ($(this).css('height') == '31px') {
			$(this).closest('.post').addClass('shortBeef');

		}
	});
	
	//Enable Checkbox
	var checkboxEnabled = false;
	$('#toggleImages').click(function () {
		if (checkboxEnabled == false) {
			$('.checkbox').css({ 'background-position' : '0 -13px' });
			checkboxEnabled = true;
		}
		else if (checkboxEnabled) {
			$('.checkbox').css({ 'background-position' : '0 0' });
			checkboxEnabled = false;
		}
	});
	
	function closePopup(closeWhat, anim) {
		anim = anim === true ? true : true; //Sets default to true (animate)
		if (anim) {
			closeWhat.animate({
				'top' : '30px',
				'opacity' : 0,
			},function() {
				closeWhat.css({ 'display' : 'none' });
			});
		} else {
			closeWhat.css({ 'display' : 'none', 'top' : '30px', 'opacity' : 0 });
		}
	}


	//Alternating popups in login section
	$('#loginSection a, .popupClick').click(function() {
		$('#post .error').animate({ 'opacity' : 0 });
		var $class = $(this).attr("class");
		var popup = 'section.' + $class;
		closePopup($(this).parent().find('section:not(' + popup + ')'),false);
		$(this).parent().find(popup).css({ 'display' : 'block', 'opacity' : 0 }).animate({
			'top' : '50px',
			'opacity' : 1
		});
		return false;
	});
	
	//Popup Close
	$('.close').click(function() {
		closePopup($(this).parent());
		return false;
	});
	
	//Coming Soon Popup
	$('#imageUpload').click(function() {
		var parent = $(this).parent();
		parent.find('.popup').css({ 'z-index' : '100', 'left' : '170px' }).animate({
			'top' : '50px',
			'opacity' : 1
		})
		return false;
	});
	
	//Search Dropdown
	$('div#select div.current').live('click', function() {
		$(this).parent().children('div:not(.current)').show();
	});
	$('div#select').mouseout(function() {
		setTimeout('$("div#select div:not(.current)").hide();', 3000);
	});
	$('div#select div:not(.current)').live('click', function() {
		$('div#select div').removeClass('current');
		$(this).addClass('current');
		$(this).parent().children('div:not(.current)').hide();
	});

});
