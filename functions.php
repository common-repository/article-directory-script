<?php

function condrag_nolines($content) { return(preg_replace("/[\n\r\t]/i", "", $content)); }

function condrag_get_contents($url, $method, $post)

{

	$ch = curl_init();

	curl_setopt($ch, CURLOPT_URL, $url);

	curl_setopt($ch, CURLOPT_FAILONERROR, 1);

	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

	curl_setopt($ch, CURLOPT_HEADER, 0);

	curl_setopt($ch, CURLOPT_TIMEOUT, 25);

	curl_setopt($ch, CURLOPT_POST, $method);

	curl_setopt($ch, CURLOPT_FORBID_REUSE, 1);

	curl_setopt($ch, CURLOPT_VERBOSE, 0);

	curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);

	if ($method == 1){ curl_setopt($ch, CURLOPT_POSTFIELDS, $post); }

	$result = curl_exec($ch);

	curl_close($ch);

  return ($result);

}



function condrag_deformatURL($name)

{ return(trim(ucwords(preg_replace("/[\W\-]/", " ", urldecode($name))))); }



function condrag_formatURL($name)

{ return(trim(strtolower(preg_replace("/[\W\-]/", "-", urldecode($name))))); }



function condrag_finalFormatURL($name)

{ 

	$name = strtolower(trim(urldecode($name)));

	$name = preg_replace('/[#$^*<>.\/;:\'"+|`]/', "", $name);

	$name = preg_replace("/[\s]/", "-", $name);

	$name = preg_replace("/[\-]+/", "-", $name);

	return($name);

}



function condrag_utf8encode($input)

{

	$search = array('—', '–', '“', '‘', '’');

	$replace = array('-', '-', '"', '\'', '\'');

	$input = str_replace($search, $replace, $input);

	if (function_exists('mb_convert_encoding')) return(mb_convert_encoding($input, "UTF-8")); else return(utf8_encode($input));

}



function condrag_is_article($url)

{

	if (is_numeric($url)) $field = "article_id";

	else $field = "article_urltitle";

	$query = mysql_query("SELECT * FROM `condrag_articles` WHERE `$field`='$url';");

	$title_check = (int)mysql_num_rows($query);

	if ($title_check > 0) return(mysql_fetch_assoc($query)); else return(false);

}



function condrag_is_category($url)

{

	if (is_numeric($url)) $field = "category_id";

	else $field = "category_urltitle";

	$query = mysql_query("SELECT * FROM `condrag_categories` WHERE `$field`='$url';");

	$title_check = (int)mysql_num_rows($query);

	if ($title_check > 0) return(mysql_fetch_assoc($query)); else return(false);

}



function condrag_get_artlink($artid=0)

{

	$article_page_id = get_option("article_page_id");

	

	$perma = get_option("permalink_structure");

	if ($perma == '')

	{

		$link = "?page_id=".$article_page_id;

	}

	else

	{

		$p = get_page($article_page_id);

		$link = '/'.$p->post_name.'/';

	}

	

	

	if ($artid==0) return($link);

	

	$art = condrag_is_article($artid);

	$cat = condrag_is_category($art['article_categoryid']);

	if ($cat['category_parentid']>0)

	{

		$cat2 = condrag_is_category($cat['category_parentid']);

		if ($perma == '')

		{

			$link .= '&amp;catid='.$cat2['category_id'];

			$link .= '&amp;subcatid='.$cat['category_id'];

		}

		else

		{

			$link .= $cat2['category_urltitle'].'/';

			$link .= $cat['category_urltitle'].'/';

		}

	}

	else

	{

		if ($perma == '')

		{

			$link .= '&amp;catid='.$cat['category_id'];

		}

		else

		{

			$link .= $cat['category_urltitle'].'/';

		}

	}

	

	if ($perma == '')

	{

		$link .= '&amp;artid='.$artid;

	}

	else

	{

		$link .= $art['article_urltitle'].'/display/';

	}

	return($link);

}





function condrag_insert_contentpost($post_title, $post_content, $post_category)

{

	global $userdata;

  get_currentuserinfo();

	$post_author = $userdata->user_ID;

	$post_status = 'publish';

	$post_category = split("," , $post_category);

	foreach($post_category as $key=>$val) $post_category[$key] = get_cat_ID($val);

	

	$post_type = "page";

	$post_date = current_time('mysql');

	$post_date_gmt = current_time('mysql', 1);

	$post_data = compact('post_author', 'post_date', 'post_date_gmt', 'post_content', 'post_title', 'post_category', 'post_status', 'post_type');

	$post_ID = wp_insert_post($post_data);

	

	add_option('article_page_id', $post_ID);

	

	if (!$post_ID) return(false); else return($post_ID);

}



