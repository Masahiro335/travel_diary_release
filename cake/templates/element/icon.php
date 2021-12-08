<div class="icon-class">
    <?php if($user->is_icon == true){ ?>

		<?= $this->Html->image($user->userImage('icon'),
			[
				'class' => "icon",
				'id' => $user->modified->getTimestamp(),
				'url' => ['prefix' => false,'controller' => 'Users', 'action' => 'view', $user->id]
			]);
		?>
    <?php }else{ ?>
		<?= $this->Html->link('', '/users/view/'.$user->id, ['class' => "fas fa-user"]); ?>
    <?php } ?>
    <div class="name"><?= h($user->name) ?></div>
</div>