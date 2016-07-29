<?php
namespace lilHermit\Bootstrap4\View\Helper;


use Cake\View\View;

class FormHelper extends \Cake\View\Helper\FormHelper {

    protected $_bootstrapWidgets = [
        'bootstrapDateTime' => ['lilHermit/Bootstrap4.BootstrapDateTime']
    ];

    protected $_bootstrapTypeMap = [
        'datetime' => 'bootstrapDateTime',
        'date' => 'bootstrapDate',
        'time' => 'bootstrapTime'
    ];

    protected $_bootstrapConfig = [
        'templates' => [
            'button' => '<button {{attrs}}>{{text}}</button>',
            'checkbox' => '<input type="checkbox" name="{{name}}" value="{{value}}"{{attrs}}> ',
            'checkboxFormGroup' => ' {{label}}',
            'checkboxContainer' => '<div class="checkbox">{{content}}</div>',
            'dateWidget' => '{{year}} {{month}} {{day}} {{hour}} {{minute}} {{second}} {{meridian}}',
            'datetimeFormGroup' => '{{label}}<div class="form-inline">{{input}}</div>',
            'dateFormGroup' => '{{label}}<div class="form-inline">{{input}}</div>',
            'timeFormGroup' => '{{label}}<div class="form-inline">{{input}}</div>',
            'error' => '<div class="form-control-feedback">{{content}}</div>',
            'file' => '<input type="file" name="{{name}}" class="form-control-file"{{attrs}}>{{placeholder}}',
            'fieldset' => '<fieldset{{attrs}} class="form-group">{{content}}</fieldset>',
            'formGroup' => '{{label}}{{input}}',
            'input' => '{{prefix}}<input type="{{type}}" name="{{name}}" class="form-control"{{attrs}}/>{{suffix}}',
            'inputContainer' => '<div class="form-group">{{content}}<small class="form-text text-muted">{{help}}</small></div>',
            'inputContainerError' => '<div class="form-group has-danger">{{content}}{{error}}<small class="form-text text-muted">{{help}}</small></div>',
            'select' => '<select name="{{name}}" class="form-control"{{attrs}}>{{content}}</select>',
            'selectMultiple' => '<select name="{{name}}[]" class="form-control" multiple="multiple"{{attrs}}>{{content}}</select>',
            'radioWrapper' => '<div class="radio">{{label}}&nbsp;</div>',
            'radio' => '<input type="radio" name="{{name}}" value="{{value}}"{{attrs}}> ',
            'textarea' => '<textarea name="{{name}}" class="form-control"{{attrs}}>{{value}}</textarea>',

            'label' => '<label class="col-form-label" for="{{id}}" {{attrs}}>{{text}}</label>',
            'nestingLabel' => '{{hidden}}<label class="col-form-label" for="{{id}}" {{attrs}}>{{input}}{{text}}</label>',

            'bootstrapDateTime' => '<input type="{{type}}" name="{{name}}" class="form-control"{{attrs}}/>',
//            'bootstrapDateTimeContainer' => '<div  class="form-group">{{content}}</div>',
//            'bootstrapTime' => '<input type="{{type}}" name="{{name}}" class="form-control"{{attrs}}/>',
//            'bootstrapTimeContainer' => '<div class="form-group">{{content}}</div>',
//            'bootstrapDate' => '<input type="{{type}}" name="{{name}}" class="form-control"{{attrs}}/>',
//            'bootstrapDateContainer' => '<div class="form-group">{{content}}</div>',
        ]
    ];

    public function __construct(View $View, array $config = []) {
        $this->_defaultConfig =
            array_replace_recursive($this->_defaultConfig, $this->_bootstrapConfig);

        $this->_defaultWidgets =
            array_replace_recursive($this->_defaultWidgets, $this->_bootstrapWidgets);

        parent::__construct($View, $config);
    }

    public function input($fieldName, array $options = []) {

        // Move certain options to templateVars
        $this->_parseTemplateVar($options, ['help', 'prefix', 'suffix']);
        return parent::input($fieldName, $options);
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
                $options['class'][] = 'btn-large';
                break;
            case 'small':
            case 'sm':
                $options['class'][] = 'btn-sm';
                break;
        }

        unset($options['size'], $options['secondary']);

        return parent::button($title, $options);
    }

    private function formatDateTimes(&$options) {
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