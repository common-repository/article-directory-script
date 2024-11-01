<style>
#deny {
	font-size: 15px;
	color: #FF9900;
	font-weight: bold;
}
#approve {
	font-size: 15px;
	color: #006600;
	font-weight: bold;
}
#delete {
	font-size: 15px;
	color: #CC0000;
	font-weight: bold;
}
</style>
<table width="100%" border="0" cellspacing="1" cellpadding="6">
  <tr>
    <td>{title}</td>
  </tr>
  <tr>
    <td>{body}</td>
  </tr>
  <tr>
    <td><form id="form1" name="form1" method="post" action="">
        <table width="100%" border="0" cellspacing="0" cellpadding="0">
          <tr>
          	<!--
            <td width="50%" align="center">Deny Reason:<br />
              <textarea name="deny_reason" cols="50" rows="3" id="deny_reason">{deny_reason}</textarea>
              <br />
              <input type="submit" name="deny" id="deny" value="Deny article" /></td>
              -->
            <td align="center">
            <p><input type="submit" name="approve" id="approve" value="Approve article" /></p>
            <p></p>
            <p><input type="submit" name="delete" id="delete" value="delete article..." onclick="if(!confirm('are you sure you want to delete this article?')) return false;" /></p></td>
          </tr>
        </table>
      </form></td>
  </tr>
</table>
