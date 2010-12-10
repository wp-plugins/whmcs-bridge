<?php

$wpusers=new wpusers();

class wpusers {
	var $prefix;
	var $base_prefix;
	var $wpAdmin=false;
	var $wpCustomer=false;
	var $dbname;

	function wpusers() {
		global $wpdb;
		if (isset($wpdb->base_prefix)) $this->base_prefix=$wpdb->base_prefix;
		else $this->base_prefix=$wpdb->prefix;
		$this->prefix=$wpdb->prefix."cc_mybb_";
		$this->dbname=DB_NAME;
		$this->wpAdmin=true;
		$this->wpCustomer=true;
	}

	function getWpUsers() {
		global $wpdb,$blog_id;
		$users=array();
		$u=get_users_of_blog($blog_id);
		foreach ($u as $o) {
			$users[$o->user_login]=$o->user_id;
		}
		return $users;
	}

	function sync() {
		global $wpdb,$blog_id;
		global $zErrorLog;

		if (!$this->wpAdmin) return;

		$wpdb->show_errors();
		$users=$this->getWpUsers();
		//print_r($users);

		//sync Forum to Wordpress - Wordpress is master so we're not changing roles in Wordpress
		$bbUsers=$this->getForumUsers();
		foreach ($bbUsers as $row) {
			$zErrorLog->log(0,'Sync Forum to WP: '.$row['username']);
			if ($row['group']['canmodcp']) $role='editor';
			else $role='subscriber';
			$query2=sprintf("SELECT `ID` FROM `".$this->base_prefix."users` WHERE `user_email`='%s'",$row['email']);
			$sql2 = mysql_query($query2) or die(mysql_error());
			if (mysql_num_rows($sql2) == 0) { //WP user doesn't exist
				$data=array();
				$data['user_login']=$row['email'];
				$data['user_email']=$row['email'];
				$data['first_name']=$row['firstname'];
				$data['last_name']=$row['lastname'];
				$data['user_pass']='';
				$id=$this->createWpUser($data,$role);
				if (function_exists('add_user_to_blog')) {
					add_user_to_blog($blog_id,$id,$role);
				}
			}
		}
		//sync Wordpress to Forum - Wordpress is master so we're updating roles in Forum
		$users=$this->getWpUsers();
		foreach ($users as $id) {
			$user=new WP_User($id);
			$zErrorLog->log(0,'Sync WP to Forum: '.$id.'/'.$user->data->display_name);
			if (!isset($user->data->first_name)) $user->data->first_name=$user->data->display_name;
			if (!isset($user->data->last_name)) $user->data->last_name=$user->data->display_name;
			$group=$this->getForumGroup($user);
			/*
			 if (!$this->existsForumUser($user->data->user_login)) { //create user
				$this->createForumUser($user->data->user_login,$user->data->user_pass,$user->data->user_email,$group);
				} else { //update user
				$this->updateForumUser($user->data->user_login,$user->data->user_pass,$user->data->user_email,$group);
				}
				*/
		}
	}

	function getForumUsers() {
		$users=array();

		cc_whmcs_bridge_login_admin();

		$http=cc_whmcs_bridge_http('cc_action=getUsers');
		$news = new HTTPRequest($http);
		$new->get=$get;
		if ($news->live()) {
			$output=$news->DownloadToString(true,false);
			$users=$output['users'];
		}
		return $users;
	}

	function getForumGroup($user) {
		//echo 'ok';
		if ($user->has_cap('level_10')) {
			$group='4'; //admins
		} elseif ($user->has_cap('level_5')) {
			$group='6'; //moderators
		} else {
			$group='2'; //registered
		}
		return $group;
	}

	function currentForumUser() {
		global $current_user;
		global $wpdb;

		$wpdb->select($this->dbname);
		$query=sprintf("SELECT * FROM `".$this->prefix."users` WHERE `username`='".$current_user->data->user_login."'");
		$sql = mysql_query($query) or die(mysql_error());
		$row = mysql_fetch_array($sql);
		$wpdb->select(DB_NAME);
		return $row;
	}

	function existsForumUser($login) {
		global $wpdb;

		$wpdb->select($this->dbname);
		$query2=sprintf("SELECT `uid` FROM `".$this->prefix."users` WHERE `username`='%s'",$login);
		$sql2 = mysql_query($query2) or die(mysql_error());
		if (mysql_num_rows($sql2) == 0) $exists=false;
		else $exists=true;
		$wpdb->select(DB_NAME);
		return $exists;
	}

