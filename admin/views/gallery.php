<table class="form-table">
      	<tr>
      		<td>
        		<a class="image-add button" href="#" data-uploader-title="Add image(s) to gallery" data-uploader-button-text="Add image(s)">Add image(s)</a>
        		<ul id="image-list">
        		<?php if (@get_post_meta($post->ID, 'images', true)) : foreach (@get_post_meta($post->ID, 'images', true) as $key => $value) : $image = wp_get_attachment_image_src($value); ?>
          			<li>
            				<input type="hidden" name="images[<?php echo $key; ?>]" value="<?php echo $value; ?>">
            				<img class="image-preview" src="<?php echo $image[0]; ?>">
            				<small><a class="remove-image" href="#">Remove image</a></small>
          			</li>
        		<?php endforeach; endif; ?>
        		</ul>
      		</td>
      	</tr>
</table>
