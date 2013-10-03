<?php

/**
 * Delete CDN aliases on the deleted site
 */
function drush_provision_cdn_provision_delete() {
  if (d()->type === 'site') {
    d()->service('cdn')->delete_config('site');
  }
}
