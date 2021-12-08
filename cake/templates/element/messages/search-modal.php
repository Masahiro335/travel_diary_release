<article>
	<div class="modal search-modal">
		<div class="modalchild search-modalchild">
			<?= $this->Form->create(null, ['url' => ['controller' => 'Messages', 'action' => 'index']]) ?>
				<div class="search-modal-title">検索</div>
				<div class="search-modal">
					<div class="search-item">
						<div class="search-name">SORT</div>
						<?= $this->Form->select('order',['最新順','古い順','いいねが多い順','コメントが多い順'],['default' => '0']) ?>
					</div>
					<div class="search-item">
						<div class="search-name">日付</div>
						<?= $this->Form->date('begin') ?>
						<?= $this->Form->label('から') ?></br>
						<?= $this->Form->date('end') ?>
						<?= $this->Form->label('まで') ?></br>
					</div>
					<div class="search-item">
						<div class="search-name">フリーワード</div>
						<?= $this->Form->input('freeWord',['type'=>'text', 'placeholder' => '検索']) ?>
					</div>
					<div class="search-item">
						<div class="search-name">旅行先</div>
						<?= $this->Form->control('prefectures',[
							'type'=>'select', 
							'multiple'=> true,
							'empty' => true,
							'name'=> 'prefectures-search', 
							'label' => false
						]) ?>
					</div>
					<?php if(empty($Auth) == false){ ?>
						<div class="search-item">
							<div class="search-follow">
								<label class="form-label">
									<?= $this->Form->input('follow_check',['type' => 'checkbox','label' => false]) ?>フォローユーザーのみ
								</label>
							</div>
						</div>
					<?php } ?>
					<?= $this->Form->button('検索',['class' => 'btn btn-search-modal']);?>
					<?= $this->Form->end() ?>
				</div>
		</div>
	</div>
</article>

<script>
$('select[name^="prefectures-search"]').select2({
	placeholder: "都道府県を選択",
	allowClear: true,
	multiple: true,
});
 
</script>