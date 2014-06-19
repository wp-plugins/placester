<?php  
	// Build theme link data object...
	$themes = array(
		"Columbus" => array(
			"download_link" => "http://plcstr.com/16taGJs",
			"info_link" => "https://placester.com/wordpress-themes/#columbus"
		),
		"Manchester" => array(
			"download_link" => "http://plcstr.com/14S94cX",
			"info_link" => "https://placester.com/wordpress-themes/#manchester"
		),
		"Tampa" => array(
			"download_link" => "http://plcstr.com/14jjoIw",
			"info_link" => "https://placester.com/wordpress-themes/#tampa"
		),
		"Ventura" => array(
			"download_link" => "http://plcstr.com/10eIMQw",
			"info_link" => "https://placester.com/wordpress-themes/#ventura"
		)		
	);

/*** Temporary solution to theme AJAX functionality not yet working on v3 of the corporate site ***/
?>

<table id="availablethemes" cellspacing="0" cellpadding="0">
	<tbody id="the-list" class="list:themes">
		<tr>
			<?php foreach ($themes as $theme => $meta): ?>
				<td class="available-theme top left">
					<h3><?php echo $theme; ?></h3>
					<a id="theme_info_link" target="_blank" href="<?php echo $meta['info_link']; ?>" class="">Take a tour</a>
					<span>&nbsp;|&nbsp;</span>
					<a id="install_theme" target="_blank" href="<?php echo $meta['download_link']; ?>" title="">Download</a>
					<span class="note">(right-click and "Save As")</span>
				</td>
			<?php endforeach; ?>
		</tr>
	</tbody>
</table>

<br class="clear">

<div class="theme_wrapper">
	<h1>Looking for more Placester Themes?</h1>
	<h3>Take a look at our <a href="https://placester.com/themes/">Theme Portfolio</a></h3>
	<h3>Give us a call at (800) 728-8391 if you have any questions!</h3>
</div>