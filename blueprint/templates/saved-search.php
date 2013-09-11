<div class="saved_search_client_area_wrapper">
	<?php if (!empty($saved_searches)): ?>
		<ul class="saved_searches">
			<?php foreach ($saved_searches as $saved_search_hash => $saved_search): ?>
				<li class="saved_search_block" id="<?php echo $saved_search_hash ?>" >
					<div class="title_wrapper">
						<h4><?php echo $saved_search['search_name'] ?></h4>	
						<a class="pls_view_search" href="<?php echo $saved_search['link_to_search'] ?>" >View Search</a> 
						<a class="pls_remove_search" href="" ref="<?php echo $saved_search_hash ?>">Remove Search</a>
					</div>
					<ul class="saved_search_items">
						
						<?php foreach (json_decode($saved_search['search_value']) as $key => $value): ?>
							<?php if (!PLS_Saved_Search::search_to_skip($key)): ?>
								<li><span><?php echo PLS_Saved_Search::translate_key($key) ?>:</span> <?php echo $value ?></li>		
							<?php endif ?>
						<?php endforeach ?>
					</ul>
				</li>
			<?php endforeach ?>
		</ul>
	<?php else: ?>
		<p>No Saved Searches Yet!</p>	
	<?php endif ?>
</div>


<script type="text/javascript">

	jQuery(document).ready(function($) {

		$('.pls_remove_search').live('click', function (event) {
			event.preventDefault();
			
			// So we can keep the HTML object context for use in the success call back
			var that = this;
			var data = {};
			data.action = 'delete_client_saved_search'
			data.saved_search_option_key = $(this).attr('ref');

			// console.log(data);

			$.post(info.ajaxurl, data, function(response, textStatus, xhr) {
				// Optional stuff to do after success
				// console.log(response);
				if (response == 1) {
					$('.saved_search_block#' + data.saved_search_option_key).remove();
				} 
				else {
					// show error message
				}
			});
			
		});

		$('#pls_view_search').on('click', function (event) {
			event.preventDefault();
			
			// Act on the event
			$.post(info.ajaxurl, {param1: 'value1'}, function (data, textStatus, xhr) {
				//optional stuff to do after success
			});
		});

	});

</script>