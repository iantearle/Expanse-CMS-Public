<?
/*
##################################################
#
# Filename..........: $RCSfile: Calendar.class,v $
# Original Author...: Anthony L. Awtrey
# Version...........: $Revision: 0.1 $
# Last Modified By..: $Author: aawtrey $
# Last Modified.....: $Date: 2006/09/21 09:34:00 $
#
# Copyright 2006 Anthony Awtrey
#
# This program is free software; you can redistribute it and/or modify
# it under the terms of the GNU General Public License as published by
# the Free Software Foundation; either version 2 of the License, or
# (at your option) any later version.
#
# This program is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
#
# You should have received a copy of the GNU General Public License
# along with this program; if not, write to the Free Software
# Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
#
*/

/*
 * This table generates HTML calendars
 */

class Calendar {

  var $year  = "";
  var $month = "";
  var $appt = array( );

  /*
   * Constructor
   */
  function __construct($year='',$month='')
  {
    if ( $year != '' ) {
      $this->year = $year;
    } else {
      $this->year = gmstrftime("%Y", gmmktime());
    }
    if ( $year != '' ) {
      $this->month = $month;
    } else {
      $this->month = gmstrftime("%m", gmmktime());
    }
  }

  function __destruct()
  {
  }

  function _get_last_dom($year,$month)
  {
    return gmstrftime("%d",gmmktime(0,0,0,$month+1,0,$year));
  }

  function _get_first_dow($year,$month)
  {
    $fdow = gmstrftime('%w',gmmktime (0,0,0,$month,1,$year));
    if ( $fdow == 0 ) { $fdow = 7; }
    return $fdow;
  }

  function _get_month_name($month)
  {
    return gmstrftime("%B",gmmktime(0,0,0,$month,1,$this->year));
  }

  function _render_month($year,$month)
  {
    $mon  = $this->_get_month_name($month);
    $days = $this->_get_last_dom($year,$month);
    $fdow = $this->_get_first_dow($year,$month);
    $result  = '<table style="border-style:dotted; border-color:#C0C0C0; border-width:1px;">';
    $result .= '  <tr><td colspan="7" align="center" style="background: #f0f0f0;">'.$mon.' '.$year.'</td></tr>';
    $result .= '  <tr><td align="center">M</td><td align="center">T</td><td align="center">W</td><td align="center">T</td><td align="center">F</td><td align="center">S</td><td align="center">S</td></tr>';
    $day = 1;
    while ( $day <= $days ) {
      $result .= "  <tr>";
      for ( $i=1; $i<8; $i++) {
        if (
             ( ( $day == 1 ) && ( $fdow == $i ) ) ||
             ( ( $day > 1 ) && ( $day <= $days ) )
           )
        {
          if ( $this->get_appt($year,$month,$day) != '' ) {
            $result .= '<td align="right"><a href="'.$this->get_appt($year,$month,$day).'">'.$day.'</a></td>';
          } else {
            $result .= '<td align="right">'.$day.'</td>';
          }
          $day++;
        } else {
          $result .= '<td>&nbsp;</td>';
        }
      }
      $result .= "</tr>";
    }
    $result .= "</table>";
    return($result);
  }

  function _render_year($year)
  {
    $month = 1;
    $result = "<table>";
    for ( $c=1; $c<5; $c++ ) {
      $result .= "<tr>";
      for ( $r=1; $r<4; $r++ ) {
        $result .= "<td valign='top'>";
        $result .= $this->_render_month($year,$month);
        $month++;
        $result .= "</td>";
      }
      $result .= "</tr>";
    }
    $result .= "</table>";
    return($result);
  }

  function get_appt($year,$month,$day)
  {
    return( $this->appt[ gmmktime(0,0,0,$month,$day,$year) ] );
  }

  function set_appt($year,$month,$day,$url)
  {
    $this->appt[ gmmktime(0,0,0,$month,$day,$year) ] = $url;
    return(true);
  }

  function render($year='',$month='')
  {
    if ( $year != '' && $year >= 0 && $year <= 9999 && $month == '' ) {
      return($this->_render_year($year));
    } elseif ( $year != '' && $year >= 0 && $year <= 9999 && $month != '' && $month >= 1 && $month <= 12 ) {
      return($this->_render_month($year,$month));
    } else {
      return($this->_render_year($this->year));
    }
  }
}
?>