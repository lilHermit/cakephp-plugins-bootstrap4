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

if (!function_exists('getColumnType')) {
    function getColumnType($schema, $field) {
        if (method_exists($schema, 'getColumnType')) {
            return $schema->getColumnType($field);
        } else {
            return $schema->column($field);
        }
    }
}%>
<?php
/**
 * @var \<%= $namespace %>\View\AppView $this
 * @var \<%= $namespace %>\Model\Entity\<%= $this->_entityName($modelClass) %> $<%= $singularVar %>
 */

$this->set('bakeEntities', <%= var_export($bakeEntities) %>);
?>
<%
use Cake\Utility\Inflector;

$associations += ['BelongsTo' => [], 'HasOne' => [], 'HasMany' => [], 'BelongsToMany' => []];
$immediateAssociations = $associations['BelongsTo'];
$associationFields = collection($fields)
    ->map(function($field) use ($immediateAssociations) {
        foreach ($immediateAssociations as $alias => $details) {
            if ($field === $details['foreignKey']) {
                return [$field => $details];
            }
        }
    })
    ->filter()
    ->reduce(function($fields, $value) {
        return $fields + $value;
    }, []);

$groupedFields = collection($fields)
    ->filter(function($field) use ($schema) {
        return getColumnType($schema, $field) !== 'binary';
    })
    ->groupBy(function($field) use ($schema, $associationFields) {
        $type = getColumnType($schema, $field);
        if (isset($associationFields[$field])) {
            return 'string';
        }
        if (in_array($type, ['integer', 'float', 'decimal', 'biginteger'])) {
            return 'number';
        }
        if (in_array($type, ['date', 'time', 'datetime', 'timestamp'])) {
            return 'date';
        }
        return in_array($type, ['text', 'boolean']) ? $type : 'string';
    })
    ->toArray();

$groupedFields += ['number' => [], 'string' => [], 'boolean' => [], 'date' => [], 'text' => []];
$pk = "\$$singularVar->{$primaryKey[0]}";
%>
<div class="container"  id="<%= Inflector::underscore($singularVar.Inflector::humanize($action)) %>">
    <div class="float-right">
        <?= $this->Html->button(
        '<i class="fa fa-pencil fa-lg pr-3"></i> ' . __('Edit'),
        ['action' => 'edit', <%= $pk %>],
        ['class' => ['mr-3'], 'escape' => false, 'size' => 'sm']); ?>

        <?= $this->Form->postLink('<i class="fa fa-trash fa-lg pr-3"></i>'. __('Delete'), ['action' => 'delete', <%= $pk %>], [
        'escape' => false,
        'class' => 'btn btn-primary btn-sm',
        'confirm' => __('Are you sure you want to delete # {0}?', <%= $pk %>)]) ?>
    </div>

    <h3 class="mb-3"><?= h($<%= $singularVar %>-><%= $displayField %>) ?></h3>
    <dl class="row">
<% if ($groupedFields['string']) : %>
<% foreach ($groupedFields['string'] as $field) : %>
<% if (isset($associationFields[$field])) :
            $details = $associationFields[$field];
%>
        <dt class="col-sm-3"><?= __('<%= Inflector::humanize($details['property']) %>') ?></dt>
        <dd class="col-sm-9"><?= $<%= $singularVar %>->has('<%= $details['property'] %>') ? $this->Html->link($<%= $singularVar %>-><%= $details['property'] %>-><%= $details['displayField'] %>, ['controller' => '<%= $details['controller'] %>', 'action' => 'view', $<%= $singularVar %>-><%= $details['property'] %>-><%= $details['primaryKey'][0] %>]) : '' ?></dd>
<% else : %>
        <dt class="col-sm-3"><?= __('<%= Inflector::humanize($field) %>') ?></dt>
        <dd class="col-sm-9"><?= h($<%= $singularVar %>-><%= $field %>) ?></dd>
