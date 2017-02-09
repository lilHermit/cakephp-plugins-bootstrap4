<?php


namespace lilHermit\Bootstrap4\Test\TestCase\View\Helper;


use Cake\TestSuite\TestCase;
use Cake\View\View;
use lilHermit\Bootstrap4\View\Helper\FormHelper;

class BootstrapFormHelperTest extends TestCase {

    // TODO
    // - Add Test for error message on file input (customControls and  not)

    /**
     * @var FormHelper $Form
     */
    protected $Form;

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp() {

        parent::setUp();

        $this->Form = new FormHelper(new View());

    }

    public function testMultiCheckbox() {

        $result = $this->Form->multiCheckbox('checkbox1',
            [
                ['text' => 'First Checkbox', 'value' => 1],
                ['text' => 'Second Checkbox', 'value' => 2]
            ],
            [
                'default' => 2,
                'customControls' => false
            ]);
        $this->assertHtml([
            'input' => ['type' => 'hidden', 'name' => 'checkbox1', 'value' => ''],
            ['div' => ['class' => 'form-check']],

            ['label' => ['for' => 'checkbox1-1', 'class' => 'form-check-label']],
            [
                'input' => [
                    'type' => 'checkbox',
                    'name' => 'checkbox1[]',
                    'value' => '1',
                    'id' => 'checkbox1-1',
                    'class' => 'form-check-input'
                ]
            ],
            'First Checkbox',
            '/label',
            '/div',
            ['div' => ['class' => 'form-check']],
            ['label' => ['for' => 'checkbox1-2', 'class' => 'form-check-label selected']],
            [
                'input' => [
                    'type' => 'checkbox',
                    'name' => 'checkbox1[]',
                    'value' => '2',
                    'checked' => 'checked',
                    'id' => 'checkbox1-2',
                    'class' => 'form-check-input'
                ]
            ],
            'Second Checkbox',
            '/label',
            '/div',
        ], $result);

        $result = $this->Form->multiCheckbox('checkbox1',
            [
                ['text' => 'First Checkbox', 'value' => 1],
                ['text' => 'Second Checkbox', 'value' => 2]
            ],
            [
                'default' => 2,
                'customControls' => true
            ]);
        $this->assertHtml([
            'input' => ['type' => 'hidden', 'name' => 'checkbox1', 'value' => ''],

            ['label' => ['for' => 'checkbox1-1', 'class' => 'custom-control custom-checkbox']],
            [
                'input' => [
                    'type' => 'checkbox',
                    'name' => 'checkbox1[]',
                    'value' => '1',
                    'id' => 'checkbox1-1',
                    'class' => 'custom-control-input'
                ]
            ],
            ['span' => ['class' => 'custom-control-indicator']],
            '/span',
            ['span' => ['class' => 'custom-control-description']],
            'First Checkbox',
            '/span',
            '/label',

            ['label' => ['for' => 'checkbox1-2', 'class' => 'custom-control custom-checkbox selected']],
            [
                'input' => [
                    'type' => 'checkbox',
                    'name' => 'checkbox1[]',
                    'value' => '2',
                    'checked' => 'checked',
                    'id' => 'checkbox1-2',
                    'class' => 'custom-control-input'
                ]
            ],
            ['span' => ['class' => 'custom-control-indicator']],
            '/span',
            ['span' => ['class' => 'custom-control-description']],
            'Second Checkbox',
            '/span',
            '/label',
        ], $result);
    }

