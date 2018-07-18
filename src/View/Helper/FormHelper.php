<?php

namespace LilHermit\Bootstrap4\View\Helper;


use Cake\Utility\Hash;
use Cake\View\View;
use LilHermit\Toolkit\Utility\Html;

class FormHelper extends \Cake\View\Helper\FormHelper {

    protected $bootstrapConfigDefaults = [
        'customControls' => true,
        'html5Render' => true,
        'layout' => [
            'type' => 'block',
            'showLabels' => true,
            'classes' => [
                'label' => [],
                'control' => [],
                'submitContainer' => [],
                'checkboxContainer' => ['col-sm-10', 'offset-sm-2'],
                'radioContainer' => ['col-sm-10 pl-0'],
                'grid' => []
            ]
        ],
        'errorClass' => 'is-invalid'
    ];

    protected $_userChangedTemplates = [];

    protected $_bootstrapWidgets = [
        'bootstrapDateTime' => ['LilHermit/Bootstrap4.BootstrapDateTime'],
        'hidden' => ['LilHermit/Bootstrap4.Hidden']
    ];

    protected $_bootstrapTypeMap = [
        'datetime' => 'bootstrapDateTime',
        'date' => 'bootstrapDate',
        'time' => 'bootstrapTime'
    ];

    protected $_templates = [
        'button' => '<button{{attrs}}>{{text}}</button>',

        'checkbox' => '<input type="checkbox" name="{{name}}" value="{{value}}"{{attrs}}>',
        'checkboxFormGroup' => '{{input}}{{label}}',
        'checkboxWrapper' => '<div class="checkbox">{{label}}</div>',
        'dateWidget' => '{{year}} {{month}} {{day}} {{hour}} {{minute}} {{second}} {{meridian}}',
        'error' => '<div class="invalid-feedback">{{content}}</div>',
        'errorList' => '<ul>{{content}}</ul>',
        'errorItem' => '<li>{{text}}</li>',
        'fieldset' => '<fieldset{{attrs}}>{{content}}</fieldset>',
        'formStart' => '<form{{attrs}}>',
        'formEnd' => '</form>',
        'formGroupGrid' => '{{label}}<div{{attrs}}>{{input}}',
        'formGroup' => '{{label}}{{input}}',
        'hiddenBlock' => '<div style="display:none;">{{content}}</div>',
        'hidden' => '<input type="hidden" name="{{name}}"{{attrs}}/>',
        'input' => '<input type="{{type}}" name="{{name}}"{{attrs}}/>',
        'inputSubmit' => '<input type="{{type}}"{{attrs}}/>',
        'inputContainer' => '<div class="form-group{{required}}">{{content}}{{help}}</div>',
        'inputContainerError' => '<div class="form-group{{required}}">{{content}}{{error}}{{help}}</div>',
        'inputContainerGrid' => '<div class="form-group row{{required}}">{{content}}{{help}}</div></div>',
        'inputContainerGridError' => '<div class="form-group row{{required}}">{{content}}{{error}}{{help}}</div></div>',
        'label' => '<label{{attrs}}>{{text}}</label>',
        'nestingLabel' => '{{hidden}}<label{{attrs}}>{{input}}{{text}}</label>',
        'legend' => '<legend>{{text}}</legend>',
        'multicheckboxTitle' => '<legend>{{text}}</legend>',
        'multicheckboxWrapper' => '<fieldset{{attrs}}>{{content}}</fieldset>',
        'option' => '<option value="{{value}}"{{attrs}}>{{text}}</option>',
        'optgroup' => '<optgroup label="{{label}}"{{attrs}}>{{content}}</optgroup>',
        'radio' => '<input type="radio" name="{{name}}" value="{{value}}"{{attrs}}>',
        'radioWrapper' => '{input}{{label}}',
        'select' => '<select name="{{name}}"{{attrs}}>{{content}}</select>',
        'selectMultiple' => '<select name="{{name}}[]" multiple="multiple"{{attrs}}>{{content}}</select>',
        'textarea' => '<textarea name="{{name}}"{{attrs}}>{{value}}</textarea>',
        'submitContainer' => '{{content}}',
        'datetimeFormGroup' => '{{label}}<div class="form-inline">{{input}}</div>',
        'dateFormGroup' => '{{label}}<div class="form-inline">{{input}}</div>',
        'timeFormGroup' => '{{label}}<div class="form-inline">{{input}}</div>',
        'bootstrapDateTime' => '<input type="{{type}}" name="{{name}}"{{attrs}}/>',
        'help' => '<small{{attrs}}>{{content}}</small>',
        'prependAppendText' => '<span{{attrs}}>{{content}}</span>',
        'prependAppendWrapper' => '<div{{attrs}}>{{content}}</div>',
        'prependAppendContainer' => '<div{{attrs}}>{{prepend}}{{input}}{{append}}</div>'
    ];

