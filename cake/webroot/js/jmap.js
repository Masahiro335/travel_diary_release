$(function(){
$.getScript('/js/plugins/jquery.japan-map.min.js', function(){

var areas = [];
var num = 0;

$('body').on('click', '.tab.map', function(){
	++num
	if(num > 1) return false;

	$.ajax({
		url: '/prefectures/user-prefectures',
		type: 'post',
		dataType: 'json',
		data:{
			user_id: $(this).attr('user_id'),
		}
	//成功
	}).done(function(data){
		if( data != undefined ){
			areas = data;
			jmapUser();
		}else{
			alert('データの取得に失敗しました。')
		}
	})
	.fail(function(){
		alert('データの取得に失敗しました。');
	});

});

function jmapUser(){
    $('.jmap-heatlabel').remove();

	$('#jmap').jmap({
		height: "650px",
		backgroundColor: '#6FCFDD',
		prefectureRadius: '5px',
		showHeatmap: true,
		showHeatlabel: true,
        heatmapType: 'HRed',
        heatmapConditions: ["0","1","2","3","4","5","6","7","8",">=9"], 
		onSelect: function(e, data) {
            if(data.option.number == 0){
                return false;
            }

			$.ajax({
				url: '/prefectures/user-select',
				type: 'POST',
				dataType: 'html',
				data:{
					prefecture_id: data.option.code,
					user_id: data.option.user_id,
				},
                timeout: 30000,
			//成功
			}).done(function(html){
                messageList(html, data)
			})
			.fail(function(xhr){
				alert(xhr.responseText);
			});

		},
		areas: areas
    }).show(function(){
        $('.loader-jmap').hide();
        areas = [];
        num = 0;
    }); 
}

if(location.pathname == '/prefectures/index' || location.pathname == '/prefectures' ){
    $.ajax({
		url: '/prefectures/index',
		type: 'post',
		dataType: 'json',
	//成功
	}).done(function(data){
		if( data != undefined ){
			areas = data;
			jmapMap();
		}else{
			alert('データの取得に失敗しました。')
		}
	})
	.fail(function(){
		alert('データの取得に失敗しました。');
	});

    function jmapMap(){
        $('.jmap-heatlabel').remove();

        $('#jmapMap').jmap({
            height: "650px",
            backgroundColor: '#6FCFDD',
            prefectureRadius: '5px',
            showHeatmap: true,
            showHeatlabel: true,
            heatmapType: 'HRed',
			heatmapConditions: ["0","1","2","3","4","5","6","7","8",">=9"], 
            onSelect: function(e, data) {
                if(data.option.number == 0){
                    return false;
                }
        
				var month = $('input[name="month"]').val();
                if( !month ){
					alert('データの取得に失敗しました。');
                    return false;
                }

                $.ajax({
                    url: '/prefectures/select',
                    type: 'POST',
                    dataType: 'html',
                    data:{
                        prefecture_id: data.option.code,
						month: month,
                    },
                    timeout: 30000,
                //成功
                }).done(function(html){
                    messageList(html, data)
                })
                .fail(function(xhr){
                    alert(xhr.responseText);
                });

            },
            areas: areas
        }).show(function(){
            $('.loader-jmap').hide();
            $('.map-select').show();
            $('.btn.success.search-map').show();
            areas = [];
            num = 0;
        }); 
    }
}

$('body').on('click', '.btn.success.search-map', function(){

    $.ajax({
		url: '/prefectures/index',
		type: 'post',
		dataType: 'json',
        data: {
            month: $('input[name="month"]').val(),
            follow_check: $('[name="follow_check"]:checked').val() !== undefined ? '1' : '0'
        }
	//成功
	}).done(function(data){
		if( data != undefined ){
			areas = data;
			jmapMap();
		}else{
			alert('データの取得に失敗しました。')
		}
	})
	.fail(function(){
		alert('データの取得に失敗しました。');
	});

    return false;
});


//投稿一覧を表示
function messageList(html, data){
    var $html = $('<div>'+html+'</div>');
    var $targetHtml = $('.messages-list.map#prefectures-select');
    $targetHtml.find('.messages').remove();
    $targetHtml.find('.select-name').remove();
    $targetHtml.prepend( $html.html() );
    $targetHtml.prepend( '<p class="select-name">'+data.option.name+'　投稿数：'+data.option.number+'</p>' );
    $('.modal.prefectures-select').slideDown('slow');
}

});
});
