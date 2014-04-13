<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2010 osCommerce

  Released under the GNU General Public License
*/

  require('includes/application_top.php');

  require(DIR_WS_LANGUAGES . $language . '/' . FILENAME_CONTACT_US);

  if (isset($HTTP_GET_VARS['action']) && ($HTTP_GET_VARS['action'] == 'send') && isset($HTTP_POST_VARS['formid']) && ($HTTP_POST_VARS['formid'] == $sessiontoken)) {
    $error = false;

    $name = tep_db_prepare_input($HTTP_POST_VARS['name']);
    $email_address = tep_db_prepare_input($HTTP_POST_VARS['email']);
    $enquiry = tep_db_prepare_input($HTTP_POST_VARS['enquiry']);
$code = tep_db_prepare_input($HTTP_POST_VARS['code']);
  if ($name < ENTRY_FIRST_NAME_MIN_LENGTH) {
      $error = true;

      $messageStack->add('contact', ENTRY_NAME_ERROR);
    }

    if (!tep_validate_email($email_address)) {
      $error = true;

      $messageStack->add('contact', ENTRY_EMAIL_ADDRESS_CHECK_ERROR);
    }
  if ($name < ENTRY_FIRST_NAME_MIN_LENGTH) {
      $error = true;

      $messageStack->add('contact', ENTRY_NAME_ERROR);
    }
  if ($code != 4) {
      $error = true;

      $messageStack->add('contact', ENTRY_SECURIMAGECODE_ERROR);
    }
    $actionRecorder = new actionRecorder('ar_contact_us', (tep_session_is_registered('customer_id') ? $customer_id : null), $name);
    if (!$actionRecorder->canPerform()) {
      $error = true;

      $actionRecorder->record(false);

      $messageStack->add('contact', sprintf(ERROR_ACTION_RECORDER, (defined('MODULE_ACTION_RECORDER_CONTACT_US_EMAIL_MINUTES') ? (int)MODULE_ACTION_RECORDER_CONTACT_US_EMAIL_MINUTES : 15)));
    }

    if ($error == false) {
      tep_mail(STORE_OWNER, STORE_OWNER_EMAIL_ADDRESS, EMAIL_SUBJECT, $enquiry, $name, $email_address);

      $actionRecorder->record();

      tep_redirect(tep_href_link(FILENAME_CONTACT_US, 'action=success'));
    }
  }

  $breadcrumb->add(NAVBAR_TITLE, tep_href_link(FILENAME_CONTACT_US));

  if (tep_session_is_registered('customer_id')) {
  $account_query = tep_db_query("select customers_firstname, customers_lastname, customers_email_address from " . TABLE_CUSTOMERS . " where customers_id = '" . (int)$customer_id . "'");
    $account = tep_db_fetch_array($account_query);

$from_name = $account['customers_firstname'] . ' ' . $account['customers_lastname']; 
$name = '<input type="text" name="name" value="'.  $from_name .'" id="name" readonly="readonly" />';
$from_email_address = $account['customers_email_address']; 
$email = '<input type="text" name="email" value="'. $from_email_address .'" id="email" readonly="readonly" />' ;
  } else {
$name = '<input type="text" name="name" value="" id="name" />';
$email = '<input type="text" name="email" value="" id="email" />' ;
  }
  require(DIR_WS_INCLUDES . 'template_top.php');
?>

<h1><?php echo HEADING_TITLE; ?></h1>

<?php
  if ($messageStack->size('contact') > 0) {
    echo $messageStack->output('contact');
  }

  if (isset($HTTP_GET_VARS['action']) && ($HTTP_GET_VARS['action'] == 'success')) {
?>

<div class="contentContainer">
  <div class="contentText">
    <?php echo TEXT_SUCCESS; ?>
  </div>

  <div style="float: right;">
    <?php echo tep_draw_button(IMAGE_BUTTON_CONTINUE, 'triangle-1-e', tep_href_link(FILENAME_DEFAULT)); ?>
  </div>
</div>

<?php
  } else {
?>

<?php echo tep_draw_form('contact_us', tep_href_link(FILENAME_CONTACT_US, 'action=send'), 'post', '', true); ?>

<div class="contentContainer">
  <div class="contentText">
    <table border="0" width="100%" cellspacing="0" cellpadding="2">
      <tr>
        <td class="fieldKey"><?php echo ENTRY_NAME; ?></td>
        <td class="fieldValue"><?php echo $name; ?></td>
      </tr>
      <tr>
        <td class="fieldKey"><?php echo ENTRY_EMAIL; ?></td>
        <td class="fieldValue"><?php echo $email; ?></td>
      </tr>
      <tr>
        <td class="fieldKey" valign="top"><?php echo ENTRY_ENQUIRY; ?></td>
        <td class="fieldValue"><?php echo tep_draw_textarea_field('enquiry', 'soft', 50, 15); ?></td>
      </tr>
    <tr>
        <td class="fieldKey"><?php echo ENTRY_SECURIMAGECODE; ?></td>
        <td class="fieldValue"><?php echo tep_draw_input_field('code'); ?></td>
      </tr>
    </table>
  </div>

  <div class="buttonSet">
    <span class="buttonAction"><?php echo tep_draw_button(ENTRY_SEND, 'mail-open', null, 'primary'); ?></span>
  </div>
</div>

</form>

<?php
  }

  require(DIR_WS_INCLUDES . 'template_bottom.php');
  require(DIR_WS_INCLUDES . 'application_bottom.php');
?>
