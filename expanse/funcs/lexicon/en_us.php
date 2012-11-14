<?php
if(!defined('EXPANSE')){return;}
/*
------------------------------------------------------------
Language: English - US
Translated by: Nate Cavanaugh and Ryan Miglavs
============================================================
*/
/*
------------------------------------------------------------
Login/Logout
============================================================
*/
$LEX['bad_login'] = 'The Username and/or Password are not valid.';
$LEX['not_logged_in'] = 'You are not logged in.';
$LEX['logged_out'] = 'You are now logged out.';
$LEX['logout'] = 'Logout';
$LEX['welcome'] = 'Welcome, %s';
$LEX['get_started'] = 'Login to get started.';
$LEX['login_username'] = 'Username:';
$LEX['login_password'] = 'Password:';
$LEX['login_email'] = 'Email:';
$LEX['login_remember_me'] = 'Remember Me';
$LEX['login_forgot_password'] = 'I forgot my password';
$LEX['login_go_back'] = '&laquo; Login';

$LEX['nothing_here'] = '<div class="alert alert-info"><strong>Heads up!</strong> Please use the <a href="index.php" title="Main Menu">menu</a> to navigate through the CMS.</div>';
/*   Password request/change   //-------------------------------*/
$LEX['missing_retrieve_field'] = 'Please make sure you\'ve entered in a valid username and email';
$LEX['cant_retrieve'] = 'Sorry, but there does not appear to be a user with that information';
$LEX['mail_password_from'] = 'Password Manager';
$LEX['mail_password_request'] = 'Password Change Request';
$LEX['password_invalid_key'] = 'Sorry, but that does not appear to be a valid key.';
$LEX['mail_password_changed'] = 'Your password has been changed.';
$LEX['password_instructions_sent'] = 'Instructions for resetting your password have been sent to your email address.';
$LEX['password_instructions_not_sent'] = 'Sorry, but your password information could not be sent.';
$LEX['password_mail_failed'] = 'Sorry, but your password information could not be sent. It appears that there is a problem sending mail from your server. Please ask your host to see if they have disabled the mail() function.';
$LEX['password_changed'] = 'Your password has been successfully changed, and sent to your email address';
$LEX['password_not_changed'] = 'Sorry, but your password could not be changed.';
$LEX['mailer_request_title'] = 'Change Password Request for %s';
$LEX['mailer_request_body'] = 'Someone, hopefully you, has requested to change your password. If you did not request this, ignore this email, and all will be fine.<br /><br />
If you do wish to reset your password, you can do so by going to this address <a href="%s">%s</a>';
$LEX['mailer_changed_title'] = 'Your password has been changed.';
$LEX['mailer_changed_body'] = 'As requested, the password for %s has been changed. Your new login details are below';
$LEX['mailer_changed_details_title'] = 'Login information';
$LEX['mailer_changed_details'] = '<strong>What is my username?</strong> %s<br />
<strong>What is my new password?</strong> %s';
$LEX['mailer_powered_by'] = 'Powered By <a href="%s" target="_blank">%s</a>';
/*
------------------------------------------------------------
Admin Menu text
============================================================
*/
$LEX['menu_add'] = 'Add';
$LEX['menu_edit'] = 'Edit';
$LEX['menu_mass_upload'] = 'Mass Upload';
$LEX['menu_send_newsletter'] = 'Send Newsletter';
$LEX['menu_admin_settings'] = 'Admin Settings';
$LEX['menu_view_site'] = 'View Site';
/*
------------------------------------------------------------
Admin overview text
============================================================
*/
$LEX['over_pages_online'] = '<span>%s</span> pages online / <span>%s</span> total';
$LEX['over_items_online'] = '<span>%s</span> items online / <span>%s</span> total';
$LEX['menu_admin_settings'] = 'Admin Settings';
$LEX['menu_view_site'] = 'View Site';
$LEX['welcome_header'] = 'Welcome, <strong>%s</strong> to %s&hellip;';
$LEX['over_total_usage'] = 'You have added <span>%s</span> items so far out of a total of <span>%s</span> items.';
$LEX['permissions_warning']  = 'It is HIGHLY recommended that you change the permissions of the current directory to a safer level (ie. 755).';
$LEX['latest_buzz']  = 'Latest <a href="http://blogsearch.google.com/blogsearch_feeds?hl=en&q=link:%s&output=rss" target="_blank">Buzz</a> about %s';
$LEX['no_buzz']  = 'There\'s no buzz about you yet, but I\'m sure there will be soon. After all, you\'re running <a href="%s" target="_blank">%s</a>.';
$LEX['latest_news']  = 'Latest News from %s';
$LEX['news_not_loading']  = 'Sorry, but the news could not be loaded. Please try again later.';
/*
------------------------------------------------------------
Add/Edit Messages
============================================================
*/
$LEX['add_success'] = 'Your entry, <strong>&#8220;%s&#8221;</strong> has been added.<br /> You can edit it <a href="index.php?type=edit&amp;cat_id=%s&amp;id=%s">here</a>';
$LEX['add_failure'] = 'Sorry, but <strong>%s</strong> could not be added.  <p>Why?<br /> %s</p>';
$LEX['edit_success'] = '&#8220;%s&#8221; has been edited.';
$LEX['edit_failure'] = 'Sorry, but there was a problem editing your entry.';
$LEX['edit_move_success'] = '&#8220;%s&#8221; has been edited. Additionally, you chose to move this item to another category, and you can edit it <a href="index.php?type=edit&amp;cat_id=%s&amp;id=%s">here</a>.';
/*
------------------------------------------------------------
Delete Messages
============================================================
*/
$LEX['delete_success'] = 'The following items have been deleted:<ul>%s</ul>';
$LEX['delete_failure'] = 'Sorry, but there was a problem deleting the following item(s): <ul>%s</ul>  <p>Why?<br />%s</p>';
/*
------------------------------------------------------------
File Upload Messages
============================================================
*/
$LEX['upload_failure'] = 'Sorry, but this upload has some errors:%s';

/*
------------------------------------------------------------
Comment Output
============================================================
*/
$LEX['add_comment_success'] = 'Your comment has been added.';
$LEX['add_comment_pending']	= 'Your comment has been added. However, it is waiting to be moderated, so it will only go live after a site admin approves it.';
$LEX['add_comment_failure']	= 'Sorry, but there was a problem adding your comment.';
$LEX['comment_subject']		= 'Someone has posted a comment to ';
$LEX['comment_format_url']		= 'One of these fields: <strong>%s</strong> contains an invalid URL.';
$LEX['comment_format_email']	= 'One of these fields: <strong>%s</strong> contains an invalid email address.';
$LEX['comment_mailer_subject']	= 'Someone has posted a comment to %s';
$LEX['comment_pending_mailer_subject']	= 'Moderation needed - Someone has posted a pending comment to %s';
$LEX['comment_mailer_from']	= '%s Comment Notification';
/*
------------------------------------------------------------
Reorder Ouput
============================================================
*/
$LEX['reorder_success'] = 'Your items have been reordered.';
$LEX['reorder_failure'] = 'Sorry, but your items couldn\'t be reordered.';
$LEX['reorder_menu_success'] = 'Your menu has been reordered.';
$LEX['reorder_menu_failure'] = 'Sorry, but your menu couldn\'t be reordered.';
/*
------------------------------------------------------------
Contact Form Output
============================================================
*/
$LEX['contact_success'] = 'Your message has been sent.';
$LEX['contact_failure'] = 'Your message could not be sent. Please try again later.';
$LEX['contact_subject'] = 'Someone has sent you an e-mail';
$LEX['contact_from']	= '%s Mail Notification';

