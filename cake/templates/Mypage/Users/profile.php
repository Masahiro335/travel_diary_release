<div class="profile-class">
	<h2>プロフィール編集</h2>
	<fieldset>
	<?= $this->Form->create($user,['url' =>['action'=>'profile'], 'type' => 'file','id'=>'profile']) ?>
		<div class="name">
			<p>名前</p>
			<?= $this->Form->control('name', ['type' => 'text', 'placeholder' => '10文字以内で入力して下さい', 'label' => false]) ?>
		</div>
		<div class="email" style="font-size:20px;">
			<p>メールアドレス</p>
			<?= h($user->email) ?>
		</div>
		<div class="prefecture">
			<p>出身地</p>
			<?= $this->Form->control('prefecture_id',['type'=>'select', 'label' => false, 'name'=> 'prefecture_id']) ?>
		</div>
		<div class="profile">
			<p>プロフィール</p>
			<?= $this->Form->control('profile',['type'=>'textarea', 'placeholder' => '100文字以内で入力して下さい', 'label' => false]) ?>
		</div>
		<?= $this->element('profile_image',[
			'title' => 'アイコン画像',
			'user' => $user, 
			'iconOrhome' => ICON,
			'is_image' => $user->is_icon
		]) ?>
		<?= $this->element('profile_image',[
			'title' => 'ホーム画像',
			'user' => $user, 
			'iconOrhome' => HOME,
			'is_image' => $user->is_home
		]) ?>
	</fieldset>
	<div class="btn-class">
		<?= $this->Form->button('登録',['class' => 'btn save']);?>
		<?= $this->Form->end() ?>
	</div>
</div>

<?= $this->Html->script('vue/image_upload.js') ?>

<script>
new Vue({
	el: "#icon-image",
	mixins: [uploadFile],
	data: {
		url:'',
		show: 1,
		is_delete: 0
	},
});


new Vue({
	el: "#home-image",
	mixins: [uploadFile],
	data: {
		url:'',
		show: 1,
		is_delete: 0
	},
});
</script>