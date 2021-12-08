<div class="users-login">
	<h2>新規登録</h2>
	<fieldset>
		<?= $this->Form->create($user,['url' =>['action'=>'registration'], 'type' => 'post', 'id'=>'registration']) ?>
		<div class="mail-class">
			<p>メールアドレス</p>
			<?= $this->Form->control('email', ['type' => 'email', 'label' => false]) ?>
		</div>
		<div class="password-class">
			<p>パスワード</p>
			<?= $this->Form->control('password',['type'=>'password', 'label' => false]) ?>
				<div class="help-text">
				・半角英数字で入力してください</br>
				・8文字以上20文字以内で入力してください。
			</div>
		</div>
	</fieldset>
	<div class="btn-class">
		<?= $this->Form->button('登録',['class' => 'btn success']);?>
		<?= $this->Form->end() ?>
	</div>
</div>