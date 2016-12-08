# Bootstrap-4 plugin for CakePHP-3

This plugin helps render HTML elements so they are styled correctly for Bootstrap-4.

## Installation

- Add this Bitbucket repository with the following:

```
composer config repositories.lilhermit-cakephp-plugins-bootstrap4 vcs https://bitbucket.org/lilHermit/cakephp-plugins-bootstrap4.git
```

- Add the plugin with the following command, replacing `1.*` with `dev-master` if you want the bleeding edge:

```
composer require lilhermit/cakephp-plugin-bootstrap4:1.*
```

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

- Copy or symlink `form-manipulation.js` into your `webroot/js/BootStrap4`, it is advisable to use link as this should survive a `composer update`

**Copy**
```
mkdir -p webroot/js/Bootstrap4
cp vendor/lilHermit/cakephp-plugin-bootstrap4/webroot/js/form-manipulation.js webroot/js/Bootstrap4/
```

**Symlink**
```
mkdir -p webroot/js/Bootstrap4
( cd webroot/js/Bootstrap4/; ln -s ../../../vendor/lilHermit/cakephp-plugin-bootstrap4/webroot/js/form-manipulation.js . )
```

- Include the `form-manipulation.js` in your layout file `<?= $this->Html->script('Bootstrap4/form-manipulation.js'); ?>`

**NOTE**
`form-manipulation.js` requires jquery

## Helpers & Components

With the various Helpers & Components you can style various html elements see what below. If any have been missed please feel free to create an issue on bitbucket and I'll endeavour to add them.

**Styles**

    - Form Elements, including submit button (FormHelper + JS)
    - Crumblist (HtmlHelper)
    - Pagination (PaginationHelper)
    - Flash messages `error`, `info`, `warning` & `success` are styled as dismissable alerts (FlashComponent)

## FlashComponent

The standard `default`, `error` and `success` flash messages are styled to Bootstrap Alerts. I have also implemented `warning` and `info` (`default` will render `info`).

Use the Flash component as you normally would:

```
$this->Flash->set('This is an alert, which will render as info');
$this->Flash->info('This is an info alert');
$this->Flash->error('Something bad happened');
$this->Flash->success('Everything worked as expected');
$this->Flash->warning('Warning: Might not have saved!');
```
would render as

![alt text][alert-dismiss]

You can drop the dismiss icon by passing `noDismiss` as `true`

```
$this->Flash->set('This is an alert, which will render as info', [ 'params' => [  'noDismiss' => true]]);
$this->Flash->info('This is an info alert', [ 'params' => [  'noDismiss' => true]]);
$this->Flash->error('Something bad happened', [ 'params' => [  'noDismiss' => true]]);
$this->Flash->success('Everything worked as expected', [ 'params' => [  'noDismiss' => true]]);
$this->Flash->warning('Warning: Might not have saved!', [ 'params' => [  'noDismiss' => true]]);
```

would render as

![alt text][alert-not-dismiss]

[alert-dismiss]: docs/img/alerts-dismissable.png "Dismissable Alerts"
[alert-not-dismiss]: docs/img/alerts-not-dismissable.png "Non dismissable Alerts"

## FormHelper

### Additional input options

Additional `$options` are now supported by FormHelper::input

```
$options = [
    // This text appears when a text box is empty
    'placeholder' => 'Currency',

    // This text appears below the control
    'help' => 'Enter the value you wish to donate',

    // This text appears within the control at the start
    'prefix' => '£',

    // This text appears within the control at the end
    'suffix' => '.00',
];

echo $this->Form->input('Donation', $options);
```

please see example below.

![alt text](docs/img/input-example1.png "Input example")

### Datetime elements

This plugin now renders using HTML5 date functionality but you can go to CakePHP defaults of multiple dropdowns using the following `$option`

```
echo $this->Form->input('Date', [ 'html5Render' => false ]);
```

### Custom controls (checkboxes and radios)

By default checkboxes and radios are rendered using Bootstrap4 Custom Controls. To disable this either do at Form creation time or per input

```
echo $this->Form->create($registerUserForm, ['customControls' => false]);

```
or
```
echo $this->Form->input('terms_agreed', [
  'label' => 'I agree to the terms of use',
  'customControls' => false
]);
```
You can create checkboxes via the normal input method (if the type is boolean) or by forcing type
```
echo $this->Form->input('communications_opt_in', [
  'label' => 'Please send me promotional emails',
]);
echo $this->Form->input('terms_agreed', [
  'label' => 'I agree to the terms of use',
  'type' => 'checkbox'
]);

```
You can create multiple checkboxes or radios via the `select` method (This creates the full label and container code)
```
echo $this->Form->input('checkbox1', [
  'label' => 'My checkboxes',
  'default' => 2,
  'multiple' => 'checkbox',
  'type' => 'select',
  'options' => [
    ['text' => 'First Checkbox', 'value' => 1],
    ['text' => 'Second Checkbox', 'value' => 2]
  ]
]);
```
Or via the `multiCheckbox` or `radio` methods which just creates the checkboxes/radios so you need to add your container and labels separately
```
echo $this->Html->tag('div', null, ['class' => 'form-group clearfix']);
echo $this->Form->label('My checkboxes');
echo $this->Html->tag('div', null, ['class' => 'custom-controls-stacked']);

echo $this->Form->multiCheckbox('checkbox2', [
    ['text' => 'First Checkbox', 'value' => 1],
    ['text' => 'Second Checkbox', 'value' => 2]],
    [
        'default' => 2
    ]);
echo $this->Html->tag('/div');
echo $this->Html->tag('/div');
```
or
```
echo $this->Html->tag('div', null, ['class' => 'form-group clearfix']);
echo $this->Form->label('My radios');
echo $this->Html->tag('div', null, ['class' => 'custom-controls-stacked']);

echo $this->Form->radio('radio1', [
    ['text' => 'First Radio', 'value' => 1],
    ['text' => 'Second Radio', 'value' => 2]],
    [
        'label' => 'label here',
        'default' => 1
    ]);
echo $this->Html->tag('/div');
echo $this->Html->tag('/div');
```