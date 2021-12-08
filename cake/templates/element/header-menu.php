<div class="header-top">
	<?php if(empty($Auth) == false ){ ?>
		<?= $this->element('hamburger-menu',['Auth' => empty($Auth) ? '' : $Auth]); ?>
	<?php } ?>
	<div class="header-btn home">
		<?= $this->Html->link(__('TRAVEL'),  '/', ['class' => 'btn-header home'] ) ?>
	</div>
	<div class="header-btn sign">
		<?php if(empty($Auth)){ ?>
			<?= $this->Html->link('会員登録',['controller' => 'Login', 'action' => 'signup'], ['class' => 'btn signup'] ) ?>
			<?= $this->Html->link('ログイン',['controller' => 'Login', 'action' => 'signin'], ['class' => 'btn signin'] ) ?>
		<?php }else{ ?>
			<a href="<?= $this->Url->build(['prefix' => 'Mypage', 'controller' => 'Users', 'action' => 'index']) ?>" class="mypage-icon">
			<?php if($Auth->is_icon == true){ ?>	
				<?= $this->Html->image($Auth->userImage(ICON),['class' => "icon-user"]); ?>
			<?php }else{ ?>
				<div class="icon-user"><i class="fas fa-user"></i></div> 
			<?php } ?>
					<?= h($Auth->name) ?>
				</a>
		<?php } ?>
	</div>
</div>