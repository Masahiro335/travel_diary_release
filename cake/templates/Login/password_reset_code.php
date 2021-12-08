<div class="wrapper">
	<h2>パスワードの認証コード</h2>
	<div class="input-setting">
		<?= $this->Form->create(null, ['url' =>['action'=>'passwordResetCode']]) ?>
			認証コード
			<?= $this->Form->control('code',['type'=>'text','label' => false]) ?>
			<div class="help-text">
				メールで送信された認証コードを入力してください。
			</div>
			<div class="btn-class">
				<?= $this->Form->button('送信',['class' => 'btn success']);?>
			</div>
		<?= $this->Form->end() ?>
	</div>
</div>