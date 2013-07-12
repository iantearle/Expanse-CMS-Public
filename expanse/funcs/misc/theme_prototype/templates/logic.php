<?php 
/*
Logic.php
---------------------------
This page allows you to add and extend custom variables and loops to your pages.
For instance, if you wanted to have your recent photos on your home page, or wanted to add a reusable variable for your site, you would add it here.
Broken down simply, there are two primary functions that add variables, and add loops, and they, of course, are titled add_variable() and add_loop, respectively.

add_variable takes the following parameters:
add_variable('VARIABLE_LIST', 'AREA_TO_ADD_VARIABLE_TO', 'LOOP');
VARIABLE_LIST is a list of variable name:variable value pairs, seperated by a | (eg 'name:Nate|date:2007|city:Pomona')
AREA_TO_ADD_VARIABLE_TO is one of the following options: header, menu, main, footer . This is the area of the template where you want to assign the variable. If nothing is specified, it will automatically add it to the main area.
LOOP is the loop in the AREA that you wish to add it to. If nothing is specified, it will automatically just add it to the main area of the page, outside of any loops.
If you add a variable that is already in the template, it will override it. So if you add a variable called title to the main area and the content loop, then every title in that loop will be the value you've given it.

An example of using this to add a variable to the footer area would be like so:
add_variable('portfolio:nate_portfolio', 'footer');
Now, when you add {portfolio} to your footer template, it will read out nate_portfolio

*NOTE: you must put the variable in your template for it to be recognized

add_loop takes the following parameters
add_loop('CONDITIONS', 'LOOP_NAME');
CONDITIONS is a list of conditions that you want to create the loop with. This list looks similar to the  variable list above, but takes different arguments. For example, to get a list of recent photos, you would do something like: 'category:photos|howmany:3|orderby:newestontop'
LOOP_NAME is the name that you wish to give to your loop so that you may call it in your template.

An example of using this to add a loop to the main area of your site is like so:
add_loop('category:photos|howmany:3|orderby:newestontop', 'recent_photos');

Then you would add something like this to your template (outside of any other loops)
{loop:recent_photos}
<img src="{thumbnail}" alt="{title}" />
{/loop}

Conditionals
--------------------
A conditional is a snippet that you use to determine if something is happening. A list of conditionals is below, but first, an example:

if(is_home()){
add_loop('category:photos|howmany:3', 'recent_photos');
}

if(is_single()){
add_variable('portfolio:nate_portfolio');
}
----------

is_home()
This conditional indicates whenever you are on the home page of your site

is_single()
This conditional indicates whenever you are on a full/detail view of an item

is_subcat()
This conditional indicates whenever you are browsing a subcategory

is_userpage()
This conditional indicates whenever you are on a custom, user-created page

is_feed()
This conditional indicates whenever you are looking at the RSS/XML feed
*/
?>