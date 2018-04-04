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
<div class="aros ajax_role_permissions large-9 medium-8 columns content">

    <?php
    echo $this->element('design/header');
    ?>

    <?php
    echo $this->element('Aros/links');
    ?>

    <div class="separator"></div>

    <div>

        <?php
        echo $this->Html->link($this->Html->image('/acl_manager/img/design/cross.png') . ' ' . __d('acl', 'Clear permissions table'), '/admin/acl_manager/aros/empty_permissions', array('confirm' => __d('acl', 'Are you sure you want to delete all roles and users permissions ?'), 'escape' => false));
        ?>


    </div>

    <div class="separator"></div>

    <table cellspacing="0">

        <tr>
            <th></th>
            <th><?php echo __d('acl', 'grant access to <em>all actions</em>'); ?></th>
            <th><?php echo __d('acl', 'deny access to <em>all actions</em>'); ?></th>
        </tr>

        <?php
        $i = 0;
        foreach ($roles as $role) {
            $color = ($i % 2 == 0) ? 'color1' : 'color2';
            echo '<tr class="' . $color . '">';
            echo '  <td>' . $role->$role_display_field . '</td>';
            echo '  <td style="text-align:center">' . $this->Html->link($this->Html->image('/acl_manager/img/design/tick.png'), '/admin/acl_manager/aros/grant_all_controllers/' . $role->$role_pk_name, array('escape' => false, 'confirm' => sprintf(__d('acl', "Are you sure you want to grant access to all actions of each controller to the role '%s' ?"), $role->$role_display_field))) . '</td>';
            echo '  <td style="text-align:center">' . $this->Html->link($this->Html->image('/acl_manager/img/design/cross.png'), '/admin/acl_manager/aros/deny_all_controllers/' . $role->$role_pk_name, array('escape' => false, 'confirm' => sprintf(__d('acl', "Are you sure you want to deny access to all actions of each controller to the role '%s' ?"), $role->$role_display_field))) . '</td>';
            echo '<tr>';

            $i++;
        }
        ?>
    </table>

    <div class="separator"></div>

    <div>

        <table border="0" cellpadding="5" cellspacing="2">
            <?php

            $column_count = 1;

            $headers = array(__d('acl', 'action'));

            foreach ($roles as $role) {
                $headers[] = $role->$role_display_field;
                $column_count++;
            }

            echo $this->Html->tableHeaders($headers);
            ?>

            <?php
            $js_init_done = [];
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

                foreach ($roles as $role) {
                    echo '<td>';
                    echo '<span id="right_' . implode('/', $aliases) . '_' . $role->$role_pk_name . '_' . $controller . '_' . $method . '">';

                    /*
                    * The right of the action for the role must still be loaded
                    */
                    echo $this->Html->image('/acl_manager/img/ajax/waiting16.gif', array('title' => __d('acl', 'loading')));

                    if (!in_array(implode('/', $aliases) . "_" . $controller . '_' . $role->$role_pk_name, $js_init_done)) {
                        $js_init_done[] = implode('/', $aliases) . "_" . $controller . '_' . $role->$role_pk_name;
                        $this->Js->buffer('init_register_role_controller_toggle_right("' . $this->Url->build('/', true) . '", "' . $role->$role_pk_name . '", "' . implode('/', $aliases) . '", "' . $controller . '", "' . __d('acl', 'The ACO node is probably missing. Please try to rebuild the ACOs first.') . '");');
                    }

                    echo '</span>';

                    echo ' ';
                    echo $this->Html->image('/acl_manager/img/ajax/waiting16.gif', array('id' => 'right_' . implode('/', $aliases) . '_' . $role->$role_pk_name . '_' . $controller . '_' . $method . '_spinner', 'style' => 'display:none;'));

                    echo '</td>';
                }

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

    </div>

    <?php
    echo $this->element('design/footer');
    ?>

</div>