/*
------------------------------------------------------------
Misc Messages
============================================================
*/
$LEX['no_entries_user'] 	= 'Sorry, but there are no entries here.';
$LEX['previous_text'] 		= '&laquo;Previous';
$LEX['next_text'] 			= 'Next&raquo;';
$LEX['missing_fields'] 		= 'Sorry, but you are missing the following required field(s): %s.';
$LEX['entry_not_found']		= 'Sorry, but that entry doesn\'t exist. Why don\'t you add an entry <a href="index.php?type=add&amp;cat_id=%s">here</a>?';
$LEX['page_not_found'] 		= 'Sorry, but that page does not exist.';
$LEX['no_entries'] 			= 'Sorry, but there are no entries in this category. Go ahead and add one <a href="index.php?type=add&amp;cat_id=%s">here</a>.';
$LEX['preview_text'] 		= 'View this item on your site';
$LEX['flooding_message']	= 'Sorry, but &quot;flood control&quot; is enabled. Please wait %s seconds.';
$LEX['eula_notice']			= 'Please read the EULA and check the box indicating that you have read and understood the agreement. If you cannot see the checkbox, scroll down to the bottom of the EULA box.';
$LEX['prefs_updated']		= 'Your preferences have been saved';
$LEX['prefs_update_failed']	= 'Some of your options could not be saved';
$LEX['file_not_readable']	= 'This file cannot be read by expanse.';
$LEX['upgrade_available']	= 'There is an upgrade to version %s';
$LEX['upgrade_instructions']= 'It looks like you just upgraded your copy of %s to version %s. However, there is one final step you must take to wrap up the upgrade. Go to <a href="upgrade.php">this page</a>, and press the &#8220;Finish Upgrade&#8221; button, and you\'re all set.';
$LEX['finish_update']		= 'Finish Update';
$LEX['update_title']		= 'Software Update';
$LEX['update_crumb']		= 'Updating %s';
$LEX['up_to_date']			= '%s is up to date. We\'ll let you know when there\'s a new version for you to install.';
$LEX['update_successful']	= 'You are awesome! Thanks for updating your software, as we think you\'ll appreciate the changes.';
$LEX['update_thanks']		= 'Thanks for updating. Now go on and enjoy %s.';
$LEX['update_descr']		= 'You\'re just one step away from finishing your update.<br />Just press this button, and you\'re done.';
$LEX['copyright_footer']	= ' &copy; %s. All Rights Reserved.';
$LEX['legal_footer']		= 'Legal';
$LEX['support_footer']		= 'Support';
$LEX['concat_and']			= 'and';
$LEX['concat_or']			= 'or';
$LEX['separator']			= '/';
$LEX['menu_separator']		= ' | ';
/*   Page titles   //-------------------------------*/
$LEX['main_title']			= 'Welcome to %s';
$LEX['login_title']			= 'Login to %s to get started';
$LEX['default_title']		= 'Look away! I\'m hideous!';
/*   Breadcrumbs   //-------------------------------*/
$LEX['crumb_home']		= 'Home';
$LEX['crumb_add']		= 'Add items to %s';
$LEX['crumb_edit']		= 'Manage your %s items';
$LEX['crumb_edit_admin']= 'Edit Admin Settings';
/*   Misc. page   //-------------------------------*/
$LEX['misc_account_disabled'] = 'Sorry, but your account has been disabled. If you feel you\'ve reached this page in error, please speak to the Admin.';
$LEX['misc_disabled_title'] = 'Your Account Has Been Disabled';
$LEX['misc_no_permissions'] = 'Sorry, but you do not have the proper permissions to view this page. Please speak to the Admin if you feel that you have recieved this message in error.';
$LEX['misc_no_permissions_title'] = 'Access Denied';
$LEX['misc_nothing_here'] = 'Whoops! It looks like you took a wrong turn somewhere, because there is nothing here.';
$LEX['misc_nothing_here_title'] = 'Whoops!';
/*
------------------------------------------------------------
Generic Module Text
============================================================
*/
$LEX['add_item_title'] 		= 'Add an item to %s';
$LEX['edit_item_title'] 	= 'Edit an item in %s';
$LEX['online'] 				= 'Online?';
$LEX['title'] 				= 'Title';
$LEX['materials'] 			= 'Materials';
$LEX['url'] 				= 'URL';
$LEX['image'] 				= 'Image';
$LEX['thumbnail'] 			= 'Thumbnail';
$LEX['caption'] 			= 'Caption';
$LEX['auto_thumbnail'] 		= 'Automatically generate thumbnail?';
$LEX['body'] 				= 'Body';
$LEX['use_smilies'] 		= 'Use Smilies?';
$LEX['allow_comments'] 		= 'Allow Comments';
$LEX['category_options'] 	= 'Category Options';
$LEX['post_time_add'] 		= 'Post in the Future (or past)<img src="images/help.gif" alt="" width="16" height="16" class="hasHelp" id="editDate" />';
$LEX['currently_editing'] 	= 'Editing &#8220;%s&#8221;';
$LEX['currently_editing_html'] 	= 'Editing &#8220;<strong>%s</strong>&#8221;';
$LEX['currently_editing_plain'] = 'Editing %s';
$LEX['custom_fields_title'] = 'Custom Fields';
$LEX['custom_fields_help']  = 'Custom fields are a way for you to easily add custom data to your item. Sometimes you\'d like to add extra information into your item, such as &quot;Client URL&quot; or &quot;Role in project&quot;. This information can be anything you wish, and you can add as many as you like.<br />
Also, a custom variable is created for you in the content loop. So you can paste the variable anywhere in your loop, and it will work.';
$LEX['post_time_edit'] 		= 'Edit Date/Time <img src="images/help.gif" alt="" width="16" height="16" class="hasHelp" id="editDate" />';
$LEX['clean_url_titles'] 	= 'Clean URL Titles';
$LEX['edit_item'] 		= 'Edit Item';
$LEX['share_item'] 		= 'Share this item';
$LEX['edit_page'] 		= 'Edit page';
$LEX['share_page'] 		= 'Share this page';
$LEX['delete_item'] 		= 'Delete';
$LEX['no_text_in_title'] 	= 'No title';
$LEX['no_text_in_description'] 	= 'No text in the body of the description';
$LEX['posted_by'] 		= 'Posted by: <strong>%s</strong> (%s)';
$LEX['posted_on'] 		= 'Posted on: %s';
$LEX['item_online'] 		= 'Item is online';
$LEX['item_offline'] 		= 'Item is offline';
$LEX['page_online'] 		= 'Page is online';
$LEX['page_offline'] 		= 'Page is offline';
/*   Sub-category   //-------------------------------*/
$LEX['sub_category'] 		= 'Sub-category';
$LEX['sub_category_add'] 	= 'Add a sub-category';
$LEX['sub_category_select'] 	= 'Select a sub-category';
/*   Category actions (move or edit)   //-------------------------------*/
$LEX['category_action'] 	= 'Category Action';
$LEX['move_or_copy'] 		= 'Move or copy item to a different category';
$LEX['move_to'] 		= 'Move item to:';
$LEX['copy_to'] 		= 'Copy item to:';
/*   Sharing   //-------------------------------*/
$LEX['sharing_direct_link']	= 'Direct Link (&quot;Permalink&quot;) to this item';
$LEX['sharing_image_link']	= 'Permalink to the full size image ';
$LEX['sharing_thumb_link']	= 'Permalink to thumbnail';
/*   Clean URLs   //-------------------------------*/
$LEX['clean_url_title']	= 'Clean URL Title';
$LEX['clean_url_help']	= 'If you\'re using clean urls, sometimes you may wish to customize the url text for this item. Anything you enter here will be "cleansed" so as to be a valid URL, but you can enter whatever you wish. If you\'d like to automatically regenerate it based upon the title, go ahead and erase the current text, and it will be done for you.';
/*   Date/Time   //-------------------------------*/
$LEX['time_date']	= 'Date';
$LEX['time_month']	= 'Select a month';
$LEX['time_day']	= 'Day';
$LEX['time_year']	= 'Year';
$LEX['time_time']	= 'Time';
$LEX['time_hour']	= 'Hour';
$LEX['time_minute']	= 'Minute';
$LEX['time_second']	= 'Second';
$LEX['time_reset']	= 'Reset date/time to right now';
$LEX['time_use_current']	= 'Reset date/time to right now';
$LEX['time_help_add'] = 'You can set your item to go live in the future, or backdate your post. In order for your post to be published in the future, you <strong>MUST</strong> also have the Online checkbox checked.';
$LEX['time_help_edit'] = 'The time shown is the time on your host\'s server. This does not account for your local timezone.<br />
The local time of the entry is: ';
/*   Category Details (Sharing)   //-------------------------------*/
$LEX['sharing_title']	= 'Sharing';
$LEX['rss_feed']	= 'RSS Feed';
$LEX['atom_feed']	= 'Atom Feed';
/*   Category Details (Sorting)   //-------------------------------*/
$LEX['sort_title']	= 'Sorting';
$LEX['sort_viewing']	= 'Currently viewing:';
$LEX['sort_category_details']	= 'Category Details';
$LEX['sort_subcategory_details']	= 'Sub-category Details';
$LEX['sort_direct_link_category']	= 'Direct link to this category';
$LEX['sort_direct_link_subcategory']	= 'Direct link to this sub-category';
$LEX['sort_view']	= 'View just the items in:';
$LEX['sort_all_subcategories']	= 'All sub-categories';
$LEX['sort_howmany']	= 'How many per page?';
$LEX['sort_sortby']	= 'Sort by';
$LEX['sort_order_direction']	= 'Order direction';
$LEX['sort_button']	= 'Sort';
$LEX['sort_by_user_rank']	= 'User ranking';
$LEX['sort_by_title']	= 'Title';
$LEX['sort_by_user']	= 'User';
$LEX['sort_by_date']	= 'Date';
$LEX['sort_by_id']		= 'Item Number (ID)';
$LEX['sort_by_asc']		= 'Ascending (Up)';
$LEX['sort_by_desc']	= 'Descending (Down)';
$LEX['size_note']	= 'Your host allows you to upload files up to <strong>%s</strong> in size. If you need to upload a larger file, please speak with your host, and they can help you bump that size up.';


