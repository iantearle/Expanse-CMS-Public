/*
 * FCKeditor - The text editor for Internet - http://www.fckeditor.net
 * Copyright (C) 2003-2010 Frederico Caldeira Knabben
 *
 * == BEGIN LICENSE ==
 *
 * Licensed under the terms of any of the following licenses at your
 * choice:
 *
 *  - GNU General Public License Version 2 or later (the "GPL")
 *    http://www.gnu.org/licenses/gpl.html
 *
 *  - GNU Lesser General Public License Version 2.1 or later (the "LGPL")
 *    http://www.gnu.org/licenses/lgpl.html
 *
 *  - Mozilla Public License Version 1.1 or later (the "MPL")
 *    http://www.mozilla.org/MPL/MPL-1.1.html
 *
 * == END LICENSE ==
 *
 * Plugin: automatically resizes the editor until a configurable maximun
 * height (FCKConfig.AutoGrowMax), based on its contents.
 */
 
 
function FCKAutoGrow_Check()
{
   var oInnerDoc = FCK.EditorDocument ;

   var iFrameHeight, iInnerHeight ;

   if ( FCKBrowserInfo.IsIE )
   {
      iFrameHeight = FCK.EditorWindow.frameElement.offsetHeight ;
      iInnerHeight = oInnerDoc.body.scrollHeight ;
   }
   else
   {
      iFrameHeight = FCK.EditorWindow.innerHeight ;
      iInnerHeight = oInnerDoc.body.offsetHeight ;
   }

   var iDiff = iInnerHeight - iFrameHeight ;

   if ( iDiff != 0 )
   {
      var iMainFrameSize = window.frameElement.offsetHeight ;

      if ( iDiff > 0 && iMainFrameSize < FCKConfig.AutoGrowMax )
      {
         iMainFrameSize += iDiff ;
         if ( iMainFrameSize > FCKConfig.AutoGrowMax )
            iMainFrameSize = FCKConfig.AutoGrowMax ;
      }
      else if ( iDiff < 0 && iMainFrameSize > FCKAutoGrow_Min )
      {
         iMainFrameSize += iDiff ;
         if ( iMainFrameSize < FCKAutoGrow_Min )
            iMainFrameSize = FCKAutoGrow_Min ;
      }
      else
         return ;

      if(window.frameElement.style.height) {
		   window.frameElement.style.height=iMainFrameSize;
		} else {
		   window.frameElement.height = iMainFrameSize;
		}
      window.frameElement.style.height = iMainFrameSize+"px";

      // Gecko browsers use an onresize handler to update the innermost
      // IFRAME's height. If the document is modified before the onresize
      // is triggered, the plugin will miscalculate the new height. Thus,
      // forcibly trigger onresize. #1336
      if ( typeof window.onresize == 'function' )
         window.onresize() ;
   }
}
