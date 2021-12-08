var uploadFile = {
	methods: {
		uploadFile(){
			const file = this.$refs.preview.files[0];
      		this.url = URL.createObjectURL(file);
			this.show = 2;
			this.is_delete = 0;
		},
		deleteFile(){
			if(this.show == 1){
				this.show = 3;
				this.is_delete = 1;
				return false;
			}
			this.$refs.preview.value = "";
			URL.revokeObjectURL(this.url);
			this.url = '';
			this.show = 1;
			this.is_delete = 0;
		}
	}
}