function condrag_category_display($category_parentid = 0)

{

	$cats = array();

	$total_categories = 0;

	$query = mysql_query("SELECT * FROM `condrag_categories` WHERE `category_parentid`='$category_parentid' AND `active`='1' ORDER BY `category_title` ASC;");

	while ($row = mysql_fetch_assoc($query))

	{

		extract($row);

		$category_sets = unserialize($category_sets);

		if ((int)$category_sets['article_count'] <= 0) continue;

		$total_categories++;

	}

	$cats['total'] = $total_categories;

	

	

	$article_page_id = get_option("article_page_id");

	$perma = get_option("permalink_structure");

	

	$topcat = NULL;

	if ($perma != '' && $category_parentid>0)

	{

		$topcat = condrag_is_category($category_parentid);

	}

	

	$query = mysql_query("SELECT * FROM `condrag_categories` WHERE `category_parentid`='$category_parentid' AND `active`='1' ORDER BY `category_title` ASC;");

	while ($row = mysql_fetch_assoc($query))

	{

		extract($row);

		$category_sets = unserialize($category_sets);

		$sub_category_count = $category_sets['sub_category_count']; // [x]

		$sub_category_count_inclusive = $category_sets['sub_category_count_inclusive']; // [x]

		$article_count = (int)$category_sets['article_count'];

		$article_count_inclusive = $category_sets['article_count_inclusive']; // [x]

		//if ($article_count <= 0) continue;

		$url = condrag_get_artlink();

		if ($perma == '')

		{

			if ($category_parentid > 0)

			$url .= "&amp;catid=$category_parentid&amp;subcatid=$category_id";

			else $url .= "&amp;catid=$category_id";

		}

		else

		{

			if ($category_parentid > 0)

			$url .= "{$topcat['category_urltitle']}/$category_urltitle/sub/";

			else $url .= "$category_urltitle/top/";

		}

		$cats['rows'][] = array($url, $category_title, $category_id);

	}

	return($cats);

}



function condrag_email($to_email, $to_name, $subject, $message, $from_email, $from_name)

{

	$text = $message;    

  $headers  = "";

  $headers .= "From: $from_name <$from_email>\n";

  $headers .= "Reply-To: $from_name <$from_email>\n";

  $headers .= "Date: ".date("r")."\n";

  $headers .= "Subject: $subject\n";

  $headers .= "Delivered-to: $to_name <$to_email>\n";

  $headers .= "MIME-Version: 1.0\n";

  $headers .= "Content-type: text/html;charset=utf-8\n";

  $headers .= "X-Mailer: PHP/" . phpversion();

  if (mail($to_email, $subject, $text, $headers)){

    return true;

  }else{

    return false;

  }

}



function condrag_generate_code($pass_len)

{ 

  $nps = "";  

  mt_srand ((double) microtime() * 1000000);  

  while (strlen($nps)<$pass_len) { 

    $c = chr(mt_rand (0,255));  

    if (ereg("^[0-9a-z]$", $c)) $nps = $nps.$c; 

  }

  return ($nps);  

}



/* Version Check

******************************************************/

function condrag_version_check() {

	$version = '1.0.3';

	if ($version != CONDRAG_VERSION)

	return('New version of Article Directory available! Version: '.$version);

	else

	return(NULL);

	

	// check for valid account - if none, show signup form.

}



function condrag_return_cats()

{

	$htmllink = "http://www.contentdragon.com/article_directory_api.php?resource=categories";

	$contents = condrag_nolines(condrag_get_contents($htmllink, 0, NULL));

	$reg = "[<id=]{4}[parent]{6}[>](.*?)[<\/id=]{5}[parent]{6}[>][<id=]{4}[title]{5}[>](.*?)[<\/id=]{5}[title]{5}[>][<id=]{4}[url]{3}[>](.*?)[<\/id=]{5}[url]{3}[>]";

	preg_match_all("/$reg/i", $contents, $categories);

	$cats = array();

	for ($i=0; $i<count($categories[0]); $i++)

	{

		$opt_name = $categories[2][$i];

		$opt_value = condrag_formatURL($opt_name);

		if (get_option( $opt_value )==1) $cats[] = $opt_name;

	}

	return($cats);

}