/*   Paging   //-------------------------------*/
$LEX['paging_pages']	= 'Pages:';
$LEX['paging_previous']	= '&laquo;Previous';
$LEX['paging_next']	= 'Next&raquo;';
/*   Misc.   //-------------------------------*/
$LEX['err_item_not_found']	= 'Entry not found';
$LEX['err_item_missing']	= 'That item does not exist';
$LEX['err_item_missing_plural']	= 'Those items do not exist';
$LEX['err_upload_heavy']		= 'Your uploaded file, <strong>%s</strong> is too big. Please make sure your file is no larger than  %s';
$LEX['err_upload_not_image']	= 'Your uploaded file, <strong>%s</strong> is not an image.';
$LEX['err_upload_missing_dir']	= 'That specified upload directory does not exist.';
$LEX['button_add']		= 'Add';
$LEX['button_edit']		= 'Edit';
$LEX['button_update']	= 'Update';
$LEX['button_delete']	= 'Delete';
$LEX['button_activate']	= 'Activate';
$LEX['button_create']	= 'Create';
$LEX['button_get_info']	= 'get my information';
$LEX['button_mass_upload']	= 'Mass Upload';
$LEX['button_send_newsletter'] = 'Send Newsletter';
$LEX['button_install'] 			        = 'Install';
$LEX['button_uninstall'] 		        = 'Uninstall';
$LEX['button_make_new_theme'] 		        = 'Make a new theme';
/*
------------------------------------------------------------
Blog Module
============================================================
*/
$LEX['blog_name'] = 'Blog';
$LEX['blog_description'] = 'This category is useful for news/blog type updates and posts to your website.';
/*
------------------------------------------------------------
Forum Module
============================================================
*/
$LEX['forum_name'] = 'Forum';
$LEX['forum_description'] = 'This category is for managing a forum and posts on your website.';
/*
------------------------------------------------------------
Advert Module
============================================================
*/
$LEX['advert_name'] = 'Advert';
$LEX['advert_description'] = 'This category is for managing an ad network on your website.';
$LEX['advert_title'] = 'Campaign Name';
$LEX['advert_body'] = 'Campaign Text';
$LEX['advert_body_help'] = '80 Characters of text. No need to worry about formatting. Our publishers do that for you.';
$LEX['advert_caption'] 	= 'Campaign Name';
$LEX['advert_custom_fields_title'] = 'Web Sites';
$LEX['advert_custom_fields_help']  = 'Publisher websites and locations of their adverts can be removed below. If you remove one by mistake then you will need to contact the administrator.';
/*
------------------------------------------------------------
Gallery Module
============================================================
*/
$LEX['gallery_name']	= 'Gallery';
$LEX['gallery_description']	= 'This type of category useful for gallery/portfolio type updates and posts to your website.';
$LEX['gallery_upload_note']	= 'You can upload a zip file and %s will automatically extract all of the images, and insert them into the database. The name of the image inside of the zip will be the name of the item. Below you can set the options that will be applied to all of the items (don\'t worry, after upload, you can always edit the individual item on a piece by piece basis).Note: please do not include folders inside of the zip file.';
$LEX['gallery_file']	= 'File';
$LEX['gallery_optional']	= 'Optional';
$LEX['gallery_auto_thumb']	= 'Auto-Generate Thumbnail?';
$LEX['gallery_body']	= 'Description';
$LEX['gallery_paypal_settings']	= 'Paypal Settings';
$LEX['gallery_ftp_upload_success']	= 'Your files have been added to the category. Make sure to leave your files in the folder that you added them to.';
$LEX['gallery_ftp_upload_failure']	= 'Sorry, but your files could not be added to the category.';
$LEX['gallery_ftp_upload_missing']	= 'Sorry, but it appears that you entered in a folder that doesn\'t seem to exist.';
$LEX['gallery_edit_more_later']	= 'You will be able to add/modify more options when you edit each item individually.';
$LEX['gallery_mass_upload']	= 'Upload multiple items at once';
$LEX['gallery_single_upload']	= 'Upload one item at a time';
$LEX['gallery_mass_upload_ftp']	= 'FTP Upload';
$LEX['gallery_mass_upload_ftp_help']	= 'If you want to mass upload a series of images via FTP and add them to your category, go ahead and create a folder inside of your <strong>expanse/uploads/</strong> directory. Upload all of your images to that directory, and then type in just the name of the folder here. And that\'s it, you\'re all set.';
$LEX['gallery_title']	= 'Artwork Title';
$LEX['gallery_additional_images']	= 'Additional Images';
$LEX['gallery_additional_images_help']	= 'If you like, you can specify additional images to be associated with this item. The thumbnail for this item will be automatically generated for you.<br />
This is useful if you have an image set that is related to the main item.';
$LEX['gallery_additional_images_delete']	= 'Delete?';
$LEX['gallery_invalid_mass_upload']	= 'Sorry, but you must either choose to upload a zip file, or give a folder name where you have uploaded your images';
$LEX['gallery_zip_extracted']	= 'Your file has been extracted, and the following items added to the database:%s';
$LEX['gallery_zip_partially_extracted']	= 'Your file has been extracted, but the following items could not be added to the database:%s';
$LEX['gallery_zip_no_images']	= 'Sorry, but there didn\'t appear to be any images inside of that zip file. Nothing was added to the database.';
$LEX['gallery_zip_not_images_plural']	= 'were removed because they are not images';
$LEX['gallery_zip_not_images_singular']	= 'was removed because it is not an image';
$LEX['gallery_thumbnail_settings']	= 'Thumbnail Settings';
$LEX['gallery_thumb_x']	= 'Thumbnail X';
$LEX['gallery_thumb_y']	= 'Thumbnail Y';
$LEX['gallery_thumb_w']	= 'Thumbnail Width';
$LEX['gallery_thumb_h']	= 'Thumbnail Height';
$LEX['gallery_thumb_max']	= 'Maximum Thumbnail Width';
$LEX['gallery_thumb_keep_default']	= 'Keep default thumbnail size?';
$LEX['gallery_pp_for_sale']	= 'For Sale?';
$LEX['gallery_pp_price']	= 'Price';
$LEX['gallery_pp_handling_cost']	= 'Handling Cost';
$LEX['gallery_pp_handling_note']	= '*Must be a number';
$LEX['gallery_pp_more_options']	= 'More Options';
$LEX['gallery_pp_more_options_help']	= 'Here you can specify additional options for the item you\'re selling. For example, if you\'re selling a shirt, you could put Red in one field, Black in another, or Green another. If you were selling prints, you could enter in print dimensions. There is no required formatting, other than each option goes into it\'s own field.';
/*
------------------------------------------------------------
Events Module
============================================================
*/
$LEX['events_name'] = 'Events';
$LEX['events_description'] = 'This type of category is best for posting upcoming events, such as Art shows, gallery exhibits, etc to your website.';
$LEX['events_title'] = 'Event Title';
$LEX['events_body']	= 'Event Details';
$LEX['events_date'] = 'Event Date';
$LEX['events_date_start'] = 'Event Start Date-time';
$LEX['events_date_end'] = 'Event End Date-time';
$LEX['events_link'] = 'Event Link';
/*
------------------------------------------------------------
Press Module
============================================================
*/
$LEX['press_name']	= 'Press';
$LEX['press_description']	= 'This category is best for posting any sort of media coverage to your website.';
/*
------------------------------------------------------------
Newsletter Module
============================================================
*/
$LEX['newsletter_name'] = 'Newsletter';
$LEX['newsletter_description'] = 'This category is best for managing and sending to a newsletter mailing list.';
$LEX['newsletter_address_online'] = 'Active?';
$LEX['newsletter_address_online_help'] = 'Send mail to this address?';
$LEX['newsletter_address_is_online'] = 'Email address active';
$LEX['newsletter_address_is_offline'] = 'Email address inactive';
$LEX['newsletter_address_title'] = 'Email Address';
$LEX['newsletter_address_name'] = 'Name';
$LEX['newsletter_address_description'] = 'Notes';
$LEX['newsletter_address_description_help'] = 'These notes will not be viewable by anyone but this list\'s administrators.';
$LEX['newsletter_edit_address'] = 'Edit Address';
$LEX['newsletter_view_profile'] = 'View Recipient\'s Profile';
/*
------------------------------------------------------------
Links Module
============================================================
*/
$LEX['links_name']	= 'Links';
$LEX['links_description']	= 'This category is best for posting links to friends sites, etc.';
$LEX['links_title']	= 'Name';
$LEX['links_body']	= 'Description';
$LEX['links_remove_image']	= 'Remove Image?';
$LEX['links_']	= '';
$LEX['links_']	= '';
$LEX['links_']	= '';
$LEX['links_']	= '';
/*
------------------------------------------------------------
Pages Module
============================================================
*/
$LEX['page_name'] 				= 'Pages';
$LEX['page_description'] 		= 'This category is best for creating static pages.';
$LEX['edit_page'] 				= 'Edit page';
$LEX['share_page'] 				= 'Share this page';
$LEX['page_online'] 			= 'This page is online';
$LEX['page_offline'] 			= 'This page is offline';
$LEX['page_title']		= 'Page Title';
$LEX['page_body']		= 'Page body';
$LEX['page_parent']		= 'Page parent';
$LEX['page_parent_none']		= 'None (top-level)';
$LEX['page_files']		= 'Page Files';
$LEX['page_additional_files']		= 'Additional files';
$LEX['page_additional_files_help']		= 'If you like, you can specify additional files to be associated with this item. The thumbnail for this item will be automatically generated for you, if this item is an image.';
$LEX['page_file']		= 'File';
$LEX['page_template_safe_title'] = 'Template-safe title';
$LEX['page_template_safe_title_help']		= 'The "template-safe" title is one that has been made safe to use as the file name of a template. If you wish for this page to use it\'s own unique template, you can create a new one, and when you give it this name, %s will use that template for this page. <br />
If you delete the value in here, it will reset it to use the current title, otherwise, whatever you use in here will be "cleansed" to be made safe for using in a template.';
$LEX['page_additional_files_delete']	= 'Delete?';
$LEX['page_file_caption']		= 'File caption';
$LEX['page_file_name']		= 'File name:';
/*
------------------------------------------------------------
Property Module
============================================================
*/
$LEX['property_title']			= 'Place Name';
$LEX['property_twitter']		= 'Twitter';
$LEX['property_telephone']		= 'Telephone';
$LEX['property_admission']		= 'Admission';
$LEX['property_events']			= 'Events';
$LEX['property_events_help']	= 'If you have planned events throughout the year you can list them here. You can edit this section at anytime, but changes will only be applied to the database at our periodical updates throughout the year.';
$LEX['property_dates']			= 'Dates';
$LEX['property_other']			= 'Other';
$LEX['property_other_help']		= 'Any other information you would like to appear that you feel would benefit your potential visitors.';
$LEX['property_facilities']		= 'Facilities';
$LEX['property_shop']			= 'Shop';
$LEX['property_plantsales']		= 'Plantsales';
$LEX['property_cafe']			= 'Cafe';
$LEX['property_civil_wedding_license']		= 'Civil Wedding License';
$LEX['property_audio_tours']	= 'Audio Tours';
$LEX['property_dogs_disallowed'] = 'Dogs Disallowed';
$LEX['property_open_all_year']	= 'Open All Year';
$LEX['allow_property_reviews']	= 'Allow reviews';
/*//-------------------------------*/
/*
------------------------------------------------------------
Admin Modules
============================================================
*/
$LEX['admin_settings'] 			= 'Admin Settings';
$LEX['admin_manage_categories'] = 'Manage Categories';
$LEX['admin_manage_users'] 		= 'Manage Users';
$LEX['admin_manage_comments'] 	= 'Manage Comments';
$LEX['admin_edit_themes'] 		= 'Edit Themes';
$LEX['admin_prefs'] 			= 'Preferences';
$LEX['admin_db_settings'] 		= 'Database Settings';

