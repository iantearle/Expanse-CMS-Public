<?php
/****************************************************************

                    `-+oyhdNMMMMMMMNdhyo/-`
                .+ymNNmys+:::....-::/oshmNNdy/.
             :smMmy/-``.-:-:-:----:-::--..-+hNNdo.
          .smMdo-`.:::.`               `.-::-`:smMd/`
        .yMNy- -::`                         `-::`:hMmo`
      `yMNo``:/`                               `-/--yMN+
     /mMy.`:-                                  ```./--dMd.
    sMN/ //`                                    `..`-/`sMN/
   yMm-`s.                                       `.-.`+-/NN+
  yMm--y. ```.-/ooyoooo/:.                        `---`/::NN/
 +MN:.h--/sdNNNNMMMNNNmmmhdoo+:.                  `.-::`/:+MN.
`NMs`hyhNNMMMMMMMMMMMNNNmhyso+syy/:-.`          `.-/+o++:. hMh
+MN.`:ssdmmmmmmmmmmmmhyyyo++:.``   `.-:::://:::::.```````  -MN-
mMy    ````````....`````````                         ````  `dMo
MM+            ````                                  ````   yMy
MM:                                                  ````   yMd
MM+                                                  ````   yMy
dMy                                                  ````  `dM+
+Mm.       ``-://++oo+///-``    ``-::/ooooyhhddddddmmm+yo. -MN-
`NM+ -/+s.`ommmmmmmmmmmmmmddhyhyo+++oosyhhdddmmmNNNNMddmh+ hMh
 /MN-oNmds``sdmmmmNNNNNmmmdNmmdddhhyyyyyhhdddmmmNNmmy-+:s`+MN.
  sMm-sNmd+`.ydmmNNNNNNmmmNNNmdhysso+oosyssssso/:--:`.-o`:NN/
   yMm-+Nmds..ymmmNNNNNmNNNNNmdhyso++//::--...```..``:+ /NN+
    sNN/-hmdh+-ommNNNNmNNNNNNmdhyso+//::--..````.` .+:`oMN/
     /mMy.+mmddhhmNNNmmNMNNNNmdyso+//::--..````` `++`-dMd.
      `yMN+./hNmmmmmmmmmNNNNmmhyso+//:--..``..`-//`-yMN/
        .yMNy--odNNNmmmmmNNNmdhyso+/::--..`.://-`:hMmo`
          .smMdo-.+ydNNmmddmmdysso+/::::////.`:smMd/`
             :smMmy+---/oysydhhyyyo/+/:-``-+hNNdo.
                .+yNMNmhs+/::....-::/oshmNNdy/.
                    .-+oyhdNMMMMMMMNdhyo/-`

Expanse - Content Management For Web Designers, By A Web Designer
			  Extended by Ian Tearle, @iantearle
		Started by Nate Cavanaugh and Jason Morrison

****************************************************************/
$reason = $_GET['reason'];
?>

