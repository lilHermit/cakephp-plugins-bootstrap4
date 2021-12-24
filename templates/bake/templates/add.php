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
 * @var \<%= $namespace %>\Model\Entity\<%= $this->_entityName($modelClass) %> $<%= $singularVar %>
 */

$this->set('bakeEntities', <%= var_export($bakeEntities) %>);
?>
<%= $this->element('form');
