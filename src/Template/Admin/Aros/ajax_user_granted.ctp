<?php
echo '<span id="right__' . $user_id . '_' . implode('_', explode('/', $action)) . '">';

if (isset($acl_error)) {
    if (isset($acl_error_aro)) {
        $title = __d('acl', 'The user node does not exist in the ARO table');
    } elseif (isset($acl_error_aco)) {
        $title = __d('acl', 'The ACO node is probably missing. Please try to rebuild the ACOs first.');
    } else {
        $title = __d('acl', 'The ARO or the ACO node is probably missing. Please try to rebuild the ACOs first.');
    }

    echo $this->Html->image('/acl_manager/img/design/important16.png', array('class' => 'pointer', 'alt' => $title, 'title' => $title));
} else {
    echo $this->Html->image('/acl_manager/img/design/tick.png', array('class' => 'pointer'));
}

echo '</span>';
