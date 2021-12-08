<div class="mobile-footer">
	<div class="mobile-footer-list">
		<a href="/" class="fas fa-home"></a>
		<?php //検索画面 ?>
		<?php if( $search_display == true){ ?>
			<i class="fas fa-search"></i>
		<?php } ?>
		<?php //マップ画面 ?>
		<?php if( $search_display == true){ ?>
			<a href="<?= $this->Url->build(['prefix'=>false, 'controller'=>'Prefectures', 'action'=>'index']) ?>" class="fas fa-map-marker-alt"></a>
		<?php } ?>
		<?php if(empty($Auth)){ ?>
			<a href="/signup" class="fas fa-user-plus"></a>
			<a href="/signin" class="fas fa-sign-in-alt"></a>
		<?php }else{ ?>
			<a href="/mypage/users" class="fas fa-user footer"></a>
		<?php } ?>
	</div>
</div>

<script>
$(function(){
	$('body').on('click','.fa-search', function(){
		$('.modal.search-modal').css('display','block');
	});
});
</script>