	function getForumUser($login) {
		global $wpdb;

		$wpdb->select($this->dbname);
		$query=sprintf("SELECT * FROM `".$this->prefix."users` WHERE `username`='".$login."'");
		$sql = mysql_query($query) or die(mysql_error());
		$row = mysql_fetch_array($sql);
		$wpdb->select(DB_NAME);
		return $row;
	}

	function createForumUser($username,$password,$email,$group) {
		global $zErrorLog;

		cc_whmcs_bridge_login_admin();
		$admin=$this->getForumUser(get_option('cc_whmcs_bridge_admin_login'));

		$zErrorLog->log(0,'Create Forum user '.$username);
		$post['username']=$username;
		$post['password']=$post['confirm_password']=substr($password,1,25);
		$post['email']=$email;
		$post['usergroup']=$group;
		$post['displaygroup']=0;
		$post['submit']='Save User';
		$post['my_post_key']=md5($admin['loginkey'].$admin['salt'].$admin['regdate']);
		$_GET['module']='user/users';
		$_GET['action']='add';
		$http=cc_whmcs_bridge_http("mybb",'admin/index.php');
		$news = new HTTPRequest($http);
		$news->post=$post;
		if ($news->live()) {
			$output=$news->DownloadToString(true,false);
			$zErrorLog->log(0,'out='.$output.'=');
		}

	}

	function updateForumUser($user_login,$user_pass,$user_email,$group) {
		global $wpdb,$zErrorLog;

		cc_whmcs_bridge_login_admin();

		echo 'password='.$user_pass;
		//select client
		$http=cc_whmcs_bridge_http('ccce=admin/index.php&fuse=clients&view=Admin_Profile_Contact&frmClientID=422');
		$news = new HTTPRequest($http);
		$news->post=$post;
		if ($news->live()) {
			$output=$news->DownloadToString(true,false);
		}
		
		//update client
		$post['new_password']=$post['confirm_password']=$user_pass;
		$http=cc_whmcs_bridge_http('ccce=admin/index&fuse=clients&action=UpdatePassword&frmClientID=422');
		$news = new HTTPRequest($http);
		$news->post=$post;
		if ($news->live()) {
			$output=$news->DownloadToString(true,false);
			print_r($output);
			die();
		}

//		$zErrorLog->log(0,'Update Forum user '.$username);

	}

	function createWpUser($user,$role) {
		global $wpdb,$zErrorLog;

		$zErrorLog->log(0,'Create WP user '.print_r($user,true));
		require_once(ABSPATH.'wp-includes/registration.php');
		$user['role']=$role;
		$id=wp_insert_user($user);
		return $id;
	}

	function deleteForumUser($login) {
		global $zErrorLog;

		$user=$this->getForumUser($login);
		$admin=$this->getForumUser(get_option('cc_whmcs_bridge_admin_login'));
		$zErrorLog->log(0,'Delete Forum user '.$user);

		//$post['username']=$username;
		//$post['password']=$post['confirm_password']=substr($password,1,25);
		//$post['email']=$email;
		//$post['usergroup']=$group;
		//$post['displaygroup']=0;
		$post['submit']='Yes';
		$post['my_post_key']=md5($admin['loginkey'].$admin['salt'].$admin['regdate']);
		$_GET['module']='user/users';
		$_GET['action']='delete';
		$_GET['uid']=$user['uid'];
		$http=cc_whmcs_bridge_http("mybb",'admin/index.php');
		$zErrorLog->log(0,$http);
		$news = new HTTPRequest($http);
		$news->post=$post;
		if ($news->live()) {
			$output=$news->DownloadToString(true,false);
			$zErrorLog->log(0,'out='.$output.'=');
		}

	}

	/*
	 function updateWpUser($user,$role) {
		require_once(ABSPATH.'wp-includes/registration.php');
		global $wpdb;
		$olduser=get_userdatabylogin($user['user_login']);
		$id=$user['ID']=$olduser->ID;
		$user['role']=$role;
		$user['user_pass']=wp_hash_password($user['user_pass']);
		wp_insert_user($user);
		}
		*/

	function loggedIn() {
		if ($this->wpAdmin && is_user_logged_in()) return true;
		else return false;
	}

	function isAdmin() {
		if ($this->wpAdmin && (current_user_can('edit_plugins')  || current_user_can('edit_pages'))) return true;
		else return false;
	}

	function loginWpUser($login,$pass) {
		wp_signon(array('user_login'=>$login,'user_password'=>$pass));
	}
}

?>