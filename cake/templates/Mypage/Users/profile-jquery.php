<?php
/*

	プロフィール画面のjQuery版
	実際にこのファイルを使うことはありません。
*/
?>

<div class="profile-class">
	<h2>プロフィール編集</h2>
	<fieldset>
	<?= $this->Form->create($user,['url' =>['action'=>'profile'], 'type' => 'file','id'=>'profile']) ?>
		<div class="name">
			<p>名前</p>
			<?= $this->Form->control('name', ['type' => 'text', 'placeholder' => '10文字以内で入力して下さい', 'label' => false]) ?>
		</div>
		<div class="prefecture">
			<p>出身地</p>
			<?= $this->Form->control('prefecture_id',['type'=>'select', 'label' => false, 'name'=> 'prefecture_id']) ?>
		</div>
		<div class="profile">
			<p>プロフィール</p>
			<?= $this->Form->control('profile',['type'=>'textarea', 'placeholder' => '100文字以内で入力して下さい', 'label' => false]) ?>
		</div>
		<div class="icon-image">
			<p>アイコン画像</p>
			<?php if($user->is_icon == true){ ?>
				<div class="image-display icon">
					<i class="far fa-window-close" id="icon_image_delete"></i>
					<img 
						src="<?= $this->Url->build('users_icon/'.$user->id.'.png'.'?'.$user->modified->getTimestamp() ) ?>"
						class="img"
						id=<?= $user->modified->getTimestamp() ?> 
					>
				</div>
				<?= $this->Form->hidden('icon_image_delete') ?>
			<?php } ?>
			<?= $this->Form->file('icon_image',['accept'=> 'image/png,image/jpg,image/jpeg', 'id' => "icon_image", 'target' => 'icon', 'label' => false]) ?>
		</div>
		<div class="home-image">
			<p>ホーム画像</p>
			<?php if($user->is_home == true){ ?>
				<div class="image-display home">
					<i class="far fa-window-close" id="home_image_delete"></i>
					<img 
						src="<?= $this->Url->build('users_home/'.$user->id.'.png'.'?'.$user->modified->getTimestamp() ) ?>"
						class="img"
						id=<?= $user->modified->getTimestamp() ?> 
					>
				</div>
				<?= $this->Form->hidden('home_image_delete') ?>
			<?php } ?>
			<?= $this->Form->file('home_image',['accept'=> 'image/png,image/jpg,image/jpeg','id' => "home_image", 'target' => 'home','label' => false, ]) ?>
		</div>
	</fieldset>
	<div class="btn-class">
		<?= $this->Form->button('登録',['class' => 'btn auth']);?>
		<?= $this->Form->end() ?>
	</div>
</div>


<script>
$(function () {
	$('select[name="prefecture_id"]').select2({
		width: "130px",
		hight: "40px",
		placeholder: "出身地を選択して下さい。",
		allowClear: true
	});

	<?php //選択したアイコン画像を表示 ?>
	$('.user-profile').on('change', '#icon_image', function (e) {
		var $icon_image_display = $('.icon-image').find('.image-display.icon');
		if($icon_image_display ){
			$icon_image_display.remove();
		}
		var reader = new FileReader();
		reader.onload = function (e) {
			$('.icon-image p').after(
				'<div class="image-display icon">'
					+'<i class="far fa-window-close" id="icon_image_cancel"></i>'
					+'<img src="'+reader.result+'"class="img">'
				+'</div>'
			);
		}
		reader.readAsDataURL(e.target.files[0]);
	});

	<?php //選択したホーム画像を表示 ?>
	$('.user-profile').on('change', '#home_image', function (e) {
		var $home_image_display = $('.home-image').find('.image-display.home');
		if( $home_image_display ){
			$home_image_display.remove();
		}
		var reader = new FileReader();
		reader.onload = function (e) {
			$('.home-image p').after(
				'<div class="image-display home">'
					+'<i class="far fa-window-close" id="home_image_cancel"></i>'
					+'<img src="'+reader.result+'"class="img">'
				+'</div>'
			);
		}
		reader.readAsDataURL(e.target.files[0]);
	});

	<?php //選択したアイコン画像を削除 ?>
	$('.user-profile').on('click', '#icon_image_delete',function() {
		$('input[name="icon_image_delete"]').val(1);
    	$('.icon-image').find('.image-display.icon').remove();
	});

	<?php //選択したホーム画像を削除 ?>
	$('.user-profile').on('click', '#home_image_delete',function() {
		$('input[name="home_image_delete"]').val(1);
    	$('.home-image').find('.image-display.home').remove();
	});

	<?php //選択したアイコン画像を非表示 ?>
	$('.user-profile').on('click', '#icon_image_cancel',function() {
		$('#icon_image').val('');
    	$('.icon-image').find('.image-display.icon').remove();
	});

	<?php //選択したホーム画像を非表示 ?>
	$('.user-profile').on('click', '#home_image_cancel',function() {
		$('#home_image').val('');
    	$('.home-image').find('.image-display.home').remove();
	});

});
</script>