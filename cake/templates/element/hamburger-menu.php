<div class="header-btn hamburger">
	<div class="hamburger-container" id="hamburger">
		<!--ハンバーガーメニューのボタン-->
		<div class="hamburger-btn" @click='ActiveBtn=!ActiveBtn'>
			<i class="fas fa-bars"></i>
		</div>
		<!--サイドメニュー-->
		<div class="side-menu-list" :class="{'active':ActiveBtn}">
			<div class="side-menu">
				<div class="side-item">
					<?= $this->Html->link(__('TRAVEL'),  '/', ['class' => 'btn-header home'] ) ?>
				</div>
				<div class="side-item">
					<?php if($Auth->is_icon == true){ ?>	
						<?= $this->Html->image($Auth->userImage('icon'), ['class' => "icon-user"]); ?>
					<?php }else{ ?>
						<div class="icon-user"><i class="fas fa-user"></i></div> 
					<?php } ?>
					<?= h($Auth->name) ?>
				</div>
				<div class="side-item follow">
					<span>フォロー　<?= $Auth->followUsers()->count() ?>人</span>
					<span>フォロワー　<?= $Auth->followerUsers()->count() ?>人</span>
				</div>
					<div class="side-menu-title">メニュー</div>
					<a href="<?= $this->Url->build(['prefix' => 'Mypage', 'controller' => 'Users', 'action' => 'index']) ?>" class="side-menu-link">マイページ</a>
					<a href="<?= $this->Url->build(['prefix' => 'Mypage', 'controller' => 'Users', 'action' => 'profile']) ?>" class="side-menu-link">プロフィール</a>
					<a href="<?= $this->Url->build(['prefix' => 'Mypage', 'controller' => 'Users', 'action' => 'setting']) ?>" class="side-menu-link">設定</a>
					<a href="<?= $this->Url->build(['prefix' => false, 'controller' => 'Login', 'action' => 'signout']) ?>" onclick="return confirm('本当にログアウトしてもよろしいでしょうか？')" class="side-menu-link">ログアウト</a>
			</div>
			<div class="side-cancel" @click='ActiveBtn=!ActiveBtn'></div>
		</div>
	</div>
</div>

<script>
var hamburger_btn = new Vue({
	el: '#hamburger',
	data: {
		ActiveBtn: false
	},
});
</script>