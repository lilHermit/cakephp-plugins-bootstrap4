Validation
##########

If you use HTML5 datetime elements then the standard dateTime Validator will fail. Therefore you need to register an additional provider. To use the Bootstrap-4 validation rule you need the following in relevant Table::

    namespace App\Model\Table;

    use Cake\ORM\Table;
    use Cake\Validation\Validator;
    use Cake\Validation\RulesProvider;

    class MyTable extends Table {

        public function validationDefault(Validator $validator) {

            // Register the provider with the correct Validation class
            $validator->provider('bootstrap4', new RulesProvider('\lilHermit\Bootstrap4\Validation\Validation'));

            // User the custom provider for the `expires` field
            $validator
                ->add('expires',  'custom', [
                    'rule' => 'dateTime',
                    'provider' => 'bootstrap4',
            ]);
        }
    }




.. todolist

    Need to check this below how does it relate to html5Render and validation too?

    By default the plugin automatically parses the html5 date format of `2014-12-31T23:59`
    to disable this add the following to your app config array::

        return [

             // ... other config

                'lilHermit-plugin-bootstrap4' => [
                     'skip-html5-datetime-type' => true
                ]
            ];