<!DOCTYPE html>
<html lang="en" class="no-js">
<head>
<<<<<<< HEAD
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
=======
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
>>>>>>> 325e700e95f305a91d7685ba9c9b19b036d2e24c
<title>Whoops, something went wrong!</title>
<style>
*{margin:0;padding:0;}
body{
font-size: 76%;
color: #fff;
font: 68.5%/1.6em 'Lucida Sans Unicode','Lucida Grande', 'Lucida', Arial, Verdana, sans-serif;
<<<<<<< HEAD
background:#fff url(expanse/images/clouds.jpg) repeat-x;
=======
background:#fff url(/expanse/images/clouds.jpg) repeat-x;
>>>>>>> 325e700e95f305a91d7685ba9c9b19b036d2e24c
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
<<<<<<< HEAD
$config_file = realpath(dirname(__FILE__).'/../').'/config.php';
if($reason == 'db_nocnx'){
?><img src="expanse/images/db_nocnx.gif" alt="Cannot connect to your database" />
=======
if($reason == 'db_nocnx'){
?><img src="/expanse/images/db_nocnx.gif" alt="Cannot connect to your database" />
>>>>>>> 325e700e95f305a91d7685ba9c9b19b036d2e24c
<h1>We cannot connect to your database<strong><?php echo !empty($CONFIG['host']) ? ' on '.$CONFIG['host'] : '' ?></strong>.</h1>
<p>There are a few reasons why this may be happening. </p>
<ul>
<?php
<<<<<<< HEAD
if(empty($CONFIG['host'])) {
	?> <li>It appears that you've left your hostname blank. Try putting in the host where expanse can find your database.</li><?php
}
if(empty($CONFIG['user'])) {
	?> <li>It appears that you've not entered in a username . Try entering in a username.</li><?php
}
if(empty($CONFIG['pass'])) {
	?> <li>It appears that you've left your password blank. Not all servers require a password, but many do. Try entering in a password.</li><?php
}
if(!@fsockopen($CONFIG['host'], 3306, $errno, $errstr, 30)) {
	?>
	<li>It appears that the host is currently unreachable. Your host can confirm the server status.</li>
	<li>Is <strong><?php echo $CONFIG['host'] ?></strong> spelled correctly?</li>
	<?php
=======
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
>>>>>>> 325e700e95f305a91d7685ba9c9b19b036d2e24c
}
?>
<li>Do you have the right username/password combination in your config.php file?</li>
<li>Is your database hosted with a different company/organization than your web hosting? Your database server could be blocking remote connections.</li>
</ul>
<p>Many times if you're experiencing a large amount of traffic, this can take down your database server, and hence, your entire site. Sites like <a href="http://digg.com" target="_blank">http://digg.com</a> and <a href="http://slashdot.com" target="_blank">http://slashdot.com</a> are particularly notorious for this. If this is the case, congratulations, you're popular!</p>
<p>If you've ruled out all of the above, you have two options left.<br />
<<<<<<< HEAD
  You can contact your host and ask them for help, or you could even find help at the <a href="http://forums.expanse.io/" target="_blank">expanse Forums</a>.  </p>
<?php
} elseif($reason == 'db_noselect') {
?>
<img src="expanse/images/db_noselect.gif" alt="Cannot select your database" width="381" height="70" />
=======
  You can contact your host and ask them for help, or you could even find help at the <a href="http://forums.expansecms.org/" target="_blank">expanse Forums</a>.  </p>
<?php
} elseif($reason == 'db_noselect'){
?>
<img src="/expanse/images/db_noselect.gif" alt="Cannot select your database" width="381" height="70" />
>>>>>>> 325e700e95f305a91d7685ba9c9b19b036d2e24c
<h1>We cannot select your database<strong><?php echo !empty($CONFIG['db']) ? ', '.$CONFIG['db'] : '' ?></strong>.</h1> <p>We could connect to your database server, so feel confident that your username and password are correct, but we're having trouble selecting the right database to use.</p>
<p>There are a few reasons why this may be happening. </p>
<ul>
<?php
<<<<<<< HEAD
if(!file_exists($config_file)) {
	?> <li>We couldn't find a configuration file.</li><?php
} else {
	if(empty($CONFIG['db'])){
		?> <li>It appears that you've left the name of your database blank. Try putting in the database name.</li><?php
	} else {
		?>
		<li>Does the database <strong><?php echo $CONFIG['db'] ?></strong> exist on the server?</li>
		<li>Is <strong><?php echo $CONFIG['db'] ?></strong> spelled correctly?</li>
		<li>With some hosts, they automatically place your username before the database name like so: <strong><em>username_</em><?php echo $CONFIG['db'] ?></strong>. Usually this happens with hosts that use cPanel.</li>
		<?php
	}
=======
if(empty($CONFIG['db'])){
?> <li>It appears that you've left the name of your database blank. Try putting in the database name.</li><?php
} else {
?>
<li>Does the database <strong><?php echo $CONFIG['db'] ?></strong> exist on the server?</li>
<li>Is <strong><?php echo $CONFIG['db'] ?></strong> spelled correctly?</li>
<li>With some hosts, they automatically place your username before the database name like so: <strong><em>username_</em><?php echo $CONFIG['db'] ?></strong>. Usually this happens with hosts that use cPanel.</li>
<?php
>>>>>>> 325e700e95f305a91d7685ba9c9b19b036d2e24c
}
?>
</ul>
<p>If you've ruled out all of the above, you have two options left.<br />
<<<<<<< HEAD
  You can contact your host and ask them for help, or you could even find help at the <a href="http://forums.expanse.io/" target="_blank">expanse Forums</a>.  </p>
<?php
}
?>
<a href="http://expanse.io/" target="_blank"><img src="expanse/images/expanselove.gif" alt="Expanse loves you!" width="110" height="33" border="0" /></a></div>
=======
  You can contact your host and ask them for help, or you could even find help at the <a href="http://forums.expansecms.org/" target="_blank">expanse Forums</a>.  </p>
<?php
}
?>
<a href="http://expansecms.org/" target="_blank"><img src="/expanse/images/expanselove.gif" alt="Expanse loves you!" width="110" height="33" border="0" /></a></div>
>>>>>>> 325e700e95f305a91d7685ba9c9b19b036d2e24c
</body>
</html>
