<?php
namespace lilHermit\Bootstrap4\View\Helper;


use Cake\View\View;

class FormHelper extends \Cake\View\Helper\FormHelper {

    private $bootstrapTemplates = [
        'button' => '<button {{attrs}}>{{text}}</button>',
        'checkbox' => '<input type="checkbox" name="{{name}}" value="{{value}}"{{attrs}}> ',
        'checkboxFormGroup' => ' {{label}}',
        'checkboxContainer' => '<div class="checkbox">{{content}}</div>',
        'dateWidget' => '{{year}} {{month}} {{day}} {{hour}} {{minute}} {{second}} {{meridian}}',
        'datetimeFormGroup' => '{{label}}<div class="form-inline">{{input}}</div>',
        'dateFormGroup' => '{{label}}<div class="form-inline">{{input}}</div>',
        'timeFormGroup' => '{{label}}<div class="form-inline">{{input}}</div>',
        'error' => '<small class="text-muted text-help">{{content}}</small>',
        'file' => '<input type="file" name="{{name}}" class="form-control-file"{{attrs}}>{{placeholder}}',
        'fieldset' => '<fieldset{{attrs}} class="form-group">{{content}}</fieldset>',
        'formGroup' => '{{label}}{{input}}',
        'input' => '<input type="{{type}}" name="{{name}}" class="form-control"{{attrs}}/>',
        'inputContainer' => '<div class="form-group">{{content}}</div>',
        'inputContainerError' => '<div class="form-group has-danger">{{content}}{{error}}</div>',
        'select' => '<select name="{{name}}" class="form-control"{{attrs}}>{{content}}</select>',
        'selectMultiple' => '<select name="{{name}}[]" class="form-control" multiple="multiple"{{attrs}}>{{content}}</select>',

        'radioWrapper' => '<div class="radio">{{label}}&nbsp;</div>',
        'radio' => '<input type="radio" name="{{name}}" value="{{value}}"{{attrs}}> ',
        'textarea' => '<textarea name="{{name}}" class="form-control"{{attrs}}>{{value}}</textarea>',
    ];

    public function __construct(View $View, array $config = []) {
        $this->_defaultConfig['templates'] =
            array_merge($this->_defaultConfig['templates'], $this->bootstrapTemplates);
        parent::__construct($View, $config);
    }

    public function button($title, array $options = []) {
        $class = ['btn', 'btn-secondary'];

        if (isset($options['class'])) {
            $class = array_merge($class, explode(' ', $options['class']));
        }

        $options['class'] = implode(' ', $class);
        return parent::button($title, $options);
    }
}