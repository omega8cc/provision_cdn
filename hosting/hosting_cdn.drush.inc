<?php
/**
 * @file
 *   Drush include for the hosting_cdn module.
 */

/**
 * Implementation of hook_hosting_site_context_options().
 */
function hosting_cdn_hosting_site_context_options(&$task) {
  if (isset($task->ref->cdn) && !empty($task->ref->cdn)) {
    $task->context_options['cdn'] = $task->ref->cdn;
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
  if (module_exists('hosting_cdn')) {
    $task =& drush_get_context('HOSTING_TASK');
    if ($task->ref->type == 'site') {
      $cdn = implode(',', hosting_cdn_get_cdn($task->ref));
      $task->context_options['cdn'] = ($cdn) ? $cdn : array();
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
