Running XAMPP for Linux:
```
sudo /opt/lampp/manager-linux-x64.run
```


Install Packages:
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