/*   Categories Module   //-------------------------------*/
$LEX['category_add_title'] 			= 'Add a category';
$LEX['category_edit_title'] 		= 'Edit a category';
$LEX['category_manage_types'] 		= 'Manage Category Types';
$LEX['missing_category_detail'] 	= 'Please make sure your category has a title, and that you\'ve chosen a category type.';
$LEX['category_added'] 				= 'Your category &#8220;%s&#8221; has been added. You can edit it <a href="index.php?cat=admin&amp;sub=categories&amp;action=edit&amp;id=%s">here</a>.';
$LEX['category_not_added'] 			= 'Your category could not be added.';
$LEX['category_give_name'] 			= 'Give your category a name';
$LEX['category_name'] 				= 'Category name';
$LEX['category_give_type'] 			= 'Choose your type of category';
$LEX['category_type'] 				= 'Category type';
$LEX['category_description'] 				= 'Category Description (optional)';
$LEX['category_pending_install'] 	        = 'This category module needs to be installed.';
$LEX['category_pending_uninstall'] 	        = 'This category module can be uninstalled';
$LEX['category_pending_uninstall_warning'] 	= 'Do this with EXTREME caution.';
$LEX['category_pending_install_list']           = 'Categories that still need to be <a href="?cat=admin&amp;sub=categories&amp;action=manage">installed</a>';
$LEX['category_installed'] 		        = 'Category module installed.';
$LEX['category_already_installed'] 	        = 'This category module is already installed.';
$LEX['category_uninstalled'] 		        = 'Category module uninstalled.';
$LEX['category_already_uninstalled']= 'This category module is not installed.';
$LEX['category_finished_managing'] 	        = 'If you\'re done managing your categories, you can always <a href="?cat=admin&amp;sub=categories&amp;action=add">add</a> or <a href="?cat=admin&amp;sub=categories&amp;action=edit">edit</a> a category to get started.';
$LEX['category_missing'] 		        = 'That category does not exist.';
$LEX['subcategory_deleted'] 	        = 'Your sub-category <strong>%s</strong> has been deleted.';
$LEX['subcategory_not_deleted']         = 'Your sub-category <strong>%s</strong> could not be deleted.(%s)';
$LEX['edit_category_success']           = 'The category: <strong>%s</strong> has been edited.%s';
$LEX['edit_category_failure']           = 'Sorry, but the category: <strong>%s</strong> could not be edited.%s  <p>Why?<br />
%s</p>';
$LEX['add_subcategory']        		= 'Add a Sub-Category';
$LEX['edit_category_name_label']        = 'Edit category name';
$LEX['category_missing']        	= 'Sorry, but there is no category with that id. Please make sure you\'re following a correct link.';
$LEX['category_does_not_exist']        	= '(That category does not exist)';
$LEX['category_deleted']        	= 'Your category: <strong>%s</strong> has been deleted.';
$LEX['category_not_deleted']        	= 'Your category: <strong>%s</strong> could not be deleted.%s';
$LEX['edit_subcategory_name_label']     = 'Edit Sub-Category Names';
$LEX['new_subcategory_added']           = 'Also, your new categories: %s, were added.';
$LEX['new_subcategory_not_added']       = 'Unfortunately, your new categories: %s, could not be added.';
$LEX['list_category_name']       	= 'Category Name';
$LEX['list_no_of_items']       		= 'No. of items';
$LEX['list_category_type']       	= 'Category Type';
$LEX['list_template_name']       	= 'Template Name';
$LEX['list_category_manage']		= 'Manage';
$LEX['list_template_name_help']		= 'This is the name of the template file, if you want to create a unique template for that category. You do NOT have to name your template files in this fashion. If you want to just have a generic template for all categories of a certain type, you would name the template file in the format of <strong>category_type.tpl.html</strong>. So, for instance, if you want a generic template for every category that is a "blog" category type, you would name your template <strong>blog.tpl.html</strong>';
$LEX['delete_category_alert'] = 'Deleting a main category will delete all of the items in that category as well.';
$LEX['category_add_link'] = 'Looking to add a category? Look <a href="?cat=admin&sub=categories&action=add">no further</a>.';

