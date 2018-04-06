# ACL Manager for CakePHP 3.x

Version: 2.3.0
Date: 2013-05-02
Author: Nicolas Rod <nico@alaxos.com>
Website: http://www.alaxos.net/blaxos/pages/view/plugin_acl_2.0
License: http://www.opensource.org/licenses/mit-license.php The MIT License

## Requirements

- CakePHP 3.x
- Acl (see https://github.com/cakephp/acl)
- CakeJs (see https://github.com/oldskool/cakephp-js)

## Installation

### Install acl and cakephp-js

At first, you need to install `acl` and `cakephp-jp` plugins using composer.

The recommended way to install composer packages is:

```shell
composer require cakephp/acl
composer require oldskool/cakephp-js:dev-master
```

### Enable acl plugin

In 3.0 you need to enable the plugin your `config/bootstrap.php` file:

```php
Plugin::load('Acl', ['bootstrap' => true]);
```

### Install acl-manager

_[Manual]_

* Download and unzip the repo (see the download button somewhere on this git page)
* Copy the resulting folder into `plugins`
* Rename the folder you just copied to `AclManager`

_[GIT Submodule]_

In your `app` directory type:

```shell
git submodule add -b master git://github.com/tsmsogn/acl-manager.git plugins/AclManager
git submodule init
git submodule update
```

_[GIT Clone]_

In your `plugins` directory type:

```shell
git clone -b master git://github.com/tsmsogn/acl-manager.git AclManager
```

### Enable plugin

In 3.0 you need to enable the plugin your `config/bootstrap.php` file:

```php
Plugin::load('AclManager', ['bootstrap' => true, 'routes' => true, 'autoload' => true]);
```

### Acting as a requester

Add `$this->addBehavior('Acl.Acl', ['type' => 'requester']);` to the initialize function in the files `src/Model/Table/RolesTable.php` and `src/Model/Table/UsersTable.php`:

```php
    public function initialize(array $config) {
        parent::initialize($config);

        $this->addBehavior('Acl.Acl', ['type' => 'requester']);
    }
```

### Implement parentNode function in Role entity

Add the following implementation of parentNode to the file src/Model/Entity/Role.php:

```php
    public function parentNode()
    {
        return null;
    }
```

### Implement parentNode function in User entity

Add the following implementation of parentNode to the file src/Model/Entity/User.php:

```php
    public function parentNode()
    {
        if (!$this->id) {
            return null;
        }
        if (isset($this->role_id)) {
            $roleId = $this->role_id;
        } else {
            $Users = TableRegistry::get('Users');
            $user = $Users->find('all', ['fields' => ['role_id']])->where(['id' => $this->id])->first();
            $roleId = $user->role_id;
        }
        if (!$roleId) {
            return null;
        }
        return ['Roles' => ['id' => $roleId]];
    }
```

## Visit acl-manager using browser

Visit `/admin/acl_manager/acos/index` using browser, you will see anything.
