<?php
/**
 * Simply used to build and construct the html markup without putting it in the raw php classes like it was previously done. 
 * 
 * @author Mitchell Murphy
 * @package What's Your Beef
 * @version 1.0.9
 */

require_once 'Utility.php';
class HTMLHelper {

	public function __construct() {}

	public static function markupBeef($id, $author, $content, $heats, $timestamp, $avatar) {
		$tago = Utility::timeAgo($timestamp, time());
		if($heats < 100)
				$color = "brown";
				else if($heats < 1000)
				$color = "orange";
				else if($heats < 10000)
				$color = "red";
				else if($heats < 100000)
				$color = "silver";
			
				$content = htmlspecialchars($content);
				return "<!-- Post -->
								<div class=\"post\" id=\"{$id}\">
									<div class=\"heat {$color}\" id=\"{$heats}\">
										<div class=\"flame\">Flame</div>
											<div class=\"number\">
												<span>".number_format($heats)."</span>
											</div>
											<div class=\"reheat\">
												<a href=\"#\">Reheat Beef</a>
											</div>
										</div>
										<div class=\"content\" id=\"".$id."\">
											<p>\"{$content}\"
											<a href=\"#\">What's your beef?</a></p>
												<div class=\"posted\">
													<img src=\"".$avatar."\" alt=\"".ucfirst($author)."\" />
													Posted <span>{$tago}</span> ago by <a href=\"#\">".ucfirst($author).".</a>
												</div>
												<div class=\"fbShare\" id=\"{$id}\">
													<a href=\"#\">Facebook Share</a>
												</div>
												<div class=\"viewComments\">
													<a href=\"#\">View Comments</a> <span class=\"commentNumber\"> 0 </span>
												</div>
											</div>
										</div>
							<!-- End Post -->";
	}

	public static function markupBeefResult($author, $tago, $content, $heats, $avatar) {
		return "<div class=\"result\">
						<div class=\"postBy\">
							<img src='".$avatar."' alt='{$author}' width=\"37px\" height=\"35px\" />
							<span>Posted {$tago} by <a href=\"#\">~{$author}.</a></span>
						</div>
						<div class=\"content\">
							<div class=\"left heat\">
								{$heats}
							</div>
							<div class=\"postContent\">
								\"{$content}\"
								<a href=\"#\" rel=\"wyb\">What’s your beef?</a>
							</div>
						</div>
					</div>";
	}

	public static function markupPeopleResult($author, $beefs, $reheats, $location, $lastlogin, $lastbeef, $avatar) {
		return "<div class=\"result\">
						<div class=\"left profpic\">
							<img src='".$avatar."' alt='{$author}' width=\"140px\" height=\"106px\" />
						</div>
						<div class=\"right clearfix\">
							<div class=\"head\">
								<span class=\"name\">{$author}</span>
								<span class=\"\location\">From <strong>{$location}</strong></span>
							</div>
							<ul class=\"left last\">
								<li class=\"login\"><strong>Last Login:</strong> {$lastlogin}</li>
								<li class=\"beef\"><strong>Last Beef:</strong> {$lastbeef}</li>
							</ul>
							<div class=\"right stats\">
								<div class=\"beefs\">
									<span class=\"title\">Beefs</span>
									<span class=\"value\">{$beefs}</span>
								</div>
								<div class=\"reheats\">
									<span class=\"title\">Reheats</span>
									<span class=\"value\">{$reheats}</span>
								</div>
							</div>
						</div>
						<a href=\"#\" class=\"visit-profile\">Go to ~{$author}'s Profile</a>
					</div>";
	}

	public static function naviLink($id = null, $liClass = null, $page, $type = "latest") {
		if($type == null)
			$type = "latest";
			
	    $id = isset($id) ? " id=\"{$id}\""  : '';
	    $liClass = isset($liClass) ? " class=\"{$liClass}\"" : '';
	    return "<li{$liClass}><a href=\"#\" type=\"{$type}\" class=\"naviAnchor\" {$id}><span>{$page}</span></a></li>";
	}
}
?>