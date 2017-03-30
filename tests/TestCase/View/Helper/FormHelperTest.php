<?php

namespace lilHermit\Bootstrap4\Test\TestCase\View\Helper;

use Cake\Collection\Collection;
use Cake\Core\Configure;
use Cake\ORM\Entity;
use Cake\ORM\TableRegistry;
use lilHermit\Bootstrap4\View\Helper\FormHelper;
use TestApp\Model\Entity\Article;

class FormHelperTest extends \Cake\Test\TestCase\View\Helper\FormHelperTest {

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp() {

        parent::setUp();

        $request = $this->Form->Url->request;

        $this->Form = new FormHelper($this->View, ['customControls' => false, 'html5Render' => false]);
        $this->Form->Url->request = $this->Form->request = $request;
    }

    public function testRenderingWidgetWithEmptyName() {
        $this->assertEquals([], $this->Form->fields);

        $result = $this->Form->widget('select', ['secure' => true, 'name' => '']);
        $this->assertEquals('<select name="" class="form-control"></select>', $result);
        $this->assertEquals([], $this->Form->fields);

        $result = $this->Form->widget('select', ['secure' => true, 'name' => '0']);
        $this->assertEquals('<select name="0" class="form-control"></select>', $result);
        $this->assertEquals(['0'], $this->Form->fields);


    }

    public function testInputTemplateVars() {
        $result = $this->Form->input('text', [
            'templates' => [
                'input' => '<input custom="{{forinput}}" type="{{type}}" name="{{name}}"{{attrs}}/>',
                'label' => '<label{{attrs}}>{{text}} {{forlabel}}</label>',
                'formGroup' => '{{label}}{{forgroup}}{{input}}',
                'inputContainer' => '<div class="input {{type}}{{required}}">{{content}}{{forcontainer}}</div>',
            ],
            'templateVars' => [
                'forinput' => 'in-input',
                'forlabel' => 'in-label',
                'forgroup' => 'in-group',
                'forcontainer' => 'in-container'
            ]
        ]);
        $expected = [
            'div' => ['class'],
            'label' => ['for', 'class'],
            'Text in-label',
            '/label',
            'in-group',
            'input' => ['name', 'type' => 'text', 'id', 'custom' => 'in-input', 'class' => 'form-control'],
            'in-container',
            '/div',
        ];
        $this->assertHtml($expected, $result);
    }

    /**
     * Test ensuring template variables work in template files loaded
     * during input().
     *
     * @return void
     */
    public function testInputTemplatesFromFile() {
        $result = $this->Form->input('title', [
            'templates' => 'test_templates',
            'templateVars' => [
                'forcontainer' => 'container-data'
            ]
        ]);
        $expected = [
            'div' => ['class'],
            'label' => ['for', 'class' => 'col-form-label'],
            'Title',
            '/label',
            'input' => ['name', 'type' => 'text', 'id', 'class' => 'form-control'],
            'container-data',
            '/div',
        ];
        $this->assertHtml($expected, $result);
    }

    /**
     * Test using template vars in inputSubmit and submitContainer template.
     *
     * @return void
     */
    public function testSubmitTemplateVars() {
        $this->Form->templates([
            'inputSubmit' => '<input custom="{{forinput}}" type="{{type}}"{{attrs}}/>',
            'submitContainer' => '<div class="submit">{{content}}{{forcontainer}}</div>'
        ]);
        $result = $this->Form->submit('Submit', [
            'templateVars' => [
                'forinput' => 'in-input',
                'forcontainer' => 'in-container'
            ]
        ]);
        $expected = [
            'div' => ['class'],
            'input' => ['custom' => 'in-input', 'type' => 'submit', 'value' => 'Submit', 'class' => 'btn btn-primary'],
            'in-container',
            '/div',
        ];

        $this->assertHtml($expected, $result);
    }

    /**
     * test creating a get form, and get form inputs.
     *
     * @return void
     */
    public function testGetFormCreate() {
        $encoding = strtolower(Configure::read('App.encoding'));
        $result = $this->Form->create($this->article, ['type' => 'get']);
        $expected = [
            'form' => [
                'method' => 'get',
                'action' => '/articles/add',
                'accept-charset' => $encoding
            ]
        ];

        $this->assertHtml($expected, $result);
        $result = $this->Form->text('title');
        $expected = [
            'input' => [
                'name' => 'title',
                'type' => 'text',
                'required' => 'required',
                'class' => 'form-control'
            ]
        ];

        $this->assertHtml($expected, $result);

        $result = $this->Form->password('password');

        $expected = [
            'input' => [
                'name' => 'password',
                'type' => 'password',
                'class' => 'form-control'
            ]
        ];
        $this->assertHtml($expected, $result);

        $this->assertNotRegExp('/<input[^<>]+[^id|name|type|value|class]=[^<>]*\/>$/', $result);

        $result = $this->Form->text('user_form');
        $expected = [
            'input' => [
                'name' => 'user_form',
                'type' => 'text',
                'class' => 'form-control'
            ]
        ];

        $this->assertHtml($expected, $result);
    }

    /**
     * test get form, and inputs when the model param is false
     *
     * @return void
     */
    public function testGetFormWithFalseModel() {
        $encoding = strtolower(Configure::read('App.encoding'));
        $this->Form->request['controller'] = 'contact_test';
        $result = $this->Form->create(false, [
            'type' => 'get',
            'url' => ['controller' => 'contact_test']
        ]);

        $expected = [
            'form' => [
                'method' => 'get',
                'action' => '/contact_test/add',
                'accept-charset' => $encoding
            ]
        ];
        $this->assertHtml($expected, $result);

        $result = $this->Form->text('reason');
        $expected = [
            'input' => ['type' => 'text', 'name' => 'reason', 'class' => 'form-control']
        ];
        $this->assertHtml($expected, $result);
    }

    /**
     * Tests correct generation of number fields for double and float fields
     *
     * @return void
     */
    public function testTextFieldGenerationForFloats() {
        $this->article['schema'] = [
            'foo' => [
                'type' => 'float',
                'null' => false,
                'default' => null,
                'length' => 10
            ]
        ];

        $this->Form->create($this->article);
        $result = $this->Form->input('foo');
        $expected = [
            'div' => ['class' => 'form-group'],
            'label' => ['for' => 'foo', 'class' => 'col-form-label'],
            'Foo',
            '/label',
            [
                'input' => [
                    'type' => 'number',
                    'name' => 'foo',
                    'id' => 'foo',
                    'step' => 'any',
                    'class' => 'form-control'
                ]
            ],
            '/div'
        ];
        $this->assertHtml($expected, $result);

        $result = $this->Form->input('foo', ['step' => 0.5]);
        $expected = [
            'div' => ['class' => 'form-group'],
            'label' => ['for' => 'foo', 'class' => 'col-form-label'],
            'Foo',
            '/label',
            [
                'input' => [
                    'type' => 'number',
                    'name' => 'foo',
                    'id' => 'foo',
                    'step' => '0.5',
                    'class' => 'form-control'
                ]
            ],
            '/div'
        ];
        $this->assertHtml($expected, $result);
    }


    /**
     * Tests correct generation of number fields for integer fields
     *
     * @return void
     */
    public function testTextFieldTypeNumberGenerationForIntegers() {
        TableRegistry::get('Contacts', [
            'className' => 'Cake\Test\TestCase\View\Helper\ContactsTable'
        ]);

        $this->Form->create([], ['context' => ['table' => 'Contacts']]);
        $result = $this->Form->input('age');
        $expected = [
            'div' => ['class' => 'form-group'],
            'label' => ['for' => 'age', 'class' => 'col-form-label'],
            'Age',
            '/label',
            [
                'input' => [
                    'type' => 'number',
                    'name' => 'age',
                    'id' => 'age',
                    'class' => 'form-control'
                ]
            ],
            '/div'
        ];
        $this->assertHtml($expected, $result);
    }

    /**
     * testFormSecurityMultipleSubmitButtons
     *
     * test form submit generation and ensure that _Token is only created on end()
     *
     * @return void
     */
    public function testFormSecurityMultipleSubmitButtons() {
        $this->Form->request->params['_Token'] = 'testKey';

        $this->Form->create($this->article);
        $this->Form->text('Address.title');
        $this->Form->text('Address.first_name');

        $result = $this->Form->submit('Save', ['name' => 'save']);
        $expected = [
            'input' => ['type' => 'submit', 'name' => 'save', 'value' => 'Save', 'class' => 'btn btn-primary'],
        ];
        $this->assertHtml($expected, $result);

        $result = $this->Form->submit('Cancel', ['name' => 'cancel']);
        $expected = [
            'input' => ['type' => 'submit', 'name' => 'cancel', 'value' => 'Cancel', 'class' => 'btn btn-primary'],
        ];
        $this->assertHtml($expected, $result);

        $result = $this->Form->end();
        $tokenDebug = urlencode(json_encode([
            '/articles/add',
            [
                'Address.title',
                'Address.first_name',
            ],
            ['save', 'cancel']
        ]));

        $expected = [
            'div' => ['style' => 'display:none;'],
            [
                'input' => [
                    'type' => 'hidden',
                    'name' => '_Token[fields]',
                    'value'
                ]
            ],
            [
                'input' => [
                    'type' => 'hidden',
                    'name' => '_Token[unlocked]',
                    'value' => 'cancel%7Csave'
                ]
            ],
            [
                'input' => [
                    'type' => 'hidden',
                    'name' => '_Token[debug]',
                    'value' => $tokenDebug,
                ]
            ],
            '/div'
        ];
        $this->assertHtml($expected, $result);
    }

    /**
     * testFormSecuredInput method
     *
     * Test generation of entire secure form, assertions made on input() output.
     *
     * @return void
     */
    public function testFormSecuredInput() {
        $this->Form->request->params['_csrfToken'] = 'testKey';
        $this->Form->request->params['_Token'] = 'stuff';
        $this->article['schema'] = [
            'ratio' => ['type' => 'decimal', 'length' => 5, 'precision' => 6],
            'population' => ['type' => 'decimal', 'length' => 15, 'precision' => 0],
        ];

        $result = $this->Form->create($this->article, ['url' => '/articles/add']);
        $encoding = strtolower(Configure::read('App.encoding'));
        $expected = [
            'form' => ['method' => 'post', 'action' => '/articles/add', 'accept-charset' => $encoding],
            'div' => ['style' => 'display:none;'],
            ['input' => ['type' => 'hidden', 'name' => '_method', 'value' => 'POST']],
            [
                'input' => [
                    'type' => 'hidden',
                    'name' => '_csrfToken',
                    'value' => 'testKey'
                ]
            ],
            '/div'
        ];
        $this->assertHtml($expected, $result);

        $result = $this->Form->input('ratio');
        $expected = [
            'div' => ['class'],
            'label' => ['for', 'class'],
            'Ratio',
            '/label',
            'input' => ['name', 'type' => 'number', 'step' => '0.000001', 'id', 'class'],
            '/div',
        ];
        $this->assertHtml($expected, $result);

        $result = $this->Form->input('population');
        $expected = [
            'div' => ['class'],
            'label' => ['for', 'class'],
            'Population',
            '/label',
            'input' => ['name', 'type' => 'number', 'step' => '1', 'id', 'class'],
            '/div',
        ];
        $this->assertHtml($expected, $result);


        $result = $this->Form->input('published', ['type' => 'text']);
        $expected = [
            'div' => ['class' => 'form-group'],
            'label' => ['for' => 'published', 'class'],
            'Published',
            '/label',
            [
                'input' => [
                    'type' => 'text',
                    'name' => 'published',
                    'id' => 'published',
                    'class'
                ]
            ],
            '/div'
        ];
        $this->assertHtml($expected, $result);

        $result = $this->Form->input('other', ['type' => 'text']);
        $expected = [
            'div' => ['class' => 'form-group'],
            'label' => ['for' => 'other', 'class'],
            'Other',
            '/label',
            [
                'input' => [
                    'type' => 'text',
                    'name' => 'other',
                    'id',
                    'class'
                ]
            ],
            '/div'
        ];
        $this->assertHtml($expected, $result);

        $result = $this->Form->hidden('stuff');
        $expected = [
            'input' => [
                'type' => 'hidden',
                'name' => 'stuff'
            ]
        ];

        $this->assertHtml($expected, $result);

        $result = $this->Form->hidden('hidden', ['value' => '0']);
        $expected = [
            'input' => [
                'type' => 'hidden',
                'name' => 'hidden',
                'value' => '0'
            ]
        ];
        $this->assertHtml($expected, $result);

        $result = $this->Form->input('something', ['type' => 'checkbox']);
        $expected = [
            'div' => ['class' => 'form-check'],
            [
                'input' => [
                    'type' => 'hidden',
                    'name' => 'something',
                    'value' => '0'
                ]
            ],
            'label' => ['for' => 'something', 'class' => 'form-check-label'],
            [
                'input' => [
                    'type' => 'checkbox',
                    'name' => 'something',
                    'value' => '1',
                    'id' => 'something',
                    'class' => 'form-check-input'
                ]
            ],
            'Something',
            '/label',
            '/div'
        ];
        $this->assertHtml($expected, $result);

        $result = $this->Form->fields;
        $expectedFields = [
            'ratio',
            'population',
            'published',
            'other',
            'stuff' => '',
            'hidden' => '0',
            'something'
        ];
        $this->assertEquals($expectedFields, $result);

        $result = $this->Form->secure($this->Form->fields);
        $tokenDebug = urlencode(json_encode([
            '/articles/add',
            $expectedFields,
            []
        ]));

        $expected = [
            'div' => ['style' => 'display:none;'],
            [
                'input' => [
                    'type' => 'hidden',
                    'name' => '_Token[fields]',
                    'value'
                ]
            ],
            [
                'input' => [
                    'type' => 'hidden',
                    'name' => '_Token[unlocked]',
                    'value' => ''
                ]
            ],
            [
                'input' => [
                    'type' => 'hidden',
                    'name' => '_Token[debug]',
                    'value' => $tokenDebug,
                ]
            ],
            '/div'
        ];
        $this->assertHtml($expected, $result);
    }

    /**
     * testErrorMessageDisplay method
     *
     * Test error message display.
     *
     * @return void
     */
    public function testErrorMessageDisplay() {
        $this->article['errors'] = [
            'Article' => [
                'title' => 'error message',
                'content' => 'some <strong>test</strong> data with <a href="#">HTML</a> chars'
            ]
        ];
        $this->Form->create($this->article);

        $result = $this->Form->input('Article.title');
        $expected = [
            'div' => ['class' => 'form-group has-danger'],
            'label' => ['for' => 'article-title', 'class' => 'col-form-label'],
            'Title',
            '/label',
            'input' => [
                'type' => 'text',
                'name' => 'Article[title]',
                'id' => 'article-title',
                'class' => 'form-control-danger form-control'
            ],
            ['div' => ['class' => 'form-control-feedback']],
            'error message',
            '/div',
            '/div'
        ];
        $this->assertHtml($expected, $result);

        $result = $this->Form->input('Article.title', [
            'templates' => [
                'inputContainerError' => '<div class="input {{type}}{{required}} error">{{content}}</div>'
            ]
        ]);

        $expected = [
            'div' => ['class' => 'input text error'],
            'label' => ['for' => 'article-title', 'class' => 'col-form-label'],
            'Title',
            '/label',
            'input' => [
                'type' => 'text',
                'name' => 'Article[title]',
                'id' => 'article-title',
                'class' => 'form-control-danger form-control'
            ],
            '/div'
        ];

        $this->assertHtml($expected, $result);


        $result = $this->Form->input('Article.content');
        $expected = [
            'div' => ['class' => 'form-group has-danger'],
            'label' => ['for' => 'article-content', 'class' => 'col-form-label'],
            'Content',
            '/label',
            'input' => [
                'type' => 'text',
                'name' => 'Article[content]',
                'id' => 'article-content',
                'class' => 'form-control-danger form-control'
            ],
            ['div' => ['class' => 'form-control-feedback']],
            'some &lt;strong&gt;test&lt;/strong&gt; data with &lt;a href=&quot;#&quot;&gt;HTML&lt;/a&gt; chars',
            '/div',
            '/div'
        ];
        $this->assertHtml($expected, $result);

        $result = $this->Form->input('Article.content', ['error' => ['escape' => true]]);
        $expected = [
            'div' => ['class' => 'form-group has-danger'],
            'label' => ['for' => 'article-content', 'class' => 'col-form-label'],
            'Content',
            '/label',
            'input' => [
                'type' => 'text',
                'name' => 'Article[content]',
                'id' => 'article-content',
                'class' => 'form-control-danger form-control'
            ],
            ['div' => ['class' => 'form-control-feedback']],
            'some &lt;strong&gt;test&lt;/strong&gt; data with &lt;a href=&quot;#&quot;&gt;HTML&lt;/a&gt; chars',
            '/div',
            '/div'
        ];
        $this->assertHtml($expected, $result);

        $result = $this->Form->input('Article.content', ['error' => ['escape' => false]]);
        $expected = [
            'div' => ['class' => 'form-group has-danger'],
            'label' => ['for' => 'article-content', 'class' => 'col-form-label'],
            'Content',
            '/label',
            'input' => [
                'type' => 'text',
                'name' => 'Article[content]',
                'id' => 'article-content',
                'class' => 'form-control-danger form-control'
            ],
            ['div' => ['class' => 'form-control-feedback']],
            'some <strong>test</strong> data with <a href="#">HTML</a> chars',
            '/div',
            '/div'
        ];
        $this->assertHtml($expected, $result);
    }

    /**
     * testEmptyErrorValidation method
     *
     * Test validation errors, when validation message is an empty string.
     *
     * @return void
     */
    public function testEmptyErrorValidation() {
        $this->article['errors'] = [
            'Article' => ['title' => '']
        ];
        $this->Form->create($this->article);

        $result = $this->Form->input('Article.title');
        $expected = [
            'div' => ['class' => 'form-group has-danger'],
            'label' => ['for' => 'article-title', 'class' => 'col-form-label'],
            'Title',
            '/label',
            'input' => [
                'type' => 'text',
                'name' => 'Article[title]',
                'id' => 'article-title',
                'class' => 'form-control-danger form-control'
            ],
            ['div' => ['class' => 'form-control-feedback']],
            [],
            '/div',
            '/div'
        ];
        $this->assertHtml($expected, $result);
    }

    /**
     * testEmptyInputErrorValidation method
     *
     * Test validation errors, when calling input() overriding validation message by an empty string.
     *
     * @return void
     */
    public function testEmptyInputErrorValidation() {
        $this->article['errors'] = [
            'Article' => ['title' => 'error message']
        ];
        $this->Form->create($this->article);

        $result = $this->Form->input('Article.title', ['error' => '']);
        $expected = [
            'div' => ['class' => 'form-group has-danger'],
            'label' => ['for' => 'article-title', 'class' => 'col-form-label'],
            'Title',
            '/label',
            'input' => [
                'type' => 'text',
                'name' => 'Article[title]',
                'id' => 'article-title',
                'class' => 'form-control-danger form-control'
            ],
            ['div' => ['class' => 'form-control-feedback']],
            [],
            '/div',
            '/div'
        ];
        $this->assertHtml($expected, $result);
    }

    /**
     * testInputErrorMessage method
     *
     * Test validation errors, when calling input() overriding validation messages.
     *
     * @return void
     */
    public function testInputErrorMessage() {
        $this->article['errors'] = [
            'title' => ['error message']
        ];
        $this->Form->create($this->article);

        $result = $this->Form->input('title', [
            'error' => 'Custom error!'
        ]);
        $expected = [
            'div' => ['class' => 'form-group has-danger'],
            'label' => ['for' => 'title', 'class' => 'col-form-label'],
            'Title',
            '/label',
            'input' => [
                'type' => 'text',
                'name' => 'title',
                'id' => 'title',
                'class' => 'form-control-danger form-control',
                'required' => 'required',
            ],
            ['div' => ['class' => 'form-control-feedback']],
            'Custom error!',
            '/div',
            '/div'
        ];
        $this->assertHtml($expected, $result);

        $result = $this->Form->input('title', [
            'error' => ['error message' => 'Custom error!']
        ]);
        $expected = [
            'div' => ['class' => 'form-group has-danger'],
            'label' => ['for' => 'title', 'class' => 'col-form-label'],
            'Title',
            '/label',
            'input' => [
                'type' => 'text',
                'name' => 'title',
                'id' => 'title',
                'class' => 'form-control-danger form-control',
                'required' => 'required'
            ],
            ['div' => ['class' => 'form-control-feedback']],
            'Custom error!',
            '/div',
            '/div'
        ];
        $this->assertHtml($expected, $result);
    }

    /**
     * testFormValidationAssociated method
     *
     * Tests displaying errors for nested entities.
     *
     * @return void
     */
    public function testFormValidationAssociated() {
        $nested = new Entity(['foo' => 'bar']);
        $nested->errors('foo', ['not a valid bar']);
        $entity = new Entity(['nested' => $nested]);
        $this->Form->create($entity, ['context' => ['table' => 'Articles']]);

        $result = $this->Form->error('nested.foo');
        $this->assertEquals('<div class="form-control-feedback">not a valid bar</div>', $result);
    }

    /**
     * testFormValidationAssociatedSecondLevel method
     *
     * Test form error display with associated model.
     *
     * @return void
     */
    public function testFormValidationAssociatedSecondLevel() {
        $inner = new Entity(['bar' => 'baz']);
        $nested = new Entity(['foo' => $inner]);
        $entity = new Entity(['nested' => $nested]);
        $inner->errors('bar', ['not a valid one']);
        $this->Form->create($entity, ['context' => ['table' => 'Articles']]);
        $result = $this->Form->error('nested.foo.bar');
        $this->assertEquals('<div class="form-control-feedback">not a valid one</div>', $result);
    }

    /**
     * testFormValidationMultiRecord method
     *
     * Test form error display with multiple records.
     *
     * @return void
     */
    public function testFormValidationMultiRecord() {
        $one = new Entity();
        $two = new Entity();
        TableRegistry::get('Contacts', [
            'className' => 'Cake\Test\TestCase\View\Helper\ContactsTable'
        ]);
        $one->set('email', '');
        $one->errors('email', ['invalid email']);

        $two->set('name', '');
        $two->errors('name', ['This is wrong']);
        $this->Form->create([$one, $two], ['context' => ['table' => 'Contacts']]);

        $result = $this->Form->input('0.email');
        $expected = [
            'div' => ['class' => 'form-group has-danger'],
            'label' => ['for' => '0-email', 'class' => 'col-form-label'],
            'Email',
            '/label',
            'input' => [
                'type' => 'email',
                'name' => '0[email]',
                'id' => '0-email',
                'class' => 'form-control-danger form-control',
                'maxlength' => 255,
                'value' => '',
            ],
            ['div' => ['class' => 'form-control-feedback']],
            'invalid email',
            '/div',
            '/div'
        ];
        $this->assertHtml($expected, $result);

        $result = $this->Form->input('1.name');
        $expected = [
            'div' => ['class' => 'form-group has-danger'],
            'label' => ['for' => '1-name', 'class' => 'col-form-label'],
            'Name',
            '/label',
            'input' => [
                'type' => 'text',
                'name' => '1[name]',
                'id' => '1-name',
                'class' => 'form-control-danger form-control',
                'maxlength' => 255,
                'value' => ''
            ],
            ['div' => ['class' => 'form-control-feedback']],
            'This is wrong',
            '/div',
            '/div'
        ];
        $this->assertHtml($expected, $result);
    }

    /**
     * testInput method
     *
     * Test various incarnations of input().
     *
     * @return void
     */
    public function testInput() {
        TableRegistry::get('ValidateUsers', [
            'className' => 'Cake\Test\TestCase\View\Helper\ValidateUsersTable'
        ]);
        $this->Form->create([], ['context' => ['table' => 'ValidateUsers']]);
        $result = $this->Form->input('ValidateUsers.balance');
        $expected = [
            'div' => ['class'],
            'label' => ['for', 'class'],
            'Balance',
            '/label',
            'input' => ['name', 'type' => 'number', 'id', 'step', 'class'],
            '/div',
        ];
        $this->assertHtml($expected, $result);

        $result = $this->Form->input('ValidateUser.cost_decimal');
        $expected = [
            'div' => ['class'],
            'label' => ['for', 'class'],
            'Cost Decimal',
            '/label',
            'input' => ['name', 'type' => 'number', 'step' => '0.001', 'id', 'class'],
            '/div',
        ];
        $this->assertHtml($expected, $result);

        $result = $this->Form->input('ValidateUser.null_decimal');
        $expected = [
            'div' => ['class'],
            'label' => ['for', 'class'],
            'Null Decimal',
            '/label',
            'input' => ['name', 'type' => 'number', 'id', 'class'],
            '/div',
        ];
        $this->assertHtml($expected, $result);
    }

    /**
     * testInputCustomization method
     *
     * Tests the input method and passing custom options.
     *
     * @return void
     */
    public function testInputCustomization() {
        TableRegistry::get('Contacts', [
            'className' => 'Cake\Test\TestCase\View\Helper\ContactsTable'
        ]);
        $this->Form->create([], ['context' => ['table' => 'Contacts']]);
        $result = $this->Form->input('Contact.email', ['id' => 'custom']);
        $expected = [
            'div' => ['class' => 'form-group'],
            'label' => ['for' => 'custom', 'class'],
            'Email',
            '/label',
            [
                'input' => [
                    'type' => 'email',
                    'name' => 'Contact[email]',
                    'id' => 'custom',
                    'maxlength' => 255,
                    'class' => 'form-control'
                ]
            ],
            '/div'
        ];
        $this->assertHtml($expected, $result);

        $result = $this->Form->input('Contact.email', [
            'templates' => ['inputContainer' => '<div>{{content}}</div>']
        ]);
        $expected = [
            '<div',
            'label' => ['for' => 'contact-email', 'class'],
            'Email',
            '/label',
            [
                'input' => [
                    'type' => 'email',
                    'name' => 'Contact[email]',
                    'id' => 'contact-email',
                    'maxlength' => 255,
                    'class' => 'form-control'
                ]
            ],
            '/div'
        ];

        $this->assertHtml($expected, $result);

        $result = $this->Form->input('Contact.email', ['type' => 'text']);
        $expected = [
            'div' => ['class' => 'form-group'],
            'label' => ['for' => 'contact-email', 'class' => 'col-form-label'],
            'Email',
            '/label',
            [
                'input' => [
                    'type' => 'text',
                    'name' => 'Contact[email]',
                    'id' => 'contact-email',
                    'maxlength' => '255',
                    'class' => 'form-control'
                ]
            ],
            '/div'
        ];
        $this->assertHtml($expected, $result);

        $result = $this->Form->input('Contact.5.email', ['type' => 'text']);
        $expected = [
            'div' => ['class' => 'form-group'],
            'label' => ['for' => 'contact-5-email', 'class' => 'col-form-label'],
            'Email',
            '/label',
            [
                'input' => [
                    'type' => 'text',
                    'name' => 'Contact[5][email]',
                    'id' => 'contact-5-email',
                    'maxlength' => '255',
                    'class' => 'form-control'
                ]
            ],
            '/div'
        ];
        $this->assertHtml($expected, $result);

        $result = $this->Form->input('Contact.password');
        $expected = [
            'div' => ['class' => 'form-group'],
            'label' => ['for' => 'contact-password', 'class' => 'col-form-label'],
            'Password',
            '/label',
            [
                'input' => [
                    'type' => 'password',
                    'name' => 'Contact[password]',
                    'id' => 'contact-password',
                    'class' => 'form-control'
                ]
            ],
            '/div'
        ];
        $this->assertHtml($expected, $result);

        $result = $this->Form->input('Contact.email', [
            'type' => 'file',
            'class' => 'textbox'
        ]);
        $expected = [
            'div' => ['class' => 'form-group'],
            'label' => ['for' => 'contact-email'],
            'Email',
            '/label',
            [
                'input' => [
                    'type' => 'file',
                    'name' => 'Contact[email]',
                    'class' => 'textbox form-control-file',
                    'id' => 'contact-email'
                ]
            ],
            '/div'
        ];
        $this->assertHtml($expected, $result);

        $entity = new Entity(['phone' => 'Hello & World > weird chars']);
        $this->Form->create($entity, ['context' => ['table' => 'Contacts']]);
        $result = $this->Form->input('phone');
        $expected = [
            'div' => ['class' => 'form-group'],
            'label' => ['for' => 'phone', 'class' => 'col-form-label'],
            'Phone',
            '/label',
            [
                'input' => [
                    'type' => 'tel',
                    'name' => 'phone',
                    'value' => 'Hello &amp; World &gt; weird chars',
                    'id' => 'phone',
                    'maxlength' => 255,
                    'class' => 'form-control'
                ]
            ],
            '/div'
        ];
        $this->assertHtml($expected, $result);

        $this->Form->request->data['Model']['0']['OtherModel']['field'] = 'My value';
        $this->Form->create();
        $result = $this->Form->input('Model.0.OtherModel.field', ['id' => 'myId']);
        $expected = [
            'div' => ['class' => 'form-group'],
            'label' => ['for' => 'myId', 'class' => 'col-form-label'],
            'Field',
            '/label',
            'input' => [
                'type' => 'text',
                'name' => 'Model[0][OtherModel][field]',
                'value' => 'My value',
                'id' => 'myId',
                'class' => 'form-control'
            ],
            '/div'
        ];
        $this->assertHtml($expected, $result);

        $this->Form->request->data = [];

        $entity->errors('field', 'Badness!');
        $this->Form->create($entity, ['context' => ['table' => 'Contacts']]);
        $result = $this->Form->input('field');
        $expected = [
            'div' => ['class' => 'form-group has-danger'],
            'label' => ['for' => 'field', 'class' => 'col-form-label'],
            'Field',
            '/label',
            'input' => [
                'type' => 'text',
                'name' => 'field',
                'id' => 'field',
                'class' => 'form-control-danger form-control'
            ],
            ['div' => ['class' => 'form-control-feedback']],
            'Badness!',
            '/div',
            '/div'
        ];
        $this->assertHtml($expected, $result);

        $result = $this->Form->input('field', [
            'templates' => [
                'inputContainerError' => '{{content}}{{error}}',
                'error' => '<span class="error-message">{{content}}</span>'
            ]
        ]);
        $expected = [
            'label' => ['for' => 'field', 'class' => 'col-form-label'],
            'Field',
            '/label',
            'input' => [
                'type' => 'text',
                'name' => 'field',
                'id' => 'field',
                'class' => 'form-control-danger form-control'
            ],
            ['span' => ['class' => 'error-message']],
            'Badness!',
            '/span'
        ];
        $this->assertHtml($expected, $result);

        $entity->errors('field', ['minLength'], true);
        $result = $this->Form->input('field', [
            'error' => [
                'minLength' => 'Le login doit contenir au moins 2 caractères',
                'maxLength' => 'login too large'
            ]
        ]);
        $expected = [
            'div' => ['class' => 'form-group has-danger'],
            'label' => ['for' => 'field', 'class' => 'col-form-label'],
            'Field',
            '/label',
            'input' => [
                'type' => 'text',
                'name' => 'field',
                'id' => 'field',
                'class' => 'form-control-danger form-control'
            ],
            ['div' => ['class' => 'form-control-feedback']],
            'Le login doit contenir au moins 2 caractères',
            '/div',
            '/div'
        ];
        $this->assertHtml($expected, $result);

        $entity->errors('field', ['maxLength'], true);
        $result = $this->Form->input('field', [
            'error' => [
                'minLength' => 'Le login doit contenir au moins 2 caractères',
                'maxLength' => 'login too large',
            ]
        ]);
        $expected = [
            'div' => ['class' => 'form-group has-danger'],
            'label' => ['for' => 'field', 'class' => 'col-form-label'],
            'Field',
            '/label',
            'input' => [
                'type' => 'text',
                'name' => 'field',
                'id' => 'field',
                'class' => 'form-control-danger form-control'
            ],
            ['div' => ['class' => 'form-control-feedback']],
            'login too large',
            '/div',
            '/div'
        ];
        $this->assertHtml($expected, $result);
    }


