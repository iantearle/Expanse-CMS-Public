<?php $reason = $_GET['reason']; ?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Whoops, something went wrong!</title>
<style>
*{margin:0;padding:0;}
body{
font-size: 76%;
color: #fff;
font: 68.5%/1.6em 'Lucida Sans Unicode','Lucida Grande', 'Lucida', Arial, Verdana, sans-serif;
background:#fff url(/expanse/images/clouds.jpg) repeat-x;
color:#333;
}
a {
color: #363636;
text-decoration: underline;
}
a:hover {
color: #900909;
text-decoration: none;
}
h1, h2, h3, h4, h5, h6{
margin:0;
padding:0;
}

#explanation{
padding: 2em;
margin: 80px 0;
}
#explanation img{
margin:2em auto;
display:block;
}
#explanation p, #explanation ul{
margin:0 auto;
width: 350px;
padding:1em;
}
#explanation ul li{
list-style:inside;
}
#explanation h1{
margin:0 auto;
width: 350px;
font-size:120%;
}
</style>
</head>

<body>
<div id="explanation"><?php
if($reason == 'db_nocnx'){
?><img src="/expanse/images/db_nocnx.gif" alt="Cannot connect to your database" />
<h1>We cannot connect to your database<strong><?php echo !empty($CONFIG['host']) ? ' on '.$CONFIG['host'] : '' ?></strong>.</h1>
<p>There are a few reasons why this may be happening. </p>
<ul>
<?php
if(empty($CONFIG['host'])){
?> <li>It appears that you've left your hostname blank. Try putting in the host where expanse can find your database.</li><?php
} if(empty($CONFIG['user'])){
?> <li>It appears that you've not entered in a username . Try entering in a username.</li><?php
} if(empty($CONFIG['pass'])){
?> <li>It appears that you've left your password blank. Not all servers require a password, but many do. Try entering in a password.</li><?php
} if(!@fsockopen($CONFIG['host'], 3306, $errno, $errstr, 30)){
?>
<li>It appears that the host is currently unreachable. Your host can confirm the server status.</li>
<li>Is <strong><?php echo $CONFIG['host'] ?></strong> spelled correctly?</li>
<?php
}
?>
<li>Do you have the right username/password combination in your config.php file?</li>
<li>Is your database hosted with a different company/organization than your web hosting? Your database server could be blocking remote connections.</li>
</ul>
<p>Many times if you're experiencing a large amount of traffic, this can take down your database server, and hence, your entire site. Sites like <a href="http://digg.com" target="_blank">http://digg.com</a> and <a href="http://slashdot.com" target="_blank">http://slashdot.com</a> are particularly notorious for this. If this is the case, congratulations, you're popular!</p>
<p>If you've ruled out all of the above, you have two options left.<br />
  You can contact your host and ask them for help, or you could even find help at the <a href="http://forums.expansecms.org/" target="_blank">expanse Forums</a>.  </p>
<?php
} elseif($reason == 'db_noselect'){
?>
<img src="/expanse/images/db_noselect.gif" alt="Cannot select your database" width="381" height="70" />
<h1>We cannot select your database<strong><?php echo !empty($CONFIG['db']) ? ', '.$CONFIG['db'] : '' ?></strong>.</h1> <p>We could connect to your database server, so feel confident that your username and password are correct, but we're having trouble selecting the right database to use.</p>
<p>There are a few reasons why this may be happening. </p>
<ul>
<?php
if(empty($CONFIG['db'])){
?> <li>It appears that you've left the name of your database blank. Try putting in the database name.</li><?php
} else {
?>
<li>Does the database <strong><?php echo $CONFIG['db'] ?></strong> exist on the server?</li>
<li>Is <strong><?php echo $CONFIG['db'] ?></strong> spelled correctly?</li>
<li>With some hosts, they automatically place your username before the database name like so: <strong><em>username_</em><?php echo $CONFIG['db'] ?></strong>. Usually this happens with hosts that use cPanel.</li>
<?php
}
?>
</ul>
<p>If you've ruled out all of the above, you have two options left.<br />
  You can contact your host and ask them for help, or you could even find help at the <a href="http://forums.expansecms.org/" target="_blank">expanse Forums</a>.  </p>
<?php
}
?>
<a href="http://expansecms.org/" target="_blank"><img src="/expanse/images/expanselove.gif" alt="Expanse loves you!" width="110" height="33" border="0" /></a></div>
</body>
</html>
