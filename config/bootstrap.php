<?php

/* -------------------------------------------------------------------
 * The settings below have to be loaded to make the acl plugin work.
 * -------------------------------------------------------------------
 *
 * See how to include these settings in the README file
 */

use Cake\Core\Configure;

/*
 * The model name used for the user role (typically 'Role' or 'Group')
 */
if (!Configure::check('acl.aro.role.model')) {
    Configure::write('acl.aro.role.model', 'Roles');
}

/*
 * The primary key of the role model
 *
 * (can be left empty if your primary key's name follows CakePHP conventions)('id')
 */
if (!Configure::check('acl.aro.role.primary_key')) {
    Configure::write('acl.aro.role.primary_key', '');
}

/*
 * The foreign key's name for the roles
 *
 * (can be left empty if your foreign key's name follows CakePHP conventions)(e.g. 'role_id')
 */
if (!Configure::check('acl.aro.role.foreign_key')) {
    Configure::write('acl.aro.role.foreign_key', '');
}

/*
 * The model name used for the user (typically 'User')
 */
if (!Configure::check('acl.aro.user.model')) {
    Configure::write('acl.aro.user.model', 'Users');
}

/*
 * The primary key of the user model
 *
 * (can be left empty if your primary key's name follows CakePHP conventions)('id')
 */
if (!Configure::check('acl.aro.user.primary_key')) {
    Configure::write('acl.aro.user.primary_key', '');
}

/*
 * The name of the database field that can be used to display the role name
 */
if (!Configure::check('acl.aro.role.display_field')) {
    Configure::write('acl.aro.role.display_field', 'alias');
}

/*
 * You can add here role id(s) that are always allowed to access the ACL plugin (by bypassing the ACL check)
 * (This may prevent a user from being rejected from the ACL plugin after a ACL permission update)
 */
if (!Configure::check('acl.role.access_plugin_role_ids')) {
    Configure::write('acl.role.access_plugin_role_ids', array());
}

/*
 * You can add here users id(s) that are always allowed to access the ACL plugin (by bypassing the ACL check)
 * (This may prevent a user from being rejected from the ACL plugin after a ACL permission update)
 */
if (!Configure::check('acl.role.access_plugin_user_ids')) {
    Configure::write('acl.role.access_plugin_user_ids', array(1));
}

/*
 * The users table field used as username in the views
 * It may be a table field or a SQL expression such as "CONCAT(User.lastname, ' ', User.firstname)" for MySQL or "User.lastname||' '||User.firstname" for PostgreSQL
 */
if (!Configure::check('acl.user.display_name')) {
    Configure::write('acl.user.display_name', 'name');
}

/*
 * Indicates whether the presence of the Acl behavior in the user and role models must be verified when the ACL plugin is accessed
 */
if (!Configure::check('acl.check_act_as_requester')) {
    Configure::write('acl.check_act_as_requester', true);
}

/*
 * Add the ACL plugin 'locale' folder to your application locales' folders
 */
// App:: build(array('locales' => App:: pluginPath('Acl') . DS . 'locale')); // TODO FIX ME

/*
 * Warning: This option is WIP
 * Indicates whether the roles permissions page must load through Ajax
 */
//Configure::write('acl.gui.roles_permissions.ajax', true);

/*
 * Warning: This option is WIP
 * Indicates whether the users permissions page must load through Ajax
 */
//Configure::write('acl.gui.users_permissions.ajax', true);
