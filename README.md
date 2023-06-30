# Custom provider for VoIP Integration extension of EspoCRM

This repository contains an example of a new VoIP Provider for VoIP Integration extension for EspoCRM.

## Required versions:

- EspoCRM v7.0+
- VoIP Integration v1.17.0+

## VoIP Integration extension:

* [Website](https://www.espocrm.com/extensions/voip-integration/)
* [Documentation](https://docs.espocrm.com/extensions/voip-integration/asterisk-integration-setup/)

## Creating an extension in EspoCRM

* [Documentation](https://docs.espocrm.com/development/extension-packages/)

## How to start

1. Define a name of your extension.
```
php init.php
```

2. Install necessary dependencies:
```
npm install
```

3. Create a `config.json` with your settings, [more details](#configuration).

4. Create a database with the defined `name`, `user` and `password` in your `config.json`.

5. Create a `config.php` with the version of your EspoCRM instance, [more details](#config-for-espocrm-instance).
```php
<?php
return [
    'version' => '7.5.0',
];
```

6. Put `VoIP Integration` extension package in `extensions` directory, [more details](#installing-addition-extensions).

7. Build your EspoCRM instance, [more details](#full-espocrm-instance-building).
```
node build --all
```

8. After building, EspoCRM instance with installed extension will be available at `site` directory. You will be able to access it with credentials:
  - Username: admin
  - Password: 1

## Development workflow

1. Do development in `src` dir.
2. Run `node build --copy`.
3. Test changes in EspoCRM instance at `site` dir.

## Configuration

Create `config.json` file in the root directory. You can copy `config-default.json` and rename it to `config.json`.

When reading, this config will be merged with `config-default.json`. You can override default parameters in the created config.

Parameters:

* espocrm.repository - from what repository to fetch EspoCRM;
* espocrm.branch - what branch to fetch (`stable` is set by default); you can specify version number instead (e.g. `5.9.2`);
* database - credentials of the dev database;
* install.siteUrl - site url of the dev instance.


## Config for EspoCRM instance

You can override EspoCRM config. Create `config.php` in the root directory of the repository. This file will be applied after EspoCRM intallation (when building).

Example:

```php
<?php
return [
    'useCacheInDeveloperMode' => true,
];
```

## Building

After building, EspoCRM instance with installed extension will be available at `site` directory. You will be able to access it with credentials:

* Username: admin
* Password: 1

### Preparation

1. You need to have *node*, *npm*, *composer* installed.
2. Run `npm install`.
3. Create a database. The database name is set in the config file.

### Full EspoCRM instance building

It will download EspoCRM (from the repository specified in the config), then build and install it. Then it will install the extension.

Command:

```
node build --all
```

Note: It will remove a previously installed EspoCRM instance, but keep the database intact.

### Copying extension files to EspoCRM instance

You need to run this command every time you make changes in `src` directory and you want to try these changes on Espo instance.

Command:

```
node build --copy
```

### Running after-install script

AfterInstall.php will be applied for EspoCRM instance.

Command:

```
node build --after-install
```

### Extension package building

Command:

```
node build --extension
```

The package will be created in `build` directory.

Note: The version number is taken from `package.json`.

### Installing addition extensions

If your extension requires other extensions, there is a way to install them automatically while building the instance.

Necessary steps:

1. Add the current EspoCRM version to the `config.php`:

```php
<?php
return [
    'version' => '7.5.0',
];

```

2. Create the `extensions` directory in the root directory of your repository.

3. Put needed extensions (e.g. `my-extension-1.0.0.zip`) in this directory.

Extensions will be installed automatically after running the command `node build --all` or `node build --install`.

## Versioning

The version number is stored in `package.json` and `package-lock.json`.

Bumping version:

```
npm version patch
npm version minor
npm version major
```

## Tests

Prepare:

1. `node build --copy`
2. `cd site`
3. `grunt test`

### Unit

Command to run unit tests:

```
vendor/bin/phpunit tests/unit/Espo/Modules/{@name}
```

### Integration

You need to create a config file `tests/integration/config.php`:

```php
<?php

return [
    'database' => [
        'driver' => 'pdo_mysql',
        'host' => 'localhost',
        'charset' => 'utf8mb4',
        'dbname' => 'TEST_DB_NAME',
        'user' => 'YOUR_DB_USER',
        'password' => 'YOUR_DB_PASSWORD',
    ],
];
```
The file should exist before you run `node build --copy`.

Command to run integration tests:

```
vendor/bin/phpunit tests/integration/Espo/Modules/{@name}
```

## Configuring IDE

You need to set the following paths to be ignored in your IDE:

* `build`
* `site/build`
* `site/application/Espo/Modules/{@name}`
* `site/tests/unit/Espo/Modules/{@name}`
* `site/tests/integration/Espo/Modules/{@name}`

## License

Change a license in `LICENSE` file. The current license is intended for scripts of this repository. It's not supposed to be used for code of your extension.
