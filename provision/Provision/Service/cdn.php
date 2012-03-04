<?php

/**
 * The service type base class.
 *
 * All implementations of the service type will inherit this class.
 * This class should define the 'public API' to be used by the rest
 * of the system, which should not expose implementation details.
 */
class Provision_Service_cdn extends Provision_Service {
  public $service = 'cdn';

  /**
   * Initialize the service along with the server object.
   */
  function init() {
    parent::init();
  }

  /**
   * Register the cdn handler for sites.
   */
  static function subscribe_site($context) {
    $context->setProperty('cdn', array(), TRUE);
  }

  /**
   * Called on provision-verify.
   *
   * We change what we will do based on what the 
   * type of object the command is being run against.
   */
  function verify() {
    $this->create_config(d()->type);
    $this->parse_configs();
  }

}