/*   Comments Module   //-------------------------------*/
$LEX['comment_online']		= 'Approved?';
$LEX['comment_edit_title']		= 'Edit a posted comment';
$LEX['no_comments']				= 'Sorry, but there are no comments posted yet.';
$LEX['comment_delete_success']      = 'The comment by <strong>%s</strong> has been deleted.';
$LEX['comment_delete_failure']      = 'The comment by <strong>%s</strong> could not be deleted.';
$LEX['comment_missing']       		= 'That comment no longer exists.';
$LEX['comment_email']       		= 'E-mail';
$LEX['comment_comments']       		= 'Comments';
$LEX['comment_name']       			= 'Name';
$LEX['comment_does_not_exist']      = 'Sorry, but that comment just doesn\'t exist.';
$LEX['comment_name_missing']       	= 'Name not given';
$LEX['comment_message_missing']     = 'No Text In Body Of Comment';
$LEX['comment_edit']       			= 'Edit comment';
$LEX['comment_posted_by']			= 'Posted by:';
$LEX['ban_delete']	= 'Delete the comment(s) and ban the commenter\'s IP address';
$LEX['comment_ips_banned']       	= 'The following IP addresses were banned: %s';
$LEX['comment_ips_not_banned']      = 'The following IP addresses could not be banned: %s';