    public function testMultiCheckboxViaSelect() {
        $result = $this->Form->select(
            'checkbox1',
            [
                ['text' => 'First Checkbox', 'value' => 1],
                ['text' => 'Second Checkbox', 'value' => 2]
            ],
            [
                'label' => 'My checkboxes',
                'default' => 2,
                'multiple' => 'checkbox',
                'customControls' => false
            ]);
        $this->assertHtml([
            'input' => ['type' => 'hidden', 'name' => 'checkbox1', 'value' => ''],
            ['div' => ['class' => 'form-check']],

            ['label' => ['for' => 'checkbox1-1', 'class' => 'form-check-label']],
            [
                'input' => [
                    'type' => 'checkbox',
                    'name' => 'checkbox1[]',
                    'value' => '1',
                    'id' => 'checkbox1-1',
                    'class' => 'form-check-input'
                ]
            ],
            'First Checkbox',
            '/label',
            '/div',
            ['div' => ['class' => 'form-check']],
            ['label' => ['for' => 'checkbox1-2', 'class' => 'form-check-label selected']],
            [
                'input' => [
                    'type' => 'checkbox',
                    'name' => 'checkbox1[]',
                    'value' => '2',
                    'checked' => 'checked',
                    'id' => 'checkbox1-2',
                    'class' => 'form-check-input'
                ]
            ],
            'Second Checkbox',
            '/label',
            '/div',
        ], $result);

        $result = $this->Form->select(
            'checkbox1',
            [
                ['text' => 'First Checkbox', 'value' => 1],
                ['text' => 'Second Checkbox', 'value' => 2]
            ],
            [
                'label' => 'My checkboxes',
                'default' => 2,
                'multiple' => 'checkbox',
                'customControls' => true
            ]);

        $this->assertHtml([
            'input' => ['type' => 'hidden', 'name' => 'checkbox1', 'value' => ''],
            ['label' => ['for' => 'checkbox1-1', 'class' => 'custom-control custom-checkbox']],
            [
                'input' => [
                    'type' => 'checkbox',
                    'name' => 'checkbox1[]',
                    'value' => '1',
                    'id' => 'checkbox1-1',
                    'class' => 'custom-control-input'
                ]
            ],
            ['span' => ['class' => 'custom-control-indicator']],
            '/span',
            ['span' => ['class' => 'custom-control-description']],
            'First Checkbox',
            '/span',
            '/label',

            ['label' => ['for' => 'checkbox1-2', 'class' => 'custom-control custom-checkbox selected']],
            [
                'input' => [
                    'type' => 'checkbox',
                    'name' => 'checkbox1[]',
                    'value' => '2',
                    'checked' => 'checked',
                    'id' => 'checkbox1-2',
                    'class' => 'custom-control-input'
                ]
            ],
            ['span' => ['class' => 'custom-control-indicator']],
            '/span',
            ['span' => ['class' => 'custom-control-description']],
            'Second Checkbox',
            '/span',
            '/label',
        ], $result);
    }

    public function testMultiCheckboxViaInput() {

        $result = $this->Form->input('checkbox1', [
            'label' => 'My checkboxes',
            'default' => 2,
            'multiple' => 'checkbox',
            'type' => 'select',
            'options' => [
                ['text' => 'First Checkbox', 'value' => 1],
                ['text' => 'Second Checkbox', 'value' => 2]
            ],
            'customControls' => false
        ]);
        $this->assertHtml([
            'div' => ['class' => 'form-group'],
            ['label' => ['for' => 'checkbox1']],
            'My checkboxes',
            '/label',
            'input' => ['type' => 'hidden', 'name' => 'checkbox1', 'value' => ''],
            ['div' => ['class' => 'form-check']],

            ['label' => ['for' => 'checkbox1-1', 'class' => 'form-check-label']],
            [
                'input' => [
                    'type' => 'checkbox',
                    'name' => 'checkbox1[]',
                    'value' => '1',
                    'id' => 'checkbox1-1',
                    'class' => 'form-check-input'
                ]
            ],
            'First Checkbox',
            '/label',
            '/div',
            ['div' => ['class' => 'form-check']],
            ['label' => ['for' => 'checkbox1-2', 'class' => 'form-check-label selected']],
            [
                'input' => [
                    'type' => 'checkbox',
                    'name' => 'checkbox1[]',
                    'value' => '2',
                    'checked' => 'checked',
                    'id' => 'checkbox1-2',
                    'class' => 'form-check-input'
                ]
            ],
            'Second Checkbox',
            '/label',
            '/div',
            '/div'
        ], $result);

        $result = $this->Form->input('checkbox1', [
            'label' => 'My checkboxes',
            'default' => 2,
            'multiple' => 'checkbox',
            'type' => 'select',
            'options' => [
                ['text' => 'First Checkbox', 'value' => 1],
                ['text' => 'Second Checkbox', 'value' => 2]
            ]
        ]);
        $this->assertHtml([
            'div' => ['class' => 'form-group clearfix'],
            ['label' => ['for' => 'checkbox1']],
            'My checkboxes',
            '/label',
            ['div' => ['class' => 'custom-controls-stacked']],
            'input' => ['type' => 'hidden', 'name' => 'checkbox1', 'value' => ''],

            ['label' => ['for' => 'checkbox1-1', 'class' => 'custom-control custom-checkbox']],
            [
                'input' => [
                    'type' => 'checkbox',
                    'name' => 'checkbox1[]',
                    'value' => '1',
                    'id' => 'checkbox1-1',
                    'class' => 'custom-control-input'
                ]
            ],
            ['span' => ['class' => 'custom-control-indicator']],
            '/span',
            ['span' => ['class' => 'custom-control-description']],
            'First Checkbox',
            '/span',
            '/label',

            ['label' => ['for' => 'checkbox1-2', 'class' => 'custom-control custom-checkbox selected']],
            [
                'input' => [
                    'type' => 'checkbox',
                    'name' => 'checkbox1[]',
                    'value' => '2',
                    'checked' => 'checked',
                    'id' => 'checkbox1-2',
                    'class' => 'custom-control-input'
                ]
            ],
            ['span' => ['class' => 'custom-control-indicator']],
            '/span',
            ['span' => ['class' => 'custom-control-description']],
            'Second Checkbox',
            '/span',
            '/label',
            '/div',
            '/div'
        ], $result);

    }

