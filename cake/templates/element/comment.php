<div class="comment-box" comment-id="<?= $comment->id ?>">
    <?= $this->element('icon',['user' => $comment->user]) ?>
    <?php if($comment->is_deleted == false){ ?>
        <?php if($comment->user->id == $message->user->id){ ?>
            <div class="contributor">投稿者</div>
		<?php }elseif(empty($Auth) == false && $Auth->id == $comment->user->id){ ?>
            <div class="contributor">自分</div>
		<?php } ?>
        <div class="comment">
			<?php if(empty($Auth) == false && $Auth->id == $comment->user->id){ ?>
				<i class="far fa-window-close" comment-id="<?= $comment->id ?>" id="comment-icon"></i>
			<?php } ?>
			<p><?= nl2br(h($comment->comment)) ?></p>
		</div>
    <?php }else{ ?>
        <div class="comment"><p>コメントが削除されました。</p></div>
    <?php } ?>
</div>