<?php 
	global $wpdb;
	$upload_dir = wp_upload_dir();

	if(isset($_POST['Save'])){
		if ( ! function_exists( 'wp_handle_upload' ) ) require_once( ABSPATH . 'wp-admin/includes/file.php' );

		$name = trim(stripslashes_deep($_POST['sm_name']));
		$link_address = trim(stripslashes_deep($_POST['sm_link']));
		$visibility = $_POST['sm_visibity'];
		$icon_image = '';
		$errors = [];

		// Check Name
		if( $name == '' ) $errors[] = 'Name is required.';

		// Check Link Address
		if( $link_address == '' ) $errors[] = 'Link Address is required.';
		else if( !check_url($link_address) ) $errors[] = 'Link Address is invalid.';

		// Check if file is image
		if( $_FILES['sm_file']['name'] != '' ) {
			if( in_array($_FILES['sm_file']['type'], array('image/png', 'image/jpeg')) ) {
				// Set New File Name
				$new_file_name = date('YmdHis') . '_' . str_replace(' ', '_', $_FILES['sm_file']['name']);
				$new_file_name = str_replace('(', '', $new_file_name);
				$new_file_name = str_replace(')', '', $new_file_name);
				$_FILES['sm_file']['name'] = $new_file_name;
				$icon_image = $upload_dir['subdir'] . '/' . $new_file_name;
			}
			else {
				$errors[] = 'Icon Image is not a valid image (.png, .jpg only).';
			}
		}
		else {
			$errors[] = 'Icon Image is required.';
		}

		// Save
		if( count($errors) == 0 ) {
			// Upload File
			$uploadedfile = $_FILES['sm_file'];
			$upload_overrides = array( 'test_form' => false );
			$movefile = wp_handle_upload( $uploadedfile, $upload_overrides );
			
			$wpdb->insert(
				'nova_social_media',
				array(
					'name' => trim($name),
					'link_address' => $link_address,
					'visibility' => $visibility,
					'icon_image' => $icon_image,
					'created' => current_time('mysql')
				)
			);
		}
	}

	function check_url($uri){
		if(preg_match( '/^(http|https):\/\/[a-z0-9_]+([\-\.]{1}[a-z_0-9]+)*\.[_a-z]{2,5}'.'((:[0-9]{1,5})?\/.*)?$/i' ,$uri)){
			return $uri;
		}
		return false;
	}
?>
<form method="POST" action="<?php echo $_SERVER['REQUEST_URI']; ?>" enctype="multipart/form-data">
	<div class="wrap">
		<div class="nova nova-container">
			<?php
				if(isset($_POST['Save'])){
					if(count($errors) == 0) {
						echo '<div id="message" class="updated below-h2"><p>Social Media Added. <a href="admin.php?page=epik_social_media/social_media.php">View Social Medias</a></p></div>';
					}
					else {
						echo '<div id="message" class="error below-h2">';
						foreach( $errors as $v ) echo '<p>' . $v . '</p>';
						echo '</div>';
					}
				}
			?>
			<div class="nova-row">
				<h2><strong>Add New Social Media</strong></h2>
				<div class="nova-label-column">
					<ul>
						<li>Name</li>
						<li>Link Address</li>
						<li>Icon Image</li>
						<li>Visibility</li>
					</ul>
				</div>
				<div class="nova-control-column">
					<ul class="nova-input-list">
						<li><input type="text" class="nova nova-field nova-medium-field" name="sm_name" value="<?php echo isset($_POST['sm_name']) && count($errors) > 0 ? $name : '' ?>" size="50" autocomplete="off" placeholder="Enter Name here"></li>
						<li><input type="text" class="nova nova-field nova-medium-field" name="sm_link" value="<?php echo isset($_POST['sm_link']) && count($errors) > 0 ? $link_address : '' ?>" autocomplete="off" placeholder="Enter Link Address here"></li>
						<li>
							<div class="nova-upload" style="height: 37px;">
								<input name="sm_file" type="file">
								<div class="input-group nova-medium-field">
									<span class="input-group-addon"><img class="nova-button-icon" src="<?php echo plugins_url('epik_social_media/images/image.png'); ?>"></span>
									<input class="nova-field" disabled="disabled">
									<span class="input-group-addon nova-upload-trigger">Browse for Image</span>
								</div>
							</div>
						</li>
						<li>
							<input type="radio" name="sm_visibity" value="Y" id="sm_visible_y" <?php echo isset($_POST['sm_visibity']) && count($errors) > 0 ? ($_POST['sm_visibity'] == 'Y' ? 'checked' : '') : 'checked' ?>><label for="sm_visible_y">Yes</label>
							&emsp;
							<input type="radio" name="sm_visibity" value="N" id="sm_visible_n" <?php echo isset($_POST['sm_visibity']) && count($errors) > 0 ? ($_POST['sm_visibity'] == 'N' ? 'checked' : '') : '' ?>><label for="sm_visible_n">No</label>
						</li>
					</ul>
				</div>
			</div>
			<button class="nova-button nova-button-primary" type='submit' name='Save'>
				<img class="nova-button-icon" src="<?php echo plugins_url('epik_social_media/images/save.png'); ?>">
				Add Social Media
			</button>
		</div>
	</div>
</form>
<script>
	jQuery(document).ready(function($){
		$(".nova-upload-trigger").click(function () {
			$( this ).closest(".nova-upload").find("input[type=file]").trigger('click');
		});

		$('.nova-upload input[type=file]').change(function(){
			$(this).next().find('input').val($(this).val());
		});
	});
</script>