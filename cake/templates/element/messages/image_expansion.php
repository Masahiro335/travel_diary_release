<?php //画像拡大 ?>
<div class="modal expansion">
	<div class="modalchild expansion">
		<div class="swiper-container">
			<div class="swiper-wrapper">
				<?php foreach ($message->message_images as $message_image){ ?>
					<div class="swiper-slide">
						<img 
							src="<?= $message_image->image ?>"
							class="img-expansion"
						>
					</div>
				<?php } ?>
			</div>
			<div class="swiper-pagination"></div>
			<div class="swiper-button-prev"></div>
			<div class="swiper-button-next"></div>
		</div>
	</div>
</div>