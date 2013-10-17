<?php $saved_searches = PLS_Plugin_API::get_user_saved_searches(); ?>
<?php // error_log(var_export($saved_searches, true)); ?>
<div class="saved_search_client_area_wrapper">
	<?php if (!empty($saved_searches)): ?>
		<ul class="saved_searches">
			<?php foreach ($saved_searches as $saved_search_hash => $saved_search): ?>
				<li class="saved_search_block" id="<?php echo $saved_search_hash; ?>" >
					<div class="title_wrapper">
						<h4><?php echo $saved_search['name']; ?></h4>	
						<a class="pls_view_search" href="<?php echo $saved_search['url']; ?>" >View Search</a> 
						<a class="pls_remove_search" href="<?php echo $saved_search_hash; ?>">Remove Search</a>
					</div>
					<ul class="saved_search_items">
						<?php foreach ($saved_search['filters'] as $key => $value): ?>
							<li><span><?php echo PLS_Plugin_API::translate_key($key) ?>:</span> <?php echo $value ?></li>
						<?php endforeach; ?>
					</ul>
				</li>
			<?php endforeach; ?>
		</ul>
	<?php else: ?>
		<p>No Saved Searches Yet!</p>	
	<?php endif; ?>
</div>