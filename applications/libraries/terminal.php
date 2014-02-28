<?php

  /*
   * Terminal Library
   */

  if( !defined('BASEPATH') ){ die(); }

  class Library_Terminal {

      /*
       * Check is command exists.
       */
      public function commandExists($name) {
        global $MYSQL;
        $MYSQL->where('command_name', $name);
        $query = $MYSQL->get('{prefix}terminal');
        if( !empty($query) ) {
          return false;
        } else {
          return true;
        }
      }

      /*
       * Create new command.
       * $name - Must be lowercase.
       * $syntax - %s for arguements, example "cugroup %s %s". cugroup must be the same as in $name.
       * $function - Function to be ran when the command is called out. Full function terminal_FUNCTION(). Only FUNCTION() is allowed
       */
      public function create($name, $syntax, $function) {
        global $MYSQL;
        $data = array(
          'command_name' => $name,
          'command_syntax' => $syntax,
          'run_function' => $function
        );
        try {
            $MYSQL->insert('{prefix}terminal', $data);
          return true;
        } catch (mysqli_sql_exception $e) {
          return false;
        }
      }

  }

?>