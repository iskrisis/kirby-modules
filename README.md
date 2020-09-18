# Kirby Modules

![module](https://user-images.githubusercontent.com/7975568/69164144-ba765480-0aef-11ea-8b4e-b586066c3cbf.gif)

## Introduction to modules

### What is a module?

A module is a regular page, differentiated from other pages by being inside a modules container.
This approach makes it possible to use pages as modules without sacrificing regular subpages.

```
📄 Page
  📄 Subpage A
  📄 Subpage B
  🗂 Modules
    📄 Module A
    📄 Module B
```

## Instructions

Add a `modules` section to any page blueprint and a modules container will be automatically created.
 
You can create modules by putting them in a `site/modules` folder. For example you can add a `site/modules/text` folder with the template `text.php` and the blueprint `text.yml`.

In the parent page template you can then use `<?php $page->renderModules() ?>` to render the modules.

## Options

By default, the `module.text` blueprint will be the first option when adding a module. You can set it to another blueprint in your `site/config/config.php`:

```php
return [
  'medienbaecker.modules' => [
      'default' => 'module.text'
  ]
];
```
