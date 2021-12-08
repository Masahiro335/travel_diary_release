<ul class="sidenavi">
	<li><?= $this->Html->link('マイページ',['prefix' => 'Mypage', 'controller' => 'Users', 'action' => 'index'],['class' => $nav == NAV_MYPAGE ? 'active' : ''] ) ?></li>
	<li><?= $this->Html->link('プロフィール',['prefix' => 'Mypage', 'controller' => 'Users', 'action' => 'profile'],['class' => $nav == NAV_PROFILE ? 'active' : '']  ) ?></li>
	<li><?= $this->Html->link('設定',['prefix' => 'Mypage', 'controller' => 'Users', 'action' => 'setting'],['class' => $nav == NAV_SETTING ? 'active' : ''] ) ?></li>
	<li><?= $this->Form->postLink('ログアウト',['prefix' => false, 'controller' => 'Login', 'action' => 'signout'],['confirm' => '本当にログアウトしてもよろしいでしょうか？'] ) ?></li>
</ul>
