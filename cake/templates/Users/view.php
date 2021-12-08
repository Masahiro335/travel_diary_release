<?= $this->Html->script('jmap.js') ?>

<div class="user-view">
	<div class="user-image">
		<div class="user-home">
			<?php if($user->is_home == true){ ?>
				<img 
					src="<?= $user->userImage(HOME) ?>"
					class="home-img"
					id=<?= $user->modified->getTimestamp() ?> 
				>
			<?php }else{ ?>
				<div class="home-img no"></div>
			<?php } ?>
		</div>
		<div class="user-icon">
			<?php if($user->is_icon == true){ ?>
				<img 
					src="<?= $user->userImage(ICON) ?>"
					class="icon-img"
					id=<?= $user->modified->getTimestamp() ?>
					style="cursor:default;"
				>
			<?php }else{ ?>
				<div class="icon-img"><i class="fas fa-user" style="cursor:default;"></i></div>
			<?php } ?>
		</div>
	</div>
	<div class="user-name"><?= h($user->name) ?></div>
	<div class="user-id">ID：<?= $user->unique_id ?></div>
	<div class="user-prefecture">出身：<?= empty($user->prefecture) ? '' : h($user->prefecture->name) ?></div>
	<div class="user-profile"><?= nl2br(h($user->profile)) ?></div>
	<div class="user-shere">
		<a href="<?= $this->twitterShareUrl(SYSTEM_URL.'users/view/'.$user->id, $user->name, false) ?>" class="btn btn-shere user" target="_blank"><?= $this->Html->image('social-twitter.png', ['alt'=>'Twitter', 'class'=>'sns-icon']) ?></a>
		<a href="<?= $this->lineShareUrl(SYSTEM_URL.'users/view/'.$user->id, $user->name, false) ?>" class="btn btn-shere user" target="_blank"><?= $this->Html->image('social-line.png', ['alt'=>'LINE', 'class'=>'sns-icon']) ?></a>
	</div>
	<?= $this->element('follow',['user'=> $user, 'Auth' => $Auth]) ?>
	<div id="tab">
		<div class="user-tabs">
			<div class="tab messages" v-on:click="select(1)" v-bind:class="{active: isActive == 1}">投稿一覧</div>
			<div class="tab goods" v-on:click="select(2)" v-bind:class="{active: isActive == 2}">いいね一覧</div>
			<div class="tab map" v-on:click="select(3)" v-bind:class="{active: isActive == 3}" user_id="<?= $user->id ?>">マップ</div>
		</div>
		<div class="tabContents">
			<div v-show="isActive == 1">
				<div class="messages-list map">
					投稿一覧　　投稿数：<span id="MessageCount"><?= $messages->count() ?></span>
					<div class="messages" id="messages-list">
						<?php if(empty($messages) == false){ ?>
							<?php foreach($messages as $message){ ?>
								<?= $this->element('messages/message',['message' => $message]) ?>
							<?php } ?>
							<div class="scroll-btn">↑</div>
						<?php }else{ ?>
							投稿はありません。
						<?php } ?>
					</div>
				</div>
			</div>
			<div v-show="isActive == 2">
				<div class="messages-list map">
					いいね一覧	　投稿数：<span><?= $messages->count() ?></span>
					<div class="messages">
						<?php if(empty($goodMessages) == false){ ?>
							<?php foreach($goodMessages as $goodMessage){ ?>
								<?= $this->element('messages/message',['message'=>$goodMessage]) ?>
							<?php } ?>
							<div class="scroll-btn">↑</div>
						<?php }else{ ?>
							投稿はありません。
						<?php } ?>
					</div>
				</div>
			</div>
			<div v-show="isActive == 3">
				<div id="jmap"></div>
				<img class="loader-jmap" src="<?= $this->Url->image('preloader.svg') ?>" style="height:80px; width:80px; margin:150px;">
				<div class="messages-list map" id="prefectures-select">
				</div>
			</div>
		</div>
	</div>
</div>

<script>
new Vue({
	el: '#tab',
	data: {
	  isActive: 1
	},
	methods: {
		select: function (num) {
		this.isActive = num;
	  }
	}
});
</script>
