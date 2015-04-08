<IfModule mod_rewrite.c>
RewriteEngine On

RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)$ read.php?k=$1&path=$0 [QSA,NC,L]
</IfModule>