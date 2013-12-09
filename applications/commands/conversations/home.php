<?php

  /*
   * Conversations module for TangoBB
   * Everything that you want to display MUST be in the $content variable.
   */
  if( !defined('BASEPATH') ){ die(); }
  $content    = '';
  $page_title = 'Conversations';

  if( !$TANGO->sess->isLogged ){ header('Location: ' . SITE_URL); } //If user is not logged in.
  $query = $MYSQL->query("SELECT * FROM
                          {prefix}messages
                          WHERE
                          (message_sender = {$TANGO->sess->data['id']} OR message_receiver = {$TANGO->sess->data['id']})
                          AND
                          message_type = 1
                          ORDER BY
                          message_time
                          DESC");
  if( !empty($query) ) {
      foreach( $query as $msg ) {
          
          $sender   = $TANGO->user($msg['message_sender']);
          $receiver = $TANGO->user($msg['message_receiver']);
          $content .= '<div style="border-bottom:1px solid #ccc;padding-bottom:10px;overflow:auto;">
                         <h4><a href="' . SITE_URL . '/conversations.php/cmd/view/v/' . $msg['id'] . '">' . $msg['message_title'] . '</a></h4>
                         By <a href="' . SITE_URL . '/members.php/cmd/user/id/' . $sender['id'] . '">' . $sender['username_style'] . '</a> on ' . date('F j, Y', $msg['message_time']) . '<br />
                         For <a href="' . SITE_URL . '/members.php/cmd/user/id/' . $receiver['id'] . '">' . $receiver['username_style'] . '</a>
                       </div>';
          
      }
  } else {
      $content .= $TANGO->tpl->entity(
          'danger_notice',
          'content',
          'No conversations yet.'
      );
  }

?>