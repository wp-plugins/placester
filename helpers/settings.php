<?php 

class PL_Settings_Helper {

	function display_global_filters () {
		$filters = PL_Helper_User::get_global_filters();
		// pls_dump($filters);
		$html = '';
		if (!empty($filters)) {
			foreach ($filters as $key => $filter) {
				if (is_array($filter)) {
					foreach ($filter as $subkey => $item) {
						if (!is_array($item)) {
							if ($item == 'in') { continue; }
							$label = is_int($subkey) ? $key : $key . '-' . $subkey;
							$value = $item;
							$name = $key . '['.$subkey.']=';
							ob_start();
							?>
								<span id="active_filter_item">
									<a href="#"  id="remove_filter"></a>
									<span class="global_dark_label"><?php echo $label ?></span> : <?php echo $value ?>
									<input test="true" type="hidden" name="<?php echo $name ?>" value="<?php echo $value ?>">	
								</span>
							<?php
							$html .= ob_get_clean();
						} else {
							foreach ($item as $k => $value) {
								if ($value == 'in') { continue; }
								$label = is_int($subkey) ? $key : $key . '-' . $subkey;
								$value = $value;
								$name = $key . '['.$subkey.'][]=';
								ob_start();
								?>
									<span id="active_filter_item">
										<a href="#"  id="remove_filter"></a>
										<span class="global_dark_label"><?php echo $label ?></span> : <?php echo $value ?>
										<input type="hidden" name="<?php echo $name ?>" value="<?php echo $value ?>">	
									</span>
								<?php
								$html .= ob_get_clean();
							}
						}
					}
				}
			}
		}
		echo $html;
	}

//end of class
}