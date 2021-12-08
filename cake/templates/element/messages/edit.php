<article>
	<div class="modal save-modal" id="save<?= $message_id ?>">
		<div class="modalchild save-modalchild"> 
			<div class="save-message">
				<h2><?= empty($message) ? '投稿追加' : '投稿編集' ?></h2>
				<?= 
					$this->Form->create($message,[
						'type' => 'file',
						'name'=> empty($message) ? 'add' : 'edit',
						'action' => 'save','id'=>'saveForm'.$message_id,
					])
				?>
				<fieldset>
					<div class="save-item">
						<div class="save-message-title">旅行先</div>
						<div class="prefectures-class">
							<?= $this->Form->control('prefectures',[
								'type'=>'select', 
								'value' => empty($message->prefectures) ? '' : $message->prefecturesList($message->prefectures), 
								'multiple'=> 'multiple',
								'name'=> 'prefectures-edit', 
								'options'=> $prefectures,
								'label' => false
							]) ?>
						</div>
					</div>
					<div class="save-item">
						<div class="save-message-title">メッセージ</div>
						<div class="message-class">
							<?= $this->Form->control('message',['type'=>'textarea', 'placeholder' => '140文字以下で入力してください。', 'label' => false]) ?>
						</div>
					</div>
					<div class="image-class" id="image<?= $message_id ?>">
						<div class="save-message-title">画像</div>
						<?php if(!empty($message->message_images)){ ?>
							<?php foreach ($message->message_images as $message_image){ ?>
								<div class="image edit" data-id="<?= $message_image->id ?>" >
									<img 
										src="<?= $message_image->image ?>" 
										class="img" 
										<?php //@change="uploadFile" ?>
									>
									<?php //画像削除ボタン ?>
									<img 
										src=" <?= $this->Url->build( 'default/image_delete_icon.png' ) ?>" 
										class="image-delete" message_imgae_id="<?= $message_image->id ?>" 
									>
								</div>
							<?php } ?>
						<?php }?>
					</div>
					<?= $this->Form->file('images[]',['accept'=> 'image/png,image/jpg,image/jpeg', 'multiple'=> true, 'id' => 'imagefiles']) ?>
					<div class="help-text">
						※4枚までアップロード可能です。</br>
						※10MBまでアップロード可能です。</br>
					</div>

					<?= $this->Form->hidden('delete_image_ids') ?>
					<?= $this->Form->hidden('image_count') ?>
					<?php if( $this->request->getParam('controller') != 'Messages'){ ?>
						<?= $this->Form->hidden('userPage',['value'=>'1']) ?>
					<?php } ?>
				</fieldset>
				<?= $this->Form->end() ?>
			</div>
			<div class="btn-class">
				<a 
					class="btn save"  
					href="<?= $this->Url->build('/messages/edit/'.$message_id )?>"
					message_id=<?= $message_id ?> 
				>
					<?= empty($message) ? '追加' : '編集' ?>
				</a>
				<div class="btn save-close" message_id=<?= $message_id ?> >閉じる</div>
			</div>
			<img class="loader-save" src="<?= $this->Url->image('preloader.svg') ?>" style="display:none; height:80px; width:80px; margin:150px;">
		</div>
	</div>
</article>