function condrag_manage_article_listing($userid)

{

	extract($_POST);

	$error = "";

	$article_submitterid = $userid;

	$action = ($article_id != "" && is_numeric($article_id))?"modify":"create";

	$success = false;

	if ($save_article != "" || $submit_article != "")

	{

		$error = $hash = "";

		

		if ($type == "public" && $author_firstname == ""){ $error = "You must specify the authors first name."; }

		else if ($type == "public" && $author_lastname == ""){ $error = "You must specify the authors last name."; }

		else if ($type == "public" && $email == ""){ $error = "You must specify your email address."; }

		

		else if ($article_categoryid == ""){ $error = "You must categorize your article."; }

		else if ($article_typeid == ""){ $error = "You must select the type of this article."; }

		else if ($article_title == ""){ $error = "You must give your article a title."; }

		else if (preg_match("/\W/i", str_replace(array(' ', '-', '_', ',', '.') , array('', '', '', '', ''), $article_title))){ $error = "The article title must only contain a-z 0-9 -_ characters "; }

		else if ($submit_article != "" && strlen($article_title)>100){ $error = "Your article title can not be longer than 100 characters, yours is ".strlen($article_title)." characters in length."; }

		else if ($submit_article != "" && $article_keywords == ""){ $error = "You must enter atleast 1 keyword for your article submission."; }

		else if (preg_match("/\W/i", str_replace(array(' ', '-', '_', ',', '.') , array('', '', '', '', ''), $article_keywords))){ $error = "The article keywords must only contain a-z 0-9 -_ characters "; }

		else if ($submit_article != "" && strlen($article_keywords)>100) {  

		$error = "Your article keywords can not be longer than 100 characters, yours are ".strlen($article_keywords)." characters in length."; }

		else if ($submit_article != "" && $article_summary == ""){ $error = "You must summarize your article in 2-5 sentences, no paragraphs please."; }

		else if ($submit_article != "" && count(explode(" ", $article_summary))>100) {  

		$error = "Your article summary can not be longer than 100 words, yours has ".count(explode(" ", $article_summary))." words."; }

		else if ($submit_article != "" && trim(strip_tags($article_text)) == ""){ $error = "You can not submit an empty article."; }

		else if ($submit_article != "" && count(explode(" ", trim(strip_tags($article_text))))<250) {  

		$error = "Your article must contain a minimum of 250 words, currently your article has a word count of "

		.count(explode(" ", trim(strip_tags($article_text))))." words."; }

		else if ($submit_article != "" && count(explode(" ", trim(strip_tags($article_text))))>5000) {  

		$error = "Your article can not have more than 5000 words, currently your article has a word count of "

		.count(explode(" ", trim(strip_tags($article_text))))." words."; }

		else if ($i_agree == "" && $submit_article != ""){ $error = "You must read and agree to our terms on submitting an article to ContentDragon.com."; }

		else 

		{

			// AND `category`='$category'

			$title_check = (int)mysql_num_rows(mysql_query("SELECT * FROM `condrag_articles` WHERE `article_title`='$article_title' "

			. (($action!="create")?" AND `article_id`!='$article_id'":"") . ";"));



			if ($title_check > 0){   $error = "The title you entered for your article is already being used by another article."; }

			else 

			{

				$code =  condrag_generate_code(60);

				$data = array(

					"article_submitterid"=>$article_submitterid,

					"article_categoryid"=>$article_categoryid,

					"article_typeid"=>$article_typeid,

					"article_title"=>$article_title,

					"article_summary"=>$article_summary,

					"article_keywords"=>$article_keywords,

					"article_text"=>$article_text,

					"article_authorinfo"=>$article_authorinfo,

					"article_modifydate"=>$article_modifydate,

					"author_firstname"=>$author_firstname,

					"author_lastname"=>$author_lastname,

					"email"=>$email,

					"code"=>$code

				);

				

				$article_id = condrag_handle_article($action, $data);

				

				if ($action == "create")

				{

					$blogname = get_bloginfo('name');

					$web = 'http://'.$_SERVER['HTTP_HOST'].'/'.condrag_get_artlink();

					if (ereg('page_id=', $web)) $web .= '&amp;'; else $web .= '?';

					$web .= 'display=submit&amp;verify='.$code.'&amp;id='.$article_id;

					$fromemail = 'noreply@'.str_replace('www.','',$_SERVER['HTTP_HOST']);

								

					$subject = "$blogname - Article Email Verification";

					$message = "<h2>$blogname Article Directory Email Verify</h2><p>

					$author_firstname $author_lastname, you are receiving this email because you have submitted an article to $blogname, before your article will be read by the administrator for approval you must verify your email below.</p><h3>Verifying your email:</h3><p>To verify your email either click or copy and paste the URL below in to your browser and your email will be verified:<br><br><strong><a href=\"$web\">$web</a></strong></p>";

					condrag_email($email, $author_firstname, $subject, $message, $fromemail, $blogname);

					

					

					$admin_email = get_bloginfo('admin_email');

					$subject = "$blogname - Article Approval Required";

					$message = "<h2>$blogname Article Directory</h2><br>

					<p>A new article has been published to your blog from your Article Directory which needs approval.</p>";

					condrag_email($admin_email, $admin_email, $subject, $message, $fromemail, $blogname);

				}

				

				$success = true;

				// Add to `condrag_article_approval_requests`

				if ($submit_article != "" && $success) 

				{

					$query = mysql_query("SELECT * FROM `condrag_article_approval_requests` WHERE `article_id`='$article_id';");

					if (mysql_num_rows($query)>0)

					{

						mysql_query("UPDATE `condrag_article_approval_requests` SET `approved`=0, `do_approve`=1 WHERE `article_id`='$article_id';");

					}

					else

					{

						//id, deny_reason, do_approve, request_count, article_id, approved

						mysql_query("INSERT INTO `condrag_article_approval_requests` VALUES (NULL, '', '0', '$article_id', '0');");

					}

				}

			}

		}

	}

	if ($error == "" && $success) $error = array("error_display"=>$success, "code"=>$code, "article_id"=>$article_id);

	else $error = array("error_display"=>$error);

	return($error);

}



