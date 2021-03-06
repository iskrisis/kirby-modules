<?php

use Kirby\Cms\Section;

// Get all blueprints that start with "module.":
$blueprints = [];
foreach (kirby()->blueprints() as $blueprint) {
	if(Str::startsWith($blueprint, 'module.')) $blueprints[] = $blueprint;
}

// Move default module to first position:
if($default = array_search(option('medienbaecker.modules.default', 'module.text'), $blueprints)) {
	$module_text = $blueprints[$default];
	unset($blueprints[$default]);
	array_unshift($blueprints, $module_text);
}

// Create a model for each of the module blueprints
class ModulePage extends Page {
	public function url($options = null): string {
		return $this->parents()->filterBy('intendedTemplate', '!=', 'modules')->first()->url() . '#' . $this->slug();
	}
}
$models = [];
foreach ($blueprints as $blueprint) {
	$models[$blueprint] = 'ModulePage';
}
$models['modules'] = 'ModulePage';

// Create a template for each of the blueprints
$templates = [];
foreach ($blueprints as $blueprint) {
	$templates[$blueprint] = __DIR__ . '/module.php';
}
$templates["modules"] = __DIR__ . '/module.php';

Kirby::plugin('medienbaecker/modules', [
	'options' => [
		'default' => 'module.text'
	],
	'sections' => [
		'modules' => array_replace_recursive(Section::$types['pages'], [
			'props' => [
				'create' => $blueprints,
				'info' => function(string $info = '{{ page.moduleName }}') {
					return $info;
				},
				'image' => false,
				'parent' => function($parent = null) {
					if($parent != null) {
						return $parent;
					}
					if($this->model()->find('modules')) {
						return 'page.find("modules")';
					}
					return null;
				}
			]
		])
	],
	'fields' => [
		'modules_redirect' => [
			'computed' => [
				'redirect' => function () {
					return $this->model()->parent()->panelUrl();
				}
			]
		]
	],
	'hooks' => [
		'route:after' => function ($route, $path, $method) {
			$uid = explode('/', $path);
			$uid = end($uid);
			$uid = str_replace('+', '/', $uid);
			$page = kirby()->page($uid);
			if ($page) {
				if(!$page->find('modules') AND $page->intendedTemplate() != 'modules') {
					if($page->blueprint()->section('modules')) {
						kirby()->impersonate('kirby');
						try {
							$modulesPage = $page->createChild([
								'content'  => ['title' => 'Modules'],
								'slug'     => 'modules',
								'template' => 'modules'
							]);
						}
						catch (Exception $error) {
							throw new Exception($error);
						}
						if($modulesPage) {
							$modulesPage->publish();
						}
					}
				}
			}
		},
	],
	'templates' => $templates,
	'pageModels' => $models,
	'blueprints' => [
		'module/changeTemplate' => [
			'changeTemplate' => $blueprints
		],
		'pages/modules' => [
			'title' => 'Modules',
			'fields' => [
				'modules_redirect' => true
			]
		]
	],
	'pageMethods' => [
		'isModule' => function () {
			return Str::startsWith($this->intendedTemplate(), 'module.');
		},
		'moduleName' => function () {
			return $this->blueprint()->title();
		},
		'moduleId' => function () {
			return str_replace('.', '__', $this->intendedTemplate());
		},
	]
]);