    public function testCheckboxSingleViaInput() {
        $result = $this->Form->input('terms_agreed', [
            'label' => 'I agree to the terms of use',
            'type' => 'checkbox',
            'customControls' => false
        ]);
        $this->assertHtml([
            'div' => ['class' => 'form-check'],
            'input' => ['type' => 'hidden', 'name' => 'terms_agreed', 'value' => '0'],
            'label' => ['for' => 'terms-agreed', 'class' => 'form-check-label'],
            [
                'input' => [
                    'type' => 'checkbox',
                    'name' => 'terms_agreed',
                    'value' => '1',
                    'id' => 'terms-agreed',
                    'class' => 'form-check-input'
                ]
            ],
            'I agree to the terms of use',
            '/label',
            '/div'
        ], $result);

        $result = $this->Form->input('terms_agreed', [
            'label' => 'I agree to the terms of use',
            'type' => 'checkbox',
            'customControls' => true
        ]);

        $this->assertHtml([
            'div' => ['class' => 'form-group clearfix'],
            'input' => ['type' => 'hidden', 'name' => 'terms_agreed', 'value' => '0'],
            'label' => ['for' => 'terms-agreed', 'class' => 'custom-control custom-checkbox'],
            [
                'input' => [
                    'type' => 'checkbox',
                    'name' => 'terms_agreed',
                    'value' => '1',
                    'id' => 'terms-agreed',
                    'class' => 'custom-control-input'
                ]
            ],
            ['span' => ['class' => 'custom-control-indicator']],
            '/span',
            ['span' => ['class' => 'custom-control-description']],
            'I agree to the terms of use',
            '/span',
            '/label',
            '/div'
        ], $result);

    }

