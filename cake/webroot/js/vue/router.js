var router = new VueRouter({
    mode: 'history',
    routes: [],
});

var clickColor = {
	//URLにハッシュタグが付いてた場合、指定した投稿に色をつける
    mounted: function() {
        var hash = this.$route.hash
		var message_id = hash.replace('#message','');
		this.isClick = message_id;
    },
	//投稿の日付にクリックした場合、指定した投稿に色をつける
	methods: {
		doClick: function(message_id){
			this.isClick = message_id;
		}
	},
}