    /**
     * testInputWithTemplateFile method
     *
     * Test that input() accepts a template file.
     *
     * @return void
     */
    public function testInputWithTemplateFile() {
        $result = $this->Form->input('field', [
            'templates' => 'htmlhelper_tags'
        ]);
        $expected = [
            'label' => ['for' => 'field', 'class' => 'col-form-label'],
            'Field',
            '/label',
            'input' => [
                'type' => 'text',
                'name' => 'field',
                'id' => 'field',
                'class' => 'form-control'
            ],
        ];
        $this->assertHtml($expected, $result);
    }

    /**
     * testNestedInputsEndWithBrackets method
     *
     * Test that nested inputs end with brackets.
     *
     * @return void
     */
    public function testNestedInputsEndWithBrackets() {
        $result = $this->Form->text('nested.text[]');
        $expected = [
            'input' => [
                'type' => 'text',
                'name' => 'nested[text][]',
                'class' => 'form-control'
            ],
        ];
        $this->assertHtml($expected, $result);

        $result = $this->Form->file('nested.file[]');
        $expected = [
            'input' => [
                'type' => 'file',
                'name' => 'nested[file][]',
                'class' => 'form-control-file'
            ],
        ];
        $this->assertHtml($expected, $result);
    }

    /**
     * Test that the correct fields are unlocked for image submits with no names.
     *
     * @return void
     */
    public function testSecuritySubmitImageNoName() {
        $this->Form->request->params['_Token'] = 'testKey';

        $this->Form->create(false);
        $result = $this->Form->submit('save.png');
        $expected = [
            'input' => ['type' => 'image', 'src' => 'img/save.png'],
        ];
        $this->assertHtml($expected, $result);
        $this->assertEquals(['x', 'y'], $this->Form->unlockField());
    }

    /**
     * Test that the correct fields are unlocked for image submits with names.
     *
     * @return void
     */
    public function testSecuritySubmitImageName() {
        $this->Form->request->params['_Token'] = 'testKey';

        $this->Form->create(null);
        $result = $this->Form->submit('save.png', ['name' => 'test']);
        $expected = [
            'input' => ['type' => 'image', 'name' => 'test', 'src' => 'img/save.png'],
        ];
        $this->assertHtml($expected, $result);
        $this->assertEquals(['test', 'test_x', 'test_y'], $this->Form->unlockField());
    }

    /**
     * testCreateIdPrefix method
     *
     * Test id prefix.
     *
     * @return void
     */
    public function testCreateIdPrefix() {
        $this->Form->create(false, ['idPrefix' => 'prefix']);

        $result = $this->Form->input('field');
        $expected = [
            'div' => ['class' => 'form-group'],
            'label' => ['for' => 'prefix-field', 'class' => 'col-form-label'],
            'Field',
            '/label',
            'input' => ['type' => 'text', 'name' => 'field', 'id' => 'prefix-field', 'class' => 'form-control'],
            '/div'
        ];
        $this->assertHtml($expected, $result);

        $result = $this->Form->input('field', ['id' => 'custom-id']);
        $expected = [
            'div' => ['class' => 'form-group'],
            'label' => ['for' => 'custom-id', 'class' => 'col-form-label'],
            'Field',
            '/label',
            'input' => ['type' => 'text', 'name' => 'field', 'id' => 'custom-id', 'class' => 'form-control'],
            '/div'
        ];
        $this->assertHtml($expected, $result);

        $result = $this->Form->radio('Model.field', ['option A'], ['customControls' => false]);
        $expected = [
            'input' => ['type' => 'hidden', 'name' => 'Model[field]', 'value' => ''],
            'div' => ['class' => 'form-check'],
            'label' => ['for' => 'prefix-model-field-0', 'class' => 'form-check-label'],
            [
                'input' => [
                    'type' => 'radio',
                    'name' => 'Model[field]',
                    'value' => '0',
                    'id' => 'prefix-model-field-0',
                    'class' => 'form-check-input'
                ]
            ],
            'option A',
            '/label',
            '/div'
        ];

        $this->assertHtml($expected, $result);

        $result = $this->Form->radio('Model.field', ['option A', 'option'], ['customControls' => false]);
        $expected = [
            'input' => ['type' => 'hidden', 'name' => 'Model[field]', 'value' => ''],
            'div' => ['class' => 'form-check'],
            'label' => ['for' => 'prefix-model-field-0', 'class' => 'form-check-label'],
            [
                'input' => [
                    'type' => 'radio',
                    'name' => 'Model[field]',
                    'value' => '0',
                    'id' => 'prefix-model-field-0',
                    'class' => 'form-check-input'
                ]
            ],
            'option A',
            '/label'
        ];
        $this->assertHtml($expected, $result);

        $result = $this->Form->select(
            'Model.multi_field',
            ['first'],
            ['multiple' => 'checkbox', 'customControls' => false]
        );
        $expected = [
            'input' => [
                'type' => 'hidden',
                'name' => 'Model[multi_field]',
                'value' => ''
            ],
            ['div' => ['class' => 'form-check']],
            ['label' => ['for' => 'prefix-model-multi-field-0', 'class' => 'form-check-label']],
            [
                'input' => [
                    'type' => 'checkbox',
                    'name' => 'Model[multi_field][]',
                    'value' => '0',
                    'id' => 'prefix-model-multi-field-0',
                    'class' => 'form-check-input'
                ]
            ],
            'first',
            '/label',
            '/div',
        ];

        $this->assertHtml($expected, $result);

        $this->Form->end();
        $result = $this->Form->input('field');
        $expected = [
            'div' => ['class' => 'form-group'],
            'label' => ['for' => 'field', 'class' => 'col-form-label'],
            'Field',
            '/label',
            'input' => ['type' => 'text', 'name' => 'field', 'id' => 'field', 'class' => 'form-control'],
            '/div'
        ];
        $this->assertHtml($expected, $result);
    }

    /**
     * testInputZero method
     *
     * Test that inputs with 0 can be created.
     *
     * @return void
     */
    public function testInputZero() {
        TableRegistry::get('Contacts', [
            'className' => 'Cake\Test\TestCase\View\Helper\ContactsTable'
        ]);
        $this->Form->create([], ['context' => ['table' => 'Contacts']]);
        $result = $this->Form->input('0');
        $expected = [
            'div' => ['class' => 'form-group'],
            'label' => ['for' => '0', 'class'],
            '/label',
            'input' => ['type' => 'text', 'name' => '0', 'id' => '0', 'class' => 'form-control'],
            '/div'
        ];
        $this->assertHtml($expected, $result);
    }

    /**
     * testInputCheckbox method
     *
     * Test input() with checkbox creation.
     *
     * @return void
     */
    public function testInputCheckbox() {
        $result = $this->Form->input('User.active', ['label' => false, 'checked' => true]);
        $expected = [
            'div' => ['class' => 'form-check'],
            'input' => ['type' => 'hidden', 'name' => 'User[active]', 'value' => '0'],
            ['input' => ['type' => 'checkbox', 'name' => 'User[active]', 'value' => '1',
                'id' => 'user-active', 'checked' => 'checked',
                'class' => 'form-check-input'
            ]],
            '/div'
        ];
        $this->assertHtml($expected, $result);

        $result = $this->Form->input('User.active', ['label' => false, 'checked' => 1, 'customControls' => false]);
        $expected = [
            'div' => ['class' => 'form-check'],
            'input' => ['type' => 'hidden', 'name' => 'User[active]', 'value' => '0'],
            ['input' => ['type' => 'checkbox', 'name' => 'User[active]', 'value' => '1',
                'id' => 'user-active', 'checked' => 'checked',
                'class' => 'form-check-input'
            ]],
            '/div'
        ];
        $this->assertHtml($expected, $result);

        $result = $this->Form->input('User.active', ['label' => false, 'checked' => '1', 'customControls' => false]);
        $expected = [
            'div' => ['class' => 'form-check'],
            'input' => ['type' => 'hidden', 'name' => 'User[active]', 'value' => '0'],
            ['input' => ['type' => 'checkbox', 'name' => 'User[active]', 'value' => '1',
                'id' => 'user-active', 'checked' => 'checked',
                'class' => 'form-check-input'
            ]],
            '/div'
        ];
        $this->assertHtml($expected, $result);

        $result = $this->Form->input('User.disabled', [
            'label' => 'Disabled',
            'type' => 'checkbox',
            'data-foo' => 'disabled',
            'customControls' => false
        ]);
        $expected = [
            'div' => ['class' => 'form-check'],
            'input' => ['type' => 'hidden', 'name' => 'User[disabled]', 'value' => '0'],
            'label' => ['for' => 'user-disabled', 'class'],
            [
                'input' => [
                    'type' => 'checkbox',
                    'name' => 'User[disabled]',
                    'value' => '1',
                    'id' => 'user-disabled',
                    'data-foo' => 'disabled',
                    'class' => 'form-check-input'
                ]
            ],
            'Disabled',
            '/label',
            '/div'
        ];
        $this->assertHtml($expected, $result);

        $result = $this->Form->input('User.confirm', [
            'label' => 'Confirm <b>me</b>!',
            'type' => 'checkbox',
            'escape' => false,
            'customControls' => false
        ]);
        $expected = [
            'div' => ['class' => 'form-check'],
            'input' => ['type' => 'hidden', 'name' => 'User[confirm]', 'value' => '0'],
            'label' => ['for' => 'user-confirm', 'class' => 'form-check-label'],
            [
                'input' => [
                    'type' => 'checkbox',
                    'name' => 'User[confirm]',
                    'value' => '1',
                    'id' => 'user-confirm',
                    'class' => 'form-check-input'
                ]
            ],
            'Confirm <b>me</b>!',
            '/label',
            '/div'
        ];
        $this->assertHtml($expected, $result);
    }

    /**
     * testInputCheckboxWithDisabledElements method
     *
     * Test generating checkboxes with disabled elements.
     *
     * @return void
     */
    public function testInputCheckboxWithDisabledElements() {
        $options = [1 => 'One', 2 => 'Two', '3' => 'Three'];
        $result = $this->Form->input('Contact.multiple', [
            'multiple' => 'checkbox',
            'disabled' => 'disabled',
            'options' => $options,
            'customControls' => false
        ]);
        $expected = [
            ['div' => ['class' => 'form-group']],
            ['label' => ['for' => "contact-multiple"]],
            'Multiple',
            '/label',
            ['input' => ['type' => 'hidden', 'name' => "Contact[multiple]", 'disabled' => 'disabled', 'value' => '']],
            ['div' => ['class' => 'form-check']],
            ['label' => ['for' => "contact-multiple-1", 'class' => 'form-check-label']],
            [
                'input' => [
                    'type' => 'checkbox',
                    'name' => "Contact[multiple][]",
                    'value' => 1,
                    'disabled' => 'disabled',
                    'id' => "contact-multiple-1",
                    'class' => 'form-check-input'
                ]
            ],
            'One',
            '/label',
            '/div',
            ['div' => ['class' => 'form-check']],
            ['label' => ['for' => "contact-multiple-2", 'class' => 'form-check-label']],
            [
                'input' => [
                    'type' => 'checkbox',
                    'name' => "Contact[multiple][]",
                    'value' => 2,
                    'disabled' => 'disabled',
                    'id' => "contact-multiple-2",
                    'class' => 'form-check-input'
                ]
            ],
            'Two',
            '/label',
            '/div',
            ['div' => ['class' => 'form-check']],
            ['label' => ['for' => "contact-multiple-3", 'class' => 'form-check-label']],
            [
                'input' => [
                    'type' => 'checkbox',
                    'name' => "Contact[multiple][]",
                    'value' => 3,
                    'disabled' => 'disabled',
                    'id' => "contact-multiple-3",
                    'class' => 'form-check-input'
                ]
            ],
            'Three',
            '/label',
            '/div',
            '/div'
        ];
        $this->assertHtml($expected, $result);

        // make sure 50 does only disable 50, and not 50f5c0cf
        $options = ['50' => 'Fifty', '50f5c0cf' => 'Stringy'];
        $disabled = [50];

        $expected = [
            ['div' => ['class' => 'form-group']],
            ['label' => ['for' => "contact-multiple",]],
            'Multiple',
            '/label',
            ['input' => ['type' => 'hidden', 'name' => "Contact[multiple]", 'value' => '']],
            ['div' => ['class' => 'form-check']],
            ['label' => ['for' => "contact-multiple-50", 'class' => 'form-check-label']],
            [
                'input' => [
                    'type' => 'checkbox',
                    'name' => "Contact[multiple][]",
                    'value' => 50,
                    'disabled' => 'disabled',
                    'id' => "contact-multiple-50",
                    'class' => 'form-check-input'
                ]
            ],
            'Fifty',
            '/label',
            '/div',
            ['div' => ['class' => 'form-check']],
            ['label' => ['for' => "contact-multiple-50f5c0cf", 'class' => 'form-check-label']],
            [
                'input' => [
                    'type' => 'checkbox',
                    'name' => "Contact[multiple][]",
                    'value' => '50f5c0cf',
                    'id' => "contact-multiple-50f5c0cf",
                    'class' => 'form-check-input'
                ]
            ],
            'Stringy',
            '/label',
            '/div',
            '/div'
        ];
        $result = $this->Form->input('Contact.multiple', [
            'multiple' => 'checkbox',
            'disabled' => $disabled,
            'options' => $options,
            'customControls' => false
        ]);
        $this->assertHtml($expected, $result);
    }

    /**
     * testInputWithLeadingInteger method
     *
     * Test input name with leading integer, ensure attributes are generated correctly.
     *
     * @return void
     */
    public function testInputWithLeadingInteger() {
        $result = $this->Form->text('0.Node.title');
        $expected = [
            'input' => ['name' => '0[Node][title]', 'type' => 'text', 'class' => 'form-control']
        ];
        $this->assertHtml($expected, $result);
    }

    /**
     * testInputSelectType method
     *
     * Test form->input() with select type inputs.
     *
     * @return void
     */
    public function testInputSelectType() {
        $result = $this->Form->input(
            'email',
            [
                'options' => ['è' => 'Firést', 'é' => 'Secoènd'],
                'empty' => true
            ]
        );
        $expected = [
            'div' => ['class' => 'form-group'],
            'label' => ['for' => 'email', 'class'],
            'Email',
            '/label',
            ['select' => ['name' => 'email', 'id' => 'email', 'class' => 'form-control']],
            ['option' => ['value' => '']],
            '/option',
            ['option' => ['value' => 'è']],
            'Firést',
            '/option',
            ['option' => ['value' => 'é']],
            'Secoènd',
            '/option',
            '/select',
            '/div'
        ];
        $this->assertHtml($expected, $result);

        $result = $this->Form->input(
            'email',
            [
                'options' => ['First', 'Second'],
                'empty' => true
            ]
        );
        $expected = [
            'div' => ['class' => 'form-group'],
            'label' => ['for' => 'email', 'class'],
            'Email',
            '/label',
            ['select' => ['name' => 'email', 'id' => 'email', 'class' => 'form-control']],
            ['option' => ['value' => '']],
            '/option',
            ['option' => ['value' => '0']],
            'First',
            '/option',
            ['option' => ['value' => '1']],
            'Second',
            '/option',
            '/select',
            '/div'
        ];
        $this->assertHtml($expected, $result);

        $result = $this->Form->input('email', [
            'type' => 'select',
            'options' => new \ArrayObject(['First', 'Second']),
            'empty' => true
        ]);
        $this->assertHtml($expected, $result);

        $this->View->viewVars['users'] = ['value' => 'good', 'other' => 'bad'];
        $this->Form->request->data = ['Model' => ['user_id' => 'value']];

        $result = $this->Form->input('Model.user_id', ['empty' => true]);
        $expected = [
            'div' => ['class' => 'form-group'],
            'label' => ['for' => 'model-user-id', 'class'],
            'User',
            '/label',
            'select' => ['name' => 'Model[user_id]', 'id' => 'model-user-id', 'class' => 'form-control'],
            ['option' => ['value' => '']],
            '/option',
            ['option' => ['value' => 'value', 'selected' => 'selected']],
            'good',
            '/option',
            ['option' => ['value' => 'other']],
            'bad',
            '/option',
            '/select',
            '/div'
        ];
        $this->assertHtml($expected, $result);

        $this->View->viewVars['users'] = ['value' => 'good', 'other' => 'bad'];
        $this->Form->request->data = ['Thing' => ['user_id' => null]];
        $result = $this->Form->input('Thing.user_id', ['empty' => 'Some Empty']);
        $expected = [
            'div' => ['class' => 'form-group'],
            'label' => ['for' => 'thing-user-id', 'class'],
            'User',
            '/label',
            'select' => ['name' => 'Thing[user_id]', 'id' => 'thing-user-id', 'class' => 'form-control'],
            ['option' => ['value' => '']],
            'Some Empty',
            '/option',
            ['option' => ['value' => 'value']],
            'good',
            '/option',
            ['option' => ['value' => 'other']],
            'bad',
            '/option',
            '/select',
            '/div'
        ];
        $this->assertHtml($expected, $result);

        $this->View->viewVars['users'] = ['value' => 'good', 'other' => 'bad'];
        $this->Form->request->data = ['Thing' => ['user_id' => 'value']];
        $result = $this->Form->input('Thing.user_id', ['empty' => 'Some Empty']);
        $expected = [
            'div' => ['class' => 'form-group'],
            'label' => ['for' => 'thing-user-id', 'class'],
            'User',
            '/label',
            'select' => ['name' => 'Thing[user_id]', 'id' => 'thing-user-id', 'class' => 'form-control'],
            ['option' => ['value' => '']],
            'Some Empty',
            '/option',
            ['option' => ['value' => 'value', 'selected' => 'selected']],
            'good',
            '/option',
            ['option' => ['value' => 'other']],
            'bad',
            '/option',
            '/select',
            '/div'
        ];
        $this->assertHtml($expected, $result);

        $this->Form->data = [];
        $result = $this->Form->input('Publisher.id', [
            'label' => 'Publisher',
            'type' => 'select',
            'multiple' => 'checkbox',
            'options' => ['Value 1' => 'Label 1', 'Value 2' => 'Label 2'],
            'customControls' => false
        ]);
        $expected = [
            ['div' => ['class' => 'form-group']],
            ['label' => ['for' => 'publisher-id']],
            'Publisher',
            '/label',
            'input' => ['type' => 'hidden', 'name' => 'Publisher[id]', 'value' => ''],
            ['div' => ['class' => 'form-check']],
            ['label' => ['for' => 'publisher-id-value-1', 'class' => 'form-check-label']],
            [
                'input' => [
                    'type' => 'checkbox',
                    'name' => 'Publisher[id][]',
                    'value' => 'Value 1',
                    'id' => 'publisher-id-value-1',
                    'class' => 'form-check-input'
                ]
            ],
            'Label 1',
            '/label',
            '/div',
            ['div' => ['class' => 'form-check']],
            ['label' => ['for' => 'publisher-id-value-2', 'class' => 'form-check-label']],
            [
                'input' => [
                    'type' => 'checkbox',
                    'name' => 'Publisher[id][]',
                    'value' => 'Value 2',
                    'id' => 'publisher-id-value-2',
                    'class' => 'form-check-input'

                ]
            ],
            'Label 2',
            '/label',
            '/div',
            '/div'
        ];
        $this->assertHtml($expected, $result);

    }

    /**
     * testInputOverridingMagicSelectType method
     *
     * Test that overriding the magic select type widget is possible.
     *
     * @return void
     */
    public function testInputOverridingMagicSelectType() {
        $this->View->viewVars['users'] = ['value' => 'good', 'other' => 'bad'];
        $result = $this->Form->input('Model.user_id', ['type' => 'text']);
        $expected = [
            'div' => ['class' => 'form-group'],
            'label' => ['for' => 'model-user-id', 'class'],
            'User',
            '/label',
            'input' => [
                'name' => 'Model[user_id]',
                'type' => 'text',
                'id' => 'model-user-id',
                'class' => 'form-control'
            ],
            '/div'
        ];
        $this->assertHtml($expected, $result);

        //Check that magic types still work for plural/singular vars
        $this->View->viewVars['types'] = ['value' => 'good', 'other' => 'bad'];
        $result = $this->Form->input('Model.type');
        $expected = [
            'div' => ['class' => 'form-group'],
            'label' => ['for' => 'model-type', 'class'],
            'Type',
            '/label',
            'select' => ['name' => 'Model[type]', 'id' => 'model-type', 'class' => 'form-control'],
            ['option' => ['value' => 'value']],
            'good',
            '/option',
            ['option' => ['value' => 'other']],
            'bad',
            '/option',
            '/select',
            '/div'
        ];
        $this->assertHtml($expected, $result);
    }

    /**
     * testInputMagicTypeDoesNotOverride method
     *
     * Test that inferred types do not override developer input.
     *
     * @return void
     */
    public function testInputMagicTypeDoesNotOverride() {
        $this->View->viewVars['users'] = ['value' => 'good', 'other' => 'bad'];
        $result = $this->Form->input('Model.user', ['type' => 'checkbox', 'customControls' => false]);
        $expected = [
            'div' => ['class' => 'form-check'],
            [
                'input' => [
                    'type' => 'hidden',
                    'name' => 'Model[user]',
                    'value' => 0,
                ]
            ],
            'label' => ['for' => 'model-user', 'class'],
            [
                'input' => [
                    'name' => 'Model[user]',
                    'type' => 'checkbox',
                    'id' => 'model-user',
                    'value' => 1,
                    'class' => 'form-check-input'
                ]
            ],
            'User',
            '/label',
            '/div'
        ];

        $this->assertHtml($expected, $result);

        // make sure that for HABTM the multiple option is not being overwritten in case it's truly
        $options = [
            1 => 'blue',
            2 => 'red'
        ];
        $result = $this->Form->input('tags._ids',
            ['options' => $options, 'multiple' => 'checkbox', 'customControls' => false]);
        $expected = [
            'div' => ['class' => 'form-group'],
            'label' => ['for' => 'tags-ids'],
            'Tags',
            '/label',
            'input' => ['type' => 'hidden', 'name' => 'tags[_ids]', 'value' => ''],

            ['div' => ['class' => 'form-check']],
            ['label' => ['for' => 'tags-ids-1', 'class' => 'form-check-label']],
            [
                'input' => [
                    'id' => 'tags-ids-1',
                    'type' => 'checkbox',
                    'value' => '1',
                    'name' => 'tags[_ids][]',
                    'class' => 'form-check-input'
                ]
            ],
            'blue',
            '/label',
            '/div',

            ['div' => ['class' => 'form-check']],
            ['label' => ['for' => 'tags-ids-2', 'class' => 'form-check-label']],
            [
                'input' => [
                    'id' => 'tags-ids-2',
                    'type' => 'checkbox',
                    'value' => '2',
                    'name' => 'tags[_ids][]',
                    'class' => 'form-check-input'
                ]
            ],
            'red',
            '/label',
            '/div',
            '/div'
        ];
        $this->assertHtml($expected, $result);
    }

    /**
     * testInputMagicSelectForTypeNumber method
     *
     * Test that magic input() selects are created for type=number.
     *
     * @return void
     */
    public function testInputMagicSelectForTypeNumber() {
        TableRegistry::get('ValidateUsers', [
            'className' => 'Cake\Test\TestCase\View\Helper\ValidateUsersTable'

        ]);
        $entity = new Entity(['balance' => 1]);
        $this->Form->create($entity, ['context' => ['table' => 'ValidateUsers']]);
        $this->View->viewVars['balances'] = [0 => 'nothing', 1 => 'some', 100 => 'a lot'];
        $result = $this->Form->input('balance');
        $expected = [
            'div' => ['class' => 'form-group'],
            'label' => ['for' => 'balance', 'class'],
            'Balance',
            '/label',
            'select' => ['name' => 'balance', 'id' => 'balance', 'class' => 'form-control'],
            ['option' => ['value' => '0']],
            'nothing',
            '/option',
            ['option' => ['value' => '1', 'selected' => 'selected']],
            'some',
            '/option',
            ['option' => ['value' => '100']],
            'a lot',
            '/option',
            '/select',
            '/div'
        ];
        $this->assertHtml($expected, $result);
    }

    /**
     * testFormInputSubmit method
     *
     * Test correct results for form::input() and type submit.
     *
     * @return void
     */
    public function testFormInputSubmit() {
        $result = $this->Form->input('Test Submit', ['type' => 'submit', 'class' => 'foobar']);
        $expected = [
            'input' => [
                'type' => 'submit',
                'class' => 'foobar btn btn-primary',
                'id' => 'test-submit',
                'value' => 'Test Submit'
            ],
        ];
        $this->assertHtml($expected, $result);
    }

    /**
     * testFormInputs method
     *
     * Test correct results from form::inputs().
     *
     * @return void
     */
    public function testFormInputsLegendFieldset() {
        $this->Form->create($this->article);
        $result = $this->Form->allInputs([], ['legend' => 'The Legend']);
        $expected = [
            'fieldset' => ['class' => 'form-group'],
            '<legend',
            'The Legend',
            '/legend',
            '*/fieldset',
        ];
        $this->assertHtml($expected, $result);

        $result = $this->Form->allInputs([], ['fieldset' => true, 'legend' => 'Field of Dreams']);
        $this->assertContains('<legend>Field of Dreams</legend>', $result);
        $this->assertContains('<fieldset class="form-group">', $result);

        $result = $this->Form->allInputs([], ['fieldset' => false, 'legend' => false]);
        $this->assertNotContains('<legend>', $result);
        $this->assertNotContains('<fieldset>', $result);

        $result = $this->Form->allInputs([], ['fieldset' => false, 'legend' => 'Hello']);
        $this->assertNotContains('<legend>', $result);
        $this->assertNotContains('<fieldset>', $result);
        $this->Form->create($this->article);
        $this->Form->request->params['prefix'] = 'admin';
        $this->Form->request->params['action'] = 'admin_edit';
        $this->Form->request->params['controller'] = 'articles';
        $result = $this->Form->allInputs();
        $expected = [
            'fieldset' => ['class' => 'form-group'],
            '<legend',
            'New Article',
            '/legend',
            '*/fieldset',
        ];
        $this->assertHtml($expected, $result);

        $this->Form->create($this->article);
        $result = $this->Form->allInputs([], ['fieldset' => [], 'legend' => 'The Legend']);
        $expected = [
            'fieldset' => ['class'],
            '<legend',
            'The Legend',
            '/legend',
            '*/fieldset',
        ];
        $this->assertHtml($expected, $result);

        $this->Form->create($this->article);

        $result = $this->Form->allInputs([], [
            'fieldset' => [
                'class' => 'some-class some-other-class',
                'disabled' => true,
                'data-param' => 'a-param'
            ],
            'legend' => 'The Legend'
        ]);
        $expected = [
            '<fieldset class="some-class some-other-class form-group" disabled="disabled" data-param="a-param"',
            '<legend',
            'The Legend',
            '/legend',
            '*/fieldset',
        ];
        $this->assertHtml($expected, $result);
    }

    /**
     * testFormInputs method
     *
     * Test the inputs() method.
     *
     * @return void
     */
    public function testFormInputs() {
        $this->Form->create($this->article);
        $result = $this->Form->allInputs();
        $expected = [
            'fieldset' => ['class' => 'form-group'],
            '<legend',
            'New Article',
            '/legend',
            'input' => ['type' => 'hidden', 'name' => 'id', 'id' => 'id'],
            ['div' => ['class' => 'form-group']],
            '*/div',
            ['div' => ['class' => 'form-group']],
            '*/div',
            ['div' => ['class' => 'form-group']],
            '*/div',
            ['div' => ['class' => 'form-group']],
            '*/div',
            '/fieldset',
        ];
        $this->assertHtml($expected, $result);

        $result = $this->Form->allInputs([
            'published' => ['type' => 'boolean']
        ]);
        $expected = [
            'fieldset' => ['class' => 'form-group'],
            '<legend',
            'New Article',
            '/legend',
            'input' => ['type' => 'hidden', 'name' => 'id', 'id' => 'id'],
            ['div' => ['class' => 'form-group']],
            '*/div',
            ['div' => ['class' => 'form-group']],
            '*/div',
            ['div' => ['class' => 'form-group']],
            '*/div',
            ['div' => ['class' => 'form-group']],
            '*/div',
            '/fieldset',
        ];
        $this->assertHtml($expected, $result);

        $this->Form->create($this->article);
        $result = $this->Form->allInputs([], ['legend' => 'Hello']);
        $expected = [
            'fieldset' => ['class' => 'form-group'],
            'legend' => [],
            'Hello',
            '/legend',
            'input' => ['type' => 'hidden', 'name' => 'id', 'id' => 'id'],
            ['div' => ['class' => 'form-group']],
            '*/div',
            ['div' => ['class' => 'form-group']],
            '*/div',
            ['div' => ['class' => 'form-group']],
            '*/div',
            ['div' => ['class' => 'form-group']],
            '*/div',
            '/fieldset'
        ];
        $this->assertHtml($expected, $result);

        $this->Form->create(false);
        $expected = [
            'fieldset' => ['class' => 'form-group'],
            ['div' => ['class' => 'form-group']],
            'label' => ['for' => 'foo', 'class'],
            'Foo',
            '/label',
            'input' => ['type' => 'text', 'name' => 'foo', 'id' => 'foo', 'class' => 'form-control'],
            '*/div',
            '/fieldset'
        ];
        $result = $this->Form->allInputs(
            ['foo' => ['type' => 'text']],
            ['legend' => false]
        );
        $this->assertHtml($expected, $result);
    }

