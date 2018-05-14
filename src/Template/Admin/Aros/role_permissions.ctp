<?php
/**
 * @var \App\View\AppView $this
 */

$this->assign('subtitle', __d('acl', 'Role Permissions'));

$this->start('breadcrumb');
$this->Breadcrumbs
    ->add(__d('acl', 'Aros'), ['action' => 'index'])
    ->add('Role Permissions', null, ['class' => 'active']);

echo $this->Breadcrumbs->render();
$this->end();

echo $this->Html->script('/acl_manager/js/jquery', ['block' => true]);
echo $this->Html->script('/acl_manager/js/acl_manager', ['block' => true]);
?>
<div class="row">
    <div class="col-xs-12">
        <div class="box">
            <div class="box-header">
                <div class="box-tools">
                    <?=
                    $this->Html->link(
                        __d('acl', 'Clear permissions table'),
                        ['action' => 'empty_permissions'],
                        ['confirm' => __d('acl', 'Are you sure you want to delete all roles and users permissions ?'), 'class' => 'btn btn-danger btn-xs']
                    )
                    ?>
                </div>
            </div>
            <div class="box-body table-responsive">
                <table class="table table-hover">
                    <?php
                    $headers = [
                        h($role_display_field),
                        __d('acl', 'grant access to <em>all actions</em>'),
                        __d('acl', 'deny access to <em>all actions</em>'),
                    ];

                    echo $this->Html->tag('thead', $this->Html->tableHeaders($headers));

                    $rows = [];
                    foreach ($roles as $role) {
                        $rows [] = [
                            h($role->$role_display_field),
                            $this->Html->link(
                                $this->Html->image('/acl_manager/img/design/tick.png'),
                                ['action' => 'grant_all_controllers', $role->$role_pk_name],
                                ['escape' => false, 'confirm' => sprintf(__d('acl', "Are you sure you want to grant access to all actions of each controller to the role '%s' ?"), $role->$role_display_field)]
                            ),
                            $this->Html->link(
                                $this->Html->image('/acl_manager/img/design/cross.png'),
                                ['action' => 'deny_all_controllers', $role->$role_pk_name],
                                ['escape' => false, 'confirm' => sprintf(__d('acl', "Are you sure you want to deny access to all actions of each controller to the role '%s' ?"), $role->$role_display_field)]
                            ),
                        ];
                    }

                    echo $this->Html->tag('tbody', $this->Html->tableCells($rows));
                    ?>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-xs-12">
        <div class="box">
            <div class="box-body table-responsive">
                <table class="table table-hover">
                    <?php
                    $headers = [__d('acl', 'Action')];
                    foreach ($roles as $role) {
                        $headers[] = $role->$role_display_field;
                    }

                    echo $this->Html->tag('thead', $this->Html->tableHeaders($headers));

                    $this->start('table-body');
                    foreach ($actions as $action) {
                        echo '<tr>';

                        echo '<td>' . $action . '</td>';

                        foreach ($roles as $role) {
                            $spanId = 'right__' . $role->$role_pk_name . '_' . implode('_', explode('/', $action));
                            echo '<td>';
                            echo '<span id="' . $spanId . '">';

                            if (isset($permissions[$action][$role->$role_pk_name])) {
                                if ($permissions[$action][$role->$role_pk_name] == 1) {
                                    $this->Js->buffer('register_role_toggle_right(true, "' . $this->Url->build('/') . '", "' . $spanId . '", "' . $role->$role_pk_name . '", "' . $action . '")');

                                    echo $this->Html->image('/acl_manager/img/design/tick.png', ['class' => 'pointer']);
                                } else {
                                    $this->Js->buffer('register_role_toggle_right(false, "' . $this->Url->build('/') . '", "' . $spanId . '", "' . $role->$role_pk_name . '", "' . $action . '")');

                                    echo $this->Html->image('/acl_manager/img/design/cross.png', ['class' => 'pointer']);
                                }
                            } else {
                                /*
                                 * The right of the action for the role is unknown
                                 */
                                echo $this->Html->image('/acl_manager/img/design/important16.png', ['title' => __d('acl', 'The ACO node is probably missing. Please try to rebuild the ACOs first.')]);
                            }

                            echo '</span>';

                            echo ' ';
                            echo $this->Html->image('/acl_manager/img/ajax/waiting16.gif', ['id' => '' . $spanId . '_spinner', 'style' => 'display:none;']);

                            echo '</td>';
                        }

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
