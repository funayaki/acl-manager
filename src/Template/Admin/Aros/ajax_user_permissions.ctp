<?php
/**
 * @var \App\View\AppView $this
 */

echo $this->Html->script('/acl_manager/js/jquery');
echo $this->Html->script('/acl_manager/js/acl_plugin');
?>
<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
    </ul>
</nav>
<div class="aros ajax_user_permissions large-9 medium-8 columns content">

    <?php
    echo $this->element('design/header');
    ?>

    <?php
    echo $this->element('Aros/links');
    ?>

    <h1><?php echo __d('acl', 'User') . ' : ' . $user->$user_display_field; ?></h1>

    <h2><?php echo __d('acl', 'Role'); ?></h2>

    <table border="0">
        <tr>
            <?php
            foreach ($roles as $role) {
                echo '<td>';

                echo $role->$role_display_field;
                if ($role->$role_pk_name == $user->$role_fk_name) {
                    echo $this->Html->image('/acl_manager/img/design/tick.png');
                } else {
                    $title = __d('acl', 'Update the user role');
                    echo $this->Html->link($this->Html->image('/acl_manager/img/design/tick_disabled.png'), array('action' => 'updateUserRole', $user->$user_pk_name, $role->$role_pk_name), array('title' => $title, 'alt' => $title, 'escape' => false));
                }

                echo '</td>';
            }
            ?>
        </tr>
    </table>

    <h2><?php echo __d('acl', 'Permissions'); ?></h2>

    <?php
    if ($user_has_specific_permissions) {
        echo '<div class="separator"></div>';
        echo $this->Html->image('/acl_manager/img/design/bulb24.png') . __d('acl', 'This user has specific permissions');
        echo ' (';
        echo $this->Html->link($this->Html->image('/acl_manager/img/design/cross2.png', array('style' => 'vertical-align:middle;')) . ' ' . __d('acl', 'Clear'), '/admin/acl_manager/aros/clear_user_specific_permissions/' . $user->$user_pk_name, array('confirm' => __d('acl', 'Are you sure you want to clear the permissions specific to this user ?'), 'escape' => false));
        echo ')';
        echo '<div class="separator"></div>';
    }
    ?>

    <table border="0" cellpadding="5" cellspacing="2">
        <?php

        $column_count = 2;

        $headers = array(__d('acl', 'action'), __d('acl', 'authorization'));

        echo $this->Html->tableHeaders($headers);
        ?>

        <?php
        $js_init_done = array();
        $previousAction = '';
        foreach ($actions as $action) {
            $aliases = explode('/', $action);
            $method = array_pop($aliases);
            $controller = array_pop($aliases);

            $previousAlias = explode('/', $previousAction);
            $previousMethod = array_pop($previousAlias);
            $previousController = array_pop($previousAlias);

            if ($previousAlias != $aliases) {
                echo '<tr class="title"><td colspan="' . $column_count . '">' . implode('/', $aliases) . '</td></tr>';
            }

            echo '<tr>';

            echo '<td>' . $controller . '->' . $method . '</td>';

            $spanId = 'right__' . $user->$user_pk_name . '_' . implode('_', explode('/', $action));

            echo '<td>';
            echo '<span id="' . $spanId . '">';

            /*
            * The right of the action for the role must still be loaded
            */
            echo $this->Html->image('/acl_manager/img/ajax/waiting16.gif', array('title' => __d('acl', 'loading')));

            if (!in_array($controller . '_' . $user->$user_pk_name, $js_init_done)) {
                $js_init_done[] = $controller . '_' . $user->$user_pk_name;
                $this->Js->buffer('init_register_user_controller_toggle_right("' . $this->Url->build('/', true) . '", "' . $user->$user_pk_name . '", "", "' . $controller . '", "' . __d('acl', 'The ACO node is probably missing. Please try to rebuild the ACOs first.') . '");');
            }

            echo '</span>';

            echo ' ';
            echo $this->Html->image('/acl_manager/img/ajax/waiting16.gif', array('id' => $spanId . '_spinner', 'style' => 'display:none;'));

            echo '</td>';
            echo '</tr>';
            $previousAction = $action;
        }
        ?>
    </table>
    <?php
    echo $this->Html->image('/acl_manager/img/design/tick.png') . ' ' . __d('acl', 'authorized');
    echo '&nbsp;&nbsp;&nbsp;';
    echo $this->Html->image('/acl_manager/img/design/cross.png') . ' ' . __d('acl', 'blocked');
    ?>

    <?php
    echo $this->element('design/footer');
    ?>

</div>