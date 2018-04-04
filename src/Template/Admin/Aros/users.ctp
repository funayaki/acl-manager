<?php
/**
 * @var \App\View\AppView $this
 */
?>
<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
    </ul>
</nav>
<div class="aros users large-9 medium-8 columns content">

    <?php
    echo $this->element('design/header');
    ?>

    <?php
    echo $this->element('Aros/links');
    ?>

    <?php
    echo $this->Form->create('User');
    echo __d('acl', 'name');
    echo '<br/>';
    echo $this->Form->input($user_display_field, array('label' => false, 'div' => false));
    echo ' ';
    echo $this->Form->end(array('label' => __d('acl', 'filter'), 'div' => false));
    echo '<br/>';
    ?>
    <table border="0" cellpadding="5" cellspacing="2">
        <?php
        $column_count = 1;

        $headers = array($this->Paginator->sort($user_display_field, __d('acl', 'name')));

        foreach ($roles as $role) {
            $headers[] = $role->$role_display_field;
            $column_count++;
        }

        echo $this->Html->tableHeaders($headers);

        ?>

        <?php
        foreach ($users as $user) {
            $style = isset($user['aro']) ? '' : ' class="line_warning"';

            echo '<tr' . $style . '>';
            echo '  <td>' . $user->$user_display_field . '</td>';

            foreach ($roles as $role) {
                if (isset($user['aro']) && $role->$role_pk_name == $user->$role_fk_name) {
                    echo '  <td>' . $this->Html->image('/acl_manager/img/design/tick.png') . '</td>';
                } else {
                    $title = __d('acl', 'Update the user role');
                    echo '  <td>' . $this->Html->link($this->Html->image('/acl_manager/img/design/tick_disabled.png'), ['action' => 'updateUserRole', $user->$user_pk_name, $role->$role_pk_name], array('title' => $title, 'alt' => $title, 'escape' => false)) . '</td>';
                }
            }

            //echo '  <td>' . (isset($user['aro']) ? $this->Html->image('/acl_manager/img/design/tick.png') : $this->Html->image('/acl_manager/img/design/cross.png')) . '</td>';

            echo '</tr>';
        }
        ?>
        <tr>
            <td class="paging" colspan="<?php echo $column_count ?>">
                <?php echo $this->Paginator->prev('<< ' . __d('acl', 'previous'), array(), null, array('class' => 'disabled')); ?>
                |
                <?php echo $this->Paginator->numbers(array('modulus' => 5, 'first' => 2, 'last' => 2, 'after' => ' ', 'before' => ' ')); ?>
                |
                <?php echo $this->Paginator->next(__d('acl', 'next') . ' >>', array(), null, array('class' => 'disabled')); ?>
            </td>
        </tr>
    </table>


    <?php
    if ($missing_aro) {
        ?>
        <div style="margin-top:20px">

            <p class="warning"><?php echo __d('acl', 'Some users AROS are missing. Click on a role to assign one to a user.') ?></p>

        </div>
    <?php
    }
    ?>

    <?php
    echo $this->element('design/footer');
    ?>

</div>