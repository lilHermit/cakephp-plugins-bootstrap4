<?php
namespace lilHermit\Bootstrap4\View\Helper;


use Cake\View\View;

class FormHelper extends \Cake\View\Helper\FormHelper {

    private $customControls = true;

    protected $_bootstrapWidgets = [
        'bootstrapDateTime' => ['lilHermit/Bootstrap4.BootstrapDateTime']
    ];

    protected $_bootstrapTypeMap = [
        'datetime' => 'bootstrapDateTime',
        'date' => 'bootstrapDate',
        'time' => 'bootstrapTime'
    ];

    protected $_templates = [
        'button' => '<button {{attrs}}>{{text}}</button>',

        'checkbox' => '<input type="checkbox" name="{{name}}" value="{{value}}"{{attrs}}>',
        'checkboxFormGroup' => '{{label}}',
        'checkboxWrapper' => '<div class="checkbox">{{label}}</div>',
        'dateWidget' => '{{year}} {{month}} {{day}} {{hour}} {{minute}} {{second}} {{meridian}}',
        'error' => '<div class="form-control-feedback">{{content}}</div>',
        'errorList' => '<ul>{{content}}</ul>',
        'errorItem' => '<li>{{text}}</li>',
        'file' => '<input type="file" name="{{name}}" class="form-control-file"{{attrs}}>{{placeholder}}',
        'fieldset' => '<fieldset{{attrs}} class="form-group">{{content}}</fieldset>',
        'formStart' => '<form{{attrs}}>',
        'formEnd' => '</form>',
        'formGroup' => '{{label}}{{input}}',
        'hiddenBlock' => '<div style="display:none;">{{content}}</div>',
        'input' => '{{prefix}}<input type="{{type}}" name="{{name}}" class="form-control"{{attrs}}/>{{suffix}}',
        'inputSubmit' => '<input type="{{type}}"{{attrs}}/>',
        'inputContainer' => '<div class="form-group">{{content}}<small class="form-text text-muted">{{help}}</small></div>',
        'inputContainerError' => '<div class="form-group has-danger">{{content}}{{error}}<small class="form-text text-muted">{{help}}</small></div>',
        'label' => '<label class="col-form-label" for="{{id}}" {{attrs}}>{{text}}</label>',
        'nestingLabel' => '{{hidden}}<label class="col-form-label" for="{{id}}" {{attrs}}>{{input}}{{text}}</label>',
        'legend' => '<legend>{{text}}</legend>',
        'multicheckboxTitle' => '<legend>{{text}}</legend>',
        'multicheckboxWrapper' => '<fieldset{{attrs}}>{{content}}</fieldset>',
        'option' => '<option value="{{value}}"{{attrs}}>{{text}}</option>',
        'optgroup' => '<optgroup label="{{label}}"{{attrs}}>{{content}}</optgroup>',
        'select' => '<select name="{{name}}" class="form-control"{{attrs}}>{{content}}</select>',
        'selectMultiple' => '<select name="{{name}}[]" class="form-control" multiple="multiple"{{attrs}}>{{content}}</select>',
        'textarea' => '<textarea name="{{name}}" class="form-control"{{attrs}}>{{value}}</textarea>',
        'submitContainer' => '<div class="submit">{{content}}</div>',
        'datetimeFormGroup' => '{{label}}<div class="form-inline">{{input}}</div>',
        'dateFormGroup' => '{{label}}<div class="form-inline">{{input}}</div>',
        'timeFormGroup' => '{{label}}<div class="form-inline">{{input}}</div>',
        'bootstrapDateTime' => '<input type="{{type}}" name="{{name}}" class="form-control"{{attrs}}/>'

    ];

    public function __construct(View $View, array $config = []) {

        $this->_defaultConfig['templates'] = $this->_templates;

        $this->_defaultWidgets =
            array_replace_recursive($this->_defaultWidgets, $this->_bootstrapWidgets);

        parent::__construct($View, $config);
    }

    public function create($model = null, array $options = []) {

        if (isset($options['customControls']) && is_bool($options['customControls'])) {
            $this->customControls = $options['customControls'];
            unset($options['customControls']);
        }

        return parent::create($model, $options);
    }

    public function input($fieldName, array $options = []) {

        $this->_defaultConfig['templates'] = $this->_templates;

        // Work out the type, so we can switchTemplates if required!
        $options = $this->_parseOptions($fieldName, $options);

        $this->switchTemplates($options);

        // Move certain options to templateVars
        $this->_parseTemplateVar($options, ['help', 'prefix', 'suffix']);
        return parent::input($fieldName, $options);
    }

    public function multiCheckbox($fieldName, $options, array $attributes = []) {
        $this->_defaultConfig['templates'] = $this->_templates;
        $this->switchTemplates($attributes, 'checkbox');

        return parent::multiCheckbox($fieldName, $options, $attributes);
    }

    public function radio($fieldName, $options = [], array $attributes = []) {
        $this->_defaultConfig['templates'] = $this->_templates;
        $this->switchTemplates($attributes, 'radio');

        return parent::radio($fieldName, $options, $attributes);
    }

