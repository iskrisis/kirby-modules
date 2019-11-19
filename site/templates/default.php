<?php snippet('head') ?>

<?php if($modules = $page->find("modules")): ?>
	<?php foreach($modules->children()->listed() as $module): ?>
		<?php snippet('modules/' . $module->intendedTemplate(), ['page' => $module]) ?>
	<?php endforeach ?>
<?php endif ?>

<?php snippet('bottom') ?>