    public function __construct(View $View, array $config = []) {

        $this->_defaultConfig = array_merge($this->_defaultConfig, [
            'templates' => $this->_templates,
        ], $this->bootstrapConfigDefaults);

        $this->_defaultWidgets =
            array_replace_recursive($this->_defaultWidgets, $this->_bootstrapWidgets);

        $this->_parseGlobals($config);

        parent::__construct($View, $config);
    }

    public function create($model = null, array $options = []) {

        $options += [
            'customControls' => $this->getConfig('customControls'),
            'html5Render' => $this->getConfig('html5Render')
        ];

        $this->_parseGlobals($options);

        if ($this->isLayout('inline')) {
            $options = Html::addClass($options, 'form-inline');
            $this->_setTemplatesInternal([
                'inputContainer' => '{{content}}{{help}}']);
        } else if ($this->isLayout('grid')) {
            $options = Html::addClass($options, ['container']);

            if (empty($this->getConfig('layout.classes.grid'))) {
                $this->setConfig('layout.classes.grid', [['col-sm-2'], ['col-sm-10']]);
            }

        }

        return parent::create($model, $options);
    }

    private function _parseGlobals(&$input) {
        if (isset($input['customControls']) && is_bool($input['customControls'])) {
            $this->setConfig('customControls', $input['customControls']);
            unset($input['customControls']);
        }

        if (isset($input['html5Render']) && is_bool($input['html5Render'])) {
            $this->setConfig('html5Render', $input['html5Render']);
            unset($input['html5Render']);
        }

        // Reset the layout to defaults before we merge in any
        $this->setConfig('layout', $this->bootstrapConfigDefaults['layout'], false);

        if (isset($input['layout'])) {

            // Sanitise against anything other than array
            if (!is_array($input['layout'])) {
                $input['layout'] = $this->bootstrapConfigDefaults['layout'];
            }
            $this->setConfig('layout', $input['layout']);
            unset($input['layout']);
        }
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
            'customControls' => $this->getConfig('customControls'),
            'html5Render' => $this->getConfig('html5Render'),
            'help' => false,
            'prepend' => false,
            'append' => false,
            'gridClasses' => $this->getConfig('layout.classes.grid')
        ];

        $this->_defaultConfig['templates'] = $this->_templates;

        // Work out the type, so we can switchTemplates if required!
        $options = $this->_parseOptions($fieldName, $options);

        $this->setLabelClass($options, isset($options['type']) ? $options['type'] : null, true);
        $this->switchTemplates($options);

        // Move certain options to templateVars
        $this->_parseTemplateVar($options, ['help', 'prepend', 'append']);

        if (method_exists(get_parent_class($this), 'control')) {
            return parent::control($fieldName, $options);
        } else {
            /** @noinspection PhpDeprecationInspection */
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
            /** @noinspection PhpDeprecationInspection */
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

    protected function _getLabel($fieldName, $options) {
        if ($options['type'] === 'hidden') {
            return false;
        }

        $label = null;
        if (isset($options['label'])) {
            $label = $options['label'];
        }

        if ($label === false) {
            return false;
        }

        return $this->_inputLabel($fieldName, $label, $options);
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
            /** @noinspection PhpDeprecationInspection */
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
            'customControls' => $this->getConfig('customControls')
        ];
        $this->_defaultConfig['templates'] = $this->_templates;
        $this->setLabelClass($attributes, 'multicheckbox');
        $this->switchTemplates($attributes, 'multicheckbox');

        return parent::multiCheckbox($fieldName, $options, $attributes);
    }