    /**
     *
     * Wrapper method for __parseTemplateVar which facilitates array and string
     *
     * @param $options
     * @param $var
     */

    private function _parseTemplateVar(&$options, $var) {

        if (is_array($var)) {
            foreach ($var as $item) {
                $this->__parseTemplateVar($options, $item);
            }
        } else {
            $this->__parseTemplateVar($options, $var);
        }
    }

    private function __parseTemplateVar(&$options, $var) {
        if (isset($options[$var])) {
            $options['templateVars'][$var] = $options[$var];
            unset($options[$var]);
        }
    }

    private function _parsePrefixSuffix(&$options) {

        $prefix = $suffix = '';

        $needContainer = isset($options['templateVars']['prefix']) || isset($options['templateVars']['suffix']);
        if ($needContainer) {
            $prefix = '<div class="input-group">';
        }

        if (isset($options['templateVars']['prefix'])) {
            $prefix .= sprintf('<span class="input-group-addon" id="%s">%s</span>', $options['id'], $options['templateVars']['prefix']);
        }

        if (isset($options['templateVars']['suffix'])) {
            $suffix = sprintf('<span class="input-group-addon" id="%s">%s</span>', $options['id'], $options['templateVars']['suffix']);
        }

        if ($needContainer) {
            $suffix .= '</div>';
        }

        $options['templateVars'] = array_merge($options['templateVars'], compact(['prefix', 'suffix']));
    }

    protected function _getInput($fieldName, $options) {

        // now process the prefix and suffix
        $this->_parsePrefixSuffix($options);

        return parent::_getInput($fieldName, $options);
    }

    private function switchTemplates(&$options, $type = null) {

        $type = $type ?: $options['type'];
        if ($type == 'select' && isset($options['multiple']) && $options['multiple'] == 'checkbox') {
            $type = 'checkbox';
        }

        $customControls = $this->customControls;
        if (isset($options['customControls']) && is_bool($options['customControls'])) {
            $customControls = $options['customControls'];
        }

        if ($customControls) {

            switch ($type) {
                case 'checkbox':
                    $this->templates([
                        'nestingLabel' => '{{hidden}}<label class="custom-control custom-checkbox"{{attrs}}>{{input}}<span class="custom-control-indicator"></span> <span class="custom-control-description">{{text}}</span></label>',
                        'checkbox' => '<input class="custom-control-input" type="checkbox" name="{{name}}" value="{{value}}"{{attrs}}> ',
                        'checkboxContainer' => '<div class="form-group clearfix"{{attrs}}>{{content}}</div>',
                        'checkboxWrapper' => '{{label}}',
                        'checkboxFormGroup' => '<div class="custom-controls-stacked"{{attrs}}>{{label}}</div>',

                        // Select because we might be using the ['type' => 'select', 'multiple' => 'checkbox']
                        'selectContainer' => '<div class="form-group clearfix"{{attrs}}>{{content}}</div>',
                        'selectFormGroup' => '{{label}}<div class="custom-controls-stacked"{{attrs}}>{{input}}</div>',
                    ]);
                    break;
                case 'radio':
                    $this->templates([
                        'nestingLabel' => '<label class="custom-control custom-radio"{{attrs}}>{{input}}<span class="custom-control-indicator"></span> <span class="custom-control-description">{{text}}</span></label>',
                        'radio' => '<input class="custom-control-input" type="radio" name="{{name}}" value="{{value}}"{{attrs}}> ',
                        'radioContainer' => '<div class="form-group">{{content}}</div>',
                        'radioWrapper' => '{{label}}',
                        'radioFormGroup' => '{{label}}',
                    ]);

                    break;
            }
        } else {
            switch ($type) {
                case 'checkbox':
                    $this->templates([
                        'checkboxFormGroup' => '{{label}}',
                        'checkboxWrapper' => '<div class="form-check">{{label}}</div>',
                        'nestingLabel' => '{{hidden}}<label class="form-check-label" for="{{id}}"{{attrs}}>{{input}}{{text}}</label>',
                        'checkbox' => '<input class="form-check-input" type="checkbox" name="{{name}}" value="{{value}}"{{attrs}}> ',
                        'multicheckboxContainer' => '<div class="form-check">{{content}}</div>',
                    ]);

                    break;
                case 'radio':
                    $this->templates([
                        'checkboxFormGroup' => '{{label}}',
                        'nestingLabel' => '{{hidden}}<label class="form-check-label" for="{{id}}"{{attrs}}>{{input}}{{text}}</label>',
                        'checkbox' => '<input class="form-check-input" type="checkbox" name="{{name}}" value="{{value}}"{{attrs}}> ',
                        'checkboxContainer' => '<div class="form-check">{{content}}</div>',
                    ]);;
                    break;
            }
        }
    }

    protected function _getLabel($fieldName, $options) {

        // Make ID available in templates
        $options['templateVars']['id'] = $options['id'];

        return parent::_getLabel($fieldName, $options);
    }

