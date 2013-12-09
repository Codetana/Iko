<?php

  define('BASEPATH', 'Staff');
  require_once('../applications/wrapper.php');

  if( !$TANGO->perm->check('access_administration') ) { header('Location: ' . SITE_URL); }//Checks if user has permission to create a thread.
  require_once('template/top.php');
  $notice = '';

  /*
   * Additional notice.
   */
  if( $PGET->g('notice') ) {
      switch( $PGET->g('notice') ) {
          case "create_success":
            $notice .= $ADMIN->alert(
                'Forum node has been created!',
                'success'
            );
          break;
          case "edit_success":
            $notice .= $ADMIN->alert(
                'Forum node has been successfully edited!',
                'success'
            );
          break;
      }
  }

  /*
   * Toggle Lock
   */
  if( $PGET->g('toggle_lock') ) {
      $id = clean($PGET->g('toggle_lock'));
      $MYSQL->where('id', $id);
      $query = $MYSQL->get('{prefix}forum_node');
      $lock  = ($query['0']['node_locked'] == 1)? '0' : '1';
      $data  = array(
          'node_locked' => $lock
      );
      $MYSQL->where('id', $id);
      if( $MYSQL->update('{prefix}forum_node', $data) ) {
          $notice .= $ADMIN->alert(
              'Lock has successfully been toggled node <strong>' . $query['0']['node_name'] . '</strong>.',
              'success'
          );
      } else {
          $notice .= $ADMIN->alert(
              'Error toggling lock on node <strong>' . $query['0']['node_name'] . '</strong>.',
              'danger'
          );
      }
  }

  /*
   * Delete Node
   */
  if( $PGET->g('delete_node') ) {
      $d_node = clean($PGET->g('delete_node'));
      $MYSQL->where('id', $d_node);
      $query  = $MYSQL->get('{prefix}forum_node');
      
      if( !empty($query) ) {
          
          $MYSQL->where('id', $d_node);
          if( $MYSQL->delete('{prefix}forum_node') ) {
              $notice .= $ADMIN->alert(
                  'Node <strong>' . $query['0']['node_name'] . '</strong> has been deleted!',
                  'success'
              );
          } else {
              $notice .= $ADMIN->alert(
                  'Error deleting node.',
                  'danger'
              );
          }
          
      } else {
          $notice .= $ADMIN->alert(
              'Node does not exist!',
              'danger'
          );
      }
  }

  /*
   * Edit place.
   */
  if( isset($_POST['change_place']) ) {
      try {
          
          foreach( $_POST as $parent => $value ) {
              $_POST[$parent] = clean($value);
          }
          
          NoCSRF::check( 'csrf_token', $_POST, true, 60*10, true );
          
          $place  = $_POST['node_place'];
          $p_node = $_POST['node_id'];
          
          if( !$place or !$p_node ) {
              throw new Exception ('All fields are required!');
          } else {
              $data = array(
                  'node_place' => $place
              );
              $MYSQL->where('id', $p_node);
              if( $MYSQL->update('{prefix}forum_node', $data) ) {
                  $notice .= $ADMIN->alert(
                      'Node place has been updated!',
                      'success'
                  );
              } else {
                  throw new Exception ('Error updating node place.');
              }
          }
          
      } catch( Exception $e ) {
          $notice .= $ADMIN->alert(
              $e->getMessage(),
              'danger'
          );
      }
  }

  $token = NoCSRF::generate('csrf_token');

  function list_manage_node($category) {
      global $MYSQL, $token;
      $query = $MYSQL->query("SELECT * FROM {prefix}forum_node WHERE in_category = $category ORDER BY node_place");
      $return = '';
      foreach( $query as $n ) {
          $locked  = ($n['node_locked'] == "1")? ' style="border-left:2px solid #e84040;" title="Node is locked."' : '';
          $return .= '<tr' . $locked . '>
                        <td>
                          <strong><a href="' . SITE_URL . '/node.php/v/' . $n['name_friendly'] . '.' . $n['id'] . '" target="_blank">' . $n['node_name'] . '</a></strong><br />
                          <small>' . $n['node_desc'] . '</small>
                        </td>
                        <td>
                          <form action="" method="POST">
                            <input type="hidden" name="csrf_token" value="' . $token . '">
                            <input type="hidden" name="node_id" value="' . $n['id'] . '" />
                            <input type="text" class="form-control" name="node_place" value="' . $n['node_place'] . '" />
                            <input type="submit" name="change_place" style="display:none;" />
                          </form>
                        </td>
                        <td>
                          <div class="btn-group">
                            <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                              Options <span class="caret"></span>
                            </button>
                            <span class="dropdown-arrow dropdown-arrow-inverse"></span>
                            <ul class="dropdown-menu dropdown-inverse" role="menu">
                              <li><a href="' . SITE_URL . '/admin/edit_node.php/id/' . $n['id'] . '">Edit Node</a></li>
                              <li><a href="' . SITE_URL . '/admin/manage_node.php/delete_node/' . $n['id'] . '">Delete Node</a></li>
                              <li><a href="' . SITE_URL . '/admin/manage_node.php/toggle_lock/' . $n['id'] . '">Toggle Lock</a></li>
                            </ul>
                          </div>
                        </td>
                      </tr>';
      }
      return $return;
  }

  $query    = $MYSQL->query("SELECT * FROM {prefix}forum_category ORDER BY category_place ASC");
  $category = '';
  foreach( $query as $cat ) {
      $category .= $ADMIN->box(
          $cat['category_title'] . '<br /><small>' . $cat['category_desc'] . '</small>',
          '',
          '<table class="table table-hover">
                      <thead>
                        <tr>
                          <th style="width:70%">Node</th>
                          <th style="width:10%">Order</th>
                          <th style="width:20%">Controls</th>
                        </tr>
                      </thead>
                      <tbody>
                        ' . list_manage_node($cat['id']) . '
                      </tbody>
                    </table>',
          '12'
      );
  }

  echo $ADMIN->box(
      'Forum Nodes <p class="pull-right"><a href="' . SITE_URL . '/admin/new_node.php" class="btn btn-default btn-xs">New Node</a></p>',
      $notice .
      'You can manage the forum nodes here.',
      '',
      '12'
  );

  echo $category;

  require_once('template/bot.php');
?>