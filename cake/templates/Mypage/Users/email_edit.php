<div class="wrapper">
	<h2>メールアドレスの変更</h2>
	<div class="input-setting">
		<div class="email-present">
			現在のメールアドレス</br>
			<?= h($Auth->email) ?>
		</div>
		<?= $this->Form->create(null, ['url' =>['action'=>'emailEdit']]) ?>
			新しいメールアドレス
			<?= $this->Form->control('email',['type'=>'email','label' => false]) ?>
			<?= $this->Form->error('email') ?>
			<div class="btn-class">
				<a href="/mypage/users/setting" class="btn cancel'">戻る</a>
				<?= $this->Form->button('送信',['class' => 'btn success']);?>
			</div>
		<?= $this->Form->end() ?>
	</div>
</div>