    /**
     * testFormInputsBlacklist method
     *
     * @return void
     */
    public function testFormInputsBlacklist() {
        $this->Form->create($this->article);
        $result = $this->Form->allInputs([
            'id' => false
        ]);
        $expected = [
            'fieldset' => ['class' => 'form-group'],
            '<legend',
            'New Article',
            '/legend',
            ['div' => ['class' => 'form-group']],
            '*/div',
            ['div' => ['class' => 'form-group']],
            '*/div',
            ['div' => ['class' => 'form-group']],
            '*/div',
            ['div' => ['class' => 'form-group']],
            '*/div',
            '/fieldset',
        ];
        $this->assertHtml($expected, $result);

        $this->Form->create($this->article);
        $result = $this->Form->allInputs([
            'id' => []
        ]);
        $expected = [
            'fieldset' => ['class' => 'form-group'],
            '<legend',
            'New Article',
            '/legend',
            'input' => ['type' => 'hidden', 'name' => 'id', 'id' => 'id'],
            ['div' => ['class' => 'form-group']],
            '*/div',
            ['div' => ['class' => 'form-group']],
            '*/div',
            ['div' => ['class' => 'form-group']],
            '*/div',
            ['div' => ['class' => 'form-group']],
            '*/div',
            '/fieldset',
        ];
        $this->assertHtml($expected, $result, 'A falsey value (array) should not remove the input');
    }

    /**
     * testSelectAsCheckbox method
     *
     * Test multi-select widget with checkbox formatting.
     *
     * @return void
     */
    public function testSelectAsCheckbox() {

        $result = $this->Form->select(
            'Model.multi_field',
            ['first', 'second', 'third'],
            [
                'multiple' => 'checkbox',
                'value' => [0, 1]
            ]
        );
        $expected = [
            'input' => ['type' => 'hidden', 'name' => 'Model[multi_field]', 'value' => ''],
            ['div' => ['class' => 'form-check']],
            ['label' => ['for' => 'model-multi-field-0', 'class' => 'form-check-label selected']],
            [
                'input' => [
                    'type' => 'checkbox',
                    'name' => 'Model[multi_field][]',
                    'checked' => 'checked',
                    'value' => '0',
                    'id' => 'model-multi-field-0',
                    'class' => 'form-check-input'
                ]
            ],
            'first',
            '/label',
            '/div',
            ['div' => ['class' => 'form-check']],
            ['label' => ['for' => 'model-multi-field-1', 'class' => 'form-check-label selected']],
            [
                'input' => [
                    'type' => 'checkbox',
                    'name' => 'Model[multi_field][]',
                    'checked' => 'checked',
                    'value' => '1',
                    'id' => 'model-multi-field-1',
                    'class' => 'form-check-input'
                ]
            ],
            'second',
            '/label',
            '/div',
            ['div' => ['class' => 'form-check']],
            ['label' => ['for' => 'model-multi-field-2', 'class' => 'form-check-label']],
            [
                'input' => [
                    'type' => 'checkbox',
                    'name' => 'Model[multi_field][]',
                    'value' => '2',
                    'id' => 'model-multi-field-2',
                    'class' => 'form-check-input'
                ]
            ],
            'third',
            '/label',
            '/div',
        ];
        $this->assertHtml($expected, $result);

        $result = $this->Form->select(
            'Model.multi_field',
            ['1/2' => 'half'],
            ['multiple' => 'checkbox']
        );
        $expected = [
            'input' => ['type' => 'hidden', 'name' => 'Model[multi_field]', 'value' => ''],
            ['div' => ['class' => 'form-check']],
            ['label' => ['for' => 'model-multi-field-1-2', 'class' => 'form-check-label']],
            [
                'input' => [
                    'type' => 'checkbox',
                    'name' => 'Model[multi_field][]',
                    'value' => '1/2',
                    'id' => 'model-multi-field-1-2',
                    'class' => 'form-check-input'
                ]
            ],
            'half',
            '/label',
            '/div',
        ];
        $this->assertHtml($expected, $result);
    }

    /**
     * testLabel method
     *
     * Test label generation.
     *
     * @return void
     */
    public function testLabel() {
        $result = $this->Form->label('Person.name');
        $expected = ['label' => ['for' => 'person-name'], 'Name', '/label'];
        $this->assertHtml($expected, $result);

        $result = $this->Form->label('Person.name');
        $expected = ['label' => ['for' => 'person-name'], 'Name', '/label'];
        $this->assertHtml($expected, $result);

        $result = $this->Form->label('Person.first_name');
        $expected = ['label' => ['for' => 'person-first-name'], 'First Name', '/label'];
        $this->assertHtml($expected, $result);

        $result = $this->Form->label('Person.first_name', 'Your first name');
        $expected = ['label' => ['for' => 'person-first-name'], 'Your first name', '/label'];
        $this->assertHtml($expected, $result);

        $result = $this->Form->label('Person.first_name', 'Your first name', ['class' => 'my-class']);
        $expected = ['label' => ['for' => 'person-first-name', 'class' => 'my-class'], 'Your first name', '/label'];
        $this->assertHtml($expected, $result);

        $result = $this->Form->label('Person.first_name', 'Your first name',
            ['class' => 'my-class', 'id' => 'LabelID']);
        $expected = [
            'label' => ['for' => 'person-first-name', 'class' => 'my-class', 'id' => 'LabelID'],
            'Your first name',
            '/label'
        ];
        $this->assertHtml($expected, $result);

        $result = $this->Form->label('Person.first_name', '');
        $expected = ['label' => ['for' => 'person-first-name'], '/label'];
        $this->assertHtml($expected, $result);

        $result = $this->Form->label('Person.2.name', '');
        $expected = ['label' => ['for' => 'person-2-name'], '/label'];
        $this->assertHtml($expected, $result);
    }

    /**
     * testLabelContainInput method
     *
     * Test that label() can accept an input with the correct template vars.
     *
     * @return void
     */
    public function testLabelContainInput() {
        $this->Form->templates([
            'label' => '<label{{attrs}}>{{input}}{{text}}</label>',
        ]);
        $result = $this->Form->label('Person.accept_terms', 'Accept', [
            'input' => '<input type="checkbox" name="accept_tos"/>'
        ]);
        $expected = [
            'label' => ['for' => 'person-accept-terms'],
            'input' => ['type' => 'checkbox', 'name' => 'accept_tos'],
            'Accept',
            '/label',
        ];
        $this->assertHtml($expected, $result);
    }

    /**
     * testTextbox method
     *
     * Test textbox element generation.
     *
     * @return void
     */
    public function testTextbox() {
        $result = $this->Form->text('Model.field');
        $expected = ['input' => ['type' => 'text', 'name' => 'Model[field]', 'class' => 'form-control']];
        $this->assertHtml($expected, $result);

        $result = $this->Form->text('Model.field', ['type' => 'password']);
        $expected = ['input' => ['type' => 'password', 'name' => 'Model[field]', 'class' => 'form-control']];
        $this->assertHtml($expected, $result);

        $result = $this->Form->text('Model.field', ['id' => 'theID']);
        $expected = ['input' => ['type' => 'text', 'name' => 'Model[field]', 'id' => 'theID', 'class' => 'form-control']];
        $this->assertHtml($expected, $result);
    }

    /**
     * testTextBoxDataAndError method
     *
     * Test that text() hooks up with request data and error fields.
     *
     * @return void
     */
    public function testTextBoxDataAndError() {
        $this->article['errors'] = [
            'Contact' => ['text' => 'wrong']
        ];
        $this->Form->create($this->article);

        $this->Form->request->data['Model']['text'] = 'test <strong>HTML</strong> values';
        $result = $this->Form->text('Model.text');
        $expected = [
            'input' => [
                'type' => 'text',
                'name' => 'Model[text]',
                'value' => 'test &lt;strong&gt;HTML&lt;/strong&gt; values',
                'class' => 'form-control'
            ]
        ];
        $this->assertHtml($expected, $result);

        $this->Form->request->data['Contact']['text'] = 'test';
        $result = $this->Form->text('Contact.text', ['id' => 'theID']);
        $expected = [
            'input' => [
                'type' => 'text',
                'name' => 'Contact[text]',
                'value' => 'test',
                'id' => 'theID',
                'class' => 'form-control-danger form-control'
            ]
        ];
        $this->assertHtml($expected, $result);
    }

    /**
     * testDefaultValue method
     *
     * Test default value setting.
     *
     * @return void
     */
    public function testTextDefaultValue() {
        $this->Form->request->data['Model']['field'] = 'test';
        $result = $this->Form->text('Model.field', ['default' => 'default value']);
        $expected = ['input' => ['type' => 'text', 'name' => 'Model[field]', 'value' => 'test', 'class' => 'form-control']];
        $this->assertHtml($expected, $result);

        unset($this->Form->request->data['Model']['field']);
        $result = $this->Form->text('Model.field', ['default' => 'default value']);
        $expected = ['input' => ['type' => 'text', 'name' => 'Model[field]', 'value' => 'default value', 'class' => 'form-control']];
        $this->assertHtml($expected, $result);

        $this->loadFixtures('Articles');
        $Articles = TableRegistry::get('Articles');
        $title = $Articles->schema()->column('title');
        $Articles->schema()->addColumn(
            'title',
            ['default' => 'default title'] + $title
        );

        $entity = $Articles->newEntity();
        $this->Form->create($entity);
        // Get default value from schema
        $result = $this->Form->text('title');
        $expected = ['input' => ['type' => 'text', 'name' => 'title', 'value' => 'default title', 'class' => 'form-control']];
        $this->assertHtml($expected, $result);

        // Don't get value from schema
        $result = $this->Form->text('title', ['schemaDefault' => false]);
        $expected = ['input' => ['type' => 'text', 'name' => 'title', 'class' => 'form-control']];
        $this->assertHtml($expected, $result);

        // Custom default value overrides default value from schema
        $result = $this->Form->text('title', ['default' => 'override default']);
        $expected = ['input' => ['type' => 'text', 'name' => 'title', 'value' => 'override default', 'class' => 'form-control']];
        $this->assertHtml($expected, $result);

        // Default value from schema is used only for new entities.
        $entity->isNew(false);
        $result = $this->Form->text('title');
        $expected = ['input' => ['type' => 'text', 'name' => 'title', 'class' => 'form-control']];
        $this->assertHtml($expected, $result);
    }

    /**
     * testError method
     *
     * Test field error generation.
     *
     * @return void
     */
    public function testError() {
        $this->article['errors'] = [
            'Article' => ['field' => 'email']
        ];
        $this->Form->create($this->article);

        $result = $this->Form->error('Article.field');
        $expected = [
            ['div' => ['class' => 'form-control-feedback']],
            'email',
            '/div',
        ];
        $this->assertHtml($expected, $result);

        $result = $this->Form->error('Article.field', "<strong>Badness!</strong>");
        $expected = [
            ['div' => ['class' => 'form-control-feedback']],
            '&lt;strong&gt;Badness!&lt;/strong&gt;',
            '/div',
        ];
        $this->assertHtml($expected, $result);

        $result = $this->Form->error('Article.field', "<strong>Badness!</strong>", ['escape' => false]);
        $expected = [
            ['div' => ['class' => 'form-control-feedback']],
            '<strong', 'Badness!', '/strong',
            '/div',
        ];
        $this->assertHtml($expected, $result);
    }

    /**
     * testErrorRuleName method
     *
     * Test error translation can use rule names for translating.
     *
     * @return void
     */
    public function testErrorRuleName() {
        $this->article['errors'] = [
            'Article' => [
                'field' => ['email' => 'Your email was not good']
            ]
        ];
        $this->Form->create($this->article);

        $result = $this->Form->error('Article.field');
        $expected = [
            ['div' => ['class' => 'form-control-feedback']],
            'Your email was not good',
            '/div',
        ];
        $this->assertHtml($expected, $result);

        $result = $this->Form->error('Article.field', ['email' => 'Email in use']);
        $expected = [
            ['div' => ['class' => 'form-control-feedback']],
            'Email in use',
            '/div',
        ];
        $this->assertHtml($expected, $result);

        $result = $this->Form->error('Article.field', ['Your email was not good' => 'Email in use']);
        $expected = [
            ['div' => ['class' => 'form-control-feedback']],
            'Email in use',
            '/div',
        ];
        $this->assertHtml($expected, $result);

        $result = $this->Form->error('Article.field', [
            'email' => 'Key is preferred',
            'Your email was not good' => 'Email in use'
        ]);
        $expected = [
            ['div' => ['class' => 'form-control-feedback']],
            'Key is preferred',
            '/div',
        ];
        $this->assertHtml($expected, $result);
    }

    /**
     * testErrorMessages method
     *
     * Test error with nested lists.
     *
     * @return void
     */
    public function testErrorMessages() {
        $this->article['errors'] = [
            'Article' => ['field' => 'email']
        ];
        $this->Form->create($this->article);

        $result = $this->Form->error('Article.field', [
            'email' => 'No good!'
        ]);
        $expected = [
            'div' => ['class' => 'form-control-feedback'],
            'No good!',
            '/div'
        ];
        $this->assertHtml($expected, $result);
    }

    /**
     * testErrorMultipleMessages method
     *
     * Test error() with multiple messages.
     *
     * @return void
     */
    public function testErrorMultipleMessages() {
        $this->article['errors'] = [
            'field' => ['notBlank', 'email', 'Something else']
        ];
        $this->Form->create($this->article);

        $result = $this->Form->error('field', [
            'notBlank' => 'Cannot be empty',
            'email' => 'No good!'
        ]);
        $expected = [
            'div' => ['class' => 'form-control-feedback'],
            'ul' => [],
            '<li', 'Cannot be empty', '/li',
            '<li', 'No good!', '/li',
            '<li', 'Something else', '/li',
            '/ul',
            '/div'
        ];
        $this->assertHtml($expected, $result);
    }

    /**
     * testPassword method
     *
     * Test password element generation.
     *
     * @return void
     */
    public function testPassword() {
        $this->article['errors'] = [
            'Contact' => [
                'passwd' => 1
            ]
        ];
        $this->Form->create($this->article);

        $result = $this->Form->password('Contact.field');
        $expected = ['input' => ['type' => 'password', 'name' => 'Contact[field]', 'class' => 'form-control']];
        $this->assertHtml($expected, $result);

        $this->Form->request->data['Contact']['passwd'] = 'test';
        $result = $this->Form->password('Contact.passwd', ['id' => 'theID']);
        $expected = ['input' => ['type' => 'password', 'name' => 'Contact[passwd]', 'value' => 'test', 'id' => 'theID', 'class' => 'form-control-danger form-control']];
        $this->assertHtml($expected, $result);
    }

    /**
     * testRadio method
     *
     * Test radio element set generation.
     *
     * @return void
     */
    public function testRadio() {
        $result = $this->Form->radio('Model.field', ['option A']);
        $expected = [
            'input' => ['type' => 'hidden', 'name' => 'Model[field]', 'value' => ''],
            ['div' => ['class' => 'form-check']],
            'label' => ['for' => 'model-field-0', 'class' => 'form-check-label'],
            ['input' => ['type' => 'radio', 'name' => 'Model[field]', 'value' => '0', 'id' => 'model-field-0', 'class' => 'form-check-input']],
            'option A',
            '/label',
            '/div'
        ];
        $this->assertHtml($expected, $result);

        $result = $this->Form->radio('Model.field', new Collection(['option A']));
        $this->assertHtml($expected, $result);

        $result = $this->Form->radio('Model.field', ['option A', 'option B']);
        $expected = [
            'input' => ['type' => 'hidden', 'name' => 'Model[field]', 'value' => ''],
            ['div' => ['class' => 'form-check']],
            ['label' => ['for' => 'model-field-0', 'class' => 'form-check-label']],
            ['input' => ['type' => 'radio', 'name' => 'Model[field]', 'value' => '0', 'id' => 'model-field-0', 'class' => 'form-check-input']],
            'option A',
            '/label',
            '/div',
            ['div' => ['class' => 'form-check']],
            ['label' => ['for' => 'model-field-1', 'class' => 'form-check-label']],
            ['input' => ['type' => 'radio', 'name' => 'Model[field]', 'value' => '1', 'id' => 'model-field-1', 'class' => 'form-check-input']],
            'option B',
            '/label',
            '/div'
        ];
        $this->assertHtml($expected, $result);

        $result = $this->Form->radio(
            'Employee.gender',
            ['male' => 'Male', 'female' => 'Female'],
            ['form' => 'my-form']
        );
        $expected = [
            'input' => ['type' => 'hidden', 'name' => 'Employee[gender]', 'value' => '', 'form' => 'my-form'],
            ['div' => ['class' => 'form-check']],
            ['label' => ['for' => 'employee-gender-male', 'class' => 'form-check-label']],
            ['input' => ['type' => 'radio', 'name' => 'Employee[gender]', 'value' => 'male', 'id' => 'employee-gender-male', 'form' => 'my-form', 'class' => 'form-check-input']],
            'Male',
            '/label',
            '/div',
            ['div' => ['class' => 'form-check']],


            ['label' => ['for' => 'employee-gender-female', 'class' => 'form-check-label']],
            ['input' => ['type' => 'radio', 'name' => 'Employee[gender]', 'value' => 'female', 'id' => 'employee-gender-female', 'form' => 'my-form', 'class' => 'form-check-input']],
            'Female',
            '/label',
            '/div'
        ];
        $this->assertHtml($expected, $result);


        $result = $this->Form->radio('Model.field', ['option A', 'option B'], ['name' => 'Model[custom]']);
        $expected = [
            ['input' => ['type' => 'hidden', 'name' => 'Model[custom]', 'value' => '']],
            ['div' => ['class' => 'form-check']],
            ['label' => ['for' => 'model-custom-0', 'class' => 'form-check-label']],
            ['input' => ['type' => 'radio', 'name' => 'Model[custom]', 'value' => '0', 'id' => 'model-custom-0', 'class' => 'form-check-input']],
            'option A',
            '/label',
            '/div',
            ['div' => ['class' => 'form-check']],
            ['label' => ['for' => 'model-custom-1', 'class' => 'form-check-label']],
            ['input' => ['type' => 'radio', 'name' => 'Model[custom]', 'value' => '1', 'id' => 'model-custom-1', 'class' => 'form-check-input']],
            'option B',
            '/label',
            '/div'
        ];
        $this->assertHtml($expected, $result);

        $result = $this->Form->radio(
            'Employee.gender',
            [
                ['value' => 'male', 'text' => 'Male', 'style' => 'width:20px'],
                ['value' => 'female', 'text' => 'Female', 'style' => 'width:20px'],
            ]
        );
        $expected = [
            'input' => ['type' => 'hidden', 'name' => 'Employee[gender]', 'value' => ''],
            ['div' => ['class' => 'form-check']],
            ['label' => ['for' => 'employee-gender-male', 'class' => 'form-check-label']],
            ['input' => ['type' => 'radio', 'name' => 'Employee[gender]', 'value' => 'male',
                'id' => 'employee-gender-male', 'style' => 'width:20px', 'class' => 'form-check-input']],
            'Male',
            '/label',
            '/div',
            ['div' => ['class' => 'form-check']],
            ['label' => ['for' => 'employee-gender-female', 'class' => 'form-check-label']],
            ['input' => ['type' => 'radio', 'name' => 'Employee[gender]', 'value' => 'female',
                'id' => 'employee-gender-female', 'style' => 'width:20px', 'class' => 'form-check-input']],
            'Female',
            '/label',
            '/div'
        ];
        $this->assertHtml($expected, $result);
    }

    /**
     * testRadioDefaultValue method
     *
     * Test default value setting on radio() method.
     *
     * @return void
     */
    public function testRadioDefaultValue() {
        $this->loadFixtures('Articles');
        $Articles = TableRegistry::get('Articles');
        $title = $Articles->schema()->column('title');
        $Articles->schema()->addColumn(
            'title',
            ['default' => '1'] + $title
        );

        $this->Form->create($Articles->newEntity());

        $result = $this->Form->radio('title', ['option A', 'option B']);
        $expected = [
            ['input' => ['type' => 'hidden', 'name' => 'title', 'value' => '']],
            ['div' => ['class' => 'form-check']],
            ['label' => ['for' => 'title-0', 'class' => 'form-check-label']],
            ['input' => ['type' => 'radio', 'name' => 'title', 'value' => '0', 'id' => 'title-0', 'class' => 'form-check-input']],
            'option A',
            '/label',
            '/div',
            ['div' => ['class' => 'form-check']],
            ['label' => ['for' => 'title-1', 'class' => 'form-check-label selected']],
            ['input' => ['type' => 'radio', 'name' => 'title', 'value' => '1', 'id' => 'title-1', 'checked' => 'checked', 'class' => 'form-check-input']],
            'option B',
            '/label',
            '/div'
        ];
        $this->assertHtml($expected, $result);
    }

    /**
     * testInputRadio method
     *
     * Test that input works with radio types.
     *
     * @return void
     */
    public function testInputRadio() {
        $result = $this->Form->input('test', [
            'type' => 'radio',
            'options' => ['A', 'B'],
        ]);
        $expected = [
            'div' => ['class' => 'form-group'],
            '<label',
            'Test',
            '/label',
            ['input' => ['type' => 'hidden', 'name' => 'test', 'value' => '']],
            ['div' => ['class' => 'form-check']],
            ['label' => ['for' => 'test-0', 'class' => 'form-check-label']],
            ['input' => ['type' => 'radio', 'name' => 'test', 'value' => '0', 'id' => 'test-0', 'class' => 'form-check-input']],
            'A',
            '/label',
            '/div',
            ['div' => ['class' => 'form-check']],
            ['label' => ['for' => 'test-1', 'class' => 'form-check-label']],
            ['input' => ['type' => 'radio', 'name' => 'test', 'value' => '1', 'id' => 'test-1', 'class' => 'form-check-input']],
            'B',
            '/label',
            '/div',
            '/div'
        ];
        $this->assertHtml($expected, $result);
        $result = $this->Form->input('test', [
            'type' => 'radio',
            'options' => ['A', 'B'],
            'value' => '0'
        ]);
        $expected = [
            'div' => ['class' => 'form-group'],
            '<label',
            'Test',
            '/label',
            ['input' => ['type' => 'hidden', 'name' => 'test', 'value' => '']],
            ['div' => ['class' => 'form-check']],
            ['label' => ['for' => 'test-0', 'class' => 'form-check-label selected']],
            ['input' => ['type' => 'radio', 'checked' => 'checked', 'name' => 'test', 'value' => '0', 'id' => 'test-0', 'class' => 'form-check-input']],
            'A',
            '/label',
            '/div',
            ['div' => ['class' => 'form-check']],
            ['label' => ['for' => 'test-1', 'class' => 'form-check-label']],
            ['input' => ['type' => 'radio', 'name' => 'test', 'value' => '1', 'id' => 'test-1', 'class' => 'form-check-input']],
            'B',
            '/label',
            '/div',
            '/div'
            ,
        ];
        $this->assertHtml($expected, $result);

        $result = $this->Form->input('test', [
            'type' => 'radio',
            'options' => ['A', 'B'],
            'label' => false
        ]);
        $expected = [
            'div' => ['class' => 'form-group'],
            ['input' => ['type' => 'hidden', 'name' => 'test', 'value' => '']],
            ['div' => ['class' => 'form-check']],
            ['label' => ['for' => 'test-0', 'class' => 'form-check-label']],
            ['input' => ['type' => 'radio', 'name' => 'test', 'value' => '0', 'id' => 'test-0', 'class' => 'form-check-input']],
            'A',
            '/label',
            '/div',
            ['div' => ['class' => 'form-check']],
            ['label' => ['for' => 'test-1', 'class' => 'form-check-label']],
            ['input' => ['type' => 'radio', 'name' => 'test', 'value' => '1', 'id' => 'test-1', 'class' => 'form-check-input']],
            'B',
            '/label',
            '/div',
            '/div'
        ];
        $this->assertHtml($expected, $result);
    }

    /**
     * testRadioNoLabel method
     *
     * Test that radio() works with label = false.
     *
     * @return void
     */
    public function testRadioNoLabel() {
        $result = $this->Form->radio('Model.field', ['A', 'B'], ['label' => false]);
        $expected = [
            'input' => ['type' => 'hidden', 'name' => 'Model[field]', 'value' => ''],
            ['div' => ['class' => 'form-check']],
            ['input' => ['type' => 'radio', 'name' => 'Model[field]', 'value' => '0', 'id' => 'model-field-0', 'class' => 'form-check-input']],
            '/div',
            ['div' => ['class' => 'form-check']],
            ['input' => ['type' => 'radio', 'name' => 'Model[field]', 'value' => '1', 'id' => 'model-field-1', 'class' => 'form-check-input']],
            '/div'
        ];
        $this->assertHtml($expected, $result);
    }

    /**
     * testRadioInputInsideLabel method
     *
     * Test generating radio input inside label ala twitter bootstrap.
     *
     * @return void
     */
    public function testRadioInputInsideLabel() {
        $this->Form->templates([
            'label' => '<label{{attrs}}>{{input}}{{text}}</label>',
            'radioWrapper' => '{{label}}'
        ]);

        $result = $this->Form->radio('Model.field', ['option A', 'option B']);
        //@codingStandardsIgnoreStart
        $expected = [
            ['input' => [
                'type' => 'hidden',
                'name' => 'Model[field]',
                'value' => ''
            ]],
            ['label' => ['for' => 'model-field-0', 'class' => 'form-check-label']],
            ['input' => [
                'type' => 'radio',
                'name' => 'Model[field]',
                'value' => '0',
                'id' => 'model-field-0',
                'class' => 'form-check-input'
            ]],
            'option A',
            '/label',
            ['label' => ['for' => 'model-field-1', 'class' => 'form-check-label']],
            ['input' => [
                'type' => 'radio',
                'name' => 'Model[field]',
                'value' => '1',
                'id' => 'model-field-1',
                'class' => 'form-check-input'
            ]],
            'option B',
            '/label'
        ];
        //@codingStandardsIgnoreEnd
        $this->assertHtml($expected, $result);
    }

    /**
     * testRadioHiddenInputDisabling method
     *
     * Test disabling the hidden input for radio buttons.
     *
     * @return void
     */
    public function testRadioHiddenInputDisabling() {
        $result = $this->Form->radio('Model.1.field', ['option A'], ['hiddenField' => false]);
        $expected = [

            ['div' => ['class' => 'form-check']],
            'label' => ['for' => 'model-1-field-0', 'class' => 'form-check-label'],
            'input' => ['type' => 'radio', 'name' => 'Model[1][field]', 'value' => '0', 'id' => 'model-1-field-0', 'class' => 'form-check-input'],
            'option A',
            '/label',
            '/div'
        ];
        $this->assertHtml($expected, $result);
    }

    /**
     * testRadioOutOfRange method
     *
     * Test radio element set generation.
     *
     * @return void
     */
    public function testRadioOutOfRange() {
        $result = $this->Form->radio('Model.field', ['v' => 'value'], ['value' => 'nope']);
        $expected = [
            'input' => ['type' => 'hidden', 'name' => 'Model[field]', 'value' => ''],

            ['div' => ['class' => 'form-check']],
            'label' => ['for' => 'model-field-v', 'class' => 'form-check-label'],
            ['input' => ['type' => 'radio', 'name' => 'Model[field]', 'value' => 'v', 'id' => 'model-field-v', 'class' => 'form-check-input']],
            'value',
            '/label',
            '/div',
        ];
        $this->assertHtml($expected, $result);
    }

    /**
     * testSelect method
     *
     * Test select element generation.
     *
     * @return void
     */
    public function testSelect() {
        $result = $this->Form->select('Model.field', []);
        $expected = [
            'select' => ['name' => 'Model[field]', 'class' => 'form-control'],
            '/select'
        ];
        $this->assertHtml($expected, $result);

        $this->Form->request->data = ['Model' => ['field' => 'value']];
        $result = $this->Form->select('Model.field', ['value' => 'good', 'other' => 'bad']);
        $expected = [
            'select' => ['name' => 'Model[field]', 'class' => 'form-control'],
            ['option' => ['value' => 'value', 'selected' => 'selected']],
            'good',
            '/option',
            ['option' => ['value' => 'other']],
            'bad',
            '/option',
            '/select'
        ];
        $this->assertHtml($expected, $result);

        $result = $this->Form->select('Model.field', new Collection(['value' => 'good', 'other' => 'bad']));
        $this->assertHtml($expected, $result);

        $this->Form->request->data = [];
        $result = $this->Form->select('Model.field', ['value' => 'good', 'other' => 'bad']);
        $expected = [
            'select' => ['name' => 'Model[field]', 'class' => 'form-control'],
            ['option' => ['value' => 'value']],
            'good',
            '/option',
            ['option' => ['value' => 'other']],
            'bad',
            '/option',
            '/select'
        ];
        $this->assertHtml($expected, $result);

        $options = [
            ['value' => 'first', 'text' => 'First'],
            ['value' => 'first', 'text' => 'Another First'],
        ];
        $result = $this->Form->select(
            'Model.field',
            $options,
            ['escape' => false, 'empty' => false]
        );
        $expected = [
            'select' => ['name' => 'Model[field]', 'class' => 'form-control'],
            ['option' => ['value' => 'first']],
            'First',
            '/option',
            ['option' => ['value' => 'first']],
            'Another First',
            '/option',
            '/select'
        ];
        $this->assertHtml($expected, $result);

        $this->Form->request->data = ['Model' => ['contact_id' => 228]];
        $result = $this->Form->select(
            'Model.contact_id',
            ['228' => '228 value', '228-1' => '228-1 value', '228-2' => '228-2 value'],
            ['escape' => false, 'empty' => 'pick something']
        );

        $expected = [
            'select' => ['name' => 'Model[contact_id]', 'class' => 'form-control'],
            ['option' => ['value' => '']], 'pick something', '/option',
            ['option' => ['value' => '228', 'selected' => 'selected']], '228 value', '/option',
            ['option' => ['value' => '228-1']], '228-1 value', '/option',
            ['option' => ['value' => '228-2']], '228-2 value', '/option',
            '/select'
        ];
        $this->assertHtml($expected, $result);

        $this->Form->request->data['Model']['field'] = 0;
        $result = $this->Form->select('Model.field', ['0' => 'No', '1' => 'Yes']);
        $expected = [
            'select' => ['name' => 'Model[field]', 'class' => 'form-control'],
            ['option' => ['value' => '0', 'selected' => 'selected']], 'No', '/option',
            ['option' => ['value' => '1']], 'Yes', '/option',
            '/select'
        ];
        $this->assertHtml($expected, $result);
    }

    /**
     * testSelectEscapeHtml method
     *
     * Test that select() escapes HTML.
     *
     * @return void
     */
    public function testSelectEscapeHtml() {
        $result = $this->Form->select(
            'Model.field',
            ['first' => 'first "html" <chars>', 'second' => 'value'],
            ['empty' => false]
        );
        $expected = [
            'select' => ['name' => 'Model[field]', 'class' => 'form-control'],
            ['option' => ['value' => 'first']],
            'first &quot;html&quot; &lt;chars&gt;',
            '/option',
            ['option' => ['value' => 'second']],
            'value',
            '/option',
            '/select'
        ];
        $this->assertHtml($expected, $result);

        $result = $this->Form->select(
            'Model.field',
            ['first' => 'first "html" <chars>', 'second' => 'value'],
            ['escape' => false, 'empty' => false]
        );
        $expected = [
            'select' => ['name' => 'Model[field]', 'class' => 'form-control'],
            ['option' => ['value' => 'first']],
            'first "html" <chars>',
            '/option',
            ['option' => ['value' => 'second']],
            'value',
            '/option',
            '/select'
        ];
        $this->assertHtml($expected, $result);
    }

