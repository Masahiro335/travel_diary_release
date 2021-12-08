<div class="users-login">
	<h2>ログイン</h2>
	<fieldset>
		<h3>SNSでログイン</h3>
		<a href="<?= $this->Url->build('/signin/'.\App\Model\Table\SnsProvidersTable::SNS_ID['GOOGLE'])  ?>" class="btn btn-sns"><?= $this->Html->image('social-google.png',  ['alt'=>'Google', 'class'=>'sns-icon'])  ?>Googleでログイン</a>
		<a href="<?= $this->Url->build('/signin/'.\App\Model\Table\SnsProvidersTable::SNS_ID['TWETTER']) ?>" class="btn btn-sns"><?= $this->Html->image('social-twitter.png', ['alt'=>'Twitter', 'class'=>'sns-icon']) ?>Twitterでログイン</a>
		<a href="<?= $this->Url->build('/signin/'.\App\Model\Table\SnsProvidersTable::SNS_ID['LINE'])    ?>" class="btn btn-sns"><?= $this->Html->image('social-line.png',    ['alt'=>'LINE', 'class'=>'sns-icon'])    ?>LINEでログイン</a>
	</fieldset>
	</br>
	<fieldset>
		<h3>メールアドレスでログイン</h3>
		<?= $this->Form->create(null,['url' =>['action'=>'signin'], 'type' => 'post', 'id'=>'signin']) ?>
		<div class="mail-class">
			<p>メールアドレス</p>
			<?= $this->Form->control('email', ['type' => 'email', 'label' => false]) ?>
		</div>
		<div class="password-class">
			<p>パスワード</p>
			<?= $this->Form->control('password',['type'=>'password', 'label' => false]) ?>
		</div>
		<div class="btn-class">
			<?= $this->Form->button('ログイン',['class' => 'btn success']);?>
			<?= $this->Form->end() ?>
		</div>
	</fieldset>
	<div class="help-text">
		パスワードのお忘れの方は<a href="/password-reset">こちら</a>をクリックしてください。</br>
	</div>
</div>