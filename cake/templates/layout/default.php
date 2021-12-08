<!DOCTYPE html>
<html>
	<head>
		<title><?= SYSTEM_NAME ?> | 旅行の思い出を投稿しよう</title>
		<meta name="description" content="<?= SYSTEM_NAME ?> | 旅行の思い出を投稿しよう">
		<?= $this->Html->css('https://unpkg.com/swiper/swiper-bundle.min.css') ?>
		<?= $this->Html->script('https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js') ?>
		<?= $this->Html->script('https://cdn.jsdelivr.net/npm/vue/dist/vue.js') ?>
		<?= $this->Html->script('https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js') ?>
		<link href="//cdnjs.cloudflare.com/ajax/libs/select2/4.0.0-rc.2/css/select2.min.css" rel="stylesheet" />
		<script src="//cdnjs.cloudflare.com/ajax/libs/select2/4.0.0-rc.2/js/select2.min.js"></script>
		<script src="https://kit.fontawesome.com/cd97f58519.js" crossorigin="anonymous"></script>
		<?= $this->fetch('meta') ?>
		<?= $this->fetch('css') ?>
		<?= $this->fetch('script') ?>
		<?= $this->Html->css('style') ?>
		<?= $this->Html->script('common.js') ?>
		<link rel="canonical" href="<?= $this->Url->build([], ['fullBase' => true,]) ?>">
		<?php //OGPタグここから ?>
		<meta property="og:title" content="<?= SYSTEM_NAME ?> | 旅行の思い出を投稿しよう" />
		<meta property="og:image" content="<?= SYSTEM_URL . 'img/ogp.png' ?>" />
		<meta property="og:image:width" content="1200" />
		<meta property="og:image:height" content="630" />
		<meta property="og:description" content="旅行の思い出を投稿できるアプリです。 | <?= SYSTEM_NAME ?>" />
		<meta property="og:url" content="<?= $this->Url->build([], ['fullBase' => true,]) ?>" />
		<meta property="og:type" content="website" />
		<meta property="og:site_name" content="<?= SYSTEM_NAME ?>" />
		<meta property="og:locale" content="ja_JP"/>
		<meta name="twitter:card" content="summary" />
		<meta name="twitter:title" content="<?= SYSTEM_NAME ?> | 旅行の思い出を投稿しよう" />
		<meta name="twitter:image" content="<?= SYSTEM_URL . 'img/ogp.png' ?>" />
		<meta name="twitter:description" content="旅行の思い出を投稿できるアプリです。 | <?= SYSTEM_NAME ?>" />
	</head>
	<body>
		<header>
			<?= $this->element('header-menu',['Auth' => empty($Auth) ? '' : $Auth]); ?>
		</header>

		<?php //検索画面 ?>
		<?php if( $search_display == true){ ?>
			<?= $this->element('messages/search-modal',['selectPrefectures' => empty($selectPrefectures) ? '' : $selectPrefectures]); ?>
			<?= $this->element('messages/search',['selectPrefectures' => empty($selectPrefectures) ? '' : $selectPrefectures] ); ?>
		<?php } ?>

		<?php //マップ ?>
		<?php if( $map_display == true){ ?>
			<?= $this->Html->image('map.png',['class' => "btn map", 'url' => ['prefix'=>false, 'controller'=>'Prefectures', 'action'=>'index'] ]); ?>
		<?php } ?>

		<?php //新規投稿 ?>
		<?php if( $add_display == true){ ?>
			<?php if( empty($Auth) ){ ?>
				<a href="/signin" onclick="return confirm('投稿にはログインまたは会員登録が必要です。')" class="btn add-not">投稿</a>
			<?php }else{ ?>
				<div class="btn add">投稿</div> 
				<?= $this->element('messages/edit',['message' => null, 'message_id' => null]) ?>
				<?= $this->Html->script('message_ajax.js') ?>
			<?php } ?>
		<?php } ?>
	
		<?php //サイドメニュー ?>
		<?php if( $this->request->getParam('prefix') == 'Mypage'){ ?>
			<?= $this->element('sidenavi',['nav' => $nav]) ?>
		<?php } ?>

		<?= $this->Flash->render() ?>
		<?= $this->fetch('content') ?>

		<?php //モバイルフッター画面 ?>
		<?= $this->element('mobile-footer',[
			'search_display' => $search_display,
			'map_display' => $map_display,
			'add_display' => $add_display,
		]); ?>

		<footer>
			<p><?= SYSTEM_NAME ?></p>
			旅行の思い出を投稿しよう！
		</footer>
		<?= $this->Html->script('https://unpkg.com/swiper/swiper-bundle.min.js') ?>
	</body>　
</html>
