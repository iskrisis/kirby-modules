<div class="module images">
	<?php foreach($page->images()->sortBy('sort', 'asc') as $i): ?>
		<?= $i->crop(800, 600) ?>
	<?php endforeach ?>
</div>