    /**
     * testSelectRequired method
     *
     * Test select() with required and disabled attributes.
     *
     * @return void
     */
    public function testSelectRequired() {
        $this->article['required'] = [
            'user_id' => true
        ];
        $this->Form->create($this->article);
        $result = $this->Form->select('user_id', ['option A']);
        $expected = [
            'select' => [
                'name' => 'user_id',
                'required' => 'required',
                'class' => 'form-control'
            ],
            ['option' => ['value' => '0']], 'option A', '/option',
            '/select'
        ];
        $this->assertHtml($expected, $result);

        $result = $this->Form->select('user_id', ['option A'], ['disabled' => true]);
        $expected = [
            'select' => [
                'name' => 'user_id',
                'disabled' => 'disabled',
                'class' => 'form-control'
            ],
            ['option' => ['value' => '0']], 'option A', '/option',
            '/select'
        ];
        $this->assertHtml($expected, $result);
    }

    /**
     * testNestedSelect method
     *
     * Test select element generation with optgroups.
     *
     * @return void
     */
    public function testNestedSelect() {
        $result = $this->Form->select(
            'Model.field',
            [1 => 'One', 2 => 'Two', 'Three' => [
                3 => 'Three', 4 => 'Four', 5 => 'Five'
            ]],
            ['empty' => false]
        );
        $expected = [
            'select' => ['name' => 'Model[field]', 'class' => 'form-control'],
            ['option' => ['value' => 1]],
            'One',
            '/option',
            ['option' => ['value' => 2]],
            'Two',
            '/option',
            ['optgroup' => ['label' => 'Three']],
            ['option' => ['value' => 3]],
            'Three',
            '/option',
            ['option' => ['value' => 4]],
            'Four',
            '/option',
            ['option' => ['value' => 5]],
            'Five',
            '/option',
            '/optgroup',
            '/select'
        ];
        $this->assertHtml($expected, $result);
    }

    /**
     * testSelectMultiple method
     *
     * Test generation of multiple select elements.
     *
     * @return void
     */
    public function testSelectMultiple() {
        $options = ['first', 'second', 'third'];
        $result = $this->Form->select(
            'Model.multi_field',
            $options,
            ['form' => 'my-form', 'multiple' => true]
        );
        $expected = [
            'input' => [
                'type' => 'hidden',
                'name' => 'Model[multi_field]',
                'value' => '',
                'form' => 'my-form',
            ],
            'select' => [
                'name' => 'Model[multi_field][]',
                'multiple' => 'multiple',
                'form' => 'my-form',
                'class' => 'form-control'
            ],
            ['option' => ['value' => '0']],
            'first',
            '/option',
            ['option' => ['value' => '1']],
            'second',
            '/option',
            ['option' => ['value' => '2']],
            'third',
            '/option',
            '/select'
        ];
        $this->assertHtml($expected, $result);

        $result = $this->Form->select(
            'Model.multi_field',
            $options,
            ['multiple' => 'multiple', 'form' => 'my-form']
        );
        $this->assertHtml($expected, $result);
    }

    /**
     * testCheckboxZeroValue method
     *
     * Test that a checkbox can have 0 for the value and 1 for the hidden input.
     *
     * @return void
     */
    public function testCheckboxZeroValue() {
        $result = $this->Form->input('User.get_spam', [
            'type' => 'checkbox',
            'value' => '0',
            'hiddenField' => '1',
        ]);
        $expected = [
            'div' => ['class' => 'form-check'],
            'label' => ['for' => 'user-get-spam', 'class' => 'form-check-label'],
            ['input' => [
                'type' => 'hidden', 'name' => 'User[get_spam]',
                'value' => '1'
            ]],
            ['input' => [
                'type' => 'checkbox', 'name' => 'User[get_spam]',
                'value' => '0', 'id' => 'user-get-spam',
                'class' => 'form-check-input'
            ]],
            'Get Spam',
            '/label',
            '/div'
        ];
        $this->assertHtml($expected, $result);
    }

    /**
     * testHabtmSelectBox method
     *
     * Test generation of habtm select boxes.
     *
     * @return void
     */
    public function testHabtmSelectBox() {
        $this->loadFixtures('Articles');
        $options = [
            1 => 'blue',
            2 => 'red',
            3 => 'green'
        ];
        $tags = [
            new Entity(['id' => 1, 'name' => 'blue']),
            new Entity(['id' => 3, 'name' => 'green'])
        ];
        $article = new Article(['tags' => $tags]);
        $this->Form->create($article);
        $result = $this->Form->input('tags._ids', ['options' => $options]);
        $expected = [
            'div' => ['class' => 'form-group'],
            'label' => ['for' => 'tags-ids', 'class' => 'col-form-label'],
            'Tags',
            '/label',
            'input' => ['type' => 'hidden', 'name' => 'tags[_ids]', 'value' => ''],
            'select' => [
                'name' => 'tags[_ids][]', 'id' => 'tags-ids',
                'multiple' => 'multiple',
                'class' => 'form-control'
            ],
            ['option' => ['value' => '1', 'selected' => 'selected']],
            'blue',
            '/option',
            ['option' => ['value' => '2']],
            'red',
            '/option',
            ['option' => ['value' => '3', 'selected' => 'selected']],
            'green',
            '/option',
            '/select',
            '/div'
        ];
        $this->assertHtml($expected, $result);

        // make sure only 50 is selected, and not 50f5c0cf
        $options = [
            '1' => 'blue',
            '50f5c0cf' => 'red',
            '50' => 'green'
        ];
        $tags = [
            new Entity(['id' => 1, 'name' => 'blue']),
            new Entity(['id' => 50, 'name' => 'green'])
        ];
        $article = new Article(['tags' => $tags]);
        $this->Form->create($article);
        $result = $this->Form->input('tags._ids', ['options' => $options]);
        $expected = [
            'div' => ['class' => 'form-group'],
            'label' => ['for' => 'tags-ids', 'class' => 'col-form-label'],
            'Tags',
            '/label',
            'input' => ['type' => 'hidden', 'name' => 'tags[_ids]', 'value' => ''],
            'select' => [
                'name' => 'tags[_ids][]', 'id' => 'tags-ids',
                'multiple' => 'multiple', 'class' => 'form-control'
            ],
            ['option' => ['value' => '1', 'selected' => 'selected']],
            'blue',
            '/option',
            ['option' => ['value' => '50f5c0cf']],
            'red',
            '/option',
            ['option' => ['value' => '50', 'selected' => 'selected']],
            'green',
            '/option',
            '/select',
            '/div'
        ];
        $this->assertHtml($expected, $result);

        $spacecraft = [
            1 => 'Orion',
            2 => 'Helios'
        ];
        $this->View->viewVars['spacecraft'] = $spacecraft;
        $this->Form->create();
        $result = $this->Form->input('spacecraft._ids');
        $expected = [
            'div' => ['class' => 'form-group'],
            'label' => ['for' => 'spacecraft-ids', 'class' => 'col-form-label'],
            'Spacecraft',
            '/label',
            'input' => ['type' => 'hidden', 'name' => 'spacecraft[_ids]', 'value' => ''],
            'select' => [
                'name' => 'spacecraft[_ids][]', 'id' => 'spacecraft-ids',
                'multiple' => 'multiple', 'class' => 'form-control'
            ],
            ['option' => ['value' => '1']],
            'Orion',
            '/option',
            ['option' => ['value' => '2']],
            'Helios',
            '/option',
            '/select',
            '/div'
        ];
        $this->assertHtml($expected, $result);
    }

    /**
     * testErrorsForBelongsToManySelect method
     *
     * Tests that errors for belongsToMany select fields are being
     * picked up properly.
     *
     * @return void
     */
    public function testErrorsForBelongsToManySelect() {
        $spacecraft = [
            1 => 'Orion',
            2 => 'Helios'
        ];
        $this->View->viewVars['spacecraft'] = $spacecraft;

        $this->loadFixtures('Articles');
        $article = new Article();
        $article->errors('spacecraft', ['Invalid']);

        $this->Form->create($article);
        $result = $this->Form->input('spacecraft._ids');

        $expected = [
            ['div' => ['class' => 'form-group has-danger']],
            'label' => ['for' => 'spacecraft-ids', 'class' => 'col-form-label'],
            'Spacecraft',
            '/label',
            'input' => ['type' => 'hidden', 'name' => 'spacecraft[_ids]', 'value' => ''],
            'select' => [
                'name' => 'spacecraft[_ids][]', 'id' => 'spacecraft-ids',
                'multiple' => 'multiple', 'class' => 'form-control'
            ],
            ['option' => ['value' => '1']],
            'Orion',
            '/option',
            ['option' => ['value' => '2']],
            'Helios',
            '/option',
            '/select',
            ['div' => ['class' => 'form-control-feedback']],
            'Invalid',
            '/div',
            '/div'
        ];
        $this->assertHtml($expected, $result);
    }

    /**
     * testSelectMultipleCheckboxes method
     *
     * Test generation of multi select elements in checkbox format.
     *
     * @return void
     */
    public function testSelectMultipleCheckboxes() {
        $result = $this->Form->select(
            'Model.multi_field',
            ['first', 'second', 'third'],
            ['multiple' => 'checkbox']
        );

        $expected = [
            'input' => [
                'type' => 'hidden', 'name' => 'Model[multi_field]', 'value' => ''
            ],
            ['div' => ['class' => 'form-check']],
            ['label' => ['for' => 'model-multi-field-0', 'class' => 'form-check-label']],
            ['input' => [
                'type' => 'checkbox', 'name' => 'Model[multi_field][]',
                'value' => '0', 'id' => 'model-multi-field-0', 'class' => 'form-check-input'
            ]],
            'first',
            '/label',
            '/div',
            ['div' => ['class' => 'form-check']],
            ['label' => ['for' => 'model-multi-field-1', 'class' => 'form-check-label']],
            ['input' => [
                'type' => 'checkbox', 'name' => 'Model[multi_field][]',
                'value' => '1', 'id' => 'model-multi-field-1', 'class' => 'form-check-input'
            ]],
            'second',
            '/label',
            '/div',
            ['div' => ['class' => 'form-check']],
            ['label' => ['for' => 'model-multi-field-2', 'class' => 'form-check-label']],
            ['input' => [
                'type' => 'checkbox', 'name' => 'Model[multi_field][]',
                'value' => '2', 'id' => 'model-multi-field-2', 'class' => 'form-check-input'
            ]],
            'third',
            '/label',
            '/div'
        ];
        $this->assertHtml($expected, $result);

        $result = $this->Form->select(
            'Model.multi_field',
            ['a+' => 'first', 'a++' => 'second', 'a+++' => 'third'],
            ['multiple' => 'checkbox']
        );
        $expected = [
            'input' => [
                'type' => 'hidden', 'name' => 'Model[multi_field]', 'value' => ''
            ],
            ['div' => ['class' => 'form-check']],
            ['label' => ['for' => 'model-multi-field-a+', 'class' => 'form-check-label']],
            ['input' => [
                'type' => 'checkbox', 'name' => 'Model[multi_field][]',
                'value' => 'a+', 'id' => 'model-multi-field-a+', 'class' => 'form-check-input'
            ]],
            'first',
            '/label',
            '/div',
            ['div' => ['class' => 'form-check']],
            ['label' => ['for' => 'model-multi-field-a++', 'class' => 'form-check-label']],
            ['input' => [
                'type' => 'checkbox', 'name' => 'Model[multi_field][]',
                'value' => 'a++', 'id' => 'model-multi-field-a++', 'class' => 'form-check-input'
            ]],
            'second',
            '/label',
            '/div',
            ['div' => ['class' => 'form-check']],
            ['label' => ['for' => 'model-multi-field-a+++', 'class' => 'form-check-label']],
            ['input' => [
                'type' => 'checkbox', 'name' => 'Model[multi_field][]',
                'value' => 'a+++', 'id' => 'model-multi-field-a+++', 'class' => 'form-check-input'
            ]],
            'third',
            '/label',
            '/div'
        ];
        $this->assertHtml($expected, $result);

        $result = $this->Form->select(
            'Model.multi_field',
            ['a>b' => 'first', 'a<b' => 'second', 'a"b' => 'third'],
            ['multiple' => 'checkbox']
        );
        $expected = [
            'input' => [
                'type' => 'hidden', 'name' => 'Model[multi_field]', 'value' => ''
            ],
            ['div' => ['class' => 'form-check']],
            ['label' => ['for' => 'model-multi-field-a-b', 'class' => 'form-check-label']],
            ['input' => [
                'type' => 'checkbox', 'name' => 'Model[multi_field][]',
                'value' => 'a&gt;b', 'id' => 'model-multi-field-a-b',
                'class' => 'form-check-input'
            ]],
            'first',
            '/label',
            '/div',
            ['div' => ['class' => 'form-check']],
            ['label' => ['for' => 'model-multi-field-a-b1', 'class' => 'form-check-label']],
            ['input' => [
                'type' => 'checkbox', 'name' => 'Model[multi_field][]',
                'value' => 'a&lt;b', 'id' => 'model-multi-field-a-b1',
                'class' => 'form-check-input'
            ]],
            'second',
            '/label',
            '/div',
            ['div' => ['class' => 'form-check']],
            ['label' => ['for' => 'model-multi-field-a-b2', 'class' => 'form-check-label']],
            ['input' => [
                'type' => 'checkbox', 'name' => 'Model[multi_field][]',
                'value' => 'a&quot;b', 'id' => 'model-multi-field-a-b2',
                'class' => 'form-check-input'
            ]],
            'third',
            '/label',
            '/div'
        ];
        $this->assertHtml($expected, $result);
    }

    /**
     * testSelectMultipleCheckboxRequestData method
     *
     * Ensure that multiCheckbox reads from the request data.
     *
     * @return void
     */
    public function testSelectMultipleCheckboxRequestData() {
        $this->Form->request->data = ['Model' => ['tags' => [1]]];
        $result = $this->Form->select(
            'Model.tags',
            ['1' => 'first', 'Array' => 'Array'],
            ['multiple' => 'checkbox']
        );
        $expected = [
            'input' => [
                'type' => 'hidden', 'name' => 'Model[tags]', 'value' => ''
            ],
            ['div' => ['class' => 'form-check']],
            ['label' => ['for' => 'model-tags-1', 'class' => 'form-check-label selected']],
            ['input' => [
                'type' => 'checkbox', 'name' => 'Model[tags][]',
                'value' => '1', 'id' => 'model-tags-1', 'checked' => 'checked',
                'class' => 'form-check-input'
            ]],
            'first',
            '/label',
            '/div',

            ['div' => ['class' => 'form-check']],
            ['label' => ['for' => 'model-tags-array', 'class' => 'form-check-label']],
            ['input' => [
                'type' => 'checkbox', 'name' => 'Model[tags][]',
                'value' => 'Array', 'id' => 'model-tags-array',
                'class' => 'form-check-input'
            ]],
            'Array',
            '/label',
            '/div'
        ];
        $this->assertHtml($expected, $result);
    }

    /**
     * testInputMultipleCheckboxes method
     *
     * Test input() resulting in multi select elements being generated.
     *
     * @return void
     */
    public function testInputMultipleCheckboxes() {
        $result = $this->Form->input('Model.multi_field', [
            'options' => ['first', 'second', 'third'],
            'multiple' => 'checkbox'
        ]);
        $expected = [
            ['div' => ['class' => 'form-group']],
            ['label' => ['for' => 'model-multi-field']],
            'Multi Field',
            '/label',
            'input' => ['type' => 'hidden', 'name' => 'Model[multi_field]', 'value' => ''],
            ['div' => ['class' => 'form-check']],
            ['label' => ['for' => 'model-multi-field-0', 'class' => 'form-check-label']],
            ['input' => [
                'type' => 'checkbox', 'name' => 'Model[multi_field][]',
                'value' => '0', 'id' => 'model-multi-field-0',
                'class' => 'form-check-input'
            ]],
            'first',
            '/label',
            '/div',
            ['div' => ['class' => 'form-check']],
            ['label' => ['for' => 'model-multi-field-1', 'class' => 'form-check-label']],
            ['input' => [
                'type' => 'checkbox', 'name' => 'Model[multi_field][]',
                'value' => '1', 'id' => 'model-multi-field-1',
                'class' => 'form-check-input'
            ]],
            'second',
            '/label',
            '/div',
            ['div' => ['class' => 'form-check']],
            ['label' => ['for' => 'model-multi-field-2', 'class' => 'form-check-label']],
            ['input' => [
                'type' => 'checkbox', 'name' => 'Model[multi_field][]',
                'value' => '2', 'id' => 'model-multi-field-2',
                'class' => 'form-check-input'
            ]],
            'third',
            '/label',
            '/div',
            '/div'
        ];
        $this->assertHtml($expected, $result);

        $result = $this->Form->input('Model.multi_field', [
            'options' => ['a' => 'first', 'b' => 'second', 'c' => 'third'],
            'multiple' => 'checkbox'
        ]);
        $expected = [
            ['div' => ['class' => 'form-group']],
            ['label' => ['for' => 'model-multi-field']],
            'Multi Field',
            '/label',
            'input' => ['type' => 'hidden', 'name' => 'Model[multi_field]', 'value' => ''],
            ['div' => ['class' => 'form-check']],
            ['label' => ['for' => 'model-multi-field-a', 'class' => 'form-check-label']],
            ['input' => [
                'type' => 'checkbox', 'name' => 'Model[multi_field][]',
                'value' => 'a', 'id' => 'model-multi-field-a',
                'class' => 'form-check-input'
            ]],
            'first',
            '/label',
            '/div',
            ['div' => ['class' => 'form-check']],
            ['label' => ['for' => 'model-multi-field-b', 'class' => 'form-check-label']],
            ['input' => [
                'type' => 'checkbox', 'name' => 'Model[multi_field][]',
                'value' => 'b', 'id' => 'model-multi-field-b',
                'class' => 'form-check-input'
            ]],
            'second',
            '/label',
            '/div',
            ['div' => ['class' => 'form-check']],
            ['label' => ['for' => 'model-multi-field-c', 'class' => 'form-check-label']],
            ['input' => [
                'type' => 'checkbox', 'name' => 'Model[multi_field][]',
                'value' => 'c', 'id' => 'model-multi-field-c',
                'class' => 'form-check-input'
            ]],
            'third',
            '/label',
            '/div',
            '/div'
        ];
        $this->assertHtml($expected, $result);
    }

    /**
     * testSelectCheckboxMultipleOverrideName method
     *
     * Test that select() with multiple = checkbox works with overriding name attribute.
     *
     * @return void
     */
    public function testSelectCheckboxMultipleOverrideName() {
        $result = $this->Form->select('category', ['1', '2'], [
            'multiple' => 'checkbox',
            'name' => 'fish',
        ]);
        $expected = [
            'input' => ['type' => 'hidden', 'name' => 'fish', 'value' => ''],
            ['div' => ['class' => 'form-check']],
            ['label' => ['for' => 'fish-0', 'class' => 'form-check-label']],
            ['input' => [
                'type' => 'checkbox', 'name' => 'fish[]',
                'value' => '0', 'id' => 'fish-0',
                'class' => 'form-check-input'
            ]],
            '1',
            '/label',
            '/div',
            ['div' => ['class' => 'form-check']],
            ['label' => ['for' => 'fish-1', 'class' => 'form-check-label']],
            ['input' => [
                'type' => 'checkbox', 'name' => 'fish[]',
                'value' => '1', 'id' => 'fish-1',
                'class' => 'form-check-input'
            ]],
            '2',
            '/label',
            '/div'
        ];
        $this->assertHtml($expected, $result);

        $result = $this->Form->multiCheckbox(
            'category',
            new Collection(['1', '2']),
            ['name' => 'fish']
        );
        $this->assertHtml($expected, $result);

        $result = $this->Form->multiCheckbox('category', ['1', '2'], [
            'name' => 'fish',
        ]);
        $this->assertHtml($expected, $result);
    }

    /**
     * testInputMultiCheckbox method
     *
     * Test that input() works with multicheckbox.
     *
     * @return void
     */
    public function testInputMultiCheckbox() {
        $result = $this->Form->input('category', [
            'type' => 'multicheckbox',
            'options' => ['1', '2'],
        ]);
        $expected = [
            ['div' => ['class' => 'form-group']],
            '<label',
            'Category',
            '/label',
            'input' => ['type' => 'hidden', 'name' => 'category', 'value' => ''],
            ['div' => ['class' => 'form-check']],
            ['label' => ['for' => 'category-0', 'class' => 'form-check-label']],
            ['input' => [
                'type' => 'checkbox', 'name' => 'category[]',
                'value' => '0', 'id' => 'category-0',
                'class' => 'form-check-input'
            ]],
            '1',
            '/label',
            '/div',
            ['div' => ['class' => 'form-check']],
            ['label' => ['for' => 'category-1', 'class' => 'form-check-label']],
            ['input' => [
                'type' => 'checkbox', 'name' => 'category[]',
                'value' => '1', 'id' => 'category-1',
                'class' => 'form-check-input'
            ]],
            '2',
            '/label',
            '/div',
            '/div',
        ];
        $this->assertHtml($expected, $result);
    }

    /**
     * testCheckbox method
     *
     * Test generation of checkboxes.
     *
     * @return void
     */
    public function testCheckbox() {
        $result = $this->Form->checkbox('Model.field');
        $expected = [
            'input' => ['type' => 'hidden', 'name' => 'Model[field]', 'value' => '0'],
            ['input' => [
                'type' => 'checkbox', 'name' => 'Model[field]',
                'value' => '1', 'class' => 'form-check-input']],
        ];
        $this->assertHtml($expected, $result);

        $result = $this->Form->checkbox('Model.field', [
            'id' => 'theID',
            'value' => 'myvalue',
            'form' => 'my-form',
        ]);
        $expected = [
            'input' => ['type' => 'hidden', 'name' => 'Model[field]', 'value' => '0', 'form' => 'my-form'],
            ['input' => [
                'type' => 'checkbox', 'name' => 'Model[field]',
                'value' => 'myvalue', 'id' => 'theID',
                'form' => 'my-form', 'class' => 'form-check-input'
            ]]
        ];
        $this->assertHtml($expected, $result);
    }

    /**
     * testCheckboxDefaultValue method
     *
     * Test default value setting on checkbox() method.
     *
     * @return void
     */
    public function testCheckboxDefaultValue() {
        $this->Form->request->data['Model']['field'] = false;
        $result = $this->Form->checkbox('Model.field', ['default' => true, 'hiddenField' => false]);
        $expected = [
            'input' => [
                'type' => 'checkbox', 'name' => 'Model[field]',
                'value' => '1', 'class' => 'form-check-input'
            ]];
        $this->assertHtml($expected, $result);

        unset($this->Form->request->data['Model']['field']);
        $result = $this->Form->checkbox('Model.field', ['default' => true, 'hiddenField' => false]);
        $expected = [
            'input' => [
                'type' => 'checkbox', 'name' => 'Model[field]',
                'value' => '1', 'checked' => 'checked',
                'class' => 'form-check-input'
            ]];
        $this->assertHtml($expected, $result);

        $this->Form->request->data['Model']['field'] = true;
        $result = $this->Form->checkbox('Model.field', ['default' => false, 'hiddenField' => false]);
        $expected = [
            'input' => [
                'type' => 'checkbox', 'name' => 'Model[field]',
                'value' => '1', 'checked' => 'checked',
                'class' => 'form-check-input'
            ]];
        $this->assertHtml($expected, $result);

        unset($this->Form->request->data['Model']['field']);
        $result = $this->Form->checkbox('Model.field', ['default' => false, 'hiddenField' => false]);
        $expected = [
            'input' => [
                'type' => 'checkbox', 'name' => 'Model[field]',
                'value' => '1', 'class' => 'form-check-input'
            ]];
        $this->assertHtml($expected, $result);

        $this->loadFixtures('Articles');
        $Articles = TableRegistry::get('Articles');
        $Articles->schema()->addColumn(
            'published',
            ['type' => 'boolean', 'null' => false, 'default' => true]
        );

        $this->Form->create($Articles->newEntity());
        $result = $this->Form->checkbox('published', ['hiddenField' => false]);
        $expected = [
            'input' => [
                'type' => 'checkbox', 'name' => 'published',
                'value' => '1', 'checked' => 'checked',
                'class' => 'form-check-input'
            ]];
        $this->assertHtml($expected, $result);
    }

    /**
     * testCheckboxCheckedAndError method
     *
     * Test checkbox being checked or having errors.
     *
     * @return void
     */
    public function testCheckboxCheckedAndError() {
        $this->article['errors'] = [
            'published' => true
        ];
        $this->Form->request->data['published'] = 'myvalue';
        $this->Form->create($this->article);

        $result = $this->Form->checkbox('published', ['id' => 'theID', 'value' => 'myvalue']);
        $expected = [
            'input' => ['type' => 'hidden', 'class' => 'form-control-danger', 'name' => 'published', 'value' => '0'],
            ['input' => [
                'type' => 'checkbox',
                'name' => 'published',
                'value' => 'myvalue',
                'id' => 'theID',
                'checked' => 'checked',
                'class' => 'form-control-danger form-check-input'
            ]]
        ];
        $this->assertHtml($expected, $result);

        $this->Form->request->data['published'] = '';
        $result = $this->Form->checkbox('published');
        $expected = [
            'input' => ['type' => 'hidden', 'class' => 'form-control-danger', 'name' => 'published', 'value' => '0'],
            ['input' => ['type' => 'checkbox', 'name' => 'published', 'value' => '1', 'class' => 'form-control-danger form-check-input']]
        ];
        $this->assertHtml($expected, $result);
    }

    /**
     * testCheckboxCustomNameAttribute method
     *
     * Test checkbox() with a custom name attribute.
     *
     * @return void
     */
    public function testCheckboxCustomNameAttribute() {
        $result = $this->Form->checkbox('Test.test', ['name' => 'myField']);
        $expected = [
            'input' => ['type' => 'hidden', 'name' => 'myField', 'value' => '0'],
            ['input' => ['type' => 'checkbox', 'name' => 'myField', 'value' => '1', 'class' => 'form-check-input']]
        ];
        $this->assertHtml($expected, $result);
    }

    /**
     * testCheckboxHiddenField method
     *
     * Test that the hidden input for checkboxes can be omitted or set to a
     * specific value.
     *
     * @return void
     */
    public function testCheckboxHiddenField() {
        $result = $this->Form->checkbox('UserForm.something', [
            'hiddenField' => false
        ]);
        $expected = [
            'input' => [
                'type' => 'checkbox',
                'name' => 'UserForm[something]',
                'value' => '1',
                'class' => 'form-check-input'
            ],
        ];
        $this->assertHtml($expected, $result);

        $result = $this->Form->checkbox('UserForm.something', [
            'value' => 'Y',
            'hiddenField' => 'N',
        ]);
        $expected = [
            ['input' => [
                'type' => 'hidden', 'name' => 'UserForm[something]',
                'value' => 'N'
            ]],
            ['input' => [
                'type' => 'checkbox', 'name' => 'UserForm[something]',
                'value' => 'Y', 'class' => 'form-check-input'
            ]],
        ];
        $this->assertHtml($expected, $result);
    }

    /**
     * testInputLabelFalse method
     *
     * Test the label option being set to false.
     *
     * @return void
     */
    public function testInputLabelFalse() {
        $this->Form->create($this->article);
        $result = $this->Form->input('title', ['label' => false]);
        $expected = [
            'div' => ['class' => 'form-group'],
            'input' => ['type' => 'text', 'required' => 'required', 'id' => 'title', 'name' => 'title', 'class' => 'form-control'],
            '/div'
        ];
        $this->assertHtml($expected, $result);
    }

    /**
     * testTextArea method
     *
     * Test generation of a textarea input.
     *
     * @return void
     */
    public function testTextArea() {
        $this->Form->request->data = ['field' => 'some test data'];
        $result = $this->Form->textarea('field');
        $expected = [
            'textarea' => ['name' => 'field', 'rows' => 5, 'class' => 'form-control'],
            'some test data',
            '/textarea',
        ];
        $this->assertHtml($expected, $result);

        $result = $this->Form->textarea('user.bio');
        $expected = [
            'textarea' => ['name' => 'user[bio]', 'rows' => 5, 'class' => 'form-control'],
            '/textarea',
        ];
        $this->assertHtml($expected, $result);

        $this->Form->request->data = ['field' => 'some <strong>test</strong> data with <a href="#">HTML</a> chars'];
        $result = $this->Form->textarea('field');
        $expected = [
            'textarea' => ['name' => 'field', 'rows' => 5, 'class' => 'form-control'],
            htmlentities('some <strong>test</strong> data with <a href="#">HTML</a> chars'),
            '/textarea',
        ];
        $this->assertHtml($expected, $result);

        $this->Form->request->data = [
            'Model' => ['field' => 'some <strong>test</strong> data with <a href="#">HTML</a> chars']
        ];
        $result = $this->Form->textarea('Model.field', ['escape' => false]);
        $expected = [
            'textarea' => ['name' => 'Model[field]', 'rows' => 5, 'class' => 'form-control'],
            'some <strong>test</strong> data with <a href="#">HTML</a> chars',
            '/textarea',
        ];
        $this->assertHtml($expected, $result);

        $result = $this->Form->textarea('0.OtherModel.field');
        $expected = [
            'textarea' => ['name' => '0[OtherModel][field]', 'rows' => 5, 'class' => 'form-control'],
            '/textarea'
        ];
        $this->assertHtml($expected, $result);
    }

    /**
     * testTextAreaWithStupidCharacters method
     *
     * Test text area with non-ascii characters.
     *
     * @return void
     */
    public function testTextAreaWithStupidCharacters() {
        $result = $this->Form->textarea('Post.content', [
            'value' => "GREAT®",
            'rows' => '15',
            'cols' => '75'
        ]);
        $expected = [
            'textarea' => ['name' => 'Post[content]', 'rows' => '15', 'cols' => '75', 'class' => 'form-control'],
            'GREAT®',
            '/textarea',
        ];
        $this->assertHtml($expected, $result);
    }

    /**
     * testTextAreaMaxLength method
     *
     * Test textareas maxlength read from schema.
     *
     * @return void
     */
    public function testTextAreaMaxLength() {
        $this->Form->create([
            'schema' => [
                'stuff' => ['type' => 'string', 'length' => 10],
            ]
        ]);
        $result = $this->Form->input('other', ['type' => 'textarea']);
        $expected = [
            'div' => ['class' => 'form-group'],
            'label' => ['for' => 'other', 'class' => 'col-form-label'],
            'Other',
            '/label',
            'textarea' => ['name' => 'other', 'id' => 'other', 'rows' => 5, 'class' => 'form-control'],
            '/textarea',
            '/div'
        ];
        $this->assertHtml($expected, $result);

        $result = $this->Form->input('stuff', ['type' => 'textarea']);
        $expected = [
            'div' => ['class' => 'form-group'],
            'label' => ['for' => 'stuff', 'class' => 'col-form-label'],
            'Stuff',
            '/label',
            'textarea' => ['name' => 'stuff', 'maxlength' => 10, 'id' => 'stuff', 'rows' => 5, 'class' => 'form-control'],
            '/textarea',
            '/div'
        ];
        $this->assertHtml($expected, $result);
    }

