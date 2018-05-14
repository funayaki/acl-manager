<?php
/**
 * @var \App\View\AppView $this
 */
?>
<?= $this->Html->css('/acl_manager/css/acl.css'); ?>

<?= $this->Flash->render('plugin_acl'); ?>

    <h1><?= __d('acl', 'ACL plugin'); ?></h1>

<?php

if (!isset($no_acl_links)) {
    $selected = isset($selected) ? $selected : $this->request->getParam('controller');

    $links = array();
    $links[] = $this->Html->link(__d('acl', 'Permissions'), '/admin/acl_manager/aros/index', array('class' => ($selected == 'aros') ? 'selected' : null));
    $links[] = $this->Html->link(__d('acl', 'Actions'), '/admin/acl_manager/acos/index', array('class' => ($selected == 'acos') ? 'selected' : null));

    echo $this->Html->nestedList($links, array('class' => 'acl_links'));
}
?>