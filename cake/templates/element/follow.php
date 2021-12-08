<div class="user-follow">
	<div id="follow">
		<?php if( $this->request->getParam('prefix') != 'Mypage' && $user->id !== @$Auth->id ){ ?>
			<?= $this->element('follow-btn',['user' => $user, 'Auth' => $Auth]) ?>
		<?php } ?>
		<div class="follow-list-btn">
			<span v-on:click="open(1, show = !show)">フォロー　<?= $user->followUsers()->count() ?>人</span>
			<span v-on:click="open(2, show = !show)">フォロワー　<?= $user->followerUsers()->count() ?>人</span>
		</div>
		<transition>
			<div class="follow-list" v-show="show">
				<div class="follow-users" v-show="type === 1">
					<?php if($user->followUsers()->count() > 0){ ?>
						<?php foreach($user->followUsers() as $follow_user){ ?>
							<div class="follow-user">
								<?php if(empty($Auth) || $follow_user->id !== @$Auth->id){ ?>
									<?= $this->element('follow-btn',['user' => $follow_user, 'Auth' => $Auth]) ?>
								<?php } ?>
									<?= $this->element('icon',['user' => $follow_user ]) ?>
								<?php //文字制限100文字　改行削除 ?>
								<?= h(str_replace(PHP_EOL,'',mb_strimwidth($follow_user->profile,0,100,'...'))) ?>
							</div>
						<?php } ?>
					<?php }else{ ?>
						フォローしている人がいません。
					<?php } ?>
				</div>
				<div class="follower-users" v-show="type === 2">
					<?php if($user->followerUsers()->count() > 0){ ?>
						<?php foreach($user->followerUsers() as $follower_user){ ?>
							<div class="follower-user">
								<?php if(empty($Auth) || $follower_user->id !== @$Auth->id){ ?>
									<?= $this->element('follow-btn',['user' => $follower_user, 'Auth' => $Auth]) ?>
								<?php } ?>
								<?= $this->element('icon',['user' => $follower_user ]) ?>
								<?= h(str_replace(PHP_EOL,'',mb_strimwidth($follower_user->profile,0,100,'...'))) ?>
							</div>
						<?php } ?>
					<?php }else{ ?>
						フォローされている人がいません。
					<?php } ?>
				</div>
			</div>
		</transition>
	</div>
</div>

<?= $this->Html->script('vue/follow.js') ?>

<script>
new Vue({
	el: "#follow",
	mixins: [followBtn],
	data: {
		show: false,
		type: 1,
		is_follow: [],
	},
});
</script>
