<?php

/*
Plugin Name: AMPS Content Display
Plugin URI: http://www.cynosure.com
Description: Object to simplify display of different content types
Version: 1.0
Author: Daniel Miller


*/

add_image_size( 'amps-thumb-250-250', 250, 400, true);


class Amps_Content_Display
{
	function __construct($material)
	{
		$this->material = $material;
	}


	function PickDisplayImage($post_id)
	{

		$media_thumbnail = get_post_meta($this->material->ID, 'wpcf-material-thumbnail-image', true);

		if($media_thumbnail) {

			$file_id = $this->get_attachment_id_from_url($media_thumbnail);

			return wp_get_attachment_image($file_id, 'amps-thumb-250-250');

		} else {

			$product_thumbnail = get_post_meta($post_id, 'wpcf-product-thumbnail', true);
			$file_id = $this->get_attachment_id_from_url($product_thumbnail);

			return wp_get_attachment_image($file_id, 'amps-thumb-250-250');

		}
	}

	function get_attachment_id_from_url( $file_src ) {
 
	  global $wpdb;
		$query = "SELECT ID FROM {$wpdb->posts} WHERE guid='$file_src'";
		$id = $wpdb->get_var($query);
		return $id;
	}


	function displayContent()
	{
		if ($this->material->post_content) {
			return $this->material->post_content;
		} else {
			return $this->material->post_title;
		}
	}

	function getMedia($mediaUrl) 
	{
		$media_id = $this->get_attachment_id_from_url($mediaUrl);
		$media = get_post($media_id);
		return $media;
	}

	function getAssociatedFiles()
	{
		
		$associated_files = get_post_meta($this->material->ID, 'wpcf-material-file', false);
		return $associated_files;

	}

	function returnSelectFormat() 
	{

		$select_format = array();

		$associated_files = $this->getAssociatedFiles();

		foreach($associated_files as $file) {


			$select_format[$file] = $this->getMedia($file)->post_title; 

		}

		return $select_format;
	}

	function filesDropdown()
	{
		$select = '<center><div class = "css-select-moz">';
		$select .= '<select class = "amps-dropdown file-select" id = "file-select-' . $this->material->ID . '" >';

		$files = $this->returnSelectFormat();

		foreach($files as $key => $value) {

			$select .= '<option value = ' . $key . '>' . $value . '</option>';
		}

		$select .= '</select>';
		$select .= '</div></center>';


		return $select;

	}

	function materialUrl()
	{
		$url = get_post_meta($this->material->ID, 'wpcf-material-file', true);

		return $url;
	}

	function materialFileName() 
	{
		$url = $this->materialUrl();
		$file_array = explode('/', $url);
		$file_name = array_pop($file_array);

		$file_array = explode('.', $file_name);

		$file_name = array_shift($file_array);
		
		return $file_name;


	}

	function contentBoxHTML($items_per_row, $post_id)
	{

		ob_start();
		?>
			<div class = "col-1-<?=$items_per_row ?> content-single-wrapper <?= get_post_meta($this->material->ID, 'wpcf-thumbnail-orientation', true); ?>">

				<div class = "content content-single-item-inner" >
											
					<div class = "media-image" title = "<?= $this->material->post_content; ?>">
						<center><?= $this->pickDisplayImage($post_id); ?></center>
					</div>
					<span class = "material-caption"><p><?= $this->material->post_title; ?></p></span>
													
					<span class = "material-obtain">
						<p>

							

							<span class = "material-file-select">
								<?= $this->filesDropdown(); ?>
							</span>

							<span id = "preview-download" class = "preview-download">
								<span class = "material-preview"><a id = "material-preview-link-<?= $this->material->ID ?>" href = "<?= $this->materialUrl(); ?>" >Preview </a></span>								
								 |
								<span class = "material-download"><a id = "material-download-link-<?= $material->ID ?>" href = "<?= $this->materialUrl(); ?>" download = "<?= $this->materialFileName(); ?>">Download</a></span>
							</span>
						</p>
					</span>
													
				</div>

			</div>

		<?php
	
		$html = ob_get_contents();

		ob_end_clean();

		return $html;

	}

	function displayContentBox($buffer) 
	{

		d($buffer);
		return $buffer;
	}






	


}

