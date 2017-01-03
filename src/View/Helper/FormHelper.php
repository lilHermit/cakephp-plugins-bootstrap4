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

    /**
     * Generates a form control element complete with label and wrapper div.
     *
     * ### Options
     *
     * See each field type method for more information. Any options that are part of
     * $attributes or $options for the different **type** methods can be included in `$options` for input().
     * Additionally, any unknown keys that are not in the list below, or part of the selected type's options
     * will be treated as a regular HTML attribute for the generated input.
     *
     * - `type` - Force the type of widget you want. e.g. `type => 'select'`
     * - `label` - Either a string label, or an array of options for the label. See FormHelper::label().
     * - `options` - For widgets that take options e.g. radio, select.
     * - `error` - Control the error message that is produced. Set to `false` to disable any kind of error reporting (field
     *    error and error messages).
     * - `empty` - String or boolean to enable empty select box options.
     * - `nestedInput` - Used with checkbox and radio inputs. Set to false to render inputs outside of label
     *   elements. Can be set to true on any input to force the input inside the label. If you
     *   enable this option for radio buttons you will also need to modify the default `radioWrapper` template.
     * - `templates` - The templates you want to use for this input. Any templates will be merged on top of
     *   the already loaded templates. This option can either be a filename in /config that contains
     *   the templates you want to load, or an array of templates to use.
     *
     * @param string $fieldName This should be "modelname.fieldname"
     * @param array $options Each type of input takes different options.
     * @return string Completed form widget.
     * @link http://book.cakephp.org/3.0/en/views/helpers/form.html#creating-form-inputs
     */
    public function control($fieldName, array $options = []) {
        $this->_defaultConfig['templates'] = $this->_templates;

        // Work out the type, so we can switchTemplates if required!
        $options = $this->_parseOptions($fieldName, $options);

        $this->switchTemplates($options);

        // Move certain options to templateVars
        $this->_parseTemplateVar($options, ['help', 'prefix', 'suffix']);

        if (method_exists(get_parent_class($this), 'control')) {
            return parent::control($fieldName, $options);
        } else {
            return parent::input($fieldName, $options);
        }
    }

    /**
     * Generates a form control element complete with label and wrapper div.
     *
     * @param string $fieldName This should be "modelname.fieldname"
     * @param array $options Each type of input takes different options.
     * @return string Completed form widget.
     * @link http://book.cakephp.org/3.0/en/views/helpers/form.html#creating-form-inputs
     * @deprecated 3.4.0 Use FormHelper::control() instead.
     */
    public function input($fieldName, array $options = []) {
        return $this->control($fieldName, $options);
    }

    /**
     * Generate a set of controls for `$fields` wrapped in a fieldset element.
     *
     * You can customize individual controls through `$fields`.
     * ```
     * $this->Form->controls([
     *   'name' => ['label' => 'custom label'],
     *   'email'
     * ]);
     * ```
     *
     * @param array $fields An array of the fields to generate. This array allows
     *   you to set custom types, labels, or other options.
     * @param array $options Options array. Valid keys are:
     * - `fieldset` Set to false to disable the fieldset. You can also pass an
     *    array of params to be applied as HTML attributes to the fieldset tag.
     *    If you pass an empty array, the fieldset will be enabled.
     * - `legend` Set to false to disable the legend for the generated input set.
     *    Or supply a string to customize the legend text.
     * @return string Completed form inputs.
     * @link http://book.cakephp.org/3.0/en/views/helpers/form.html#generating-entire-forms
     */
    public function controls(array $fields, array $options = []) {
        if (method_exists(get_parent_class($this), 'controls')) {
            return parent::controls($fields, $options);
        } else {
            return parent::inputs($fields, $options);
        }
    }

    /**
     * Generate a set of controls for `$fields` wrapped in a fieldset element.
     *
     * @param array $fields An array of the fields to generate. This array allows
     *   you to set custom types, labels, or other options.
     * @param array $options Options array. Valid keys are:
     * - `fieldset` Set to false to disable the fieldset. You can also pass an
     *    array of params to be applied as HTML attributes to the fieldset tag.
     *    If you pass an empty array, the fieldset will be enabled.
     * - `legend` Set to false to disable the legend for the generated input set.
     *    Or supply a string to customize the legend text.
     * @return string Completed form inputs.
     * @link http://book.cakephp.org/3.0/en/views/helpers/form.html#generating-entire-forms
     * @deprecated 3.4.0 Use FormHelper::controls() instead.
     */
    public function inputs(array $fields, array $options = []) {
        return $this->controls($fields, $options);
    }

    /**
     * Generate a set of controls for `$fields`. If $fields is empty the fields
     * of current model will be used.
     *
     * You can customize individual controls through `$fields`.
     * ```
     * $this->Form->allControls([
     *   'name' => ['label' => 'custom label']
     * ]);
     * ```
     *
     * You can exclude fields by specifying them as `false`:
     *
     * ```
     * $this->Form->allControls(['title' => false]);
     * ```
     *
     * In the above example, no field would be generated for the title field.
     *
     * @param array $fields An array of customizations for the fields that will be
     *   generated. This array allows you to set custom types, labels, or other options.
     * @param array $options Options array. Valid keys are:
     * - `fieldset` Set to false to disable the fieldset. You can also pass an array of params to be
     *    applied as HTML attributes to the fieldset tag. If you pass an empty array, the fieldset will
     *    be enabled
     * - `legend` Set to false to disable the legend for the generated control set. Or supply a string
     *    to customize the legend text.
     * @return string Completed form controls.
     * @link http://book.cakephp.org/3.0/en/views/helpers/form.html#generating-entire-forms
     */
    public function allControls(array $fields = [], array $options = []) {
        if (method_exists(get_parent_class($this), 'allControls')) {
            return parent::allControls($fields, $options);
        } else {
            return parent::allInputs($fields, $options);
        }
    }

    /**
     * Generate a set of controls for `$fields`. If $fields is empty the fields
     * of current model will be used.
     *
     * @param array $fields An array of customizations for the fields that will be
     *   generated. This array allows you to set custom types, labels, or other options.
     * @param array $options Options array. Valid keys are:
     * - `fieldset` Set to false to disable the fieldset. You can also pass an array of params to be
     *    applied as HTML attributes to the fieldset tag. If you pass an empty array, the fieldset will
     *    be enabled
     * - `legend` Set to false to disable the legend for the generated control set. Or supply a string
     *    to customize the legend text.
     * @return string Completed form controls.
     * @link http://book.cakephp.org/3.0/en/views/helpers/form.html#generating-entire-forms
     * @deprecated 3.4.0 Use FormHelper::allControls() instead.
     */
    public function allInputs(array $fields = [], array $options = []) {
        return $this->allControls($fields, $options);
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

    /**
     * Sets templates to use.
     *
     * @param array $templates Templates to be added.
     * @return void
     */
    public function setTemplates(array $templates) {
        if (method_exists(get_parent_class($this), 'setTemplates')) {
            parent::setTemplates($templates);
        } else {
            parent::templates($templates);
        }
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
                    $this->setTemplates([
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
                    $this->setTemplates([
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
                    $this->setTemplates([
                        'checkboxFormGroup' => '{{label}}',
                        'checkboxWrapper' => '<div class="form-check">{{label}}</div>',
                        'nestingLabel' => '{{hidden}}<label class="form-check-label" for="{{id}}"{{attrs}}>{{input}}{{text}}</label>',
                        'checkbox' => '<input class="form-check-input" type="checkbox" name="{{name}}" value="{{value}}"{{attrs}}> ',
                        'multicheckboxContainer' => '<div class="form-check">{{content}}</div>',
                    ]);

                    break;
                case 'radio':
                    $this->setTemplates([
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

        if (is_string($options['val'])) {
            switch ($options['type']) {
                case 'date':
                    $options['val'] = \Cake\I18n\Date::createFromFormat("Y-m-d", $options['val']);
                    break;
                case 'time':
                    $options['val'] = \Cake\I18n\Time::createFromFormat("G:i", $options['val']);
                    break;
                default:
                case 'datetime-local':
                    $options['val'] = \Cake\I18n\Time::createFromFormat("Y-m-d\\TG:i", $options['val']);
            }
        }

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

        // Switch to the html5 step attribute
        if (isset($options['interval'])) {
            $options['step'] = 60 * $options['interval'];
            unset($options['interval']);
        }

        $this->formatDateTimes($options);
        return $this->widget('bootstrapDateTime', $options);
    }

    public function bootstrapTime($fieldName, array $options = []) {
        unset($options['html5Render']);
        $options['type'] = 'time';
        $options = $this->_initInputField($fieldName, $options);

        // Switch to the html5 step attribute
        if (isset($options['interval'])) {
            $options['step'] = 60 * $options['interval'];
            unset($options['interval']);
        }

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