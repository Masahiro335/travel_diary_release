<?php
/**
 * Routes configuration.
 *
 * In this file, you set up routes to your controllers and their actions.
 * Routes are very important mechanism that allows you to freely connect
 * different URLs to chosen controllers and their actions (functions).
 *
 * It's loaded within the context of `Application::routes()` method which
 * receives a `RouteBuilder` instance `$routes` as method argument.
 *
 * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link          https://cakephp.org CakePHP(tm) Project
 * @license       https://opensource.org/licenses/mit-license.php MIT License
 */

use Cake\Http\Middleware\CsrfProtectionMiddleware;
use Cake\Routing\Route\DashedRoute;
use Cake\Routing\Router;
use Cake\Routing\RouteBuilder;

$routes->setRouteClass(DashedRoute::class);

$routes->scope('/', function (RouteBuilder $builder) {

	$builder->connect('/pages/*', ['controller' => 'Pages', 'action' => 'display']);
	//$builder->connect('/', ['controller' => 'Pages', 'action' => 'display', 'home']);

	$builder->connect('/', ['controller' => 'Messages', 'action' => 'index']);
	$builder->connect('/messages/edit/:id', ['controller' => 'Messages', 'action' => 'edit'])->setPatterns(['id' => '\d+'])->setPass(['id']);
	$builder->connect('/users/view/:id', ['controller' => 'Users', 'action' => 'view'])->setPatterns(['id' => '\d+'])->setPass(['id']);
	$builder->connect('/goods/add', ['controller' => 'Goods', 'action' => 'add']);
	$builder->connect('/goods/delete', ['controller' => 'Goods', 'action' => 'delete']);
	$builder->connect('/comments/edit/:id', ['controller' => 'Comments', 'action' => 'edit'])->setPatterns(['id' => '\d+'])->setPass(['id']);
	$builder->connect('/comments/delete/:id', ['controller' => 'Comments', 'action' => 'delete'])->setPatterns(['id' => '\d+'])->setPass(['id']);
	$builder->connect('/follow-users/add', ['controller' => 'FollowUsers', 'action' => 'add']);
	$builder->connect('/follow-users/delete/', ['controller' => 'FollowUsers', 'action' => 'delete']);
	$builder->connect('/prefectures/user-prefectures', ['controller' => 'Prefectures', 'action' => 'userPrefectures']);
	$builder->connect('/prefectures/user-select', ['controller' => 'Prefectures', 'action' => 'userSelect']);
	$builder->connect('/prefectures/index', ['controller' => 'Prefectures', 'action' => 'index']);

	//ログイン処理
	$builder->connect('/signin', ['controller' => 'Login', 'action' => 'signin']);
	$builder->connect('/signin/:id', ['controller' => 'Login', 'action' => 'signin'])->setPatterns(['id' => '\d+'])->setPass(['id']);
	$builder->connect('/signup', ['controller' => 'Login', 'action' => 'signup']);
	$builder->connect('/signout', ['controller' => 'Login', 'action' => 'signout']);
	$builder->connect('/registration', ['controller' => 'Login', 'action' => 'registration']);
	$builder->connect('/registration/token', ['controller' => 'Login', 'action' => 'registrationToken']);
	$builder->connect('/email-reset', ['controller' => 'Login', 'action' => 'emailReset']);
	$builder->connect('/password-reset', ['controller' => 'Login', 'action' => 'passwordReset']);
	$builder->connect('/password-reset-code', ['controller' => 'Login', 'action' => 'passwordResetCode']);
	$builder->connect('/password-reset-complete', ['controller' => 'Login', 'action' => 'passwordResetComplete']);

	$builder->fallbacks();
});

//マイページ
Router::prefix('mypage', function (RouteBuilder $routes) {
	$routes->connect('/users/profile/:id', ['controller' => 'Users', 'action' => 'profile'])->setPatterns(['id' => '\d+'])->setPass(['id']);
	$routes->connect('/users/setting', ['controller' => 'Users', 'action' => 'setting']);
	$routes->connect('/users/email-edit', ['controller' => 'Users', 'action' => 'emailEdit']);
	$routes->connect('/users/withdrawal', ['controller' => 'Users', 'action' => 'withdrawal']);
	$routes->connect('/users/password-edit', ['controller' => 'Users', 'action' => 'passwordEdit']);

	$routes->fallbacks(DashedRoute::class);
});

//管理画面
Router::prefix('admin', function ($routes) {
	$routes->connect('/', ['controller' => 'messages', 'action' => 'index']);
	$routes->fallbacks(DashedRoute::class);
});

/*
* If you need a different set of middleware or none at all,
* open new scope and define routes there.
*
* ```
* $routes->scope('/api', function (RouteBuilder $builder) {
*     // No $builder->applyMiddleware() here.
*     // Connect API actions here.
* });
* ```
*/
