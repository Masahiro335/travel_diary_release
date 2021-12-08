<?php
/**
 * ローカル環境用 定数設定ファイル
 *
 * 開発者個人の開発環境に合わせて内容を変更したい場合は、
 * Dockerから 環境変数 **OPERATING_ENV** の内容を変更し、
 * 対応する名称のpathsファイルを作成すること。
 * その際はこのファイルをコピーして作成すると楽なので、ご利用ください。
 *
 * @author matsubara
 */

 // 共通設定を読み込み
require_once __DIR__ . '/paths.php';

//【ローカル環境】ドメイン
define('SYSTEM_DOMAIN', 'localhost');

//【ローカル環境】システムURL
define('SYSTEM_URL', 'http://'.SYSTEM_DOMAIN.'/');

//メールの送信元アドレス
define('MAIL_FROM_ADDRESS', '設定メール');

// ■■■■　ローカル環境: ソーシャル連携の設定　■■■■
define('OAUTH_KEYS', [
	'GOOGLE'=>[
		'key'=>'設定key',
		'secret'=>'設定secret',
	],
	'TWITTER'=>[
		'key'=>'設定key',
		'secret'=>'設定secret',
	],
	'LINE'=>[
		'key'=>'設定key',
		'secret'=>'設定secret',
	],
] );

// ■■■■　ローカル環境: S3の設定　■■■■
define('AWS_S3_BUCKETNAME', '設定バケットネーム');
define('AWS_S3_USERNAME', '設定ネーム');
define('AWS_S3_REGION', '設定REGION');
define('AWS_S3_ACCESS_TOKEN', '設定トークン');
define('AWS_S3_ACCESS_SECRET', '設定パスワード');
//【ローカル環境】S3のオブジェクトURL
define('AWS_S3_ENDPOINT', '設定S3のオブジェクトURL');
