<!--- parent:buttons:start -->
<!--- tpl:custom_button:start -->
<a href="%url%" class="btn btn-primary">%name%</a>
<!--- tpl:custom_button:end -->
<!--- tpl:create_thread:start -->
<a href="%url%" class="btn btn-theme">New Thread</a>
<!--- tpl:create_thread:end -->
<!--- tpl:reply_thread:start -->
<a href="#reply" class="btn btn-theme">Reply</a>
<!--- tpl:reply_thread:end -->
<!--- tpl:watch_thread:start -->
<small><a href="%url%"><i class="fa fa-eye"></i> Watch Thread</a></small>
<!--- tpl:watch_thread:end -->
<!--- tpl:unwatch_thread:start -->
<small><a href="%url%"><i class="fa fa-eye-slash"></i> Unwatch Thread</a></small>
<!--- tpl:unwatch_thread:end -->
<!--- tpl:quote_post:start -->
<a href="%url%" class="btn btn-theme">Quote</a>
<!--- tpl:quote_post:end -->
<!--- tpl:edit_post:start -->
<a href="%url%" class="btn btn-theme">Edit</a>
<!--- tpl:edit_post:end -->
<!--- tpl:report_post:start -->
<a href="%url%" class="btn btn-theme">Report</a>
<!--- tpl:report_post:end -->
<!--- tpl:mod_tools:start -->
<a href="%stick_thread_url%">%stick_thread%</a> | <a href="%close_thread_url%">%close_thread%</a> | <a href="%edit_post_url%">Edit Post</a> | <a href="%delete_post_url%">Delete Thread</a>
<br />
%move_thread_form%
<!--- tpl:mod_tools:end -->
<!--- tpl:mod_tools_posts:start -->
<a href="%edit_post_url%">Edit Post</a> | <a href="%delete_post_url%">Delete Post</a>
<!--- tpl:mod_tools_posts:end -->
<!--- tpl:mod_tools_profile:start -->
<a href="%ban_user_url%" class="btn btn-theme">%ban_user%</a>
<!--- tpl:mod_tools_profile:end -->
<!--- parent:buttons:end -->