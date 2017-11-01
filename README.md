# flock
qqes/flock

## Install
``` shell
composer require "qqes/flock:0.2.1"
```

## Example
``` php
require 'vendor/autoload.php';

try{
  $flock = new Qqes\Flock\Flock(__DIR__);
  $flock->lock('me');
  echo 'I heave lock file' . PHP_EOL;
  sleep(5);
  $flock->unLock('me');
  echo 'I have unlock the file' . PHP_EOL;
}catch(Exception $e){
	echo $e->getMessage();
}
```
