$(function(){

	//確認・確定時のエラー表示
	function postError($form, errors){
		alert('入力エラーがありました');
		$form.find('.error-message').remove();
		$.each(errors, function(index, value){
			//メッセージの位置
			var $target = $form.find('.'+index+'-class');
			//バリデーションの数だけメッセージを表示
			$.each(value, function(index, message){
				if($target.closest('.after-text').length > 0){
					//バリデーションのメッセージを追加
					$target.closest('.after-text').after('<p class="error-message">'+message+'</p>');
				}else{
					//バリデーションのメッセージを表示
					$target.after('<p class="error-message">'+message+'</p>');
				}
			})
		})
	}

	//投稿追加
	$('body').on('click', '.btn.save', function(){

		if( confirm('投稿を保存しますか？') ){
			var $hide = $('.btn-class, fieldset');
			$this = $(this);
			var message_id = $this.attr('message_id');
			var $form = message_id == null ? $this.closest('article').find('#saveForm') : $this.closest('article').find('#saveForm'+message_id);
			//削除する画像のsortを入力
			var delete_image_ids;
			var image_count = 0;
			$this.closest('.btn-class').prev().find('.image').each(function(index, element){
				if($(this).css('display') == 'none'){
					if(!delete_image_ids){
						delete_image_ids = $(this).attr('data-id');
						return true;
					}
					delete_image_ids += ','+$(this).attr('data-id');
					return true;
				}else{
					image_count++;
				}
			});
			$('input[name="delete_image_ids"]').val(delete_image_ids);
			$('input[name="image_count"]').val(image_count);

			var userPage = $('input[name="userPage"]').val();

			//非表示にしてローダーを表示
			$hide.hide();
			$('.loader-save').fadeIn();

			//Ajax実行
			$.ajax({
				url: $this.attr('href'),
				type: 'POST',
				enctype: 'multipart/form-data',
				data: new FormData( $form.get(0) ),
				processData: false, //他の形式でデータを送るために自動変換をfalse
				contentType: false, //サーバにデータを送信する際に用いるcontent-typeヘッダの値
				cache: false,
			//成功
			}).done(function(data, status, jqxhr){

				$form.find('.error-message').remove();

				//追加する要素をダミーの親要素でラップして jquery オブジェクト化
				var $html = $('<div>'+data+'</div>');
	
				if( jqxhr.responseJSON != undefined && data.errors != undefined ){
					postError( $form, data.errors );
				}else if(userPage != null && !message_id){
					alert('投稿を追加しました');
					//モーダルを閉じる
					$('.modal.save-modal#save').fadeOut(function(){
						document.add.reset();
						//要素を追加
						$html.hide();
						$('.messages#messages-list').prepend( $html.html() );
						$('.messages#messages-list').children(':hidden').find('.message').slideDown('slow');
						//新規の場合、投稿数+1する
						$('#MessageCount').text( parseInt( $('#MessageCount').text() ) + 1 );
					});
				}else{
					//新規追加の場合
					if(!message_id){
						alert('投稿を追加しました');
						//モーダルを閉じる
						$('.modal.save-modal#save').fadeOut(function(){
							//フォームの値をリセット
							document.add.reset();
							//投稿数が0の場合、.message-noneクラスを非表示
							$('.message-none').hide();
							//一旦非表示
							$html.hide();
							//要素を追加
							$('.messages').prepend( $html.html() );
							$('.messages').children(':hidden').find('.message').slideDown('slow');
							//新規の場合、投稿数+1する
							$('#MessageCount').text( parseInt( $('#MessageCount').text() ) + 1 );
						});
					}else{
						//編集の場合
						alert('投稿を編集しました');
						//モーダルを閉じる
						$this.closest('.modal.save-modal#save'+message_id).fadeOut(function(){
							//編集の場合、編集する投稿を変更
							$this.closest('.message#message'+message_id).parent().html($html.html());
						});
					}
				}

			})
			//失敗
			.fail(function(xhr){
				alert(xhr.responseText);  
			})
			//成功失敗かかわらず
			.always(function(){
				$('.loader-save').fadeOut(function(){
					$hide.show();
				});
			});
		}

		return false;
	});


	//投稿削除
	$('body').on('click', '.delete', function(){

		if( confirm('この投稿を削除しますか？') ) {
			//ボタンを非表示
			var $btn = $('.btn-class');
			$btn.hide();

			$this = $(this);

			// Ajax 実行
			$.ajax({
				url: $this.attr('href'),
				type: 'get',
				dataType: 'json',
			//成功
			}).done(function(id){
				alert('投稿の削除をしました');
				//投稿数-1する
				$('#MessageCount').text( parseInt( $('#MessageCount').text() ) - 1 );
				//投稿　コメント非表示
				var $parent = $this.closest('article');
				var $comennts = $('.comments#comments_'+id);
				$parent.slideUp();
				$comennts.slideUp();
			})
			.fail(function(xhr){
				alert(xhr.responseText);
			});
			$btn.show();
		}

		return false;
	});

	/*投稿の追加と編集*/
	$('body').on('click', '.btn.add, .btn.edit, .fas.fa-plus-square', function(){
		var message_id = $(this).attr('message_id');
		if(message_id){
			$('.modal.save-modal#save'+message_id).fadeIn();
		}else{
			$('.modal.save-modal#save').fadeIn();
		} 
		//画像の表示
		$('.image').show();
		//都道府県
		$('select[name^="prefectures"]').select2({
			width: "200px",
			placeholder: "都道府県を選択",
			allowClear: true,
		});
		return false;
	});

	/*投稿画面を閉じる*/
	$('body').on('click', '.save-close', function(){
		var message_id = $(this).attr('message_id');
		if(message_id){
			$('.modal.save-modal#save'+message_id).fadeOut();
		}else{  
			$('.modal.save-modal#save').fadeOut();
		}
		return false;	
	});

	//いいね
	$('body').on('click', '.fa-heart.not-good', function(){
		var $this = $(this);
		$this.css('pointer-events', 'none');
		//Ajax実行
		$.ajax({
			type: 'GET',
			url: '/goods/add?message_id='+$this.attr('message_id'),
		}).done(function(){
			$this.removeClass('not-good');
			$this.addClass('good');
			$this.next().text( parseInt( $this.next().text() ) + 1 );
		})
		.fail(function(){
			alert('いいねに失敗しました');
		})
		.always(function(){
			$this.css('pointer-events', 'auto');
		});
		return false;
	});

	//いいねキャンセル
	$('body').on('click', '.fa-heart.good', function(){
		var $this = $(this);
		$this.css('pointer-events', 'none');
		//Ajax実行
		$.ajax({
			type: 'GET',
			url: '/goods/delete?message_id='+$this.attr('message_id'),
		}).done(function(){
			$this.removeClass('good');
			$this.addClass('not-good');
			$this.next().text( parseInt( $this.next().text() ) - 1 );
		})
		.fail(function(){
			alert('いいねキャンセルに失敗しました');
		})
		.always(function(){
			$this.css('pointer-events', 'auto');
		});
		return false;
	});

	//コメントを追加
	$('body').on('click', '.btn.btn-comment', function(){
		var $this = $(this);
		if(!$this.prevAll('form').find('textarea#comment').val()){
			return false;
		}
		$this.hide();
		$('.loader-comment').fadeIn();

		var message_id = $this.attr('message_id');
		//Ajax実行
		$.ajax({
			type: 'POST',
			url: '/comments/edit',
			dataType: 'html',
			data: {
				comment: $this.prevAll('form').find('textarea#comment').val(),
				message_id: message_id,
			}
		}).done(function(html){
			$this.prevAll('form').find('textarea#comment').val('');
			$html = $('<div>'+html+'</div>');
			$html.children().hide();
			var comment_boxs = $this.closest('.comment-post').prevAll('.comment-boxs#comment-boxs-'+message_id);
			comment_boxs.prepend( $html.html() );
			comment_boxs.children(':hidden').slideDown('slow');
			var $message_comments_count = $this.closest('.comments')
				.prev()
				.find('.message-comments-count#comments-count-'+message_id);
			$message_comments_count.text(
				parseInt($message_comments_count.text()) + 1 
			);
		})
		.fail(function(){
			alert('コメントに失敗しました');
		})
		.always(function(){
			$('.loader-comment').fadeOut(function(){
				$this.show();
			});
		});
		return false;
	});


	//コメントを削除
	$('body').on('click', '.fa-window-close', function(){
		var $this = $(this);

		//Ajax実行
		$.ajax({
			type: 'GET',
			url: '/comments/delete/'+$this.attr('comment-id'),
		}).done(function(){
			alert('コメントを削除しました');
			$this.hide();
			$this.next().text('コメントが削除されました。');
		})
		.fail(function(){
			alert('コメントの削除に失敗しました');
		})
		.always(function(){
		});
		return false;
	});

});
