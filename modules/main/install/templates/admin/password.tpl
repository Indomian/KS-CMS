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
				<div class="login_logo"><a href="/admin.php"><img src="{#images_path#}/logo.gif" alt="logo" height="67" width="249" /></a></div>
				{if $step==1}
				<form action="/admin.php?lostpwd=Y" method="POST">
					<input type="hidden" name="step" value="2"/>
					<div class="ltop">
						<div class="ltop2">
							<div class="login_text">
								{#password_restore_notify#}
							</div>
							<table width="216" cellspacing="4" cellpadding="0">
								<tr>
									<td>E-mail</td>
									<td><div class="vb"><span><input type="text" name="email"/></span></div></td>
								</tr>
								<tr>
									<td width="96"><div class="login_code">{#restore_captcha_code#}</div><div class="vb"><span><input type="text" name="c" value=""/></span></div></td>
									<td width="120"><img src="{captchaImageUrl}" border="0"/></td>
								</tr>
							</table>
						</div>
					</div>
					<div class="lm">
						<div class="lm2">
							<table width="216" cellspacing="0" cellpadding="0">
								<tr>
									<td align="center"><input class="login_button" type="submit" value="{#send#}" /></td>
								</tr>
							</table>
						</div>
					</div>
				</form>
				{elseif $step==2}
				<div class="ltop">
					<div class="ltop2">
						<div style="text-align:center;">{#restore_mail_send#}</div>
					</div>
				</div>
				{elseif $step==3}
				<div class="ltop">
					<div class="ltop2">
						<div class="login_text">
							{#restore_login_info#}
						</div>
						<table width="216" cellspacing="4" cellpadding="0">
							<tr>
								<td>{#restore_new_password#}</td>
								<td><div class="vb"><span><input type="text" value="{$pwd}"/></span></div></td>
							</tr>
						</table>
					</div>
				</div>
				<div class="lm">
					<div class="lm2">
						<table width="216" cellspacing="0" cellpadding="0">
							<tr>
								<td align="center"><div class="login_forgot"><a href="/admin.php">{#restore_go_back#}</a></div></td>
							</tr>
						</table>
					</div>
				</div>
				{/if}
				<div class="lbottom">
					<div class="lbottom2">
						{if $message!=''}
						<div class="login_atention">{$message}</div>
						{/if}
						<div class="login_blank">&nbsp;</div>
					</div>
				</div>
			</div>
		</div>
	</body>
</html>