    /**
     * testHiddenField method
     *
     * Test generation of a hidden input.
     *
     * @return void
     */
    public function testHiddenField() {
        $this->article['errors'] = [
            'field' => true
        ];
        $this->Form->request->data['field'] = 'test';
        $this->Form->create($this->article);
        $result = $this->Form->hidden('field', ['id' => 'theID']);
        $expected = [
            'input' => ['type' => 'hidden', 'class' => 'form-control-danger', 'name' => 'field', 'id' => 'theID', 'value' => 'test']];
        $this->assertHtml($expected, $result);

        $result = $this->Form->hidden('field', ['value' => 'my value']);
        $expected = [
            'input' => ['type' => 'hidden', 'class' => 'form-control-danger', 'name' => 'field', 'value' => 'my value']
        ];
        $this->assertHtml($expected, $result);
    }

    /**
     * testButton method
     *
     * Test generation of a form button.
     *
     * @return void
     */
    public function testButton() {
        $result = $this->Form->button('Hi');
        $expected = ['button' => ['type' => 'submit', 'class' => 'btn btn-primary'], 'Hi', '/button'];
        $this->assertHtml($expected, $result);

        $result = $this->Form->button('Clear Form >', ['type' => 'reset']);
        $expected = ['button' => ['type' => 'reset', 'class' => 'btn btn-primary'], 'Clear Form >', '/button'];
        $this->assertHtml($expected, $result);

        $result = $this->Form->button('Clear Form >', ['type' => 'reset', 'id' => 'clearForm']);
        $expected = ['button' => ['type' => 'reset', 'id' => 'clearForm', 'class' => 'btn btn-primary'], 'Clear Form >', '/button'];
        $this->assertHtml($expected, $result);

        $result = $this->Form->button('<Clear Form>', ['type' => 'reset', 'escape' => true]);
        $expected = ['button' => ['type' => 'reset', 'class' => 'btn btn-primary'], '&lt;Clear Form&gt;', '/button'];
        $this->assertHtml($expected, $result);

        $result = $this->Form->button('No type', ['type' => false]);
        $expected = ['button' => ['class' => 'btn btn-primary'], 'No type', '/button'];
        $this->assertHtml($expected, $result);

        $result = $this->Form->button('Upload Text', [
            'onClick' => "$('#postAddForm').ajaxSubmit({target: '#postTextUpload', url: '/posts/text'});return false;'",
            'escape' => false
        ]);
        $this->assertNotRegExp('/\&039/', $result);
    }

    /**
     * testPostButton method
     *
     * @return void
     */
    public function testPostButton() {
        $result = $this->Form->postButton('Hi', '/controller/action');
        $expected = [
            'form' => ['method' => 'post', 'action' => '/controller/action', 'accept-charset' => 'utf-8'],
            'div' => ['style' => 'display:none;'],
            'input' => ['type' => 'hidden', 'name' => '_method', 'value' => 'POST'],
            '/div',
            'button' => ['type' => 'submit', 'class' => 'btn btn-primary'],
            'Hi',
            '/button',
            '/form'
        ];
        $this->assertHtml($expected, $result);

        $result = $this->Form->postButton('Send', '/', ['data' => ['extra' => 'value']]);
        $this->assertTrue(strpos($result, '<input type="hidden" name="extra" value="value"') !== false);
    }

    /**
     * testPostButtonMethodType method
     *
     * @return void
     */
    public function testPostButtonMethodType() {
        $result = $this->Form->postButton('Hi', '/controller/action', ['method' => 'patch']);
        $expected = [
            'form' => ['method' => 'post', 'action' => '/controller/action', 'accept-charset' => 'utf-8'],
            'div' => ['style' => 'display:none;'],
            'input' => ['type' => 'hidden', 'name' => '_method', 'value' => 'PATCH'],
            '/div',
            'button' => ['type' => 'submit', 'class' => 'btn btn-primary'],
            'Hi',
            '/button',
            '/form'
        ];
        $this->assertHtml($expected, $result);
    }

    /**
     * testPostButtonFormOptions method
     *
     * @return void
     */
    public function testPostButtonFormOptions() {
        $result = $this->Form->postButton('Hi', '/controller/action', ['form' => ['class' => 'inline']]);
        $expected = [
            'form' => ['method' => 'post', 'action' => '/controller/action', 'accept-charset' => 'utf-8', 'class' => 'inline'],
            'div' => ['style' => 'display:none;'],
            'input' => ['type' => 'hidden', 'name' => '_method', 'value' => 'POST'],
            '/div',
            'button' => ['type' => 'submit', 'class' => 'btn btn-primary'],
            'Hi',
            '/button',
            '/form'
        ];
        $this->assertHtml($expected, $result);
    }

    /**
     * testSecurePostButton method
     *
     * Test that postButton adds _Token fields.
     *
     * @return void
     */
    public function testSecurePostButton() {
        $this->Form->request->params['_csrfToken'] = 'testkey';
        $this->Form->request->params['_Token'] = ['unlockedFields' => []];

        $result = $this->Form->postButton('Delete', '/posts/delete/1');
        $tokenDebug = urlencode(json_encode([
            '/posts/delete/1',
            [],
            []
        ]));

        $expected = [
            'form' => [
                'method' => 'post', 'action' => '/posts/delete/1', 'accept-charset' => 'utf-8',
            ],
            ['div' => ['style' => 'display:none;']],
            ['input' => ['type' => 'hidden', 'name' => '_method', 'value' => 'POST']],
            ['input' => ['type' => 'hidden', 'name' => '_csrfToken', 'value' => 'testkey']],
            '/div',
            'button' => ['type' => 'submit', 'class' => 'btn btn-primary'],
            'Delete',
            '/button',
            ['div' => ['style' => 'display:none;']],
            ['input' => ['type' => 'hidden', 'name' => '_Token[fields]', 'value' => 'preg:/[\w\d%]+/']],
            ['input' => ['type' => 'hidden', 'name' => '_Token[unlocked]', 'value' => '']],
            ['input' => [
                'type' => 'hidden', 'name' => '_Token[debug]',
                'value' => $tokenDebug,
            ]],
            '/div',
            '/form',
        ];
        $this->assertHtml($expected, $result);
    }

    /**
     * testSubmitButton method
     *
     * @return void
     */
    public function testSubmitButton() {
        $result = $this->Form->submit('');
        $expected = [
            'input' => ['type' => 'submit', 'value' => '', 'class' => 'btn btn-primary'],
        ];
        $this->assertHtml($expected, $result);

        $result = $this->Form->submit('Test Submit');
        $expected = [
            'input' => ['type' => 'submit', 'value' => 'Test Submit', 'class' => 'btn btn-primary'],
        ];
        $this->assertHtml($expected, $result);

        $result = $this->Form->submit('Next >');
        $expected = [
            'input' => ['type' => 'submit', 'value' => 'Next &gt;', 'class' => 'btn btn-primary'],
        ];
        $this->assertHtml($expected, $result);

        $result = $this->Form->submit('Next >', ['escape' => false]);
        $expected = [
            'input' => ['type' => 'submit', 'value' => 'Next >', 'class' => 'btn btn-primary'],
        ];
        $this->assertHtml($expected, $result);

        $result = $this->Form->submit('Reset!', ['type' => 'reset']);
        $expected = [
            'input' => ['type' => 'reset', 'value' => 'Reset!', 'class' => 'btn btn-primary'],
        ];
        $this->assertHtml($expected, $result);
    }

    /**
     * testSubmitImage method
     *
     * Test image submit types.
     *
     * @return void
     */
    public function testSubmitImage() {
        $result = $this->Form->submit('http://example.com/cake.power.gif');
        $expected = [
            'input' => ['type' => 'image', 'src' => 'http://example.com/cake.power.gif'],
        ];
        $this->assertHtml($expected, $result);

        $result = $this->Form->submit('/relative/cake.power.gif');
        $expected = [
            'input' => ['type' => 'image', 'src' => 'relative/cake.power.gif'],
        ];
        $this->assertHtml($expected, $result);

        $result = $this->Form->submit('cake.power.gif');
        $expected = [
            'input' => ['type' => 'image', 'src' => 'img/cake.power.gif'],
        ];
        $this->assertHtml($expected, $result);

        $result = $this->Form->submit('Not.an.image');
        $expected = [
            'input' => ['type' => 'submit', 'value' => 'Not.an.image', 'class' => 'btn btn-primary'],
        ];
        $this->assertHtml($expected, $result);
    }

    /**
     * testSubmitImageTimestamp method
     *
     * Test submit image with timestamps.
     *
     * @return void
     */
    public function testSubmitImageTimestamp() {
        Configure::write('Asset.timestamp', 'force');

        $result = $this->Form->submit('cake.power.gif');
        $expected = [
            'input' => ['type' => 'image', 'src' => 'preg:/img\/cake\.power\.gif\?\d*/'],
        ];
        $this->assertHtml($expected, $result);
    }

    /**
     * testForMagicInputNonExistingNorValidated method
     *
     * @return void
     */
    public function testForMagicInputNonExistingNorValidated() {
        $this->Form->create($this->article);
        $this->Form->templates(['inputContainer' => '{{content}}']);
        $result = $this->Form->input('non_existing_nor_validated');
        $expected = [
            'label' => ['for' => 'non-existing-nor-validated', 'class' => 'col-form-label'],
            'Non Existing Nor Validated',
            '/label',
            'input' => [
                'type' => 'text', 'name' => 'non_existing_nor_validated',
                'id' => 'non-existing-nor-validated', 'class' => 'form-control'
            ]
        ];
        $this->assertHtml($expected, $result);

        $result = $this->Form->input('non_existing_nor_validated', [
            'val' => 'my value'
        ]);
        $expected = [
            'label' => ['for' => 'non-existing-nor-validated', 'class' => 'col-form-label'],
            'Non Existing Nor Validated',
            '/label',
            'input' => [
                'type' => 'text', 'name' => 'non_existing_nor_validated',
                'value' => 'my value', 'id' => 'non-existing-nor-validated',
                'class' => 'form-control'
            ]
        ];
        $this->assertHtml($expected, $result);

        $this->Form->request->data = ['non_existing_nor_validated' => 'CakePHP magic'];
        $result = $this->Form->input('non_existing_nor_validated');
        $expected = [
            'label' => ['for' => 'non-existing-nor-validated', 'class' => 'col-form-label'],
            'Non Existing Nor Validated',
            '/label',
            'input' => [
                'type' => 'text', 'name' => 'non_existing_nor_validated',
                'value' => 'CakePHP magic', 'id' => 'non-existing-nor-validated',
                'class' => 'form-control'
            ]
        ];
        $this->assertHtml($expected, $result);
    }

    /**
     * testFormMagicInputLabel method
     *
     * @return void
     */
    public function testFormMagicInputLabel() {
        TableRegistry::get('Contacts', [
            'className' => 'Cake\Test\TestCase\View\Helper\ContactsTable'
        ]);
        $this->Form->create([], ['context' => ['table' => 'Contacts']]);
        $this->Form->templates(['inputContainer' => '{{content}}']);

        $result = $this->Form->input('Contacts.name', ['label' => 'My label']);
        $expected = [
            'label' => ['for' => 'contacts-name', 'class' => 'col-form-label'],
            'My label',
            '/label',
            'input' => [
                'type' => 'text', 'name' => 'Contacts[name]',
                'id' => 'contacts-name', 'maxlength' => '255',
                'class' => 'form-control'
            ]
        ];
        $this->assertHtml($expected, $result);

        $result = $this->Form->input('name', [
            'label' => ['class' => 'mandatory']
        ]);
        $expected = [
            'label' => ['for' => 'name', 'class' => 'mandatory col-form-label'],
            'Name',
            '/label',
            'input' => [
                'type' => 'text', 'name' => 'name',
                'id' => 'name', 'maxlength' => '255',
                'class' => 'form-control'
            ]
        ];
        $this->assertHtml($expected, $result);

        $result = $this->Form->input('name', [
            'div' => false,
            'label' => ['class' => 'mandatory', 'text' => 'My label']
        ]);
        $expected = [
            'label' => ['for' => 'name', 'class' => 'mandatory col-form-label'],
            'My label',
            '/label',
            'input' => [
                'type' => 'text', 'name' => 'name',
                'id' => 'name', 'maxlength' => '255',
                'class' => 'form-control'
            ]
        ];
        $this->assertHtml($expected, $result);

        $result = $this->Form->input('Contact.name', [
            'div' => false, 'id' => 'my_id', 'label' => ['for' => 'my_id']
        ]);
        $expected = [
            'label' => ['for' => 'my_id', 'class' => 'col-form-label'],
            'Name',
            '/label',
            'input' => [
                'type' => 'text', 'name' => 'Contact[name]',
                'id' => 'my_id', 'maxlength' => '255',
                'class' => 'form-control'
            ]
        ];
        $this->assertHtml($expected, $result);

        $result = $this->Form->input('1.id');
        $expected = ['input' => [
            'type' => 'hidden', 'name' => '1[id]',
            'id' => '1-id'
        ]];
        $this->assertHtml($expected, $result);

        $result = $this->Form->input("1.name");
        $expected = [
            'label' => ['for' => '1-name', 'class' => 'col-form-label'],
            'Name',
            '/label',
            'input' => [
                'type' => 'text', 'name' => '1[name]',
                'id' => '1-name', 'maxlength' => '255',
                'class' => 'form-control'
            ]
        ];
        $this->assertHtml($expected, $result);
    }

    /**
     * testMultiRecordForm method
     *
     * Test the generation of fields for a multi record form.
     *
     * @return void
     */
    public function testMultiRecordForm() {
        $this->loadFixtures('Articles', 'Comments');
        $articles = TableRegistry::get('Articles');
        $articles->hasMany('Comments');

        $comment = new Entity(['comment' => 'Value']);
        $article = new Article(['comments' => [$comment]]);
        $this->Form->create([$article]);
        $result = $this->Form->input('0.comments.1.comment');
        //@codingStandardsIgnoreStart
        $expected = [
            'div' => ['class' => 'form-group'],
            'label' => ['for' => '0-comments-1-comment', 'class' => 'col-form-label'],
            'Comment',
            '/label',
            'textarea' => [
                'name',
                'id' => '0-comments-1-comment',
                'rows' => 5,
                'class' => 'form-control'
            ],
            '/textarea',
            '/div'
        ];
        //@codingStandardsIgnoreEnd
        $this->assertHtml($expected, $result);

        $result = $this->Form->input('0.comments.0.comment');
        //@codingStandardsIgnoreStart
        $expected = [
            'div' => ['class' => 'form-group'],
            'label' => ['for' => '0-comments-0-comment', 'class' => 'col-form-label'],
            'Comment',
            '/label',
            'textarea' => [
                'name',
                'id' => '0-comments-0-comment',
                'rows' => 5, 'class' => 'form-control'
            ],
            'Value',
            '/textarea',
            '/div'
        ];
        //@codingStandardsIgnoreEnd
        $this->assertHtml($expected, $result);

        $comment->errors('comment', ['Not valid']);
        $result = $this->Form->input('0.comments.0.comment');
        //@codingStandardsIgnoreStart
        $expected = [
            'div' => ['class' => 'form-group has-danger'],
            'label' => ['for' => '0-comments-0-comment', 'class' => 'col-form-label'],
            'Comment',
            '/label',
            'textarea' => [
                'name',
                'class' => 'form-control-danger form-control',
                'id' => '0-comments-0-comment',
                'rows' => 5
            ],
            'Value',
            '/textarea',
            ['div' => ['class' => 'form-control-feedback']],
            'Not valid',
            '/div',
            '/div'
        ];

        //@codingStandardsIgnoreEnd
        $this->assertHtml($expected, $result);

        TableRegistry::get('Comments')
            ->validator('default')
            ->allowEmpty('comment', false);
        $result = $this->Form->input('0.comments.1.comment');
        //@codingStandardsIgnoreStart
        $expected = [
            'div' => ['class' => 'form-group'],
            'label' => ['for' => '0-comments-1-comment', 'class' => 'col-form-label'],
            'Comment',
            '/label',
            'textarea' => [
                'name',
                'required' => 'required',
                'id' => '0-comments-1-comment',
                'rows' => 5, 'class' => 'form-control'
            ],
            '/textarea',
            '/div'
        ];
        //@codingStandardsIgnoreEnd
        $this->assertHtml($expected, $result);
    }

    /**
     * testHtml5Inputs method
     *
     * Test that some html5 inputs + FormHelper::__call() work.
     *
     * @return void
     */
    public function testHtml5Inputs() {
        $result = $this->Form->email('User.email');
        $expected = [
            'input' => ['type' => 'email', 'name' => 'User[email]', 'class' => 'form-control']
        ];
        $this->assertHtml($expected, $result);

        $result = $this->Form->search('User.query');
        $expected = [
            'input' => ['type' => 'search', 'name' => 'User[query]', 'class' => 'form-control']
        ];
        $this->assertHtml($expected, $result);

        $result = $this->Form->search('User.query', ['value' => 'test']);
        $expected = [
            'input' => ['type' => 'search', 'name' => 'User[query]', 'value' => 'test', 'class' => 'form-control']
        ];
        $this->assertHtml($expected, $result);

        $result = $this->Form->search('User.query', ['type' => 'text', 'value' => 'test']);
        $expected = [
            'input' => ['type' => 'text', 'name' => 'User[query]', 'value' => 'test', 'class' => 'form-control']
        ];
        $this->assertHtml($expected, $result);
    }

    /**
     * testHtml5InputWithInput method
     *
     * Test accessing html5 inputs through input().
     *
     * @return void
     */
    public function testHtml5InputWithInput() {
        $this->Form->create();
        $this->Form->templates(['inputContainer' => '{{content}}']);
        $result = $this->Form->input('website', [
            'type' => 'url',
            'val' => 'http://domain.tld',
            'label' => false
        ]);
        $expected = [
            'input' => ['type' => 'url', 'name' => 'website', 'id' => 'website', 'value' => 'http://domain.tld', 'class' => 'form-control']
        ];
        $this->assertHtml($expected, $result);
    }

    /**
     * testRequiredAttribute method
     *
     * Tests that formhelper sets required attributes.
     *
     * @return void
     */
    public function testRequiredAttribute() {
        $this->article['required'] = [
            'title' => true,
            'body' => false,
        ];
        $this->Form->create($this->article);

        $result = $this->Form->input('title');
        $expected = [
            'div' => ['class' => 'form-group'],
            'label' => ['for' => 'title', 'class' => 'col-form-label'],
            'Title',
            '/label',
            'input' => [
                'type' => 'text',
                'name' => 'title',
                'id' => 'title',
                'required' => 'required',
                'class' => 'form-control'
            ],
            '/div'
        ];
        $this->assertHtml($expected, $result);

        $result = $this->Form->input('title', ['required' => false]);
        $this->assertNotContains('required', $result);

        $result = $this->Form->input('body');
        $expected = [
            'div' => ['class' => 'form-group'],
            'label' => ['for' => 'body', 'class' => 'col-form-label'],
            'Body',
            '/label',
            'input' => [
                'type' => 'text',
                'name' => 'body',
                'id' => 'body',
                'class' => 'form-control'
            ],
            '/div'
        ];
        $this->assertHtml($expected, $result);

        $result = $this->Form->input('body', ['required' => true]);
        $this->assertContains('required', $result);
    }

    /**
     * testInputsNotNested method
     *
     * Tests that it is possible to put inputs outside of the label.
     *
     * @return void
     */
    public function testInputsNotNested() {
        $this->Form->templates([
            'nestingLabel' => '{{hidden}}{{input}}<label{{attrs}}>{{text}}</label>',
            'formGroup' => '{{input}}{{label}}',
        ]);
        $result = $this->Form->input('foo', ['type' => 'checkbox', 'skipSwitchTemplates' => true]);
        $expected = [
            'div' => ['class' => 'form-group'],
            ['input' => ['type' => 'hidden', 'name' => 'foo', 'value' => '0']],
            ['input' => [
                'type' => 'checkbox',
                'name' => 'foo',
                'id' => 'foo',
                'value' => '1',
                'class' => 'form-check-input'
            ]],
            'label' => ['for' => 'foo', 'class' => 'form-check-label'],
            'Foo',
            '/label',
            '/div'
        ];

        $this->assertHtml($expected, $result);

        $result = $this->Form->input('foo', ['type' => 'checkbox', 'label' => false, 'skipSwitchTemplates' => true]);
        $expected = [
            'div' => ['class' => 'form-group'],
            ['input' => ['type' => 'hidden', 'name' => 'foo', 'value' => '0']],
            ['input' => [
                'type' => 'checkbox',
                'name' => 'foo',
                'id' => 'foo',
                'value' => '1',
                'class' => 'form-check-input'
            ]],
            '/div'
        ];
        $this->assertHtml($expected, $result);

        $result = $this->Form->input('confirm', [
            'type' => 'radio',
            'options' => ['Y' => 'Yes', 'N' => 'No'],
            'skipSwitchTemplates' => true
        ]);
        $expected = [
            'div' => ['class' => 'form-group'],
            ['input' => ['type' => 'hidden', 'name' => 'confirm', 'value' => '']],
            ['input' => ['type' => 'radio', 'name' => 'confirm', 'id' => 'confirm-y', 'value' => 'Y', 'class' => 'form-check-input']],
            ['label' => ['for' => 'confirm-y', 'class' => 'form-check-label']],
            'Yes',
            '/label',
            ['input' => ['type' => 'radio', 'name' => 'confirm', 'id' => 'confirm-n', 'value' => 'N', 'class' => 'form-check-input']],
            ['label' => ['for' => 'confirm-n', 'class' => 'form-check-label']],
            'No',
            '/label',
            '<label',
            'Confirm',
            '/label',
            '/div',
        ];
        $this->assertHtml($expected, $result);

        $result = $this->Form->select('category', ['1', '2'], [
            'multiple' => 'checkbox',
            'name' => 'fish',
            'skipSwitchTemplates' => true
        ]);
        $expected = [
            'input' => ['type' => 'hidden', 'name' => 'fish', 'value' => ''],
            ['div' => ['class' => 'checkbox']],
            ['input' => ['type' => 'checkbox', 'name' => 'fish[]', 'value' => '0', 'id' => 'fish-0', 'class' => 'form-check-input']],
            ['label' => ['for' => 'fish-0', 'class' => 'form-check-label']],
            '1',
            '/label',
            '/div',
            ['div' => ['class' => 'checkbox']],
            ['input' => ['type' => 'checkbox', 'name' => 'fish[]', 'value' => '1', 'id' => 'fish-1', 'class' => 'form-check-input']],
            ['label' => ['for' => 'fish-1', 'class' => 'form-check-label']],
            '2',
            '/label',
            '/div'
        ];
        $this->assertHtml($expected, $result);
    }

    /**
     * testInputContainerTemplates method
     *
     * Test that *Container templates are used by input.
     *
     * @return void
     */
    public function testInputContainerTemplates() {
        $this->Form->templates([
            'checkboxContainer' => '<div class="check">{{content}}</div>',
            'radioContainer' => '<div class="rad">{{content}}</div>',
            'radioContainerError' => '<div class="rad err">{{content}}</div>',
            'datetimeContainer' => '<div class="dt">{{content}}</div>',
        ]);

        $this->article['errors'] = [
            'Article' => ['published' => 'error message']
        ];
        $this->Form->create($this->article);

        $result = $this->Form->input('accept', [
            'type' => 'checkbox', 'skipSwitchTemplates' => true
        ]);
        $expected = [
            'div' => ['class' => 'check'],
            ['input' => ['type' => 'hidden', 'name' => 'accept', 'value' => 0]],
            'label' => ['for' => 'accept', 'class' => 'form-check-label'],
            ['input' => ['id' => 'accept', 'type' => 'checkbox', 'name' => 'accept', 'value' => 1, 'class' => 'form-check-input']],
            'Accept',
            '/label',
            '/div'
        ];
        $this->assertHtml($expected, $result);

        $result = $this->Form->input('accept', [
            'type' => 'radio',
            'options' => ['Y', 'N']
        ]);
        $this->assertContains('<div class="rad">', $result);

        $result = $this->Form->input('Article.published', [
            'type' => 'radio',
            'options' => ['Y', 'N']
        ]);
        $this->assertContains('<div class="rad err">', $result);

        $result = $this->Form->input('Article.created', [
            'type' => 'datetime'
        ]);
        $this->assertContains('<div class="dt">', $result);
    }

    /**
     * testAutoDomId method
     *
     * @return void
     */
    public function testAutoDomId() {
        $result = $this->Form->text('field', ['id' => true]);
        $expected = [
            'input' => ['type' => 'text', 'name' => 'field', 'id' => 'field', 'class' => 'form-control'],
        ];
        $this->assertHtml($expected, $result);

        // Ensure id => doesn't cause problem when multiple inputs are generated.
        $result = $this->Form->radio('field', ['option A', 'option B'], ['id' => true]);
        $expected = [
            'input' => ['type' => 'hidden', 'name' => 'field', 'value' => ''],
            ['div' => ['class' => 'form-check']],
            ['label' => ['for' => 'field-0', 'class' => 'form-check-label']],
            ['input' => ['type' => 'radio', 'name' => 'field', 'value' => '0', 'id' => 'field-0', 'class' => 'form-check-input']],
            'option A',
            '/label',
            '/div',
            ['div' => ['class' => 'form-check']],
            ['label' => ['for' => 'field-1', 'class' => 'form-check-label']],
            ['input' => ['type' => 'radio', 'name' => 'field', 'value' => '1', 'id' => 'field-1', 'class' => 'form-check-input']],
            'option B',
            '/label',
            '/div'
        ];
        $this->assertHtml($expected, $result);

        $result = $this->Form->select(
            'multi_field',
            ['first', 'second'],
            ['multiple' => 'checkbox', 'id' => true]
        );
        $expected = [
            'input' => [
                'type' => 'hidden', 'name' => 'multi_field', 'value' => ''
            ],
            ['div' => ['class' => 'form-check']],
            ['label' => ['for' => 'multi-field-0', 'class' => 'form-check-label']],
            ['input' => [
                'type' => 'checkbox', 'name' => 'multi_field[]',
                'value' => '0', 'id' => 'multi-field-0',
                'class' => 'form-check-input'
            ]],
            'first',
            '/label',
            '/div',
            ['div' => ['class' => 'form-check']],
            ['label' => ['for' => 'multi-field-1', 'class' => 'form-check-label']],
            ['input' => [
                'type' => 'checkbox', 'name' => 'multi_field[]',
                'value' => '1', 'id' => 'multi-field-1',
                'class' => 'form-check-input'
            ]],
            'second',
            '/label',
            '/div',
        ];
        $this->assertHtml($expected, $result);
    }

    /**
     * testNestedLabelInput method
     *
     * Test the `nestedInput` parameter
     *
     * @return void
     */
    public function testNestedLabelInput() {
        $result = $this->Form->input('foo', ['nestedInput' => true]);
        $expected = [
            'div' => ['class' => 'form-group'],
            'label' => ['for' => 'foo', 'class' => 'col-form-label'],
            ['input' => [
                'type' => 'text',
                'name' => 'foo',
                'id' => 'foo',
                'class' => 'form-control'
            ]],
            'Foo',
            '/label',
            '/div'
        ];
        $this->assertHtml($expected, $result);
    }

    /**
     * Tests to make sure `labelOptions` is rendered correctly by MultiCheckboxWidget and RadioWidget
     *
     * This test makes sure `false` excludes the label from the render
     *
     */
    public function testInputLabelManipulationDisableLabels() {
        $result = $this->Form->input('test', [
            'type' => 'radio',
            'options' => ['A', 'B'],
            'labelOptions' => false
        ]);
        $expected = [
            ['div' => ['class' => 'form-group']],
            '<label',
            'Test',
            '/label',
            ['input' => ['type' => 'hidden', 'name' => 'test', 'value' => '']],
            ['div' => ['class' => 'form-check']],
            ['input' => ['type' => 'radio', 'name' => 'test', 'value' => '0', 'id' => 'test-0', 'class' => 'form-check-input']],
            '/div',
            ['div' => ['class' => 'form-check']],
            ['input' => ['type' => 'radio', 'name' => 'test', 'value' => '1', 'id' => 'test-1', 'class' => 'form-check-input']],
            '/div',
            '/div'
        ];

        $this->assertHtml($expected, $result);

        $result = $this->Form->input('checkbox1', [
            'label' => 'My checkboxes',
            'multiple' => 'checkbox',
            'type' => 'select',
            'options' => [
                ['text' => 'First Checkbox', 'value' => 1],
                ['text' => 'Second Checkbox', 'value' => 2]
            ],
            'labelOptions' => false
        ]);
        $expected = [
            ['div' => ['class' => 'form-group']],
            ['label' => ['for' => 'checkbox1']],
            'My checkboxes',
            '/label',
            'input' => ['type' => 'hidden', 'name' => 'checkbox1', 'value' => ''],
            ['div' => ['class' => 'form-check']],
            ['input' => ['type' => 'checkbox', 'name' => 'checkbox1[]', 'value' => '1', 'id' => 'checkbox1-1', 'class' => 'form-check-input']],
            '/div',
            ['div' => ['class' => 'form-check']],
            ['input' => ['type' => 'checkbox', 'name' => 'checkbox1[]', 'value' => '2', 'id' => 'checkbox1-2', 'class' => 'form-check-input']],
            '/div',
            '/div'
        ];
        $this->assertHtml($expected, $result);
    }

