<div class="<?= $iconOrhome ?>-image" id="<?= $iconOrhome ?>-image">
	<p><?= $title ?></p>
	<?php if($is_image == true){ ?>
		<div class="image-preview" v-if="show == 1">
			<i class="far fa-window-close" @click='deleteFile()'></i>
			<img 
				src="<?= $user->userImage($iconOrhome) ?>" 
				class="img" 
				id=<?= $user->modified->getTimestamp() ?> 
			>
		</div>
		<input type="hidden" name="delete_<?= $iconOrhome ?>" :value="is_delete">
	<?php }else{ ?>
		<div class="image-preview" v-if="show == 1">
			<div class="img" style="background-color: #c0c0c059;"></div>
		</div>
	<?php } ?>
	<div class="image-preview" v-if="show == 2">
		<i class="fas fa-redo" @click='deleteFile()'></i>
		<img :src="url" class="img">
	</div>
	<div class="image-preview" v-if="show == 3">
		<i class="fas fa-redo" @click='deleteFile()'></i>
		<div class="img" style="background-color: #c0c0c059;"></div>
	</div>
	<?= $this->Form->file($iconOrhome,
		[
			'accept'=> 'image/png,image/jpg,image/jpeg',
			'label' => false,
			'ref' => 'preview',
			'@change' => 'uploadFile'
		]) 
	?>
</div>
