<?php
$ip_address = !empty($ip_address) ? $ip_address : '*';
?>
server {
  limit_conn   gulag 18;
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
  server_name  <?php print implode(' ', $this->cdn); ?>;
  root         /var/www/nginx-default;
  index        index.html index.htm;
  ### Dont't reveal Aegir front-end URL here.
}
