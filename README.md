# Usage

```php
require_once 'vendor/autoload.php';

// Specify which version you want
//$os = Datasift\Os::getOs("Ubuntu", "12.10");

// Or, autodetect
$os = Datasift\Os::getOs();

// Show what version we're currently running
echo "Currently running: ". $os->getName()." ".$os->getVersion().PHP_EOL;
echo PHP_EOL;

// Run a command
echo $os->runCommand("ls -l");
echo PHP_EOL;

// Possible class names
var_dump($os->getPossibleClassNames());

// Dump out the class so that we can see if it's a specialist class or a base one
var_dump($os);
```
