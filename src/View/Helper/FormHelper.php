<?php
namespace lilHermit\Bootstrap4\View\Helper;


use Cake\Utility\Hash;
use Cake\View\View;
use lilHermit\Toolkit\Utility\Html;

class FormHelper extends \Cake\View\Helper\FormHelper {

    private $customControls = true;

    protected $_bootstrapWidgets = [
        'bootstrapDateTime' => ['lilHermit/Bootstrap4.BootstrapDateTime'],
        'hidden' => ['lilHermit/Bootstrap4.Hidden']
    ];

    protected $_bootstrapTypeMap = [
        'datetime' => 'bootstrapDateTime',
        'date' => 'bootstrapDate',
        'time' => 'bootstrapTime'
    ];

    protected $_templates = [
        'button' => '<button{{attrs}}>{{text}}</button>',

        'checkbox' => '<input type="checkbox" name="{{name}}" value="{{value}}"{{attrs}}>',
        'checkboxFormGroup' => '{{label}}',
        'checkboxWrapper' => '<div class="checkbox">{{label}}</div>',
        'dateWidget' => '{{year}} {{month}} {{day}} {{hour}} {{minute}} {{second}} {{meridian}}',
        'error' => '<div class="form-control-feedback">{{content}}</div>',
        'errorList' => '<ul>{{content}}</ul>',
        'errorItem' => '<li>{{text}}</li>',
        'fieldset' => '<fieldset{{attrs}}>{{content}}</fieldset>',
        'formStart' => '<form{{attrs}}>',
        'formEnd' => '</form>',
        'formGroup' => '{{label}}{{input}}',
        'hiddenBlock' => '<div style="display:none;">{{content}}</div>',
        'hidden' => '<input type="hidden" name="{{name}}"{{attrs}}/>',
        'input' => '<input type="{{type}}" name="{{name}}"{{attrs}}/>',
        'inputSubmit' => '<input type="{{type}}"{{attrs}}/>',
        'inputContainer' => '<div class="form-group">{{content}}{{help}}</div>',
        'inputContainerError' => '<div class="form-group has-danger">{{content}}{{error}}{{help}}</div>',
        'label' => '<label{{attrs}}>{{text}}</label>',
        'nestingLabel' => '{{hidden}}<label{{attrs}}>{{input}}{{text}}</label>',
        'legend' => '<legend>{{text}}</legend>',
        'multicheckboxTitle' => '<legend>{{text}}</legend>',
        'multicheckboxWrapper' => '<fieldset{{attrs}}>{{content}}</fieldset>',
        'option' => '<option value="{{value}}"{{attrs}}>{{text}}</option>',
        'optgroup' => '<optgroup label="{{label}}"{{attrs}}>{{content}}</optgroup>',
        'radio' => '<input type="radio" name="{{name}}" value="{{value}}"{{attrs}}>',
        'radioWrapper' => '{{label}}',
        'select' => '<select name="{{name}}"{{attrs}}>{{content}}</select>',
        'selectMultiple' => '<select name="{{name}}[]" multiple="multiple"{{attrs}}>{{content}}</select>',
        'textarea' => '<textarea name="{{name}}"{{attrs}}>{{value}}</textarea>',
        'submitContainer' => '{{content}}',
        'datetimeFormGroup' => '{{label}}<div class="form-inline">{{input}}</div>',
        'dateFormGroup' => '{{label}}<div class="form-inline">{{input}}</div>',
        'timeFormGroup' => '{{label}}<div class="form-inline">{{input}}</div>',
        'bootstrapDateTime' => '<input type="{{type}}" name="{{name}}" class="form-control"{{attrs}}/>',
        'help' => '<small{{attrs}}>{{content}}</small>',
        'prefixSuffix' => '<span{{attrs}}>{{content}}</span>',
        'prefixSuffixContainer' => '<div{{attrs}}>{{prefix}}{{input}}{{suffix}}</div>'
    ];

