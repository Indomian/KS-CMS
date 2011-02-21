{if $isajax==0}
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"     "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
	    <meta name="description" content="{#control_panel#} {$VERSION.TITLE}" />
     	<meta name="keywords" content="CMS, {$VERSION.TITLE}, {$VERSION.ID}" />
     	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
      	<title>
	    	{#control_panel#} {$VERSION.TITLE}
    	</title>
	    <link rel="stylesheet" href="/uploads/templates/admin/css/adminmain.css" type="text/css" />
	    <!--[if lt IE 8]><link rel=stylesheet href="css/main_ie.css"><![endif]-->
	</head>
  	<body class="login_body">
    	<div id="Ruler">&nbsp;</div>
    	    <div class="wrap">
				<div class="login_head">&nbsp;</div>
{/if}
					<div class="login_logo"><a href="/admin.php"><img src="{#images_path#}/logo.gif" alt="logo" height="67" width="249" /></a></div>
					<form action="{$backurl}" method="POST" id="login_form">
						<input type="hidden" name="CU_ACTION" value="login"/>
						{strip}
						<div class="ltop">
							<div class="ltop2">
								<table width="216" cellspacing="4" cellpadding="0">
									<tr>
										<td width="108"><div class="vb"><span><input type="text" name="CU_LOGIN" tabindex="1"/></span></div></td>
										<td width="108"><div class="vb"><span><input type="password" name="CU_PASSWORD" tabindex="2"/></span></div></td>
									</tr>
								</table>
							</div>
						</div>
						<div class="lm">
							<div class="lm2">
								<table class="lm2t" width="216" cellspacing="0" cellpadding="0">
									<tr>
										<td width="108" align="center" valign="middle"><input class="login_button" type="submit" value="{#login#}" tabindex="3"/></td>
										<td width="108" align="center"><div class="login_forgot"><a href="/admin.php?lostpwd=Y">{#forgot_password#}</a></div></td>
									</tr>
								</table>
							</div>
						</div>
						{/strip}
					</form>
					<div class="lbottom">
						<div class="lbottom2">
							{if $last_error!=''}
								<div class="login_atention">
									{$last_error}
								</div>
							{/if}
						</div>
					</div>
{if $isajax==0}
				</div>
			</div>
		</div>
	</body>
</html>
{/if}