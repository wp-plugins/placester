<div style="display:none" class="polygon_controls">
				<h3>Custom Drawn Area Controls</h3>
					<form>
						<div class="form-item">
							<label for="name">Area Name</label>
							<input type="text" name="name" id="name">	
						</div>
						<div class="form-item">
							<label for="preset_area_styles">Area Style</label>	
							<select id="preset_area_styles">
								<?php echo PL_Taxonomy_Helper::get_preset_polygon_styles() ?>
							</select>
							<a href="#" id="show_advanced_styles">Show Advanced Controls</a>
						</div>
						<div class="form-item advanced_area_controls">
							<label for="border-weight">Border Weight</label>	
							<select name="[border][weight]" id="border-weight">
								<option value="1">1</option>
								<option value="2">2</option>
								<option value="3" selected="selected">3</option>
								<option value="4">4</option>
								<option value="5">5</option>
								<option value="6">6</option>
								<option value="7">7</option>
								<option value="8">8</option>
								<option value="9">9</option>
								<option value="10">10</option>
							</select>
						</div>
						<div class="form-item advanced_area_controls">
							<label for="border-opacity">Border Opacity</label>
							<select name="[border][opacity]" id="border-opacity">
								<option value="0.2">0.2</option>
								<option value="0.3">0.3</option>
								<option value="0.4">0.4</option>
								<option value="0.5">0.5</option>
								<option value="0.6">0.6</option>
								<option value="0.7">0.7</option>
								<option value="0.8">0.8</option>
								<option value="0.9">0.9</option>
								<option value="1" selected>1.0</option>
							</select>	
						</div>
						<div class="form-item advanced_area_controls">
							<label>Border Color</label>
							<div id="polygon_border" class="another_colorpicker">
								<div style="background-color: #FF0000"></div>
							</div>
						</div>
						<div class="form-item advanced_area_controls">
							<label>Fill Opacity</label>
							<select name="[fill][opacity]" id="fill-opacity">
								<option value="0.2">0.2</option>
								<option value="0.3" selected>0.3</option>
								<option value="0.4">0.4</option>
								<option value="0.5" >0.5</option>
								<option value="0.6">0.6</option>
								<option value="0.7">0.7</option>
								<option value="0.8">0.8</option>
								<option value="0.9">0.9</option>
								<option value="1">1.0</option>
							</select>	
						</div>
						<div class="form-item advanced_area_controls">
							<label>Fill Color</label>
							<div id="polygon_fill" class="another_colorpicker">
								<div style="background-color: #FF0000"></div>
							</div>
						</div>
						<div class="form-item">
							<label for="poly_taxonomies">Area Type</label>
							<?php echo PL_Taxonomy_Helper::types_as_selects(); ?>
						</div>
						<div class="form-item">
							<label for="neighborhood">Attached to existing area?</label>
							<?php echo PL_Taxonomy_Helper::taxonomies_as_selects(); ?>
						</div>
						<div class="form-item" id="custom_name" style="display: none">
							<label for="custom_taxonomy_name">New Area Name</label>
							<input type="text" name="custom_taxonomy_name" id="custom_taxonomy_name">	
						</div>
						<input type="hidden" id="edit_id" name="id">
						<div class="form-item buttons">
							<a id="polygon_clear_drawing" class="button" href="#">Cancel</a>
							<a id="polygon_edit_drawing" class="button" href="#">Edit Drawing</a>
							<a id="polygon_save_drawing" class="button-primary" href="#">Save as Custom Area</a>	
						</div>

					</form>
				</div>