<?php

  define('BASEPATH', 'Staff');
  require_once('../applications/wrapper.php');

  if( !$TANGO->perm->check('access_moderation') ) { redirect(SITE_URL); }//Checks if user has permission to create a thread.
  $TANGO->tpl->getTpl('page');

  $content    = '';

  if( $PGET->g('id') ) {
      $MYSQL->bind('id', $PGET->g('id'));
      $query = $MYSQL->query("SELECT * FROM {prefix}users WHERE id = :id");

      if( !empty($query) ) {

          if( $query['0']['is_banned'] == "0" ) {
              $MYSQL->bindMore(
                array(
                  'user_group' => BAN_ID,
                  'id' => $PGET->g('id')
                )
              );

              if( $MYSQL->query("UPDATE {prefix}users SET is_banned = 1, user_group = :user_group WHERE id = :id") > 0 ) {
                $content .= $TANGO->tpl->entity(
                      'success_notice',
                      'content',
                      str_replace(
                        '%url%',
                        SITE_URL . '/members.php/cmd/user/id/' . $query['0']['id'],
                        $LANG['mod']['ban']['ban_success']
                      )
                  );
              } else {
                $content .= $TANGO->tpl->entity(
                      'danger_notice',
                      'content',
                      $LANG['mod']['ban']['ban_error']
                  );
              }

          } else {
              $content .= $TANGO->tpl->entity(
                  'danger_notice',
                  'content',
                  $LANG['mod']['ban']['already_banned']
              );
          }

      } else {
          redirect(SITE_URL);
      }

  } else {
      redirect(SITE_URL);
  }

  $TANGO->tpl->addParam(
      array(
          'page_title',
          'content'
      ),
      array(
          $LANG['mod']['ban']['ban'],
          $content
      )
  );

  echo $TANGO->tpl->output();

?>