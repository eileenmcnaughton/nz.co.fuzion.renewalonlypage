<?php

require_once 'renewalonlypage.civix.php';

/**
 * Implementation of hook_civicrm_config
 */
function renewalonlypage_civicrm_config(&$config) {
  _renewalonlypage_civix_civicrm_config($config);
}

/**
 * Implementation of hook_civicrm_xmlMenu
 *
 * @param $files array(string)
 */
function renewalonlypage_civicrm_xmlMenu(&$files) {
  _renewalonlypage_civix_civicrm_xmlMenu($files);
}

/**
 * Implementation of hook_civicrm_install
 */
function renewalonlypage_civicrm_install() {
  return _renewalonlypage_civix_civicrm_install();
}

/**
 * Implementation of hook_civicrm_uninstall
 */
function renewalonlypage_civicrm_uninstall() {
  return _renewalonlypage_civix_civicrm_uninstall();
}

/**
 * Implementation of hook_civicrm_enable
 */
function renewalonlypage_civicrm_enable() {
  return _renewalonlypage_civix_civicrm_enable();
}

/**
 * Implementation of hook_civicrm_disable
 */
function renewalonlypage_civicrm_disable() {
  return _renewalonlypage_civix_civicrm_disable();
}

/**
 * Implementation of hook_civicrm_upgrade
 *
 * @param $op string, the type of operation being performed; 'check' or 'enqueue'
 * @param $queue CRM_Queue_Queue, (for 'enqueue') the modifiable list of pending up upgrade tasks
 *
 * @return mixed  based on op. for 'check', returns array(boolean) (TRUE if upgrades are pending)
 *                for 'enqueue', returns void
 */
function renewalonlypage_civicrm_upgrade($op, CRM_Queue_Queue $queue = NULL) {
  return _renewalonlypage_civix_civicrm_upgrade($op, $queue);
}

/**
 * Implementation of hook_civicrm_managed
 *
 * Generate a list of entities to create/deactivate/delete when this module
 * is installed, disabled, uninstalled.
 */
function renewalonlypage_civicrm_managed(&$entities) {
  return _renewalonlypage_civix_civicrm_managed($entities);
}

/**
 * Implementation of hook_buildAmount
 */

function renewalonlypage_civicrm_buildAmount($pageType, &$form, &$amount) {
  $validFormID = 265;
  $loggedOutRedirect = 'http://devnew.imba.com/user/login';
  $noOptionsRedirect = 'http://devnew.imba.com/blog/supporter/random-page';
  if($pageType != 'membership' || $form->_id != $validFormID) {
    return;
  }
  $contact_id = CRM_Core_Session::singleton()->get('userID');
  if(!$contact_id) {
    CRM_Utils_System::redirect($loggedOutRedirect);
  }
  $memberships = civicrm_api3('membership', 'get', array(
    'active_only' => TRUE,
    'contact_id' => CRM_Core_Session::singleton()->get('userID'))
  );
  $membershipTypes = array();
  foreach ($memberships['values'] as $membership) {
    $membershipTypes[$membership['membership_type_id']] = $membership['membership_type_id'];
  }
  $optionCount = 0;
  foreach ($amount as $priceFieldID => $priceField) {
    foreach ($priceField['options'] as $option) {
      if(!array_key_exists($option['membership_type_id'], $membershipTypes)) {
        unset($amount[$priceFieldID]['options'][$option['id']]);
      }
      elseif(count($membershipTypes) == 1) {
        $amount[$priceFieldID]['options'][$option['id']]['is_default'] = 1;
      }
    }
    $optionCount += count($amount[$priceFieldID]['options']);
  }
  if($optionCount == 0) {
    CRM_Utils_System::redirect($noOptionsRedirect);
  }
}
