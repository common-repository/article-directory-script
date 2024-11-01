<?php

/*

Plugin Name: Article Directory

Plugin URI: http://contentdragon.com/plugin/article-directory-plugin/

Description: Article Directory Plugin for WordPress

Author: Jeffrey

Version: 1.1.0

Author URI: http://contentdragon.com

*/



error_reporting(E_ERROR|E_WARNING|E_PARSE);

include 'functions.php';

define("CONDRAG_VERSION", '1.1.0');



/** Administration

**********************************************************************************

**********************************************************************************/

if ( function_exists('register_deactivation_hook') )

register_deactivation_hook(__FILE__, 'deactivate_article_directory');



function deactivate_article_directory()

{

	$auth_code = get_option("condrag_auth_code");

	$user_name = get_option("contentdragon_uname");

	$post = "&amp;auth_code=$auth_code&amp;user_name=$user_name&amp;status=0";

	$result = condrag_get_contents('http://www.contentdragon.com/article_directory_api.php?resource=status', 1, $post);

	

	$article_page_id = get_option("article_page_id");

	if ($article_page_id != '')

	{

		delete_option('article_page_id');

		wp_delete_post($article_page_id);

	}

	$contentdragon_uname = get_option("contentdragon_uname");

	if ($contentdragon_uname != '')

	{

		delete_option('contentdragon_uname');

	}

	$condrag_auth_code = get_option("condrag_auth_code");

	if ($condrag_auth_code != '')

	{

		delete_option('condrag_auth_code');

	}

	$condrag_syndication_message = get_option("condrag_syndication_message");

	if ($condrag_syndication_message != '')

	{

		delete_option('condrag_syndication_message');

	}	

}





//edit_form_advanced

//post_submitbox_start



 

add_action('edit_post', array('Article_Directory_Posts', 'save_post'));

add_action('post_submitbox_start', array('Article_Directory_Posts', 'register'));

class Article_Directory_Posts 

{

	function save_post()

	{

		if (isset($_POST['draft_contentdragon']))

		{

			$auth_code = get_option("condrag_auth_code");

			$user_name = get_option("contentdragon_uname");

			$title = $_POST['post_title'];

    	$content = $_POST['content'];

			$post = "&amp;auth_code=$auth_code&amp;user_name=$user_name&amp;title=".urlencode($title)."&amp;content=".urlencode($content);

			$result = condrag_get_contents('http://www.contentdragon.com/article_directory_api.php?resource=add_post', 1, $post);

		}

	}

	

  function register()

	{

		echo '

		<div style="text-align: center; margin-bottom: 15px;">

		<strong>Send this post to your ContentDragon.com account as a draft.</strong><br /><br />

		<input class="button button-highlighted" type="submit" name="draft_contentdragon" value="Save &amp; Draft to ContentDragon.com" />

		</div>

		';

	}

}



add_action('admin_menu', array('Article_Directory_Private', 'register'));

class Article_Directory_Private 

{

	var $auth_code;

  function menu()

