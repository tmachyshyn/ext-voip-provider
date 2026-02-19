<?php

fwrite(\STDOUT, "Enter an extension or provider name:\n");
$fh = fopen('php://stdin', 'r');
$name = trim(fgets($fh));
fclose($fh);

$nameLabel = $name;

$name = ucfirst($name);

$name = str_replace(' ', '', ucwords(preg_replace('/[^a-z0-9]+/i', ' ', $name)));
$nameHyphen = strtolower(preg_replace('/(?<!^)[A-Z]/', '-$0', $name));

fwrite(\STDOUT, "Enter a description text:\n");
$fh = fopen('php://stdin', 'r');
$description = trim(fgets($fh));
fclose($fh);

if (substr($description, -1) !== '.') $description .= '.';

fwrite(\STDOUT, "Enter an author name:\n");
$fh = fopen('php://stdin', 'r');
$author = trim(fgets($fh));
fclose($fh);

fwrite(\STDOUT, "Do you want to use ES6 modules in the frontend? (if unsure, choose no) [y/n]\n");
$fh = fopen('php://stdin', 'r');
$es6 = trim(fgets($fh)) === 'y';
$bundled = $es6 ? "true" : "false";
$jsTranspiled = $es6 ? "true" : "false";
fclose($fh);

$replacePlaceholders = function (string $file) use ($name, $nameHyphen, $nameLabel, $description, $author, $bundled, $jsTranspiled)
{
    $content = file_get_contents($file);

    $content = str_replace('{@name}', $name, $content);
    $content = str_replace('{@nameHyphen}', $nameHyphen, $content);
    $content = str_replace('{@nameLabel}', $nameLabel, $content);
    $content = str_replace('{@description}', $description, $content);
    $content = str_replace('{@author}', $author, $content);
    $content = str_replace('{@bundled}', $bundled, $content);
    $content = str_replace('{@jsTranspiled}', $jsTranspiled, $content);

    file_put_contents($file, $content);
};

$replacePlaceholders('package.json');
$replacePlaceholders('extension.json');
$replacePlaceholders('jsconfig.json');
$replacePlaceholders('config-default.json');
$replacePlaceholders('README.md');
$replacePlaceholders('src/files/custom/Espo/Modules/MyModuleName/Resources/module.json');

if ($es6) {
    $content = <<<CLIENT_JSON
{
  "scriptList": [
      "__APPEND__",
      "client/custom/modules/{@nameHyphen}/lib/init.js"
  ]
}
CLIENT_JSON;

    $path = 'src/files/custom/Espo/Modules/MyModuleName/Resources/metadata/app/';
    mkdir($path, 0755, true);

    $path .= "client.json";
    file_put_contents($path, $content);

    $replacePlaceholders($path);
}

// Replace MyModuleName in all files
$replaceInFiles = function(string $directory) use ($name) {
    $iterator = new \RecursiveIteratorIterator(
        new \RecursiveDirectoryIterator($directory, \RecursiveDirectoryIterator::SKIP_DOTS)
    );

    foreach ($iterator as $file) {
        if (!$file->isFile()) {
            continue;
        }

        $content = file_get_contents($file->getPathname());

        $content = str_replace('MyModuleName', $name, $content);
        $content = str_replace('NewProvider', $name, $content);
        $content = str_replace('New Provider', $name, $content);

        file_put_contents($file->getPathname(), $content);
    }
};

$replaceInFiles('src/files/custom/Espo/Modules/MyModuleName');
$replaceInFiles('tests/unit/Espo/Modules/MyModuleName');
$replaceInFiles('tests/integration/Espo/Modules/MyModuleName');

rename('src/files/custom/Espo/Modules/MyModuleName/Providers/NewProvider', 'src/files/custom/Espo/Modules/MyModuleName/Providers/'. $name);
rename('src/files/custom/Espo/Modules/MyModuleName/Resources/metadata/integrations/NewProvider.json', 'src/files/custom/Espo/Modules/MyModuleName/Resources/metadata/integrations/'. $name . '.json');

rename('src/files/custom/Espo/Modules/MyModuleName', 'src/files/custom/Espo/Modules/'. $name);
rename('src/files/client/custom/modules/my-module-name', 'src/files/client/custom/modules/'. $nameHyphen);

rename('tests/unit/Espo/Modules/MyModuleName', 'tests/unit/Espo/Modules/'. $name);
rename('tests/integration/Espo/Modules/MyModuleName', 'tests/integration/Espo/Modules/'. $name);

echo "Ready. Now you need to run 'npm install'.\n";
