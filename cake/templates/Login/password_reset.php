<div class="wrapper">
	<h2>パスワードの再設定</h2>
	<div class="input-setting">
		<?= $this->Form->create(null, ['url' =>['action'=>'passwordReset']]) ?>
			メールアドレス
			<?= $this->Form->control('email',['type'=>'email','label' => false]) ?>
			<?= $this->Form->error('email') ?>
			<div class="help-text">
			<?= SYSTEM_NAME ?>に登録されたメールアドレスを入力してください。
			</div>
			<div class="btn-class">
				<?= $this->Form->button('送信',['class' => 'btn success']);?>
			</div>
		<?= $this->Form->end() ?>
	</div>
</div>