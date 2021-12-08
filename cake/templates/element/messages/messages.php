<div class="messages">
	<?php foreach($messages as $message){ ?>
		<?= $this->element('messages/message',['message'=>$message]) ?>
	<?php } ?>
</div>