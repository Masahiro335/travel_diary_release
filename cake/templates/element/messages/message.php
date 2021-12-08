<article>
	<div class="message" id="message<?= h($message->id) ?>" v-bind:class="<?= empty($clickColor) ? '' : '{active: isClick == '.$message->id.'}'?>" >
		<?= $this->element('icon',['user' => $message->user]) ?>
		<a href="#message<?= h($message->id) ?>" class="list date" @click='doClick(<?= $message->id ?>)'>
			<?= $message->created->format('Y年m月d日 H:i') ?>
		</a>
		<?php if(empty($message->prefectures) == false){ ?>
			<div class="list prefectures">
				<?php foreach ($message->prefectures as $prefecture){ ?>
					・<?= h($prefecture->name) ?>
				<?php } ?>
			</div>
		<?php } ?>
		<div class="list">
			<p><?= nl2br(h($message->message)) ?></p>
		</div>
		<div class="list">
			<?php if(empty($message->message_images) == false){ ?>
				<div class="images">
					<?php foreach ($message->message_images as $message_image){ ?>
						<div class="image">
							<img 
								src="<?= $message_image->image ?>" 
								class="img display" 
								id="<?= $message->id ?>"
								sort="<?= $message_image->sort ?>"
							>
						</div>
					<?php } ?>
				</div>
				<?php //画像拡大 ?>
				<?= $this->element('messages/image_expansion',['message' => $message]) ?>
			<?php } ?>
		</div>
		<div class="message-icon">
			<?php //シェア ?>
			<?= $this->element('share_modal',['message' => $message]) ?>
			<i class="fas fa-share-square" message_id="<?= $message->id ?>" style="margin-right:10px;"></i>
			<?php //コメント ?>
			<i class="far fa-comment-alt" message_id="<?= $message->id ?>"></i>
			<div class="message-comments-count" id="comments-count-<?= $message->id ?>"><?= count($message->comments) ?></div>
			<?php //いいね ?>
			<?php if(empty($Auth)){ ?>
				<i class="fas fa-heart"></i>
			<?php }elseif(empty($message->auth_goods) == false){ ?>
				<a class="fas fa-heart good" message_id="<?= $message->id ?>"></a>
			<?php }else{ ?>
				<a class="fas fa-heart not-good" message_id="<?= $message->id ?>"></a>
			<?php } ?>
			<div class="message-goods-count"><?= count($message->goods) ?></div>
		</div>
		<div class="btn-class">
			<?php if(!empty($Auth) && $Auth->id == $message->user_id){ ?>
				<div class="btn edit" message_id="<?= h($message->id) ?>">編集</div>
				<?= $this->Html->link('削除',"/messages/delete/{$message->id}", ['class' => 'btn delete'] ) ?>
			<?php } ?>
		</div>
		<?php if( $message->is_edit == true ){ ?>
			<span class="edit-message">(編集済み)</span>
		<?php } ?>
		<img class="loader-delete" src="<?= $this->Url->image('preloader.svg') ?>" style="display:none; width:2em; margin: 5px;">
	
		<?php if(!empty($Auth) && $Auth->id == $message->user_id){ ?>
			<?php //編集画面 ?>
			<?= $this->element('messages/edit',['message' => $message, 'message_id' => $message->id]) ?>
		<?php } ?>

	</div>
</article>

<?php //コメント画面 ?>
<?= $this->element('comments',['message'=>$message]) ?>