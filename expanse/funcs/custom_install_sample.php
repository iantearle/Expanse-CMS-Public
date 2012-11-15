<?php
/*
---------------------------------------------------------------------
Private Labeling
If you'd like to hide the fact that you're using expanse from your
end users, you can "private label" expanse so that they
think they're using a custom CMS.
This does NOT allow you to redistribute expanse as your own, nor
does it in any way void or supersede the End User License Agreement.
=====================================================================
*/

// Change false to true (without quotes) in order to activate private labeling

$private_label 	= false;
$company_name	= ''; // Your company name
$company_url	= ''; //Your company's website
$cms_name		= ''; // The name of the actual CMS. This will be the company name if left blank

// The URL to your company's logo. Must be 151px wide and 33px high
// It would also probably look best as a transparent gif or png,
// or it should have this background color: #f5f5f5

$company_logo	= '';

// Company news feed URL
// If you'd like some specialized news to show on the main page
// you can enter in the URL to the RSS feed here:

$custom_news_feed = '';

$expanse_folder = '';
