<?php
declare(strict_types=1);

/**
 * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link      https://cakephp.org CakePHP(tm) Project
 * @since     3.0.0
 * @license   https://opensource.org/licenses/mit-license.php MIT License
 */
namespace App\View;

use Cake\View\View;

/**
 * Application View
 *
 * Your application's default view class
 *
 * @link https://book.cakephp.org/4/en/views.html#the-app-view
 */
class AppView extends View
{
    /**
     * Initialization hook method.
     *
     * Use this method to add common initialization code like loading helpers.
     *
     * e.g. `$this->loadHelper('Html');`
     *
     * @return void
     */
    public function initialize(): void
    {
    }

    //TwitterシェアリンクのURLを生成する
	public function twitterShareUrl($url, $userName = null, $is_message = false){
		$url = 'https://twitter.com/intent/tweet?url=' . urlencode($url).'%0D%0A';
		$url .= '&text=トラベルダイアリーへようこそ！'.$userName.($is_message == true ? 'さんの投稿です。' : 'さんのプロフィールページです。').'%0D%0A';
		$url .= '&hashtags=トラベルダイアリー';
		return $url;
	}

	//LINEシェアリンクのURLを生成する
	public function lineShareUrl($link, $userName = null, $is_message = false){
        $text = 'トラベルダイアリーへようこそ！'.$userName.($is_message == true ? 'さんの投稿です。' : 'さんのプロフィールページです。');
		$url = "https://social-plugins.line.me/lineit/share?url=" . urlencode($link). '&text='.$text ;
		return $url;
	}
}
