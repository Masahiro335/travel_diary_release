<?php //シェア ?>
<div class="modal shere" id="shere<?= $message->id ?>">
	<div class="modalchild shere">
		<div class="shere">
			<div class="shere-title">
				<h2>この投稿をシェアする</h2>
			</div>
			<div class="shere-select">
				<a href="<?= $this->twitterShareUrl(SYSTEM_URL.'#message'.$message->id, $message->user->name, true) ?>" class="btn btn-shere" target="_blank"><?= $this->Html->image('social-twitter.png', ['alt'=>'Twitter', 'class'=>'sns-icon']) ?>Twitterでシェア</a>
			</div>
			<div class="shere-select">
			<a href="<?= $this->lineShareUrl(SYSTEM_URL.'#message'.$message->id, $message->user->name, true) ?>" class="btn btn-shere" target="_blank"><?= $this->Html->image('social-line.png', ['alt'=>'LINE', 'class'=>'sns-icon']) ?>LINEでシェア</a>
			</div>
		</div>
	</div>
</div>