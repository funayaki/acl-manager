<?php
echo '<span id="right__' . $role_id . '_' . implode('_', explode('/', $action)) . '">';

if (isset($acl_error)) {
    $title = isset($acl_error_aro) ? __d('acl', 'The role node does not exist in the ARO table') : __d('acl', 'The ACO node is probably missing. Please try to rebuild the ACOs first.');
    echo $this->Html->image('/acl_manager/img/design/important16.png', array('class' => 'pointer', 'alt' => $title, 'title' => $title));
} else {
    echo $this->Html->image('/acl_manager/img/design/tick.png', array('class' => 'pointer', 'alt' => 'granted'));
}

echo '</span>';