	{

		global $wpdb;



    $auth_code = get_option("condrag_auth_code");

		if ($auth_code == '')

		{

			$success = false;

			$error = $awb_user = $awb_pass = "";

			$site = get_option("siteurl");

			

			if (isset($_POST['signup']))

			{

				$awb_user = $_POST['awb_user'];

				$awb_pass = $_POST['awb_pass'];

				if ($awb_user == '') $error = 'You forgot to include your ContentDragon.com username!';

				else if ($awb_pass == '') $error = 'You forgot to include your ContentDragon.com password!';

				else

				{

					$htmllink = "http://www.contentdragon.com/article_directory_api.php?resource=addblog&amp;site=$site&amp;user=$awb_user&amp;pass=$awb_pass";

					$send = file_get_contents($htmllink);

					preg_match_all("/<error>(.*?)<\/error>/i", $send, $error);

					$error = $error[1][0];

					if ($error == '')

					{

						$success = true;

						$auth_code = $send;

						

						// if reinstall, clear previous settings

						deactivate_article_directory();

						

						// add new settings

						if ($article_page_id == "") condrag_insert_contentpost("Article Directory", "-- DO NOT REMOVE THIS PAGE --", "");

						add_option("contentdragon_uname", $awb_user);

						add_option("condrag_auth_code", $auth_code);

						$message = htmlentities('<p><strong>{author_firstname},</strong></p>

						<p>Are you tired of wasting hours submitting articles manually? Syndicate &quot;<strong>{article_title}</strong>&quot; 

							now to over 1000 article directories. No need to retype your article. <a href="javascript:void(0);" onclick="document.forms.synd_form.submit();">Just 

							click here to Syndicate</a><br />

						</p>

						<blockquote>

							<p><strong>What do you get?</strong><br />

								Your article submitted to 1000 general and niche directories.<br />

								Your article will be submitted to all relevant article directories listed here<br />

								With Content Dragons unique system we can rapidly distribute your article to 

								niche specific websites. Giving you laser precision as well as permanent advertisements. 

								With our system you will have full control over your articles from our members 

								control panel. You will be able to edit, update and remove an article instantly. 

								Learn more about our system.........</p>

						</blockquote>');

						add_option("condrag_syndication_message", $message);

						$page_id = get_option("article_page_id");

						$htmllink = "http://www.contentdragon.com/article_directory_api.php?resource=updateblog&amp;user=$awb_user&amp;auth_code=$auth_code&amp;page_id=$page_id";

						$send = file_get_contents($htmllink);

					}

				}

			}

			

			echo '<div class="wrap">';

			

			if ($success)

			{

				include(dirname(__FILE__)."/install.php");

				foreach ($sql_commands['tables'] as $table=>$sql){ mysql_query($sql); }

				

				foreach ($sql_commands['data'] as $table=>$sql)

				{

					if (mysql_num_rows(mysql_query("SELECT * FROM `$table`;"))==0)

					mysql_query($sql);

				}

				

				echo '<h2>Wordpress Blog Successfully Added!</h2>';

				echo "<p>You have successfully registered your WordPress blog as an article directory to your ContentDragon.com account \"$awb_user\"!</p>

				<h3>Setting up your Article Directory</h3>

				<p>Before you can start to recieving or displaying syndicated articles from ContentDragon.com you must choose the article 

				categories that you would like assigned to your article directory.

				<br /><br />

				<font size=\"4\">

				<a href=\"?page=sub_settings\">Choose Article Directory Categories Now</a>

				</font>

				</p>";			

			}

			else

			{

				if ($_GET['fix']==1)

				{

					mysql_query("DROP TABLE `condrag_articles`;");

					mysql_query("CREATE TABLE IF NOT EXISTS `condrag_articles` (

					`article_id` int(12) NOT NULL auto_increment,

					`article_state` int(11) NOT NULL default '0',

					`article_featured_state` int(11) NOT NULL default '0',

					`article_submitterid` int(12) NOT NULL default '1',

					`article_categoryid` int(12) NOT NULL default '0',

					`article_typeid` int(11) NOT NULL,

					`article_title` varchar(255) NOT NULL default '',

					`article_urltitle` varchar(255) NOT NULL default '',

					`article_summary` text NOT NULL,

					`article_keywords` text NOT NULL,

					`article_custom_fields` text NOT NULL,

					`article_text` text NOT NULL,

					`article_authorinfo` text NOT NULL,

					`article_submitteddate` datetime NOT NULL default '0000-00-00 00:00:00',

					`article_modifydate` datetime NOT NULL default '0000-00-00 00:00:00',

					`article_viewcount` int(12) NOT NULL default '0',

					`article_clicks` text NOT NULL,

					`author_firstname` varchar(255) NOT NULL,

					`author_lastname` varchar(255) NOT NULL,

					`email` varchar(255) NOT NULL,

					`code` varchar(60) NOT NULL,

					`enabled` char(1) NOT NULL default '1',

					PRIMARY KEY  (`article_id`),

					UNIQUE KEY `article_urltitle` (`article_urltitle`,`article_categoryid`),

					KEY `article_state` (`article_state`,`article_categoryid`),

					FULLTEXT KEY `article_title` (`article_title`,`article_text`,`article_keywords`)

				) ENGINE=MyISAM;");

				}

				if ($error != '') echo '<div class="updated"><p><strong>'.$error.'</strong></p></div>';

				echo '

				<form id="form1" name="form1" method="post" action="">

				<table border="0" cellspacing="10" cellpadding="0">

					<tr>

						<td colspan="2" align="left"><h2>Add  Wordpress Blog</h2>

						<p>To start using your article directory you need to add your Wordpress Blog to your ContentDragon.com account using the form below.</p>

						<p>If you do not have a ContentDragon.com account yet, 

						<a href="http://members.contentdragon.com/account/register.php" target="_blank"><strong>click here to create one now</strong></a>.</p></td>

					</tr>

					<tr>

						<td align="right" valign="middle"><strong>Blog URL</strong></td>

						<td><span style="font-size:16px;">'.$site.'</span></td>

					</tr>

					<tr>

						<td align="right" valign="middle"></td>

						<td>This associates your ContentDragon.com account with your blog.</td>

					</tr>

					<tr>

						<td align="right" valign="middle"><strong>Username</strong></td>

						<td><input name="awb_user" type="text" id="awb_user" size="30" value="'.$awb_user.'" style="font-size: 16px;" /></td>

					</tr>

					<tr>

						<td align="right" valign="middle"></td>

						<td>This is your ContentDragon.com username.</td>

					</tr>

					<tr>

						<td align="right" valign="middle"><strong>Password</strong></td>

						<td><input name="awb_pass" type="password" id="awb_pass" size="30" value="'.$awb_pass.'" style="font-size: 16px;" /></td>

					</tr>

					<tr>

						<td align="right" valign="middle"></td>

						<td>

							This is your ContentDragon.com password.<br />

							<br />

							Forget your ContentDragon.com password? <a href="http://members.contentdragon.com/account/forgot.php" target="_blank">Click here</a>.</td>

					</tr>

					<tr>

						<td align="right" valign="middle"></td>

						<td>&nbsp;</td>

					</tr>

					<tr>

						<td align="right" valign="middle"></td>

						<td><input name="signup" type="submit" id="signup" value="Add Wordpress Blog" style="font-size: 16px;" /></td>

					</tr>

				</table>

				</form>';

			}

			echo '</div>';

		}

		else

		{

			$query = mysql_query("SELECT `article_id` FROM `condrag_articles` WHERE `article_state`='1';");

			$approved = (int)mysql_num_rows($query);

			$query = mysql_query("SELECT `do_approve` FROM `condrag_article_approval_requests` WHERE `do_approve`='1';");

			$rapprove = (int)mysql_num_rows($query);

			$total = $approved+$rapprove;

			

			echo '<div class="wrap">';

			echo '<h1>Article Directory (v'.CONDRAG_VERSION.') '.condrag_version_check().'</h1>';

			echo '<p>( Article Directory Powered by: <a href="http://contentdragon.com/article-directory-word-press-plugin/" target="_blank">ContentDragon.com</a> )</p>

			<br /><br />

			<table border="0" cellspacing="0" cellpadding="10">

				<tr>

					<td colspan="4" align="left">

					&raquo; Article Directory Contains <strong>'.$total.'</strong> Total Articles, <strong>'.$rapprove.'</strong> 

					Require Approval &amp; <strong>'.$approved.'</strong> Are Live.</td>

					</tr>

					<tr>

					<td colspan="4" align="center"><hr style="height: 1px; color: #CCCCCC;" noshade="noshade" size="1" /></td>

					</tr>

				<tr>

					<td width="70" align="center" valign="middle"><img src="/wp-content/plugins/article-directory-script/images/ok.png" width="48" height="48" /></td>

					<td><h2><a href="?page=sub_approve">Article Approval Queue</a> (<strong>'.$rapprove.'</strong>)</h2>

					Approve or deny articles submitted to your article directory.</td>

					<td width="70" align="center" valign="middle"><img src="/wp-content/plugins/article-directory-script/images/options.png" width="48" height="48" /></td>

					<td><h2><a href="?page=sub_settings">Article Directory Settings</a></h2>

					Choose the categories you want to display in your article directory</td>

				</tr>

				<tr>

					<td colspan="4" align="center"><h2>&nbsp;</h2></td>

					</tr>

				<tr>

					<td align="center" valign="middle"><img src="/wp-content/plugins/article-directory-script/images/user.png" width="48" height="48" /></td>

					<td><h2><a href="?page=sub_account">ContentDragon.com Account</a></h2>

					Your ContentDragin.com account summary.</td>

					<td align="center" valign="middle"><img src="/wp-content/plugins/article-directory-script/images/project.png" width="48" height="48" /></td>

					<td><h2><a href="?page=sub_projects">ContentDragon.com Projects</a></h2>

					Check out the available projects listed on ContentDragon.com</td>

				</tr>

				<tr>

					<td colspan="4" align="center"><h2>&nbsp;</h2></td>

					</tr>

				<tr>

					<td width="70" align="center" valign="middle"><img src="/wp-content/plugins/article-directory-script/images/file-edit.png" width="48" height="48" /></td>

					<td><h2><a href="?page=sub_syndication">Syndication Message</a></h2>

					Personalise the syndicate your article page displayed when a user submits an article.</td>

					<td width="70" align="center" valign="middle"></td>

					<td></td>

				</tr>

			</table><br /><br />';

			flush();

			$htmllink = "http://www.contentdragon.com/article_directory_api.php?resource=announcements";

			$announcements = file_get_contents($htmllink);

			echo $announcements;

			echo '</div>';

		}

  }

	

	function approve()

	{

		global $wpdb;



		$do = $_GET['do'];

		$article_id = $_GET['article_id'];

		$status_msg = '';

		if ($_POST['delete'] != "" && is_numeric($article_id))

		{

			$row = mysql_fetch_assoc(mysql_query("SELECT * FROM `condrag_articles` WHERE `article_id`='$article_id';"));

			mysql_query("DELETE FROM `condrag_articles` WHERE `article_id`='$article_id';");

			mysql_query("DELETE FROM `condrag_article_approval_requests` WHERE `article_id`='$article_id';");

			$success = true;

			$do = $article_id = "";

			$status_msg =  "DELETED ARTICLE!";

		} 

		else if ($_POST['deny'] != "" && is_numeric($article_id))

		{

			$deny_reason = $_POST['deny_reason'];

			mysql_query("UPDATE `condrag_article_approval_requests` SET `deny_reason`='$deny_reason', `do_approve`='0' WHERE `article_id`='$article_id';")

			or die(mysql_error());

			$success = true;

			$do = $article_id = "";

			$status_msg =  "ARTICLE DENIED!";

		}

		else if ($_POST['approve'] != "" && is_numeric($article_id))

		{

			$row = mysql_fetch_assoc(mysql_query("SELECT * FROM `condrag_articles` WHERE `article_id`='$article_id';"));

			mysql_query("UPDATE `condrag_articles` SET `article_state`='1' WHERE `article_id`='$article_id';");

			mysql_query("UPDATE `condrag_article_approval_requests` SET `approved`='1', `do_approve`='0' WHERE `article_id`='$article_id';");

			$success = true;

			$do = $article_id = "";

			$status_msg =  "ARTICLE APPROVED!";

		}

		

		echo '<div class="wrap">';

		

		if ($do == 'view')

		{

			$membersQuery = mysql_query("SELECT * FROM condrag_articles WHERE article_id='$article_id'");

			$articles = mysql_fetch_assoc ( $membersQuery );

			extract($articles);

			$article_title = condrag_utf8encode($article_title);

			$article_text = condrag_utf8encode($article_text);

			$article_text = html_entity_decode($article_text, ENT_QUOTES);

			$article_text = stripslashes($article_text);

			$article_text = stripslashes($article_text);

			$text = strip_tags($article_text);

			$front = substr($text, 0, 500);

			$end = substr($text, 500, strpos(substr($text, 500), ".")+1)." ...";

			$text = $front.$end;

			$word_count = str_word_count(strip_tags(strtolower($article_text)));

			if (!eregi("<br>", $article_text) && !eregi("<br />", $article_text)) $article_text = str_replace("\n\n", "<br /><br />", $article_text);

			$by = "By: $author_firstname $author_lastname &lt;<a href=\"mailto:$email?subject=$article_title\">$email</a>&gt;</a>";

			$about = "<p><strong>About the author</strong>:<br />$article_authorinfo</p><p><strong>Article Word Count</strong>: $word_count</p>";

			$article_body = "<p>".$by."</p>"

			. "<p>{$article_text}</p>"

			. $about;

			$deny_reason = '';

			$query = mysql_query("SELECT * FROM `condrag_article_approval_requests` WHERE `article_id`='$article_id';");

			if (mysql_num_rows($query)>0)

			{

				$row = mysql_fetch_assoc($query);

				$deny_reason = $row['deny_reason'];

			}

			$template = file_get_contents(dirname(__FILE__)."/approve-action.tpl");

			$data = array (

				'title' => $article_title,

				'body' => $article_body,

				'deny_reason' => $deny_reason

			);

			echo '<h2><a href="?page=sub_approve">Article Approval Queue</a> &raquo; '.$article_title.'</h2>';

		}

		else

		{

			$rows = '';

			$topQuery = mysql_query ("SELECT * FROM condrag_article_approval_requests WHERE do_approve='1'");

			while ( $row = mysql_fetch_assoc ( $topQuery ) ){

			$aid = $row['article_id'];

				$membersQuery = mysql_query ("SELECT * FROM condrag_articles WHERE article_id='$aid'");	

				while ( $articles = mysql_fetch_assoc( $membersQuery ) )

				{

					$article_title = condrag_utf8encode($articles['title']);	

					$catinfo = condrag_is_category($articles['article_categoryid']);

					$category_title = $catinfo['category_title'];

					if ($catinfo['category_parentid'] != 0)

					{

						$subcatinfo = condrag_is_category($catinfo['category_parentid']);

						$category_title = $subcatinfo['category_title'];

					}		

					$rows .= '<tr class="row2">

					<td>'.$articles['article_title'].'</td>

					<td align="center">'.$category_title.'</td>

					<td align="center">'.date("m/d/y", strtotime($articles['article_submitteddate'])).'</td>

					<td align="center">

					<a href="?page=sub_approve&amp;do=view&amp;article_id='.$articles['article_id'].'">view options...</a> 

					</td>

					</tr>';

				}

			}

			$template = file_get_contents(dirname(__FILE__)."/approve-list.tpl");

			$data = array ('rows' => $rows);

			echo '<h2><a href="?page=article-directory-script/article_directory.php">Article Directory</a> &raquo; Article Approval Queue</h2>';

			if ($status_msg!='') echo '<div class="updated"><p><strong>'.$status_msg.'</strong></p></div>';

		}

		

		foreach ($data as $k=>$v) $template = str_replace('{'.$k.'}', $v, $template);

		echo $template;

		echo '</div>';

	}

	

	function settings()

	{

		global $wpdb;

		echo '<div class="wrap">';

		echo "<h2><a href=\"?page=article-directory-script/article_directory.php\">Article Directory</a> &raquo; Article Directory Settings</h2>";

		echo "<h3>Category Selection</h3><p>Check off the categories that you would like displayed.</p>";

		if( $_POST['action'] == 'save' )

		{

			foreach ($_POST['cats_def'] as $id=>$val)

			{

				$s = (isset($_POST['cats'][$id]))?1:0;

				mysql_query("UPDATE `condrag_categories` SET `active`='{$s}' WHERE `category_id`='$id';");

				mysql_query("UPDATE `condrag_articles` SET `enabled`='$s' WHERE `article_categoryid`='$id';");

			}

			

			$query = mysql_query("SELECT * FROM `condrag_categories` WHERE `category_parentid`='0' AND `active`='1' ORDER BY `category_title` ASC;");

			while ($row = mysql_fetch_assoc($query))

			{

				$query2 = mysql_query("SELECT * FROM `condrag_categories` WHERE `category_parentid`='{$row['category_id']}' ORDER BY `category_title` ASC;");

				$count1 = mysql_num_rows($query2);

				$query2 = mysql_query("SELECT * FROM `condrag_categories` WHERE `category_parentid`='{$row['category_id']}' AND `active`='0' ORDER BY `category_title` ASC;");

				$count2 = mysql_num_rows($query2);

				if ($count1==$count2)

				{

					mysql_query("UPDATE `condrag_categories` SET `active`='1' WHERE `category_parentid`='{$row['category_id']}';");

					mysql_query("UPDATE `condrag_articles` SET `enabled`='1' WHERE `article_categoryid`='{$row['category_id']}';");

				}

			}

			

			$query = mysql_query("SELECT * FROM `condrag_categories` WHERE `category_parentid`='0' AND `active`='0' ORDER BY `category_title` ASC;");

			while ($row = mysql_fetch_assoc($query))

			{

				mysql_query("UPDATE `condrag_categories` SET `active`='0' WHERE `category_parentid`='{$row['category_id']}';");

				mysql_query("UPDATE `condrag_articles` SET `enabled`='0' WHERE `article_categoryid`='{$row['category_id']}';");

				$query2 = mysql_query("SELECT * FROM `condrag_categories` WHERE `category_parentid`='{$row['category_id']}' ORDER BY `category_title` ASC;");

				while ($row2 = mysql_fetch_assoc($query2))

				{

					mysql_query("UPDATE `condrag_categories` SET `active`='0' WHERE `category_parentid`='{$row2['category_id']}';");

					mysql_query("UPDATE `condrag_articles` SET `enabled`='0' WHERE `article_categoryid`='{$row2['category_id']}';");

				}

			}

			

			$cat_ids = array();

			$cat_data = array();

			$query = mysql_query("SELECT * FROM `condrag_categories` WHERE `category_parentid`='0' AND `active`='1' ORDER BY `category_title` ASC;");

			while ($row = mysql_fetch_assoc($query))

			{

				$cat_ids[$row['category_id']] = $row['category_title'];

				$query2 = mysql_query("SELECT * FROM `condrag_categories` WHERE `category_parentid`='{$row['category_id']}' AND `active`='1' ORDER BY `category_title` ASC;");

				if (mysql_num_rows($query2)>0)

				{

					$input = $row['category_title'].'[**]';

					while ($row2 = mysql_fetch_assoc($query2))

					{

						$cat_ids[$row2['category_id']] = $row2['category_title'];

						$cat_ids[$row2['category_id'].'topid'] = $row['category_id'];

						$input .= $row2['category_title'].'||';

					}

					$input = substr($input, 0, -2);

					$cat_data[] = $input;

				}

				else

				{

					$cat_data[] = $row['category_title'];

				}

			}

			$cat_data = implode("[*]", $cat_data);

			$htmllink = "http://www.contentdragon.com/article_directory_api.php?resource=savecategories&amp;auth_code=".get_option('condrag_auth_code')."&amp;categories=".base64_encode($cat_data);

			$send = file_get_contents($htmllink);

			

			echo '<div class="updated"><p><strong>Settings saved.</strong></p></div>';

		}



		echo '<script language="javascript" type="text/javascript" src="/wp-includes/js/prototype.js"></script>

		<script language="javascript">

		function condrag_catview(id)

		{

			if ($(id).checked) $(id+\'subs\').show();

			else $(id+\'subs\').hide();

		}

		</script>';



		echo '<form name="form1" method="post" action="'.str_replace( '%7E', '~', $_SERVER['REQUEST_URI']).'">

		<input type="hidden" name="action" value="save">';

		$sql = mysql_query("SELECT * FROM `condrag_categories` WHERE `category_parentid`='' ORDER BY `category_title` ASC;");								

		while ($row = mysql_fetch_array($sql))

		{

			$checked = $row['active']==1?' checked':NULL;

			echo '<div style="padding: 5px;">';

			echo '<div style="font-size: 18px;">';

			echo '

			<input type="hidden" name="cats_def['.$row['category_id'].']" value="'.$row['active'].'">

			<input type="checkbox" name="cats['.$row['category_id'].']" id="cat'.$row['category_id'].'" value="1" onclick="condrag_catview(this.id);" '.$checked.'>

			<label for="cat'.$row['category_id'].'">'.$row['category_title'].'</label>';

			echo '</div>';

			$sql2 = mysql_query("SELECT * FROM `condrag_categories` WHERE `category_parentid`='{$row['category_id']}'	ORDER BY `category_title` ASC;");

			if (mysql_num_rows($sql2)>0)

			{

				echo '<div id="cat'.$row['category_id'].'subs" style="margin-left: 30px; padding: 5px 0; display: '.($row['active']==1?'block':'none').';">';

				while ($row2 = mysql_fetch_array($sql2))

				{

					$checked = $row2['active']==1?' checked':NULL;

					echo '<div style="width: 250px; float: left;">

					<input type="hidden" name="cats_def['.$row2['category_id'].']" value="'.$row2['active'].'">

					<input type="checkbox" name="cats['.$row2['category_id'].']" id="cat'.$row2['category_id'].'" value="1" '.$checked.'>

					<label for="cat'.$row2['category_id'].'">'.$row2['category_title'].'</label></div>';

				}

				echo '<br style="clear: left;">';

				echo '</div>';

			}

			echo '</div>';

		}

		echo '<p class="submit"><input type="submit" name="Submit" value="Update Settings" /></p>

		</form>

		</div>';

	}

	

	function syndication_page()

	{

		global $wpdb;

		echo '<div class="wrap">';

		echo "<h2><a href=\"?page=article-directory-script/article_directory.php\">Article Directory</a> &raquo; Article Syndication Message</h2>";

		

		echo "<h3>Messages</h3>

		<p>Customize your syndication message displayed to authors publishing articles.<br /><br />

		<strong>Keywords</strong><br />

		&bull; \"{article_title}\" = Title of the article being published<br />

		&bull; \"{author_firstname}\" = The publishing authors first name<br />

		&bull; \"{author_lastname}\" = The publishing authors last name<br />

		&bull; \"{email}\" = The publishing authors email address<br />

		&bull; \"javascript:document.forms.synd_form.submit();\" = Link used to submit the syndicate form

		</p>";

		$message = get_option("condrag_syndication_message");

		

		if ($_POST['action'] == "message")

		{

			$message = $_POST['message'];

			update_option("condrag_syndication_message", $message);

		}

		

		$theme_name = get_current_theme();

		echo '

		<script type="text/javascript" src="../wp-includes/js/tinymce/tiny_mce.js"></script>

		<script type="text/javascript">

		<!--

		tinyMCE.init({

		theme : "advanced",

		mode : "exact",

		elements : "message",

		theme_advanced_toolbar_align : "left",

		theme_advanced_statusbar_location : "none",

		entities : "", 

		auto_resize : true,

		plugins : "advlink,advimage,safari,pagebreak,table,save,style,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,spellchecker,template,ImageManager",

		

		theme_advanced_buttons1 : "bullist,numlist,|,outdent,indent,blockquote,|,bold,italic,underline,strikethrough,sub,sup,|,forecolor,backcolor,|,justifyleft,justifycenter,justifyright,justifyfull,|,table,|,formatselect,fontselect,fontsizeselect",

		

		theme_advanced_buttons2 : "spellchecker,|,link,unlink,anchor,|,undo,redo,|,template,image,media,|,cut,copy,paste,pastetext,pasteword,|,ltr,rtl,|,removeformat,visualaid,ImageManager,code",

		theme_advanced_buttons3 : "",

		valid_elements : "*[*]",

		extended_valid_elements : "*[*]",

		convert_urls : false,

		content_css : "../wp-content/themes/'.$theme_name.'/style.css",

		});

		-->

		</script>

		<form name="form2" method="post" action="">

		<input type="hidden" name="action" value="message">

		<textarea name="message" id="message" style="width: 75%; height: 300px;">'.html_entity_decode(stripslashes($message)).'</textarea>

		<p class="submit"><input type="submit" name="Submit" value="Update Message" /></p>

		</form>

		';

		echo '</div>';

	}

	

	function account()

	{

		$htmllink = "http://www.contentdragon.com/article_directory_api.php?resource=account&amp;auth_code="

		.get_option("condrag_auth_code")."&amp;user=".get_option("contentdragon_uname");

		$contents = condrag_nolines(condrag_get_contents($htmllink, 0, NULL));

		$contents = unserialize($contents);

		

		echo '<div class="wrap">';

		echo '<h2><a href="?page=article-directory-script/article_directory.php">Article Directory</a> &raquo; ContentDragon.com Account</h2>';

		

		echo '

		

		<table width="100%" border="0" cellspacing="8">

			<tr>

				<td colspan="2" align="center" height="100"><h2><strong>My ContentDragon.com Account Summary</strong></h2></td>

			</tr>

			<tr>

				<td width="30%" height="40" align="center" valign="top">

					<div style="border-bottom: 1px solid #666666; padding: 2px; font-size: 16px;">Account Balances</div><br />

					<table width="100%" border="0">

					<tr>

						<td width="50%"><font size="3"><strong>Balance</strong></font></td>

						<td width="50%"><font size="4">$'.number_format($contents['balance'], 2, '.', ',').'</font></td>

					</tr>

					<tr>

						<td>&nbsp;</td>

						<td></td>

					</tr>

					<tr>

						<td><font size="3"><strong>Credit Balance</strong></font></td>

						<td><font size="4">$'.number_format($contents['creditbalance'], 2, '.', ',').'</font></td>

					</tr>

					

					<tr>

						<td>&nbsp;</td>

						<td></td>

					</tr>

					<tr>

						<td><font size="3"><strong>Syndication Cost</strong></font></td>

						<td><font size="4">$'.number_format($contents['synd_amount'], 2, '.', ',').'</font></td>

					</tr>

					

					<tr>

						<td>&nbsp;</td>

						<td></td>

					</tr>

					<tr>

						<td><font size="3"><strong>Earned Per Sale</strong></font></td>

						<td><font size="4">$'.number_format($contents['synd_earn'], 2, '.', ',').'</font></td>

					</tr>

				</table>

				<br />

				<p align="center">

					<input type="button" class="button button-highlighted" name="button" id="button" value="Go to my ContentDragon.com Account" 

					onclick="window.open(\'http://members.contentdragon.com/account/myacc.php\');" />

				</p></td>

				<td width="70%" align="right" valign="top">

					

					<div style="border-bottom: 1px solid #666666; padding: 2px; font-size: 16px;">Recent Transactions</div>

					

					<table width="100%" border="0">

					<tr>

						<!--<td bgcolor="#F2F1F3"><strong>ID</strong></td>-->

						<td bgcolor="#F2F1F3"><strong>Amount</strong></td>

						<td bgcolor="#F2F1F3"><strong>Date</strong></td>

						<td bgcolor="#F2F1F3"><strong>Description</strong></td>

					</tr>

					<tr>

						<td colspan="4" align="center" height="1" style="border-top: 1px solid #CCCCCC;"></td>

					</tr>';

							

					$rows = $contents['transactions'];

					if (count($rows)==0)

					{

						echo '<tr>

								<td colspan="4" align="center"><strong>no transaction history available</strong></td>

							</tr>';

					}

					else

					{

						foreach ($rows as $row)

						{

							echo '<tr>

								<!--<td>'.$row[0].'</td>-->

								<td>'.$row[1].'</td>

								<td>'.$row[2].'</td>

								<td>'.$row[4].'</td>

							</tr>';

						}

					}

					echo '

				</table></td>

			</tr>

		</table>

		<br /><br />

		<div style="margin: 10px; font-size: 14px;">

		<strong>Glossary of terms:</strong><br />

		<u>Syndication Cost</u> = Retail Price that Content Dragon Charges for a syndicated article<br />

		<u>Earned Per Sale</u> = Commisson Earned per Syndicated Article that comes from your Site!

		</div>

		';

		

		/*

		echo '<form action="http://members.contentdragon.com/account/login.php" method="post" target="_blank">

			<input type="hidden" name="ru" value="" />

			<table width="100%" border="0" cellspacing="4" cellpadding="0">

				<tr>

					<td>&nbsp;</td>

					<td><h3>Sign In</h3></td>

				</tr>

				<tr>

					<td width="8%" align="right">Username</td>

					<td width="92%"><input type="text" name="user" class="" size="30"></td>

				</tr>

				<tr>

					<td align="right">Password</td>

			

					<td><input type="password" name="password" class="" size="30"></td>

				</tr>

				<tr>

					<td>&nbsp;</td>

				<td><input type="checkbox" name="staylogged" value="on">Keep me signed in on this computer unless I sign out</td>

				</tr>

				<tr>

					<td>&nbsp;</td>

			

					<td><input type="submit" name="loginsys" value="Login"></td>

				</tr>

				<tr>

					<td>&nbsp;</td>

					<td>&nbsp;</td>

				</tr>

				<tr>

					<td>&nbsp;</td>

					<td><a href="http://members.contentdragon.com/account/forgot.php" target="_blank">I\'ve forgotten my login details</a></td>

				</tr>

			</table>

		</form>';

		*/

		echo '</div>';

	}

	

	function projects()

	{

		$htmllink = "http://www.contentdragon.com/article_directory_api.php?resource=projects";

		$contents = condrag_nolines(condrag_get_contents($htmllink, 0, NULL));

		echo '<div class="wrap">';

		echo '<h2><a href="?page=article-directory-script/article_directory.php">Article Directory</a> &raquo; ContentDragon.com Projects</h2><br /><br />';

		echo $contents;

		echo '</div>';

	}

	

  function register()

	{

    add_menu_page("Article Directory", "Articles", 8, __FILE__, array('Article_Directory_Private', 'menu')); 

		$auth_code = get_option("condrag_auth_code");

		if ($auth_code != '')

		{

			add_submenu_page(__FILE__, 'Approve Articles', 'Approve Articles', 8, 'sub_approve', array('Article_Directory_Private', 'approve'));

			add_submenu_page(__FILE__, 'Content Dragon Settings', 'Settings', 8, 'sub_settings', array('Article_Directory_Private', 'settings'));

			add_submenu_page(__FILE__, 'Content Dragon Syndication', 'Syndication Message', 8, 'sub_syndication', array('Article_Directory_Private', 'syndication_page'));

			

			add_submenu_page(__FILE__, 'Content Dragon Account', 'Account', 8, 'sub_account', array('Article_Directory_Private', 'account'));

			add_submenu_page(__FILE__, 'View Content Dragon Projects', 'Projects', 8, 'sub_projects', array('Article_Directory_Private', 'projects'));

		}

  }

}





/** Widgets

**********************************************************************************

**********************************************************************************/

add_action("widgets_init", array('Article_Directory_Widgets', 'register'));

class Article_Directory_Widgets

{

  function control()

	{

    echo 'no options available.';

  }

	

	function sidebar_articles($args)

	{

		global $wpdb;

		extract($args);

		echo $before_widget;

		echo $before_title

		. '<a href="'.condrag_get_artlink().'">Recently Submitted Articles</a>'

		. $after_title;

		$query = mysql_query("SELECT * FROM `condrag_articles` WHERE `article_state`='1' AND `enabled`='1' ORDER BY `article_submitteddate` DESC LIMIT 10;");

		if (mysql_num_rows($query)>0)

		{

			echo '<ul>';

			while($row=mysql_fetch_assoc($query))

			{

				$title = stripslashes(stripslashes($row['article_title']));

				echo '<li><a href="'.condrag_get_artlink($row['article_id']).'" title="'.$title.'">'.$title.'</a></li>';

			}

			echo '</ul>';

		}

		$perma = get_option("permalink_structure");

		echo '<ul>';

		echo '<li><a href="'.condrag_get_artlink().(($perma == '')?'&amp;display=submit':'?display=submit').'"><strong>Submit Your Own</strong></a></li>';

		echo '<li><a href="'.condrag_get_artlink().'"><strong>Article Directory</strong></a></li>';

		echo '</ul>';	

		echo $after_widget;

	}

	

	function sidebar_categories($args)

	{

		extract($args);

		echo $before_widget;

		echo $before_title

		. '<a href="'.condrag_get_artlink().'">Article Categories</a>'

		. $after_title;

		$catid = (int)$_GET['catid'];

		$subcatid = (int)$_GET['subcatid'];

		$icon = "<img src=\"/wp-content/plugins/article-directory-script/images/folderfile.gif\" align=\"middle\" alt=\"\" />";

		if ($catid > 0)

		{

			$catinfo = condrag_is_category($catid);

			echo '<br />'.$icon.' <a href="'.condrag_get_artlink().'"><strong>'.$catinfo['category_title'].'</strong></a><br />';

		}

		$cats = condrag_category_display($catid);

		if (is_array($cats['rows']))

		{

			echo '<ul>';

			foreach ($cats['rows'] as $cat)

			{

				$style = ($cat[2]==$catid||$cat[2]==$subcatid)?' style="font-weight: bold;"':NULL;

				echo '<li><a href="'.$cat[0].'" title="'.$cat[1].'"'.$style.'>'.$cat[1].'</a></li>';

			}

			echo '</ul>';

		}

		echo $after_widget;

	}

	

  function register()

	{

		if (get_option("condrag_auth_code") == '') return;

    register_sidebar_widget('Article Categories', array('Article_Directory_Widgets', 'sidebar_categories'));

		register_sidebar_widget('Recent Articles', array('Article_Directory_Widgets', 'sidebar_articles'));

    register_widget_control('Article Directory', array('Article_Directory_Widgets', 'control')); 

  }

}





/** Browse / Syndication / Submit

**********************************************************************************

**********************************************************************************/

add_action("init", array('Article_Directory_Public', 'register'));

add_action('wp_head', array('Article_Directory_Public', 'head'));

class Article_Directory_Public 

{

	var $auth_code;

	var $article_page_id;

	

  function head()

	{

		if ( defined( 'WP_PLUGIN_URL' ) )

		{

			echo '<link rel="stylesheet" href="' . WP_PLUGIN_URL 

			. str_replace( str_replace( '\\', '/', WP_PLUGIN_DIR ), '', str_replace( '\\', '/', dirname( __file__ ) ) ) 

			. '/style.css" type="text/css" media="all" />' . "\n";

		} else {

			echo '<link rel="stylesheet" href="' . get_option('siteurl') . '/' 

			. str_replace( ABSPATH, '', dirname( __file__ ) ) 

			. '/style.css" type="text/css" media="all" />' . "\n";

		}

  }

	

	function category_select($article_categoryid)

	{

		global $wpdb;

		$sql = mysql_query("SELECT * FROM `condrag_categories` WHERE `category_parentid`='' AND `active`='1' ORDER BY `category_title` ASC;");								

		$html = "<select name=\"article_categoryid\" id=\"article_categoryid\">

		<option value=\"\">Select one...</option>";

		while ($row = mysql_fetch_array($sql))

		{

			$sql2 = mysql_query("SELECT * FROM `condrag_categories` WHERE `category_parentid`='{$row['category_id']}' AND `active`='1' ORDER BY `category_title` ASC;");

			while ($row2 = mysql_fetch_array($sql2))

			$html .= '<option value="'.$row2['category_id'].'" '.(($article_categoryid==$row2['category_id'])?"selected":false).'>'.$row['category_title'].' : '.$row2['category_title'].'</option>';

		}

		$html .= "</select>";

		return($html);

	}

	

	

	function type_select($article_typeid)

	{

		global $wpdb;

		$sql = mysql_query("SELECT * FROM `condrag_article_type` ORDER BY `title` ASC;");								

		$html = "<select name=\"article_typeid\" id=\"article_typeid\">

		<option value=\"\">Select one...</option>";

		while ($row = mysql_fetch_array($sql))

		{

			$html .= '<option value="'.$row['id'].'" '.(($article_typeid==$row['id'])?"selected":false).'>'.$row['title'].'</option>';

		}

		$html .= "</select>";

		return($html);

	}

	

	function submit()

	{

		global $wpdb;

		$showform = true;

		if ( isset($_GET['verify']) && isset($_GET['id']) && is_numeric($_GET['id']))

		{

			$code = $_GET['verify'];

			$id = $_GET['id'];

			$query = mysql_query("SELECT * FROM `condrag_articles` WHERE `code`='$code' AND `article_id`='$id';");

			if (mysql_num_rows($query)>0)

			{

				$showform = false;

				mysql_query("UPDATE `condrag_articles` SET `article_state`='0' WHERE `article_id`='$id';");

				mysql_query("UPDATE `condrag_article_approval_requests` SET `do_approve`='1' WHERE `article_id`='$id';");

				$out = '<div class="wrap">';

				$out .= '<h2>E-mail address successfully verified</h2><p>

				Thank you for verifying your email, your article will now viewed by the administrator and will decide if your article should be approved shortly, you will receieve an email if they do approve your article.</p>';

				$out .= '</div>';

			}

		}

		

		if ($showform)

		{

			$step = 1;

			$success_display = $error_display = "";

			if ( isset ( $_POST['submit_article'] ) )

			{

				$obj = condrag_manage_article_listing(0);

				$error_display = $obj['error_display'];

				$article_id = $obj['article_id'];

				$code = $obj['code'];

			}

			

			if (is_bool($error_display) && $error_display === true)

			{

				$step = 2;

				$error_display = "";

				unset($error_display);

				if ( isset ( $_POST['save_article'] ) ) $success_display = "Your article has been successfully submitted.";

				else if ( isset ( $_POST['submit_article'] ) ) $success_display = "You have successfully submitted your article and it is now up for approval.";

				

				if ($success_display != '')

				{

					@mysql_close();

					$web = 'http://'.$_SERVER['HTTP_HOST'].'/'.condrag_get_artlink();

					if (ereg('page_id=', $web)) $web .= '&amp;'; else $web .= '?';

					$web .= 'display=submit&amp;code='.$code.'&amp;id='.$article_id;

					echo '<script language="javascript">window.location=\''.$web.'\';</script>';

					

					

					//header("Location: ".condrag_get_artlink()."&display=submit&id=".$article_id.'&code='.$code);

					exit;

				}

			}

			else

			{

				$success_display = "";

			}

			

			$show_form = true;

			if ($_GET['id']!=''&&$_GET['code']!=''&&is_numeric($_GET['id']))

			{

				$show_form = false;

				$article_id = $_GET['id'];

				$blogname = get_bloginfo('name');

				$contentdragon_uname = get_bloginfo('contentdragon_uname');

				$artinfo = condrag_is_article($article_id);

				

				//echo "<br />{$artinfo['code']}<br />{$_GET['code']}<br />";

				$code = $_GET['code'];

				if (substr($code,-1)=='/')$code = substr($code, 0, -1);

				if ($artinfo['code'] != $code)

				$show_form = true;

				else

				{

					$template = file_get_contents(dirname(__FILE__)."/submit-public-syndicate.tpl");

					$type_row = mysql_fetch_assoc(mysql_query("SELECT * FROM `condrag_article_type` WHERE `id`='{$artinfo['article_typeid']}';"));

					$type = $type_row['title'];

					$catinfo = condrag_is_category($artinfo['article_categoryid']);

					$cat = $catinfo['category_title'];

					if ($catinfo['category_parentid'] != 0)

					{

						$subcatinfo = condrag_is_category($catinfo['category_parentid']);

						$subcat = $cat;

						$cat = $subcatinfo['category_title'];

					}

					else

					{

						$subcat = "";

					}

					

					$condrag_syndication_message = stripslashes(html_entity_decode(get_option("condrag_syndication_message"), ENT_QUOTES));

					

					$condrag_syndication_message = str_replace(

						array('{article_title}', '{author_firstname}', '{author_lastname}', '{email}'),

						array($artinfo['article_title'], $artinfo['author_firstname'], $artinfo['author_lastname'], $artinfo['email']),

						$condrag_syndication_message

					);

					

					$htmllink = "http://www.contentdragon.com/article_directory_api.php?resource=synd_price";

					$synd_price = condrag_nolines(condrag_get_contents($htmllink, 0, NULL));

					$synd_price = '$'.number_format($synd_price, 2, '.', ',');

		

					$data = array (

						'success.msg' => $success_display,

						'error.msg' => $error_display,

						'blogname' => $blogname,

						'auth_code' => get_option("condrag_auth_code"),

						'contentdragon_uname' => $contentdragon_uname,

						'article_summary' => base64_encode(addslashes($artinfo['article_summary'])),

						'article_keywords' => base64_encode(addslashes($artinfo['article_keywords'])),

						'article_title2' => base64_encode(addslashes($artinfo['article_title'])),

						'article_title' => $artinfo['article_title'],

						'article_authorinfo' => base64_encode(addslashes($artinfo['article_authorinfo'])),

						'article_text' => base64_encode(addslashes($artinfo['article_text'])),

						'author_firstname' => addslashes($artinfo['author_firstname']),

						'author_lastname' => addslashes($artinfo['author_lastname']),

						'email' => addslashes($artinfo['email']),

						'type' => $type,

						'synd.price' => $synd_price,

						'condrag_syndication_message' => $condrag_syndication_message,

						'article_category' => $cat,

						'article_subcategory' => $subcat,

						'code'=>$artinfo['code'],

						'article_id' => $article_id,

					);

					foreach ($data as $k=>$v) $template = str_replace('{'.$k.'}', $v, $template);

					

					$out = '<div class="wrap">';

					$out .= $template;

					$out .= '</div>';

				}

			}

			

			

			if ($show_form)

			{

				if ($success_display != '') $success_display = '<div class="success_msg">'.$success_display.'</div>';

				if ($error_display != '') $error_display = '<div class="error_msg">'.$error_display.'</div>';				

				if ($type == "private") $file = "submit"; else $file = "submit-public";

				$template = file_get_contents(dirname(__FILE__)."/{$file}.tpl");

				$data = array (

					'success.msg' => $success_display,

					'error.msg' => $error_display,

					'auth_code' => get_option('condrag_auth_code'),

					'categories' => condrag_display_article_categories ( (($_GET['catid']!='')?$_GET['catid']:$_POST['article_categoryid']) ),

					'type' => condrag_display_article_types ( $_POST['article_typeid'] ),

					'author.firstname' => stripslashes($_POST['author_firstname']),

					'author.lastname' => stripslashes($_POST['author_lastname']),

					'email' => stripslashes($_POST['email']),

					'd.article_title' => stripslashes($_POST['article_title']),

					'd.article_keywords' => stripslashes($_POST['article_keywords']),

					'd.article_summary' => stripslashes($_POST['article_summary']),

					'd.article_text' => stripslashes($_POST['article_text']),

					'd.article_authorinfo' => stripslashes($_POST['article_authorinfo']),

					'action.value' => (($_GET['article_id']=="")?"create":"edit"),

					'd.article_id' => $_GET['article_id']

				);

				foreach ($data as $k=>$v) $template = str_replace('{'.$k.'}', $v, $template);

				

				$out = '<div class="wrap">';

				$out .= '<h2>'.(($type=='private')?'<a href="?page=article-directory-script/article_directory.php">Article Directory</a> &raquo; ':NULL).'Submit Article Form</h2>';

				$out .= $template;

				$out .= '</div>';

			}

		}

		if ($type=="private")echo $out; else return($out);

	}

	

	function browse($text)

	{

		global $wpdb;

		$level = "top";

		$pagename = $categoryname = $subcategoryname = $rss_feed_url = "";

		$category_parentid = 0;

		$pagenumber = ($_GET['pagenumber']!=''&&is_numeric($_GET['pagenumber']))?$_GET['pagenumber']:1;

		$base_url = condrag_get_artlink();

		$stl = $base_url;

		$submit_link = $base_url;

		

		

		

		$perma = get_option("permalink_structure");

		$article_page_id = get_option("article_page_id");

		$navigation = "<br />&raquo; <a href=\"{$stl}\">Article Directory</a>";

		$level = "top";

		if ($perma == '')

		{

			//if ($_GET['page_id']==$article_page_id) $valid = true;

			$catid = $_GET['catid'];

			$subcatid = $_GET['subcatid'];

			$artid = $_GET['artid'];

			$display = $_GET['display'];

			

			if ($catid != '' && is_numeric($catid))

			{

				$stl .= "&catid=$catid";

				$level = "category";

				$catinfo = condrag_is_category($catid);

				if ($subcatid != '' && is_numeric($subcatid))

				{

					$stl .= "&amp;subcatid=$subcatid";

					$level = "subcategory";

					$submit_link .= "&amp;catid=$subcatid";

					$subcatinfo = condrag_is_category($subcatid);

				}

				else

				{

					$submit_link .= "&amp;catid=$catid";

				}

				if ($artid != '' && is_numeric($artid))

				{

					$artinfo = condrag_is_article($artid);

					$stl .= "&amp;artid=$artid";

					$level = "display";

				}

			}

		}

		else

		{

			$dir = getenv("REQUEST_URI");

			$dir = split("/", $dir);

			//$p = get_page($article_page_id);

			$pages = array();

			foreach ($dir as $d)

			{ 

				if ($d == "") continue;

				$pages[] = $d;

			}

			

			$last_page = $pages[count($pages)-1];

			if (substr($last_page, 0, 4) == "page")

			{

				$pagenumber = (int)substr($last_page, 5);

				if ($pagenumber <= 0) $pagenumber = 1;

				$last_page = $pages[count($pages)-2];

			}

			

			if ($last_page == "top" || $last_page == "sub" || $last_page == "display" || $last_page == "publish")

			{

				$thecatid = 0;

				if ($last_page == "top" || $last_page == "sub" || $last_page == "display" || $last_page == "publish")

				{

					$tp = $pages[1];

					$level = "category";

					$query = mysql_query("SELECT * FROM `condrag_categories` WHERE `category_urltitle`='$tp';");

					$catinfo = mysql_fetch_assoc($query); 

					$catid = $thecatid = $catinfo['category_id'];

					if ($last_page == "top") $submit_link .= "?catid={$catinfo['category_id']}";

					$stl .= $tp.'/';

				}

				

				if ($last_page == "sub" || $last_page == "display" || $last_page == "publish")

				{

					$tp = $pages[2];

					$level = "subcategory";

					$query = mysql_query("SELECT * FROM `condrag_categories` WHERE `category_parentid`='$catid' AND `category_urltitle`='$tp';");

					$subcatinfo = mysql_fetch_assoc($query); 

					$subcatid = $thecatid = $subcatinfo['category_id'];

					if ($last_page == "sub") $submit_link .= "?catid={$subcatinfo['category_id']}";

					$stl .= $tp.'/';

				}

				

				

				if ($last_page == "display" || $last_page == "publish")

				{

					$tp = $pages[3];

					$level = "display";

					$query = mysql_query("SELECT * FROM `condrag_articles` WHERE `article_categoryid`='$thecatid' AND `article_urltitle`='$tp';");

					$artinfo = mysql_fetch_assoc($query); 

					$artid = $artinfo['article_id'];

					$stl .= $tp.'/';

					if ($last_page == "publish")

					{

						$display = "publish";

						$last_page = "display";

					}

				}

				

				$stl .= $last_page.'/';

			}

		}

		$artinfo['article_title'] = stripslashes(stripslashes($artinfo['article_title']));

		$submit_link .= (($perma==''||strpos($submit_link,'?catid')>0)?"&amp;display=submit":"?display=submit");

		

		if ($level == "top")

		{

		

		}

		else if ($level == "category")

		{

			

			$category_parentid = $catinfo['category_id'];

			$categoryname = $catinfo['category_title'];

			$pagename = "{$catinfo['category_title']} Articles";

			$navigation .= " &raquo; {$catinfo['category_title']}";

			$meta_description = "Search {$catinfo['category_title']} Articles and News from ContentDragon.com {$catinfo['category_title']} writers grow their exposure by submitting quality original articles to our article directory";

			$meta_keywords = "{$catinfo['category_title']}, {$catinfo['category_title']} articles, {$catinfo['category_title']} article directory, {$catinfo['category_title']} content, {$catinfo['category_title']} freelance writer, {$catinfo['category_title']} writer, {$catinfo['category_title']} publisher, {$catinfo['category_title']} article publisher";

		}

		else if ($level == "subcategory")

		{

			$categoryname = $catinfo['category_title'];

			$subcategoryname = $subcatinfo['category_title'];

			$category_parentid = $subcatinfo['category_id'];

			$pagename = "{$subcatinfo['category_title']} Articles";

			if ($perma == '')

			{

				$navigation .= " &raquo; <a href=\"{$base_url}&amp;catid=$catid\">{$catinfo['category_title']}</a>";

			}

			else

			{

				$navigation .= " &raquo; <a href=\"{$base_url}{$catinfo['category_urltitle']}/top/\">{$catinfo['category_title']}</a>";

			}

			$navigation .= " &raquo; {$subcatinfo['category_title']}";

			$meta_description = "Search {$subcatinfo['category_title']} Articles and News from ContentDragon.com {$subcatinfo['category_title']} writers grow their exposure by submitting quality original articles to our article directory";

			$meta_keywords = "{$subcatinfo['category_title']}, {$subcatinfo['category_title']} articles, {$subcatinfo['category_title']} article directory, {$subcatinfo['category_title']} content, {$subcatinfo['category_title']} freelance writer, {$subcatinfo['category_title']} writer, {$subcatinfo['category_title']} publisher, {$subcatinfo['category_title']} article publisher";

		}

		else if ($level == "display")

		{

			$article_id = $artinfo['article_id'];

			$categoryname = $catinfo['category_title'];

			

			if ($subcatid != '' && is_numeric($subcatid))

			{

				$subcategoryname = $subcatinfo['category_title'];

				$category_parentid = $subcatinfo['category_id'];	

				$pagename = condrag_utf8encode($artinfo['article_title'])." in {$subcatinfo['category_title']} Articles";

				if ($perma == '')

				{

					$navigation .= " &raquo; <a href=\"{$base_url}&amp;catid=$catid\">{$catinfo['category_title']}</a>";

					$navigation .= " &raquo; <a href=\"{$base_url}&amp;catid=$catid&amp;subcatid=$subcatid\">{$subcatinfo['category_title']}</a>";

				}

				else

				{

					$navigation .= " &raquo; <a href=\"{$base_url}{$catinfo['category_urltitle']}/top/\">{$catinfo['category_title']}</a>";

					$navigation .= " &raquo; <a href=\"{$base_url}{$catinfo['category_urltitle']}/{$subcatinfo['category_urltitle']}/sub/\">{$subcatinfo['category_title']}</a>";

				}

			}

			else

			{

				$pagename = condrag_utf8encode($artinfo['article_title'])." in {$catinfo['category_title']} Articles";// on Page $pagenumber for articles";

				if ($perma == '')

				{

					$navigation .= " &raquo; <a href=\"{$base_url}&amp;catid=$catid\">{$catinfo['category_title']}</a>";

				}

				else

				{

					$navigation .= " &raquo; <a href=\"{$base_url}{$catinfo['category_urltitle']}/top/\">{$catinfo['category_title']}</a>";

				}

			}

			

			if ($display == "publish")

			{

				if ($perma == '')

				{

					$navigation .= " &raquo; <a href=\"{$stl}&amp;catid=$catid&amp;subcatid=$subcatid&amp;artid=$artid\">".condrag_utf8encode($artinfo['article_title'])."</a>";

				}

				else

				{

					$navigation .= " &raquo; <a href=\"".condrag_get_artlink($artid)."\">".condrag_utf8encode($artinfo['article_title'])."</a>";

				}

				$display = "publish";

			}

		}

		

		$page = $cat_list = '';

		$subcatids = array($category_parentid);

		if ($level == "top" || $level == "category")

		{

			$cats = condrag_category_display($category_parentid);

			if (is_array($cats['rows']))

			{

				foreach ($cats['rows'] as $cat)

				{

					$subcatids[] = $cat[2];

					$value = "<img src=\"/wp-content/plugins/article-directory-script/images/folderfile.gif\" align=\"middle\" alt=\"\" /> "

					. "<a href=\"{$cat[0]}\" title=\"{$cat[1]}\">{$cat[1]}</a>";

					$cat_list .= "<div>$value</div>\n";

				}

			}

		}

		if (count($subcatids)>0)

		$subcatids = "AND (`article_categoryid`='".implode("' OR `article_categoryid`='", $subcatids)."')";

		else $subcatids = '';

		

		

		if ($level != "display") $text = 'Submit an article to this category';

		

		else $text = 'Submit an article';

		

		$navigation .= " &raquo; <a href=\"$submit_link\">$text</a>";

		

		$limit = 30;

		$sort = "`article_id` DESC";

		

		if ($level == "category" || $level == "subcategory")

		$where = "WHERE `article_state`='1' $subcatids"; //AND `article_categoryid`='$category_parentid' 

		else if ($level == "display")

		$where = "WHERE `article_state`='1' AND `article_id`='$article_id'";

		else if ($level == "search")

		$where = "WHERE `article_state`='1' AND $searchquery";

		else $where = "WHERE `article_state`='1'";

		

		

		$olstart = (int)($pagenumber == 1)?1:($pagenumber*$limit-$limit);

		

		$page_menu = $page_stat = "";

		$sql = "SELECT * FROM `condrag_articles` $where AND `enabled`='1' ORDER BY $sort";

		if ($level != "display" && $level != "top")

		{

			$pageCount = mysql_num_rows(mysql_query($sql));

			if ($pageCount == 0) $offset = 0;

			else

			{

				$pagenumber2 = ceil($pageCount / $limit);

				$pagenumber = max($pagenumber, 1);

				$pagenumber = min($pagenumber, $pagenumber2);

				$offset = ($pagenumber - 1) * $limit;

			}

			$sql .= " LIMIT $offset, $limit;";

			

			$query = mysql_query($sql);

			$count = mysql_num_rows($query);

			if ($count == 0){

				$count1 = 0;

				$count2 = 0;

			}else{

				$count2 = ($count+$offset);

				if ($pagenumber == 1){ $count1=0; }else{

					$count1 = $offset;

				}

			}

			if ($pagenumber < $pagenumber2 && $pagenumber != $pagenumber2){

				$q = $pagenumber+1;

				if ($perma == '') $u = $stl."&amp;page=$q";

				else $u = $stl."page-$q/";

				$next = " <a href=\"$u\"><b>Next &rsaquo;</b></a>";

			}

			if ($pagenumber <= $pagenumber2 && $pagenumber != 1){

				$w = $pagenumber-1;

				if ($perma == '') $u = $stl."&amp;page=$w";

				else $u = $stl."page-$w/";

				$prev = "<a href=\"$u\"><b>&lsaquo; Prev</b></a> ";

			}

			$pages = "";

			for ($i=0;$i<$pagenumber2;$i++){

				$z = $i+1;

				if ($perma == '') $u = $stl."&amp;page=$z";

				else $u = $stl."page-$z/";

				if ($pagenumber == $z){

					$pages .= "<span class=\"active_tnt_link\">Page ".$z."</span> ";

				}else{

					$pages .= "<a href=\"$u\">Page $z</a> ";

				}

			}

			

			$first = $last = "";

			if ($pagenumber > 1) $first = "<a href=\"$stl\"><b>&laquo; First</b></a> ";

			if ($pagenumber < $pagenumber2) $last = " <a href=\"{$stl}".(($perma=='')?"&amp;page=$pagenumber2":"page-$pagenumber2/")."\"><b>Last &raquo;</b></a>";

			

			if ($pagenumber2 == "") $pagenumber2 = 1;

			$catnames = $categoryname.(($subcategoryname != "")?", $subcategoryname":"")." ";

			$page_stat = "Displaying <b>$count1-$count2</b> of <b>$pageCount</b> total {$catnames} related articles"

			. " on page <b>$pagenumber</b> of <b>$pagenumber2</b>";

			$page_menu = "<div class=\"pagination\">{$first}{$prev}{$pages}{$next}{$last}</div>";

		}

		else

		{

			$sql .= " LIMIT 30;";

			$query = mysql_query($sql);

		}

		

		

		

		$rows = ""; $i=$olstart-1; $reset=0;
		
		
		$article_count = mysql_num_rows(mysql_query("SELECT * FROM `condrag_articles` WHERE `enabled`='1' LIMIT 1;"));

		while ($row = mysql_fetch_assoc($query))

		{

			$i++; $reset++;

			extract($row);

			

			$article_title = condrag_utf8encode($article_title);

			$article_title = stripslashes(stripslashes($article_title));

			$article_text = condrag_utf8encode($article_text);

			$article_text = html_entity_decode($article_text, ENT_QUOTES);

			$article_authorinfo = stripslashes($article_authorinfo);

			

			if (!preg_match("/<.*?>/i", $article_text))

			{

				$article_text = nl2br($article_text);

			}

			

			$article_text = stripslashes($article_text);

			$article_authorinfo = stripslashes($article_authorinfo);	

			/*

			$reg = "/(http|ftp)+(s)?:(\/\/)((\w|\.)+)(\/)?(\S+)?/i";

			preg_match($reg, $article_authorinfo, $authurl);

			if ($authurl[0] != '')

			{

				$article_authorinfo = str_replace($authurl[0], '<a href="'.$authurl[0].'" target="_blank">'.$authurl[0].'</a>', $article_authorinfo);

			}

			*/

			$article_text = stripslashes(stripslashes($article_text));

			$article_summary = stripslashes(stripslashes($article_summary));

			$article_authorinfo = stripslashes($article_authorinfo);

			//$article_text = utf8_encode($article_text);

			$text = strip_tags($article_text);

			

			$front = substr($text, 0, 500);

			$end = substr($text, 500, strpos(substr($text, 500), ".")+1)." ...";

			$text = $front.$end;

			

			preg_match_all("/(<a\shref=[\"\'](.*?)[\"\'].*?>(.*?)<\/a>)/i", $article_authorinfo, $urls);

			if (count($urls[0])>0)

			{

				for ($ii=0; $ii<count($urls[0]); $ii++)

				{

					$all = $urls[0][$ii];

					$href = $urls[2][$ii];

					//$text2 = $urls[3][$ii];

					if (substr($href, 0, 7)!='http://')

					{

						if (substr($href, 0, 4) == 'www.') $href='http://'.$href;

						else if (eregi('www.', $href))

						{

							$href = 'http://'.substr($href, strpos($href, 'www.'));

						}

						else $href = 'http://'.$href;

						$article_authorinfo = str_replace($all, '<a href="'.$href.'" target="_blank" rel="NOFOLLOW">'.$href.'</a>', $article_authorinfo);

					}

					else if (!preg_match("/target=[\'\"]_blank[\"\']/i", $all))

					{

						$article_authorinfo = str_replace($all, str_replace("href=", "target=\"_blank\" rel=\"NOFOLLOW\" href=", $all), $article_authorinfo);

					}

				}

			}

		

			$catinfo = is_category($article_categoryid);

			if ($catinfo['category_parentid'] != 0)

			{

				$subcatinfo = is_category($catinfo['category_parentid']);

			}

			

			$url = condrag_get_artlink($article_id);

			

			

			if ($level == "top")

			$rows .= "<br /><div class=\"article_recent\"><p><a href=\"$url\" title=\"$article_title\">$article_title</a> - <span class=\"grey\">$text</span></p></div>";

			else if ($level != "display")

			$rows .= "<br /><a href=\"$url\" title=\"$article_title\">$article_title</a><br />$text<br /><br />";

			else

			{

				$full_article_url = str_replace('//', '/', "http://".$_SERVER['HTTP_HOST'].'/'.$url);

				

				$full_author_url = $full_article_url;

				$stumble_url = urlencode("http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);

				

				if ($display != "print")

				{

					if ($perma == '')

					{

						$puburl = $stl .= '&amp;display=publish';

					}

					else

					{

						$puburl = str_replace('/display/', '/publish/', $stl);

					}

					$article_options_panel = "<br />"

					. "<a href=\"http://www.stumbleupon.com/submit?url=$stumble_url\" rel=\"nofollow\" target=\"_blank\">"

					. "<img src=\"/wp-content/plugins/article-directory-script/images/stumbleupon_16.gif\" alt=\"Stumble It!\" border=0 align=\"middle\"> Stumble It!</a>"

					. "&nbsp;"

					. "<a href=\"javascript:void(0);\" onclick=\"condrag_printarticle('".addslashes($article_title)."');\">"

					. "<img src=\"/wp-content/plugins/article-directory-script/images/printer.png\" alt=\"Print this article\" border=0 align=\"middle\"> Print It!</a>"

					. "&nbsp;"

					. "<a href=\"{$puburl}\" rel=\"nofollow\">"

					. "<img src=\"/wp-content/plugins/article-directory-script/images/publisher.png\" alt=\"Ezine Publisher\" border=0 align=\"middle\"> Ezine Publisher</a>"

					. "&nbsp;"

					. "<a href=\"javascript:void(0);\" onclick=\"if(window.sidebar){window.sidebar.addPanel(document.title,location.href,'');}"

					. "else{window.external.AddFavorite(location.href,document.title);}\" rel=\"nofollow\">"

					. "<img src=\"/wp-content/plugins/article-directory-script/images/bookmark_add.png\" alt=\"Add to favorites\" border=0 align=\"middle\"> Add to favorites</a>"

					

					. "&nbsp;"

					. "<a href=\"$submit_link\">"

					. "<img src=\"/wp-content/plugins/article-directory-script/images/document.png\" alt=\"Submit an Article\" border=0 align=\"middle\"> Submit an Article</a>";

				}

				

				if ($memberinfo!='')

				{

					$memberinfo = explode(":",$memberinfo);

					

					$by = $memberinfo[1];

					

					$article_source_html = "Article Source:<br />\n"

					. "Author - <a href=\"http://members.contentdragon.com/community/space/".$memberinfo[1]."\">{$memberinfo[0]}</a><br />\n"

					. "Location - <a href=\"$full_article_url\">$full_article_url</a>";

					

					$article_source_plain = "Article Source:\n"

					. "Author - {$memberinfo[0]} [ http://members.contentdragon.com/community/space/".$memberinfo[1]."/ ]\n"

					. "Location - $full_article_url";

				}

				else

				{

					$by = "$author_firstname $author_lastname";

					$article_source_html = "Article Source:<br />\n"

					. "Author - $author_firstname $author_lastname<br />\n"

					. "Location - <a href=\"$full_article_url\">$full_article_url</a>";

					

					$article_source_plain = "Article Source:\n"

					. "Author - $author_firstname $author_lastname \n"

					. "Location - $full_article_url";

				}

				

				$word_count = str_word_count(strip_tags(strtolower($article_text)));

				if ($display == "publish")

				{

					$rows = "<div class=\"article_title\">$article_title</div>"

					. "<p>You have permission to publish this article electronically or in print, free of charge, as long as you leave the article title, author name, body and resource box in tact (means NO changes) with the links made active and you agree to our posted publisher terms of service.<br /><br />

				Use the tools below to copy the article in plain text form, or you can copy it as HTML, ready to copy and paste directly into a web page.</p>";

				

					if ($article_summary != "")

					$rows .= "<p><strong>Article Summary</strong><br /><textarea style=\"width: 100%; height: 75px;\" readonly=\"readonly\" onclick=\"this.select();\">"

					. "$article_summary</textarea></p>";

					

					if (!eregi("<br>", $article_text) || !eregi("<br />", $article_text))

					$source = strip_tags(str_replace("<p>", "\n\n", $article_text));

					else $source = strip_tags(str_replace("<br />", "\n", str_replace("<br>", "\n", $article_text)));

					

					

					$rows .= "<p><strong>Plain Text Version</strong><br /><textarea style=\"width: 100%; height: 250px;\" readonly=\"readonly\" onclick=\"this.select();\">"

					. "$article_title\nBy: $by\n\n".$source."\n\n$article_source_plain</textarea></p>"

					

					. "<p><strong>HTML Version</strong><br /><textarea style=\"width: 100%; height: 250px;\" readonly=\"readonly\" onclick=\"this.select();\">"

					. "<h1>$article_title</h1><br />\nBy: $by<br /><br />\n\n$article_text<br /><br />\n\n$article_source_html</textarea></p>";

					

					if ($article_keywords != "")

					$rows .= "<p><strong>Article Keywords</strong><br />"

					. "<input type=\"text\" style=\"width: 100%;\" readonly=\"readonly\" onclick=\"this.select();\" value=\"$article_keywords\"></p>";

					

					$rows .= "<p><strong>Article URL</strong><br />"

					. "<input type=\"text\" style=\"width: 100%;\" readonly=\"readonly\" onclick=\"this.select();\" value=\"$full_article_url\"></p>"

					

					. "<p><strong>About the author</strong>:<br />$article_authorinfo</p>"

					. "<p><strong>Article Word Count</strong>: $word_count</p>";

				}

				else

				{

					

					if (!eregi("<br>", $article_text) && !eregi("<br />", $article_text)) $article_text = str_replace("\n\n", "<br /><br />", $article_text);

					

					$rows = "<div id=\"article_display_container\">

					<div class=\"article_title\">$article_title</div>"

					. (($article_modifydate!='0000-00-00 00:00:00')?"<div>Last modified on: ".$article_modifydate."</div>":NULL)

					. '<div id="options_panel">'.$article_options_panel.'</div>';

					

					$rows .= "<p>By: $author_firstname $author_lastname</p>"

					. "<p>{$article_text}</p>"

					. "<p><strong>About the author</strong>:<br />$article_authorinfo</p>"

					. "<p><strong>Article Word Count</strong>: $word_count</p></div><div class=\"clear\"></div>";

					

				}

			}

		}

	
		$page = '
		<div class="article_directory">
			<div class="article_navigation">'.$navigation.'</div>
			<div class="category_block">
				<div class="category_container">'.$cat_list.'</div>
				
				<div style="clear: both;"></div>
			</div>
			
			<div class="article_list">';
			
			if ($article_count == 0) $page .= '<div class="no-articles">This article directory does not have any published articles.<br />Check back soon or <a href="'.$submit_link.'"><b>Submit Your Own!</b></a></div>';
			else
			{
	
				if ($level == "top" || $level == "display") $page .= $rows;
					
				else 
					
				{
				
					if ($display != "print") $page .= '<div class="page_stat">'.$page_stat.'</div>';
						
					$page .= $rows.$page_menu;
	
				}
			
			}
		
			$page .= '</div>
		</div>';
		
		
		// for print this article link
		if ($level == "display")

		$page = $page."\n\n\n".'<script language="javascript">

		function condrag_printarticle(printTitle)

		{

			var delimiter="\n";

			var printContent = document.getElementById("article_display_container").innerHTML;	

			

			//header

			var printHead="";

			printHead+="<html>\n";

			printHead+="<head>\n";

			printHead+="<title>"+printTitle+"</title>\n";

			printHead+="<style>\n";

			printHead+="td, th, body{\n";

			printHead+="font-size: 12px;\n font-family: Arial;";

			printHead+="}\n";

			printHead+="</style>\n";

			printHead+="</head>\n";

			printHead+="<body topmargin=\'0\' leftmargin=\'0\'>";

			

			printContent="<div id=\"printContent\">"+printContent+"</div>";

			

			//footer

			var printFoot="";

			printFoot+="</body>\n";

			printFoot+="</html>\n";

			

			printWindow = window.open("","printWindow");

			printWindow.document.write(printHead+printContent+printFoot);

			printWindow.document.getElementById("options_panel").style.display = "none";

			printWindow.document.close();

			printWindow.print();

		}

		</script>';
		
		
		
		

		// Quick fix for encoding.

		if (function_exists("iconv")) $page = iconv("UTF-8","UTF-8//IGNORE",$page);
		
		//dirname(__FILE__)."/page.php"
		
		$stylesheet_url = 'http://'.$_SERVER['HTTP_HOST'].'/'.substr(dirname(__FILE__), strpos(dirname(__FILE__), 'wp-content')).'/style.css';
		
		$page = '<link href="'.$stylesheet_url.'" rel="stylesheet" type="text/css" />'
		
		. $page
		
		. '<!-- you must leave this intact! -->

        <div class="article_directory_copyright"><a href="http://contentdragon.com">Article Directory</a> - Powered by Content Dragon</div>

        <!-- // -->';
		
		
		return $page;

	}

	

	function wp_title($text)

	{

		$title = " - Article Directory";

		$perma = get_option("permalink_structure");

		$article_page_id = get_option("article_page_id");

		if ($perma == '')

		{

			if ($_GET['artid']!='')

			{

				$artinfo = condrag_is_article($_GET['artid']);

				$_title = stripslashes(stripslashes($artinfo['article_title']));

				$title = ' - '.$_title.$title;

			}

		}

		else

		{

			$dir = getenv("REQUEST_URI");

			$dir = split("/", $dir);

			//$p = get_page($article_page_id);

			$pages = array();

			foreach ($dir as $d)

			{ 

				if ($d == "") continue;

				$pages[] = $d;

			}

			$last_page = $pages[count($pages)-1];

			

			if ($last_page == "display")

			{

				$tp = $pages[3];

				$level = "display";

				$query = mysql_query("SELECT * FROM `condrag_articles` WHERE `article_urltitle`='$tp';");

				$artinfo = mysql_fetch_assoc($query); 

				$_title = stripslashes(stripslashes($artinfo['article_title']));

				$title = ' - '.$_title.$title;

			}

		}

		

		

		

		return($title);

	}

	function replace_title($text)

	{

		//echo $text;

		return($text);

	}

	

	function limit_1_post($limit) {

		return 'LIMIT 1';

	}

	

	function replace_template() {

		//include(dirname(__FILE__)."/page.php");
		include(TEMPLATEPATH . "/page.php");
		exit;

	}

	

	function posts_where($where)

	{

		

		$article_page_id = get_option("article_page_id");

		$where = "AND (wp_posts.ID = '".$article_page_id."') AND wp_posts.post_type = 'page'";

		return($where);

	}

	

  function register()

	{

		$auth_code = get_option("condrag_auth_code");

		if ($auth_code == '') return;

		

		/* Process Syndication from ContentDragon.com

		* Only allow if the proper auth code is sent.

		*****************************************************************/

		if ($_GET['syndication_update']==1&&$auth_code==$_POST['auth_code'])

		{

			condrag_syndication();

			// this will be accessed by a script, no need to waste bandwidth so we kill the page

			// before it can send any output to the browser.

			mysql_close();

			exit();

		}

		

		/* Serve up the page

		*****************************************************************/

		$valid = false;

		$perma = get_option("permalink_structure");

		$article_page_id = get_option("article_page_id");

		if ($perma == '')

		{

			if ($_GET['page_id']==$article_page_id) $valid = true;

		}

		else

		{

			$p = get_page($article_page_id);

			$dir = getenv("REQUEST_URI");

			$dir = split("/", $dir);

			$pages = array();

			foreach ($dir as $d)

			{ 

				if ($d == "") continue;

				if (preg_match('/[\?]/i', $d)) $d = substr($d, 0, strpos($d, '?'));

				$pages[] = $d;

			}

			//if (strpos($pages[0],$p->post_name)!=-1){ $valid = true; }

			if (in_array($p->post_name, $pages)){ $valid = true; }

		}



		if ($valid)

		{

			//query_posts('p='.$article_page_id);

			add_filter('posts_where', array('Article_Directory_Public', 'posts_where'));

			if ($_GET['display']=='submit') add_filter('the_content', array('Article_Directory_Public', 'submit'));

			else add_filter('the_content', array('Article_Directory_Public', 'browse'));

			

			add_filter('post_limits', array('Article_Directory_Public', 'limit_1_post'));

			add_filter('the_title', array('Article_Directory_Public', 'replace_title'));

			add_filter('wp_title', array('Article_Directory_Public', 'wp_title'));

			add_action('template_redirect', array('Article_Directory_Public', 'replace_template'));

		}

  }

}

?>