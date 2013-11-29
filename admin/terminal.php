<?php

  define('BASEPATH', 'Staff');
  require_once('../applications/wrapper.php');

  if( !$TANGO->perm->check('access_administration') ) { header('Location: ' . SITE_URL); }//Checks if user has permission to create a thread.
  require_once('template/top.php');
  $notice = '';

  if( isset($_POST['run']) ) {
      try {
          
          foreach( $_POST as $parent => $child ) {
              $_POST[$parent] = clean($child);
          }
          
          $cmd = '/' . $_POST['command'];
          
          if( !$cmd ) {
              throw new Exception ('Please enter a command.');
          } else {
              
              list($command) = sscanf($cmd, '/%s');
              
              switch($command) {
                  
                  case "cugroup":
                    //die($cmd);
                    list($del, $username, $ug) = sscanf($cmd, '/%s %s %s');
                    if( !$g = usergroupExists($ug) ) {
                        throw new Exception ('Usergroup does not exist.');
                    } else {
                        $data = array(
                            'user_group' => $g['id']
                        );
                        $MYSQL->where('username', $username);
                        if( $MYSQL->update('{prefix}users', $data) ) {
                            $notice .= $ADMIN->alert(
                                'User\'s usergroup has been changed!',
                                'success'
                            );
                        } else {
                            throw new Exception ('Error changing user\'s usergroup.');
                        }
                    }
                  break;
                  
                  case "ban":
                    list($del, $username) = sscanf($cmd, '/%s %s');
                    $data = array(
                        'is_banned' => 1,
                        'user_group' => BAN_ID
                    );
                    $MYSQL->where('username', $username);
                    if( $MYSQL->update('{prefix}users', $data) ) {
                        $notice .= $ADMIN->alert(
                            'User has been banned!',
                            'success'
                        );
                    } else {
                        throw new Exception ('Error banning user.');
                    }
                  break;
                  
                  case "unban":
                    list($del, $username) = sscanf($cmd, '/%s %s');
                    $data = array(
                        'is_banned' => 0,
                        'user_group' => 1
                    );
                    $MYSQL->where('username', $username);
                    if( $MYSQL->update('{prefix}users', $data) ) {
                        $notice .= $ADMIN->alert(
                            'User has been unbanned!',
                            'success'
                        );
                    } else {
                        throw new Exception ('Error unbanning user.');
                    }
                  break;
                  
                  default:
                    throw new Exception ('Command does not exist!');
                  break;
                  
              }
              
          }
          
      } catch (Exception $e) {
          $notice .= $ADMIN->alert(
              $e->getMessage(),
              'danger'
          );
      }
  }

  echo $ADMIN->box(
      'Terminal',
      $notice .
      '<form action="" method="POST">
         <div class="input-group">
           <span class="input-group-addon">/</span>
           <input type="text" name="command" class="form-control" placeholder="Command" />
           <span class="input-group-btn">
             <input type="submit" name="run" value="Run" class="btn btn-default" />
           </span>
         </div>
       </form>'
  );

 echo $ADMIN->box(
      'Command',
      'You can run commands that are built-in to TangoBB. This development of this feature is still in it\'s infancy. It may or may not work.
       <br />
       <h4>Commands</h4>
       Change User\'s Usergroup: <code>cugroup &lt;username&gt; &lt;usergroup&gt;</code>
       <br />
       Ban User: <code>ban &lt;username&gt;</code><br />
       Unban User: <code>unban &lt;username&gt;</code>'
  );

  require_once('template/bot.php');
?>