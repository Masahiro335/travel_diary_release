
<div 
	class="<?= empty($user->AuthfollowUser( $Auth )) ? 'btn follow not' : 'btn follow' ?>" 
	id="btn-follow-<?= $user->id ?>" 
	v-on:click="follow( $event )" 
	follow-user-id="<?= $user->id ?>" 
	is_follow="<?= empty($user->AuthfollowUser( $Auth )) ? false : true ?>" 
	is_Auth="<?= empty($Auth) ? false : true ?>" 
>
	フォロー
</div>
