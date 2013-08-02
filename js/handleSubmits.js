
	$('form.login .submit').live('click', validateLogin);
	$('form.register .submit').live('click', validateRegister);
	$('#post .submit').live('click', submitBeef);
	$('div#navi a').live('click', handleNavi);
	$('.reheat a').live('click', reheat);
	$('a#refresh').live('click', clickRefresh);
	$('#search input.submit').live('click', search);
	$('div.fbShare').live('click', sharebeef);

	function validateLogin(){
		
		var $this = $(this);
		$this.parent().addClass('processing');
		
		var $processing = $('#processing');
		$processing.show();
		
		var $endNote = $this.parent().find('#endNote')
		$endNote.slideDown();

		//Variables
		var userExists = true;
		var invalid = false;
		
		function returnErrors() {
			$processing.hide();
			$this.parent().removeClass('processing');
			$endNote.slideUp();
			hasError = true;
		}
		
		//Functions
		user();
		
		function user() {
			var $userError = $('form.login #username .error');
			var $passError = $('form.login #password .error');
			var user = $('#username input').val();
			var pass = $('#password input').val();
			
			//Reset error messages
			$userError
				.stop().animate({ 'left' : '-130px', 'opacity' : '0' });
			$passError
				.stop().animate({ 'left' : '-130px', 'opacity' : '0' });
			if (user.length > 0 && pass.length > 0) {
				$.ajax({
					type: 'POST',
					url: 'requestAPI/index.php',
					data: "user="+ user + "&pass=" + pass +"&request=checkLogin",
					async: false,
					statusCode: {
						404: function() {
							alert('page not found');
						}
					},
					error: function(){
						alert('There was an error in your submisson, please try again later.');
					},
					
					success: function(data) {
						if (!data == 1) {
							$passError
								.html('<div>Invalid Username or <span>Password!</span></div>')
								.stop().animate({ 'left' : '-150px', 'opacity' : '1' });
								returnErrors();
						} else if (data == 'true' || data == 1) {
							$.ajax({
								type: 'POST',
								url: 'requestAPI/index.php',
								data: "request=checkLogin",
								async: false,
								statusCode: {
									404: function() {
										alert('page not found');
									}
								},
								error: function(){
									alert('There was an error in your submisson, please try again later.');
								},
								
								success: function() {
								
									//Modify username to capitalize first letter
									var userModified = user.replace(/\b[a-z]/g, function () {
										return arguments[0].toUpperCase();
									});
									
									//Return Success to User
									$this.parent().parent()
									.animate({ 'opacity' : 0 }, function() {
										$(this).html('<h1><span>Welcome!<span></h1>Welcome to WYB, ' + userModified + '! <a href="index.php" class="reload">Click here to finish logging in!</a>')
									})
									.animate({ 'opacity' : 1 });
									
									//Start Session
									$.ajax({
										type: 'POST',
										url: 'requestAPI/index.php',
										data: "request=login&user=" + user +"&pass="+pass,
										async: false,
										statusCode: {
											404: function() {
												alert('page not found');
											}
										},
										
										success: function() {
										
											//nothing to be done --
										
										},
										
										error: function(){
											$endNote.slideUp().html('<div class="endNote">Unable to start login session. Please try again later.</div>').slideDown();
										}
									});
									
								},
								
								error: function(){
									$endNote.slideUp().html('<div class="endNote">Unable to start login session. Please try again later.</div>').slideDown();
								}
							});
						}// End if Data = True (account login was successful)
					
					},
					error: function(){
						$endNote.slideUp().html('<div class="endNote">An error occured upon submitting your form! Please try again later.</div>').slideDown();
					}
				});
			}
			//Check if user or pass is a blank field
			if (user.length == 0) {
				returnErrors();
				$userError
					.html('<div>Username Not <span>Specified!</span></div>')
					.stop().animate({ 'left' : '-150px', 'opacity' : '1' });
					returnErrors();
			} if (pass.length == 0) {
				returnErrors();
				$passError
					.html('<div>Password Not <span>Specified!</span></div>')
					.stop().animate({ 'left' : '-150px', 'opacity' : '1' });
					returnErrors();
			}
		}
		
		return false;
		
	}//End validateLogin()
	
	function validateRegister(){
		
		var $this = $(this);
		var $form = $this.parent();
		$this.parent().addClass('processing');
		
		var $processing = $('#processing');
		$processing.show();
		
		var $endNote = $this.parent().find('#endNote')
		$endNote.slideDown();

		//Variables
		var emailExists = false;
		var userExists = false;
		var hasError = false;

		//Executes functions
		email();
		pass();
		user();
				
		function returnErrors() {
			$processing.hide();
			$this.parent().removeClass('processing');
			$endNote.slideUp();
			hasError = true;
		}
		
		function user() {
			var $error = $form.find('#username .error');
			var input = $form.find('#username input').val();
			if (input.length > 0) {
				$error
					.stop().animate({ 'left' : '-130px', 'opacity' : '0' });
				$.ajax({
					type: 'POST',
					url: 'requestAPI/index.php',
					data: "input="+input + "&request=checkAvail",
					async: false,
					statusCode: {
						404: function() {
							alert('page not found');
						}
					},
					
					success: function(data) {
						if (data == 'true') {
							$error
								.html('<div>A user is already using <span>this username!</span></div>')
								.stop().animate({ 'left' : '-150px', 'opacity' : '1' });
								returnErrors();
						}
					},
					error: function(){
						$endNote.slideUp().html('An error occured upon submitting your form! Please try again later.').slideDown();
					}
				});
			} else {
				returnErrors();
				$error
					.html('<div>Please enter a <span>Username!</span></div>')
					.stop().animate({ 'left' : '-150px', 'opacity' : '1' });
					returnErrors();
			}
		}// End user() function.


		function email () {
			var $error = $form.find('#email .error');
			var input = $form.find('#email input').val();
			var emailRE = /^.*@.+\..{2,5}$/;
			if (input.match(emailRE)){
				$error
					.stop().animate({ 'left' : '-130px', 'opacity' : '0' });
			
				//Checks for Existing Email
				function checkExisting_email() {
					$.ajax({
						type: 'POST',
						url: 'requestAPI/index.php',
                        data: "input="+input + "&request=checkAvail",
						async: false,
						statusCode: {
							404: function() {
								alert('page not found');
							}
						},
						
						success: function(data) {
							if (data == 'true') {
								$error
									.html('<div>A user is already using <span>this email!</span></div>')
									.stop().animate({ 'left' : '-150px', 'opacity' : '1' });
									returnErrors();
							}
						},
						error: function(){
							$endNote.slideUp().html('An error occured upon submitting your form! Please try again later.').slideDown();
						}
					});
				}// End CheckExisting_email() function.
				checkExisting_email();
			
			} else {
				//Email doesn't match
				returnErrors();
				$error
					.html('<div>Proper Email Format: <span>Hello@Yoursite.com</span></div>')
					.stop().animate({ 'left' : '-150px', 'opacity' : '1' });
			}
		}// End email() function.
		
		function pass() {
			var $error = $form.find('#password .error');
			var input = $form.find('#password input').val();
			if (input.length > 0) {
				$error
					.stop().animate({ 'left' : '-130px', 'opacity' : '0' });
			} else {
				$error
					.stop().animate({ 'left' : '-150px', 'opacity' : '1' });
					returnErrors();
			}
		}// End pass() function.
	
		if (hasError == false) {
			success();
		}
		return false;
	}
	
	function success() {
		var $popupContent = $('form.register').parent();
		
		//Get Variables from Form
		var username = $('form.register #username input').val();
		var email = $('form.register #email input').val();
		var pass = $('form.register #password input').val();
	
		$.ajax({
			type: 'POST',
			url: 'requestAPI/index.php',
			data: "user="+ username + "&email=" + email + "&pass=" + pass+ "&request=register",
			datatype: 'text',
			async: false,
			statusCode: {
				404: function() {
					alert('page not found');
				}
			},
			
                success: function(data) {
                //Return Success to User
				$('form.register').parent()
				.animate({ 'opacity' : 0 }, function() {
					$(this).html('<h1><span>Success!<span></h1>Your account has been created! <a href="index.php" class="reload">Reload the page to login!</a>')
				})
				.animate({ 'opacity' : 1 });
			},

			error: function(){
				alert("There's an error in your Ajax Function");;
			}
		});
		
		return false;
	}// End validateRegister

	function submitBeef() {
		var $this = $(this);
		var $form = $('div#formPost textarea');
		var preloader = $('#preloader');
		var content = $form.val();

		if ($form.val().length == 0) {
			$form.addClass('animateColor').animate({ 'background-color' : '#ff9898' }, 300); //flash red
			setTimeout("$('div#formPost textarea').animate({ 'background-color' : '#e1e1e1' })", 300); //flash back
			setTimeout("$('div#formPost textarea').removeClass('animateColor')", 600); //add back gradient
		} else {
			preloader.show();
			var $postErrorLogin = $('#formPost a#login.error');
			if ($postErrorLogin.length) {  // Checking if php generated the login error
				$postErrorLogin
					.stop().animate({ 'left' : '520px', 'opacity' : '1' });
				preloader.hide();
			} else {
				//Submit ajax call
				$.ajax({
					type: 'POST',
					url: 'requestAPI/index.php',
					data: "request=postBeef&content=" + content,
					datatype: 'text',
					async: false,
					success: function(data) {
						preloader.hide();
						//Success function - Post to latest - data is returned in full html
						$('#posts .post:last-child').animate({ 'opacity' : 0, 'top' : '30px' }, function() {
							$('#posts').prepend(data)
							$('#posts .post:first-child').css({ 'opacity' : 0, 'top' : '-30px' }).animate({ 'opacity' : 1, 'top' : 0 });
							$('#posts .post:last').remove();
							$('.number span').widtherize({'width': 60});
						});
					},
					error: function(data) {
						alert("Error: " + data);
					}
				});
			}
		}
		
		
		return false;
	}
	
	function newNavi(currentPage, type, term) {
		$('.number span').widtherize({'width': 60});
		$('html').animate({scrollTop:300}, 1200);
		$.ajax({
			type: 'GET',
			url: 'requestAPI/index.php',
			data: "request=navigationRequest&pageRequest=" + currentPage + "&type=" + type + "&term=" + term,
			datatype: 'text',
			async: false,
			success: function(data) {
				$('div#navi ul.pages').html(data);
			}
		});
	}
	
	function handleNavi() {
	
		var currentPage = $('div#navi li.current a').attr('id');
		var type = $('div#navi li a').attr('type');
		var term = $('div#searchResults info').attr('term');
		var pages = $('div#navi ul info').attr('pages');
		if ($(this).attr('class') == 'naviAnchor') {
			currentPage = $(this).attr('id');
			$.ajax({
				type: 'POST',
				url: 'requestAPI/index.php',
				data: "request=pagination&pageRequest=" + currentPage + "&term=" + term + "&type=" + type,
				datatype: 'text',
				async: false,
				success: function(data) {
					if(type == 'beefs' || type == 'people') {
						$('#searchResults').fadeOut(function() {
							$('#searchResults').html(data).fadeIn();
						});
					} else {
						$('#posts').fadeOut().html(data).fadeIn();
					}
					newNavi(currentPage, type, term);
				}
			});
		} else {
			if ($(this).attr('class') == 'prev') {
				currentPage--;
				if (currentPage > 0) {
					$.ajax({
						type: 'POST',
						url: 'requestAPI/index.php',
						data: "request=pagination&pageRequest=" + currentPage + "&term=" + term,
						datatype: 'text',
						async: false,
						success: function(data) {
							$('#posts').fadeOut().html(data).fadeIn();
							newNavi(currentPage, type, term);
						}
					});
				} else {
					$(this).animate({ 'color' : 'red' }, function() {
						$(this).animate({ 'color' : 'brown' });
					});
				}
			} else if ($(this).attr('class') == 'next') {
				currentPage++;
				$.ajax({
					type: 'POST',
					url: 'requestAPI/index.php',
					data: "request=pagination&pageRequest=" + currentPage + "&term=" + term,
					datatype: 'text',
					async: false,
					success: function(data) {
						$('#posts').fadeOut().html(data).fadeIn();
						newNavi(currentPage, type, term);
					}
				});
			}
		}
		return false;
	}
	
	function reheat() {
		var $this = $(this);
		var id = $(this).closest('.post').attr('id');
		var heats = $(this).closest('.heat').attr('id');
		$.ajax({
			type: 'POST',
			url: 'requestAPI/index.php',
			data: 'request=reheat&id=' + id + '&heats=' + heats,
			datatype: 'text',
			async: false,
			success: function(data) {
				if (data != heats) {
					$this.closest('.heat').find('span').fadeOut(200, function() {
						$this.closest('.heat').find('span').text(data).fadeIn();
						$('.number span').widtherize({'width': 60});
					});
				} else if (data == heats) {
					$('.number span').animate({ 'color' : '#ffadad' }, function() {
						$(this).animate({ 'color' : '#fff' });
					});
				}
			}
		});
		
		return false;
	}

	function sharebeef() {
		var id = $(this).attr('id');
		var data = $('div#posts div#' + id + '.post div.content p').text();
		$.ajax({
			type: 'POST', 
			url: 'requestAPI/index.php',
			data: 'request=shareBeef&content=' + data + '&id=' +id,
			async: false,
			datatype: 'text',
			success: function(d) {
				if(d == "1") {
				} else if(d == "0") {
					//failed
					$.ajax({
						type: 'POST', 
						url: 'requestAPI/index.php',
						data: 'request=facebookPermissions',
						async: false,
						datatype: 'text',
						success: function(d) {
							if(d != "0") {
								window.location.href = d;
							}
						}
					});
				}
			}
		});
	}

	function updateRefresh() {
		if ($('a#refresh').length > 0) {
			$.ajax({
				type: 'POST',
				url: 'requestAPI/index.php',
				data: 'request=refresh',
				datatype: 'text',
				async: false,
				success: function(data) {
					$('a#refresh span').text(data);
				}
			});
		}
	}
	setInterval( "updateRefresh()", 10000 );
	
	function clickRefresh() {
		$.ajax({
			type: 'POST',
			url: 'requestAPI/index.php',
			data: "request=pagination&pageRequest=1&update",
			datatype: 'text',
			async: false,
			success: function(data) {
				$('a#refresh span').text('0');
				$('#posts').fadeOut().html(data).fadeIn();
				newNavi(1);
			}
		});
	}
	
	function endSession() {
		$.ajax({
			type: 'POST',
			url: 'requestAPI/index.php',
			data: 'request=logout',
			datatype: 'text',
			async: false,
			success: function(data) {
				location.reload();
			}
		});
	}
	
	function search() {
		var searchVal = $(this).siblings().val();
		var searchType = $('div#search div#select div.current').attr('data-option');
		$.ajax({
			type: 'POST',
			url: 'requestAPI/index.php',
			data: 'type=' + searchType + '&request=search&value=' + searchVal,
			datatype: 'text',
			async: false,
			success: function(data) {
				$('#beefs').fadeOut(function() {
					$('#beefs').html(data).fadeIn();
				});
			}
		});
		
		return false;
	}