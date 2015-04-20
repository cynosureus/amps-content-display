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

			return wp_get_attachment_image($file_id);

		} else {

			$product_thumbnail = get_post_meta($post_id, 'wpcf-product-thumbnail', true);
			$file_id = $this->get_attachment_id_from_url($product_thumbnail);

			return wp_get_attachment_image($file_id);

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
		
		$associated_files = get_post_meta($this->material->ID, 'amps_content_items', true);
		return $associated_files;

	}


	function filesDropdown()
	{

		ob_start();

		?>

		<div class="btn-group">
	  		<button type="button" class="btn btn-default dropdown-toggle file-select" data-toggle="dropdown" aria-expanded="false" id = "file-select-<?= $this->material->ID ?>">
	    Select <span class="caret"></span>
	  		</button>
	  		<ul class="dropdown-menu" role="menu">
	    		<?php 

	    			$files = $this->getAssociatedFiles();
	    		


			    	foreach($files as $key=>$value) {

			    	?>
			   

			    		<li><a href = "<?= $files[$key]['url'] ?>" class = "files-dropdown-item"><?= $files[$key]['caption'] ?></a></li>

			    	<?php } ?>
	    
	  		</ul>
		</div>

<?php 

	$dropdown = ob_get_contents();

	ob_end_clean();

	return $dropdown;



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

		$col_size = 12/$items_per_row;

		ob_start();
		?>
			<div class = "col-lg-<?= $col_size ?> col-md-<?=$col_size ?> col-sm-<?=$col_size ?> single-wrapper">

				<div class = "single-item" >
											
					<div class = "media-image" title = "<?= $this->material->post_content; ?>">
						<center><?= $this->pickDisplayImage($post_id); ?></center>
					</div>
					<span class = "material-caption text-center"><p><?= $this->material->post_title; ?></p></span>
													
					<span class = "material-obtain">
						<p>

							

							<div class = "material-file-select">
								<?= $this->filesDropdown(); ?>
							</div>

							<div id = "preview-download" class = "preview-download text-center">
								<span class = "material-preview"><a id = "material-preview-link-<?= $this->material->ID ?>" href = "<?= $this->materialUrl(); ?>" >Preview </a></span>								
								 |
								<span class = "material-download"><a id = "material-download-link-<?= $material->ID ?>" href = "<?= $this->materialUrl(); ?>" download = "<?= $this->materialFileName(); ?>">Download</a></span>
							</div>
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

	
		return $buffer;
	}


	





	


}

