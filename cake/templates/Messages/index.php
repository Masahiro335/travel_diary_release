<script src="https://unpkg.com/vue-router"></script>

<div class="messages-list" id="messages-list">
	<p>投稿数：<span id="MessageCount"><?= $this->Paginator->counter('{{count}}') ?></span></p>
	<div class="messages">
		<?php foreach ($queryMessages as $message){ ?>
			<?php //投稿画面 ?>
			<?= $this->element('messages/message',['message'=>$message, 'clickColor'=>true]) ?>
		<?php } ?>
	</div>
	<?php if($this->Paginator->counter('{{count}}')== 0){ ?>
		<div class="message-none">
			<h1>投稿はありません</h1>
			<img src=" <?= $this->Url->build( 'default/zero.png') ?>"  class="image-zero">
		</div>
	<?php } ?>
	<p><?= $this->Paginator->counter(__('現在のページ：{{page}}　最大ページ：{{pages}}')) ?></p>
	<div class="paginator">
		<ul class="pagination">
			<?= $this->Paginator->first('<< ' . __('最初へ')) ?>
			<?= $this->Paginator->prev('< ' . __('前へ')) ?>
			<?= $this->Paginator->numbers() ?>
			<?= $this->Paginator->next(__('次へ') . ' >') ?>
			<?= $this->Paginator->last(__('最後へ') . ' >>') ?>
		</ul>
	</div>
	<div class="scroll-btn">↑</div>
</div>

<?= $this->Html->script('vue/router.js') ?>

<script>
new Vue({
    router,
	el: '#messages-list',
	mixins: [clickColor],
	data: {
		isClick: 0,
	},
});
</script>