    /**
     * Tests to make sure `labelOptions` is rendered correctly by RadioWidget
     *
     * This test checks rendering of class (as string and array) also makes sure 'selected' is
     * added to the class if checked.
     *
     * Also checks to make sure any custom attributes are rendered correctly
     */
    public function testInputLabelManipulationRadios() {
        $result = $this->Form->input('test', [
            'type' => 'radio',
            'options' => ['A', 'B'],
            'labelOptions' => ['class' => 'custom-class']
        ]);
        $expected = [
            ['div' => ['class' => 'form-group']],
            '<label',
            'Test',
            '/label',
            ['input' => ['type' => 'hidden', 'name' => 'test', 'value' => '']],
            ['div' => ['class' => 'form-check']],
            ['label' => ['for' => 'test-0', 'class' => 'custom-class form-check-label']],
            ['input' => ['type' => 'radio', 'name' => 'test', 'value' => '0', 'id' => 'test-0', 'class' => 'form-check-input']],
            'A',
            '/label',
            '/div',
            ['div' => ['class' => 'form-check']],
            ['label' => ['for' => 'test-1', 'class' => 'custom-class form-check-label']],
            ['input' => ['type' => 'radio', 'name' => 'test', 'value' => '1', 'id' => 'test-1', 'class' => 'form-check-input']],
            'B',
            '/label',
            '/div',
            '/div',
        ];
        $this->assertHtml($expected, $result);

        $result = $this->Form->input('test', [
            'type' => 'radio',
            'options' => ['A', 'B'],
            'value' => 1,
            'labelOptions' => ['class' => 'custom-class']
        ]);
        $expected = [
            ['div' => ['class' => 'form-group']],
            '<label',
            'Test',
            '/label',
            ['input' => ['type' => 'hidden', 'name' => 'test', 'value' => '']],
            ['div' => ['class' => 'form-check']],
            ['label' => ['for' => 'test-0', 'class' => 'custom-class form-check-label']],
            ['input' => ['type' => 'radio', 'name' => 'test', 'value' => '0', 'id' => 'test-0', 'class' => 'form-check-input']],
            'A',
            '/label',
            '/div',
            ['div' => ['class' => 'form-check']],
            ['label' => ['for' => 'test-1', 'class' => 'custom-class form-check-label selected']],
            ['input' => ['type' => 'radio', 'name' => 'test', 'value' => '1', 'id' => 'test-1', 'checked' => 'checked', 'class' => 'form-check-input']],
            'B',
            '/label',
            '/div',
            '/div'
        ];
        $this->assertHtml($expected, $result);

        $result = $this->Form->input('test', [
            'type' => 'radio',
            'options' => ['A', 'B'],
            'value' => 1,
            'labelOptions' => ['class' => ['custom-class', 'custom-class-array']]
        ]);
        $expected = [
            ['div' => ['class' => 'form-group']],
            '<label',
            'Test',
            '/label',
            ['input' => ['type' => 'hidden', 'name' => 'test', 'value' => '']],
            ['div' => ['class' => 'form-check']],
            ['label' => ['for' => 'test-0', 'class' => 'custom-class custom-class-array form-check-label']],
            ['input' => ['type' => 'radio', 'name' => 'test', 'value' => '0', 'id' => 'test-0', 'class' => 'form-check-input']],
            'A',
            '/label',
            '/div',
            ['div' => ['class' => 'form-check']],
            ['label' => ['for' => 'test-1', 'class' => 'custom-class custom-class-array form-check-label selected']],
            ['input' => ['type' => 'radio', 'name' => 'test', 'value' => '1', 'id' => 'test-1', 'checked' => 'checked', 'class' => 'form-check-input']],
            'B',
            '/label',
            '/div',
            '/div'
        ];
        $this->assertHtml($expected, $result);

        $result = $this->Form->radio('test', ['A', 'B'], [
            'label' => [
                'class' => ['custom-class', 'another-class'],
                'data-name' => 'bob'
            ],
            'value' => 1
        ]);
        $expected = [
            ['input' => ['type' => 'hidden', 'name' => 'test', 'value' => '']],
            ['div' => ['class' => 'form-check']],
            ['label' => ['for' => 'test-0', 'class' => 'custom-class another-class form-check-label', 'data-name' => 'bob']],
            ['input' => ['type' => 'radio', 'name' => 'test', 'value' => '0', 'id' => 'test-0', 'class' => 'form-check-input']],
            'A',
            '/label',
            '/div',
            ['div' => ['class' => 'form-check']],
            ['label' => ['for' => 'test-1', 'class' => 'custom-class another-class form-check-label selected', 'data-name' => 'bob']],
            ['input' => ['type' => 'radio', 'name' => 'test', 'value' => '1', 'id' => 'test-1', 'checked' => 'checked', 'class' => 'form-check-input']],
            'B',
            '/label',
            '/div',
        ];
        $this->assertHtml($expected, $result, true);
    }

    /**
     * Tests to make sure `labelOptions` is rendered correctly by MultiCheckboxWidget
     *
     * This test checks rendering of class (as string and array) also makes sure 'selected' is
     * added to the class if checked.
     *
     * Also checks to make sure any custom attributes are rendered correctly
     */
    public function testInputLabelManipulationCheckboxes() {
        $result = $this->Form->input('checkbox1', [
            'label' => 'My checkboxes',
            'multiple' => 'checkbox',
            'type' => 'select',
            'options' => [
                ['text' => 'First Checkbox', 'value' => 1],
                ['text' => 'Second Checkbox', 'value' => 2]
            ],
            'labelOptions' => ['class' => 'custom-class'],
            'value' => ['1']
        ]);
        $expected = [
            ['div' => ['class' => 'form-group']],
            ['label' => ['for' => 'checkbox1']],
            'My checkboxes',
            '/label',
            'input' => ['type' => 'hidden', 'name' => 'checkbox1', 'value' => ''],
            ['div' => ['class' => 'form-check']],
            ['label' => [
                'class' => 'custom-class form-check-label selected',
                'for' => 'checkbox1-1'
            ]],
            ['input' => [
                'type' => 'checkbox',
                'name' => 'checkbox1[]',
                'value' => '1',
                'id' => 'checkbox1-1',
                'checked' => 'checked',
                'class' => 'form-check-input'

            ]],
            'First Checkbox',
            '/label',
            '/div',
            ['div' => ['class' => 'form-check']],
            ['label' => [
                'class' => 'custom-class form-check-label',
                'for' => 'checkbox1-2'
            ]],
            ['input' => [
                'type' => 'checkbox',
                'name' => 'checkbox1[]',
                'value' => '2',
                'id' => 'checkbox1-2',
                'class' => 'form-check-input'
            ]],
            'Second Checkbox',
            '/label',
            '/div',
            '/div'
        ];
        $this->assertHtml($expected, $result);

        $result = $this->Form->input('checkbox1', [
            'label' => 'My checkboxes',
            'multiple' => 'checkbox',
            'type' => 'select',
            'options' => [
                ['text' => 'First Checkbox', 'value' => 1],
                ['text' => 'Second Checkbox', 'value' => 2]
            ],
            'labelOptions' => ['class' => ['custom-class', 'another-class'], 'data-name' => 'bob'],
            'value' => ['1']

        ]);
        $expected = [
            ['div' => ['class' => 'form-group']],
            ['label' => ['for' => 'checkbox1']],
            'My checkboxes',
            '/label',
            'input' => ['type' => 'hidden', 'name' => 'checkbox1', 'value' => ''],
            ['div' => ['class' => 'form-check']],
            ['label' => [
                'class' => 'custom-class another-class form-check-label selected',
                'data-name' => 'bob',
                'for' => 'checkbox1-1'
            ]],
            ['input' => [
                'type' => 'checkbox',
                'name' => 'checkbox1[]',
                'value' => '1',
                'id' => 'checkbox1-1',
                'checked' => 'checked',
                'class' => 'form-check-input'
            ]],
            'First Checkbox',
            '/label',
            '/div',
            ['div' => ['class' => 'form-check']],
            ['label' => [
                'class' => 'custom-class another-class form-check-label',
                'data-name' => 'bob',
                'for' => 'checkbox1-2'
            ]],
            ['input' => [
                'type' => 'checkbox',
                'name' => 'checkbox1[]',
                'value' => '2',
                'id' => 'checkbox1-2',
                'class' => 'form-check-input'
            ]],
            'Second Checkbox',
            '/label',
            '/div',
            '/div'
        ];
        $this->assertHtml($expected, $result);
    }

    /**
     * Tests correct generation of file upload fields for binary fields
     *
     * @return void
     */
    public function testFileUploadFieldTypeGenerationForBinaries() {
        $table = TableRegistry::get('Contacts', [
            'className' => 'Cake\Test\TestCase\View\Helper\ContactsTable'
        ]);
        $table->schema(['foo' => [
            'type' => 'binary',
            'null' => false,
            'default' => null,
            'length' => 1024
        ]]);
        $this->Form->create([], ['context' => ['table' => 'Contacts']]);

        $result = $this->Form->input('foo');
        $expected = [
            'div' => ['class' => 'form-group'],
            'label' => ['for' => 'foo'],
            'Foo',
            '/label',
            ['input' => [
                'type' => 'file', 'name' => 'foo',
                'id' => 'foo',
                'class' => 'form-control-file'
            ]],
            '/div'
        ];
        $this->assertHtml($expected, $result);
    }

    /**
     * testFileUploadField method
     *
     * Test generation of a file upload input.
     *
     * @return void
     */
    public function testFileUploadField() {
        $expected = ['input' => ['type' => 'file', 'name' => 'Model[upload]', 'class' => 'form-control-file']];

        $result = $this->Form->file('Model.upload');
        $this->assertHtml($expected, $result);

        $this->Form->request->data['Model']['upload'] = [
            'name' => '', 'type' => '', 'tmp_name' => '',
            'error' => 4, 'size' => 0
        ];
        $result = $this->Form->file('Model.upload');
        $this->assertHtml($expected, $result);

        $this->Form->request->data['Model']['upload'] = 'no data should be set in value';
        $result = $this->Form->file('Model.upload');
        $this->assertHtml($expected, $result);
    }

    /**
     * testFileUploadOnOtherModel method
     *
     * Test File upload input on a model not used in create().
     *
     * @return void
     */
    public function testFileUploadOnOtherModel() {
        $this->Form->create($this->article, ['type' => 'file']);
        $result = $this->Form->file('ValidateProfile.city');
        $expected = [
            'input' => ['type' => 'file', 'name' => 'ValidateProfile[city]', 'class' => 'form-control-file']
        ];
        $this->assertHtml($expected, $result);
    }

    /**
     * testMonth method
     *
     * Test generation of a month input.
     *
     * @return void
     */
    public function testMonth() {
        $result = $this->Form->month('Model.field', ['value' => '']);
        $expected = [
            ['select' => ['name' => 'Model[field][month]', 'class' => 'form-control']],
            ['option' => ['value' => '', 'selected' => 'selected']],
            '/option',
            ['option' => ['value' => '01']],
            date('F', strtotime('2008-01-01 00:00:00')),
            '/option',
            ['option' => ['value' => '02']],
            date('F', strtotime('2008-02-01 00:00:00')),
            '/option',
            '*/select',
        ];
        $this->assertHtml($expected, $result);

        $result = $this->Form->month('Model.field', ['empty' => true, 'value' => '']);
        $expected = [
            ['select' => ['name' => 'Model[field][month]', 'class' => 'form-control']],
            ['option' => ['selected' => 'selected', 'value' => '']],
            '/option',
            ['option' => ['value' => '01']],
            date('F', strtotime('2008-01-01 00:00:00')),
            '/option',
            ['option' => ['value' => '02']],
            date('F', strtotime('2008-02-01 00:00:00')),
            '/option',
            '*/select',
        ];
        $this->assertHtml($expected, $result);

        $result = $this->Form->month('Model.field', ['value' => '', 'monthNames' => false]);
        $expected = [
            ['select' => ['name' => 'Model[field][month]', 'class' => 'form-control']],
            ['option' => ['selected' => 'selected', 'value' => '']],
            '/option',
            ['option' => ['value' => '01']],
            '1',
            '/option',
            ['option' => ['value' => '02']],
            '2',
            '/option',
            '*/select',
        ];
        $this->assertHtml($expected, $result);

        $monthNames = [
            '01' => 'Jan', '02' => 'Feb', '03' => 'Mar', '04' => 'Apr', '05' => 'May', '06' => 'Jun',
            '07' => 'Jul', '08' => 'Aug', '09' => 'Sep', '10' => 'Oct', '11' => 'Nov', '12' => 'Dec'
        ];
        $result = $this->Form->month('Model.field', ['value' => '1', 'monthNames' => $monthNames]);
        $expected = [
            ['select' => ['name' => 'Model[field][month]', 'class' => 'form-control']],
            ['option' => ['value' => '']],
            '/option',
            ['option' => ['value' => '01', 'selected' => 'selected']],
            'Jan',
            '/option',
            ['option' => ['value' => '02']],
            'Feb',
            '/option',
            '*/select',
        ];
        $this->assertHtml($expected, $result);

        $this->Form->request->data['Project']['release'] = '2050-02-10';
        $result = $this->Form->month('Project.release');

        $expected = [
            ['select' => ['name' => 'Project[release][month]', 'class' => 'form-control']],
            ['option' => ['value' => '']],
            '/option',
            ['option' => ['value' => '01']],
            'January',
            '/option',
            ['option' => ['value' => '02', 'selected' => 'selected']],
            'February',
            '/option',
            '*/select',
        ];
        $this->assertHtml($expected, $result);

        $result = $this->Form->month('Contact.published', [
            'empty' => 'Published on',
        ]);
        $this->assertContains('Published on', $result);
    }

    /**
     * testDay method
     *
     * Test generation of a day input.
     *
     * @return void
     */
    public function testDay() {
        extract($this->dateRegex);

        $result = $this->Form->day('Model.field', ['value' => '', 'class' => 'form-control']);
        $expected = [
            ['select' => ['name' => 'Model[field][day]', 'class' => 'form-control']],
            ['option' => ['selected' => 'selected', 'value' => '']],
            '/option',
            ['option' => ['value' => '01']],
            '1',
            '/option',
            ['option' => ['value' => '02']],
            '2',
            '/option',
            $daysRegex,
            '/select',
        ];
        $this->assertHtml($expected, $result);

        $this->Form->request->data['Model']['field'] = '2006-10-10 23:12:32';
        $result = $this->Form->day('Model.field');
        $expected = [
            ['select' => ['name' => 'Model[field][day]', 'class' => 'form-control']],
            ['option' => ['value' => '']],
            '/option',
            ['option' => ['value' => '01']],
            '1',
            '/option',
            ['option' => ['value' => '02']],
            '2',
            '/option',
            $daysRegex,
            ['option' => ['value' => '10', 'selected' => 'selected']],
            '10',
            '/option',
            $daysRegex,
            '/select',
        ];
        $this->assertHtml($expected, $result);

        $this->Form->request->data['Model']['field'] = '';
        $result = $this->Form->day('Model.field', ['value' => '10']);
        $expected = [
            ['select' => ['name' => 'Model[field][day]', 'class' => 'form-control']],
            ['option' => ['value' => '']],
            '/option',
            ['option' => ['value' => '01']],
            '1',
            '/option',
            ['option' => ['value' => '02']],
            '2',
            '/option',
            $daysRegex,
            ['option' => ['value' => '10', 'selected' => 'selected']],
            '10',
            '/option',
            $daysRegex,
            '/select',
        ];
        $this->assertHtml($expected, $result);

        $this->Form->request->data['Project']['release'] = '2050-10-10';
        $result = $this->Form->day('Project.release');

        $expected = [
            ['select' => ['name' => 'Project[release][day]', 'class' => 'form-control']],
            ['option' => ['value' => '']],
            '/option',
            ['option' => ['value' => '01']],
            '1',
            '/option',
            ['option' => ['value' => '02']],
            '2',
            '/option',
            $daysRegex,
            ['option' => ['value' => '10', 'selected' => 'selected']],
            '10',
            '/option',
            $daysRegex,
            '/select',
        ];
        $this->assertHtml($expected, $result);

        $result = $this->Form->day('Contact.published', [
            'empty' => 'Published on',
        ]);
        $this->assertContains('Published on', $result);
    }

    /**
     * testMinute method
     *
     * Test generation of a minute input.
     *
     * @return void
     */
    public function testMinute() {
        extract($this->dateRegex);

        $result = $this->Form->minute('Model.field', ['value' => '']);
        $expected = [
            ['select' => ['name' => 'Model[field][minute]', 'class' => 'form-control']],
            ['option' => ['selected' => 'selected', 'value' => '']],
            '/option',
            ['option' => ['value' => '00']],
            '00',
            '/option',
            ['option' => ['value' => '01']],
            '01',
            '/option',
            ['option' => ['value' => '02']],
            '02',
            '/option',
            $minutesRegex,
            '/select',
        ];
        $this->assertHtml($expected, $result);

        $this->Form->request->data['Model']['field'] = '2006-10-10 00:12:32';
        $result = $this->Form->minute('Model.field');
        $expected = [
            ['select' => ['name' => 'Model[field][minute]', 'class' => 'form-control']],
            ['option' => ['value' => '']],
            '/option',
            ['option' => ['value' => '00']],
            '00',
            '/option',
            ['option' => ['value' => '01']],
            '01',
            '/option',
            ['option' => ['value' => '02']],
            '02',
            '/option',
            $minutesRegex,
            ['option' => ['value' => '12', 'selected' => 'selected']],
            '12',
            '/option',
            $minutesRegex,
            '/select',
        ];
        $this->assertHtml($expected, $result);

        $this->Form->request->data['Model']['field'] = '';
        $result = $this->Form->minute('Model.field', ['interval' => 5]);
        $expected = [
            ['select' => ['name' => 'Model[field][minute]', 'class' => 'form-control']],
            ['option' => ['selected' => 'selected', 'value' => '']],
            '/option',
            ['option' => ['value' => '00']],
            '00',
            '/option',
            ['option' => ['value' => '05']],
            '05',
            '/option',
            ['option' => ['value' => '10']],
            '10',
            '/option',
            $minutesRegex,
            '/select',
        ];
        $this->assertHtml($expected, $result);

        $this->Form->request->data['Model']['field'] = '2006-10-10 00:10:32';
        $result = $this->Form->minute('Model.field', ['interval' => 5]);
        $expected = [
            ['select' => ['name' => 'Model[field][minute]', 'class' => 'form-control']],
            ['option' => ['value' => '']],
            '/option',
            ['option' => ['value' => '00']],
            '00',
            '/option',
            ['option' => ['value' => '05']],
            '05',
            '/option',
            ['option' => ['value' => '10', 'selected' => 'selected']],
            '10',
            '/option',
            $minutesRegex,
            '/select',
        ];
        $this->assertHtml($expected, $result);
    }

    /**
     * testMeridian method
     *
     * Test generating an input for the meridian.
     *
     * @return void
     */
    public function testMeridian() {
        extract($this->dateRegex);

        $now = new \DateTime();
        $result = $this->Form->meridian('Model.field', ['value' => 'am']);
        $expected = [
            ['select' => ['name' => 'Model[field][meridian]', 'class' => 'form-control']],
            ['option' => ['value' => '']],
            '/option',
            $meridianRegex,
            ['option' => ['value' => $now->format('a'), 'selected' => 'selected']],
            $now->format('a'),
            '/option',
            '*/select'
        ];
        $this->assertHtml($expected, $result);
    }

    /**
     * testHour method
     *
     * Test generation of an hour input.
     *
     * @return void
     */
    public function testHour() {
        extract($this->dateRegex);

        $result = $this->Form->hour('Model.field', ['format' => 12, 'value' => '']);
        $expected = [
            ['select' => ['name' => 'Model[field][hour]', 'class' => 'form-control']],
            ['option' => ['selected' => 'selected', 'value' => '']],
            '/option',
            ['option' => ['value' => '01']],
            '1',
            '/option',
            ['option' => ['value' => '02']],
            '2',
            '/option',
            $hoursRegex,
            '/select',
        ];
        $this->assertHtml($expected, $result);

        $this->Form->request->data['Model']['field'] = '2006-10-10 00:12:32';
        $result = $this->Form->hour('Model.field', ['format' => 12]);
        $expected = [
            ['select' => ['name' => 'Model[field][hour]', 'class' => 'form-control']],
            ['option' => ['value' => '']],
            '/option',
            ['option' => ['value' => '01']],
            '1',
            '/option',
            ['option' => ['value' => '02']],
            '2',
            '/option',
            $hoursRegex,
            ['option' => ['value' => '12', 'selected' => 'selected']],
            '12',
            '/option',
            '/select',
        ];
        $this->assertHtml($expected, $result);

        $this->Form->request->data['Model']['field'] = '';
        $result = $this->Form->hour('Model.field', ['format' => 24, 'value' => '23']);
        $this->assertContains('<option value="23" selected="selected">23</option>', $result);

        $result = $this->Form->hour('Model.field', ['format' => 12, 'value' => '23']);
        $this->assertContains('<option value="11" selected="selected">11</option>', $result);

        $this->Form->request->data['Model']['field'] = '2006-10-10 00:12:32';
        $result = $this->Form->hour('Model.field', ['format' => 24]);
        $expected = [
            ['select' => ['name' => 'Model[field][hour]', 'class' => 'form-control']],
            ['option' => ['value' => '']],
            '/option',
            ['option' => ['value' => '00', 'selected' => 'selected']],
            '0',
            '/option',
            ['option' => ['value' => '01']],
            '1',
            '/option',
            ['option' => ['value' => '02']],
            '2',
            '/option',
            $hoursRegex,
            '/select',
        ];
        $this->assertHtml($expected, $result);

        unset($this->Form->request->data['Model']['field']);
        $result = $this->Form->hour('Model.field', ['format' => 24, 'value' => 'now']);
        $thisHour = date('H');
        $optValue = date('G');
        $this->assertRegExp('/<option value="' . $thisHour . '" selected="selected">' . $optValue . '<\/option>/', $result);

        $this->Form->request->data['Model']['field'] = '2050-10-10 01:12:32';
        $result = $this->Form->hour('Model.field', ['format' => 24]);
        $expected = [
            ['select' => ['name' => 'Model[field][hour]', 'class' => 'form-control']],
            ['option' => ['value' => '']],
            '/option',
            ['option' => ['value' => '00']],
            '0',
            '/option',
            ['option' => ['value' => '01', 'selected' => 'selected']],
            '1',
            '/option',
            ['option' => ['value' => '02']],
            '2',
            '/option',
            $hoursRegex,
            '/select',
        ];
        $this->assertHtml($expected, $result);
    }

    /**
     * testYear method
     *
     * Test generation of a year input.
     *
     * @return void
     */
    public function testYear() {
        $result = $this->Form->year('Model.field', ['value' => '', 'minYear' => 2006, 'maxYear' => 2007]);
        $expected = [
            ['select' => ['name' => 'Model[field][year]', 'class' => 'form-control']],
            ['option' => ['selected' => 'selected', 'value' => '']],
            '/option',
            ['option' => ['value' => '2007']],
            '2007',
            '/option',
            ['option' => ['value' => '2006']],
            '2006',
            '/option',
            '/select',
        ];
        $this->assertHtml($expected, $result);

        $result = $this->Form->year('Model.field', [
            'value' => '',
            'minYear' => 2006,
            'maxYear' => 2007,
            'orderYear' => 'asc'
        ]);
        $expected = [
            ['select' => ['name' => 'Model[field][year]', 'class' => 'form-control']],
            ['option' => ['selected' => 'selected', 'value' => '']],
            '/option',
            ['option' => ['value' => '2006']],
            '2006',
            '/option',
            ['option' => ['value' => '2007']],
            '2007',
            '/option',
            '/select',
        ];
        $this->assertHtml($expected, $result);

        $this->Form->request->data['Contact']['published'] = '2006-10-10';
        $result = $this->Form->year('Contact.published', [
            'empty' => false,
            'minYear' => 2006,
            'maxYear' => 2007,
        ]);
        $expected = [
            ['select' => ['name' => 'Contact[published][year]', 'class' => 'form-control']],
            ['option' => ['value' => '2007']],
            '2007',
            '/option',
            ['option' => ['value' => '2006', 'selected' => 'selected']],
            '2006',
            '/option',
            '/select',
        ];
        $this->assertHtml($expected, $result);

        $result = $this->Form->year('Contact.published', [
            'empty' => 'Published on',
        ]);
        $this->assertContains('Published on', $result);
    }

    /**
     * testDateTime method
     *
     * Test generation of date/time select elements.
     *
     * @return void
     */
    public function testDateTime() {
        extract($this->dateRegex);

        $result = $this->Form->dateTime('Contact.date', ['default' => true]);
        $now = strtotime('now');
        $expected = [
            ['select' => ['name' => 'Contact[date][year]', 'class' => 'form-control']],
            ['option' => ['value' => '']],
            '/option',
            $yearsRegex,
            ['option' => ['value' => date('Y', $now), 'selected' => 'selected']],
            date('Y', $now),
            '/option',
            '*/select',

            ['select' => ['name' => 'Contact[date][month]', 'class' => 'form-control']],
            ['option' => ['value' => '']],
            '/option',
            $monthsRegex,
            ['option' => ['value' => date('m', $now), 'selected' => 'selected']],
            date('F', $now),
            '/option',
            '*/select',

            ['select' => ['name' => 'Contact[date][day]', 'class' => 'form-control']],
            ['option' => ['value' => '']],
            '/option',
            $daysRegex,
            ['option' => ['value' => date('d', $now), 'selected' => 'selected']],
            date('j', $now),
            '/option',
            '*/select',

            ['select' => ['name' => 'Contact[date][hour]', 'class' => 'form-control']],
            ['option' => ['value' => '']],
            '/option',
            $hoursRegex,
            ['option' => ['value' => date('H', $now), 'selected' => 'selected']],
            date('G', $now),
            '/option',
            '*/select',

            ['select' => ['name' => 'Contact[date][minute]', 'class' => 'form-control']],
            ['option' => ['value' => '']],
            '/option',
            $minutesRegex,
            ['option' => ['value' => date('i', $now), 'selected' => 'selected']],
            date('i', $now),
            '/option',
            '*/select',
        ];
        $this->assertHtml($expected, $result);

        // Empty=>false implies Default=>true, as selecting the "first" dropdown value is useless
        $result = $this->Form->dateTime('Contact.date', ['empty' => false]);
        $now = strtotime('now');
        $expected = [
            ['select' => ['name' => 'Contact[date][year]', 'class' => 'form-control']],
            $yearsRegex,
            ['option' => ['value' => date('Y', $now), 'selected' => 'selected']],
            date('Y', $now),
            '/option',
            '*/select',

            ['select' => ['name' => 'Contact[date][month]', 'class' => 'form-control']],
            $monthsRegex,
            ['option' => ['value' => date('m', $now), 'selected' => 'selected']],
            date('F', $now),
            '/option',
            '*/select',

            ['select' => ['name' => 'Contact[date][day]', 'class' => 'form-control']],
            $daysRegex,
            ['option' => ['value' => date('d', $now), 'selected' => 'selected']],
            date('j', $now),
            '/option',
            '*/select',

            ['select' => ['name' => 'Contact[date][hour]', 'class' => 'form-control']],
            $hoursRegex,
            ['option' => ['value' => date('H', $now), 'selected' => 'selected']],
            date('G', $now),
            '/option',
            '*/select',

            ['select' => ['name' => 'Contact[date][minute]', 'class' => 'form-control']],
            $minutesRegex,
            ['option' => ['value' => date('i', $now), 'selected' => 'selected']],
            date('i', $now),
            '/option',
            '*/select',
        ];
        $this->assertHtml($expected, $result);
    }

    /**
     * testDatetimeEmpty method
     *
     * Test empty defaulting to true for datetime.
     *
     * @return void
     */
    public function testDatetimeEmpty() {
        extract($this->dateRegex);

        $result = $this->Form->dateTime('Contact.date', [
            'timeFormat' => 12,
            'empty' => true,
            'default' => true
        ]);
        $expected = [
            ['select' => ['name' => 'Contact[date][year]', 'class' => 'form-control']],
            $yearsRegex,
            ['option' => ['value' => '']],
            '/option',
            '*/select',

            ['select' => ['name' => 'Contact[date][month]', 'class' => 'form-control']],
            $monthsRegex,
            ['option' => ['value' => '']],
            '/option',
            '*/select',

            ['select' => ['name' => 'Contact[date][day]', 'class' => 'form-control']],
            $daysRegex,
            ['option' => ['value' => '']],
            '/option',
            '*/select',

            ['select' => ['name' => 'Contact[date][hour]', 'class' => 'form-control']],
            $hoursRegex,
            ['option' => ['value' => '']],
            '/option',
            '*/select',

            ['select' => ['name' => 'Contact[date][minute]', 'class' => 'form-control']],
            $minutesRegex,
            ['option' => ['value' => '']],
            '/option',
            '*/select',

            ['select' => ['name' => 'Contact[date][meridian]', 'class' => 'form-control']],
            $meridianRegex,
            ['option' => ['value' => '']],
            '/option',
            '*/select'
        ];
        $this->assertHtml($expected, $result);
        $this->assertNotRegExp('/<option[^<>]+value=""[^<>]+selected="selected"[^>]*>/', $result);
    }

    /**
     * testDatetimeMinuteInterval method
     *
     * Test datetime with interval option.
     *
     * @return void
     */
    public function testDatetimeMinuteInterval() {
        extract($this->dateRegex);

        $result = $this->Form->dateTime('Contact.date', [
            'interval' => 5,
            'value' => ''
        ]);
        $expected = [
            ['select' => ['name' => 'Contact[date][year]', 'class' => 'form-control']],
            $yearsRegex,
            ['option' => ['selected' => 'selected', 'value' => '']],
            '/option',
            '*/select',

            ['select' => ['name' => 'Contact[date][month]', 'class' => 'form-control']],
            $monthsRegex,
            ['option' => ['selected' => 'selected', 'value' => '']],
            '/option',
            '*/select',

            ['select' => ['name' => 'Contact[date][day]', 'class' => 'form-control']],
            $daysRegex,
            ['option' => ['selected' => 'selected', 'value' => '']],
            '/option',
            '*/select',

            ['select' => ['name' => 'Contact[date][hour]', 'class' => 'form-control']],
            $hoursRegex,
            ['option' => ['selected' => 'selected', 'value' => '']],
            '/option',
            '*/select',

            ['select' => ['name' => 'Contact[date][minute]', 'class' => 'form-control']],
            $minutesRegex,
            ['option' => ['selected' => 'selected', 'value' => '']],
            '/option',
            ['option' => ['value' => '00']],
            '00',
            '/option',
            ['option' => ['value' => '05']],
            '05',
            '/option',
            ['option' => ['value' => '10']],
            '10',
            '/option',
            '*/select',
        ];
        $this->assertHtml($expected, $result);
    }

    /**
     * Test using template vars in various templates used by control() method.
     *
     * @return void
     */
    public function testControlTemplateVars() {
        $result = $this->Form->control('text', [
            'templates' => [
                'input' => '<input custom="{{forinput}}" type="{{type}}" name="{{name}}"{{attrs}}/>',
                'label' => '<label{{attrs}}>{{text}} {{forlabel}}</label>',
                'formGroup' => '{{label}}{{forgroup}}{{input}}',
                'inputContainer' => '<div class="input {{type}}{{required}}">{{content}}{{forcontainer}}</div>',
            ],
            'templateVars' => [
                'forinput' => 'in-input',
                'forlabel' => 'in-label',
                'forgroup' => 'in-group',
                'forcontainer' => 'in-container'
            ]
        ]);
        $expected = [
            'div' => ['class'],
            'label' => ['for', 'class'],
            'Text in-label',
            '/label',
            'in-group',
            'input' => ['name', 'type' => 'text', 'id', 'custom' => 'in-input', 'class' => 'form-control'],
            'in-container',
            '/div',
        ];
        $this->assertHtml($expected, $result);
    }

