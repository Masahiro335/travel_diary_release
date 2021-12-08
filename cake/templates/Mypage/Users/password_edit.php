<div class="wrapper">
	<h2>パスワード変更</h2>
	<div class="input-setting">
		<?= $this->Form->create(null, ['url' =>['action'=>'passwordEdit']]) ?>
			新しいパスワード
			<?= $this->Form->control('password',['type'=>'password','label' => false]) ?>
			<?= $this->Form->error('password') ?>
			<div class="help-text">
				・8文字以上20文字以内で入力してください。
			</div>
			<div class="btn-class">
				<a href="/mypage/users/setting" class="btn cancel'">戻る</a>
				<?= $this->Form->button('変更',['class' => 'btn success']);?>
			</div>
		<?= $this->Form->end() ?>
	</div>
</div>
