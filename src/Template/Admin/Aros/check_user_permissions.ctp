<?php
/**
 * @var \App\View\AppView $this
 */
use Cake\Core\Configure;

echo $this->Html->script('/acl_manager/js/jquery');
echo $this->Html->script('/acl_manager/js/acl_plugin');
?>
<nav class="large-3 medium-4 columns" id="actions-sidebar">
</nav>
<div class="aros check_user_permissions large-9 medium-8 columns content">

    <?php
    echo $this->element('design/header');
    ?>

    <?php
    echo $this->element('Aros/links');
    ?>

    <?php
    echo '<p>&nbsp;</p>';
    echo '<p>';
    echo __d('acl', 'This page allows to manage users specific rights');
    echo '</p>';
    echo '<p>&nbsp;</p>';
    ?>
    <?php
    echo $this->Form->create('User');
    echo __d('acl', 'user');
    echo '<br/>';
    echo $this->Form->control($user_display_field, array('label' => false, 'div' => false));
    echo ' ';
    echo $this->Form->end(array('label' => __d('acl', 'filter'), 'div' => false));
    echo '<br/>';
    ?>
    <table border="0" cellpadding="5" cellspacing="2">
        <?php
        $column_count = 2;

        $headers = [
            $this->Paginator->sort(__d('acl', 'user'), $user_display_field),
            ''
        ];

        echo $this->Html->tableHeaders($headers);
        ?>
        <?php
        foreach ($users as $user) {
            echo '<tr>';
            echo '  <td>' . $user->$user_display_field . '</td>';
            $title = __d('acl', 'Manage user specific rights');

            $link = ['action' => 'user_permissions', $user->$user_pk_name];
            if (Configure:: read('acl.gui.users_permissions.ajax') === true) {
                $link [] = 'ajax';
            }

            echo '  <td>' . $this->Html->link($this->Html->image('/acl_manager/img/design/lock.png'), $link, array('alt' => $title, 'title' => $title, 'escape' => false)) . '</td>';

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
    echo $this->element('design/footer');
    ?>

</div>