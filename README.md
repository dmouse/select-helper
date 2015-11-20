![Symfony Console Select Helper](http://i.giphy.com/3oEdv5Uj1b6k5ilDzi.gif "Symfony Console Select Helper")

# Select Helper
This component provide a select helper to the symfony/console

## How to use
Include in your composer.json
```json
{
  "require": {
    "dmouse/select-helper": "@stable"
  }
}
```
Create a new instance from  the select helper class

```php
  // ...
  $select = new \Dmouse\Console\Helper\SelectHelper($output);
  $select->setOptions([
      "option 1",
      "option 2",
      "option 3"
  ]);
  $option = $select->runSelect();
  // ...
```
See more in https://github.com/dmouse/select-helper/blob/master/console.php

# Development
