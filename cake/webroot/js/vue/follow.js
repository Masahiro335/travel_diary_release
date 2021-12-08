var followBtn = {	
	methods: {
		open: function ( type ) {
			this.type = type;
		},
		follow: function ( event) {
			var is_Auth = event.currentTarget.getAttribute('is_Auth');
			if(is_Auth == false){
				alert('ログインしてください。')
				return false;
			}			
			var follow_user_id = event.currentTarget.getAttribute('follow-user-id');
			if(this.is_follow[follow_user_id] == null) this.is_follow[follow_user_id] = event.currentTarget.getAttribute('is_follow');

			if(this.is_follow[follow_user_id] == false){
				axios
				.post('/follow-users/add', {
					follow_user_id: follow_user_id,
				})
				.then((response) => {
					this.is_follow[follow_user_id] = true;
					let $tagets = document.querySelectorAll('[follow-user-id="'+follow_user_id+'"]');
					for (let i = 0; i < $tagets.length; i++) {
						$tagets[i].setAttribute('class','btn follow'); 
					}

				})
				.catch(error => {
					alert(error.response.data);
				})
			}else{
				axios
				.post('/follow-users/delete', {
					follow_user_id: follow_user_id,
				})
				.then((response) => {
					this.is_follow[follow_user_id] = false;
					let $tagets = document.querySelectorAll('[follow-user-id="'+follow_user_id+'"]');
					for (let i = 0; i < $tagets.length; i++) {
						$tagets[i].setAttribute('class','btn follow not'); 
					}
				})
				.catch(error => {
					alert(error.response.data);
				})
			}
		}
	}
}