/*   Menu builder module   //-------------------------------*/
$LEX['mb_name']       			= 'Menu Builder';
$LEX['mb_link']       			= 'Configure menu';
$LEX['mb_note']       			= 'This is where you can reorganize and rebuild the menu that shows up on your main website. This doesn\'t change the look or style of the menu, but rather allows you to decide which items are on the menu, and in what order they appear.';

/*   DB Connection Module   //-------------------------------*/
$LEX['dbcnx_title']       		= 'Database connection settings';
$LEX['db_new_cnx_failed']       = 'Sorry, but no connection could be made to the supplied settings. The original settings have been kept.';
$LEX['db_new_select_failed']    = 'Sorry, but that database could not be selected. The original database name has been kept.';
$LEX['db_update_success']       = 'MySQL Settings successfully updated.';
$LEX['db_cnx_working']       	= 'Your connection is working.';
$LEX['db_cnx_broken']       	= 'Sorry, but there is a problem with your connection.';
$LEX['db_hostname']       		= 'MySQL Hostname';
$LEX['db_username']       		= 'MySQL Username';
$LEX['db_password']       		= 'MySQL Password';
$LEX['db_password_note']       	= '(Leave blank to keep the same password)';
$LEX['db_database_name']       	= 'MySQL Database';
$LEX['db_hostname_help']       	= 'Address to the server (eg. localhost, or mysqlserver.domain.com)';
$LEX['db_database_name_help']   = 'Name of the database to use';
$LEX['db_needs_permissions']    = 'In order to edit your settings, you need to set the permissions for your config file to 777. (If you don\'t know what that means, check your FTP\'s help documentation under CHMOD.)';

/*   Preferences Module  //-------------------------------*/
$LEX['prefs_title']   			= 'Manage your preferences';
$LEX['prefs_hdr_general']   	= 'General Settings';
$LEX['prefs_hdr_time']			= 'Time Settings';
$LEX['prefs_hdr_appearance']   	= 'Theme/Appearence Settings';
$LEX['prefs_hdr_language']   	= 'Language Settings';
$LEX['prefs_hdr_paypal']   		= 'Paypal Settings (optional)';
$LEX['prefs_hdr_comments']   	= 'User/Comment Filtering';
$LEX['prefs_your_name']   		= 'Your Name';
$LEX['prefs_your_name_help']   	= 'This name is used to identify you throughout the site';
$LEX['prefs_admin_email']   	= 'Admin Email';
$LEX['prefs_admin_email_help']  = 'This is the address that will recieve mail notifications when a comment is posted. You can add more than one e-mail address to the list, by separating the e-mail addresses with commas';
$LEX['prefs_site_name']   		= 'Site Name';
$LEX['prefs_site_name_help']   	= 'The name of your website';
$LEX['prefs_site_description']  = 'Site Description';
$LEX['prefs_site_description_help']   = 'A short description of your site';
$LEX['prefs_site_url']   		= 'Your Site URL';
$LEX['prefs_site_url_help']   	= '(eg. http://domainname.com)<br />Our best guess is that this value is: <strong>http://%s</strong>';
$LEX['prefs_time_server']   = 'Current Server Time';
$LEX['prefs_time_server_help']   = 'Your server time is the local time of wherever the server is located. You can adjust the settings on your site by setting the offset.';
$LEX['prefs_time_tz_offset']   = 'Local Timezone Offset';
$LEX['prefs_time_tz_offset_help']   = 'You can display times in your local timezone throughout your website by setting the number of hours to offset. Negative numbers set it to earlier, positive numbers set it later.<br />Example: if your server says it is currently 3 o\'clock A.M on Thursday, but it\'s 11 o\'clock P.M on Wednesday in your timezone, you would set it to -4.';
$LEX['prefs_time_format_date']   = 'Date Format';
$LEX['prefs_time_curr_format_date']   = 'Current Date Format';
$LEX['prefs_time_curr_format_date_help']   = 'Current Settings: %s<br />The format you wish Date\'s to be displayed in. Accepts PHP\'s date format options[<a href="http://php.net/date" target="_blank">http://php.net/date</a>]';
$LEX['prefs_time_format_time']   = 'Time Format';
$LEX['prefs_time_curr_format_time']   = 'Current Time Format';
$LEX['prefs_app_start_category']   = 'Start Category';
$LEX['prefs_app_start_category_help']   = 'The category that you wish to have as the start page for your site';
$LEX['prefs_app_how_many']   = 'How many per page?';
$LEX['prefs_app_how_many_help']   = 'This will set the number of items to display per page. You can choose to have no limits by setting it blank.';
$LEX['prefs_app_how_many_edit']   = 'How many per edit page?';
$LEX['prefs_app_how_many_edit_help']   = 'When you are editing your items in %s, this sets how many items to display on the page list. Set this to 0 if you dont want to break your items into pages.';
$LEX['prefs_app_sort_cats']   = 'Sort your categories by';
$LEX['prefs_app_sort_cats_help']   = 'Unless you are manually displaying content, %s will try to sort your categories by the option you specify. The default is &quot;User Ranking&quot;, which just means that it\'s ordered in the way you want.';
$LEX['prefs_app_sort_dir']   = 'Sort &quot;Direction&quot;';
$LEX['prefs_app_sort_dir_help']   = 'Unless you are manually displaying content, %s will try to sort your categories by the option you specified above. Setting this option tells %s the &quot;direction&quot; you want to display the categories in. For example, if you chose to sort by &quot;Date&quot;, selecting &quot;Ascending&quot; will place the oldest entries first, where as &quot;Descending&quot; would place the newest items first. The default is &quot;Ascending&quot;.';
$LEX['prefs_app_thumb_size']   = 'Default Thumbnail Size';
$LEX['prefs_app_thumb_size_help']   = 'With this option, you can set the default thumbnail size (in pixels) for any entry that automatically generates a thumbnail. Only enter ni a number, with no text.';
$LEX['prefs_app_smilies_comments']   = 'Show smilies in comments?';
$LEX['prefs_app_smilies_comments_help']   = 'If checked, this option will parse smilies in a users comments.';
$LEX['prefs_app_theme']   = 'Select Theme';
$LEX['prefs_app_theme_help']   = 'From here you can select the name of the installed theme you wish to use';
$LEX['prefs_app_clean_urls']   = 'Use Clean URLs?';
$LEX['prefs_app_clean_urls_help']   = 'Clean URLs allows you to have nicer looking URLs. That means your links to your pages and items would go from this: %s/%s?pcat=2&amp;item=3 to something like this: %s/artwork/your-artwork-item.';
$LEX['prefs_app_index_file']   = 'I renamed the index file to:';
$LEX['prefs_app_index_file_help']   = 'If you renamed the index file (index.php) to another name (such as main.php or anything else), enter in the name that you changed it to.';
$LEX['prefs_pp_email']   = 'PayPal E-mail';
$LEX['prefs_pp_email_help']   = 'The e-mail address of the PayPal account you wish to use. This field is optional.';
$LEX['prefs_pp_currency_code']   = 'Currency Type';
$LEX['prefs_pp_currency_code_help']   = 'This is the currency type that you wish your order to be processed in.';
$LEX['prefs_pp_logo_url']   = 'Your PayPal Logo';
$LEX['prefs_pp_logo_url_help']   = 'Do you have a custom logo you want PayPal to use when people add items to their cart? Add the url to the image here.';
$LEX['prefs_pp_shipping']   = 'Shipping Cost';
$LEX['prefs_pp_shipping_help']   = 'The amount to apply towards shipping for 1-item orders. Setting this to "0" makes shipping free.';
$LEX['prefs_pp_shipping2']   = 'Shipping Cost (Multiple Items)';
$LEX['prefs_pp_shipping2_help']   = 'The amount to apply towards shipping for orders over 1 item. Setting this to "0" makes shipping free.';
$LEX['prefs_pp_tax']   = 'Item Tax';
$LEX['prefs_pp_tax_help']   = 'The amount to apply towards tax for orders. This amount applies to all buyers. Setting this to "0" makes the tax free.';
$LEX['prefs_pp_handling']   = 'Handling Cost';
$LEX['prefs_pp_handling_help']   = 'The amount to apply towards the total order amount (regardless of quantity) for handling. Setting this to "0" makes the handling free.';
$LEX['prefs_user_moderate']   = 'Moderate All Comments';
$LEX['prefs_user_moderate_help']   = 'If you\'d like to approve every comment before it gets posted live, check this box. We find that this can help keep down spammy comments.';
$LEX['prefs_user_flooding']   = '&quot;Flood Control&quot; Delay';
$LEX['prefs_user_flooding_help']   = 'The amount of time, in seconds, that you wish to force people to wait before posting again. Setting this to zero turns off &quot;Flood Control&quot;.';
$LEX['prefs_user_banned_words']   = 'Banned Words';
$LEX['prefs_user_banned_words_help']   = 'Words that you wish to keep people from being able to post in their comments. This helps in keeping comment spam and mail hacking down. The list must be comma separated.';
$LEX['prefs_user_banned_ips']   = 'Banned IPs';
$LEX['prefs_user_banned_ips_help']   = 'IP addresses of users you wish to keep from commenting on your items or contacting you. This setting is site-wide, but still allows users to view the site. Also, you may inadvertently block large groups of people, if the person you are blocking is behind a proxy. This list must be comma separated.';
$LEX['prefs_language']   = 'Language';
$LEX['prefs_language_help']   = 'This is the language you wish to have %s display all of the text in.';
$LEX['prefs_language_translated_by']   = 'Translated by: %s';