    /**
     * Creates a bootstrap button.
     *
     * ### Options
     *
     * - `size` Can be `small`, `normal` or `large` (default is `normal`)
     * - `class` Any additional css classes
     * - `secondary` Boolean true if you want secondary colour.
     * - `outline` Boolean true if you want button outlined.
     *
     * @param string $title The content to be used for the button.
     * @param array $options Array of options and HTML attributes.
     * @return string the element.
     */

    public function button($title, array $options = []) {

        $options = $this->parseButtonClass($options);

        return parent::button($title, $options);
    }

    /**
     * Creates submit button but adds bootstrap styling
     *
     * @param string|null $caption The label appearing on the button OR if string contains :// or the
     *  extension .jpg, .jpe, .jpeg, .gif, .png use an image if the extension
     *  exists, AND the first character is /, image is relative to webroot,
     *  OR if the first character is not /, image is relative to webroot/img.
     * @param array $options Array of options. See above.
     * @return string A HTML submit button
     */
    public function submit($caption = null, array $options = []) {
        $options = $this->parseButtonClass($options);

        return parent::submit($caption, $options);
    }

    private function parseButtonClass(&$options) {
        $options = $options + [
                'size' => 'normal',
                'secondary' => false,
                'outline' => false,
                'class' => []
            ];

        // Convert and sanitise the css class
        if (is_array($options['class'])) {
            $options['class'][] = 'btn';
        } else if (is_string($options['class'])) {
            $options['class'] = ['btn', $options['class']];
        } else {
            $options['class'] = ['btn'];
        }

        if ($options['outline']) {
            $options['class'][] = $options['secondary'] ? 'btn-outline-secondary' : 'btn-outline-primary';
        } else {
            $options['class'][] = $options['secondary'] ? 'btn-secondary' : 'btn-primary';
        }

        switch ($options['size']) {
            case 'large':
            case 'lg':
                $options['class'][] = 'btn-lg';
                break;
            case 'small':
            case 'sm':
                $options['class'][] = 'btn-sm';
                break;
        }

        unset($options['size'], $options['secondary'], $options['outline']);

        return $options;
    }

    private function formatDateTimes(&$options) {

        if (in_array(get_class($options['val']), ['Cake\I18n\Date', 'Cake\I18n\FrozenDate',
            'Cake\I18n\Time', 'Cake\I18n\FrozenTime',])) {

            switch ($options['type']) {
                case 'date':
                    $format = "yyyy-MM-dd";
                    break;
                case 'time':
                    $format = "HH:mm";
                    break;
                default:
                case 'datetime-local':
                    $format = "yyyy-MM-dd'T'HH:mm";
            }

            $options['val'] = $options['val']->i18nFormat($format);
        }
    }

    public function bootstrapDate($fieldName, array $options = []) {
        unset($options['html5Render']);
        $options['type'] = 'date';
        $options = $this->_initInputField($fieldName, $options);
        $this->formatDateTimes($options);
        return $this->widget('bootstrapDateTime', $options);
    }

    public function bootstrapDateTime($fieldName, array $options = []) {
        unset($options['html5Render']);
        $options['type'] = 'datetime-local';
        $options = $this->_initInputField($fieldName, $options);
        $this->formatDateTimes($options);
        return $this->widget('bootstrapDateTime', $options);
    }

    public function bootstrapTime($fieldName, array $options = []) {
        unset($options['html5Render']);
        $options['type'] = 'time';
        $options = $this->_initInputField($fieldName, $options);
        $this->formatDateTimes($options);
        return $this->widget('bootstrapDateTime', $options);
    }

    /**
     * Maps the type to bootstrap else text
     *
     * @param $type
     * @return mixed|string
     */
    protected function _bootstrapTypeMap($type) {
        $map = $this->_bootstrapTypeMap;
        return isset($map[$type]) ? $map[$type] : 'text';
    }

    /**
     * In case the type is defined manually by the user we need to map it
     *
     * @param string $fieldName
     * @param array $options
     * @return array
     */
    protected function _parseOptions($fieldName, $options) {

        $needsMagicType = false;
        if (empty($options['type'])) {
            $needsMagicType = true;

            $context = $this->_getContext();
            $internalType = $context->type($fieldName);
            if ($this->isHtml5Render($options) && in_array($internalType, ['date', 'datetime', 'time'])) {
                $options['type'] = $this->_bootstrapTypeMap($internalType);
            } else {
                $options['type'] = $this->_inputType($fieldName, $options);
            }
        } else {
            if ($this->isHtml5Render($options) && in_array($options['type'], ['date', 'datetime', 'time'])) {
                $options['type'] = $this->_bootstrapTypeMap($options['type']);
            }
        }

        $options = $this->_magicOptions($fieldName, $options, $needsMagicType);
        return $options;
    }

    /**
     * Tests if we want HTML5 render or not
     *
     * @param $options
     * @return bool if we should use the HTML5 (else its the CakePHP select boxes)
     */
    private function isHtml5Render($options) {
        return (!isset($options['html5Render']) || $options['html5Render']);
    }
}