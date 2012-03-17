<?php
$ip_address = !empty($ip_address) ? $ip_address : '*';
?>
server {
  limit_conn   gulag 18; # like mod_evasive - this allows max 18 simultaneous connections from one IP address
<?php
if ($ip_address == '*') {
  print "  listen       {$ip_address}:{$http_port};\n";
}
else {
  foreach ($server->ip_addresses as $ip) {
    print "  listen       {$ip}:{$http_port};\n";
  }
}
?>
  server_name  <?php foreach ($this->cdn as $cdn_domain) : if (trim($cdn_domain)) : ?> <?php print $cdn_domain; ?><?php endif; endforeach; ?>;
  root         <?php print "{$this->root}"; ?>;

  server_name_in_redirect off;

  location / {
    return 404;
    # or
    # rewrite ^ http://<?php print $redirect_url; ?> permanent;
  }

  ###
  ### Serve & no-log static files & images directly,
  ### without all standard drupal rewrites, php-fpm etc.
  ###
  location ~* ^.+\.(?:css|js|htc|xml|jpe?g|gif|png|ico|bmp|svg|swf|pdf|docx?|xlsx?|tiff?|txt|rtf|cgi|bat|pl|dll|aspx?|exe|class|otf|ttf|woff)$ {
    access_log  off;
    tcp_nodelay off;
    expires     30d;
    # allow files/images/downloads to be accessed without /sites/fqdn/
    rewrite     ^/files/(.*)$              /sites/$host/files/$1 last;
    rewrite     ^/images/(.*)$             /sites/$host/files/images/$1 last;
    rewrite     ^/downloads/(.*)$          /sites/$host/files/downloads/$1 last;
    rewrite     ^/.+/sites/.+/files/(.*)$  /sites/$host/files/$1 last;
    try_files   $uri @drupal;
  }

  ###
  ### Serve & log bigger media/static/archive files directly,
  ### without all standard drupal rewrites, php-fpm etc.
  ###
  location ~* ^.+\.(?:avi|mpe?g|mov|wmv|mp3|mp4|m4a|ogg|ogv|flv|wav|midi|zip|tar|t?gz|rar)$ {
    expires     30d;
    tcp_nodelay off;
    # allow files/downloads to be accessed without /sites/fqdn/
    rewrite     ^/files/(.*)$              /sites/$host/files/$1 last;
    rewrite     ^/downloads/(.*)$          /sites/$host/files/downloads/$1 last;
    rewrite     ^/.+/sites/.+/files/(.*)$  /sites/$host/files/$1 last;
    try_files   $uri @drupal;
  }

  ###
  ### Advagg_css and Advagg_js support.
  ###
  location ~* files/advagg_(?:css|js)/ {
    access_log off;
    expires    max;
    rewrite    ^/files/advagg_(.*)/(.*)$ /sites/$host/files/advagg_$1/$2 last;
    add_header ETag "";
    add_header Cache-Control "max-age=290304000, no-transform, public";
    add_header Last-Modified "Wed, 20 Jan 1988 04:20:42 GMT";
    add_header X-Header "AdvAgg Generator 1.0";
    set $nocache_details "Skip";
    try_files  $uri @drupal;
  }

  location @drupal {
    rewrite ^/(.*)$  http://<?php print $redirect_url; ?>/$1 permanent;
  }
}
