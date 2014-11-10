<?php
	global $wpdb;

	if(isset($_POST['Save'])){
		$text = trim(stripslashes_deep($_POST['smo_text']));
		$text_color = trim(stripslashes_deep($_POST['smo_text_color']));
		$text_size = trim(stripslashes_deep($_POST['smo_text_size']));
		$bar_height = trim(stripslashes_deep($_POST['smo_bar_height']));
		$bar_color = trim(stripslashes_deep($_POST['smo_bar_color']));
		$errors = [];

		// Check Text
		if( $text == '' ) $errors[] = 'Text is required.';

		// Check Text Color
		if( $text_color == '' ) $errors[] = 'Text Color is required.';
		else if( !check_color($text_color) ) $errors[] = 'Text Color is invalid.';

		// Check Text Size
		if( !is_numeric($text_size) ) $errors[] = 'Text Size is invalid.';

		// Check Bar Height
		if( !is_numeric($bar_height) ) $errors[] = 'Bar Height is invalid.';

		// Check Bar Color
		if( $bar_color == '' ) $errors[] = 'Bar Color is required.';
		else if( !check_color($bar_color) ) $errors[] = 'Bar Color is invalid.';

		// Save
		if( count($errors) == 0 ) {
			$wpdb->update(
				'nova_social_media_options',
				array(
					'text' => $text,
					'color' => $text_color,
					'size' => $text_size,
					'bar_height' => $bar_height,
					'bar_color' => $bar_color
				),
				array( 'id' =>  $_POST['id'] )
			);
		}
	}

	function check_color( $value ) {
		if ( preg_match( '/^#[a-f0-9]{6}$/i', $value ) ) { // if user insert a HEX color with #
			return true;
		}
		return false;
	}

	$options = $wpdb->get_row("SELECT * FROM nova_social_media_options");
?>
<form method="POST" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
	<input type="hidden" name="id" value="<?php echo $options->id ?>">
	<div class="wrap">
		<div class="nova nova-container">
			<?php
				if(isset($_POST['Save'])){
					if(count($errors) == 0) {
						echo '<div id="message" class="updated below-h2"><p>Social Media Options Updated.</p></div>';
					}
					else {
						echo '<div id="message" class="error below-h2">';
						foreach( $errors as $v ) echo '<p>' . $v . '</p>';
						echo '</div>';
					}
				}
			?>
			<div class="nova-row">
				<h2><strong>Social Media Options</strong></h2>
				<div class="nova-label-column">
					<ul>
						<li>Text</li>
						<li>Text Color</li>
						<li>Text Size</li>
						<li>Bar Height</li>
						<li>Bar Color</li>
					</ul>
				</div>
				<div class="nova-control-column">
					<ul class="nova-input-list">
						<li><input type="text" class="nova nova-field nova-medium-field" name="smo_text" value="<?php echo isset($_POST['Save']) && count($errors) > 0 ? $text : $options->text ?>" autocomplete="off" placeholder="Enter Text here"></li>
						<li><input type="text" class="my-color-field" name="smo_text_color" value="<?php echo isset($_POST['Save']) && count($errors) > 0 ? $text_color : $options->color ?>"></li>
						<li><input type="text" class="nova nova-field nova-medium-field" name="smo_text_size" value="<?php echo isset($_POST['Save']) && count($errors) > 0 ? $text_size : $options->size ?>" autocomplete="off" placeholder="Enter Text Size here"></li>
						<li><input type="text" class="nova nova-field nova-medium-field" name="smo_bar_height" value="<?php echo isset($_POST['Save']) && count($errors) > 0 ? $bar_height : $options->bar_height ?>" autocomplete="off" placeholder="Enter Bar Height here"></li>
						<li><input type="text" class="my-color-field" name="smo_bar_color" value="<?php echo isset($_POST['Save']) && count($errors) > 0 ? $bar_color : $options->bar_color ?>"></li>
					</ul>
				</div>
			</div>
			<button class="nova-button nova-button-primary" type='submit' name='Save'>
				<img class="nova-button-icon" src="<?php echo plugins_url('epik_social_media/images/save.png'); ?>">
				Save Options
			</button>
		</div>
	</div>
</form>
<script>
    jQuery(document).ready(function($){
        $('.my-color-field').wpColorPicker({
        	defaultColor: '#ffffff',
        	clear: function() {
        		$(this).val('#ffffff');
        	}
        });
    });
</script>