    /**
     * Test ensuring template variables work in template files loaded
     * during control().
     *
     * @return void
     */
    public function testControlTemplatesFromFile() {
        $result = $this->Form->control('title', [
            'templates' => 'test_templates',
            'templateVars' => [
                'forcontainer' => 'container-data'
            ]
        ]);
        $expected = [
            'div' => ['class'],
            'label' => ['for', 'class'],
            'Title',
            '/label',
            'input' => ['name', 'type' => 'text', 'id', 'class' => 'form-control'],
            'container-data',
            '/div',
        ];
        $this->assertHtml($expected, $result);
    }

    /**
     * testFormSecuredControl method
     *
     * Test generation of entire secure form, assertions made on control() output.
     *
     * @return void
     */
    public function testFormSecuredControl() {
        $this->Form->request->params['_csrfToken'] = 'testKey';
        $this->Form->request->params['_Token'] = 'stuff';
        $this->article['schema'] = [
            'ratio' => ['type' => 'decimal', 'length' => 5, 'precision' => 6],
            'population' => ['type' => 'decimal', 'length' => 15, 'precision' => 0],
        ];

        $result = $this->Form->create($this->article, ['url' => '/articles/add']);
        $encoding = strtolower(Configure::read('App.encoding'));
        $expected = [
            'form' => ['method' => 'post', 'action' => '/articles/add', 'accept-charset' => $encoding],
            'div' => ['style' => 'display:none;'],
            ['input' => ['type' => 'hidden', 'name' => '_method', 'value' => 'POST']],
            ['input' => [
                'type' => 'hidden',
                'name' => '_csrfToken',
                'value' => 'testKey'
            ]],
            '/div'
        ];
        $this->assertHtml($expected, $result);

        $result = $this->Form->control('ratio');
        $expected = [
            'div' => ['class'],
            'label' => ['for', 'class'],
            'Ratio',
            '/label',
            'input' => ['name', 'type' => 'number', 'step' => '0.000001', 'id', 'class'],
            '/div',
        ];
        $this->assertHtml($expected, $result);

        $result = $this->Form->control('population');
        $expected = [
            'div' => ['class'],
            'label' => ['for', 'class'],
            'Population',
            '/label',
            'input' => ['name', 'type' => 'number', 'step' => '1', 'id', 'class'],
            '/div',
        ];
        $this->assertHtml($expected, $result);

        $result = $this->Form->control('published', ['type' => 'text']);
        $expected = [
            'div' => ['class' => 'form-group'],
            'label' => ['for' => 'published', 'class'],
            'Published',
            '/label',
            ['input' => [
                'type' => 'text',
                'name' => 'published',
                'id' => 'published',
                'class'
            ]],
            '/div'
        ];
        $this->assertHtml($expected, $result);

        $result = $this->Form->control('other', ['type' => 'text']);
        $expected = [
            'div' => ['class' => 'form-group'],
            'label' => ['for' => 'other', 'class'],
            'Other',
            '/label',
            ['input' => [
                'type' => 'text',
                'name' => 'other',
                'id',
                'class'
            ]],
            '/div'
        ];
        $this->assertHtml($expected, $result);

        $result = $this->Form->hidden('stuff');
        $expected = [
            'input' => [
                'type' => 'hidden',
                'name' => 'stuff'
            ]
        ];

        $this->assertHtml($expected, $result);

        $result = $this->Form->hidden('hidden', ['value' => '0']);
        $expected = ['input' => [
            'type' => 'hidden',
            'name' => 'hidden',
            'value' => '0'
        ]];
        $this->assertHtml($expected, $result);

        $result = $this->Form->control('something', ['type' => 'checkbox']);
        $expected = [
            'div' => ['class' => 'form-check'],
            ['input' => [
                'type' => 'hidden',
                'name' => 'something',
                'value' => '0'
            ]],
            'label' => ['for' => 'something', 'class'],
            ['input' => [
                'type' => 'checkbox',
                'name' => 'something',
                'value' => '1',
                'id' => 'something',
                'class'
            ]],
            'Something',
            '/label',
            '/div'
        ];
        $this->assertHtml($expected, $result);
        $result = $this->Form->fields;
        $expectedFields = [
            'ratio',
            'population',
            'published',
            'other',
            'stuff' => '',
            'hidden' => '0',
            'something'
        ];
        $this->assertEquals($expectedFields, $result);

        $result = $this->Form->secure($this->Form->fields);
        $tokenDebug = urlencode(json_encode([
            '/articles/add',
            $expectedFields,
            []
        ]));

        $expected = [
            'div' => ['style' => 'display:none;'],
            ['input' => [
                'type' => 'hidden',
                'name' => '_Token[fields]',
                'value'
            ]],
            ['input' => [
                'type' => 'hidden',
                'name' => '_Token[unlocked]',
                'value' => ''
            ]],
            ['input' => [
                'type' => 'hidden', 'name' => '_Token[debug]',
                'value' => $tokenDebug,
            ]],
            '/div'
        ];
        $this->assertHtml($expected, $result);
    }

    /**
     * testEmptyControlErrorValidation method
     *
     * Test validation errors, when calling control() overriding validation message by an empty string.
     *
     * @return void
     */
    public function testEmptyControlErrorValidation() {
        $this->article['errors'] = [
            'Article' => ['title' => 'error message']
        ];
        $this->Form->create($this->article);

        $result = $this->Form->control('Article.title', ['error' => '']);
        $expected = [
            'div' => ['class' => 'form-group has-danger'],
            'label' => ['for' => 'article-title', 'class'],
            'Title',
            '/label',
            'input' => [
                'type' => 'text', 'name' => 'Article[title]',
                'id' => 'article-title', 'class' => 'form-control-danger form-control'
            ],
            ['div' => ['class' => 'form-control-feedback']],
            [],
            '/div',
            '/div'
        ];
        $this->assertHtml($expected, $result);
    }

    /**
     * testControlErrorMessage method
     *
     * Test validation errors, when calling control() overriding validation messages.
     *
     * @return void
     */
    public function testControlErrorMessage() {
        $this->article['errors'] = [
            'title' => ['error message']
        ];
        $this->Form->create($this->article);

        $result = $this->Form->control('title', [
            'error' => 'Custom error!'
        ]);
        $expected = [
            'div' => ['class' => 'form-group has-danger'],
            'label' => ['for' => 'title', 'class'],
            'Title',
            '/label',
            'input' => [
                'type' => 'text', 'name' => 'title',
                'id' => 'title', 'class' => 'form-control-danger form-control',
                'required' => 'required',
            ],
            ['div' => ['class' => 'form-control-feedback']],
            'Custom error!',
            '/div',
            '/div'
        ];
        $this->assertHtml($expected, $result);

        $result = $this->Form->control('title', [
            'error' => ['error message' => 'Custom error!']
        ]);
        $expected = [
            'div' => ['class' => 'form-group has-danger'],
            'label' => ['for' => 'title', 'class'],
            'Title',
            '/label',
            'input' => [
                'type' => 'text',
                'name' => 'title',
                'id' => 'title',
                'class' => 'form-control-danger form-control',
                'required' => 'required'
            ],
            ['div' => ['class' => 'form-control-feedback']],
            'Custom error!',
            '/div',
            '/div'
        ];
        $this->assertHtml($expected, $result);
    }

    /**
     * testControl method
     *
     * Test various incarnations of control().
     *
     * @return void
     */
    public function testControl() {
        TableRegistry::get('ValidateUsers', [
            'className' => 'Cake\Test\TestCase\View\Helper\ValidateUsersTable'
        ]);
        $this->Form->create([], ['context' => ['table' => 'ValidateUsers']]);
        $result = $this->Form->control('ValidateUsers.balance');
        $expected = [
            'div' => ['class'],
            'label' => ['for', 'class'],
            'Balance',
            '/label',
            'input' => ['name', 'type' => 'number', 'id', 'step', 'class'],
            '/div',
        ];
        $this->assertHtml($expected, $result);

        $result = $this->Form->control('ValidateUser.cost_decimal');
        $expected = [
            'div' => ['class'],
            'label' => ['for', 'class'],
            'Cost Decimal',
            '/label',
            'input' => ['name', 'type' => 'number', 'step' => '0.001', 'id', 'class'],
            '/div',
        ];
        $this->assertHtml($expected, $result);

        $result = $this->Form->control('ValidateUser.null_decimal');
        $expected = [
            'div' => ['class'],
            'label' => ['for', 'class'],
            'Null Decimal',
            '/label',
            'input' => ['name', 'type' => 'number', 'id', 'class'],
            '/div',
        ];
        $this->assertHtml($expected, $result);
    }

    /**
     * testControlCustomization method
     *
     * Tests the input method and passing custom options.
     *
     * @return void
     */
    public function testControlCustomization() {
        TableRegistry::get('Contacts', [
            'className' => 'Cake\Test\TestCase\View\Helper\ContactsTable'
        ]);
        $this->Form->create([], ['context' => ['table' => 'Contacts']]);
        $result = $this->Form->control('Contact.email', ['id' => 'custom']);
        $expected = [
            'div' => ['class' => 'form-group'],
            'label' => ['for' => 'custom', 'class'],
            'Email',
            '/label',
            ['input' => [
                'type' => 'email', 'name' => 'Contact[email]',
                'id' => 'custom', 'maxlength' => 255, 'class'
            ]],
            '/div'
        ];
        $this->assertHtml($expected, $result);

        $result = $this->Form->control('Contact.email', [
            'templates' => ['inputContainer' => '<div>{{content}}</div>']
        ]);
        $expected = [
            '<div',
            'label' => ['for' => 'contact-email', 'class'],
            'Email',
            '/label',
            ['input' => [
                'type' => 'email', 'name' => 'Contact[email]',
                'id' => 'contact-email', 'maxlength' => 255, 'class'
            ]],
            '/div'
        ];
        $this->assertHtml($expected, $result);

        $result = $this->Form->control('Contact.email', ['type' => 'text']);
        $expected = [
            'div' => ['class' => 'form-group'],
            'label' => ['for' => 'contact-email', 'class'],
            'Email',
            '/label',
            ['input' => [
                'type' => 'text', 'name' => 'Contact[email]',
                'id' => 'contact-email', 'maxlength' => '255', 'class'
            ]],
            '/div'
        ];
        $this->assertHtml($expected, $result);

        $result = $this->Form->control('Contact.5.email', ['type' => 'text']);
        $expected = [
            'div' => ['class' => 'form-group'],
            'label' => ['for' => 'contact-5-email', 'class'],
            'Email',
            '/label',
            ['input' => [
                'type' => 'text', 'name' => 'Contact[5][email]',
                'id' => 'contact-5-email', 'maxlength' => '255', 'class'
            ]],
            '/div'
        ];
        $this->assertHtml($expected, $result);

        $result = $this->Form->control('Contact.password');
        $expected = [
            'div' => ['class' => 'form-group'],
            'label' => ['for' => 'contact-password', 'class'],
            'Password',
            '/label',
            ['input' => [
                'type' => 'password', 'name' => 'Contact[password]',
                'id' => 'contact-password', 'class'
            ]],
            '/div'
        ];
        $this->assertHtml($expected, $result);

        $result = $this->Form->control('Contact.email', [
            'type' => 'file', 'class' => 'textbox'
        ]);
        $expected = [
            'div' => ['class' => 'form-group'],
            'label' => ['for' => 'contact-email'],
            'Email',
            '/label',
            ['input' => [
                'type' => 'file', 'name' => 'Contact[email]', 'class' => 'textbox form-control-file',
                'id' => 'contact-email'
            ]],
            '/div'
        ];
        $this->assertHtml($expected, $result);

        $entity = new Entity(['phone' => 'Hello & World > weird chars']);
        $this->Form->create($entity, ['context' => ['table' => 'Contacts']]);
        $result = $this->Form->control('phone');
        $expected = [
            'div' => ['class' => 'form-group'],
            'label' => ['for' => 'phone', 'class'],
            'Phone',
            '/label',
            ['input' => [
                'type' => 'tel', 'name' => 'phone',
                'value' => 'Hello &amp; World &gt; weird chars',
                'id' => 'phone', 'maxlength' => 255, 'class'
            ]],
            '/div'
        ];
        $this->assertHtml($expected, $result);

        $this->Form->request->data['Model']['0']['OtherModel']['field'] = 'My value';
        $this->Form->create();
        $result = $this->Form->control('Model.0.OtherModel.field', ['id' => 'myId']);
        $expected = [
            'div' => ['class' => 'form-group'],
            'label' => ['for' => 'myId', 'class'],
            'Field',
            '/label',
            'input' => [
                'type' => 'text', 'name' => 'Model[0][OtherModel][field]',
                'value' => 'My value', 'id' => 'myId', 'class'
            ],
            '/div'
        ];
        $this->assertHtml($expected, $result);

        $this->Form->request->data = [];

        $entity->errors('field', 'Badness!');
        $this->Form->create($entity, ['context' => ['table' => 'Contacts']]);
        $result = $this->Form->control('field');
        $expected = [
            'div' => ['class' => 'form-group has-danger'],
            'label' => ['for' => 'field', 'class'],
            'Field',
            '/label',
            'input' => [
                'type' => 'text', 'name' => 'field',
                'id' => 'field', 'class' => 'form-control-danger form-control'
            ],
            ['div' => ['class' => 'form-control-feedback']],
            'Badness!',
            '/div',
            '/div'
        ];
        $this->assertHtml($expected, $result);

        $result = $this->Form->control('field', [
            'templates' => [
                'inputContainerError' => '{{content}}{{error}}',
                'error' => '<span class="error-message">{{content}}</span>'
            ]
        ]);
        $expected = [
            'label' => ['for' => 'field', 'class'],
            'Field',
            '/label',
            'input' => [
                'type' => 'text', 'name' => 'field',
                'id' => 'field', 'class' => 'form-control-danger form-control'
            ],
            ['span' => ['class' => 'error-message']],
            'Badness!',
            '/span'
        ];
        $this->assertHtml($expected, $result);

        $entity->errors('field', ['minLength'], true);
        $result = $this->Form->control('field', [
            'error' => [
                'minLength' => 'Le login doit contenir au moins 2 caractères',
                'maxLength' => 'login too large'
            ]
        ]);
        $expected = [
            'div' => ['class' => 'form-group has-danger'],
            'label' => ['for' => 'field', 'class'],
            'Field',
            '/label',
            'input' => ['type' => 'text', 'name' => 'field', 'id' => 'field', 'class' => 'form-control-danger form-control'],
            ['div' => ['class' => 'form-control-feedback']],
            'Le login doit contenir au moins 2 caractères',
            '/div',
            '/div'
        ];
        $this->assertHtml($expected, $result);

        $entity->errors('field', ['maxLength'], true);
        $result = $this->Form->control('field', [
            'error' => [
                'minLength' => 'Le login doit contenir au moins 2 caractères',
                'maxLength' => 'login too large',
            ]
        ]);
        $expected = [
            'div' => ['class' => 'form-group has-danger'],
            'label' => ['for' => 'field', 'class'],
            'Field',
            '/label',
            'input' => ['type' => 'text', 'name' => 'field', 'id' => 'field', 'class' => 'form-control-danger form-control'],
            ['div' => ['class' => 'form-control-feedback']],
            'login too large',
            '/div',
            '/div'
        ];
        $this->assertHtml($expected, $result);
    }

    /**
     * testControlWithTemplateFile method
     *
     * Test that control() accepts a template file.
     *
     * @return void
     */
    public function testControlWithTemplateFile() {
        $result = $this->Form->control('field', [
            'templates' => 'htmlhelper_tags'
        ]);
        $expected = [
            'label' => ['for' => 'field', 'class'],
            'Field',
            '/label',
            'input' => [
                'type' => 'text', 'name' => 'field',
                'id' => 'field', 'class'
            ],
        ];
        $this->assertHtml($expected, $result);
    }

    /**
     * testNestedControlsEndWithBrackets method
     *
     * Test that nested inputs end with brackets.
     *
     * @return void
     */
    public function testNestedControlsEndWithBrackets() {
        $result = $this->Form->text('nested.text[]');
        $expected = [
            'input' => [
                'type' => 'text', 'name' => 'nested[text][]', 'class'
            ],
        ];
        $this->assertHtml($expected, $result);

        $result = $this->Form->file('nested.file[]');
        $expected = [
            'input' => [
                'type' => 'file', 'name' => 'nested[file][]', 'class'
            ],
        ];
        $this->assertHtml($expected, $result);
    }

    /**
     * testControlZero method
     *
     * Test that inputs with 0 can be created.
     *
     * @return void
     */
    public function testControlZero() {
        TableRegistry::get('Contacts', [
            'className' => 'Cake\Test\TestCase\View\Helper\ContactsTable'
        ]);
        $this->Form->create([], ['context' => ['table' => 'Contacts']]);
        $result = $this->Form->control('0');
        $expected = [
            'div' => ['class' => 'form-group'],
            'label' => ['for' => '0', 'class'], '/label',
            'input' => ['type' => 'text', 'name' => '0', 'id' => '0', 'class'],
            '/div'
        ];
        $this->assertHtml($expected, $result);
    }

    /**
     * testControlCheckbox method
     *
     * Test control() with checkbox creation.
     *
     * @return void
     */
    public function testControlCheckbox() {
        $result = $this->Form->control('User.active', ['label' => false, 'checked' => true]);
        $expected = [
            'div' => ['class' => 'form-check'],
            'input' => ['type' => 'hidden', 'name' => 'User[active]', 'value' => '0'],
            ['input' => ['type' => 'checkbox', 'name' => 'User[active]', 'value' => '1', 'id' => 'user-active', 'checked' => 'checked', 'class']],
            '/div'
        ];
        $this->assertHtml($expected, $result);

        $result = $this->Form->control('User.active', ['label' => false, 'checked' => 1]);
        $expected = [
            'div' => ['class' => 'form-check'],
            'input' => ['type' => 'hidden', 'name' => 'User[active]', 'value' => '0'],
            ['input' => ['type' => 'checkbox', 'name' => 'User[active]', 'value' => '1', 'id' => 'user-active', 'checked' => 'checked', 'class']],
            '/div'
        ];
        $this->assertHtml($expected, $result);

        $result = $this->Form->control('User.active', ['label' => false, 'checked' => '1']);
        $expected = [
            'div' => ['class' => 'form-check'],
            'input' => ['type' => 'hidden', 'name' => 'User[active]', 'value' => '0'],
            ['input' => ['type' => 'checkbox', 'name' => 'User[active]', 'value' => '1', 'id' => 'user-active', 'checked' => 'checked', 'class']],
            '/div'
        ];
        $this->assertHtml($expected, $result);

        $result = $this->Form->control('User.disabled', [
            'label' => 'Disabled',
            'type' => 'checkbox',
            'data-foo' => 'disabled'
        ]);
        $expected = [
            'div' => ['class' => 'form-check'],
            'input' => ['type' => 'hidden', 'name' => 'User[disabled]', 'value' => '0'],
            'label' => ['for' => 'user-disabled', 'class'],
            ['input' => [
                'type' => 'checkbox',
                'name' => 'User[disabled]',
                'value' => '1',
                'id' => 'user-disabled',
                'data-foo' => 'disabled',
                'class'
            ]],
            'Disabled',
            '/label',
            '/div'
        ];
        $this->assertHtml($expected, $result);

        $result = $this->Form->control('User.confirm', [
            'label' => 'Confirm <b>me</b>!',
            'type' => 'checkbox',
            'escape' => false
        ]);
        $expected = [
            'div' => ['class' => 'form-check'],
            'input' => ['type' => 'hidden', 'name' => 'User[confirm]', 'value' => '0'],
            'label' => ['for' => 'user-confirm', 'class'],
            ['input' => [
                'type' => 'checkbox',
                'name' => 'User[confirm]',
                'value' => '1',
                'id' => 'user-confirm',
                'class'
            ]],
            'Confirm <b>me</b>!',
            '/label',
            '/div'
        ];
        $this->assertHtml($expected, $result);
    }

    /**
     * testControlCheckboxWithDisabledElements method
     *
     * Test generating checkboxes with disabled elements.
     *
     * @return void
     */
    public function testControlCheckboxWithDisabledElements() {
        $options = [1 => 'One', 2 => 'Two', '3' => 'Three'];
        $result = $this->Form->control('Contact.multiple', [
            'multiple' => 'checkbox',
            'disabled' => 'disabled',
            'options' => $options
        ]);
        $expected = [
            ['div' => ['class' => 'form-group']],
            ['label' => ['for' => "contact-multiple"]],
            'Multiple',
            '/label',
            ['input' => ['type' => 'hidden', 'name' => "Contact[multiple]", 'disabled' => 'disabled', 'value' => '']],
            ['div' => ['class' => 'form-check']],
            ['label' => ['for' => "contact-multiple-1", 'class']],
            ['input' => ['type' => 'checkbox', 'name' => "Contact[multiple][]", 'value' => 1, 'disabled' => 'disabled', 'id' => "contact-multiple-1", 'class']],
            'One',
            '/label',
            '/div',
            ['div' => ['class' => 'form-check']],
            ['label' => ['for' => "contact-multiple-2", 'class']],
            ['input' => ['type' => 'checkbox', 'name' => "Contact[multiple][]", 'value' => 2, 'disabled' => 'disabled', 'id' => "contact-multiple-2", 'class']],
            'Two',
            '/label',
            '/div',
            ['div' => ['class' => 'form-check']],
            ['label' => ['for' => "contact-multiple-3", 'class']],
            ['input' => ['type' => 'checkbox', 'name' => "Contact[multiple][]", 'value' => 3, 'disabled' => 'disabled', 'id' => "contact-multiple-3", 'class']],
            'Three',
            '/label',
            '/div',
            '/div'
        ];
        $this->assertHtml($expected, $result);

        // make sure 50 does only disable 50, and not 50f5c0cf
        $options = ['50' => 'Fifty', '50f5c0cf' => 'Stringy'];
        $disabled = [50];

        $expected = [
            ['div' => ['class' => 'form-group']],
            ['label' => ['for' => "contact-multiple"]],
            'Multiple',
            '/label',
            ['input' => ['type' => 'hidden', 'name' => "Contact[multiple]", 'value' => '']],
            ['div' => ['class' => 'form-check']],
            ['label' => ['for' => "contact-multiple-50", 'class']],
            ['input' => ['type' => 'checkbox', 'name' => "Contact[multiple][]", 'value' => 50, 'disabled' => 'disabled', 'id' => "contact-multiple-50", 'class']],
            'Fifty',
            '/label',
            '/div',
            ['div' => ['class' => 'form-check']],
            ['label' => ['for' => "contact-multiple-50f5c0cf", 'class']],
            ['input' => ['type' => 'checkbox', 'name' => "Contact[multiple][]", 'value' => '50f5c0cf', 'id' => "contact-multiple-50f5c0cf", 'class']],
            'Stringy',
            '/label',
            '/div',
            '/div'
        ];
        $result = $this->Form->control('Contact.multiple', ['multiple' => 'checkbox', 'disabled' => $disabled, 'options' => $options]);
        $this->assertHtml($expected, $result);
    }

    /**
     * testControlWithLeadingInteger method
     *
     * Test input name with leading integer, ensure attributes are generated correctly.
     *
     * @return void
     */
    public function testControlWithLeadingInteger() {
        $result = $this->Form->text('0.Node.title');
        $expected = [
            'input' => ['name' => '0[Node][title]', 'type' => 'text', 'class']
        ];
        $this->assertHtml($expected, $result);
    }

    /**
     * testControlSelectType method
     *
     * Test form->control() with select type inputs.
     *
     * @return void
     */
    public function testControlSelectType() {
        $result = $this->Form->control(
            'email',
            [
                'options' => ['è' => 'Firést', 'é' => 'Secoènd'], 'empty' => true]
        );
        $expected = [
            'div' => ['class' => 'form-group'],
            'label' => ['for' => 'email', 'class'],
            'Email',
            '/label',
            ['select' => ['name' => 'email', 'id' => 'email', 'class']],
            ['option' => ['value' => '']],
            '/option',
            ['option' => ['value' => 'è']],
            'Firést',
            '/option',
            ['option' => ['value' => 'é']],
            'Secoènd',
            '/option',
            '/select',
            '/div'
        ];
        $this->assertHtml($expected, $result);

        $result = $this->Form->control(
            'email',
            [
                'options' => ['First', 'Second'], 'empty' => true]
        );
        $expected = [
            'div' => ['class' => 'form-group'],
            'label' => ['for' => 'email', 'class'],
            'Email',
            '/label',
            ['select' => ['name' => 'email', 'id' => 'email', 'class']],
            ['option' => ['value' => '']],
            '/option',
            ['option' => ['value' => '0']],
            'First',
            '/option',
            ['option' => ['value' => '1']],
            'Second',
            '/option',
            '/select',
            '/div'
        ];
        $this->assertHtml($expected, $result);

        $result = $this->Form->control('email', [
            'type' => 'select',
            'options' => new \ArrayObject(['First', 'Second']),
            'empty' => true,
        ]);
        $this->assertHtml($expected, $result);

        $this->View->viewVars['users'] = ['value' => 'good', 'other' => 'bad'];
        $this->Form->request->data = ['Model' => ['user_id' => 'value']];

        $result = $this->Form->control('Model.user_id', ['empty' => true]);
        $expected = [
            'div' => ['class' => 'form-group'],
            'label' => ['for' => 'model-user-id', 'class'],
            'User',
            '/label',
            'select' => ['name' => 'Model[user_id]', 'id' => 'model-user-id', 'class'],
            ['option' => ['value' => '']],
            '/option',
            ['option' => ['value' => 'value', 'selected' => 'selected']],
            'good',
            '/option',
            ['option' => ['value' => 'other']],
            'bad',
            '/option',
            '/select',
            '/div'
        ];
        $this->assertHtml($expected, $result);

        $this->View->viewVars['users'] = ['value' => 'good', 'other' => 'bad'];
        $this->Form->request->data = ['Thing' => ['user_id' => null]];
        $result = $this->Form->control('Thing.user_id', ['empty' => 'Some Empty']);
        $expected = [
            'div' => ['class' => 'form-group'],
            'label' => ['for' => 'thing-user-id', 'class'],
            'User',
            '/label',
            'select' => ['name' => 'Thing[user_id]', 'id' => 'thing-user-id', 'class'],
            ['option' => ['value' => '']],
            'Some Empty',
            '/option',
            ['option' => ['value' => 'value']],
            'good',
            '/option',
            ['option' => ['value' => 'other']],
            'bad',
            '/option',
            '/select',
            '/div'
        ];
        $this->assertHtml($expected, $result);

        $this->View->viewVars['users'] = ['value' => 'good', 'other' => 'bad'];
        $this->Form->request->data = ['Thing' => ['user_id' => 'value']];
        $result = $this->Form->control('Thing.user_id', ['empty' => 'Some Empty']);
        $expected = [
            'div' => ['class' => 'form-group'],
            'label' => ['for' => 'thing-user-id', 'class'],
            'User',
            '/label',
            'select' => ['name' => 'Thing[user_id]', 'id' => 'thing-user-id', 'class'],
            ['option' => ['value' => '']],
            'Some Empty',
            '/option',
            ['option' => ['value' => 'value', 'selected' => 'selected']],
            'good',
            '/option',
            ['option' => ['value' => 'other']],
            'bad',
            '/option',
            '/select',
            '/div'
        ];
        $this->assertHtml($expected, $result);

        $this->Form->data = [];
        $result = $this->Form->control('Publisher.id', [
            'label' => 'Publisher',
            'type' => 'select',
            'multiple' => 'checkbox',
            'options' => ['Value 1' => 'Label 1', 'Value 2' => 'Label 2']
        ]);
        $expected = [
            ['div' => ['class' => 'form-group']],
            ['label' => ['for' => 'publisher-id']],
            'Publisher',
            '/label',
            'input' => ['type' => 'hidden', 'name' => 'Publisher[id]', 'value' => ''],
            ['div' => ['class' => 'form-check']],
            ['label' => ['for' => 'publisher-id-value-1', 'class']],
            ['input' => ['type' => 'checkbox', 'name' => 'Publisher[id][]', 'value' => 'Value 1', 'id' => 'publisher-id-value-1', 'class']],
            'Label 1',
            '/label',
            '/div',
            ['div' => ['class' => 'form-check']],
            ['label' => ['for' => 'publisher-id-value-2', 'class']],
            ['input' => ['type' => 'checkbox', 'name' => 'Publisher[id][]', 'value' => 'Value 2', 'id' => 'publisher-id-value-2', 'class']],
            'Label 2',
            '/label',
            '/div',
            '/div'
        ];
        $this->assertHtml($expected, $result);
    }

    /**
     * testControlOverridingMagicSelectType method
     *
     * Test that overriding the magic select type widget is possible.
     *
     * @return void
     */
    public function testControlOverridingMagicSelectType() {
        $this->View->viewVars['users'] = ['value' => 'good', 'other' => 'bad'];
        $result = $this->Form->control('Model.user_id', ['type' => 'text']);
        $expected = [
            'div' => ['class' => 'form-group'],
            'label' => ['for' => 'model-user-id', 'class'], 'User', '/label',
            'input' => ['name' => 'Model[user_id]', 'type' => 'text', 'id' => 'model-user-id', 'class'],
            '/div'
        ];
        $this->assertHtml($expected, $result);

        //Check that magic types still work for plural/singular vars
        $this->View->viewVars['types'] = ['value' => 'good', 'other' => 'bad'];
        $result = $this->Form->control('Model.type');
        $expected = [
            'div' => ['class' => 'form-group'],
            'label' => ['for' => 'model-type', 'class'], 'Type', '/label',
            'select' => ['name' => 'Model[type]', 'id' => 'model-type', 'class'],
            ['option' => ['value' => 'value']], 'good', '/option',
            ['option' => ['value' => 'other']], 'bad', '/option',
            '/select',
            '/div'
        ];
        $this->assertHtml($expected, $result);
    }

    /**
     * testControlMagicTypeDoesNotOverride method
     *
     * Test that inferred types do not override developer input.
     *
     * @return void
     */
    public function testControlMagicTypeDoesNotOverride() {
        $this->View->viewVars['users'] = ['value' => 'good', 'other' => 'bad'];
        $result = $this->Form->control('Model.user', ['type' => 'checkbox']);
        $expected = [
            'div' => ['class' => 'form-check'],
            ['input' => [
                'type' => 'hidden',
                'name' => 'Model[user]',
                'value' => 0,
            ]],
            'label' => ['for' => 'model-user', 'class'],
            ['input' => [
                'name' => 'Model[user]',
                'type' => 'checkbox',
                'id' => 'model-user',
                'value' => 1,
                'class'
            ]],
            'User',
            '/label',
            '/div'
        ];
        $this->assertHtml($expected, $result);

        // make sure that for HABTM the multiple option is not being overwritten in case it's truly
        $options = [
            1 => 'blue',
            2 => 'red'
        ];
        $result = $this->Form->control('tags._ids', ['options' => $options, 'multiple' => 'checkbox']);
        $expected = [
            'div' => ['class' => 'form-group'],
            'label' => ['for' => 'tags-ids'],
            'Tags',
            '/label',
            'input' => ['type' => 'hidden', 'name' => 'tags[_ids]', 'value' => ''],

            ['div' => ['class' => 'form-check']],
            ['label' => ['for' => 'tags-ids-1', 'class']],
            ['input' => [
                'id' => 'tags-ids-1', 'type' => 'checkbox',
                'value' => '1', 'name' => 'tags[_ids][]',
                'class'
            ]],
            'blue',
            '/label',
            '/div',

            ['div' => ['class' => 'form-check']],
            ['label' => ['for' => 'tags-ids-2', 'class']],
            ['input' => [
                'id' => 'tags-ids-2', 'type' => 'checkbox',
                'value' => '2', 'name' => 'tags[_ids][]',
                'class'
            ]],
            'red',
            '/label',
            '/div',

            '/div'
        ];
        $this->assertHtml($expected, $result);
    }

