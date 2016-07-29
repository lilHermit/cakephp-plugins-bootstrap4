<?php
namespace lilHermit\Bootstrap4\View\Widget;

use Cake\View\Form\ContextInterface;
use Cake\View\Widget\WidgetInterface;

class BootstrapDateTimeWidget implements WidgetInterface {

    /**
     * StringTemplate instance.
     *
     * @var \Cake\View\StringTemplate
     */
    protected $_templates;

    /**
     * Constructor.
     *
     * @param \Cake\View\StringTemplate $templates Templates list.
     */
    public function __construct($templates) {
        $this->_templates = $templates;
    }

    /**
     * Render a text widget or other simple widget like email/tel/number.
     *
     * This method accepts a number of keys:
     *
     * - `name` The name attribute.
     * - `val` The value attribute.
     * - `escape` Set to false to disable escaping on all attributes.
     *
     * Any other keys provided in $data will be converted into HTML attributes.
     *
     * @param array $data The data to build an input with.
     * @param \Cake\View\Form\ContextInterface $context The current form context.
     * @return string
     */
    public function render(array $data, ContextInterface $context) {
        $data += [
            'name' => '',
            'val' => null,
            'type' => 'text',
            'escape' => true,
            'templateVars' => []
        ];
        $data['value'] = $data['val'];
        unset($data['val']);

        // Check and change the template to use
        if ($data['type'] == 'time' && $this->_templates->config('bootstrapTime')) {
            $template = 'bootstrapTime';
        } else if ($data['type'] == 'date' && $this->_templates->config('bootstrapDate')) {
            $template = 'bootstrapDate';
        } else {
            $template = 'bootstrapDateTime';
        }

        return $this->_templates->format($template, [
            'name' => $data['name'],
            'type' => $data['type'],
            'templateVars' => $data['templateVars'],
            'attrs' => $this->_templates->formatAttributes(
                $data,
                ['name', 'type']
            ),
        ]);
    }

    /**
     * {@inheritDoc}
     */
    public function secureFields(array $data) {
        if (!isset($data['name']) || $data['name'] === '') {
            return [];
        }
        return [$data['name']];
    }
}
