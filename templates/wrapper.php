<?php PLS_Route::handle_header(); ?>

<div class="inner">
	<div id="main_content"class="grid_8 alpha" role="main">
		<?php  PLS_Route::handle_dynamic(); ?>
	</div>

	<?php PLS_Route::handle_sidebar(); ?>

</div>

<?php PLS_Route::handle_footer(); ?>

