# Bootstrap-4 plugin for CakePHP-3

This plugin helps render HTML elements so they are styled correctly for Bootstrap-4.

## Installation

- Merge the following to your `require` section of composer.json, replacing {{version}} with any repo tags (eg `v1.0`, `v1.1`) or `dev-master` if you want the bleeding edge

 ```
  "require": {
    "lilhermit/cakephp-plugin-bootstrap4": "{{version}}"
  }
  ```

- Merge the following to your `repositories` section of composer.json add if you don't have one

```
  "repositories": [
    {
      "type": "vcs",
      "url": "https://bitbucket.org/lilHermit/cakephp-plugins-bootstrap4.git"
    }
  ]
```

- Perform a composer install