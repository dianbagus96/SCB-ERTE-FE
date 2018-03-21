<?php
/*******************************************************************************
* Class   : FieldHandler                                                       *
* File    : fieldHandler.class.php                                             *
* Version	: 1.3                                                                *
* Date		: 16-09-2005                                                         *
* Author	: Martin             					                                       *
* License	: Freeware                                                           *
*                                                                              *
* Class for handling field information from a table.													 *
*                                                                              *
* You may use, modify and redistribute this software as you wish.              *
*******************************************************************************/
class FieldHandler{
  
  /** CONSTANTA FOR ALIGN */
  var $LEFT = "LEFT";
  var $CENTER = "CENTER";
  var $RIGHT = "RIGHT";

  /** CONSTANTA FOR SORTABLE */  
  var $SORTABLE_ON = TRUE;
  var $SORTABLE_OFF = FALSE;
  var $SORT_ASC = TRUE;
  var $SORT_DESC = FALSE;  
  
  /** CONSTANTA FOR POSITION */  
  var $TOP = "TOP";
  var $BOTTOM = "BOTTOM";
  var $BOTH = "BOTH";
		
  /** Define the field name, it must filled. */  
  var $name;

  /** Define the field align when draw at table. */  
  var $align;

  /** Ability to sorting the field, ASC / DESC. Default is False. */  
  var $sortable = false;
  
  /** sorting the field by ASC / DESC. Default is ASC. */  
  var $sortby = true;
    
  /** Link for the value of current field. */  
  var $linker;
  
  /** Set the header column name. */  
  var $headerName;
  
  /** Set the width column. */  
  var $width;

  /** Set the width column. */  
  var $wrap = true;
  
  /** Set the hidden column. */  
  var $hidden = false;  
  
  /** Set the header as parent, it will not parse to any data header name. */
  var $isParent = false;
  
  /** Specify the parent header. */
  var $parentName;
      
  /** Constructor class. */  
  function FieldHandler(){}
}

// To make easier when setting up variabels.
$F_HANDLER = new FieldHandler();
?>
