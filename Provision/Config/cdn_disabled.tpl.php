server {
  listen       *:<?php print $http_port; ?>;
  server_name  <?php print implode(' ', $this->cdn); ?>;
  root         /var/www/nginx-default;
  index        index.html index.htm;
  ### Dont't reveal Aegir front-end URL here.
}
