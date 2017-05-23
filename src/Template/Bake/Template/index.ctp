<%
/**
* CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
* Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
*
* Licensed under The MIT License
* For full copyright and license information, please see the LICENSE.txt
* Redistributions of files must retain the above copyright notice.
*
* @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
* @link          http://cakephp.org CakePHP(tm) Project
* @since         0.1.0
* @license       http://www.opensource.org/licenses/mit-license.php MIT License
*/

$bakeEntities = [ $modelClass ];
foreach ($associations as $type => $data) {
  foreach ($data as $alias => $details){
    if (!empty($details['navLink']) && !in_array($details['controller'], $bakeEntities))
      $bakeEntities[] = $details['controller'];
  }
}
%>
<?php
/**
 * @var \<%= $namespace %>\View\AppView $this
 * @var \Cake\ORM\ResultSet $<%= $pluralVar %>
 */

$this->set('bakeEntities', <%= var_export($bakeEntities) %>);
?>
<%
use Cake\Utility\Inflector;

$fields = collection($fields)
->filter(function($field) use ($schema) {
return !in_array($schema->columnType($field), ['binary', 'text']);
});

if (isset($modelObject) && $modelObject->behaviors()->has('Tree')) {
$fields = $fields->reject(function ($field) {
return $field === 'lft' || $field === 'rght';
});
}

if (!empty($indexColumns)) {
$fields = $fields->take($indexColumns);
}

%>
<div class="container" id="<%= Inflector::underscore($pluralVar.Inflector::humanize($action)) %>">
    <?= $this->Html->button(
        '<i class="fa fa-plus-circle fa-lg"></i> ' . __('New <%= $singularHumanName %>'),
        ['action' => 'add'],
        ['class' => ['float-right'], 'escape' => false, 'size' => 'small']); ?>
    <h3 class="mb-4"><?= __('<%= $pluralHumanName %>') ?></h3>
    <div class="table-responsive">
        <table class="table table-striped table-hover">
            <thead>
            <tr>
<% foreach ($fields as $field): %>
                <th><?= $this->Paginator->sort('<%= $field %>') ?></th>
<% endforeach; %>
                <th><?= __('Actions') ?></th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($<%= $pluralVar %> as $<%= $singularVar %>): ?>
            <tr>
<% foreach ($fields as $field) {
$isKey = false;
if (!empty($associations['BelongsTo'])) {
foreach ($associations['BelongsTo'] as $alias => $details) {
if ($field === $details['foreignKey']) {
$isKey = true;
%>
                <td><?= $<%= $singularVar %>->has('<%= $details['property'] %>') ? $this->Html->link($<%= $singularVar %>-><%= $details['property'] %>-><%= $details['displayField'] %>, ['controller' => '<%= $details['controller'] %>', 'action' => 'view', $<%= $singularVar %>-><%= $details['property'] %>-><%= $details['primaryKey'][0] %>]) : '' ?></td>
<%
break;
}
}
}
if ($isKey !== true) {
if (!in_array($schema->columnType($field), ['integer', 'biginteger', 'decimal', 'float'])) {
%>
                <td><?= h($<%= $singularVar %>-><%= $field %>) ?></td>
<%
} else {
%>
                <td><?= $this->Number->format($<%= $singularVar %>-><%= $field %>) ?></td>
<%
}
}
}
$pk = '$' . $singularVar . '->' . $primaryKey[0];
%>
                <td class="text-right">
                    <?= $this->Html->link(null, ['action' => 'view', <%= $pk %>], ['class' => 'btn btn-primary fa fa-eye p-1 mx-1']) ?>
                            <?= $this->Html->link(null, ['action' => 'edit', <%= $pk %>], ['class' => 'btn btn-primary fa fa-pencil p-1 mx-1']) ?>
                            <?= $this->Form->postLink(null, ['action' => 'delete', <%= $pk %>], [
                    'confirm' => __('Are you sure you want to delete # {0}?', <%= $pk %>),
                    'class' => 'btn btn-primary fa fa-trash p-1 mx-1'
                    ]) ?>
                </td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <nav aria-label="Page navigation">
        <ul class="pagination pagination-sm">
            <?= $this->Paginator->prev('< ' . __('previous')) ?>
            <?= $this->Paginator->numbers() ?>
            <?= $this->Paginator->next(__('next') . ' >') ?>
        </ul>
        <small class="text-muted"><?= $this->Paginator->counter(['format' => __('Page {{page}} of {{pages}}, showing {{current}} record(s) out of {{count}} total')]) ?></small>
    </nav>
</div>
