<?php
/**
 * @file
 *   Drush include for the hosting_cdn module.
 */

/**
 * Implementation of hook_hosting_site_context_options().
 */
function hosting_cdn_hosting_site_context_options(&$task) {
drush_log('hosting_cdn_hosting_site_context_options', 'warning');
drush_log('cdn: ' . print_r($task->ref->cdn, 1), 'warning');
  if (isset($task->ref->cdn) && !empty($task->ref->cdn)) {
    $task->context_options['cdn'] = $task->ref->cdn;
drush_log('contact options: ' . print_r($task->context_options, 1), 'warning');
  }
  else {
    $task->context_options['cdn'] = 'null';
  }
}

/*
function drush_hosting_alias_pre_hosting_task() {
  if (module_exists('hosting_alias')) {
    $task =& drush_get_context('HOSTING_TASK');
    if ($task->ref->type == 'site') {
      $aliases = implode(',', hosting_alias_get_aliases($task->ref));
      $task->context_options['aliases'] = ($aliases) ? $aliases : 'null';

      $redirection = db_result(db_query("SELECT redirection FROM {hosting_site_alias} WHERE vid=%d", $task->ref->vid));
      $task->context_options['redirection'] = ($redirection) ? $redirection : 'null';
    }
  }
}
*/

function drush_hosting_cdn_pre_hosting_task() {
drush_log('drush_hosting_cdn_pre_hosting_task', 'error');
  if (module_exists('hosting_cdn')) {
drush_log('1', 'error');
    $task =& drush_get_context('HOSTING_TASK');
    if ($task->ref->type == 'site') {
drush_log('2', 'error');
      $cdn = implode(',', hosting_cdn_get_cdn($task->ref));
drush_log('cdn: ' . print_r($cdn, 1), 'error');
      $task->context_options['cdn'] = ($cdn) ? $cdn : array();
drush_log('cdn: ' . print_r($task, 1), 'error');
    }
  }
}

/**
 * Implementation of hook_drush_context_import().
 *
 * If we're importing a site, then import the cdn and redirection values.
 */
function hosting_cdn_drush_context_import($context, &$node) {
  if ($context->type == 'site') {
    $node->cdn = $context->cdn;
  }
}
