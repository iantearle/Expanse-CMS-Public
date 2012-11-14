<?php
/********* Expanse ***********/
//Must be included at the top of all included files.
if(!defined('EXPANSE')){die('Sorry, but this file cannot be directly viewed.');}
include('funcs/rss.class.php');
$rss = new easyRSS;
?>
<!-- Begin page content -->

<div class="span12">
	<h1><?php printf(L_WELCOME_HEADER,DISPLAY_NAME, CMS_NAME); ?></h1>
</div>
<div class="span6">
	<div class="well">
		<?php if (is_unsafe()){ printf(ALERT, L_PERMISSIONS_WARNING);} ?>
		<?php $totals = $auth->getTotals();?>
		<p><?php printf(L_OVER_TOTAL_USAGE,$totals['user_count'],$totals['total_count']); ?></p>
		<?php echo  $auth->createSummary(); ?>
	</div>
</div>

<?php applyOzoneAction('main_home'); ?>

<div id="expanseNews" class="span6">
	<h3><?php printf(L_LATEST_NEWS, CMS_NAME)?></h3>
	<?php
		$news = array('items'=>array());
		if(!isset($_SESSION['remote_news'])) {
			$expanseRSS = getRemoteFile(EXPANSE_NEWS_URL);
			$news = $_SESSION['remote_news'] = $rss->parse($expanseRSS->results, "Y-m-d H:i:s");
		} else {
			$news = $_SESSION['remote_news'];
		}
		foreach($news['items'] as $ind => $val) {
			if($ind < 3) {
				$title = $val['title'];
				$link = $val['link'];
				$descr = (strlen($val['description']) > 150) ? substr($val['description'], 0, 150).'&hellip; <a href="'.$link.'" target="_blank">[More]</a>' : $val['description'];
				$date = date('F jS, Y // g.i a', strtotime($val['pubDate']));
				?>
				<h3><a href="<?php echo $link ?>" target="_blank"><?php echo $title ?></a></h3>
				<h4><?php echo $date ?></h4>
				<?php echo html_entity_decode($descr) ?>
			<?php
			}
		}
	echo empty($news['items']) ? L_NEWS_NOT_LOADING : '';
	?>
</div>
<?php
/*
<div class="span6" id="summary">
	<div class="well">
		<h3><?php printf(L_LATEST_BUZZ, YOUR_SITE, getOption('sitename')); ?></h3>
		<?php
			$technorati_url = 'http://blogsearch.google.com/blogsearch_feeds?hl=en&q=link:'.YOUR_SITE.'&output=rss';
			if(!isset($_SESSION['remote_buzz'])){
				$sitebuzz = getRemoteFile($technorati_url);
				$buzz = $_SESSION['remote_buzz'] = $rss->parse($sitebuzz->results);
			} else {
				$buzz = $_SESSION['remote_buzz'];
			}
			$has_buzz = (count($buzz['items']) > 1 && !empty($buzz['items'][0]['link']));
			$no_buzz = '<p>'.sprintf(L_NO_BUZZ, COMPANY_URL, CMS_NAME).'</p>';
			if(!empty($buzz['items'])) {
				foreach($buzz['items'] as $ind => $val) {
					if($ind < 9) {
						$title = $val['title'];
						$descr = (strlen($val['description']) > 330) ? substr($val['description'], 0, 330)."..." : $val['description'];
						$link = $val['link'];
						if(!$has_buzz) {
							echo $no_buzz;
							break;
						}
						?>
						<p><a class="buzz" href="<?php echo $link ?>" target="_blank" title="<?php echo strip_tags($descr) ?>"><?php echo $title ?></a></p>
						<?php
					}
				}
			} else {
				echo $no_buzz;
			}
		?>
	</div>
</div>
*/
?>
	<!-- End page content -->
