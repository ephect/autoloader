# Ephect AutoLoader

The Ephect AutoLoader is a PSR-4 compliant autoloading class that allows you to 
add namespace prefixes and autoload class files.

## Example

The AutoLoader class is very simple to use. To register the AutoLoader you simply
call the register() method.

```php
<?php

$loader = new AutoLoader();

$loader->register();

?>
```