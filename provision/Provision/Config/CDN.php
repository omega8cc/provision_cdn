<?php

/**
 * A basic configuration file class.
 *
 * Just a file containing the value passed to us.
 */
class Provision_Config_CDN extends Provision_Config_Http_Site {
  /**
   * Template file to load. In the same directory as this class definition.
   */
  public $template = 'cdn.tpl.php';
  public $disabled_template = 'cdn_disabled.tpl.php';

  /**
   * Where the file generated will end up.
   */
  function filename() {
    return $this->server->cdn_config_path . '/' . $this->uri . '--CDN';
  }

  /**
   * Override the write method.
   */
  function write() {
    parent::write();

    drush_set_option('TEST', 'TEST', 'site');
  }

  /**
   * Override the unlink method.
   */
  function unlink() {
    parent::unlink();
  }

  function process() {
    parent::process();

    if ($this->cdn && !is_array($this->cdn)) {
      $this->cdn = explode(",", $this->cdn);
    }

    $this->cdn = array_filter($this->cdn, 'trim');

    if (!$this->site_enabled) {
      $this->template = $this->disabled_template;
    }

  }

}
