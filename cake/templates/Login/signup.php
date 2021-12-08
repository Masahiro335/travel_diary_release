<div class="users-login">
	<h2>新規会員登録</h2>
	<div class="input-form-section">
		<fieldset>
			<h3>SNSで登録</h3>
			<a href="<?= $this->Url->build('/signin/'.\App\Model\Table\SnsProvidersTable::SNS_ID['GOOGLE'])  ?>" class="btn btn-sns"><?= $this->Html->image('social-google.png',  ['alt'=>'Google', 'class'=>'sns-icon'])  ?>Googleで登録</a>
			<a href="<?= $this->Url->build('/signin/'.\App\Model\Table\SnsProvidersTable::SNS_ID['TWETTER']) ?>" class="btn btn-sns"><?= $this->Html->image('social-twitter.png', ['alt'=>'Twitter', 'class'=>'sns-icon']) ?>Twitterで登録</a>
			<a href="<?= $this->Url->build('/signin/'.\App\Model\Table\SnsProvidersTable::SNS_ID['LINE'])    ?>" class="btn btn-sns"><?= $this->Html->image('social-line.png',    ['alt'=>'LINE', 'class'=>'sns-icon'])    ?>LINEで登録</a>
		</fieldset>
		<h3>メールアドレスで登録</h3>
		<a href="<?= $this->Url->build('/registration') ?>" class="btn btn-mail">
			<i class="fas fa-envelope"></i>メールアドレスで登録
		</a>
	</div>
</div>