    public function testRadioViaInput() {

        $result = $this->Form->input(
            'radio1',
            [
                'label' => 'My Radios',
                'default' => 2,
                'type' => 'radio',
                'options' => [
                    ['text' => 'First Radio', 'value' => 1],
                    ['text' => 'Second Radio', 'value' => 2]
                ],
                'customControls' => false
            ]
        );
        $this->assertHtml([
            'div' => ['class' => 'form-group'],
            '<label',
            'My Radios',
            '/label',
            'input' => ['type' => 'hidden', 'name' => 'radio1', 'value' => ''],
            ['div' => ['class' => 'form-check']],

            ['label' => ['for' => 'radio1-1', 'class' => 'form-check-label']],
            [
                'input' => [
                    'type' => 'radio',
                    'name' => 'radio1',
                    'value' => '1',
                    'id' => 'radio1-1',
                    'class' => 'form-check-input'
                ]
            ],
            'First Radio',
            '/label',
            '/div',
            ['div' => ['class' => 'form-check']],
            ['label' => ['for' => 'radio1-2', 'class' => 'form-check-label selected']],
            [
                'input' => [
                    'type' => 'radio',
                    'name' => 'radio1',
                    'value' => '2',
                    'checked' => 'checked',
                    'id' => 'radio1-2',
                    'class' => 'form-check-input'
                ]
            ],
            'Second Radio',
            '/label',
            '/div',
            '/div'
        ], $result);

        $result = $this->Form->input(
            'radio1',
            [
                'label' => 'My Radios',
                'default' => 2,
                'type' => 'radio',
                'options' => [
                    ['text' => 'First Radio', 'value' => 1],
                    ['text' => 'Second Radio', 'value' => 2]
                ],
                'customControls' => true
            ]
        );
        $this->assertHtml([
            'div' => ['class' => 'form-group'],
            '<label',
            'My Radios',
            '/label',
            'input' => ['type' => 'hidden', 'name' => 'radio1', 'value' => ''],
            ['label' => ['for' => 'radio1-1', 'class' => 'custom-control custom-radio']],
            [
                'input' => [
                    'type' => 'radio',
                    'name' => 'radio1',
                    'value' => '1',
                    'id' => 'radio1-1',
                    'class' => 'custom-control-input'
                ]
            ],
            ['span' => ['class' => 'custom-control-indicator']],
            '/span',
            ['span' => ['class' => 'custom-control-description']],
            'First Radio',
            '/span',
            '/label',

            ['label' => ['for' => 'radio1-2', 'class' => 'custom-control custom-radio selected']],
            [
                'input' => [
                    'type' => 'radio',
                    'name' => 'radio1',
                    'value' => '2',
                    'id' => 'radio1-2',
                    'checked' => 'checked',
                    'class' => 'custom-control-input'
                ]
            ],
            ['span' => ['class' => 'custom-control-indicator']],
            '/span',
            ['span' => ['class' => 'custom-control-description']],
            'Second Radio',
            '/span',
            '/label',
            '/div'
        ], $result);
    }

    public function testRadio() {

        $result = $this->Form->radio(
            'radio1',
            [
                ['text' => 'First Radio', 'value' => 1],
                ['text' => 'Second Radio', 'value' => 2]
            ],
            [
                'default' => 2,
                'customControls' => false
            ]
        );
        $this->assertHtml([
            'input' => ['type' => 'hidden', 'name' => 'radio1', 'value' => ''],
            ['div' => ['class' => 'form-check']],

            ['label' => ['for' => 'radio1-1', 'class' => 'form-check-label']],
            [
                'input' => [
                    'type' => 'radio',
                    'name' => 'radio1',
                    'value' => '1',
                    'id' => 'radio1-1',
                    'class' => 'form-check-input'
                ]
            ],
            'First Radio',
            '/label',
            '/div',
            ['div' => ['class' => 'form-check']],
            ['label' => ['for' => 'radio1-2', 'class' => 'form-check-label selected']],
            [
                'input' => [
                    'type' => 'radio',
                    'name' => 'radio1',
                    'value' => '2',
                    'checked' => 'checked',
                    'id' => 'radio1-2',
                    'class' => 'form-check-input'
                ]
            ],
            'Second Radio',
            '/label',
            '/div',
        ], $result);

        $result = $this->Form->radio(
            'radio1',
            [
                ['text' => 'First Radio', 'value' => 1],
                ['text' => 'Second Radio', 'value' => 2]
            ],
            [
                'default' => 2,
                'customControls' => true
            ]
        );
        $this->assertHtml([
            'input' => ['type' => 'hidden', 'name' => 'radio1', 'value' => ''],

            ['label' => ['for' => 'radio1-1', 'class' => 'custom-control custom-radio']],
            [
                'input' => [
                    'type' => 'radio',
                    'name' => 'radio1',
                    'value' => '1',
                    'id' => 'radio1-1',
                    'class' => 'custom-control-input'
                ]
            ],
            ['span' => ['class' => 'custom-control-indicator']],
            '/span',
            ['span' => ['class' => 'custom-control-description']],
            'First Radio',
            '/span',
            '/label',

            ['label' => ['for' => 'radio1-2', 'class' => 'custom-control custom-radio selected']],
            [
                'input' => [
                    'type' => 'radio',
                    'name' => 'radio1',
                    'value' => '2',
                    'id' => 'radio1-2',
                    'checked' => 'checked',
                    'class' => 'custom-control-input'
                ]
            ],
            ['span' => ['class' => 'custom-control-indicator']],
            '/span',
            ['span' => ['class' => 'custom-control-description']],
            'Second Radio',
            '/span',
            '/label',


        ], $result);
    }

