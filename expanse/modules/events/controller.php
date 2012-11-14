<?php
/**************************************************
Module information, installation functions (if any),
and POST handling logic (if any)
***************************************************/

//Must be included at the top of all module files.
if(!defined('EXPANSE')){ die('Sorry, but this file cannot be directly viewed.'); }

class Events extends Module {
	// This is the meta data for the category.
	var $name = L_EVENTS_NAME;
	var $description = L_EVENTS_DESCRIPTION;
	// Inherit the rest of the category meta-data

	// Leave blank, going to use the inherited methods.
}
