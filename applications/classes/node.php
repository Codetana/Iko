<?php

  /*
   * Node Display class of Iko
   */
  if( !defined('BASEPATH') ){ die(); }

  class Tango_Node {
      
      /*
       * Putting the forum node together.
       * Breadcrumbs feature is to be postponed.
       */
      public function threads($id) {
          global $MYSQL, $TANGO;
          
          //$MYSQL->where('id', $id); // id? Shouldn't it be origin_node?
          //$MYSQL->where('post_type', 1);
          //$query = $MYSQL->get('{prefix}forum_posts');
          $MYSQL->bind('id', $id);
          $query = $MYSQL->query("SELECT * FROM {prefix}forum_posts WHERE post_type = 1 AND id = :id");
          $status = $this->thread_new_posts($id);
          
          $return = '';
          foreach( $query as $post ) {
              $user     = $TANGO->user($post['post_user']);
              $closed   = ( $post['post_locked'] == "1" )? $TANGO->tpl->entity('thread_closed') : '';
              $stickied =  ( $post['post_sticky'] == "1" ) ? $TANGO->tpl->entity('thread_stickied') : '';
              $post_time = simplify_time($post['post_time'], @$TANGO->sess->data['location']);
              
              $return .= $TANGO->tpl->entity(
                  'forum_listings_node_threads_posts',
                  array(
                      'thread_name',
                      'user',
                      'user_avatar',
                      'post_time',
                      'latest_post',
                      'status'
                  ),
                  array(
                      '<a href="' . SITE_URL . '/thread.php/' . $post['title_friendly'] . '.' . $post['id'] . '">' . $post['post_title'] . '</a>' . $closed . $stickied,
                      '<a href="' . SITE_URL . '/members.php/cmd/user/id/' . $user['id'] . '">' . $user['username'] . '</a>',
                       $user['user_avatar'],
                      '<span title="' . $post_time['tooltip'] . '">' . $post_time['time'] . '</span>',
                      $this->latestReply($post['id'], SITE_URL . '/thread.php/' . $post['title_friendly'] . '.' . $post['id']),
                      $status
                  )
              );
          }
          return $return;
      }
      
      /*
       * Adding latest reply to the thread.
       */
      public function latestReply($id, $url) {
          global $MYSQL, $TANGO, $LANG;
          
          /*$MYSQL->where('origin_thread', $id);
          $MYSQL->where('post_type', '2');
          $query = $MYSQL->get('{prefix}forum_posts');*/
          $id    = (int) $id;
          //$data = array($id);
          /*$query = $MYSQL->rawQuery("SELECT * FROM
                                  {prefix}forum_posts
                                  WHERE
                                  origin_thread = ?
                                  AND
                                  post_type = 2
                                  ORDER BY
                                  post_time
                                  DESC", $data);*/
          $MYSQL->bind('origin_thread', $id);
          $query = $MYSQL->query("SELECT * FROM {prefix}forum_posts WHERE origin_thread = :origin_thread AND post_type = 2 ORDER BY post_time DESC");
          if( !empty($query) ) {

              //$MYSQL->where('origin_thread', $query['0']['origin_thread']);
              //$q      = $MYSQL->get('{prefix}forum_posts');
              $MYSQL->bind('origin_thread', $query['0']['origin_thread']);
              $q      = $MYSQL->query("SELECT * FROM {prefix}forum_posts WHERE origin_thread = :origin_thread");

              $q      = (count($q) / POST_RESULTS_PER_PAGE);
              $page   = ( $q > 1 )? '/page/' . ceil($q) . '/' : '';
              
              $user   = $TANGO->user($query['0']['post_user']);
              $post_time = simplify_time($query['0']['post_time'],@$TANGO->sess->data['location']);
              
              $return = $TANGO->tpl->entity(
                  'forum_listings_node_threads_latestreply',
                  array(
                      'user_avatar',
                      'post_user',
                      'post_time'
                  ),
                  array(
                      $user['user_avatar'],
                      '<a href="' . SITE_URL . '/members.php/cmd/user/id/' . $user['id'] . '">' . $user['username'] . '</a>',
                      '<small><a href="' . $url . $page . '#post-' . $query['0']['id'] . '"><span title="' . $post_time['tooltip'] . '">' . $post_time['time'] . '</span></a></small>',
                  )
              );
              
              return $return;
              
          } else {
              return $LANG['bb']['none'];
          }
      }
      
      /**
       * Checking if the thread has been read
       * 
       */
      
      public function thread_is_read($thread_id, $user) {
          global $MYSQL, $TANGO;
          if(isset($user)) {
            
                //$MYSQL->where('user_id', $user);
                //$MYSQL->where('thread_id',$thread_id);
                //$tracker = $MYSQL->get('{prefix}thread_tracking');
                $MYSQL->bind('user_id', $user);
                $MYSQL->bind('thread_id', $thread_id);
                $tracker = $MYSQL->query("SELECT * FROM {prefix}thread_tracking WHERE user_id = :user_id AND thread_id = :thread_id");
                
                if(!empty($tracker)) {
                    $return = array(
                    'status' => true,
                    'last_visit' => $tracker['0']['last_visit']);
                }
                else {
                    $return = array('status' => false);
                }
                
          }
          else
          {
            $return = array('status' => false); 
          }
          
          return $return;
      }
      
      /**
       * Marking threads as read
       */ 
       
       
      public function thread_mark_read($thread_id) {
          global $MYSQL, $TANGO, $LANG;
          if(isset($TANGO->sess->data['id'])) {
                $status = $this->thread_is_read($thread_id, $TANGO->sess->data['id']);
                                    
                if($status['status']===false) {                    
                    // Create new entry
                    
                    /*$data = array(
                        'user_id' => $TANGO->sess->data['id'],
                        'thread_id' => $thread_id,
                        'last_visit' => time()
                    );*/
                    $MYSQL->bindMore(
                      array(
                        'user_id' => $TANGO->sess->data['id'],
                        'thread_id' => $thread_id, 
                        'last_visit' => time()
                      )
                    );
                    
                    /*try {
                        $MYSQL->insert('{prefix}thread_tracking', $data);                        
                    } catch (mysqli_sql_exception $e) {
                          throw new Exception ($LANG['errors']['thread_tracker_insert']);
                    }*/
                    $MYSQL->query("INSERT INTO {prefix}thread_tracking (user_id, thread_id, last_visit) VALUES (:user_id, :thread_id, :last_visit)");
                }
                elseif($status['status']===true) {
                    // Update
                    
                    /*$data = array(
                        'last_visit' => time()
                    );*/
                    
                    //$MYSQL->where('user_id',$TANGO->sess->data['id']);
                    //$MYSQL->where('thread_id',$thread_id);
                    
                    /*try {
                        $MYSQL->update('{prefix}thread_tracking', $data);                        
                    } catch (mysqli_sql_exception $e) {
                          throw new Exception ($LANG['errors']['thread_tracker_update']);
                    }*/
                    $MYSQL->bindMore(
                      array(
                        'last_visit' => time(),
                        'user_id' => $TANGO->sess->data['id'],
                        'thread_id' => $thread_id
                      )
                    );

                    $MYSQL->query("UPDATE {prefix}thread_tracking SET last_visit = :last_visit WHERE user_id = :user_id AND thread_id = :thread_id");
                }
                else {
                    throw new Exception ($LANG['errors']['thread_tracker_insert']);
                }
          }
      }
      
      public function thread_mark_unread($thread_id, $user, $time) {
          global $MYSQL, $TANGO, $LANG;
                $status = $this->thread_is_read($thread_id, $user);
                                    
                if($status['status']===false) {                    
                    // Create new entry
                    
                    /*$data = array(
                        'user_id' => $user,
                        'thread_id' => $thread_id,
                        'last_visit' => $time
                    );*/
                    
                    /*try {
                        $MYSQL->insert('{prefix}thread_tracking', $data);                        
                    } catch (mysqli_sql_exception $e) {
                          throw new Exception ($LANG['errors']['thread_tracker_insert']);
                    }*/
                    $MYSQL->bindMore(
                      array(
                        'user_id' => $user,
                        'thread_id' => $thread_id,
                        'last_visit' => $time
                      )
                    );
                    $MYSQL->query("INSERT INTO {prefix}thread_tracking (user_id, thread_id, last_visit) VALUES (:user_id, :thread_id, :last_visit)");
                }
                elseif($status['status']===true) {
                    // Update
                    
                    /*$data = array(
                        'last_visit' => $time
                    );
                    
                    $MYSQL->where('user_id',$user);
                    $MYSQL->where('thread_id',$thread_id);
                    
                    try {
                        $MYSQL->update('{prefix}thread_tracking', $data);                        
                    } catch (mysqli_sql_exception $e) {
                          throw new Exception ($LANG['errors']['thread_tracker_update']);
                    }*/
                    $MYSQL->bindMore(
                      array(
                        'last_visit' => $time,
                        'user_id' => $user,
                        'thread_id' => $thread_id
                      )
                    );
                    $MYSQL->query("UPDATE {prefix}thread_tracking SET last_visit = :last_visit WHERE user_id = :user_id AND thread_id = :thread_id");
                }
                else {
                    throw new Exception ($LANG['errors']['thread_tracker_insert']);
                }
          
      }
      
      public function thread_new_posts($thread_id) {
          global $MYSQL, $TANGO, $LANG;
          $return = 'read';
          if(isset($TANGO->sess->data['id'])) {
                $tracker = $this->thread_is_read($thread_id, $TANGO->sess->data['id']);
                //$data = array($thread_id, $thread_id);
                
                /*$query = $MYSQL->rawQuery("SELECT post_time FROM
                                  {prefix}forum_posts
                                  WHERE
                                  origin_thread = ?
                                  OR
                                  id = ?
                                  ORDER BY
                                  post_time
                                  DESC", $data);*/
                $MYSQL->bind('origin_thread', $thread_id);
                $MYSQL->bind('id', $thread_id);
                $query = $MYSQL->query("SELECT * FROM {prefix}forum_posts WHERE origin_thread = :origin_thread OR id = :id ORDER BY post_time DESC");
                if(isset($tracker['last_visit'])) {
                    foreach( $query as $post ) {
                        if($post['post_time'] > $tracker['last_visit']){
                            $return = 'unread';
                            break;
                        }
                    } 
                }
                     
          }
      return $return; 
      }
      
  }

?>