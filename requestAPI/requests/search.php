			<!-- Top Options -->
			<div class="topOptions clearfix">
				<h1>
					Search <span>Results</span>
				</h1>
				<div class="right">
					<div id="toggleSearch">
						<a href="#">
							Search for <? echo ucfirst(htmlspecialchars($type)); ?>
						</a>
					</div>
				</div>
			</div>
			<!-- End Top Options -->
			<!-- Posts -->
			<div id="posts">
				<div id="searchResults" <? echo $type == "people" ? 'class="people"' : ''; ?>>
					<?=$type == "beefs" ? $beef->grabSpecific(1, $value) : $people->grabSpecific(1, $value); ?> 
				</div>
			</div>
			<!-- End Posts -->
			<!-- Page Navigation -->
			<div id="navi" class="clearfix">
				<ul class="pages">
				<?=$site->drawNavigation('1', $type == "beefs" ? $beef->getSpecificPages($value) : $people->getSpecificPages($value), $type == "beefs" ? 'beefs' : 'people');?> 
				</ul>

				<div id="nextPrev">
				</div>
			</div>
			<!-- End Page Navigation -->