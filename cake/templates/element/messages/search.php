<div class="searchs">
	<?= $this->Form->create(null, ['url' => ['prefix' => false, 'controller' => 'Messages', 'action' => 'index']]) ?>
	<div class="search" id="sort">Sort</div>
	<div class="click-search" id="click_sort">
		<?= $this->Form->select('order',['最新順','古い順','いいねが多い順','コメントが多い順'],['default' => '0']) ?>
	</div>
	<div class="search" id="date">日付</div>
	<div class="click-search" id="click_date">
		<?= $this->Form->date('begin') ?>
		<?= $this->Form->label('から') ?></br>
		<?= $this->Form->date('end') ?>
		<?= $this->Form->label('まで') ?></br>
	</div>
	<div class="search" id="freeWord">フリーワード</div>
	<div class="click-search" id="click_freeWord">
		<?= $this->Form->input('freeWord',['type'=>'text', 'placeholder' => '検索']) ?>
	</div>
	<div class="search" id="prefectures">旅行先</div>
	<div class="click-search" id="click_prefectures">
		<div class="prefectures">
			<?= $this->Form->control('prefectures',[
				'type'=>'select', 
				'multiple'=> true,
				'name'=> 'prefectures-search', 
				'value' => $selectPrefectures,
				'label' => false
			]) ?>
		</div>
	</div>
	<?php if(empty($Auth) == false){ ?>
		<div class="search-follow">
			<label class="form-label">
				<?= $this->Form->input('follow_check',['type' => 'checkbox','label' => false]) ?>フォローユーザーのみ
			</label>
		</div>
	<?php } ?>
	<?= $this->Form->button('検索',['class' => 'btn btn-search']);?>
	<?= $this->Form->end() ?>
</div>

<script>
$(function(){
	/*検索ボタン押すと検索詳細のボタンの表示*/
	$('.searchs').on('click', '.search', function(){
		var id = $(this).attr('id');
		if($('#click_'+ id).css('display') == 'none'){
			$('#click_'+ id).css('display','inline-block');
		}else{
			$('#click_'+ id).css('display','none');
		}
	});

	/*検索ボタン触ると伸びる*/
	$('.search').hover(
		function () {
			$(this).css('width', '100%');
		},
		function () {
			$(this).css('width', '90%');
		}
	);

	//日付のどちらかが入力されている場合、色変更
	if( $('input[name="begin"]').val() || $('input[name="end"]').val() ){
		$('#date').css('background-color','#f91462');
	}else{
		$('#date').css('background-color','#799dec');
	}

	//beginの日付がendの日付より大きい場合、endの日付をbeginの日付に設定する
	$('.click-search#click_date :input').change(function(){
		if(
			$('input[name="begin"]').val() && $('input[name="end"]').val()
			&& $('input[name="begin"]').val() > $('input[name="end"]').val()
		){
			$('input[name="end"]').val( $('input[name="begin"]').val() );
		}
	});

	//フリーワードの色変更
	//両端に空白を削除
	var freeWord = $.trim( $('input[name="freeWord"]').val() );
	//文字の長さが1文字以上
	if( freeWord.length > 0){
		//赤色に変更
		$('#freeWord').css('background-color','#f91462');
	}else{
		//青色に変更
		$('#freeWord').css('background-color','#799dec');
	}

	//フリーワードのEnterボタン禁止
	$('input[name="freeWord"]').keydown(function(event) {
		//押されたキーがEnterの場合、Enter禁止
		if(event.key == 'Enter')  return false;
	});

	//都道府県の色変更
	$('select[name^="prefectures-search"]').each(function(){
		//セレクトボックスの都道府県が選択された場合
		if( $(this).val().length  > 0){
			//赤色に変更に終了
			$('.search#prefectures').css('background-color','#f91462');
			return false;
		}else{
			//青色に変更
			$('.search#prefectures').css('background-color','#799dec');
		}
	});
	
	$('select[name^="prefectures-search"]').select2({
		placeholder: "都道府県を選択",
		allowClear: true,
		multiple: true,
	});


});  
</script>