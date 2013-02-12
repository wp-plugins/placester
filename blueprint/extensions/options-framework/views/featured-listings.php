<div style="display:none">
	<div id="featured-listing-wrapper">
		<!-- filters wrapper -->
		<div class="filter-wrapper">
			<h3>Search Filters</h3>
			<p class="after-note">Use the filters below to find the listings you'd like to feature</p>
			<form></form>
			<?php PLS_Featured_Listing_Option::get_filters(); ?>
		</div>

		<!-- datatable wrapper -->
		<div class="datatable-wrapper">
			<hr />
			<!-- Search Results -->
			<div id="search-results" class="results">
				<h3>Search Results</h3>
				<p class="after-note">Available listings. Use the "Make Featured" link to featured them.</p>
				<?php PLS_Featured_Listing_Option::get_datatable( array('dom_id' => 'datatable_search_results', 'image_preview' => true, 'add_remove' => 'Add') ); ?>
			</div>

			<!-- Featured Listings -->
			<div id="featured-lisitngs" class="results">
				<h3>Featured Listings</h3>
				<p class="after-note">Featured listings. Use the "Remove" link to unfeature them.</p>
				<?php PLS_Featured_Listing_Option::get_datatable( array( 'dom_id' => 'datatable_featured_listings', 'add_remove' => 'Remove') ); ?>
			</div>

		</div>
		<div id="featured-button-group-wrapper">
			<button id="save-featured-listings">Save</button>
			<button id="cancel-featured-listings">Cancel</button>
		</div>
	</div>
</div>