    /**
     *
     * testInputHelp
     *
     * Test to make sure the help text is render correctly
     *
     * Test string and array, including adding custom attributes (inc class)
     *
     * @return void
     */
    public function testInputHelp() {
        $result = $this->Form->control('first_name', [
            'help' => 'Please enter the users first name'
        ]);
        $this->assertHtml([
            'div' => ['class' => 'form-group'],
            ['label' => ['class' => 'col-form-label', 'for' => 'first-name']],
            'First Name',
            '/label',
            'input' => [
                'type' => 'text',
                'name' => 'first_name',
                'id' => 'first-name',
                'class' => 'form-control'
            ],
            'small' => ['class' => 'form-text text-muted'],
            'Please enter the users first name',
            '/small',
            '/div'
        ], $result);

        $result = $this->Form->control('first_name', [
            'help' => [
                'text' => 'Please enter the users first name',
                'class' => 'custom-class',
                'data-ref' => 'custom-attr'
            ]
        ]);
        $this->assertHtml([
            'div' => ['class' => 'form-group'],
            ['label' => ['class' => 'col-form-label', 'for' => 'first-name']],
            'First Name',
            '/label',
            'input' => [
                'type' => 'text',
                'name' => 'first_name',
                'id' => 'first-name',
                'class' => 'form-control'
            ],
            'small' => ['class' => 'custom-class form-text text-muted', 'data-ref' => 'custom-attr'],
            'Please enter the users first name',
            '/small',
            '/div'
        ], $result);
    }

    /**
     * testInputHelpExclude
     *
     * Test to make sure the help text is not rendered default and if explicitly set to false
     *
     * @return void
     */
    public function testInputHelpExclude() {

        $result = $this->Form->control('first_name');
        $this->assertHtml([
            'div' => ['class' => 'form-group'],
            ['label' => ['class' => 'col-form-label', 'for' => 'first-name']],
            'First Name',
            '/label',
            'input' => [
                'type' => 'text',
                'name' => 'first_name',
                'id' => 'first-name',
                'class' => 'form-control'
            ],
            '/div'
        ], $result);

        $result = $this->Form->control('first_name', [
            'help' => false
        ]);
        $this->assertHtml([
            'div' => ['class' => 'form-group'],
            ['label' => ['class' => 'col-form-label', 'for' => 'first-name']],
            'First Name',
            '/label',
            'input' => [
                'type' => 'text',
                'name' => 'first_name',
                'id' => 'first-name',
                'class' => 'form-control'
            ],
            '/div'
        ], $result);
    }

    /**
     * testFileUpload method
     *
     * Test generation of a file upload input.
     *
     * @return void
     */
    public function testFileUpload() {

        $result = $this->Form->file('Model.upload', ['customControls' => false]);
        $this->assertHtml([
            'input' => ['type' => 'file', 'name' => 'Model[upload]'],
            'preg:/$/'
        ], $result);

        $result = $this->Form->file('Model.upload', ['customControls' => true]);
        $this->assertHtml([
            'input' => ['type' => 'file', 'name' => 'Model[upload]', 'class' => 'custom-file-input'],
            'span' => ['class' => 'custom-file-control'],
            '/span'

        ], $result);
    }

    /**
     * testFileUploadViaInput method
     *
     * Test generation of a file upload input.
     *
     * @return void
     */
    public function testFileUploadViaInput() {

        $result = $this->Form->input('Model.upload', ['type' => 'file', 'customControls' => false]);
        $this->assertHtml([
            'div' => ['class' => 'input file'],
            'label' => ['for' => 'model-upload'],
            'Upload',
            '/label',
            ['input' => [
                'type' => 'file',
                'name' => 'Model[upload]',
                'id' => 'model-upload'
            ]],
            '/div'
        ], $result);

        $result = $this->Form->input('Model.upload', ['type' => 'file', 'customControls' => true]);
        $this->assertHtml([
            'label' => ['class' => 'custom-file', 'for' => 'model-upload'],
            'input' => ['type' => 'file', 'name' => 'Model[upload]', 'class' => 'custom-file-input', 'id' => 'model-upload'],
            'span' => ['class' => 'custom-file-control'],
            '/span',
            '/label'

        ], $result);
    }
}