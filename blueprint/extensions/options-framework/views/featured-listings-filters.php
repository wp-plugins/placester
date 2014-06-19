<form id="options-filters" method="POST" >
	<div class="featured_listings_options">
		<div class="address big-option">
			<label>Street Address</label>
			<input type="text" name="location[address]">
		</div>
		<div class="featured-listing-form-city option">
			<label for="featured-listing-city-filter">City</label>
			<select id="featured-listing-city-filter" name="location[locality]">
				<?php $cities = PLS_Plugin_API::get_location_list('locality');
					if (!empty($cities)) {
						foreach ($cities as $key => $v) {
							echo '<option value="' . $key . '">' . $v . '</option>';
						}
					}
				?>
			</select>
		</div>

		<div class="featured-listing-form-zip option">
			<label for="featured-listing-zip-filter">Zip Code</label>
			<select id="featured-listing-zip-filter" name="location[postal]">
				<?php $zip = PLS_Plugin_API::get_location_list('postal');
					if (!empty($zip)) {
						foreach ($zip as $key => $v) {
							echo '<option value="' . $key . '">' . $v . '</option>';
						}
					}
				?>
			</select>
		</div>

		<div class="featured-listing-form-beds option">
			<label for="featured-listing-beds-filter">Beds</label>
			<input id="featured-listing-beds-filter" type="text" name="metadata[beds]">
		</div>

		<div class="featured-listing-form-beds option">
			<label for="featured-listing-rent-filter">Rent/Sale</label>
			<select id="featured-listing-rent-filter" name="purchase_types[]">
				<?php
					echo '<option value="false">Any</option>';
					echo '<option value="rental">Rent</option>';
					echo '<option value="sale">Buy</option>';
				?>
			</select>
		</div>

		<div class="featured-listing-form-min-price option">
			<label for="featured-listing-min-price-filter">Min Price</label>
			<input id="featured-listing-min-price-filter" type="text" name="metadata[min_price]">
		</div>

		<div class="featured-listing-form-max-price option">
			<label for="featured-listing-max-price-filter">Max Price</label>
			<input id="featured-listing-max-price-filter" type="text" name="metadata[max_price]">
		</div>

		<div class="featured-listing-form-max-price option checkboxes">
			<label for="featured-listing-non-mls-filter">Non-MLS Listings</label>
			<input id="featured-listing-non-mls-filter" type="checkbox" name="non_import">
		</div>

		<div class="featured-listing-form-max-price option checkboxes">
			<label for="featured-listing-my-offices-filter">My Offices's Listings</label>
			<input id="featured-listing-my-offices-filter" type="checkbox" name="agency_only">
		</div>

	</div>
	<input class="button" type="submit" value="Search">
</form>
