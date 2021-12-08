<div class="comments" id="comments_<?= $message->id ?>">
    <div class="comment-boxs" id="comment-boxs-<?= $message->id ?>">
        <?php foreach($message->comments as $comment){ ?>
            <?= $this->element('comment',['comment' => $comment, 'message' => $message]) ?>
        <?php } ?>
    </div>
	<div class="comment-post">
		<?php if(!empty($Auth)){ ?>
			<?= $this->Form->create(null, ['controller' => 'Commnets', 'action' => 'edit'])?>
				<?= $this->Form->control('comment',[
                    'type'=>'textarea', 
                    'placeholder' => '140文字以下で入力してください。', 
					'style' => 'margin-top:20px;',
                    'label' => false,
                ]) ?>
			<?= $this->Form->end() ?>
			<div class="btn btn-comment" message_id="<?= $message->id ?>" >送信</div>
			<img class="loader-comment" src="<?= $this->Url->image('preloader.svg') ?>" style="display:none; height:50px; width:50px;">
		<?php } ?>
	</div>
	<div class="comments-close">
		<i class="fas fa-chevron-up" message_id="<?= $message->id ?>"></i>
	</div>
</div>