<?php


namespace LilHermit\Bootstrap4\Test\TestCase\View\Helper;


use Cake\Chronos\Chronos;
use Cake\TestSuite\TestCase;
use Cake\View\View;
use LilHermit\Bootstrap4\View\Helper\FormHelper;
use LilHermit\Bootstrap4\View\Helper\HtmlHelper;

class BootstrapFormHelperTest extends TestCase {

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

    public function tearDown() {
        parent::tearDown();

        unset($this->Form);
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

            [
                'input' => [
                    'type' => 'checkbox',
                    'name' => 'checkbox1[]',
                    'value' => '1',
                    'id' => 'checkbox1-1',
                    'class' => 'form-check-input'
                ]
            ],
            ['label' => ['for' => 'checkbox1-1', 'class' => 'form-check-label']],
            'First Checkbox',
            '/label',
            '/div',
            ['div' => ['class' => 'form-check']],
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
            ['label' => ['for' => 'checkbox1-2', 'class' => 'form-check-label selected']],
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

            ['div' => ['class' => 'custom-control custom-checkbox']],
            [
                'input' => [
                    'type' => 'checkbox',
                    'name' => 'checkbox1[]',
                    'value' => '1',
                    'id' => 'checkbox1-1',
                    'class' => 'custom-control-input'
                ]
            ],
            ['label' => ['for' => 'checkbox1-1', 'class' => 'custom-control-label']],
            'First Checkbox',
            '/label',
            '/div',

            ['div' => ['class' => 'custom-control custom-checkbox']],
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
            ['label' => ['for' => 'checkbox1-2', 'class' => 'custom-control-label selected']],
            'Second Checkbox',
            '/label',
            '/div'
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
            [
                'input' => [
                    'type' => 'checkbox',
                    'name' => 'checkbox1[]',
                    'value' => '1',
                    'id' => 'checkbox1-1',
                    'class' => 'form-check-input'
                ]
            ],
            ['label' => ['for' => 'checkbox1-1', 'class' => 'form-check-label']],
            'First Checkbox',
            '/label',
            '/div',
            ['div' => ['class' => 'form-check']],
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
            ['label' => ['for' => 'checkbox1-2', 'class' => 'form-check-label selected']],
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

            ['div' => ['class' => 'custom-control custom-checkbox']],
            [
                'input' => [
                    'type' => 'checkbox',
                    'name' => 'checkbox1[]',
                    'value' => '1',
                    'id' => 'checkbox1-1',
                    'class' => 'custom-control-input'
                ]
            ],
            ['label' => ['for' => 'checkbox1-1', 'class' => 'custom-control-label']],
            'First Checkbox',
            '/label',
            '/div',
            ['div' => ['class' => 'custom-control custom-checkbox']],

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
            ['label' => ['for' => 'checkbox1-2', 'class' => 'custom-control-label selected']],
            'Second Checkbox',
            '/label',
            '/div'
        ], $result);
    }

    public function testMultiCheckboxViaInput() {

        $result = $this->Form->control('checkbox1', [
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
            [
                'input' => [
                    'type' => 'checkbox',
                    'name' => 'checkbox1[]',
                    'value' => '1',
                    'id' => 'checkbox1-1',
                    'class' => 'form-check-input'
                ]
            ],
            ['label' => ['for' => 'checkbox1-1', 'class' => 'form-check-label']],
            'First Checkbox',
            '/label',
            '/div',
            ['div' => ['class' => 'form-check']],
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
            ['label' => ['for' => 'checkbox1-2', 'class' => 'form-check-label selected']],
            'Second Checkbox',
            '/label',
            '/div',
            '/div'
        ], $result);

        $result = $this->Form->control('checkbox1', [
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

            'input' => ['type' => 'hidden', 'name' => 'checkbox1', 'value' => ''],

            ['div' => ['class' => 'custom-control custom-checkbox']],
            [
                'input' => [
                    'type' => 'checkbox',
                    'name' => 'checkbox1[]',
                    'value' => '1',
                    'id' => 'checkbox1-1',
                    'class' => 'custom-control-input'
                ]
            ],
            ['label' => ['for' => 'checkbox1-1', 'class' => 'custom-control-label']],
            'First Checkbox',
            '/label',
            '/div',

            ['div' => ['class' => 'custom-control custom-checkbox']],
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
            ['label' => ['for' => 'checkbox1-2', 'class' => 'custom-control-label selected']],
            'Second Checkbox',
            '/label',
            '/div',
            '/div'
        ], $result);

    }

    public function testCheckboxSingleViaInput() {
        $result = $this->Form->control('terms_agreed', [
            'label' => 'I agree to the terms of use',
            'type' => 'checkbox',
            'customControls' => false
        ]);
        $this->assertHtml([
            'div' => ['class' => 'form-check'],
            'input' => ['type' => 'hidden', 'name' => 'terms_agreed', 'value' => '0'],
            [
                'input' => [
                    'type' => 'checkbox',
                    'name' => 'terms_agreed',
                    'value' => '1',
                    'id' => 'terms-agreed',
                    'class' => 'form-check-input'
                ]
            ],
            'label' => ['for' => 'terms-agreed', 'class' => 'form-check-label'],
            'I agree to the terms of use',
            '/label',
            '/div'
        ], $result);

        $result = $this->Form->control('terms_agreed', [
            'label' => 'I agree to the terms of use',
            'type' => 'checkbox',
            'customControls' => true
        ]);

        $this->assertHtml([
            'div' => ['class' => 'custom-control custom-checkbox'],
            'input' => ['type' => 'hidden', 'name' => 'terms_agreed', 'value' => '0'],
            [
                'input' => [
                    'type' => 'checkbox',
                    'name' => 'terms_agreed',
                    'value' => '1',
                    'id' => 'terms-agreed',
                    'class' => 'custom-control-input'
                ]
            ],
            'label' => ['for' => 'terms-agreed', 'class' => 'custom-control-label'],
            'I agree to the terms of use',
            '/label',
            '/div'
        ], $result);

    }

    public function testRadioViaInput() {

        $result = $this->Form->control(
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
            [
                'input' => [
                    'type' => 'radio',
                    'name' => 'radio1',
                    'value' => '1',
                    'id' => 'radio1-1',
                    'class' => 'form-check-input'
                ]
            ],
            ['label' => ['for' => 'radio1-1', 'class' => 'form-check-label']],
            'First Radio',
            '/label',
            '/div',
            ['div' => ['class' => 'form-check']],
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
            ['label' => ['for' => 'radio1-2', 'class' => 'form-check-label selected']],
            'Second Radio',
            '/label',
            '/div',
            '/div'
        ], $result);

        $result = $this->Form->control(
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
            ['div' => ['class' => 'custom-control custom-radio']],
            [
                'input' => [
                    'type' => 'radio',
                    'name' => 'radio1',
                    'value' => '1',
                    'id' => 'radio1-1',
                    'class' => 'custom-control-input'
                ]
            ],
            ['label' => ['for' => 'radio1-1', 'class' => 'custom-control-label']],
            'First Radio',
            '/label',
            '/div',

            ['div' => ['class' => 'custom-control custom-radio']],
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

            ['label' => ['for' => 'radio1-2', 'class' => 'custom-control-label selected']],
            'Second Radio',
            '/label',

            '/div',
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

            [
                'input' => [
                    'type' => 'radio',
                    'name' => 'radio1',
                    'value' => '1',
                    'id' => 'radio1-1',
                    'class' => 'form-check-input'
                ]
            ],
            ['label' => ['for' => 'radio1-1', 'class' => 'form-check-label']],
            'First Radio',
            '/label',
            '/div',
            ['div' => ['class' => 'form-check']],
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
            ['label' => ['for' => 'radio1-2', 'class' => 'form-check-label selected']],
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

            ['div' => ['class' => 'custom-control custom-radio']],
            [
                'input' => [
                    'type' => 'radio',
                    'name' => 'radio1',
                    'value' => '1',
                    'id' => 'radio1-1',
                    'class' => 'custom-control-input'
                ]
            ],
            ['label' => ['for' => 'radio1-1', 'class' => 'custom-control-label']],
            'First Radio',
            '/label',
            '/div',

            ['div' => ['class' => 'custom-control custom-radio']],
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
            ['label' => ['for' => 'radio1-2', 'class' => 'custom-control-label selected']],
            'Second Radio',
            '/label',
            '/div'

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

        // Test help after custom file form control
        $result = $this->Form->control('profileImage', [
            'help' => 'Upload a profile image for the forum',
            'type' => 'file'
        ]);
        $this->assertHtml([
            'div' => ['class' => 'form-group'],
            ['label' => ['for' => 'profileimage', 'class' => 'col-form-label d-block']],
            'Profile Image',
            '/label',
            'label' => ['class' => 'custom-file'],
            'input' => ['type' => 'file', 'name' => 'profileImage', 'class' => 'custom-file-input', 'id' => 'profileimage'],
            'span' => ['class' => 'custom-file-control'],
            '/span',
            '/label',
            'small' => ['class' => 'form-text text-muted'],
            'Upload a profile image for the forum',
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
            'input' => ['type' => 'file', 'name' => 'Model[upload]', 'class' => 'form-control-file'],
            'preg:/$/'
        ], $result);

        $result = $this->Form->file('Model.upload', ['customControls' => true]);
        $this->assertHtml([
            'label' => ['class' => 'custom-file'],
            'input' => ['type' => 'file', 'name' => 'Model[upload]', 'class' => 'custom-file-input'],
            'span' => ['class' => 'custom-file-control'],
            '/span',
            '/label'

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

        $result = $this->Form->control('Model.upload', ['type' => 'file', 'customControls' => false]);
        $this->assertHtml([
            'div' => ['class' => 'form-group'],
            'label' => ['for' => 'model-upload'],
            'Upload',
            '/label',
            ['input' => [
                'type' => 'file',
                'name' => 'Model[upload]',
                'id' => 'model-upload',
                'class' => 'form-control-file'
            ]],
            '/div'
        ], $result);

        $result = $this->Form->control('Model.upload', ['type' => 'file', 'customControls' => true]);
        $this->assertHtml([
            'div' => ['class' => 'form-group'],
            ['label' => ['for' => 'model-upload', 'class' => 'col-form-label d-block']],
            'Upload',
            '/label',
            'label' => ['class' => 'custom-file'],
            'input' => ['type' => 'file', 'name' => 'Model[upload]', 'class' => 'custom-file-input', 'id' => 'model-upload'],
            'span' => ['class' => 'custom-file-control'],
            '/span',
            '/label',
            '/div'
        ], $result);
    }

    /**
     * testInputPrefixOnlyRendering
     *
     * Tests the rendering of Prefix only
     */
    public function testInputPrefixOnlyRendering() {

        $result = $this->Form->control('Donation', [
            'prefix' => '£'
        ]);
        $this->assertHtml([
            'div' => ['class' => 'form-group'],
            ['label' => ['class' => 'col-form-label', 'for' => 'donation']],
            'Donation',
            '/label',
            ['div' => ['class' => 'input-group']],
            ['span' => ['class' => 'input-group-addon']],
            '£',
            '/span',
            'input' => [
                'type' => 'text',
                'name' => 'Donation',
                'id' => 'donation',
                'class' => 'form-control'
            ],
            '/div',
            '/div'
        ], $result);
    }

    /**
     * testInputSuffixOnlyRendering
     *
     * Tests the rendering of Suffix only
     */
    public function testInputSuffixOnlyRendering() {

        $result = $this->Form->control('Donation', [
            'suffix' => '.00'
        ]);
        $this->assertHtml([
            'div' => ['class' => 'form-group'],
            ['label' => ['class' => 'col-form-label', 'for' => 'donation']],
            'Donation',
            '/label',
            ['div' => ['class' => 'input-group']],
            'input' => [
                'type' => 'text',
                'name' => 'Donation',
                'id' => 'donation',
                'class' => 'form-control'
            ],
            ['span' => ['class' => 'input-group-addon']],
            '.00',
            '/span',
            '/div',
            '/div'
        ], $result);
    }

    /**
     * testInputPrefixSuffixRendering
     *
     * Tests the rendering of Prefix and Suffix
     */
    public function testInputPrefixSuffixRendering() {

        $result = $this->Form->control('Donation', [
            'prefix' => '£',
            'suffix' => '.00'
        ]);
        $this->assertHtml([
            'div' => ['class' => 'form-group'],
            ['label' => ['class' => 'col-form-label', 'for' => 'donation']],
            'Donation',
            '/label',
            ['div' => ['class' => 'input-group']],
            ['span' => ['class' => 'input-group-addon']],
            '£',
            '/span',
            'input' => [
                'type' => 'text',
                'name' => 'Donation',
                'id' => 'donation',
                'class' => 'form-control'
            ],
            ['span' => ['class' => 'input-group-addon']],
            '.00',
            '/span',
            '/div',

            '/div'
        ], $result);
    }

    /**
     * testInputPrefixSuffixTypeRendering
     *
     * Tests the rendering of Prefix/Suffix Type setting
     */
    public function testInputPrefixSuffixTypeRendering() {

        $htmlHelper = new HtmlHelper(new View());

        $button = $htmlHelper->button('<i class="fa fa-eye fa-lg" aria-hidden="true"></i>', null, [
            'type' => 'button',
            'data-toggle' => 'button',
            'id' => 'show-password',
            'secondary' => true
        ]);

        $result = $this->Form->control('Password', [
            'suffix' => [[
                'text' => $button,
                'escape' => false,
                'type' => 'button'
            ]]
        ]);
        $this->assertHtml([
            'div' => ['class' => 'form-group'],
            ['label' => ['class' => 'col-form-label', 'for' => 'password']],
            'Password',
            '/label',
            ['div' => ['class' => 'input-group']],
            'input' => [
                'type' => 'text',
                'name' => 'Password',
                'id' => 'password',
                'class' => 'form-control'
            ],
            ['span' => ['class' => 'input-group-btn']],
            'button' => ['class' => 'btn btn-secondary', 'id' => 'show-password', 'data-toggle' => 'button', 'type' => 'button'],
            'i' => ['class' => 'fa fa-eye fa-lg', 'aria-hidden' => 'true'],
            '/i',
            '/button',
            '/span',
            '/div',
            '/div'
        ], $result);

        $result = $this->Form->control('Password', [
            'suffix' => [[
                'text' => $button,
                'escape' => false,
                'type' => 'btn']
            ]
        ]);
        $this->assertHtml([
            'div' => ['class' => 'form-group'],
            ['label' => ['class' => 'col-form-label', 'for' => 'password']],
            'Password',
            '/label',
            ['div' => ['class' => 'input-group']],
            'input' => [
                'type' => 'text',
                'name' => 'Password',
                'id' => 'password',
                'class' => 'form-control'
            ],
            ['span' => ['class' => 'input-group-btn']],
            'button' => ['class' => 'btn btn-secondary', 'id' => 'show-password', 'data-toggle' => 'button', 'type' => 'button'],
            'i' => ['class' => 'fa fa-eye fa-lg', 'aria-hidden' => 'true'],
            '/i',
            '/button',
            '/span',
            '/div',
            '/div'
        ], $result);

        $result = $this->Form->control('Password', [
            'suffix' => [[
                'text' => $button,
                'escape' => false,
                'type' => 'addon']
            ]
        ]);
        $this->assertHtml([
            'div' => ['class' => 'form-group'],
            ['label' => ['class' => 'col-form-label', 'for' => 'password']],
            'Password',
            '/label',
            ['div' => ['class' => 'input-group']],
            'input' => [
                'type' => 'text',
                'name' => 'Password',
                'id' => 'password',
                'class' => 'form-control'
            ],
            ['span' => ['class' => 'input-group-addon']],
            'button' => ['class' => 'btn btn-secondary', 'id' => 'show-password', 'data-toggle' => 'button', 'type' => 'button'],
            'i' => ['class' => 'fa fa-eye fa-lg', 'aria-hidden' => 'true'],
            '/i',
            '/button',
            '/span',
            '/div',
            '/div'
        ], $result);

        $result = $this->Form->control('Password', [
            'suffix' => [[
                'text' => $button,
                'escape' => false,
                'type' => 'unknown']
            ]
        ]);
        $this->assertHtml([
            'div' => ['class' => 'form-group'],
            ['label' => ['class' => 'col-form-label', 'for' => 'password']],
            'Password',
            '/label',
            ['div' => ['class' => 'input-group']],
            'input' => [
                'type' => 'text',
                'name' => 'Password',
                'id' => 'password',
                'class' => 'form-control'
            ],
            ['span' => ['class' => 'input-group-addon']],
            'button' => ['class' => 'btn btn-secondary', 'id' => 'show-password', 'data-toggle' => 'button', 'type' => 'button'],
            'i' => ['class' => 'fa fa-eye fa-lg', 'aria-hidden' => 'true'],
            '/i',
            '/button',
            '/span',
            '/div',
            '/div'
        ], $result);
    }

    /**
     * testInputPrefixMultipleRendering
     *
     * Tests the rendering of multiple Prefix
     */
    public function testInputPrefixMultipleRendering() {

        $result = $this->Form->control('Donation', [
            'prefix' => ['£', '$']
        ]);
        $this->assertHtml([
            'div' => ['class' => 'form-group'],
            ['label' => ['class' => 'col-form-label', 'for' => 'donation']],
            'Donation',
            '/label',
            ['div' => ['class' => 'input-group']],
            ['span' => ['class' => 'input-group-addon']],
            '£',
            '/span',
            ['span' => ['class' => 'input-group-addon']],
            '$',
            '/span',
            'input' => [
                'type' => 'text',
                'name' => 'Donation',
                'id' => 'donation',
                'class' => 'form-control'
            ],
            '/div',

            '/div'
        ], $result);

        $result = $this->Form->control('Donation', [
            'prefix' => [['text' => '£'], ['text' => '$']]
        ]);

        $this->assertHtml([
            'div' => ['class' => 'form-group'],
            ['label' => ['class' => 'col-form-label', 'for' => 'donation']],
            'Donation',
            '/label',
            ['div' => ['class' => 'input-group']],
            ['span' => ['class' => 'input-group-addon']],
            '£',
            '/span',
            ['span' => ['class' => 'input-group-addon']],
            '$',
            '/span',
            'input' => [
                'type' => 'text',
                'name' => 'Donation',
                'id' => 'donation',
                'class' => 'form-control'
            ],
            '/div',

            '/div'
        ], $result);

        $result = $this->Form->control('Donation', [
            'prefix' => [['text' => '£', 'class' => 'custom', 'id' => 'donation1'], ['text' => '$']]
        ]);
        $this->assertHtml([
            'div' => ['class' => 'form-group'],
            ['label' => ['class' => 'col-form-label', 'for' => 'donation']],
            'Donation',
            '/label',
            ['div' => ['class' => 'input-group']],
            ['span' => ['class' => 'custom input-group-addon', 'id' => 'donation1']],
            '£',
            '/span',
            ['span' => ['class' => 'input-group-addon']],
            '$',
            '/span',
            'input' => [
                'type' => 'text',
                'name' => 'Donation',
                'id' => 'donation',
                'class' => 'form-control'
            ],
            '/div',

            '/div'
        ], $result);
    }

    /**
     * testInputSuffixMultipleRendering
     *
     * Tests the rendering of multiple suffix
     */
    public function testInputSuffixMultipleRendering() {

        $result = $this->Form->control('Donation', [
            'suffix' => ['.00', 'Go']
        ]);
        $this->assertHtml([
            'div' => ['class' => 'form-group'],
            ['label' => ['class' => 'col-form-label', 'for' => 'donation']],
            'Donation',
            '/label',
            ['div' => ['class' => 'input-group']],
            'input' => [
                'type' => 'text',
                'name' => 'Donation',
                'id' => 'donation',
                'class' => 'form-control'
            ],
            ['span' => ['class' => 'input-group-addon']],
            '.00',
            '/span',
            ['span' => ['class' => 'input-group-addon']],
            'Go',
            '/span',

            '/div',

            '/div'
        ], $result);

        $result = $this->Form->control('Donation', [
            'suffix' => [['text' => '.00'], ['text' => 'Go']]
        ]);

        $this->assertHtml([
            'div' => ['class' => 'form-group'],
            ['label' => ['class' => 'col-form-label', 'for' => 'donation']],
            'Donation',
            '/label',
            ['div' => ['class' => 'input-group']],
            'input' => [
                'type' => 'text',
                'name' => 'Donation',
                'id' => 'donation',
                'class' => 'form-control'
            ],
            ['span' => ['class' => 'input-group-addon']],
            '.00',
            '/span',
            ['span' => ['class' => 'input-group-addon']],
            'Go',
            '/span',

            '/div',

            '/div'
        ], $result);

        $result = $this->Form->control('Donation', [
            'suffix' => [['text' => '.00', 'class' => 'custom', 'id' => 'donation1'], ['text' => 'Go']]
        ]);
        $this->assertHtml([
            'div' => ['class' => 'form-group'],
            ['label' => ['class' => 'col-form-label', 'for' => 'donation']],
            'Donation',
            '/label',
            ['div' => ['class' => 'input-group']],
            'input' => [
                'type' => 'text',
                'name' => 'Donation',
                'id' => 'donation',
                'class' => 'form-control'
            ],
            ['span' => ['class' => 'custom input-group-addon', 'id' => 'donation1']],
            '.00',
            '/span',
            ['span' => ['class' => 'input-group-addon']],
            'Go',
            '/span',
            '/div',

            '/div'
        ], $result);
    }

    /**
     * testInputPrefixSuffixMultipleRendering
     *
     * Tests the rendering of multiple prefix and suffix
     */
    public function testInputPrefixSuffixMultipleRendering() {

        $result = $this->Form->control('Donation', [
            'prefix' => ['£', '$'],
            'suffix' => ['.00', 'Go']
        ]);
        $this->assertHtml([
            'div' => ['class' => 'form-group'],
            ['label' => ['class' => 'col-form-label', 'for' => 'donation']],
            'Donation',
            '/label',
            ['div' => ['class' => 'input-group']],
            ['span' => ['class' => 'input-group-addon']],
            '£',
            '/span',
            ['span' => ['class' => 'input-group-addon']],
            '$',
            '/span',
            'input' => [
                'type' => 'text',
                'name' => 'Donation',
                'id' => 'donation',
                'class' => 'form-control'
            ],
            ['span' => ['class' => 'input-group-addon']],
            '.00',
            '/span',
            ['span' => ['class' => 'input-group-addon']],
            'Go',
            '/span',
            '/div',
            '/div'
        ], $result);

        $result = $this->Form->control('Donation', [
            'prefix' => [['text' => '£'], ['text' => '$']],
            'suffix' => [['text' => '.00'], ['text' => 'Go']]
        ]);
        $this->assertHtml([
            'div' => ['class' => 'form-group'],
            ['label' => ['class' => 'col-form-label', 'for' => 'donation']],
            'Donation',
            '/label',
            ['div' => ['class' => 'input-group']],
            ['span' => ['class' => 'input-group-addon']],
            '£',
            '/span',
            ['span' => ['class' => 'input-group-addon']],
            '$',
            '/span',
            'input' => [
                'type' => 'text',
                'name' => 'Donation',
                'id' => 'donation',
                'class' => 'form-control'
            ],
            ['span' => ['class' => 'input-group-addon']],
            '.00',
            '/span',
            ['span' => ['class' => 'input-group-addon']],
            'Go',
            '/span',
            '/div',
            '/div'
        ], $result);

        $result = $this->Form->control('Donation', [
            'prefix' => [['text' => '£', 'class' => 'custom', 'id' => 'donation1'], ['text' => '$']],
            'suffix' => [['text' => '.00', 'class' => 'custom', 'id' => 'donation1'], ['text' => 'Go']]
        ]);
        $this->assertHtml([
            'div' => ['class' => 'form-group'],
            ['label' => ['class' => 'col-form-label', 'for' => 'donation']],
            'Donation',
            '/label',
            ['div' => ['class' => 'input-group']],
            ['span' => ['class' => 'custom input-group-addon', 'id' => 'donation1']],
            '£',
            '/span',
            ['span' => ['class' => 'input-group-addon']],
            '$',
            '/span',
            'input' => [
                'type' => 'text',
                'name' => 'Donation',
                'id' => 'donation',
                'class' => 'form-control'
            ],
            ['span' => ['class' => 'custom input-group-addon', 'id' => 'donation1']],
            '.00',
            '/span',
            ['span' => ['class' => 'input-group-addon']],
            'Go',
            '/span',
            '/div',
            '/div'
        ], $result);
    }

    /**
     * testInputPrefixSuffixEscapingRendering
     *
     * Tests the rendering of Prefix/Suffix escaping
     */
    public function testInputPrefixSuffixEscapingRendering() {

        $result = $this->Form->control('Donation', [
            'prefix' => ['text' => '<b>text</b>', 'escape' => true]
        ]);
        $this->assertHtml([
            'div' => ['class' => 'form-group'],
            ['label' => ['class' => 'col-form-label', 'for' => 'donation']],
            'Donation',
            '/label',
            ['div' => ['class' => 'input-group']],
            ['span' => ['class' => 'input-group-addon']],
            '&lt;b&gt;text&lt;/b&gt;',
            '/span',
            'input' => [
                'type' => 'text',
                'name' => 'Donation',
                'id' => 'donation',
                'class' => 'form-control'
            ],
            '/div',
            '/div'
        ], $result);

        $result = $this->Form->control('Donation', [
            'prefix' => ['text' => '<b>text</b>']
        ]);
        $this->assertHtml([
            'div' => ['class' => 'form-group'],
            ['label' => ['class' => 'col-form-label', 'for' => 'donation']],
            'Donation',
            '/label',
            ['div' => ['class' => 'input-group']],
            ['span' => ['class' => 'input-group-addon']],
            '&lt;b&gt;text&lt;/b&gt;',
            '/span',
            'input' => [
                'type' => 'text',
                'name' => 'Donation',
                'id' => 'donation',
                'class' => 'form-control'
            ],
            '/div',
            '/div'
        ], $result);

    }

    /**
     * testInputPrefixSuffixSingleItemArrayRendering
     *
     * Tests the rendering of Prefix/Suffix with single array item
     */
    public function testInputPrefixSuffixSingleItemArrayRendering() {

        $result = $this->Form->control('Donation', [
            'prefix' =>
                [
                    'text' => 'text',
                    'class' => 'my-class'
                ]
        ]);
        $this->assertHtml([
            'div' => ['class' => 'form-group'],
            ['label' => ['class' => 'col-form-label', 'for' => 'donation']],
            'Donation',
            '/label',
            ['div' => ['class' => 'input-group']],
            ['span' => ['class' => 'my-class input-group-addon']],
            'text',
            '/span',
            'input' => [
                'type' => 'text',
                'name' => 'Donation',
                'id' => 'donation',
                'class' => 'form-control'
            ],
            '/div',
            '/div'
        ], $result);
    }

    /**
     * testControlPrefixSuffixContainerAttrRendering
     *
     * Tests the rendering of Prefix/Suffix container attributes
     */
    public function testControlPrefixSuffixContainerAttrRendering() {

        $result = $this->Form->control('Donation', [
            'prefix' =>
                [
                    [
                        'text' => 'text',
                        'class' => 'my-class',
                        'container' => ['class' => 'container-class']
                    ],

                    [
                        'text' => 'text2',
                        'class' => 'my-class2',
                        'container' => ['class' => 'container-class2', 'random_attribute' => 'true']
                    ]
                ]

        ]);
        $this->assertHtml([
            'div' => ['class' => 'form-group'],
            ['label' => ['class' => 'col-form-label', 'for' => 'donation']],
            'Donation',
            '/label',
            ['div' => ['class' => 'container-class input-group', 'random_attribute' => 'true']],
            ['span' => ['class' => 'my-class input-group-addon']],
            'text',
            '/span',
            ['span' => ['class' => 'my-class2 input-group-addon']],
            'text2',
            '/span',
            'input' => [
                'type' => 'text',
                'name' => 'Donation',
                'id' => 'donation',
                'class' => 'form-control'
            ],
            '/div',
            '/div'
        ], $result);

        $result = $this->Form->control('Donation', [
            'prefix' =>
                [
                    'text' => 'text',
                    'class' => 'my-class',
                    'container' => ['class' => 'container-class']
                ]

        ]);
        $this->assertHtml([
            'div' => ['class' => 'form-group'],
            ['label' => ['class' => 'col-form-label', 'for' => 'donation']],
            'Donation',
            '/label',
            ['div' => ['class' => 'container-class input-group']],
            ['span' => ['class' => 'my-class input-group-addon']],
            'text',
            '/span',
            'input' => [
                'type' => 'text',
                'name' => 'Donation',
                'id' => 'donation',
                'class' => 'form-control'
            ],
            '/div',
            '/div'
        ], $result);
    }


    /**
     * testInputPrefixSuffixSizeOptionRendering
     *
     * Tests the rendering of Prefix/Suffix with size option
     */
    public function testInputPrefixSuffixSizeOptionRendering() {

        $result = $this->Form->control('Donation', [
            'suffix' => [
                'text' => 'suffix',
                'size' => 'lg'
            ],
            'prefix' => [
                'text' => 'prefix',
                'size' => 'normal'
            ]
        ]);
        $this->assertHtml([
            'div' => ['class' => 'form-group'],
            ['label' => ['class' => 'col-form-label', 'for' => 'donation']],
            'Donation',
            '/label',
            ['div' => ['class' => 'input-group input-group-lg']],
            ['span' => ['class' => 'input-group-addon']],
            'prefix',
            '/span',
            'input' => [
                'type' => 'text',
                'name' => 'Donation',
                'id' => 'donation',
                'class' => 'form-control'
            ],
            ['span' => ['class' => 'input-group-addon']],
            'suffix',
            '/span',
            '/div',
            '/div'
        ], $result);

        $result = $this->Form->control('Donation', [
            'suffix' => [
                'text' => 'suffix',
                'size' => 'large'
            ],
            'prefix' => [
                'text' => 'prefix',
                'size' => 'normal'
            ]
        ]);
        $this->assertHtml([
            'div' => ['class' => 'form-group'],
            ['label' => ['class' => 'col-form-label', 'for' => 'donation']],
            'Donation',
            '/label',
            ['div' => ['class' => 'input-group input-group-lg']],
            ['span' => ['class' => 'input-group-addon']],
            'prefix',
            '/span',
            'input' => [
                'type' => 'text',
                'name' => 'Donation',
                'id' => 'donation',
                'class' => 'form-control'
            ],
            ['span' => ['class' => 'input-group-addon']],
            'suffix',
            '/span',
            '/div',
            '/div'
        ], $result);

        $result = $this->Form->control('Donation', [
            'suffix' => [
                'text' => 'suffix',
                'size' => 'normal'
            ],
            'prefix' => [
                'text' => 'prefix',
                'size' => 'large'
            ]
        ]);
        $this->assertHtml([
            'div' => ['class' => 'form-group'],
            ['label' => ['class' => 'col-form-label', 'for' => 'donation']],
            'Donation',
            '/label',
            ['div' => ['class' => 'input-group input-group-lg']],
            ['span' => ['class' => 'input-group-addon']],
            'prefix',
            '/span',
            'input' => [
                'type' => 'text',
                'name' => 'Donation',
                'id' => 'donation',
                'class' => 'form-control'
            ],
            ['span' => ['class' => 'input-group-addon']],
            'suffix',
            '/span',
            '/div',
            '/div'
        ], $result);

        $result = $this->Form->control('Donation', [
            'suffix' => [
                'text' => 'suffix',
                'size' => 'unknown'
            ],
            'prefix' => [
                'text' => 'prefix',
                'size' => 'normal'
            ]
        ]);
        $this->assertHtml([
            'div' => ['class' => 'form-group'],
            ['label' => ['class' => 'col-form-label', 'for' => 'donation']],
            'Donation',
            '/label',
            ['div' => ['class' => 'input-group']],
            ['span' => ['class' => 'input-group-addon']],
            'prefix',
            '/span',
            'input' => [
                'type' => 'text',
                'name' => 'Donation',
                'id' => 'donation',
                'class' => 'form-control'
            ],
            ['span' => ['class' => 'input-group-addon']],
            'suffix',
            '/span',
            '/div',
            '/div'
        ], $result);
    }

    public function testCustomControlsErrorMsg() {

        $content = [
            'schema' => ['radio1', 'multicheckbox1'],
            'errors' => [
                'radio1' => 'something is wrong',
                'profile-image' => 'wrong file type',
                'multicheckbox1' => 'multicheckbox error'
            ]];

        $this->Form->create($content);

        $result = $this->Form->control(
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
            'input' => ['type' => 'hidden', 'name' => 'radio1', 'value' => '', 'class' => 'is-invalid'],

            ['div' => ['class' => 'custom-control custom-radio']],
            [
                'input' => [
                    'type' => 'radio',
                    'name' => 'radio1',
                    'value' => '1',
                    'id' => 'radio1-1',
                    'class' => 'is-invalid custom-control-input'
                ]
            ],
            ['label' => ['for' => 'radio1-1', 'class' => 'custom-control-label']],
            'First Radio',
            '/label',
            '/div',

            ['div' => ['class' => 'custom-control custom-radio']],
            [
                'input' => [
                    'type' => 'radio',
                    'name' => 'radio1',
                    'value' => '2',
                    'id' => 'radio1-2',
                    'checked' => 'checked',
                    'class' => 'is-invalid custom-control-input'
                ]
            ],
            ['label' => ['for' => 'radio1-2', 'class' => 'custom-control-label selected']],
            'Second Radio',
            '/label',
            '/div',
            ['div' => ['class' => 'invalid-feedback']],
            'something is wrong',
            '/div',
            '/div'
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
            'input' => ['type' => 'hidden', 'name' => 'radio1', 'value' => '', 'class' => 'is-invalid'],

            ['div' => ['class' => 'custom-control custom-radio']],
            [
                'input' => [
                    'type' => 'radio',
                    'name' => 'radio1',
                    'value' => '1',
                    'id' => 'radio1-1',
                    'class' => 'is-invalid custom-control-input'
                ]
            ],

            ['label' => ['for' => 'radio1-1', 'class' => 'custom-control-label']],
            'First Radio',
            '/label',
            '/div',

            ['div' => ['class' => 'custom-control custom-radio']],
            [
                'input' => [
                    'type' => 'radio',
                    'name' => 'radio1',
                    'value' => '2',
                    'id' => 'radio1-2',
                    'checked' => 'checked',
                    'class' => 'is-invalid custom-control-input'
                ]
            ],
            ['label' => ['for' => 'radio1-2', 'class' => 'custom-control-label selected']],
            'Second Radio',
            '/label',
            '/div'
        ], $result);

        $result = $this->Form->multiCheckbox('multicheckbox1',
            [
                ['text' => 'First Checkbox', 'value' => 1],
                ['text' => 'Second Checkbox', 'value' => 2]
            ],
            [
                'default' => 2,
                'customControls' => true
            ]);

        $this->assertHtml([
            'input' => ['type' => 'hidden', 'name' => 'multicheckbox1', 'value' => '', 'class' => 'is-invalid'],

            ['div' => ['class' => 'custom-control custom-checkbox']],
            [
                'input' => [
                    'type' => 'checkbox',
                    'name' => 'multicheckbox1[]',
                    'value' => '1',
                    'id' => 'multicheckbox1-1',
                    'class' => 'is-invalid custom-control-input'
                ]
            ],
            ['label' => ['for' => 'multicheckbox1-1', 'class' => 'custom-control-label']],
            'First Checkbox',
            '/label',
            '/div',

            ['div' => ['class' => 'custom-control custom-checkbox']],
            [
                'input' => [
                    'type' => 'checkbox',
                    'name' => 'multicheckbox1[]',
                    'value' => '2',
                    'checked' => 'checked',
                    'id' => 'multicheckbox1-2',
                    'class' => 'is-invalid custom-control-input'
                ]
            ],
            ['label' => ['for' => 'multicheckbox1-2', 'class' => 'custom-control-label selected']],
            'Second Checkbox',
            '/label',
            '/div'
        ], $result);

        $result = $this->Form->control('multicheckbox1', [
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
            ['label' => ['for' => 'multicheckbox1']],
            'My checkboxes',
            '/label',

            'input' => ['type' => 'hidden', 'name' => 'multicheckbox1', 'value' => '', 'class' => 'is-invalid'],
            ['div' => ['class' => 'custom-control custom-checkbox']],
            [
                'input' => [
                    'type' => 'checkbox',
                    'name' => 'multicheckbox1[]',
                    'value' => '1',
                    'id' => 'multicheckbox1-1',
                    'class' => 'is-invalid custom-control-input'
                ]
            ],
            ['label' => ['for' => 'multicheckbox1-1', 'class' => 'custom-control-label']],
            'First Checkbox',
            '/label',
            '/div',

            ['div' => ['class' => 'custom-control custom-checkbox']],
            [
                'input' => [
                    'type' => 'checkbox',
                    'name' => 'multicheckbox1[]',
                    'value' => '2',
                    'checked' => 'checked',
                    'id' => 'multicheckbox1-2',
                    'class' => 'is-invalid custom-control-input'
                ]
            ],
            ['label' => ['for' => 'multicheckbox1-2', 'class' => 'custom-control-label selected']],
            'Second Checkbox',
            '/label',
            '/div',

            ['div' => ['class' => 'invalid-feedback']],
            'multicheckbox error',
            '/div',
            '/div'
        ], $result);

        $result = $this->Form->control('multicheckbox1', [
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
            ['label' => ['for' => 'multicheckbox1']],
            'My checkboxes',
            '/label',

            'input' => ['type' => 'hidden', 'name' => 'multicheckbox1', 'value' => '', 'class' => 'is-invalid'],

            ['div' => ['class' => 'custom-control custom-checkbox']],
            [
                'input' => [
                    'type' => 'checkbox',
                    'name' => 'multicheckbox1[]',
                    'value' => '1',
                    'id' => 'multicheckbox1-1',
                    'class' => 'is-invalid custom-control-input'
                ]
            ],
            ['label' => ['for' => 'multicheckbox1-1', 'class' => 'custom-control-label']],
            'First Checkbox',
            '/label',
            '/div',

            ['div' => ['class' => 'custom-control custom-checkbox']],
            [
                'input' => [
                    'type' => 'checkbox',
                    'name' => 'multicheckbox1[]',
                    'value' => '2',
                    'checked' => 'checked',
                    'id' => 'multicheckbox1-2',
                    'class' => 'is-invalid custom-control-input'
                ]
            ],

            ['label' => ['for' => 'multicheckbox1-2', 'class' => 'custom-control-label selected']],
            'Second Checkbox',
            '/label',
            '/div',

            ['div' => ['class' => 'invalid-feedback']],
            'multicheckbox error',
            '/div',
            '/div'
        ], $result);

        $result = $this->Form->file('profile-image', ['customControls' => true]);
        $this->assertHtml([
            'label' => ['class' => 'custom-file'],
            'input' => ['type' => 'file', 'name' => 'profile-image', 'class' => 'is-invalid custom-file-input'],
            'span' => ['class' => 'custom-file-control'],
            '/span',
            '/label'

        ], $result);

        $result = $this->Form->control('profile-image', ['type' => 'file', 'customControls' => true]);
        $this->assertHtml([
            'div' => ['class' => 'form-group'],
            ['label' => ['for' => 'profile-image', 'class' => 'col-form-label d-block']],
            'Profile Image',
            '/label',
            'label' => ['class' => 'custom-file'],
            'input' => ['type' => 'file', 'name' => 'profile-image', 'class' => 'is-invalid custom-file-input', 'id' => 'profile-image'],
            'span' => ['class' => 'custom-file-control'],
            '/span',
            '/label',
            ['div' => ['class' => 'invalid-feedback']],
            'wrong file type',
            '/div',
            '/div'
        ], $result);
    }

    public function testTemplateOverriding() {

        $this->Form->setTemplates([
            'checkboxContainer' => '<div class="check">{{content}}</div>'
        ]);
        $result = $this->Form->control('agree_terms', ['type' => 'checkbox', 'customControls' => false]);

        $this->assertHtml([
            'div' => ['class' => 'check'],
            'input' => [
                'type' => 'hidden',
                'name' => 'agree_terms',
                'value' => '0'
            ],
            ['input' => [
                'type' => 'checkbox',
                'name' => 'agree_terms',
                'id' => 'agree-terms',
                'value' => '1',
                'class' => 'form-check-input'
            ]],
            ['label' => ['class' => 'form-check-label', 'for' => 'agree-terms']],
            'Agree Terms',
            '/label',
            '/div'
        ], $result);
    }

    public function testLayoutInline() {

        $result = $this->Form->create(null, [
            'layout' => ['showLabels' => false, 'type' => 'inline']
        ]);
        $result .= $this->Form->control('Name', [
            'placeholder' => 'Jane Doe'
        ]);

        $this->assertHtml([
            'form' => ['method' => 'post', 'accept-charset' => 'utf-8', 'class' => 'form-inline', 'action' => '/'],
            ['div' => ['style' => 'display:none;']],
            ['input' => ['type' => 'hidden', 'name' => '_method', 'value' => 'POST']],
            '/div',
            ['label' => ['class' => 'sr-only', 'for' => 'name']],
            'Name',
            '/label',
            'input' => [
                'type' => 'text',
                'name' => 'Name',
                'id' => 'name',
                'placeholder' => 'Jane Doe',
                'class' => 'form-control'
            ]
        ], $result);

        $result = $this->Form->create(null, [
            'layout' => ['showLabels' => true, 'type' => 'inline']
        ]);
        $result .= $this->Form->control('Name', [
            'placeholder' => 'Jane Doe'
        ]);
        $this->assertHtml([
            'form' => ['method' => 'post', 'accept-charset' => 'utf-8', 'class' => 'form-inline', 'action' => '/'],
            ['div' => ['style' => 'display:none;']],
            ['input' => ['type' => 'hidden', 'name' => '_method', 'value' => 'POST']],
            '/div',
            ['label' => ['class' => 'col-form-label', 'for' => 'name']],
            'Name',
            '/label',
            'input' => [
                'type' => 'text',
                'name' => 'Name',
                'id' => 'name',
                'placeholder' => 'Jane Doe',
                'class' => 'form-control'
            ]
        ], $result);
    }

    public function testLayoutClasses() {
        $this->Form->create(null, [
            'layout' => [
                'classes' => [
                    'submitContainer' => ['submit-container-class'],
                    'control' => ['control-class'],
                    'label' => ['label-class']
                ]
            ]
        ]);

        // Simple global layout classes
        $result = $this->Form->control('name');
        $this->assertHtml([
            ['div' => ['class' => 'form-group']],
            'label' => ['class' => 'col-form-label label-class', 'for'],
            'Name',
            '/label',
            ['input' => ['name', 'type', 'id', 'class' => 'control-class form-control']],
            '/div'],
            $result);

        // Now pass in classes to control
        $result = $this->Form->control('name', [
            'class' => 'control-class2',
            'label' => ['class' => 'label-class2']
        ]);
        $this->assertHtml([
            ['div' => ['class' => 'form-group']],
            'label' => ['class' => 'label-class2 col-form-label label-class', 'for'],
            'Name',
            '/label',
            ['input' => ['name', 'type', 'id', 'class' => 'control-class2 control-class form-control']],
            '/div'], $result);

        // Submit container
        $result = $this->Form->submit();
        $this->assertHtml([
            ['div' => ['class' => 'submit-container-class']],
            ['input' => ['type' => 'submit', 'class', 'value']],
            '/div'], $result);
    }

    public function testLayoutGridSetGrid() {

        $this->Form->create(null, [
            'layout' => [
                'type' => 'grid',
                'classes' => [
                    'grid' => [['col-sm-3'], ['col-sm-9']]
                ]
            ]
        ]);

        $result = $this->Form->control('name');
        $this->assertHtml([
            ['div' => ['class' => 'form-group row']],
            'label' => ['class' => 'col-form-label col-sm-3', 'for'],
            'Name',
            '/label',
            ['div' => ['class' => 'col-sm-9']],
            ['input' => ['name', 'type', 'id', 'class' => 'form-control']],
            '/div',
            '/div',
        ], $result);

        $this->Form->create(null, [
            'layout' => [
                'type' => 'grid'
            ]
        ]);

        $result = $this->Form->control('name');
        $this->assertHtml([
            ['div' => ['class' => 'form-group row']],
            'label' => ['class' => 'col-form-label col-sm-2', 'for'],
            'Name',
            '/label',
            ['div' => ['class' => 'col-sm-10']],
            ['input' => ['name', 'type', 'id', 'class' => 'form-control']],
            '/div',
            '/div',
        ], $result);
    }

    public function testLayoutGridAllControls() {

        $result = $this->Form->create(null, [
            'layout' => [
                'type' => 'grid',
                'classes' => [
                    'submitContainer' => ['col-sm-10', 'offset-sm-2', 'p-1']
                ]
            ]
        ]);

        $result .= $this->Form->control('name', [

            'placeholder' => 'Jane Doe'
        ]);

        $result .= $this->Form->control('username', [
            'placeholder' => 'Username',
            'prefix' => ['text' => '@',]
        ]);

        $result .= $this->Form->control('checkbox1', [
            'label' => 'My checkboxes',
            'default' => 2,
            'multiple' => 'checkbox',
            'type' => 'select',
            'options' => [
                ['text' => 'First Checkbox', 'value' => 1],
                ['text' => 'Second Checkbox', 'value' => 2]
            ],
            'customControls' => true
        ]);

        $result .= $this->Form->control('checkbox1', [
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

        $result .= $this->Form->control(
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

        $result .= $this->Form->control(
            'radio',
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

        $result .= $this->Form->submit();
        $result .= $this->Form->end();

        $this->assertHtml([
            'form' => ['method', 'accept-charset', 'action', 'class' => 'container'],
            ['div' => ['style']],
            'input' => ['type' => 'hidden', 'name', 'value'],
            '/div',
            // Name text field
            ['div' => ['class' => 'form-group row']],
            'label' => ['class' => 'col-form-label col-sm-2', 'for'],
            'Name',
            '/label',
            ['div' => ['class' => 'col-sm-10']],
            ['input' => ['name', 'type', 'id', 'placeholder', 'class' => 'form-control']],
            '/div',
            '/div',
            // Username suffix
            'div' => ['class' => 'form-group row'],
            ['label' => ['class' => 'col-form-label col-sm-2', 'for' => 'username']],
            'Username',
            '/label',
            ['div' => ['class' => 'col-sm-10']],
            ['div' => ['class' => 'input-group']],
            ['span' => ['class' => 'input-group-addon']],
            '@',
            '/span',
            ['input' => ['type', 'name', 'placeholder', 'id', 'class' => 'form-control']],
            '/div',
            '/div',
            '/div',
            // Multi-checkbox custom controls
            ['div' => ['class' => 'form-group clearfix row']],
            ['label' => ['for', 'class' => 'col-sm-2']],
            'My checkboxes',
            '/label',
            ['div' => ['class' => 'col-sm-10']],
            ['input' => ['type' => 'hidden', 'name' => 'checkbox1', 'value' => '']],

            ['div' => ['class' => 'custom-control custom-checkbox']],
            ['input' => ['type', 'name', 'value', 'id', 'class' => 'custom-control-input']],
            ['label' => ['for', 'class' => 'custom-control-label']],
            'First Checkbox',
            '/label',
            '/div',

            ['div' => ['class' => 'custom-control custom-checkbox']],
            ['input' => ['type', 'name', 'value', 'id', 'checked', 'class' => 'custom-control-input']],
            ['label' => ['for', 'class' => 'custom-control-label selected']],
            'Second Checkbox',
            '/label',
            '/div',
            '/div',
            '/div',

            // Multi-checkbox non custom controls
            ['div' => ['class' => 'form-group row']],
            ['label' => ['for', 'class' => 'col-sm-2']],
            'My checkboxes',
            '/label',
            ['div' => ['class' => 'col-sm-10']],
            ['input' => ['type' => 'hidden', 'name' => 'checkbox1', 'value' => '']],
            ['div' => ['class' => 'form-check']],

            ['input' => ['type', 'name', 'value', 'id', 'class' => 'form-check-input']],
            ['label' => ['for' => 'checkbox1-1', 'class' => 'form-check-label']],
            'First Checkbox',
            '/label',
            '/div',

            ['div' => ['class' => 'form-check']],
            ['input' => ['type', 'name', 'value', 'checked', 'id', 'class' => 'form-check-input']],
            ['label' => ['for' => 'checkbox1-2', 'class' => 'form-check-label selected']],
            'Second Checkbox',
            '/label',
            '/div',
            '/div',
            '/div',
            // Radios custom controls
            ['div' => ['class' => 'form-group row']],
            ['label' => ['class' => 'col-sm-2']],
            'My Radios',
            '/label',
            ['div' => ['class' => 'col-sm-10']],
            ['input' => ['type' => 'hidden', 'name' => 'radio1', 'value' => '']],

            ['div' => ['class' => 'custom-control custom-radio']],
            ['input' => ['type', 'name', 'value', 'id', 'class' => 'custom-control-input']],
            ['label' => ['for', 'class' => 'custom-control-label']],
            'First Radio',
            '/label',
            '/div',

            ['div' => ['class' => 'custom-control custom-radio']],

            ['input' => ['type', 'name', 'value', 'id', 'checked', 'class' => 'custom-control-input']],
            ['label' => ['for' => 'radio1-2', 'class' => 'custom-control-label selected']],
            'Second Radio',
            '/label',
            '/div',
            '/div',
            '/div',
            // Radios non custom controls
            ['div' => ['class' => 'form-group row']],
            ['label' => ['class' => 'col-sm-2']],
            'My Radios',
            '/label',
            ['div' => ['class' => 'col-sm-10']],
            ['input' => ['type' => 'hidden', 'name', 'value' => ""]],
            ['div' => ['class' => 'form-check']],
            ['input' => ['type', 'name', 'value', 'id', 'class' => 'form-check-input']],
            ['label' => ['for', 'class' => 'form-check-label']],
            'First Radio',
            '/label',
            '/div',
            ['div' => ['class' => 'form-check']],
            ['input' => ['type', 'name', 'value', 'checked', 'id', 'class' => 'form-check-input']],
            ['label' => ['for', 'class' => 'form-check-label selected']],
            'Second Radio',
            '/label',
            '/div',
            '/div',
            '/div',
            // Submit
            ['div' => ['class' => 'col-sm-10 offset-sm-2 p-1']],
            ['input' => ['type' => 'submit', 'class', 'value']],
            '/div',
            '/form'
        ], $result);
    }

    public function testLayoutSubmitContainer() {
        $this->Form->create(null, [
            'layout' => [
                'classes' => [
                    'submitContainer' => ['col-sm-10', 'offset-sm-2', 'p-1']
                ]
            ]
        ]);

        $result = $this->Form->submit();
        $this->assertHtml([
            ['div' => ['class' => 'col-sm-10 offset-sm-2 p-1']],
            ['input' => ['type' => 'submit', 'class', 'value']],
            '/div'
        ], $result);

        $result = $this->Form->button('submit');
        $this->assertHtml([
            ['div' => ['class' => 'col-sm-10 offset-sm-2 p-1']],
            ['button' => ['type' => 'submit', 'class']],
            'submit',
            '/button',
            '/div'
        ], $result);
    }

    /**
     * testDateTimeFormatWithDateTime method
     *
     * Tests
     *  1) With Chronos value
     *  2) With Cake\I18n\Time value
     *  3) With valid datetime string
     *  4) With invalid datetime string
     *  5) With blank value
     *  6) With null value
     */
    public function testDateTimeFormatWithDateTime() {

        // Test (1)
        $this->Form->request = $this->Form->request->withData('expiry', Chronos::parse('2017-08-02 11:12:07'));
        $this->Form->create([
            'schema' => ['expiry' => ['type' => 'datetime']],
        ]);
        $result = $this->Form->control('expiry', ['label' => false]);
        $this->assertHtml([
            'div' => ['class'],
            'input' => ['type' => 'datetime-local', 'name', 'class', 'id', 'value' => '2017-08-02T11:12'],
            '/div'
        ], $result);

        // Test (2)
        $this->Form->request = $this->Form->request->withData('expiry', \Cake\I18n\Time::parse('2017-08-02 11:12:07'));
        $this->Form->create([
            'schema' => ['expiry' => ['type' => 'datetime']]
        ]);
        $result = $this->Form->control('expiry', ['label' => false]);
        $this->assertHtml([
            'div' => ['class'],
            'input' => ['type' => 'datetime-local', 'name', 'class', 'id', 'value' => '2017-08-02T11:12'],
            '/div'
        ], $result);

        // Test (3)
        $this->Form->request = $this->Form->request->withData('expiry', '2017-08-02 11:00:07');
        $this->Form->create([
            'schema' => ['expiry' => ['type' => 'datetime']]
        ]);
        $result = $this->Form->control('expiry', ['label' => false]);
        $this->assertHtml([
            'div' => ['class'],
            'input' => ['type' => 'datetime-local', 'name', 'class', 'id', 'value' => '2017-08-02T11:00'],
            '/div'
        ], $result);

        // Test (4)
        $this->Form->request = $this->Form->request->withData('expiry', '2017-08-02oop14:00:07');
        $this->Form->create([
            'schema' => ['expiry' => ['type' => 'datetime']]
        ]);
        $result = $this->Form->control('expiry', ['label' => false]);
        $this->assertHtml([
            'div' => ['class'],
            'input' => ['type' => 'datetime-local', 'name', 'class', 'id', 'value' => ''],
            '/div'
        ], $result);

        // Test (5)
        $this->Form->request = $this->Form->request->withData('expiry', '');
        $this->Form->create([
            'schema' => ['expiry' => ['type' => 'datetime']]
        ]);
        $result = $this->Form->control('expiry', ['label' => false]);
        $this->assertHtml([
            'div' => ['class'],
            'input' => ['type' => 'datetime-local', 'name', 'class', 'id', 'value' => ''],
            '/div'
        ], $result);

        // Test (6)
        $this->Form->request = $this->Form->request->withData('expiry', null);
        $this->Form->create([
            'schema' => ['expiry' => ['type' => 'datetime']]
        ]);
        $result = $this->Form->control('expiry', ['label' => false]);
        $this->assertHtml([
            'div' => ['class'],
            'input' => ['type' => 'datetime-local', 'name', 'class', 'id'],
            '/div'
        ], $result);

    }

    /**
     * testDateTimeFormatWithDate method
     *
     * Tests
     *  1) With Chronos value
     *  2) With Cake\I18n\Time value
     *  3) With valid date string
     *  4) With invalid date string
     *  5) With blank value
     *  6) With null value
     */
    public function testDateTimeFormatWithDate() {

        // Test (1)
        $this->Form->request = $this->Form->request->withData('expiry', Chronos::parse('2017-08-02'));
        $this->Form->create([
            'schema' => ['expiry' => ['type' => 'date']],
        ]);
        $result = $this->Form->control('expiry', ['label' => false]);
        $this->assertHtml([
            'div' => ['class'],
            'input' => ['type' => 'date', 'name', 'class', 'id', 'value' => '2017-08-02'],
            '/div'
        ], $result);

        // Test (2)
        $this->Form->request = $this->Form->request->withData('expiry', \Cake\I18n\Time::parse('2017-08-02 11:12:07'));
        $this->Form->create([
            'schema' => ['expiry' => ['type' => 'date']]
        ]);
        $result = $this->Form->control('expiry', ['label' => false]);
        $this->assertHtml([
            'div' => ['class'],
            'input' => ['type' => 'date', 'name', 'class', 'id', 'value' => '2017-08-02'],
            '/div'
        ], $result);

        // Test (3)
        $this->Form->request = $this->Form->request->withData('expiry', '2017-08-02 11:00:07');
        $this->Form->create([
            'schema' => ['expiry' => ['type' => 'date']]
        ]);
        $result = $this->Form->control('expiry', ['label' => false]);
        $this->assertHtml([
            'div' => ['class'],
            'input' => ['type' => 'date', 'name', 'class', 'id', 'value' => '2017-08-02'],
            '/div'
        ], $result);

        // Test (4)
        $this->Form->request = $this->Form->request->withData('expiry', '2017-08-02oop14:00:07');
        $this->Form->create([
            'schema' => ['expiry' => ['type' => 'date']]
        ]);
        $result = $this->Form->control('expiry', ['label' => false]);
        $this->assertHtml([
            'div' => ['class'],
            'input' => ['type' => 'date', 'name', 'class', 'id', 'value' => ''],
            '/div'
        ], $result);

        // Test (5)
        $this->Form->request = $this->Form->request->withData('expiry', '');
        $this->Form->create([
            'schema' => ['expiry' => ['type' => 'date']]
        ]);
        $result = $this->Form->control('expiry', ['label' => false]);
        $this->assertHtml([
            'div' => ['class'],
            'input' => ['type' => 'date', 'name', 'class', 'id', 'value' => ''],
            '/div'
        ], $result);

        // Test (6)
        $this->Form->request = $this->Form->request->withData('expiry', null);
        $this->Form->create([
            'schema' => ['expiry' => ['type' => 'date']]
        ]);
        $result = $this->Form->control('expiry', ['label' => false]);
        $this->assertHtml([
            'div' => ['class'],
            'input' => ['type' => 'date', 'name', 'class', 'id'],
            '/div'
        ], $result);

    }

    /**
     * testDateTimeFormatWithTime method
     *
     * Tests
     *  1) With Chronos value
     *  2) With Cake\I18n\Time value
     *  3) With valid date string
     *  4) With invalid date string
     *  5) With blank value
     *  6) With null value
     */
    public function testDateTimeFormatWithTime() {

        // Test (1)
        $this->Form->request = $this->Form->request->withData('expiry', Chronos::parse('2017-08-02 11:22:07'));
        $this->Form->create([
            'schema' => ['expiry' => ['type' => 'time']],
        ]);
        $result = $this->Form->control('expiry', ['label' => false]);
        $this->assertHtml([
            'div' => ['class'],
            'input' => ['type' => 'time', 'name', 'class', 'id', 'value' => '11:22'],
            '/div'
        ], $result);

        // Test (2)
        $this->Form->request = $this->Form->request->withData('expiry', \Cake\I18n\Time::parse('2017-08-02 11:12:07'));
        $this->Form->create([
            'schema' => ['expiry' => ['type' => 'time']]
        ]);
        $result = $this->Form->control('expiry', ['label' => false]);
        $this->assertHtml([
            'div' => ['class'],
            'input' => ['type' => 'time', 'name', 'class', 'id', 'value' => '11:12'],
            '/div'
        ], $result);

        // Test (3)
        $this->Form->request = $this->Form->request->withData('expiry', '2017-08-02 11:00:07');
        $this->Form->create([
            'schema' => ['expiry' => ['type' => 'time']]
        ]);
        $result = $this->Form->control('expiry', ['label' => false]);
        $this->assertHtml([
            'div' => ['class'],
            'input' => ['type' => 'time', 'name', 'class', 'id', 'value' => '11:00'],
            '/div'
        ], $result);

        // Test (4)
        $this->Form->request = $this->Form->request->withData('expiry', '2017-08-02oop14:00:07');
        $this->Form->create([
            'schema' => ['expiry' => ['type' => 'time']]
        ]);
        $result = $this->Form->control('expiry', ['label' => false]);
        $this->assertHtml([
            'div' => ['class'],
            'input' => ['type' => 'time', 'name', 'class', 'id', 'value' => ''],
            '/div'
        ], $result);

        // Test (5)
        $this->Form->request = $this->Form->request->withData('expiry', '');
        $this->Form->create([
            'schema' => ['expiry' => ['type' => 'time']]
        ]);
        $result = $this->Form->control('expiry', ['label' => false]);
        $this->assertHtml([
            'div' => ['class'],
            'input' => ['type' => 'time', 'name', 'class', 'id', 'value' => ''],
            '/div'
        ], $result);

        // Test (6)
        $this->Form->request = $this->Form->request->withData('expiry', null);
        $this->Form->create([
            'schema' => ['expiry' => ['type' => 'time']]
        ]);
        $result = $this->Form->control('expiry', ['label' => false]);
        $this->assertHtml([
            'div' => ['class'],
            'input' => ['type' => 'time', 'name', 'class', 'id'],
            '/div'
        ], $result);
    }

    /**
     * testControlCheckboxNesting method
     *
     * Test control() with checkbox creation.
     *
     * @return void
     */
    public function testControlCheckboxNesting() {
        $result = $this->Form->control('User.active', [
            'checked' => true,
            'customControls' => false,
            'nestedInput' => true
        ]);
        $expected = [
            'div' => ['class' => 'form-check'],
            'input' => ['type' => 'hidden', 'name' => 'User[active]', 'value' => '0'],
            'label' => ['for' => 'user-active', 'class' => 'form-check-label'],
            ['input' => ['type' => 'checkbox', 'name' => 'User[active]', 'value' => '1', 'id' => 'user-active', 'checked' => 'checked', 'class']],
            'Active',
            '/label',
            '/div'
        ];
        $this->assertHtml($expected, $result);
    }
}