function condrag_syndication()

{

	$step = $_POST['step'];

	$auth_code = $_POST['auth_code'];



	// in case a fresh installation is done, the master server needs to know so fresh articles are sent.

	// this check is only done once every 24 hours and uses minimal bandiwidth, very important though.

	if ($step == "count")

	{

		$que = mysql_query("SELECT `article_id` FROM `condrag_articles`;");

		$count = (int)mysql_num_rows($que);

		echo $count;

		mysql_close();

		exit;

	}

	/*

	Sometimes we will need to check which articles you have, this allows us to do that.

	*/

	else if ($step == "fetch-codes")

	{

		$data = array();

		$que = mysql_query("SELECT `code` FROM `condrag_articles`;");

		$count = (int)mysql_num_rows($que);

		if ($count>0)

		{

			while($row = mysql_fetch_assoc($que))

			{

				$data[] = $row['code'];

			}

		}

		echo serialize($data);

		mysql_close();

		exit;

	}

	else if ($step == "recieve")

	{

		$type_ids = array();

		$type_ids2 = array();

		$query = mysql_query("SELECT * FROM `condrag_article_type`;");

		while ($row = mysql_fetch_assoc($query))

		{

			$type_ids[$row['title']] = $row['id'];

			$type_ids2[$row['id']] = $row['title'];

		}

		

		$cat_ids = array();

		$cat_ids2 = array();

		$query = mysql_query("SELECT `category_id`, `category_title` FROM `condrag_categories` WHERE `category_parentid`='0' AND `active`='1' 

		ORDER BY `category_title` ASC;");

		while ($row = mysql_fetch_assoc($query))

		{

			$cat_ids[$row['category_title']]['id'] = $row['category_id'];

			$query2 = mysql_query("SELECT `category_id`, `category_title` FROM `condrag_categories` 

			WHERE `category_parentid`='{$row['category_id']}' AND `active`='1' ORDER BY `category_title` ASC;");

			while ($row2 = mysql_fetch_assoc($query2))

			{

				$cat_ids[$row['category_title']]['subs'][$row2['category_title']] = $row2['category_id'];

				

				$cat_ids2[$row2['category_id']] = $row2['category_title'];

				$cat_ids2[$row2['category_id'].'topid'] = $row['category_id'];

			}

			$cat_ids2[$row['category_id']] = $row['category_title'];

		}

		$count = $_POST['count'];

		for ($i=0; $i<$count; $i++)

		{

			$action = $_POST['action'][$i];

			

			if ($action == "remove")

			{

				$code = $_POST['code'];

				mysql_query("DELETE FROM `condrag_articles` WHERE `code`='$code';");

				continue;

			}

			

			if ($_POST['article_subcat'][$i]!='')

			{

				$top = $_POST['article_topcat'][$i];

				$sub = $_POST['article_subcat'][$i];

				//$article_categoryid = $cat_ids[$top]['id'];

				$article_categoryid = $cat_ids[$top]['subs'][$sub];

			}

			else

			{

				$top = $_POST['article_topcat'][$i];

				$article_categoryid = $cat_ids[$top]['id'];

			}



			$data = array(

				"syndication"=>"yes",

				"article_submitterid"=>$_POST['article_submitterid'][$i],

				"article_categoryid"=>$article_categoryid,

				"article_typeid"=>$type_ids[$_POST['article_typeid'][$i]],

				"article_title"=>urldecode($_POST['article_title'][$i]),

				"article_summary"=>urldecode($_POST['article_summary'][$i]),

				"article_keywords"=>urldecode($_POST['article_keywords'][$i]),

				"article_text"=>urldecode($_POST['article_text'][$i]),

				"article_authorinfo"=>urldecode($_POST['article_authorinfo'][$i]),

				"article_modifydate"=>$_POST['article_modifydate'][$i],

				"article_submitteddate"=>$_POST['article_submitteddate'][$i],

				"author_firstname"=>$_POST['author_firstname'][$i],

				"author_lastname"=>$_POST['author_lastname'][$i],

				"memberinfo"=>$_POST['memberinfo'][$i],

				"email"=>$_POST['email'][$i],

				"code"=>$_POST['code'][$i]

			);		



			$article_id = condrag_handle_article($action, $data);

		}

		echo "1";

	}

	return(true);

}



function condrag_handle_article($action, $data)

{

	global $wpdb;

	extract($data);

	if ($action == 'create'&&$article_submitteddate=='') $article_submitteddate = date("Y-m-d G:i:s");

	$article_urltitle = str_replace("\\", '', condrag_finalFormatURL($article_title));

	$article_title = (strip_tags($article_title));

	$article_text = addslashes(strip_tags($article_text));

	

	$article_authorinfo = stripslashes($article_authorinfo);

	$reg = "/<a(.*?)>(.*?)<\/a>/i";

	preg_match_all($reg, $article_authorinfo, $links, PREG_OFFSET_CAPTURE);

	if (count($links[0])>0)

	{

		$tags = $links[0];

		$atts = $links[1];

		$first = false;

		$add_link = $rep_link = '';

		for($v=0; $v<count($tags); $v++)

		{

			$tag = $tags[$v];

			$tag_len = strlen($tag[0]);

			if (!$first)

			{

				$first = true;

				$rep_link = $tag[0];

				// people are crafty, dont even give them a chance.

				$tag[0] = preg_replace("/target=\".*?\"/i", '', $tag[0]);

				$tag[0] = str_replace("href", "target=\"_blank\" href", $tag[0]);

				$add_link = $tag[0];

				break;

			}

		}

		if (count($links[0])>0)

		{

			for($v=0; $v<count($tags); $v++)

			{

				$tag = $tags[$v];

				$article_authorinfo = str_replace($tag, '', $article_authorinfo);

			}

			$article_authorinfo = $article_authorinfo.' '.$add_link;

		}

		else $article_authorinfo = str_replace($rep_link, $add_link, $article_authorinfo);

	}

	$article_authorinfo = addslashes($article_authorinfo);



	

	$article_keywords = addslashes(strip_tags($article_keywords));

	$article_summary = addslashes(strip_tags($article_summary));

	$article_title = addslashes(strip_tags($article_title));

	if ($syndication == "yes") $article_state = 1; else $article_state = -1;

	if ($syndication != "yes" && $code == '') $code =  condrag_generate_code(60);

	

	if (function_exists("iconv")) $article_text = iconv("UTF-8","UTF-8//IGNORE",$article_text);

	

	if ($action == "create")

	{

		$wpdb->query($wpdb->prepare( "INSERT INTO `condrag_articles` ( `article_state`, `article_featured_state`, `article_submitterid`, `article_categoryid`, `article_typeid`, `article_title`, `article_urltitle`, `article_summary`, `article_keywords`, `article_text`, `article_authorinfo`, `article_submitteddate`, `article_viewcount`, `author_firstname`, `author_lastname`, `email`, `code` ) VALUES ( %d, %d, %d, %d, %d, %s, %s, %s, %s, %s, %s, %s, %d, %s, %s, %s, %s )", $article_state, 0, $article_submitterid, $article_categoryid, $article_typeid, $article_title, $article_urltitle, $article_summary, $article_keywords, $article_text, $article_authorinfo, $article_submitteddate, 0, $author_firstname, $author_lastname, $email, $code ));

		$article_id = mysql_insert_id();

	}

	else

	{

		$sql = $wpdb->prepare( "UPDATE `condrag_articles` SET  `article_categoryid`='%d', `article_typeid`='%d', `article_title`='%s', `article_urltitle`='%s', `article_summary`='%s', `article_keywords`='%s', `article_text`='%s', `article_authorinfo`='%s', `article_modifydate`='%s' WHERE `code`='%s';", $article_categoryid, $article_typeid, $article_title, $article_urltitle, $article_summary, $article_keywords, $article_text, $article_authorinfo, $article_modifydate, $code );

		$wpdb->query($sql);

		//echo "UPDATE `condrag_articles` SET `article_state`='0', `article_typeid`='$article_typeid', `article_categoryid`='$article_categoryid', `article_title`='$article_title', `article_urltitle`='$article_urltitle', `article_summary`='$article_summary', `article_keywords`='$article_keywords', `article_text`='$article_text', `article_authorinfo`='$article_authorinfo', `article_modifydate`='$article_modifydate' WHERE `code`='$code';";

		//echo"UPDATE `condrag_articles` SET `article_typeid`='$article_typeid', `article_categoryid`='$article_categoryid', `article_title`='$article_title', `article_urltitle`='$article_urltitle', `article_summary`='$article_summary', `article_keywords`='$article_keywords', `article_text`='$article_text', `article_authorinfo`='$article_authorinfo', `article_modifydate`='$article_modifydate' WHERE `code`='$code';";

		//mysql_query("UPDATE `condrag_articles` SET `article_typeid`='$article_typeid', `article_categoryid`='$article_categoryid', `article_title`='$article_title', `article_urltitle`='$article_urltitle', `article_summary`='$article_summary', `article_keywords`='$article_keywords', `article_text`='$article_text', `article_authorinfo`='$article_authorinfo', `article_modifydate`='$article_modifydate' WHERE `code`='$code';")or die(mysql_error());

	}

	return($article_id);

}



function condrag_display_article_categories($article_categoryid)

{

	$sql = mysql_query("SELECT * FROM `condrag_categories` WHERE `category_parentid`='0' AND `active`='1' ORDER BY `category_title` ASC;");								

	$html = "<select name=\"article_categoryid\" id=\"article_categoryid\">

	<option value=\"\">Select one...</option>";

	while ($row = mysql_fetch_array($sql))

	{

		//$html .= '<option value="'.$row['category_id'].'" '.(($article_categoryid==$row['category_id'])?"selected":false).'>'.$row['category_title'].'</option>';

		$sql2 = mysql_query("SELECT * FROM `condrag_categories` WHERE `category_parentid`='{$row['category_id']}' AND `active`='1' ORDER BY `category_title` ASC;");

		while ($row2 = mysql_fetch_array($sql2))

		$html .= '<option value="'.$row2['category_id'].'" '.(($article_categoryid==$row2['category_id'])?"selected":false).'>'.$row['category_title'].' : '.$row2['category_title'].'</option>';

	}

	$html .= "</select>";

	return($html);

}





function condrag_display_article_types($article_typeid)

{

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



/* promote

******************************************************/

function condrag_delete_article($article_id)

{

	$row = mysql_fetch_assoc(mysql_query("SELECT * FROM `condrag_articles` WHERE `article_id`='$article_id';"));

	mysql_query("DELETE FROM `condrag_articles` WHERE `article_id`='$article_id';");

	mysql_query("DELETE FROM `condrag_article_approval_requests` WHERE `article_id`='$article_id';");

	//mysql_query("UPDATE `condrag_article_counts` SET `article_count`=`article_count`-1 WHERE `user_id`='{$row['article_submitterid']}';");

	return(true);

}



function condrag_build_article_rows()

{

	$uid = 0;

	

	$sort = $_GET['sort'];

	$sorts = array("article_title","article_submitteddate");

	$order = ($sort!=""&&in_array($sort, $sorts))?"ORDER BY `$sort` ASC":"ORDER BY `article_title` ASC";

	

	$data_key = 0;

	$data = array();

	

	$html = '';

	$membersQuery = mysql_query("SELECT * FROM condrag_articles WHERE `article_submitterid`='$uid' $order;");

	while ($articles = mysql_fetch_assoc($membersQuery))

	{

		$article_id = $articles['article_id'];

		$deny_reason = '';

		$status = "Draft";

		if ($articles['article_state'] == 1) $status = "Live";

		else

		{

			$approve = mysql_query("SELECT * FROM `condrag_article_approval_requests` WHERE `article_id`='{$articles['article_id']}' LIMIT 1;");

			if (mysql_num_rows($approve)>0)

			{

				$approve_row = mysql_fetch_assoc($approve);

				if ($approve_row['do_approve'] == 1) $status = "Pending Approval";

				if ($approve_row['approved'] == 0)

				{

					$deny_reason = $approve_row['deny_reason'];

					if ($deny_reason != '') $status = "<a href=\"javascript:void(0);\" onclick=\"alert('Deny Reason: ".str_replace("\n", "", $deny_reason)."');\">Denied</a>";

				}

			}

		}



		$article_title = condrag_utf8encode($articles['article_title']);

		$article_urltitle = $articles['article_urltitle'];

		$url = "/content/";

		$catinfo = condrag_is_category($articles['article_categoryid']);

		$category_title = $catinfo['category_title'];

		if ($catinfo['category_parentid'] != 0)

		{

			$subcatinfo = condrag_is_category($catinfo['category_parentid']);

			$category_title = $subcatinfo['category_title'];

			$url .= "{$subcatinfo['category_urltitle']}/";

		}

		$url .= "{$catinfo['category_urltitle']}/";

		$url .= "$article_urltitle/";

		

		$view_url = '?page=sub_myarticles&amp;article_id='.$article_id;

		$edit_url = '?page=sub_submit&amp;act=edit&amp;article_id='.$article_id;

		$delete_url = '?page=sub_myarticles&amp;delete='.$article_id;

		

		$html .= '<tr class="pagerow1">

		<td>'.$articles['article_title'].'</td>

		<td align="center">'.$category_title.'</td>

		<td align="center">'.$status.'</td>

		<td align="center">'.date("m/d/y", strtotime($articles['article_submitteddate'])).'</td>

		<td align="center">

			<a href="'.$view_url.'" target="'.(($status=='Live')?"_blank":"_self").'">View</a> - 

			<a href="'.$edit_url.'">Edit</a> - 

			<a href="'.$delete_url.'" onclick="if(!confirm(\'Are you sure you want to remove this article?\n\nthis action can not be undone!\')) return false;">Delete</a>

		</td>

		</tr>';

		

		$title_enc = urlencode($articles['article_title']);

		$url_enc = urlencode($view_url);

		

		$Delicious = 'http://del.icio.us/post?url='.$url_enc.'%26source%3ddelicious';

		$Delicious_oc = "h=encodeURIComponent('BioMS Medical Announces Third Quarter 2008 Results'); window.open('http://del.icio.us/post?v=4&noui&jump=close&url=http%3A%2F%2Fwww.newswire.ca%2Fen%2Freleases%2Farchive%2FNovember2008%2F06%2Fc6082.html&title=' + h, 'delicious','toolbar=no,width=700,height=400'); return false;";

		$Stumble = 'http://www.stumbleupon.com/submit?url='.$url_enc.'%26source%3dstumbleit';

		

		$Digg = 'http://digg.com/submit';

		$Digg_oc = "h=encodeURIComponent('".$title_enc."'); window.open('http://digg.com/submit?phase=2&url=".$url_enc."&title=' + h, 'digg','scrollbars=yes'); return false;";

		

		$Newsvine = 'http://www.newsvine.com/_tools/seed&save?u='.$url_enc.'%26source%3dnewsvine';

		

		$Facebook = 'http://www.facebook.com/sharer.php?u='.$url_enc.'%26source%3dfacebook%26yr%3d%26mk%3d%26md%3d%26r%refreshURL%3d'.$url_enc.'&t='.$title_enc;

		$Facebook_oc = "return SaveToFaceBook('http%3a%2f%2fwww.contentdragon.com%2fSocialSite%2fFaceBookPage.aspx%3fadid%3d353550%26source%3dfacebook%26yr%3d%26mk%3d%26md%3d%26r%refreshURL%3d".$url_enc."','".$title_enc."')";

		

		$Technorati = 'http://technorati.com/search/'.$url_enc;

		

		if ($status == "Live") $html .= '<tr class="pagerow2">

		<td align="left" colspan="5">

			<script type="text/javascript">

			

			function SaveToFaceBook(pageUrl, pageTitle)

			{

			 window.open(\'http://www.facebook.com/sharer.php?u=\'+pageUrl+\'&t=\'+pageTitle,\'sharer\',\'toolbar=0,status=0,width=626,height=436\');

			 return false;

			}

			

			</script>

			<b>Promote</b> - 

			<img src="/images/stumble1.gif" border=0 align=absmiddle><a href="'.$Stumble.'" title="Send to Stumble It!" target="_blank">Stumble It</a>  | 

			<img src="/images/digg.gif" border=0 align=absmiddle><a href="'.$Digg.'" onclick="'.$Digg_oc.'" title="Send to Digg" target="_blank">Digg</a>  | 

			<img src="/images/newsvine.gif" border=0 align=absmiddle><a href="'.$Newsvine.'" title="Send to Newsvine" target="_blank">Newsvine</a> | 

			<img src="/images/facebook.gif" border=0 align=absmiddle><a href="'.$Facebook.'" onclick="'.$Facebook_oc.'" title="Send to Facebook" target="_blank">Facebook</a> | 

			<img src="/images/technorati.gif" border=0 align=absmiddle><a href="'.$Technorati.'" target="_blank" title="Send to ">Technorati</a>

		</td>

		</tr>';

	}

	return($html);

}



function condrag_view_article($article_id)

{

	if ($article_id != "" && is_numeric($article_id) && $article_id > 0)

	{

		$membersQuery = mysql_query("SELECT * FROM condrag_articles WHERE article_id='$article_id'");

		$articles = mysql_fetch_assoc ( $membersQuery );

		extract($articles);

		$article_title = condrag_utf8encode($article_title);

		$article_text = condrag_utf8encode($article_text);

		$article_text = html_entity_decode($article_text, ENT_QUOTES);

		$article_text = stripslashes($article_text);

		$article_authorinfo = stripslashes($article_authorinfo);	

		$article_text = stripslashes($article_text);

		$article_authorinfo = stripslashes($article_authorinfo);

		if (!preg_match("/<.*?>/i", $article_text))

		{

			$article_text = nl2br($article_text);

		}

		$text = strip_tags($article_text);

		$front = substr($text, 0, 500);

		$end = substr($text, 500, strpos(substr($text, 500), ".")+1)." ...";

		$text = $front.$end;

						

		//echo "SELECT * FROM `users` WHERE `user_id`='{$article_submitterid}';";

		//$arow = mysql_fetch_assoc(mysql_query("SELECT * FROM `prolance_promembers` WHERE `uid`='{$article_submitterid}';"));

		$word_count = str_word_count(strip_tags(strtolower($article_text)));

		

		//if (!eregi("<br>", $article_text) && !eregi("<br />", $article_text)) $article_text = str_replace("\n\n", "<br><br>", $article_text);

	

		$by = "By: <a href=\"http://members.contentdragon.com/community/space/{$arow['uid']}/\" target=\"_blank\">{$arow['username']}</a>";

		

		$about = "<p><strong>About the author</strong>:<br>$article_authorinfo</p><p><strong>Article Word Count</strong>: $word_count</p>";

		$article_body = "<p>".$by."</p>"

		. "<p>{$article_text}</p>"

		. $about;

		

		$ptitle = '<a href="?section=articleApprovals">Article Approval Queue</a> &raquo; '.ucfirst($do).' Article';

		

		$deny_reason = '';

		$query = mysql_query("SELECT * FROM `condrag_article_approval_requests` WHERE `article_id`='$article_id';");

		if (mysql_num_rows($query)>0)

		{

			$row = mysql_fetch_assoc($query);

			$deny_reason = $row['deny_reason'];

		}

	} else $error_display = "<h3>No article selected!</h3>";

	

	return(

		array(

			"success"=>$success_display,

			"error"=>$error_display,

			"title"=>$article_title,

			"body"=>$article_body,

			"denied"=>$deny_reason,

		)

	);

}

?>