    /**
     * testControlMagicSelectForTypeNumber method
     *
     * Test that magic control() selects are created for type=number.
     *
     * @return void
     */
    public function testControlMagicSelectForTypeNumber() {
        TableRegistry::get('ValidateUsers', [
            'className' => 'Cake\Test\TestCase\View\Helper\ValidateUsersTable'
        ]);
        $entity = new Entity(['balance' => 1]);
        $this->Form->create($entity, ['context' => ['table' => 'ValidateUsers']]);
        $this->View->viewVars['balances'] = [0 => 'nothing', 1 => 'some', 100 => 'a lot'];
        $result = $this->Form->control('balance');
        $expected = [
            'div' => ['class' => 'form-group'],
            'label' => ['for' => 'balance', 'class'],
            'Balance',
            '/label',
            'select' => ['name' => 'balance', 'id' => 'balance', 'class'],
            ['option' => ['value' => '0']],
            'nothing',
            '/option',
            ['option' => ['value' => '1', 'selected' => 'selected']],
            'some',
            '/option',
            ['option' => ['value' => '100']],
            'a lot',
            '/option',
            '/select',
            '/div'
        ];
        $this->assertHtml($expected, $result);
    }

    /**
     * testFormControlSubmit method
     *
     * Test correct results for form::control() and type submit.
     *
     * @return void
     */
    public function testFormControlSubmit() {
        $result = $this->Form->control('Test Submit', ['type' => 'submit', 'class' => 'foobar']);
        $expected = [
            'input' => ['type' => 'submit', 'class' => 'foobar btn btn-primary', 'id' => 'test-submit', 'value' => 'Test Submit'],
        ];
        $this->assertHtml($expected, $result);
    }

    /**
     * testFormControls method
     *
     * Test correct results from Form::controls().
     *
     * @return void
     */
    public function testFormControlsLegendFieldset() {
        $this->Form->create($this->article);
        $result = $this->Form->allControls([], ['legend' => 'The Legend']);
        $expected = [
            ['fieldset' => ['class' => 'form-group']],
            '<legend',
            'The Legend',
            '/legend',
            '*/fieldset',
        ];
        $this->assertHtml($expected, $result);

        $result = $this->Form->allControls([], ['fieldset' => true, 'legend' => 'Field of Dreams']);
        $this->assertContains('<legend>Field of Dreams</legend>', $result);
        $this->assertContains('<fieldset class="form-group">', $result);

        $result = $this->Form->allControls([], ['fieldset' => false, 'legend' => false]);
        $this->assertNotContains('<legend>', $result);
        $this->assertNotContains('<fieldset>', $result);

        $result = $this->Form->allControls([], ['fieldset' => false, 'legend' => 'Hello']);
        $this->assertNotContains('<legend>', $result);
        $this->assertNotContains('<fieldset>', $result);

        $this->Form->create($this->article);
        $this->Form->request->params['prefix'] = 'admin';
        $this->Form->request->params['action'] = 'admin_edit';
        $this->Form->request->params['controller'] = 'articles';
        $result = $this->Form->allControls();
        $expected = [
            'fieldset' => ['class' => 'form-group'],
            '<legend',
            'New Article',
            '/legend',
            '*/fieldset',
        ];
        $this->assertHtml($expected, $result);

        $this->Form->create($this->article);
        $result = $this->Form->allControls([], ['fieldset' => [], 'legend' => 'The Legend']);
        $expected = [
            'fieldset' => ['class' => 'form-group'],
            '<legend',
            'The Legend',
            '/legend',
            '*/fieldset',
        ];
        $this->assertHtml($expected, $result);

        $this->Form->create($this->article);
        $result = $this->Form->allControls([], [
            'fieldset' => [
                'class' => 'some-class some-other-class',
                'disabled' => true,
                'data-param' => 'a-param'
            ],
            'legend' => 'The Legend'
        ]);
        $expected = [
            '<fieldset class="some-class some-other-class form-group" disabled="disabled" data-param="a-param"',
            '<legend',
            'The Legend',
            '/legend',
            '*/fieldset',
        ];
        $this->assertHtml($expected, $result);
    }

    /**
     * testFormControls method
     *
     * Test the controls() method.
     *
     * @return void
     */
    public function testFormControls() {
        $this->Form->create($this->article);
        $result = $this->Form->allControls();
        $expected = [
            'fieldset' => ['class' => 'form-group'],
            '<legend', 'New Article', '/legend',
            'input' => ['type' => 'hidden', 'name' => 'id', 'id' => 'id'],
            ['div' => ['class' => 'form-group']],
            '*/div',
            ['div' => ['class' => 'form-group']],
            '*/div',
            ['div' => ['class' => 'form-group']],
            '*/div',
            ['div' => ['class' => 'form-group']],
            '*/div',
            '/fieldset',
        ];
        $this->assertHtml($expected, $result);

        $result = $this->Form->allControls([
            'published' => ['type' => 'boolean']
        ]);
        $expected = [
            'fieldset' => ['class' => 'form-group'],
            '<legend', 'New Article', '/legend',
            'input' => ['type' => 'hidden', 'name' => 'id', 'id' => 'id'],
            ['div' => ['class' => 'form-group']],
            '*/div',
            ['div' => ['class' => 'form-group']],
            '*/div',
            ['div' => ['class' => 'form-group']],
            '*/div',
            ['div' => ['class' => 'form-group']],
            '*/div',
            '/fieldset',
        ];
        $this->assertHtml($expected, $result);

        $this->Form->create($this->article);
        $result = $this->Form->allControls([], ['legend' => 'Hello']);
        $expected = [
            'fieldset' => ['class' => 'form-group'],
            'legend' => [],
            'Hello',
            '/legend',
            'input' => ['type' => 'hidden', 'name' => 'id', 'id' => 'id'],
            ['div' => ['class' => 'form-group']],
            '*/div',
            ['div' => ['class' => 'form-group']],
            '*/div',
            ['div' => ['class' => 'form-group']],
            '*/div',
            ['div' => ['class' => 'form-group']],
            '*/div',
            '/fieldset'
        ];
        $this->assertHtml($expected, $result);

        $this->Form->create(false);
        $expected = [
            'fieldset' => ['class' => 'form-group'],
            ['div' => ['class' => 'form-group']],
            'label' => ['for' => 'foo', 'class'],
            'Foo',
            '/label',
            'input' => ['type' => 'text', 'name' => 'foo', 'id' => 'foo', 'class'],
            '*/div',
            '/fieldset'
        ];
        $result = $this->Form->allControls(
            ['foo' => ['type' => 'text']],
            ['legend' => false]
        );
        $this->assertHtml($expected, $result);
    }

    /**
     * testFormControlsBlacklist method
     *
     * @return void
     */
    public function testFormControlsBlacklist() {
        $this->Form->create($this->article);
        $result = $this->Form->allControls([
            'id' => false
        ]);
        $expected = [
            'fieldset' => ['class' => 'form-group'],
            '<legend', 'New Article', '/legend',
            ['div' => ['class' => 'form-group']],
            '*/div',
            ['div' => ['class' => 'form-group']],
            '*/div',
            ['div' => ['class' => 'form-group']],
            '*/div',
            ['div' => ['class' => 'form-group']],
            '*/div',
            '/fieldset',
        ];
        $this->assertHtml($expected, $result);

        $this->Form->create($this->article);
        $result = $this->Form->allControls([
            'id' => []
        ]);
        $expected = [
            'fieldset' => ['class' => 'form-group'],
            '<legend', 'New Article', '/legend',
            'input' => ['type' => 'hidden', 'name' => 'id', 'id' => 'id'],
            ['div' => ['class' => 'form-group']],
            '*/div',
            ['div' => ['class' => 'form-group']],
            '*/div',
            ['div' => ['class' => 'form-group']],
            '*/div',
            ['div' => ['class' => 'form-group']],
            '*/div',
            '/fieldset',
        ];
        $this->assertHtml($expected, $result, 'A falsey value (array) should not remove the input');
    }

    /**
     * testControlRadio method
     *
     * Test that input works with radio types.
     *
     * @return void
     */
    public function testControlRadio() {
        $result = $this->Form->control('test', [
            'type' => 'radio',
            'options' => ['A', 'B'],
        ]);
        $expected = [
            ['div' => ['class' => 'form-group']],
            '<label',
            'Test',
            '/label',
            ['input' => ['type' => 'hidden', 'name' => 'test', 'value' => '']],
            ['div' => ['class' => 'form-check']],
            ['label' => ['for' => 'test-0', 'class']],
            ['input' => ['type' => 'radio', 'name' => 'test', 'value' => '0', 'id' => 'test-0', 'class']],
            'A',
            '/label',
            '/div',
            ['div' => ['class' => 'form-check']],
            ['label' => ['for' => 'test-1', 'class']],
            ['input' => ['type' => 'radio', 'name' => 'test', 'value' => '1', 'id' => 'test-1', 'class']],
            'B',
            '/label',
            '/div',
            '/div'
        ];
        $this->assertHtml($expected, $result);

        $result = $this->Form->control('test', [
            'type' => 'radio',
            'options' => ['A', 'B'],
            'value' => '0'
        ]);
        $expected = [
            ['div' => ['class' => 'form-group']],
            '<label',
            'Test',
            '/label',
            ['input' => ['type' => 'hidden', 'name' => 'test', 'value' => '']],
            ['div' => ['class' => 'form-check']],
            ['label' => ['for' => 'test-0', 'class']],
            ['input' => ['type' => 'radio', 'checked' => 'checked', 'name' => 'test', 'value' => '0', 'id' => 'test-0',
                'class']],
            'A',
            '/label',
            '/div',
            ['div' => ['class' => 'form-check']],
            ['label' => ['for' => 'test-1', 'class']],
            ['input' => ['type' => 'radio', 'name' => 'test', 'value' => '1', 'id' => 'test-1', 'class']],
            'B',
            '/label',
            '/div',
            '/div'
        ];
        $this->assertHtml($expected, $result);

        $result = $this->Form->control('test', [
            'type' => 'radio',
            'options' => ['A', 'B'],
            'label' => false
        ]);
        $expected = [
            ['div' => ['class' => 'form-group']],
            ['input' => ['type' => 'hidden', 'name' => 'test', 'value' => '']],
            ['div' => ['class' => 'form-check']],
            ['label' => ['for' => 'test-0', 'class']],
            ['input' => ['type' => 'radio', 'name' => 'test', 'value' => '0', 'id' => 'test-0', 'class']],
            'A',
            '/label',
            '/div',
            ['div' => ['class' => 'form-check']],
            ['label' => ['for' => 'test-1', 'class']],
            ['input' => ['type' => 'radio', 'name' => 'test', 'value' => '1', 'id' => 'test-1', 'class']],
            'B',
            '/label',
            '/div',
            '/div'
        ];
        $this->assertHtml($expected, $result);
    }

    /**
     * testRadioControlInsideLabel method
     *
     * Test generating radio input inside label ala twitter bootstrap.
     *
     * @return void
     */
    public function testRadioControlInsideLabel() {
        $this->Form->templates([
            'label' => '<label{{attrs}}>{{input}}{{text}}</label>',
            'radioWrapper' => '{{label}}'
        ]);

        $result = $this->Form->radio('Model.field', ['option A', 'option B']);
        //@codingStandardsIgnoreStart
        $expected = [
            ['input' => [
                'type' => 'hidden',
                'name' => 'Model[field]',
                'value' => ''
            ]],
            ['label' => ['for' => 'model-field-0', 'class']],
            ['input' => [
                'type' => 'radio',
                'name' => 'Model[field]',
                'value' => '0',
                'id' => 'model-field-0',
                'class'
            ]],
            'option A',
            '/label',
            ['label' => ['for' => 'model-field-1', 'class']],
            ['input' => [
                'type' => 'radio',
                'name' => 'Model[field]',
                'value' => '1',
                'id' => 'model-field-1',
                'class'
            ]],
            'option B',
            '/label'
        ];
        //@codingStandardsIgnoreEnd
        $this->assertHtml($expected, $result);
    }

    /**
     * testRadioHiddenControlDisabling method
     *
     * Test disabling the hidden input for radio buttons.
     *
     * @return void
     */
    public function testRadioHiddenControlDisabling() {
        $result = $this->Form->radio('Model.1.field', ['option A'], ['hiddenField' => false]);
        $expected = [
            ['div' => ['class' => 'form-check']],
            'label' => ['for' => 'model-1-field-0', 'class'],
            'input' => ['type' => 'radio', 'name' => 'Model[1][field]', 'value' => '0', 'id' => 'model-1-field-0', 'class'],
            'option A',
            '/label',
            '/div'

        ];
        $this->assertHtml($expected, $result);
    }

    /**
     * testControlMultipleCheckboxes method
     *
     * Test control() resulting in multi select elements being generated.
     *
     * @return void
     */
    public function testControlMultipleCheckboxes() {
        $result = $this->Form->control('Model.multi_field', [
            'options' => ['first', 'second', 'third'],
            'multiple' => 'checkbox'
        ]);
        $expected = [
            ['div' => ['class' => 'form-group']],
            ['label' => ['for' => 'model-multi-field']],
            'Multi Field',
            '/label',
            'input' => ['type' => 'hidden', 'name' => 'Model[multi_field]', 'value' => ''],
            ['div' => ['class' => 'form-check']],
            ['label' => ['for' => 'model-multi-field-0', 'class']],
            ['input' => ['type' => 'checkbox', 'name' => 'Model[multi_field][]', 'value' => '0', 'id' => 'model-multi-field-0',
                'class']],
            'first',
            '/label',
            '/div',
            ['div' => ['class' => 'form-check']],
            ['label' => ['for' => 'model-multi-field-1', 'class']],
            ['input' => ['type' => 'checkbox', 'name' => 'Model[multi_field][]', 'value' => '1', 'id' => 'model-multi-field-1',
                'class']],
            'second',
            '/label',
            '/div',
            ['div' => ['class' => 'form-check']],
            ['label' => ['for' => 'model-multi-field-2', 'class']],
            ['input' => ['type' => 'checkbox', 'name' => 'Model[multi_field][]', 'value' => '2', 'id' => 'model-multi-field-2',
                'class']],
            'third',
            '/label',
            '/div',
            '/div'
        ];
        $this->assertHtml($expected, $result);

        $result = $this->Form->control('Model.multi_field', [
            'options' => ['a' => 'first', 'b' => 'second', 'c' => 'third'],
            'multiple' => 'checkbox'
        ]);
        $expected = [
            ['div' => ['class' => 'form-group']],
            ['label' => ['for' => 'model-multi-field']],
            'Multi Field',
            '/label',
            'input' => ['type' => 'hidden', 'name' => 'Model[multi_field]', 'value' => ''],
            ['div' => ['class' => 'form-check']],
            ['label' => ['for' => 'model-multi-field-a', 'class']],
            ['input' => ['type' => 'checkbox', 'name' => 'Model[multi_field][]', 'value' => 'a', 'id' => 'model-multi-field-a',
                'class']],
            'first',
            '/label',
            '/div',
            ['div' => ['class' => 'form-check']],
            ['label' => ['for' => 'model-multi-field-b', 'class']],
            ['input' => ['type' => 'checkbox', 'name' => 'Model[multi_field][]', 'value' => 'b', 'id' => 'model-multi-field-b',
                'class']],
            'second',
            '/label',
            '/div',
            ['div' => ['class' => 'form-check']],
            ['label' => ['for' => 'model-multi-field-c', 'class']],
            ['input' => ['type' => 'checkbox', 'name' => 'Model[multi_field][]', 'value' => 'c', 'id' => 'model-multi-field-c',
                'class']],
            'third',
            '/label',
            '/div',
            '/div'
        ];
        $this->assertHtml($expected, $result);
    }

    /**
     * testControlMultiCheckbox method
     *
     * Test that control() works with multicheckbox.
     *
     * @return void
     */
    public function testControlMultiCheckbox() {
        $result = $this->Form->control('category', [
            'type' => 'multicheckbox',
            'options' => ['1', '2'],
        ]);
        $expected = [
            ['div' => ['class' => 'form-group']],
            '<label',
            'Category',
            '/label',
            'input' => ['type' => 'hidden', 'name' => 'category', 'value' => ''],
            ['div' => ['class' => 'form-check']],
            ['label' => ['for' => 'category-0', 'class']],
            ['input' => ['type' => 'checkbox', 'name' => 'category[]', 'value' => '0', 'id' => 'category-0', 'class']],
            '1',
            '/label',
            '/div',
            ['div' => ['class' => 'form-check']],
            ['label' => ['for' => 'category-1', 'class']],
            ['input' => ['type' => 'checkbox', 'name' => 'category[]', 'value' => '1', 'id' => 'category-1', 'class']],
            '2',
            '/label',
            '/div',
            '/div',
        ];
        $this->assertHtml($expected, $result);
    }

    /**
     * testDateTimeLabelIdMatchesFirstControl method
     *
     * When changing the date format, the label should always focus the first select box when
     * clicked.
     *
     * @return void
     */
    public function testDateTimeLabelIdMatchesFirstControl() {
        $this->assertTrue(true);
        return;
        $result = $this->Form->control('Model.date', ['type' => 'date']);
        $this->assertContains('<label>Date</label>', $result);

        $result = $this->Form->control('Model.date', ['type' => 'date', 'dateFormat' => 'DMY']);
        $this->assertContains('<label>Date</label>', $result);

        $result = $this->Form->control('Model.date', ['type' => 'date', 'dateFormat' => 'YMD']);
        $this->assertContains('<label>Date</label>', $result);
    }

    /**
     * testControlLabelFalse method
     *
     * Test the label option being set to false.
     *
     * @return void
     */
    public function testControlLabelFalse() {
        $this->Form->create($this->article);
        $result = $this->Form->control('title', ['label' => false]);
        $expected = [
            'div' => ['class' => 'form-group'],
            'input' => ['type' => 'text', 'required' => 'required', 'id' => 'title', 'name' => 'title',
                'class' => 'form-control'],
            '/div'
        ];
        $this->assertHtml($expected, $result);
    }

    /**
     * testForMagicControlNonExistingNorValidated method
     *
     * @return void
     */
    public function testForMagicControlNonExistingNorValidated() {
        $this->Form->create($this->article);
        $this->Form->templates(['inputContainer' => '{{content}}']);
        $result = $this->Form->control('non_existing_nor_validated');
        $expected = [
            'label' => ['for' => 'non-existing-nor-validated', 'class'],
            'Non Existing Nor Validated',
            '/label',
            'input' => [
                'type' => 'text', 'name' => 'non_existing_nor_validated',
                'id' => 'non-existing-nor-validated', 'class' => 'form-control'
            ]
        ];
        $this->assertHtml($expected, $result);

        $result = $this->Form->control('non_existing_nor_validated', [
            'val' => 'my value'
        ]);
        $expected = [
            'label' => ['for' => 'non-existing-nor-validated', 'class'],
            'Non Existing Nor Validated',
            '/label',
            'input' => [
                'type' => 'text', 'name' => 'non_existing_nor_validated',
                'value' => 'my value', 'id' => 'non-existing-nor-validated',
                'class' => 'form-control'
            ]
        ];
        $this->assertHtml($expected, $result);

        $this->Form->request->data = ['non_existing_nor_validated' => 'CakePHP magic'];
        $result = $this->Form->control('non_existing_nor_validated');
        $expected = [
            'label' => ['for' => 'non-existing-nor-validated', 'class'],
            'Non Existing Nor Validated',
            '/label',
            'input' => [
                'type' => 'text', 'name' => 'non_existing_nor_validated',
                'value' => 'CakePHP magic', 'id' => 'non-existing-nor-validated',
                'class' => 'form-control'
            ]
        ];
        $this->assertHtml($expected, $result);
    }

    /**
     * testFormMagicControlLabel method
     *
     * @return void
     */
    public function testFormMagicControlLabel() {
        TableRegistry::get('Contacts', [
            'className' => 'Cake\Test\TestCase\View\Helper\ContactsTable'
        ]);
        $this->Form->create([], ['context' => ['table' => 'Contacts']]);
        $this->Form->templates(['inputContainer' => '{{content}}']);

        $result = $this->Form->control('Contacts.name', ['label' => 'My label']);
        $expected = [
            'label' => ['for' => 'contacts-name', 'class'],
            'My label',
            '/label',
            'input' => [
                'type' => 'text', 'name' => 'Contacts[name]',
                'id' => 'contacts-name', 'maxlength' => '255',
                'class' => 'form-control'
            ]
        ];
        $this->assertHtml($expected, $result);

        $result = $this->Form->control('name', [
            'label' => ['class' => 'mandatory']
        ]);
        $expected = [
            'label' => ['for' => 'name', 'class' => 'mandatory col-form-label'],
            'Name',
            '/label',
            'input' => [
                'type' => 'text', 'name' => 'name',
                'id' => 'name', 'maxlength' => '255',
                'class' => 'form-control'
            ]
        ];
        $this->assertHtml($expected, $result);

        $result = $this->Form->control('name', [
            'div' => false,
            'label' => ['class' => 'mandatory', 'text' => 'My label']
        ]);
        $expected = [
            'label' => ['for' => 'name', 'class' => 'mandatory col-form-label'],
            'My label',
            '/label',
            'input' => [
                'type' => 'text', 'name' => 'name',
                'id' => 'name', 'maxlength' => '255',
                'class' => 'form-control'
            ]
        ];
        $this->assertHtml($expected, $result);

        $result = $this->Form->control('Contact.name', [
            'div' => false, 'id' => 'my_id', 'label' => ['for' => 'my_id']
        ]);
        $expected = [
            'label' => ['for' => 'my_id', 'class'],
            'Name',
            '/label',
            'input' => [
                'type' => 'text', 'name' => 'Contact[name]',
                'id' => 'my_id', 'maxlength' => '255',
                'class' => 'form-control'
            ]
        ];
        $this->assertHtml($expected, $result);

        $result = $this->Form->control('1.id');
        $expected = ['input' => [
            'type' => 'hidden', 'name' => '1[id]',
            'id' => '1-id'
        ]];
        $this->assertHtml($expected, $result);

        $result = $this->Form->control("1.name");
        $expected = [
            'label' => ['for' => '1-name', 'class'],
            'Name',
            '/label',
            'input' => [
                'type' => 'text', 'name' => '1[name]',
                'id' => '1-name', 'maxlength' => '255',
                'class' => 'form-control'
            ]
        ];
        $this->assertHtml($expected, $result);
    }

    /**
     * testHtml5Controls method
     *
     * Test that some html5 inputs + FormHelper::__call() work.
     *
     * @return void
     */
    public function testHtml5Controls() {
        $result = $this->Form->email('User.email');
        $expected = [
            'input' => ['type' => 'email', 'name' => 'User[email]', 'class' => 'form-control']
        ];
        $this->assertHtml($expected, $result);

        $result = $this->Form->search('User.query');
        $expected = [
            'input' => ['type' => 'search', 'name' => 'User[query]', 'class' => 'form-control']
        ];
        $this->assertHtml($expected, $result);

        $result = $this->Form->search('User.query', ['value' => 'test']);
        $expected = [
            'input' => ['type' => 'search', 'name' => 'User[query]', 'value' => 'test', 'class' => 'form-control']
        ];
        $this->assertHtml($expected, $result);

        $result = $this->Form->search('User.query', ['type' => 'text', 'value' => 'test']);
        $expected = [
            'input' => ['type' => 'text', 'name' => 'User[query]', 'value' => 'test', 'class' => 'form-control']
        ];
        $this->assertHtml($expected, $result);
    }

    /**
     * testHtml5ControlWithControl method
     *
     * Test accessing html5 inputs through control().
     *
     * @return void
     */
    public function testHtml5ControlWithControl() {
        $this->Form->create();
        $this->Form->templates(['inputContainer' => '{{content}}']);
        $result = $this->Form->control('website', [
            'type' => 'url',
            'val' => 'http://domain.tld',
            'label' => false
        ]);
        $expected = [
            'input' => ['type' => 'url', 'name' => 'website', 'id' => 'website', 'value' => 'http://domain.tld',
                'class' => 'form-control']
        ];
        $this->assertHtml($expected, $result);
    }

    /**
     * testControlsNotNested method
     *
     * Tests that it is possible to put inputs outside of the label.
     *
     * @return void
     */
    public function testControlsNotNested() {
        $this->Form->templates([
            'nestingLabel' => '{{hidden}}{{input}}<label{{attrs}}>{{text}}</label>',
            'formGroup' => '{{input}}{{label}}',
        ]);
        $result = $this->Form->control('foo', ['type' => 'checkbox']);
        $expected = [
            'div' => ['class' => 'form-check'],
            ['input' => ['type' => 'hidden', 'name' => 'foo', 'value' => '0']],
            ['input' => ['type' => 'checkbox', 'name' => 'foo', 'id' => 'foo', 'value' => '1',
                'class' => 'form-check-input']],
            'label' => ['for' => 'foo', 'class'],
            'Foo',
            '/label',
            '/div'
        ];
        $this->assertHtml($expected, $result);

        $result = $this->Form->control('foo', ['type' => 'checkbox', 'label' => false]);
        $expected = [
            'div' => ['class' => 'form-check'],
            ['input' => ['type' => 'hidden', 'name' => 'foo', 'value' => '0']],
            ['input' => ['type' => 'checkbox', 'name' => 'foo', 'id' => 'foo', 'value' => '1',
                'class' => 'form-check-input']],
            '/div'
        ];
        $this->assertHtml($expected, $result);

        $result = $this->Form->control('confirm', [
            'type' => 'radio',
            'options' => ['Y' => 'Yes', 'N' => 'No']
        ]);
        $expected = [
            'div' => ['class' => 'form-group'],
            ['input' => ['type' => 'hidden', 'name' => 'confirm', 'value' => '']],
            ['div' => ['class' => 'form-check']],
            ['input' => ['type' => 'radio', 'name' => 'confirm', 'id' => 'confirm-y', 'value' => 'Y',
                'class' => 'form-check-input']],
            ['label' => ['for' => 'confirm-y', 'class']],
            'Yes',
            '/label',
            '/div',
            ['div' => ['class' => 'form-check']],
            ['input' => ['type' => 'radio', 'name' => 'confirm', 'id' => 'confirm-n', 'value' => 'N',
                'class' => 'form-check-input']],
            ['label' => ['for' => 'confirm-n', 'class']],
            'No',
            '/label',
            '/div',
            '<label',
            'Confirm',
            '/label',
            '/div'
        ];
        $this->assertHtml($expected, $result);

        $result = $this->Form->select('category', ['1', '2'], [
            'multiple' => 'checkbox',
            'name' => 'fish',
        ]);
        $expected = [
            'input' => ['type' => 'hidden', 'name' => 'fish', 'value' => ''],
            ['div' => ['class' => 'form-check']],
            ['input' => ['type' => 'checkbox', 'name' => 'fish[]', 'value' => '0', 'id' => 'fish-0', 'class']],
            ['label' => ['for' => 'fish-0', 'class']],
            '1',
            '/label',
            '/div',
            ['div' => ['class' => 'form-check']],
            ['input' => ['type' => 'checkbox', 'name' => 'fish[]', 'value' => '1', 'id' => 'fish-1', 'class']],
            ['label' => ['for' => 'fish-1', 'class']],
            '2',
            '/label',
            '/div'
        ];
        $this->assertHtml($expected, $result);
    }

    /**
     * testControlContainerTemplates method
     *
     * Test that *Container templates are used by input.
     *
     * @return void
     */
    public function testControlContainerTemplates() {
        $this->Form->templates([
            'checkboxContainer' => '<div class="check">{{content}}</div>',
            'radioContainer' => '<div class="rad">{{content}}</div>',
            'radioContainerError' => '<div class="rad err">{{content}}</div>',
            'datetimeContainer' => '<div class="dt">{{content}}</div>',
        ]);

        $this->article['errors'] = [
            'Article' => ['published' => 'error message']
        ];
        $this->Form->create($this->article);

        $result = $this->Form->control('accept', [
            'type' => 'checkbox',
        ]);
        $expected = [
            'div' => ['class' => 'check'],
            ['input' => ['type' => 'hidden', 'name' => 'accept', 'value' => 0]],
            'label' => ['for' => 'accept', 'class'],
            ['input' => ['id' => 'accept', 'type' => 'checkbox', 'name' => 'accept', 'value' => 1, 'class']],
            'Accept',
            '/label',
            '/div'
        ];
        $this->assertHtml($expected, $result);

        $result = $this->Form->control('accept', [
            'type' => 'radio',
            'options' => ['Y', 'N']
        ]);
        $this->assertContains('<div class="rad">', $result);

        $result = $this->Form->control('Article.published', [
            'type' => 'radio',
            'options' => ['Y', 'N']
        ]);
        $this->assertContains('<div class="rad err">', $result);

        $result = $this->Form->control('Article.created', [
            'type' => 'datetime'
        ]);
        $this->assertContains('<div class="dt">', $result);
    }

    /**
     * Test sources values defaults handling
     *
     * @return void
     */
    public function testFormValueSourcesDefaults()
    {
        $this->Form->request->query['password'] = 'open Sesame';
        $this->Form->create();

        $result = $this->Form->password('password');
        $expected = ['input' => ['type' => 'password', 'name' => 'password', 'class' => 'form-control']];
        $this->assertHtml($expected, $result);

        $result = $this->Form->password('password', ['default' => 'helloworld']);
        $expected = ['input' => ['type' => 'password', 'name' => 'password', 'value' => 'helloworld',
            'class' => 'form-control']];
        $this->assertHtml($expected, $result);

        $this->Form->setValueSources('query');
        $result = $this->Form->password('password', ['default' => 'helloworld']);
        $expected = ['input' => ['type' => 'password', 'name' => 'password', 'value' => 'open Sesame',
            'class' => 'form-control']];
        $this->assertHtml($expected, $result);

        $this->Form->setValueSources('data');
        $result = $this->Form->password('password', ['default' => 'helloworld']);
        $expected = ['input' => ['type' => 'password', 'name' => 'password', 'value' => 'helloworld',
            'class' => 'form-control']];
        $this->assertHtml($expected, $result);
    }
}
