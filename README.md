# Bootstrap-4 plugin for CakePHP-3

This plugin helps render HTML elements so they are styled correctly for Bootstrap-4.

## Installation

- Merge the following to your `require` section of composer.json, replacing {{version}} with any repo tags (eg `v1.0`, `v1.1`) or `dev-master` if you want the bleeding edge

```
  "require": {
    "lilhermit/cakephp-plugin-bootstrap4": "{{version}}"
  }
```

- Merge the following to your `repositories` section of composer.json add if you don't have one

```
  "repositories": [
    {
      "type": "vcs",
      "url": "https://bitbucket.org/lilHermit/cakephp-plugins-bootstrap4.git"
    }
  ]
```

- Perform a composer update

- Load the plugin in your `bootstrap.php`

```
Plugin::load('lilHermit/Bootstrap4', ['bootstrap' => true]);
```

- Load the Helpers (see below of info on each) you want to use in your `src/View/AppView.php` file

```
    public function initialize() {
        $this->loadHelper('lilHermit/Bootstrap4.Form');
        $this->loadHelper('lilHermit/Bootstrap4.Html');
        $this->loadHelper('lilHermit/Bootstrap4.Paginator');
    }
```

- Load the Flash Component (see below of info on each) in your `src/Controller/AppController.php`

```
    public function initialize() {
        parent::initialize();

        $this->loadComponent('lilHermit/Bootstrap4.Flash');
    }
```

## Helpers

### FormHelper

### HtmlHelper

### PaginatorHelper

## Components

### FlashComponent