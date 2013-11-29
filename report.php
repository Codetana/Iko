<?php

  define('BASEPATH', 'Forum');
  require_once('applications/wrapper.php');

  if( !$TANGO->sess->isLogged ) { header('Location:' . SITE_URL); }//Checks if user has permission to create a thread.
  $TANGO->tpl->getTpl('page');

  if( $PGET->g('post') ) {
      
      $post  = clean($PGET->g('post'));
      $MYSQL->where('id', $post);
      $query = $MYSQL->get('{prefix}forum_posts');
      
      if( !empty($query) ) {
          
          $notice  = '';
          $content = '';
          
          if( isset($_POST['report']) ) {
              try {
                  
                  foreach( $_POST as $parent => $child ) {
                      $_POST[$parent] = clean($child);
                  }
                  
                  NoCSRF::check( 'csrf_token', $_POST, true, 60*10, true );
                  $reason = $_POST['reason'];
                  
                  if( !$reason ) {
                      throw new Exception ('All fields are required!');
                  } else {
                      
                      $time = time();
                      $data = array(
                          'report_reason' => $reason,
                          'reported_by' => $TANGO->sess->data['id'],
                          'reported_post' => $post,
                          'reported_time' => $time
                      );
                      
                      if( $MYSQL->insert('{prefix}reports', $data) ) {
                          $notice .= $TANGO->tpl->entity(
                              'success_notice',
                              'content',
                              'Report has been successfully submitted!'
                          );
                      } else {
                          throw new Exception ('Error submitting report. Try again later.');
                      }
                      
                  }
                  
              } catch( Exception $e ) {
                  $notice .= $TANGO->tpl->entity(
                      'danger_notice',
                      'content',
                      $e->getMessage()
                  );
              }
          }
          
          define('CSRF_TOKEN', NoCSRF::generate( 'csrf_token' ));
          define('CSRF_INPUT', '<input type="hidden" name="csrf_token" value="' . CSRF_TOKEN . '">');
          
          $content .= '<form action="" id="tango_form" method="POST">
                         ' . CSRF_INPUT . '
                         <label for="reason">Reason</label>
                         <textarea name="reason" id="reason" style="height:150px;width:100%;min-width:100%;max-width:100%;"></textarea>
                         <br /><br />
                         <input type="submit" name="report" value="Report" />
                       </form>';
          
          $TANGO->tpl->addParam(
              array(
                  'page_title',
                  'content'
              ),
              array(
                  'Report',
                  $notice . $content
              )
          );
          
      } else {
          header('Location: ' . SITE_URL);
      }
      
  } elseif( $PGET->g('user') ) {
      /* Feature coming soon. */
      header('Location: ' . SITE_URL);
  } else {
      header('Location: ' . SITE_URL);
  }

  echo $TANGO->tpl->output();

?>