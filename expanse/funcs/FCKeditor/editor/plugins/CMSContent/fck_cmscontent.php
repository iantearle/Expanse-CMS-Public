<?php
$inclfilename = '../config.php';
while(!@file_exists($inclfilename)){
            $inclfilename = "../".$inclfilename;

}
require_once($inclfilename);
require_once(realpath(dirname($inclfilename)).'/funcs/database.class.php');
require_once(realpath(dirname($inclfilename)).'/funcs/expanse.class.php');
require_once(realpath(dirname($inclfilename)).'/funcs/functions.php');
require_once(realpath(dirname($inclfilename)).'/funcs/varsdef.php');
$yoursite = YOUR_SITE;
define('EXPANSEPATH', realpath($CONFIG['home']));
define('UPLOADS', EXPANSEPATH.'/uploads');
define('THEMES', EXPANSEPATH.'/themes');
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
	<head>
		<title>Insert a link to content on your site</title>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<meta content="noindex, nofollow" name="robots">
		<script type="text/javascript" src="fckcmscontent.js"></script>
		<script type="text/javascript">
		<!--
			var oEditor			= window.parent.InnerDialogLoaded(); 
			var FCK				= oEditor.FCK; 
			var FCKLang			= oEditor.FCKLang ;
			var FCKConfig		= oEditor.FCKConfig ;
			var FCKCMSContent	= oEditor.FCKCMSContent; 
			 
			// oLink: The actual selected link in the editor.
			var oLink = FCK.Selection.MoveToAncestorNode( 'A' ) ;
			if ( oLink )
				FCK.Selection.SelectNode( oLink ) ;
	
			window.onload = function ()	{ 
				// First of all, translates the dialog box texts.
				oEditor.FCKLanguageManager.TranslatePage(document);
				
				LoadSelected();							//See function below 
				window.parent.SetOkButton( true );		//Show the "Ok" button. 
				
			} 
			 
			//If an anchor (A) object is currently selected, load the properties into the dialog 
			function LoadSelected()	{
				var sSelected;
				
				if ( oEditor.FCKBrowserInfo.IsGecko ) {
					sSelected = FCK.EditorWindow.getSelection();
				} else {
					sSelected = FCK.EditorDocument.selection.createRange().text;
				}

				if ( sSelected == "" ) {
					alert( 'Please select a text in order to create a (internal) link' );
				}

			}

			//Code that runs after the OK button is clicked 
			function Ok() {
				//Validate is option is selected
				var oPageList = document.getElementById( 'cmbPages' ) ;
				if(oPageList.selectedIndex == -1) {
					alert('Please select a page in order to create a link');
					return false;
				}
				
					var sURL = document.getElementById( 'PageURL' ) ; 
					var sPageId = oPageList[oPageList.selectedIndex].value;
					oLink = oEditor.FCK.CreateLink( sURL.value + sPageId ) ;
					SetAttribute( oLink, 'title'	, document.getElementById( 'txtTitle' ).value ) ;
				return true;
			} 
			
		//-->
		</script>
	</head>
			
	<body>
	
		 <input type="hidden" id="PageURL" value="<?php echo $yoursite ?>" />
		 <table height="100%" cellspacing="0" cellpadding="0" width="100%" border="0"> 
		 	<tr> 
				<td>
					<table width="100%">
						<tr>
							<td colspan="2"><span fcklang="DlgCMSCommentPageSelection">Select an item on your server:</span>&nbsp;</td>
						</tr>
						<tr>
							<td colspan="2">
								<select id="cmbPages" style="WIDTH: 100%" size="8" name="cmbPages">
<?php
								//$content_array = ContentManager::GetAllContent(false);
								//$content_array = read_dir(EXPANSEPATH.'/themes');
								getPages();
								fileList(UPLOADS, EXPANSE_FOLDER.'/uploads', 'Your Uploads');
								fileList(EXPANSEPATH.'/themes', EXPANSE_FOLDER.'/themes');
								
								
?>
								</select><?php 
								//debug(read_dir(EXPANSEPATH));
function read_dir($dir) {
  $array = array();
  $d = dir($dir);
  while (false !== ($entry = $d->read())) {
      if($entry!='.' && $entry!='..') {
          $entry = $dir.'/'.$entry;
          if(is_dir($entry)) {
  $array[] = $entry;
              $array = array_merge($array, read_dir($entry));
          } else {
              $array[] = $entry;
          }
      }
  }
  $d->close();
  return $array;
}
function getPages() {
	$items = new Expanse('items');
	$pages = $items->GetList(array(array('online', '=', 1)));
	echo '<optgroup label="Your Pages">';
	foreach($pages as $page) {
		$page_value = CLEAN_URLS ? $page->dirtitle : INDEX_PAGE."?ucat=$page->id";
		echo '<option value="'.$page_value.'">'.$page->title.'</option>';
	}
	echo '</optgroup>';
}
function fileList($dir, $folder_val='', $folder_label=''){
								$files = getFiles($dir);
								echo '<optgroup label="'.$folder_label.'">';
								foreach($files['files'] as $file){
								echo '<option value="'.checkTrailingSlash($folder_val).$file.'">'.$file.'</option>';
								}
								echo '</optgroup>';
								}
								?>							</td>
						</tr>
						<tr>
							<td nowrap><span fcklang="DlgCMSCommentTitle">Title</span>&nbsp;</td>
							<td width="100%" style="align:right;"><input id="txtTitle" style="WIDTH: 98%" type="text" name="txtTitle"></td>
						</tr>
					</table>
				</td>
			</tr>
		</table>
		
	</body>
</html> 