    public function __construct(View $View, array $config = []) {

        $this->_defaultConfig['templates'] = $this->_templates;
        $this->_defaultConfig['errorClass'] = 'form-control-danger';

        $this->_defaultWidgets =
            array_replace_recursive($this->_defaultWidgets, $this->_bootstrapWidgets);

        if (isset($config['customControls']) && is_bool($config['customControls'])) {
            $this->customControls = $config['customControls'];
            unset($config['customControls']);
        }

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
     * - `error` - Control the error message that is produced. Set to `false` to disable any kind of error reporting
     * (field error and error messages).
     * - `empty` - String or boolean to enable empty select box options.
     * - `nestedInput` - Used with checkbox and radio inputs. Set to false to render inputs outside of label
     *   elements. Can be set to true on any input to force the input inside the label. If you
     *   enable this option for radio buttons you will also need to modify the default `radioWrapper` template.
     * - `templates` - The templates you want to use for this input. Any templates will be merged on top of
     *   the already loaded templates. This option can either be a filename in /config that contains
     *   the templates you want to load, or an array of templates to use.
     *
     * @param string $fieldName This should be "modelname.fieldname"
     * @param array  $options   Each type of input takes different options.
     *
     * @return string Completed form widget.
     * @link http://book.cakephp.org/3.0/en/views/helpers/form.html#creating-form-inputs
     */
    public function control($fieldName, array $options = []) {
        $options += [
            'customControls' => $this->customControls,
            'help' => false,
            'prefix' => false,
            'suffix' => false
        ];

        $this->_defaultConfig['templates'] = $this->_templates;

        // Work out the type, so we can switchTemplates if required!
        $options = $this->_parseOptions($fieldName, $options);

        $this->setLabelClass($options, isset($options['type']) ? $options['type'] : null, true);
        $this->switchTemplates($options);

        // Move certain options to templateVars
        $this->_parseTemplateVar($options, ['help', 'prefix', 'suffix']);

        if (method_exists(get_parent_class($this), 'control')) {
            return parent::control($fieldName, $options);
        } else {
            return parent::input($fieldName, $options);
        }
    }

    private function _addLabelClass($options, $class, $index = 'label') {

        if (isset($options[$index])) {
            if ($options[$index] === false) {
                return $options;
            }

            // Save the text from the label
            if (is_string($options[$index])) {
                $text = $options[$index];
                $options = Hash::insert($options, $index . '.text', $text);
            }
        }

        return Html::addClass(
            $options,
            $class,
            ['useIndex' => $index . '.class']
        );
    }

    /**
     * Generates a form control element complete with label and wrapper div.
     *
     * @param string $fieldName This should be "modelname.fieldname"
     * @param array  $options   Each type of input takes different options.
     *
     * @return string Completed form widget.
     * @link       http://book.cakephp.org/3.0/en/views/helpers/form.html#creating-form-inputs
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
     * @param array $fields  An array of the fields to generate. This array allows
     *                       you to set custom types, labels, or other options.
     * @param array $options Options array. Valid keys are:
     *                       - `fieldset` Set to false to disable the fieldset. You can also pass an
     *                       array of params to be applied as HTML attributes to the fieldset tag.
     *                       If you pass an empty array, the fieldset will be enabled.
     *                       - `legend` Set to false to disable the legend for the generated input set.
     *                       Or supply a string to customize the legend text.
     *
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
     * @param array $fields  An array of the fields to generate. This array allows
     *                       you to set custom types, labels, or other options.
     * @param array $options Options array. Valid keys are:
     *                       - `fieldset` Set to false to disable the fieldset. You can also pass an
     *                       array of params to be applied as HTML attributes to the fieldset tag.
     *                       If you pass an empty array, the fieldset will be enabled.
     *                       - `legend` Set to false to disable the legend for the generated input set.
     *                       Or supply a string to customize the legend text.
     *
     * @return string Completed form inputs.
     * @link       http://book.cakephp.org/3.0/en/views/helpers/form.html#generating-entire-forms
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
     * @param array $fields  An array of customizations for the fields that will be
     *                       generated. This array allows you to set custom types, labels, or other options.
     * @param array $options Options array. Valid keys are:
     *                       - `fieldset` Set to false to disable the fieldset. You can also pass an array of params to
     *                       be applied as HTML attributes to the fieldset tag. If you pass an empty array, the
     *                       fieldset will be enabled
     *                       - `legend` Set to false to disable the legend for the generated control set. Or supply a
     *                       string to customize the legend text.
     *
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
     * @param array $fields  An array of customizations for the fields that will be
     *                       generated. This array allows you to set custom types, labels, or other options.
     * @param array $options Options array. Valid keys are:
     *                       - `fieldset` Set to false to disable the fieldset. You can also pass an array of params to
     *                       be applied as HTML attributes to the fieldset tag. If you pass an empty array, the
     *                       fieldset will be enabled
     *                       - `legend` Set to false to disable the legend for the generated control set. Or supply a
     *                       string to customize the legend text.
     *
     * @return string Completed form controls.
     * @link       http://book.cakephp.org/3.0/en/views/helpers/form.html#generating-entire-forms
     * @deprecated 3.4.0 Use FormHelper::allControls() instead.
     */
    public function allInputs(array $fields = [], array $options = []) {
        return $this->allControls($fields, $options);
    }

    public function multiCheckbox($fieldName, $options, array $attributes = []) {
        $attributes += [
            'customControls' => $this->customControls
        ];
        $this->_defaultConfig['templates'] = $this->_templates;
        $this->setLabelClass($attributes, 'multicheckbox');
        $this->switchTemplates($attributes, 'multicheckbox');

        return parent::multiCheckbox($fieldName, $options, $attributes);
    }

    public function select($fieldName, $options = [], array $attributes = []) {
        $attributes += [
            'customControls' => $this->customControls
        ];

        return parent::select($fieldName, $options, $attributes);
    }

    public function checkbox($fieldName, array $options = []) {
        $options += [
            'customControls' => $this->customControls,
            'type' => 'checkbox'
        ];

        $this->_defaultConfig['templates'] = $this->_templates;
        $this->setLabelClass($options, 'checkbox');
        $this->switchTemplates($options, 'checkbox');

        return parent::checkbox($fieldName, $options);
    }


    public function radio($fieldName, $options = [], array $attributes = []) {
        $attributes += [
            'customControls' => $this->customControls,
            'type' => 'radio'
        ];

        $this->_defaultConfig['templates'] = $this->_templates;
        $this->setLabelClass($attributes, 'radio');
        $this->switchTemplates($attributes, 'radio');

        return parent::radio($fieldName, $options, $attributes);
    }

    public function fieldset($fields = '', array $options = []) {
        if (!isset($options['fieldset']) || $options['fieldset'] !== false) {
            $options = HTml::addClass($options, 'form-group', ['useIndex' => 'fieldset.class']);
        }

        return parent::fieldset($fields, $options);
    }

    public function hidden($fieldName, array $options = []) {
        $options['type'] = 'hidden';

        return parent::hidden($fieldName, $options);
    }

    public function widget($name, array $data = []) {
        $data = $this->_addWidgetClass($data, $name);

        // Clean up any elements so they don't end up as attributes
        unset($data['customControls'], $data['templateType'], $data['skipSwitchTemplates']);

        return parent::widget($name, $data);
    }

    public function file($fieldName, array $options = []) {
        $options += [
            'customControls' => $this->customControls,
            'type' => 'file'
        ];

        $this->_defaultConfig['templates'] = $this->_templates;
        $this->switchTemplates($options, 'file');

        return parent::file($fieldName, $options);
    }

    protected function _initInputField($field, $options = []) {
        $options = parent::_initInputField($field, $options);
        $options = $this->_addWidgetClass($options);

        return $options;
    }

    protected function _addWidgetClass($options = [], $widgetType = null) {

        $skipClassFallback = [
            'hidden'
        ];

        $widgetsClassMap = [
            'text' => 'form-control',
            'select' => 'form-control',
            'multicheckbox' => 'custom-control-input',
            'checkbox' => 'custom-control-input',
            'radio' => 'custom-control-input',
            'file' => 'custom-file-input'
        ];

        if (isset($options['customControls'])) {
            if (!$options['customControls']) {
                $widgetsClassMap = array_merge($widgetsClassMap,
                    [
                        'multicheckbox' => 'form-check-input',
                        'checkbox' => 'form-check-input',
                        'radio' => 'form-check-input',
                        'file' => 'form-control-file'
                    ]);
            }
        }

        $type = null;
        if (!empty($options['templateType'])) {
            $type = $options['templateType'];
        } else {

            if (!empty($options['type'])) {
                $type = $options['type'];
            } else {
                if ($widgetType !== null && array_key_exists($widgetType, $widgetsClassMap)) {
                    $type = $widgetType;
                }
            }
        }

        $class = null;
        if ($type == null || !array_key_exists($type, $widgetsClassMap)) {

            // Fall back for certain inputs ie didn't come from widget method
            if ($widgetType === null && !in_array($type, $skipClassFallback)) {
                $class = 'form-control';
            }
        } else {
            $class = $widgetsClassMap[$type];
        }

        $options = Html::addClass($options, $class);

        return $options;
    }


    /**
     *
     * Wrapper method for _parseTemplateVar which facilitates array and string
     *
     * @param $options
     * @param $var
     *
     * @return void
     */
    private function _parseTemplateVar(&$options, $var) {

        if (is_array($var)) {
            foreach ($var as $item) {
                $this->_parseTemplateVar($options, $item);
            }
        } else {
            if (isset($options[$var])) {
                $options['templateVars'][$var] = $options[$var];
                unset($options[$var]);
            }
        }
    }

    private function _parseAndRenderPrefixSuffix($data) {
        $out = '';
        if ($data) {
            if (is_string($data)) {
                $out = $this->_renderPrefixSuffix(h($data), ['class' => 'input-group-addon']);
            } else {
                if (is_array($data)) {
                    // Handles single array instance
                    if (isset($data['text'])) {
                        $data = [$data];
                    }

                    foreach ($data as $key => $item) {

                        if (is_string($item)) {
                            $out .= $this->_renderPrefixSuffix(h($item), ['class' => 'input-group-addon']);
                        } else if (is_array($item)) {
                            if (isset($item['text'])) {
                                $item += ['type' => 'addon', 'escape' => true];
                                if ($item['escape']) {
                                    $item['text'] = h($item['text']);
                                }

                                switch ($item['type']) {
                                    case 'button':
                                    case 'btn':
                                        $item = Html::addClass($item, 'input-group-btn');
                                        break;
                                    default:
                                        $item = Html::addClass($item, 'input-group-addon');
                                        break;
                                }
                                $out .= $this->_renderPrefixSuffix($item['text'], $item);
                            }
                        }
                    }
                }
            }
        }
        return $out;
    }

    private function _parsePrefixSuffixSize($prefix, $suffix) {
        $sizeScores = ['large' => 1, 'lg' => 1, 'normal' => 0, 'standard' => 0, 'small' => 0];
        $data = [$prefix, $suffix];

        // Add a normal so it defaults to that if not found
        $foundSizes = [0];

        foreach ($data as $item) {
            if (is_array($item)) {
                $flattened = Hash::flatten($item);
                foreach ($flattened as $key => $value) {
                    if (strstr($key, 'size') !== false && array_key_exists($value, $sizeScores)) {
                        $foundSizes[] = $sizeScores[$value];
                    }
                }
            }
        }

        rsort($foundSizes);
        return array_search($foundSizes[0], $sizeScores);
    }

    private function _renderPrefixSuffix($content, $attrs) {
        $attrs = $this->templater()->formatAttributes($attrs, ['text', 'type', 'size']);
        return $this->templater()->format('prefixSuffix', compact('content', 'attrs'));
    }

    private function renderPrefixSuffix($input, $options) {

        if (!$options['templateVars']['prefix'] && !$options['templateVars']['suffix']) {
            return $input;
        }

        $size = $this->_parsePrefixSuffixSize($options['templateVars']['prefix'], $options['templateVars']['suffix']);
        $attrs = ['class' => 'input-group'];
        if ($size === 'large') {
            $attrs = Html::addClass($attrs, 'input-group-lg');
        }
        $attrs = $this->templater()->formatAttributes($attrs);

        $prefix = $this->_parseAndRenderPrefixSuffix($options['templateVars']['prefix']);
        $suffix = $this->_parseAndRenderPrefixSuffix($options['templateVars']['suffix']);

        return $this->templater()->format('prefixSuffixContainer', compact('prefix', 'input', 'suffix', 'attrs'));
    }

    private function formatHelp(&$options) {

        if ($options['templateVars']['help']) {

            $help = $options['templateVars']['help'];

            $data = ['attrs' => []];
            if (is_string($help)) {
                $data['content'] = $help;
            } else if (is_array($help) && isset($help['text'])) {
                $data['content'] = $help['text'];
                $data['attrs'] = $help;
            }

            if (isset($data['content'])) {
                // Add the class
                $data['attrs'] = Html::addClass($data['attrs'], ['form-text', 'text-muted']);

                // Convert attrs to a string
                $data['attrs'] = $this->templater()->formatAttributes($data['attrs'], ['text']);

                $options['templateVars']['help'] = $this->templater()->format('help', $data);

            } else {
                unset($options['templateVars']['help']);
            }
        }

    }

    protected function _getInput($fieldName, $options) {
        $input = parent::_getInput($fieldName, $options);

        // now process the prefix and suffix
        return $this->renderPrefixSuffix($input, $options);
    }

    protected function _inputContainerTemplate($options) {
        $this->formatHelp($options['options']);
        return parent::_inputContainerTemplate($options);
    }

    /**
     * Sets templates to use.
     *
     * @param array $templates Templates to be added.
     *
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

        if (!isset($options['templateType'])) {
            $options['templateType'] = $type;
        }

        $type = $this->decodeType($options, $type);

        $skipSwitchTemplates = isset($options['skipSwitchTemplates']) && $options['skipSwitchTemplates'] === true;
        $skipNonRelevantWidget = !in_array($type, ['checkbox', 'radio', 'multicheckbox', 'file']);

        if ($skipSwitchTemplates || $skipNonRelevantWidget) {
            return;
        }

        if ($options['customControls']) {

            switch ($type) {
                case 'checkbox':
                    $this->setTemplates([
                        'nestingLabel' => '{{hidden}}<label{{attrs}}>{{input}}<span class="custom-control-indicator"></span> <span class="custom-control-description">{{text}}</span></label>',
                        'checkbox' => '<input type="checkbox" name="{{name}}" value="{{value}}"{{attrs}}> ',
                        'checkboxContainer' => '<div class="form-group clearfix"{{attrs}}>{{content}}{{error}}{{help}}</div>',
                    ]);
                    break;
                case 'multicheckbox':
                    $this->setTemplates([
                        'nestingLabel' => '{{hidden}}<label{{attrs}}>{{input}}<span class="custom-control-indicator"></span> <span class="custom-control-description">{{text}}</span></label>',
                        'checkbox' => '<input type="checkbox" name="{{name}}" value="{{value}}"{{attrs}}> ',
                        'checkboxWrapper' => '{{label}}',

                        // Select because we might be using the ['type' => 'select', 'multiple' => 'checkbox']
                        'selectContainer' => '<div class="form-group clearfix"{{attrs}}>{{content}}{{error}}{{help}}</div>',
                        'selectContainerError' => '<div class="form-group clearfix has-danger"{{attrs}}>{{content}}{{error}}{{help}}</div>',

                        'selectFormGroup' => '{{label}}<div class="custom-controls-stacked"{{attrs}}>{{input}}</div>',
                    ]);
                    break;
                case 'radio':
                    $this->setTemplates([
                        'nestingLabel' => '<label{{attrs}}>{{input}}<span class="custom-control-indicator"></span> <span class="custom-control-description">{{text}}</span></label>',
                        'radio' => '<input type="radio" name="{{name}}" value="{{value}}"{{attrs}}> ',
                        'radioWrapper' => '{{label}}'
                    ]);
                    break;
                case 'file':
                    $this->setTemplates([
                        'file' => '<label class="custom-file"><input type="file" name="{{name}}"{{attrs}}><span class="custom-file-control"></span></label>',
                    ]);
                    break;
            }
        } else {
            switch ($type) {
                case 'multicheckbox':
                    $this->setTemplates([
                        'nestingLabel' => '{{hidden}}<label{{attrs}}>{{input}}{{text}}</label>',
                        'checkbox' => '<input type="checkbox" name="{{name}}" value="{{value}}"{{attrs}}>',
                        'checkboxWrapper' => '<div class="form-check">{{label}}</div>',

                        // Reset incase custom was previously used
                        'selectContainer' => null,
                        'selectFormGroup' => null,
                    ]);
                    break;
                case 'checkbox':
                    $this->setTemplates([
                        'nestingLabel' => '{{hidden}}<label{{attrs}}>{{input}}{{text}}</label>',
                        'checkbox' => '<input type="checkbox" name="{{name}}" value="{{value}}"{{attrs}}>',
                        'checkboxContainer' => '<div class="form-check"{{attrs}}>{{content}}{{error}}{{help}}</div>',
                    ]);
                    break;
                case 'radio':
                    $this->setTemplates([
                        'radioWrapper' => '<div class="form-check">{{label}}</div>',
                        'nestingLabel' => '{{hidden}}<label{{attrs}}>{{input}}{{text}}</label>',
                        'radio' => '<input type="radio" name="{{name}}" value="{{value}}"{{attrs}}>',
                    ]);
                    break;
                case 'file':
                    $this->setTemplates([
                        'file' => '<input type="file" name="{{name}}"{{attrs}}>'
                    ]);
                    break;
            }
        }
    }

    private function decodeType($options, $type) {
        $type = $type ?: $options['type'];
        if ($type == 'select' && isset($options['multiple']) && $options['multiple'] === 'checkbox') {
            $type = 'multicheckbox';
        }

        return $type;
    }

    private function setLabelClass(&$options, $type = null, $fromInput = false) {
        $type = $this->decodeType($options, $type);
        $customControls = $options['customControls'];

        $label = [];
        $index = 'label';
        if ($type === 'file') {
            if ($customControls) {
                $label = 'col-form-label d-block';
            }
        } elseif ($type === 'radio' || $type === 'checkbox' || $type === 'multicheckbox') {

            if ($fromInput) {
                if ($type === 'checkbox') {
                    if ($customControls) {
                        $label = ['custom-control', 'custom-checkbox'];
                    } else {
                        $label = 'form-check-label';
                    }
                }
            } else {
                if ($type === 'radio') {
                    $label = $customControls ? ['custom-control', 'custom-radio'] : 'form-check-label';
                }
            }

            if ($type === 'multicheckbox') {

                $index = $fromInput ? 'labelOptions' : 'label';
                if ($customControls) {
                    $label = ['custom-control', 'custom-checkbox'];
                } else {
                    $label = 'form-check-label';
                }
            }

        } else {
            $label = 'col-form-label';
        }

        $options = $this->_addLabelClass($options, $label, $index);
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
     * @param string $title   The content to be used for the button.
     * @param array  $options Array of options and HTML attributes.
     *
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
     *                             extension .jpg, .jpe, .jpeg, .gif, .png use an image if the extension
     *                             exists, AND the first character is /, image is relative to webroot,
     *                             OR if the first character is not /, image is relative to webroot/img.
     * @param array       $options Array of options. See above.
     *
     * @return string A HTML submit button
     */
    public function submit($caption = null, array $options = []) {

        if (!preg_match('/\.(jpg|jpe|jpeg|gif|png|ico)$/', $caption)) {
            $options = $this->parseButtonClass($options);
        }

        return parent::submit($caption, $options);
    }

    private function parseButtonClass(&$options) {

        $options = $options + [
                'size' => 'normal',
                'secondary' => false,
                'outline' => false,
                'class' => []
            ];

        $newClasses = ['btn'];

        if ($options['outline']) {
            $newClasses[] = $options['secondary'] ? 'btn-outline-secondary' : 'btn-outline-primary';
        } else {
            $newClasses[] = $options['secondary'] ? 'btn-secondary' : 'btn-primary';
        }

        switch ($options['size']) {
            case 'large':
            case 'lg':
                $newClasses[] = 'btn-lg';
                break;
            case 'small':
            case 'sm':
                $newClasses[] = 'btn-sm';
                break;
        }

        unset($options['size'], $options['secondary'], $options['outline']);

        $options = Html::addClass($options, $newClasses);

        return $options;
    }

    /**
     *
     * This is where we add the class for each select control in the
     * datetime
     *
     * @param array $options
     *
     * @return array This options with new added class
     */
    protected function _datetimeOptions($options) {
        $options = parent::_datetimeOptions($options);

        foreach ($this->_datetimeParts as $type) {
            if ($options[$type]) {
                $options[$type] = Html::addClass($options[$type], 'form-control');
            }
        }
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

        if (is_object($options['val']) && in_array(get_class($options['val']), [
                'Cake\I18n\Date',
                'Cake\I18n\FrozenDate',
                'Cake\I18n\Time',
                'Cake\I18n\FrozenTime',
            ])
        ) {

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
     *
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
     * @param array  $options
     *
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
     *
     * @return bool if we should use the HTML5 (else its the CakePHP select boxes)
     */
    private function isHtml5Render($options) {
        return (!isset($options['html5Render']) || $options['html5Render']);
    }
}