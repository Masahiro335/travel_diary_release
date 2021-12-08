<div class="wrapper">
	<h2>設定</h2>
	<ul class="item-list">
		<li><?= $this->Html->link('メールアドレス変更',['prefix' => 'Mypage', 'controller' => 'Users', 'action' => 'emailEdit'] ) ?></li>
		<li><?= $this->Html->link('パスワード変更',['prefix' => 'Mypage', 'controller' => 'Users', 'action' => 'passwordEdit'] ) ?></li>
		<li><?= $this->Html->link('退会について',['prefix' => 'Mypage', 'controller' => 'Users', 'action' => 'withdrawal'] ) ?></li>
	</ul>
</div>