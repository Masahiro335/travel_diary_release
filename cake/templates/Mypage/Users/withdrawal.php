<div class="wrapper">
	<h2>退会について</h2>
	<div class="input-setting">
		退会すると再度ログインが不可能となります。</br>
		また、投稿やその他のデータが全て失われます。</br>

		<div class="btn-class">
			<?= $this->Form->create(null, ['url' =>['action'=>'withdrawal']]) ?>
				<a href="/mypage/users/setting" class="btn cancel'">戻る</a>
				<?= $this->Form->button('退会',['class' => 'btn success','confirm' => '本当に退会してもよろしいでしょうか？']);?>
				<?= $this->Form->hidden('withdrawal',['value' => '1']) ?>
			<?= $this->Form->end() ?>
		</div>
	</div>
</div>