<div class="wrapper">
	<h2>パスワードの再設定</h2>
	<div class="input-setting">
		<?= $this->Form->create(null, ['url' =>['action'=>'passwordResetComplete']]) ?>
			パスワード
			<?= $this->Form->control('password',['type'=>'password','label' => false]) ?>
			<div class="help-text">
				・パスワードを入力してください。</br>
				・8文字以上20文字以内で入力してください。
			</div>
			<div class="btn-class">
				<?= $this->Form->button('送信',['class' => 'btn success']);?>
			</div>
		<?= $this->Form->end() ?>
	</div>
</div>