/*   Themes module   //---------------------------*/
$LEX['theme_editor_title']   = 'Theme Editor';
$LEX['theme_activate_success']   = 'Your theme of choice has been activated.';
$LEX['theme_activate_failure']   = 'Sorry, but that theme could not be activated.';
$LEX['theme_no_permissions']   = 'Sadly, you do not have writing permissions for this directory. You can look, but you can\'t touch.';
$LEX['theme_file_updated']   = 'Your file was updated.';
$LEX['theme_file_create_note']   = '<!-- %s created by expanse. %s -->';
$LEX['theme_file_created']   = 'Your file was created. You can edit it <a href="index.php?cat=admin&amp;sub=theme_editor&amp;theme=%s&amp;themefile=%s">here</a>.';
$LEX['theme_existing_file']   = 'Sorry, but there is already a file with that name.';
$LEX['theme_missing_filename']   = 'Please enter in a file name.';
$LEX['theme_create_new_file']   = 'Create a new file';
$LEX['theme_create_in_folder']   = 'Create in folder';
$LEX['theme_create_filename']   = 'File name';
$LEX['theme_select_theme']   = 'Select a theme to edit';
$LEX['theme_select_file']   = 'Select a file to edit';
$LEX['theme_main_folder']   = 'Main folder';
$LEX['theme_by']   = 'by';
$LEX['theme_version']   = 'Version:';
$LEX['theme_edit_choose_note']   = 'You can edit your themes online. First, choose a theme to edit';
$LEX['theme_preview']   = 'Preview';
$LEX['theme_edit_text']   = 'Edit';
$LEX['theme_activate_text']   = 'Activate';
$LEX['theme_create_title']   = 'Create a new theme from scratch';
$LEX['theme_create_detail']   = 'If you would like to generate a new theme, with all the directories and files already there, you can enter in the name below, and the new theme will be created for you.';
$LEX['theme_create_theme_name']   = 'Theme name';
$LEX['theme_created']		= 'Your new theme has been created.';
$LEX['theme_not_created']	= 'Sorry, but your new theme could not be created. Please make sure there isnt already a theme in there by that name, and that you do in fact have permissions to write files.';
$LEX['theme_visit_author']	= 'Visit this person\'s website.';
$LEX['theme_name']			= 'Name';
$LEX['theme_title']			= 'Title';
$LEX['theme_description']	= 'Description';
$LEX['theme_author']		= 'Author';
$LEX['theme_version']		= 'Version';
$LEX['theme_editor_var_note'] = 'If you\'re not sure what variables you can add to your templates, you can go ahead and visit the <a href="http://help.expansecms.org/Overview/ThemesTemplates#toc3" target="_blank">Help Documentation</a>.';

/*   Plugins module   //-------------------------------*/
$LEX['plugin_installed']	= 'Your plugin has been installed.';
$LEX['plugin_not_installed']= 'Sorry, but that plugin couldn\'t be installed.';
$LEX['plugin_uninstalled']	= 'Your plugin has been uninstalled.';
$LEX['plugin_not_uninstalled']= 'Sorry, but that plugin couldn\'t be uninstalled.';
$LEX['plugin_note']	= 'Plugins are a way for you to customize and extend not only your copy of expanse, but also your site. For more information on getting or creating plugins, please check out the <a href="http://help.expansecms.org" target="_blank">Help Documentation</a>.';
$LEX['plugin_title']	= 'Plugins';
$LEX['menu_manage_plugins']	= 'Manage Plugins';

