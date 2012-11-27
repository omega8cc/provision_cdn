<?php
$ip_address = !empty($ip_address) ? $ip_address : '*';
?>
server {
  limit_conn   gulag 88; # like mod_evasive - this allows max 88 simultaneous connections from one IP address
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
  server_name <?php foreach ($this->cdn as $cdn_domain) : if (trim($cdn_domain)) : ?> <?php print $cdn_domain; ?><?php endif; endforeach; ?>;
  root         <?php print "{$this->root}"; ?>;

  server_name_in_redirect off;

  location ^~ /under_construction.jpg {
    root   /var/www/nginx-default;
    expires       30d;
    tcp_nodelay   off;
    access_log    off;
    log_not_found off;
  }

  ###
  ### CDN Far Future expiration support.
  ###
  location ^~ /cdn/farfuture/ {
    tcp_nodelay   off;
    access_log    off;
    log_not_found off;
    etag          off;
    gzip_http_version 1.0;
    if_modified_since exact;
    location ~* ^/cdn/farfuture/.+\.(?:css|js|jpe?g|gif|png|ico|bmp|svg|swf|pdf|docx?|xlsx?|pptx?|tiff?|txt|rtf|class|otf|ttf|woff|eot|less)$ {
      expires max;
      add_header Access-Control-Allow-Origin *;
      add_header X-Header "Far Future Generator 2.0 CDN";
      add_header Cache-Control "no-transform, public";
      add_header Last-Modified "Wed, 20 Jan 1988 04:20:42 GMT";
      rewrite ^/cdn/farfuture/[^/]+/[^/]+/(.+)$ /$1 break;
      try_files $uri @redirect;
    }
    location ~* ^/cdn/farfuture/ {
      expires epoch;
      add_header Access-Control-Allow-Origin *;
      add_header X-Header "Far Future Generator 2.1 CDN";
      add_header Cache-Control "private, must-revalidate, proxy-revalidate";
      rewrite ^/cdn/farfuture/[^/]+/[^/]+/(.+)$ /$1 break;
      try_files $uri @redirect;
    }
    try_files $uri @redirect;
  }

  ###
  ### Serve & no-log static files & images directly,
  ### without all standard drupal rewrites, php-fpm etc.
  ###
  location ~* ^.+\.(?:css|js|htc|xml|jpe?g|gif|png|ico|bmp|svg|swf|pdf|docx?|xlsx?|pptx?|tiff?|txt|rtf|cgi|bat|pl|dll|aspx?|class|otf|ttf|woff|eot|less)$ {
    expires       30d;
    tcp_nodelay   off;
    access_log    off;
    log_not_found off;
    rewrite ^/files/(.*)$ /sites/<?php print $redirect_url; ?>/files/$1 last;
    rewrite ^/images/(.*)$ /sites/<?php print $redirect_url; ?>/files/images/$1 last;
    rewrite ^/downloads/(.*)$ /sites/<?php print $redirect_url; ?>/files/downloads/$1 last;
    rewrite ^/.+/sites/.+/files/(.*)$ /sites/<?php print $redirect_url; ?>/files/$1 last;
    add_header Access-Control-Allow-Origin *;
    add_header X-Header "Static Generator 2.0 CDN";
    try_files $uri @redirect;
  }

  ###
  ### Serve & log bigger media/static/archive files directly,
  ### without all standard drupal rewrites, php-fpm etc.
  ###
  location ~* ^.+\.(?:avi|mpe?g|mov|wmv|mp3|mp4|m4a|ogg|ogv|flv|wav|midi|zip|tar|t?gz|rar|dmg|exe)$ {
    expires     30d;
    tcp_nodelay off;
    tcp_nopush  off;
    rewrite ^/files/(.*)$ /sites/<?php print $redirect_url; ?>/files/$1 last;
    rewrite ^/downloads/(.*)$ /sites/<?php print $redirect_url; ?>/files/downloads/$1 last;
    rewrite ^/.+/sites/.+/files/(.*)$ /sites/<?php print $redirect_url; ?>/files/$1 last;
    add_header Access-Control-Allow-Origin *;
    add_header X-Header "Static Generator 2.1 CDN";
    try_files $uri @redirect;
  }

  ###
  ### Advagg_css and Advagg_js support.
  ###
  location ~* files/advagg_(?:css|js)/ {
    expires       max;
    etag          off;
    access_log    off;
    log_not_found off;
    rewrite ^/files/advagg_(.*)/(.*)$ /sites/<?php print $redirect_url; ?>/files/advagg_$1/$2 last;
    add_header Cache-Control "no-transform, public";
    add_header Last-Modified "Wed, 20 Jan 1988 04:20:42 GMT";
    add_header Access-Control-Allow-Origin *;
    add_header X-Header "AdvAgg Generator 2.0 CDN";
    try_files $uri @redirect;
  }

  location / {
    root   /var/www/nginx-default;
    index  index.html index.htm;
  }

  location @redirect {
    rewrite ^/(.*)$  http://<?php print $redirect_url; ?>/$1 permanent;
  }

}
