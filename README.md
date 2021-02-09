Running XAMPP for Linux:
```
sudo /opt/lampp/manager-linux-x64.run
```


Setup Packages:
1. Download Composer
```
php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
php -r "if (hash_file('sha384', 'composer-setup.php') === '756890a4488ce9024fc62c56153228907f1545c228516cbf63f885e036d37e9a59d27d63f46af1d4d07ee0f76181c7d3') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;"
php composer-setup.php
php -r "unlink('composer-setup.php');"
```
2. Install Packages
```
php composer.phar install
```


## Setup:


#### XAMPP
Had to change- 
`Options Indexes FollowSymLinks ExecCGI Includes` to
`Options -Indexes +FollowSymLinks +ExecCGI +Includes`


Look into this for better URLs:
https://stackoverflow.com/questions/34679002/remove-folder-names-from-the-url-using-htaccess


#### Other:
1. Create a sessions folder and give write acess
```
sudo chmod -R a+rw sessions/
```
2. Create a db_credentials.php file and populate it with the following creds:
```
<?php
  $host = '';
  $user = '';
  $password = '';
  $dbname = '';
?>
```

