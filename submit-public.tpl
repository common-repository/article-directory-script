<script language="javascript">
function insert_link(id)
{
	var obj = document.getElementById(id);
	var url = prompt("Enter your URL (ex: http://www.mysite.com):", "http://");
	var text = prompt("Enter your linking Text (ex: MySite.com):", "");
	if (url == "" || text == "") return false;
	obj.value = obj.value + ' ' + '<a href="'+url+'" target="_blank">'+text+'<'+'/a>';
	return true;
}
</script>
{error.msg}
{success.msg}
<br />
<form action="" enctype="multipart/form-data" method="post">
<input type="hidden" name="action" value="{action.value}" />
<input type="hidden" name="article_id" value="{d.article_id}" />
<table width="100%" border="0" cellspacing="2" cellpadding="5">
  <tr>
    <td align="left"><strong>Author First Name<font color="#FF0000">*</font></strong></td>
  </tr>
  <tr>
    <td align="left">
      <input name="author_firstname" type="text" id="author_firstname" size="50" value="{author.firstname}" /></td>
  </tr>
  <tr>
    <td align="left"><strong>Author Last Name<font color="#FF0000">*</font></strong></td>
  </tr>
  <tr>
    <td align="left">
      <input name="author_lastname" type="text" id="author_lastname" size="50" value="{author.lastname}" /></td>
  </tr>
  <tr>
    <td align="left"><strong>Email Address<font color="#FF0000">*</font></strong></td>
  </tr>
  <tr>
    <td align="left">
      <input name="email" type="text" id="email" size="50" value="{email}" /></td>
  </tr>
  <tr>
    <td align="left"><a name="category"></a><strong>Select A Category : Sub-Category<font color="#FF0000">*</font></strong></td>
  </tr>
  <tr>
    <td align="left">{categories}</td>
  </tr>
  <tr>
    <td align="left"><a name="type"></a><strong>Select Article Type<font color="#FF0000">*</font></strong></td>
  </tr>
  <tr>
    <td align="left">{type}</td>
  </tr>
  <tr>
    <td align="left"><a name="title"></a><strong>Article Title<font color="#FF0000">*</font></strong></td>
  </tr>
  <tr>
    <td align="left"><input name="article_title" type="text" id="article_title" value="{d.article_title}" size="80" maxlength="100" /></td>
  </tr>
  <tr>
    <td align="left"><a name="keywords"></a><strong>Keywords</strong> (Comma Separate Please - keyword1,keyword2,keyword3,etc.)</td>
  </tr>
  <tr>
    <td align="left"><input name="article_keywords" type="text" id="article_keywords" value="{d.article_keywords}" size="80" maxlength="100"  /></td>
  </tr>
  <tr>
    <td align="left"><a name="summary"></a><strong>Abstract/Article Summary/Teaser Copy (2-5 sentences, no paragraphs please, no HTML)<font color="#FF0000">*</font></strong></td>
  </tr>
  <tr>
    <td align="left"><textarea id="article_summary" name="article_summary" cols="77" rows="5">{d.article_summary}</textarea></td>
  </tr>
  <tr>
    <td align="left"><a name="text"></a><strong>Article Body<font color="#FF0000">*</font> (No HTML)   </strong></td>
  </tr>
  <tr>
    <td align="left"><textarea id="article_text" name="article_text" style="width: 99%; height: 450px;">{d.article_text}</textarea></td>
  </tr>
  <tr>
    <td align="left"><strong>About Author (No HTML, one link permitted)</strong></td>
  </tr>
  <tr>
    <td align="left"><textarea id="article_authorinfo" name="article_authorinfo" cols="77" rows="5">{d.article_authorinfo}</textarea>
      <br />
      <input type="button" value="Insert link..." onclick="insert_link('article_authorinfo');" />
      (<strong>Note</strong>: only <u>one</u> link will be saved, extra links are stripped.)<br /></td>
  </tr>
  <tr>
    <td align="left">&nbsp;</td>
  </tr>
  <tr>
    <td align="left"><a name="agree"></a><input type="checkbox" name="i_agree" id="i_agree" value="1" />
      <label for="i_agree">&nbsp;[<strong>YES I AGREE</strong>] By submitting your article to us, you agree to the terms specified in the <a target="_blank" href="http://members.contentdragon.com/content/1/Terms-of-Service.html">Authors Agreement</a> and that the article you are submitting is an original works that you  personally wrote or have an exclusive copyright and license to the  content.</label></td>
  </tr>
  <tr>
    <td align="left">&nbsp;</td>
  </tr>
  <tr>
    <td align="left"><input type="submit" name="submit_article" id="submit_article" value="SUBMIT NOW!"/></td>
  </tr>
</table>
</form>