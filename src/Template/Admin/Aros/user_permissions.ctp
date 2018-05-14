<?php
/**
 * @var \App\View\AppView $this
 */
use Cake\Core\Configure;

$this->assign('subtitle', __d('acl', 'User Permissions'));

$this->start('breadcrumb');
$this->Breadcrumbs
    ->add(__d('acl', 'Aros'), ['action' => 'index'])
    ->add('User Permissions', null, ['class' => 'active']);

echo $this->Breadcrumbs->render();
$this->end();

echo $this->Html->script('/acl_manager/js/jquery', ['block' => true]);
echo $this->Html->script('/acl_manager/js/acl_manager', ['block' => true]);
?>
<div class="row">
    <div class="col-xs-12">
        <div class="box">
            <div class="box-header">
                <h3 class="box-title"><?= __d('acl', 'Roles') ?></h3>
            </div>
            <div class="box-body table-responsive">
                <table class="table table-hover">
                    <tbody>
                    <tr>
                        <?php
                        foreach ($roles as $role) {
                            echo '<td>';

                            echo $role->$role_display_field;
                            if ($role->$role_pk_name == $user->$role_fk_name) {
                                echo $this->Html->image('/acl_manager/img/design/tick.png');
                            } else {
                                $title = __d('acl', 'Update the user role');
                                echo $this->Html->link($this->Html->image('/acl_manager/img/design/tick_disabled.png'), ['action' => 'updateUserRole', $user->$user_pk_name, $role->$role_pk_name], ['title' => $title, 'alt' => $title, 'escape' => false]);
                            }

                            echo '</td>';
                        }
                        ?>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-xs-12">
        <div class="box">
            <div class="box-header">
                <h3 class="box-title"><?php echo __d('acl', 'Permissions'); ?></h3>
                <div class="box-tools">
                    <?php
                    if ($user_has_specific_permissions) {
                        echo $this->Html->link(
                            __d('acl', 'Clear the permissions specific to this user'),
                            ['action' => 'clearUserSpecificPermissions', $user->$user_pk_name],
                            ['confirm' => __d('acl', 'Are you sure you want to clear the permissions specific to this user ?'), 'class' => 'btn btn-danger btn-xs']
                        );
                    }
                    ?>
                </div>
            </div>
            <div class="box-body table-responsive">
                <table class="table table-hover">
                    <?php
                    $headers = [__d('acl', 'action'), __d('acl', 'authorization')];
                    echo $this->Html->tag('thead', $this->Html->tableHeaders($headers));

                    $this->start('table-body');
                    foreach ($actions as $action) {
                        echo '<tr>';

                        echo '<td>' . $action . '</td>';

                        $spanId = 'right__' . $user->$user_pk_name . '_' . implode('_', explode('/', $action));

                        echo '<td>';
                        echo '<span id="' . $spanId . '">';

                        if ($permissions[$action][$user->$user_pk_name] == 1) {
                            $this->Js->buffer('register_user_toggle_right(true, "' . $this->Url->build('/') . '", "' . $spanId . '", "' . $user->$user_pk_name . '", "' . $action . '")');

                            echo $this->Html->image('/acl_manager/img/design/tick.png', ['class' => 'pointer']);
                        } elseif ($permissions[$action][$user->$user_pk_name] == 0) {
                            $this->Js->buffer('register_user_toggle_right(false, "' . $this->Url->build('/') . '", "' . $spanId . '", "' . $user->$user_pk_name . '", "' . $action . '")');

                            echo $this->Html->image('/acl_manager/img/design/cross.png', ['class' => 'pointer']);
                        } elseif ($permissions[$action][$user->$user_pk_name] == -1) {
                            echo $this->Html->image('/acl_manager/img/design/important16.png');
                        }

                        echo '</span>';

                        echo ' ';
                        echo $this->Html->image('/acl_manager/img/ajax/waiting16.gif', ['id' => '' . $spanId . '_spinner', 'style' => 'display:none;']);

                        echo '</td>';
                        echo '</tr>';
                    }
                    $this->end();

                    echo $this->Html->tag('tbody', $this->fetch('table-body'));
                    ?>
                </table>
            </div>
            <div class="box-footer clearfix">
                <?= $this->Html->image('/acl_manager/img/design/tick.png') ?>
                <?= __d('acl', 'authorized') ?>
                <?= $this->Html->image('/acl_manager/img/design/cross.png') ?>
                <?= __d('acl', 'blocked') ?>
            </div>
        </div>
    </div>
</div>

<?= $this->element('design/footer') ?>
