# README file can be found at:

* `/files/images/icons/README.md`
* `/files/images/overlay/README.md`
* `/games/README.md`
* `/games/Game_1/README.md`

Each README file will tell you how to use that part of the website

### IMPORTANT

If you are deciding not to use the .htaccess file thats fine but you will need to change the code in

* play.php
* category.php
* creator.php

because they all rely on the formating of the url.

# Requirements

### Web Server

- **Apache HTTP Server** (recommended)
  - Must support `.htaccess` and mod_rewrite
- Alternatively: Nginx (advanced users, requires manual rewrite rule conversion)

### PHP

- **PHP 7.4+** (PHP 8.x recommended)
- Required PHP extensions:
  - `mbstring`
  - `fileinfo`
  - `json`
  - `gd`
