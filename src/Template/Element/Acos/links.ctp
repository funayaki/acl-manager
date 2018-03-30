<?php
/**
 * @var \App\View\AppView $this
 */
?>
<div id="acos_link" class="acl_links">
    <?php
    $selected = isset($selected) ? $selected : $this->request->getParam('action');

    $links = array();
    $links[] = $this->Html->link(__d('acl', 'Synchronize actions ACOs'), '/admin/acl_manager/acos/synchronize', array(array('confirm' => __d('acl', 'are you sure ?')), 'class' => ($selected == 'admin_synchronize') ? 'selected' : null));
    $links[] = $this->Html->link(__d('acl', 'Clear actions ACOs'), '/admin/acl_manager/acos/empty_acos', array(array('confirm' => __d('acl', 'are you sure ?')), 'class' => ($selected == 'admin_empty_acos') ? 'selected' : null));
    $links[] = $this->Html->link(__d('acl', 'Build actions ACOs'), '/admin/acl_manager/acos/build_acl', array('class' => ($selected == 'admin_build_acl') ? 'selected' : null));
    $links[] = $this->Html->link(__d('acl', 'Prune actions ACOs'), '/admin/acl_manager/acos/prune_acos', array(array('confirm' => __d('acl', 'are you sure ?')), 'class' => ($selected == 'admin_prune_acos') ? 'selected' : null));


    echo $this->Html->nestedList($links, array('class' => 'acl_links'));
    ?>
</div>