    public function select($fieldName, $options = [], array $attributes = []) {
        $attributes += [
            'customControls' => $this->getConfig('customControls')
        ];

        return parent::select($fieldName, $options, $attributes);
    }

    public function checkbox($fieldName, array $options = []) {
        $options += [
            'customControls' => $this->getConfig('customControls'),
            'type' => 'checkbox'
        ];

        $this->_defaultConfig['templates'] = $this->_templates;
        $this->setLabelClass($options, 'checkbox');
        $this->switchTemplates($options, 'checkbox');

        return parent::checkbox($fieldName, $options);
    }


    public function radio($fieldName, $options = [], array $attributes = []) {
        $attributes += [
            'customControls' => $this->getConfig('customControls'),
            'type' => 'radio'
        ];

        $this->_defaultConfig['templates'] = $this->_templates;
        $this->setLabelClass($attributes, 'radio');
        $this->switchTemplates($attributes, 'radio');

        return parent::radio($fieldName, $options, $attributes);
    }

    public function fieldset($fields = '', array $options = []) {
        if (!isset($options['fieldset']) || $options['fieldset'] !== false) {
            $options = Html::addClass($options, 'form-group', ['useIndex' => 'fieldset.class']);
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
        $data = $this->cleanArray($data);

        return parent::widget($name, $data);
    }

    protected function cleanArray($data = [], $extras = []) {

        $elements = array_merge([
            'customControls',
            'templateType',
            'skipSwitchTemplates',
            'html5Render',
            'gridClasses'
        ], $extras);

        foreach ($elements as $element) {
            unset($data[$element]);
        }

        return $data;
    }

    public function file($fieldName, array $options = []) {
        $options += [
            'customControls' => $this->getConfig('customControls'),
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

        if (!$this->isControlControls($options)) {
            $widgetsClassMap = array_merge($widgetsClassMap,
                [
                    'multicheckbox' => 'form-check-input',
                    'checkbox' => 'form-check-input',
                    'radio' => 'form-check-input',
                    'file' => 'form-control-file'
                ]);
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
                $class = ['form-control'];
            }
        } else {
            $class = [$widgetsClassMap[$type]];
        }

        if ($type !== 'hidden' && $class !== null) {
            $options = Html::addClass($options, $this->getConfig('layout.classes.control'));
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

    private function _parseAndRenderPrependAppend($data, $append = false) {
        $out = '';
        if ($data) {
            if (is_string($data)) {
                $out = $this->_renderPrependAppendText(h($data), ['class' => 'input-group-text']);
            } else {
                if (is_array($data)) {

                    foreach ($data as $key => $item) {

                        if (is_string($item)) {
                            $out .= $this->_renderPrependAppendText(h($item), ['class' => 'input-group-text']);
                        } else if (is_array($item)) {
                            if (isset($item['text'])) {
                                $item += ['type' => 'text', 'escape' => true];
                                if ($item['escape']) {
                                    $item['text'] = h($item['text']);
                                }

                                switch ($item['type']) {
                                    case 'button':
                                    case 'btn':
                                        $out .= $item['text'];
                                        break;
                                    default:
                                        $item = Html::addClass($item, 'input-group-text');
                                        $out .= $this->_renderPrependAppendText($item['text'], $item);
                                        break;
                                }
                            }
                        }
                    }
                }
            }
            $attrs = $this->templater()->formatAttributes([
                'class' => $append ? 'input-group-append' : 'input-group-prepend'
            ]);
            return $this->templater()->format('prependAppendWrapper', [
                'attrs' => $attrs,
                'content' => $out
            ]);
        }
        return $out;
    }

    private function _parsePrependAppendSize($prepend, $append) {
        $sizeScores = ['large' => 2, 'lg' => 2, 'normal' => 1, 'standard' => 1, 'small' => 0, 'sm' => 0];
        $data = [$prepend, $append];

        $foundSizes = [];
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
        if (empty($foundSizes)) {
            return 'normal';
        } else {
            rsort($foundSizes);
            return array_search($foundSizes[0], $sizeScores);
        }
    }

    private function _parsePrependAppendContainerAttrs($prepend, $append) {

        $data = array_merge((array)$prepend, (array)$append);
        $containerAttrs = [];
        foreach ($data as $key => $item) {
            if (is_array($item) && isset($item['container'])) {
                $containerAttrs += $item['container'];
            }
        }

        return $containerAttrs;
    }

    private function _renderPrependAppendText($content, $attrs) {
        $attrs = $this->templater()->formatAttributes($attrs, ['text', 'type', 'size', 'container']);
        return $this->templater()->format('prependAppendText', compact('content', 'attrs'));
    }

    private function renderPrependAppend($input, $options) {

        if (!$options['templateVars']['prepend'] && !$options['templateVars']['append']) {
            return $input;
        }

        $prependOptions = $options['templateVars']['prepend'];
        $appendOptions = $options['templateVars']['append'];

        // Handle single array instance
        if (isset($prependOptions['text'])) {
            $prependOptions = [$prependOptions];
        }
        if (isset($appendOptions['text'])) {
            $appendOptions = [$appendOptions];
        }

        $size = $this->_parsePrependAppendSize($prependOptions, $appendOptions);

        $attrs = $this->_parsePrependAppendContainerAttrs($prependOptions, $appendOptions);

        $attrs = Html::addClass($attrs, 'input-group');

        switch ($size) {
            case 'large':
            case 'lg':
                $attrs = Html::addClass($attrs, 'input-group-lg');
                break;
            case 'small':
            case 'sm':
                $attrs = Html::addClass($attrs, 'input-group-sm');
                break;
        }

        $attrs = $this->templater()->formatAttributes($attrs);

        $prepend = $this->_parseAndRenderPrependAppend($prependOptions);
        $append = $this->_parseAndRenderPrependAppend($appendOptions, true);

        return $this->templater()->format('prependAppendContainer', compact('prepend', 'input', 'append', 'attrs'));
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

        // now process the prepend and append
        return $this->renderPrependAppend($input, $options);
    }

    protected function _inputContainerTemplate($options) {
        $this->formatHelp($options['options']);

        $containers = [];
        if ($this->isLayout('grid')) {
            $containers = [
                $options['options']['type'] . 'ContainerGrid',
                'inputContainerGrid'
            ];
        }

        $containers += [
            $options['options']['type'] . 'Container',
            'inputContainer'
        ];

        $inputContainerTemplate = "";
        foreach ($containers as $container) {
            $inputContainerTemplate = $container . $options['errorSuffix'];
            if ($this->templater()->get($inputContainerTemplate)) {
                break;
            }
        }

        return $this->formatTemplate($inputContainerTemplate, [
            'content' => $options['content'],
            'error' => $options['error'],
            'required' => $options['options']['required'] ? ' required' : '',
            'type' => $options['options']['type'],
            'templateVars' => isset($options['options']['templateVars']) ? $options['options']['templateVars'] : []
        ]);
    }

    protected function _groupTemplate($options) {
        $containers = [];
        if ($this->isLayout('grid')) {
            $containers += [
                $options['options']['type'] . 'FormGroupGrid',
                'formGroupGrid'
            ];

            switch ($options['options']['type']) {
                case 'checkbox':
                    $baseClasses = $this->isControlControls($options['options']) ? ['custom-control', 'custom-checkbox'] : [];
                    $groupClasses = Html::addClass($baseClasses, $this->getConfig('layout.classes.checkboxContainer', []), [
                        'useIndex' => false
                    ]);
                    break;
                case 'radio':
                    $baseClasses = $this->isControlControls($options['options']) ? ['custom-control', 'custom-radio'] : [];
                    $groupClasses = Html::addClass($baseClasses, $this->getConfig('layout.classes.radioContainer', []), [
                        'useIndex' => false
                    ]);
                    break;
                default:
                    $groupClasses = $options['options']['gridClasses'][1];
            }

            $attrs = $this->templater()->formatAttributes([
                'class' => $groupClasses
            ]);
            $options['options']['templateVars']['attrs'] = $attrs;
        }

        $containers += [
            $options['options']['type'] . 'FormGroup',
            'formGroup'
        ];

        $groupTemplate = "";
        foreach ($containers as $container) {
            $groupTemplate = $container;
            if ($this->templater()->get($groupTemplate)) {
                break;
            }
        }

        return $this->formatTemplate($groupTemplate, [
            'input' => isset($options['input']) ? $options['input'] : [],
            'label' => $options['label'],
            'error' => $options['error'],
            'templateVars' => isset($options['options']['templateVars']) ? $options['options']['templateVars'] : []
        ]);
    }

    /**
     * Sets templates to use.
     *
     * @param array $templates Templates to be added.
     *
     * @return void
     */
    public function setTemplates(array $templates) {

        if ($templates !== null && is_array($templates)) {
            $this->_userChangedTemplates = array_merge(array_keys($templates), $this->_userChangedTemplates);
        }

        $this->_setTemplatesWrapper($templates);
    }

    protected function _setTemplatesWrapper(array $templates) {
        if (method_exists(get_parent_class($this), 'setTemplates')) {
            parent::setTemplates($templates);
        } else {
            /** @noinspection PhpDeprecationInspection */
            parent::templates($templates);
        }
    }

    /**
     * Set templates after removing the ones previously set by the user,
     * this is to protect against overriding the users wishes
     *
     * @param array $templates Templates to set
     *
     * @return void
     */
    protected function _setTemplatesInternal(array $templates) {

        if (is_array($templates)) {

            foreach ($this->_userChangedTemplates as $key) {

                if (array_key_exists($key, $templates)) {
                    unset($templates[$key]);
                }
            }
        }

        $this->_setTemplatesWrapper($templates);
    }

    /**
     * Gets/sets templates to use.
     *
     * @deprecated 3.4.0 Use setTemplates()/getTemplates() instead.
     *
     * @param string|null|array $templates null or string allow reading templates. An array
     *                                     allows templates to be added.
     *
     * @return $this|string|array
     */
    public function templates($templates = null) {
        if ($templates === null || is_string($templates)) {
            return $this->getTemplates($templates);
        }

        $this->setTemplates($templates);
        return $this;
    }

    /**
     * Gets templates to use.
     *
     * @param string|null|array $templates null or string allow reading templates. An array
     *                                     allows templates to be added.
     *
     * @return $this|string|array
     */
    public function getTemplates($templates = null) {

        if ($templates !== null && !is_string($templates)) {
            $this->_userChangedTemplates = array_merge(array_keys($templates), $this->_userChangedTemplates);
        }

        if (method_exists(get_parent_class($this), 'getTemplates')) {
            return parent::getTemplates($templates);
        } else {
            /** @noinspection PhpDeprecationInspection */
            return parent::templates($templates);
        }
    }

    private function switchTemplates(&$options, $type = null) {

        if (!isset($options['templateType'])) {
            $options['templateType'] = $type;
        }

        $type = $this->decodeType($options, $type);

        if (!isset($options['nestedInput']) && ($type === 'checkbox' || $type === 'multicheckbox' || $type === 'radio')) {
            $options['nestedInput'] = false;
        }

        $skipSwitchTemplates = isset($options['skipSwitchTemplates']) && $options['skipSwitchTemplates'] === true;
        $skipNonRelevantWidget = !in_array($type, ['checkbox', 'radio', 'multicheckbox', 'file']);

        // Helps maintain the nestedInput template
        if (isset($options['nestedInput']) && $options['nestedInput'] === true) {
            $options['skipSwitchTemplates'] = true;
        }

        if ($skipSwitchTemplates || $skipNonRelevantWidget) {
            return;
        }

        $newTemplates = [];
        if ($this->isControlControls($options)) {

            switch ($type) {
                case 'checkbox':
                    $newTemplates = [
                        'checkboxContainer' => '<div class="custom-control custom-checkbox{{required}}"{{attrs}}>{{content}}{{error}}{{help}}</div>',
                        'checkboxContainerGrid' => '<div class="form-group row{{required}}">{{content}}{{error}}{{help}}</div>',
                        'checkboxFormGroupGrid' => "<div{{attrs}}>{{input}}{{label}}</div>"
                    ];
                    break;
                case 'multicheckbox':
                    $newTemplates = [
                        'checkboxWrapper' => '<div class="custom-control custom-checkbox">{{label}}</div>',

                        // Select because we might be using the ['type' => 'select', 'multiple' => 'checkbox']
                        'selectContainer' => '<div class="form-group clearfix{{required}}"{{attrs}}>{{content}}{{help}}</div>',
                        'selectContainerError' => '<div class="form-group clearfix{{required}}"{{attrs}}>{{content}}{{error}}{{help}}</div>',

                        'selectContainerGrid' => '<div class="form-group clearfix row{{required}}"{{attrs}}>{{content}}{{help}}</div>',
                        'selectContainerGridError' => '<div class="form-group clearfix row{{required}}"{{attrs}}>{{content}}{{error}}{{help}}</div>',
                        'selectFormGroupGrid' => '{{label}}<div{{attrs}}>{{input}}</div>',
                    ];
                    break;
                case 'radio':
                    $newTemplates = [
                        'radioWrapper' => '<div class="custom-control custom-radio">{{label}}</div>',
                        'radioContainerGrid' => '<div class="form-group row{{required}}">{{content}}{{error}}{{help}}</div>',
                        'radioFormGroupGrid' => "{{label}}<div{{attrs}}>{{input}}</div>"
                    ];
                    break;
                case 'file':
                    $newTemplates = [
                        'file' => '<div class="custom-file"><input type="file" name="{{name}}"{{attrs}}><label class="custom-file-label">Choose file</label></div>',
                    ];
                    break;
            }
        } else {
            switch ($type) {
                case 'multicheckbox':
                    $newTemplates = [
                        'checkboxWrapper' => '<div class="form-check{{required}}">{{label}}</div>',

                        // Reset incase custom was previously used
                        'selectContainer' => null,
                        'selectContainerError' => null,
                        'selectContainerGrid' => null,
                        'selectContainerGridError' => null,
                        'selectFormGroupGrid' => null,
                    ];
                    break;
                case 'checkbox':
                    $newTemplates = [
                        'checkboxContainer' => '<div class="form-check{{required}}"{{attrs}}>{{content}}{{error}}{{help}}</div>',
                        'checkboxContainerGrid' => '<div class="form-check row{{required}}"{{attrs}}>{{content}}{{error}}{{help}}</div>',
                    ];
                    break;
                case 'radio':
                    $newTemplates = [
                        'radioWrapper' => '<div class="form-check{{required}}">{{label}}</div>',
                    ];
                    break;
                case 'file':
                    $newTemplates = [
                        'file' => '<input type="file" name="{{name}}"{{attrs}}>'
                    ];
                    break;
            }
        }

        if (isset($options['nestedInput']) && $options['nestedInput'] === true) {
            $newTemplates += [
                'nestingLabel' => '{{hidden}}<label{{attrs}}>{{input}}{{text}}</label>'
            ];
        } else {
            $newTemplates += [
                'nestingLabel' => '{{hidden}}{{input}}<label{{attrs}}>{{text}}</label>'
            ];
        }

        $this->_setTemplatesInternal($newTemplates);
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
        $customControls = $this->isControlControls($options);

        $label = false;
        $index = 'label';
        if ($type === 'file') {
            if ($customControls) {
                $label = ['col-form-label', 'd-block'];
            }
        } elseif ($type === 'radio' || $type === 'checkbox' || $type === 'multicheckbox') {

            if ($fromInput) {
                if ($type === 'checkbox') {
                    if ($customControls) {
                        $label = ['custom-control-label'];
                    } else {
                        $label = ['form-check-label'];
                    }
                } else {

                    if ($this->isLayout('grid')) {
                        $options = $this->_addLabelClass($options, $this->getConfig('layout.classes.grid.0'), 'label');
                    }
                }
            } else {
                if ($type === 'radio') {
                    $label = $customControls ? ['custom-control-label'] : ['form-check-label'];
                }
            }

            if ($type === 'multicheckbox') {

                if ($fromInput && $this->isLayout('grid')) {
                    $options = $this->_addLabelClass($options, $this->getConfig('layout.classes.grid.0'), 'label');
                }

                $index = $fromInput ? 'labelOptions' : 'label';
                if ($customControls) {
                    $label = ['custom-control-label'];
                } else {
                    $label = ['form-check-label'];
                }
            }

        } else {
            $label = $this->getConfig('layout.showLabels') ? 'col-form-label' : 'sr-only';

            if ($this->isLayout('grid')) {
                $label = Html::addClass($label, $options['gridClasses'][0], ['useIndex' => false]);
            }
        }

        // If there is a label
        if ($label !== false) {
            $label = Html::addClass($label, $this->getConfig('layout.classes.label'), ['useIndex' => false]);
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

        $options += ['type' => 'submit'];

        $options = $this->parseButtonClass($options);

        $options = $this->cleanArray($options);

        $button = parent::button($title, $options);

        if ($options['type'] === 'submit') {

            if (!empty($this->getConfig('layout.classes.submitContainer'))) {
                $this->_setTemplatesInternal([
                    'submitContainer' => '<div{{attrs}}>{{content}}</div>',
                ]);

                // Add the attributes for the submitContainer
                $attrs = $this->templater()->formatAttributes([
                    'class' => $this->getConfig('layout.classes.submitContainer')
                ]);

                return $this->formatTemplate('submitContainer', [
                    'content' => $button,
                    'templateVars' => [
                        'attrs' => $attrs
                    ]
                ]);
            }
        }

        return $button;
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

        if (!empty($this->getConfig('layout.classes.submitContainer'))) {
            $this->_setTemplatesInternal([
                'submitContainer' => '<div{{attrs}}>{{content}}</div>',
            ]);

            // Add the attributes for the submitContainer
            $options['templateVars']['attrs'] = $this->templater()->formatAttributes([
                'class' => $this->getConfig('layout.classes.submitContainer')
            ]);
        }

        $options = $this->cleanArray($options);

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
            if (is_array($options[$type])) {
                $options[$type] = Html::addClass($options[$type], 'form-control');
            }
        }
        return $options;
    }

    private function formatDateTimes(&$options) {

        if (empty($options['val'])) {
            return;
        }

        $i18nFormatClasses = [
            'Cake\I18n\Date',
            'Cake\I18n\FrozenDate',
            'Cake\I18n\Time',
            'Cake\I18n\FrozenTime',
            'Cake\I18n\FrozenTime'
        ];

        if (is_string($options['val'])) {

            try {
                $options['val'] = \Cake\I18n\Time::parse($options['val']);
            } catch (\Exception $exception) {
                $options['val'] = '';
            }
        }

        if (is_object($options['val'])) {

            if (get_class($options['val']) === 'Cake\Chronos\Chronos') {

                switch ($options['type']) {
                    case 'date':
                        $format = "Y-m-d";
                        break;
                    case 'time':
                        $format = "H:i";
                        break;
                    default:
                    case 'datetime-local':
                        $format = "Y-m-d\TH:i";
                }

                $options['val'] = $options['val']->format($format);

            } else if (in_array(get_class($options['val']), $i18nFormatClasses)) {
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
    }

    public function bootstrapDate($fieldName, array $options = []) {
        $options['type'] = 'date';
        $options = $this->_initInputField($fieldName, $options);
        $this->formatDateTimes($options);

        return $this->widget('bootstrapDateTime', $options);
    }

    public function bootstrapDateTime($fieldName, array $options = []) {
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

    protected function isLayout($type) {
        return strstr($this->getConfig('layout.type'), $type) !== false;
    }

    protected function isControlControls($options) {
        return isset($options['customControls']) && is_bool($options['customControls']) && $options['customControls'];
    }
}