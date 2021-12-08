<?= $this->Html->script('jmap.js') ?>

<div class="map-index">
	<div id="jmapMap"></div>
	<img class="loader-jmap" src="<?= $this->Url->image('preloader.svg') ?>" style="height:80px; width:80px; margin:150px;">
	<div class="map-select">
		<input type="month" name="month" value="<?= date('Y-m') ?>" style="margin-left:10px;">
		<?php if(empty($Auth) == false){ ?>
			<label class="form-label">
				<?= $this->Form->input('follow_check',['type' => 'checkbox','label' => false]) ?>フォローユーザーのみ
			</label>
		<?php } ?>
		<?= $this->Form->button('検索',['class' => 'btn success search-map', 'style' => 'background-color:#f44336;']);?>
	</div>
	<div class="messages-list map" id="prefectures-select">
	</div>
</div>