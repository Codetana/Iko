<?php

  /*
   * Facebook Authentication Module.
   */
  require_once(PATH_A . LIB . 'facebook.php');
  $FACEBOOK = new Facebook(array(
      'appId'		=>  $TANGO->data['facebook_app_id'],
      'secret'	=> $TANGO->data['facebook_app_secret'],
      'cookie' => true,
  ));
  $FB_USER  = $FACEBOOK->getUser();
  if( $FB_USER ){
      try{
          $FB_PROFILE = $FACEBOOK->api('/me');
          $params       = array('next' => SITE_URL . '/members.php/cmd/logout');
		  $logout       = $FACEBOOK->getLogoutUrl($params);
          $MYSQL->where('facebook_id', $FB_PROFILE['id']);
          $query = $MYSQL->get('{prefix}users');
          if( empty($query) ) {
              $time = time();
              $username = (isset($FB_PROFILE['username']) && !empty($FB_PROFILE['username']))? $FB_PROFILE['username'] : str_replace(' ', '_', $FB_PROFILE['name']);
              $data = array(
                  'username' => $username,
                  'user_email' => $FB_PROFILE['email'],
                  'date_joined' => $time,
                  'facebook_id' => $FB_PROFILE['id']
              );
              $MYSQL->insert('{prefix}users', $data);
                  
          }
          //setcookie('tangobb_sess', $query['0']['id'], time()+31536000, '/', NULL, isset($_SERVER['HTTPS']), true);
          if( !$TANGO->sess->isLogged ) {
            $TANGO->sess->assign($FB_PROFILE['email'], true, true);
          }
		  //$_SESSION['User']=$user_profile;
		  //$_SESSION['logout']=$logout;*/
          
      }catch(FacebookApiException $e){
          error_log($e);
          $user = NULL;
      }
  }
  if ($FB_USER) {
      //die(var_dump($FB_PROFILE));
      $FB_LOGOUT = $FACEBOOK->getLogoutUrl(array(
          'next' => SITE_URL . '/members.php/cmd/logout',  // Logout URL full path
      ));
      $TANGO->user->addUserLink(array(
          'Log Out' => $FB_LOGOUT
      ));
  } else {
      if( $TANGO->sess->isLogged ) {
          $TANGO->user->addUserLink(array(
              'Log Out' => SITE_URL . '/members.php/cmd/logout'
          ));
      } else {
          $FB_LOGIN = $FACEBOOK->getLoginUrl(array(
              'scope'		=> 'email', // Permissions to request from the user
          ));
          $TANGO->tpl->addParam('facebook_login_url', $FB_LOGIN);
      }
  }

?>