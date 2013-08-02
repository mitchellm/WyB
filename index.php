<?php
require_once 'global.php';
function __autoload($class) {
	require_once 'classes/' . $class . '.php';
}

$dbc = Database::getConnection();

$session = new Session($dbc);
$beef = new Beef($dbc);
$site = new Site($dbc);
?> 
<!doctype html>
<!--[if lt IE 7 ]> <html lang="en" class="no-js ie6"> <![endif]-->
<!--[if IE 7 ]>    <html lang="en" class="no-js ie7"> <![endif]-->
<!--[if IE 8 ]>    <html lang="en" class="no-js ie8"> <![endif]-->
<!--[if IE 9 ]>    <html lang="en" class="no-js ie9"> <![endif]-->
<!--[if (gt IE 9)|!(IE)]><!-->
<html lang="en" class="no-js">
<!--<![endif]-->
<head>
<meta charset="UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">

<title>Whats Your Beef?</title>
<meta name="description" content="What's your beef? Share it. Rant it. Beef it.">
<meta name="author" content="Some Faggot">

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<link rel="shortcut icon" href="/favicon.ico">
<link rel="apple-touch-icon" href="/apple-touch-icon-precomposed.png">
<link rel="stylesheet" href="css/style.css?v=2">

<script src="js/libs/modernizr-1.7.min.js"></script>
<script src="js/css3-mediaqueries.js"></script>
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.5.1/jquery.min.js"></script>
	<script>!window.jQuery && document.write(unescape('%3Cscript src="js/libs/jquery-1.5.1.min.js"%3E%3C/script%3E'))</script>
	<script src="js/plugins.js"></script>
	<script src="js/script.js"></script>
	<script src="js/handleSubmits.js"></script>
	<script src="js/autoresize.jquery.js"></script>
	<script src="js/jquery.counter.js"></script>
	<script src="js/jquery.color.js"></script>
	<script src="js/jquery.widtherize.js"></script>
	<!--[if lt IE 7 ]>
	<script src="js/libs/dd_belatedpng.js"></script>
	<script> DD_belatedPNG.fix('img, .png_bg');</script>
	<![endif]-->
	<script>
		var _gaq=[['_setAccount','UA-XXXXX-X'],['_trackPageview']]; // Change UA-XXXXX-X to be your site's ID
		(function(d,t){var g=d.createElement(t),s=d.getElementsByTagName(t)[0];g.async=1;
		g.src=('https:'==location.protocol?'//ssl':'//www')+'.google-analytics.com/ga.js';
		s.parentNode.insertBefore(g,s)}(document,'script'));
	</script>