/*   Users module   //-------------------------------*/
$LEX['user_add_title']		= 'Add a new user';
$LEX['user_edit_title']		= 'Edit an existing user';
$LEX['user_added']			= '<strong>%s</strong> has been added. You can edit his or her account <a href="index.php?type=edit&amp;cat=admin&sub=users&action=edit&id=%s">here</a>.';
$LEX['user_not_added']		= '%s could not be added.<br />Why?<br />%s';
$LEX['user_missing_permissions'] = 'Please make sure that the user has a username, and that they have SOME permissions. If you wish to disable the account until a later time, choose "Disabled".';
$LEX['user_duplicate_username'] 	= 'Sorry, but there is already a user named &#8220;%s&#8221;. Please choose another username.';
$LEX['user_invalid_password'] 	= 'Please make sure that the password and the confirm password entries match, that they\'re not empty, and that your password is at least 6 characters.';
$LEX['user_password_note']		= 'Passwords must be at least 6 characters long.';
$LEX['user_username']		= 'Username';
$LEX['user_displayname']		= 'Display Name';
$LEX['user_email']		= 'E-mail';
$LEX['user_url']		= 'User URL';
$LEX['user_password']		= 'Password';
$LEX['user_confirm_password']		= 'Confirm Password';
$LEX['user_privileges']	= 'Privileges';
$LEX['user_admin']			= 'Admin';
$LEX['user_moderator']		= 'Moderator:';
$LEX['user_moderator_admin']	= 'Let users modify all items in the category';
$LEX['user_moderator_user']		= 'Let users modify only their items';
$LEX['user_disable_account']		= 'Disable Account';
$LEX['user_disable_account_note']	= 'You can disable this account\'s access';
$LEX['user_disable_label']		= 'Disable';
$LEX['user_primary_note']		= 'This is your primary user account. It cannot be deleted, but you can change the user details.<br />If you change your username and/or password, you will be logged out on the next page load after you change it. You will have to log in with the new username and/or password.';
$LEX['user_new_password']		= 'New Password';
$LEX['user_confirm_new_password']		= 'Confirm New Password';
$LEX['user_new_password_note']		= '(Leave Blank to Keep the Same Password)';
$LEX['user_account_deleted']		= '<strong>%s</strong>\'s account has been deleted.';
$LEX['user_account_not_deleted']	= '<strong>%s</strong>\'s account could not be deleted.';
$LEX['user_account_primary_not_deleted']		= '<strong>%s</strong> is the primary user, and cannot be deleted.';
$LEX['user_no_username']		= 'No username';
$LEX['user_edit_text']   = 'Edit';

/*
------------------------------------------------------------
Months
============================================================
*/
$LEX['first_month'] 	= 'January';
$LEX['second_month'] 	= 'February';
$LEX['third_month'] 	= 'March';
$LEX['fourth_month'] 	= 'April';
$LEX['fifth_month'] 	= 'May';
$LEX['sixth_month'] 	= 'June';
$LEX['seventh_month'] 	= 'July';
$LEX['eighth_month'] 	= 'August';
$LEX['ninth_month'] 	= 'September';
$LEX['tenth_month'] 	= 'October';
$LEX['eleventh_month'] 	= 'November';
$LEX['twelfth_month'] 	= 'December';
/*
------------------------------------------------------------
Javascript text
(all javascript must be prefixed with js_)
============================================================
*/
/*   Install   //-------------------------------*/
$LEX['js_eula'] 	= 'Please read the EULA and check the box indicating that you have read and understood the agreement. If you cannot see the checkbox, scroll down to the bottom of the EULA box.';

/*   Uninstall   //-------------------------------*/
$LEX['js_delete_uploads'] = 'Are you SURE you want to delete the uploads folder? All of your uploaded files will be completely removed.';
$LEX['js_delete_db'] = 'Are you SURE you want to clear the database and delete the config file? This procedure is irreversible, and all of your records will be gone forever. expanse will be considered uninstalled, and it\'s always a bit sad to say goodbye.';
$LEX['js_delete_config'] = 'Are you SURE you want to delete the config file? This procedure is irreversible, and you will HAVE to reinstall expanse if you wish to access your information.';
$LEX['js_uninstall'] = 'Are you SURE you want to uninstall expanse? This procedure is irreversible.';

/*   Reordering   //-------------------------------*/
$LEX['js_reorder'] = 'Reorder your items';
$LEX['js_reorder_finished'] = 'I\'m done reordering';
$LEX['js_reorder_notice'] = 'If you wish to reorder your items, you must go to Admin Settings > Preferences > Theme/Appearance Settings and choose to order your categories by User Rank.';
$LEX['js_wait_notice'] = 'Please wait...';
$LEX['js_reorder_menu']     = 'Reorder your menu';

/*   Editor resizing   //-------------------------------*/
$LEX['js_increase_editor'] = 'Increase the editor size';
$LEX['js_decrease_editor'] = 'Decrease the editor size';

/*   Custom fields (defaults)  //-------------------------------*/
$LEX['js_add_custom_field'] = '+ Add another custom field';
$LEX['js_remove_custom_field'] = '- Remove the last custom field';
$LEX['js_clear_custom_field'] = 'Clear all custom fields';
$LEX['js_custom_field'] = 'Field';
$LEX['js_clear_confirm_custom_field'] = 'Are you sure you want to delete all of your fields?';

/*   Custom fields (custom)   //-------------------------------*/
$LEX['js_custom_label_text'] = 'Label';
$LEX['js_custom_field_text'] = 'Value';
$LEX['js_custom_variable_text'] = 'Custom variable:';
$LEX['js_custom_delete_field'] = 'Delete this field';
$LEX['js_increase_field_size'] = 'Increase the field size';
$LEX['js_decrease_field_size'] = 'Decrease the field size';

/*   Custom fields (sub-categories)   //-------------------------------*/
$LEX['js_add_subcat'] = '+ Add a sub-category';
$LEX['js_remove_subcat'] = '- Remove a sub-category';
$LEX['js_clear_subcat'] = 'Clear all sub-categories';
$LEX['js_subcat_label'] = 'Sub-Category Name:';
$LEX['js_subcat_descr'] = 'Sub-Category Description (optional)';
$LEX['js_subcat_clear_confirm'] = 'Are you sure you want to delete all of your sub-categories?';

/*   Custom fields (options)   //-------------------------------*/
$LEX['js_add_option'] = '+ Add an option';
$LEX['js_remove_option'] = '- Remove an option';
$LEX['js_clear_option'] = 'Clear all options';
$LEX['js_option_label'] = 'Option ';
$LEX['js_option_clear_confirm'] = 'Are you sure you want to delete all of your options?';

/*   Custom fields (upload fields)   //-------------------------------*/
$LEX['js_add_image'] = '+ Add an upload field';
$LEX['js_remove_image'] = '- Remove an upload field';
$LEX['js_clear_image'] = 'Clear all upload fields';
$LEX['js_image_label_file'] = 'File';
$LEX['js_image_label'] = 'Image';
$LEX['js_image_clear_confirm'] = 'Are you sure you want to delete all of your upload fields?';


/*   Misc.   //-------------------------------*/
$LEX['js_check_boxes'] = 'Check them all ';
$LEX['js_admin_rights'] = '(Total Access, Users, Database Settings, Etc.)';
$LEX['js_keep_checked'] = 'Keep this button checked';
$LEX['js_keep_unchecked'] = 'Keep this button unchecked';
$LEX['js_enter_user_details'] = 'Please make sure that you\'ve entered the username and password';
$LEX['js_bookmark_ff'] = 'Bookmark me';
$LEX['js_bookmark_ie'] = 'Add me to your favorites';
$LEX['js_mb_include_subcats']     = 'Include sub-categories?';
$LEX['js_crop_thumb']     = 'Crop Thumbnail';
$LEX['js_resize_thumb']     = 'Resize Thumbnail';
$LEX['js_crop_save']     = 'Save changes';
$LEX['js_crop_reset']     = 'Reset';
$LEX['js_crop_discard']     = 'Discard changes';
?>