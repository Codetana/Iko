<?php

  define('BASEPATH', 'Staff');
  require_once('../applications/wrapper.php');

  if( !$TANGO->perm->check('access_administration') ) { redirect(SITE_URL); }//Checks if user has permission to create a thread.
  require_once('template/top.php');
  $notice = '';

  function languagePackages() {
    global $TANGO;
    $return = '';
    if ($handle = opendir('../applications/languages/')) {
      while (false !== ($entry = readdir($handle))) {
        if ($entry != "." && $entry != ".." && $entry != "index.html") {
          $explode = explode('.php', $entry);
          $checked = ($TANGO->data['site_language'] == $explode['0'])? ' selected' : '';
          $return .= '<option value="' . $explode['0'] . '"' . $checked . '>' . ucfirst($explode['0']) . '</option>';
        }
      }
      closedir($handle);
    }
    return $return;
  }

  if( isset($_POST['update']) ) {
      try {

          foreach( $_POST as $parent => $child ) {
              $_POST[$parent] = clean($child);
          }

          NoCSRF::check( 'csrf_token', $_POST );

          $site_name    = $_POST['site_name'];
          $board_email  = $_POST['board_email'];
          $site_lang    = $_POST['default_language'];
          $site_rules   = $_POST['board_rules'];

          $fb_app_id    = $_POST['fb_app_id'];
          $fb_app_sec   = $_POST['fb_app_secret'];
          $enable_fb    = (isset($_POST['enable_facebook']))? '1' : '0';

          $rcap_public  = $_POST['rcap_public'];
          $rcap_private = $_POST['rcap_private'];
          $enable_rcap  = (isset($_POST['enable_recaptcha']))? '2' : '1';

          $smtp_port    = $_POST['smtp_port'];
          $smtp_user    = $_POST['smtp_user'];
          $smtp_pass    = $_POST['smtp_pass'];
          $smtp_add     = $_POST['smtp_add'];
          $enable_smtp  = (isset($_POST['enable_smtp']))? '2' : '1';
          if( !$site_name or !$board_email or !$site_lang ) {
              throw new Exception ('All fields are required!');
          } else {

              $data = array(
                  'site_name' => $site_name,
                  'site_email' => $board_email,
                  'site_rules' => $site_rules,
                  'site_language' => $site_lang,
                  'facebook_app_id' => $fb_app_id,
                  'facebook_app_secret' => $fb_app_sec,
                  'facebook_authenticate' => $enable_fb,
                  'recaptcha_public_key' => $rcap_public,
                  'recaptcha_private_key' => $rcap_private,
                  'captcha_type' => $enable_rcap,
                  'mail_type' => $enable_smtp,
                  'smtp_address' => $smtp_add,
                  'smtp_port' => $smtp_port,
                  'smtp_username' => $smtp_user,
                  'smtp_password' => $smtp_pass
              );
              $MYSQL->where('id', 1);

              try {
                  $MYSQL->update('{prefix}generic', $data);
                  $notice .= $ADMIN->alert(
                      'Informations saved!',
                      'success'
                  );
              } catch (mysqli_sql_exception $e) {
                  throw new Exception ('Error saving information. Try again later.');
              }

          }

      } catch (Exception $e) {
          $notice .= $ADMIN->alert(
              $e->getMessage(),
              'danger'
          );
      }
  }

  $token = NoCSRF::generate('csrf_token');

  echo '<form action="" method="POST">';

  echo $ADMIN->box(
      'General Settings',
      $notice .
      '<input type="hidden" name="csrf_token" value="' . $token . '">
       <label for="site_name">Board Name</label>
       <input type="text" class="form-control" name="site_name" id="site_name" value="' . $TANGO->data['site_name'] . '" />
       <label for="board_email">Board Email</label>
       <input type="text" class="form-control" name="board_email" id="board_email" value="' . $TANGO->data['site_email'] . '" />
       <label for="default_language">Default Languge</label><br />
       <select name="default_language" id="Default_language">
       ' . languagePackages() . '
       </select>'
  );
  echo $ADMIN->box(
    'Forum Rules',
    'HTML tags will be converted into ascii codes.
     <textarea name="board_rules" class="form-control" style="min-height:250px;">' . $TANGO->data['site_rules'] . '</textarea>'
  );
  $smtp_check = ($TANGO->data['mail_type'] == 2)? ' CHECKED' : '';
  echo $ADMIN->box(
      'SMTP/Email Settings',
      '<label for="smtp_add">SMTP Address</label>
       <input type="text" name="smtp_add" id="smtp_add" class="form-control" value="' . $TANGO->data['smtp_address'] . '" />
       <label for="smtp_user">SMTP Username</label>
       <input type="text" name="smtp_user" id="smtp_user" class="form-control" value="' . $TANGO->data['smtp_username'] . '" />
       <label for="smtp_pass">SMTP Password</label>
       <input type="text" name="smtp_pass" id="smtp_pass" class="form-control" value="' . $TANGO->data['smtp_password'] . '" />
       <label for="smtp_port">SMTP Port</label>
       <input type="text" name="smtp_port" id="smtp_port" class="form-control" value="' . $TANGO->data['smtp_port'] . '" />
       <input type="checkbox" name="enable_smtp" value="1"' . $smtp_check . ' /> Send email using SMTP.'
  );
  $fb_check = ($TANGO->data['facebook_authenticate'] == 1)? ' CHECKED' : '';
  echo $ADMIN->box(
      'Facebook Settings',
      'The Facebook application ID and secret are <strong>required</strong> for Facebook Authentication.<br />
       <label for="fb_app_id">Facebook App ID</label>
       <input type="text" name="fb_app_id" id="fb_app_id" class="form-control" value="' . $TANGO->data['facebook_app_id'] . '" />
       <label for="fb_app_secret">Facebook App Secret</label>
       <input type="text" name="fb_app_secret" id="fb_app_secret" class="form-control" value="' . $TANGO->data['facebook_app_secret'] . '" />
       <input type="checkbox" name="enable_facebook" value="1"' . $fb_check . ' /> Enable Facebook Authentication'
  );
  $recaptcha_check = ($TANGO->data['captcha_type'] == "2")? ' CHECKED' : '';
  echo $ADMIN->box(
      'Captcha Settings',
      'The  public and private keys are <strong>required</strong> for reCaptcha.<br />
       <label for="rcap_public">reCaptcha Public Key</label>
       <input type="text" name="rcap_public" id="rcap_public" class="form-control" value="' . $TANGO->data['recaptcha_public_key'] . '" />
       <label for="rcap_private">reCaptcha Private Key</label>
       <input type="text" name="rcap_private" id="rcap_private" class="form-control" value="' . $TANGO->data['recaptcha_private_key'] . '" />
       <input type="checkbox" name="enable_recaptcha" value="1"' . $recaptcha_check . ' /> Use reCaptcha'
  );

  echo $ADMIN->box(
    null,
    '<input type="submit" name="update" class="btn btn-default" value="Save Settings" />'
  );

  echo '</form>';

  require_once('template/bot.php');

?>