</head>
<body>
	<header>
		<div class="inner">
			<div class="logo">
				<a href="#">What's your beef?</a>
				<div id="justTellUs">Dude, just tell us!</div>
			</div>
			<nav>
				<a href="#" class="current" id="latestBeefs">Latest Beefs</a> <a
					href="#" id="hotBeefs">Hottest Beefs</a> <a href="#"
					id="hotBeefers">Hottest Beefers</a>
			</nav>
		</div>
	</header>
	<!-- Main -->

	<div id="main" role="main">
		<!-- Top -->
		<section class="top">
			<!-- Left -->
			<div class="left">
				<div id="post">
					<form class="beef">
						<div id="formPost" class="beef">
							<textarea name="post"></textarea>
							<a href="#" class="error">Sorry, but you can't beef until you <span>Log
									In!</span> </a>
									<?
									if(!$session->isLoggedIn())
									{
										echo '<a href="#" class="error" id="login">Sorry, but you can&rsquo;t beef until you <span>Log In!</span></a>';
									}
									?>
							<a href="#" class="error" id="empty">You need to enter <span>More
									Text!</span> </a>
						</div>
						<div class="lower">
							<label for="post" id="charRemaining">200 Characters Remaining</label>

							<!-- Image Form -->
							<form>
								<a href="#" id="imageUpload" class="popupClick">
									<div id="imageStatus" class="ready">Ready</div>
									<div id="upload">Upload an Image</div> </a>

								<!-- Coming Soon Popup -->
								<section class="popup comingSoon">
									<a href="#" class="close"><div>Close</div> </a>
									<div class="content">
										<h1>Coming Soon</h1>
										<p>
											This feature is coming soon! Stay tuned. <br />- The WYB
											Team.
										</p>
									</div>
								</section>
								<!-- End Coming Soon Popup -->

							</form>
							<!-- End Image Form -->

							<input type="submit" value="Beef It." class="submit" />

						</div>
					</form>
				</div>
				<div id="beefArrow">Beef</div>
			</div>
			<!-- End Left -->
			<!-- Right -->
			<div class="right">

				<div id="preloader"></div>

				<div id="search">
					<form>
						<input type="text" class="search" value="Mitchell"/>
						<div id="select">
							<div id="1" class="current" data-option="people"><a href="#">People</a></div>
							<div id="2" data-option="beefs"><a href="#">Beefs</a></div>
						</div>
						<input type="submit" value="Search" class="submit glossButton" />
					</form>
				</div>

				<!-- Login Section -->
				<div id="loginSection">
				<?
				if($session->isLoggedIn())
				{
					$usr = $session->private_name;
					echo "<div class=\"loggedIn\">
							<!-- Logged Out -->
							<a href=\"#\" class=\"logout\" onclick=\"endSession();\">
								<div id=\"logout\" class=\"glossButton\">Log Out</div>
							</a>
							<span class=\"divider\"></span>
							<p>
								Welcome to <a href=\"#\">WYB</a>,
								 {$usr}.
							</p>
						</div>";
				}
				else
				{
					echo "<div class=\"loggedOut\">
							<!-- Logged Out -->
							<a href=\"#\" class=\"log-in\">
								<div id=\"log-in\" class=\"glossButton\">Log in</div>
							</a>

							<!-- Login Popup -->
							<section class=\"popup log-in\">
								<a href=\"#\" class=\"close\"><div>Close</div> </a>
								<div class=\"content\">
									<h1>Login</h1>
									<form method=\"post\" action=\"\" class=\"login\">
										<div id=\"processing\"></div>

										<fieldset id=\"username\">
											<label for=\"username\">Username:</label>
											<input id=\"username\" type=\"text\" name=\"username\" class=\"text\" />
											<a href=\"#\" class=\"error\">
												This username doesn't exist!
											</a>
										</fieldset>

										<fieldset id=\"password\">
											<label for=\"password\">Password:</label>
											<input id=\"password\" type=\"password\" name=\"password\" class=\"text\" />
											<a href=\"#\" class=\"error\">No Password <span>Specified!</span></a>
										</fieldset>

										<input type=\"submit\" value=\"Login!\" name=\"submit\" class=\"submit\" />
											<fb:login-button><div class=\"fbLogin\"><a href=\"#\" target=\"_blank\">Login</a></div></fb:login-button>
										<div id=\"endNote\">Please wait while we process your info!</div>
									</form>
								</div>
							</section>
							<!-- End Login Popup -->

							<span class=\"divider\"></span> 
							
							<a href=\"#\" class=\"register\">
								<p>Need an Account?</p>
								<div id=\"register\" class=\"glossButton\">Register</div>
							</a>

							<!-- Register Popup -->
							<section class=\"popup register\">
								<a href=\"#\" class=\"close\"><div>Close</div> </a>
								<div class=\"content\">
									<h1>
										Register <span>Your Account<span>
									
									</h1>

									<div class=\"fbConnect\"><a href=\"#\" target=\"_blank\">Connect with Facebook</a></div>

									<h1 class=\"divide\">OR</h1>

									<form method=\"post\" action=\"\" class=\"register\">
										<div id=\"processing\"></div>

										<fieldset id=\"username\">
											<label for=\"username\">Desired Login Name:</label> <input
												type=\"text\" name=\"username\" class=\"text\" /> <a href=\"#\"
												class=\"error\">Username already taken!</a>
										</fieldset>

										<fieldset id=\"email\">
											<label for=\"email\">Email: <span>(Just so we know you're real)</span>
											</label> <input type=\"text\" name=\"email\" class=\"text\"/ > <a
												href=\"#\" class=\"error\">Proper Email Format: <span>Hello@Yoursite.com</span>
											</a>
										</fieldset>

										<fieldset id=\"password\">
											<label for=\"password\">Password:</label> <input type=\"password\"
												name=\"password\" class=\"text\" /> <a href=\"#\" class=\"error\">Please
												enter a <span>Password!</span> </a>
										</fieldset>

										<input type=\"submit\" value=\"Make Your Account!\" name=\"submit\"
											class=\"submit\" />
										<div id=\"endNote\">Please wait while we process your info!</div>
									</form>
								</div>
							</section>
							<!-- End Register Popup -->
						</div>";
				}
				?>
				</div>

				<!-- End Login Section -->

			</div>
			<!-- End Right -->
			<div class="cutDivider"></div>
		</section>
		<!-- End Top -->

		<!-- Sidebar (right) -->
		<section class="right" id="sidebar">

			<section id="kobe">
				<a href="#"> <img src="images/kobeBeef.jpg"
					alt="Kobe Beef, the all time best beefs!" /> </a>
			</section>

			<section class="ads">
				<a href="#">
					<img src="images/ads/default.jpg" alt="#"  />
				</a>
				<a href="#">
					<img src="images/ads/default.jpg" alt="#"  />
				</a>
				<a href="#">
					<img src="images/ads/1.jpg" alt="#"  />
				</a>
				<a href="#">
					<img src="images/ads/2.jpg" alt="#"  />
				</a>
				<a href="#">
					<img src="images/ads/3.jpg" alt="#" />
				</a>
				<a href="#">
					<img src="images/ads/4.jpg" alt="#" />
				</a>
				<a href="#">
					<img src="images/ads/5.jpg" alt="#" />
				</a>
				<a href="#">
					<img src="images/ads/6.jpg" alt="#" />
				</a>
			</section>
		</section>
		<!-- End Sidebar (right) -->

		<!-- Left (Beefs) -->
		<section class="section" id="beefs">
			<!-- Top Options -->
			<div class="topOptions clearfix">
				<h1>
					Latest <span>Beefs</span>
				</h1>
				<div class="right">
					<div id="toggleImages">
						<a href="#">
							<div class="checkbox">Toggle</div> Enable Images </a>
					</div>
					<a href="#" id="refresh">
						<span>0</span>
						<div>Refresh</div>
					</a>
				</div>
			</div>
			<!-- End Top Options -->

			<!-- Posts -->
			<div id="posts">
			<?=$beef->grabAll('1', time());?>
			</div>
			<!-- End Posts -->

			<!-- Page Navigation -->
			<div id="navi" class="clearfix">
				<ul class="pages">
				<?=$site->drawNavigation('1', $beef->getPages());?>
				</ul>

				<div id="nextPrev">
				</div>
			</div>
			<!-- End Page Navigation -->

		</section>
		<!-- End Left (Beefs) -->


	</div>
	<!-- End Main -->

	<footer>
		<div class="inner">
			<div class="left" id="network">
				<a href="#">Production of the Aian Network</a>
			</div>
			<div class="center">
				<span id="sponsors"> Check out our sponsors: </span>
				<div id="sponsor">
					<a href="#"><img src="images/sponsor.jpg" alt="#" /> </a>
				</div>
			</div>
			<div class="right" id="wyb">
				<a href="#">So tell us, What's your beef? Beef it!</a>
			</div>
		</div>
		<div id="bottomBar"></div>
	</footer>
</body>
</html>