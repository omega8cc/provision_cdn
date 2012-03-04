<?php
$ip_address = !empty($ip_address) ? $ip_address : '*';
?>
server {
   limit_conn   gulag 18; # like mod_evasive - this allows max 18 simultaneous connections from one IP address
   listen       <?php print $ip_address . ':' . $http_port; ?>;
   server_name <?php foreach ($this->cdn as $cdn_domain) : if (trim($cdn_domain)) : ?> <?php print $cdn_domain; ?><?php endif; endforeach; ?>;
   root         <?php print "{$this->root}"; ?>;
}