<% endif; %>
<% endforeach; %>
<% endif; %>
<% if ($associations['HasOne']) : %>
    <%- foreach ($associations['HasOne'] as $alias => $details) : %>
        <dt class="col-sm-3"><?= __('<%= Inflector::humanize(Inflector::singularize(Inflector::underscore($alias))) %>') ?></dt>
        <dd class="col-sm-9"><?= $<%= $singularVar %>->has('<%= $details['property'] %>') ? $this->Html->link($<%= $singularVar %>-><%= $details['property'] %>-><%= $details['displayField'] %>, ['controller' => '<%= $details['controller'] %>', 'action' => 'view', $<%= $singularVar %>-><%= $details['property'] %>-><%= $details['primaryKey'][0] %>]) : '' ?></dd>
    <%- endforeach; %>
<% endif; %>
<% if ($groupedFields['number']) : %>
<% foreach ($groupedFields['number'] as $field) : %>
        <dt class="col-sm-3"><?= __('<%= Inflector::humanize($field) %>') ?></dt>
        <dd class="col-sm-9"><?= $this->Number->format($<%= $singularVar %>-><%= $field %>) ?></dd>
<% endforeach; %>
<% endif; %>
<% if ($groupedFields['date']) : %>
<% foreach ($groupedFields['date'] as $field) : %>
        <dt class="col-sm-3"><%= "<%= __('" . Inflector::humanize($field) . "') %>" %></dt>
        <dd class="col-sm-9"><?= h($<%= $singularVar %>-><%= $field %>) ?></dd>
<% endforeach; %>
<% endif; %>
<% if ($groupedFields['boolean']) : %>
<% foreach ($groupedFields['boolean'] as $field) : %>
        <dt class="col-sm-3"><?= __('<%= Inflector::humanize($field) %>') ?></dt>
        <dd class="col-sm-9"><?= $<%= $singularVar %>-><%= $field %> ? __('Yes') : __('No'); ?></dd>
<% endforeach; %>
<% endif; %>
<% if ($groupedFields['text']) : %>
<% foreach ($groupedFields['text'] as $field) : %>

    <dt class="col-sm-3"><?= __('<%= Inflector::humanize($field) %>') ?></dt>
    <dd class="col-sm-9"><?= $this->Text->autoParagraph(h($<%= $singularVar %>-><%= $field %>)); ?></dd>
<% endforeach; %>
<% endif; %>
    </dl>
<%
$relations = $associations['HasMany'] + $associations['BelongsToMany'];
foreach ($relations as $alias => $details):
    $otherSingularVar = Inflector::variable($alias);
    $otherPluralHumanName = Inflector::humanize(Inflector::underscore($details['controller']));
        %>
    <h4><?= __('Related <%= $otherPluralHumanName %>') ?></h4>
    <?php if (!empty($<%= $singularVar %>-><%= $details['property'] %>)): ?>
    <div class=" table-responsive">
        <table class="table table-sm table-striped table-hover">
            <thead>
                <tr>
    <% foreach ($details['fields'] as $field): %>
                    <th scope="col"><?= __('<%= Inflector::humanize($field) %>') ?></th>
    <% endforeach; %>
                    <th scope="col" class="actions"><?= __('Actions') ?></th>
                </tr>
            </thead>
            <?php foreach ($<%= $singularVar %>-><%= $details['property'] %> as $<%= $otherSingularVar %>): ?>
            <tr>
    <%- foreach ($details['fields'] as $field): %>
                <td><?= h($<%= $otherSingularVar %>-><%= $field %>) ?></td>
    <%- endforeach; %>
    <%- $otherPk = "\${$otherSingularVar}->{$details['primaryKey'][0]}"; %>
                <td class="text-right">
                    <?= $this->Html->link(null, ['controller' => '<%= $details['controller'] %>', 'action' => 'view', <%= $otherPk %>], ['class' => 'btn btn-primary fa fa-eye p-1 mx-1']) ?>
                            <?= $this->Html->link(null, ['controller' => '<%= $details['controller'] %>', 'action' => 'edit', <%= $otherPk %>], ['class' => 'btn btn-primary fa fa-pencil p-1 mx-1']) ?>
                            <?= $this->Form->postLink(null, ['controller' => '<%= $details['controller'] %>', 'action' => 'delete', <%= $otherPk %>], [
                    'confirm' => __('Are you sure you want to delete # {0}?', <%= $otherPk %>),
                    'class' => 'btn btn-primary fa fa-trash p-1 mx-1'
                    ]) ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
        <?php endif; ?>
    </div>
<% endforeach; %>

</div>