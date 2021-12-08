$(function(){

	/*フラッシュメッセージを非表示*/
	$('body').on('click','.message.success.hidden, .message.error.hidden', function(){
		$('.message.success.hidden, .message.error.hidden').hide();
	});

	/*モーダルを閉じる*/
	$('body').on('click', '.modal', function(){
		$('.modal').hide();
	});
	//モーダルが閉じない様にstop
	$('body').on('click', '.modalchild', function( e ){
		e.stopPropagation();
	});

	//スクロールボタン
	$('body').on('click','.scroll-btn', function(){
		var position = 0;
		var speed = 600;
		$("html,body").animate({scrollTop:position},speed);
		return false;
	});

	/*画像拡大*/
	$('body').on('click', '.img.display', function(){
		$(this).parents('.images').nextAll('.modal.expansion').fadeIn();
		var sort = $(this).attr('sort');

		//Swiper.jsを起動
		var mySwiper = new Swiper ('.swiper-container', {
			initialSlide: sort - 1,	 //クリックした画像を表示
			loop: true,
			slidesPerView: 'auto',
			navigation: {
				nextEl: '.swiper-button-next',
				prevEl: '.swiper-button-prev',
			},
			pagination: {
				el: '.swiper-pagination',
				clickable: true,
				type: 'bullets',
			},
		});	
		return false;
	});

	//画像削除
	$('body').on('click', '.image-delete', function(){
		//画像を非表示
		$(this).parent().hide();
		return false;
	});

	//ページング
	$('body').on('click', '.pagination a', function(){
		//ボタンを非表示
		var $hide = $('.paginator');
		$hide.hide();

		// Ajax 実行
		$.ajax( $(this).href,{
			url: $(this).attr('href'),
			type: 'get',
			dataType: 'html',
		//成功
		}).done(function(html){
			//追加する要素をダミーの親要素でラップして jquery オブジェクト化
			$html = $('<div>'+html+'</div>');
			//視覚効果のため一旦非表示にする
			$html.children().hide();
			//メッセージクラスを取得
			$messageshtml = $html.find('.messages-list');
			//要素を変更
			$('.messages-list').html( $messageshtml.html() ).hide();
			//視覚効果を付けて表示
			$('.messages-list').fadeIn(600);
		})
		//失敗
		.fail(function(){
			alert('ページングを失敗しました');
		})
		.always(function(){
			//表示
			$hide.show();
		});

		return false;
	});

	$('select[name="prefecture_id"]').select2({
		width: "130px",
		hight: "40px",
		placeholder: "出身地を選択して下さい。",
		allowClear: true
	});

	//コメント表示・非表示
	$('body').on('click', '.fa-comment-alt', function(){
		var $comments = $(this).closest('article').next();
		if($comments.css('display') == 'none'){
			$comments.slideDown();
		}else{
			$comments.slideUp();
		}
		return false;
	});

	//コメント非表示
	$('body').on('click', '.fa-chevron-up', function(){
		var message_id = $(this).attr('message_id');
		$('.comments#comments_'+message_id).slideUp();
		return false;
	});

	//シェアモーダル
	$('body').on('click', '.fa-share-square', function(){
		var message_id = $(this).attr('message_id');
		$('.modal.shere#shere'+message_id).fadeIn();
	});

});
