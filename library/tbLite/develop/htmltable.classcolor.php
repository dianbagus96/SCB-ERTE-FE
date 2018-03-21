<?php

require_once('fieldHandler.class.php');
require_once('pagenav.class.php');

/*******************************************************************************
* Class   : HTMLTable                                                          *
* File    : htmltable.class.php                                                *
* Version	: 1.3                                                                *
* Date		: 16-09-2005                                                         *
* Author	: Martin             					                                       *
* License	: Freeware                                                           *
*                                                                              *
* Class for drawing a table automaticly when get input from SQL(select command)*
* So, You will more faster and easier when developing a simple output as table *                                                                             *                                                                             *
*                                                                              *
* Todo on Next Development:                                                    *
*    - Give a Footer for total counting                                        *
*    - Value from SQL, can replace by array                                    *
*    - Standardize Class-Style                                                 *
*    - Make SQL Parser, More Flexible and Complex                              *
*                                                                              *
* You may use, modify and redistribute this software as you wish.              *
*******************************************************************************/
class HTMLTableColor{

	/*******************************************************************************
	*                               Public Variable                                *
	*******************************************************************************/
		
	/** Handling current connection to DB. */
	var $connection;

	/** Fields of Query Results Handler, An Array of Class FieldHandler.For convinience, it must start from 0. */	
	var $field;
	
	/** Set the SQL Query to this variabel. Please, don't give syntax ORDER to the command line. */	
	var $SQL;

	/** Define the property variable, it must filled. */  
	var $width;
	 
	var $border;
	  
	var $cellpadding;    
	  
	var $cellspacing;  
	
	var $frameborder;
	
	/** Define Navigator variabel */
	var $showNavigator;
	
	var $navPosition;
	
	var $navRowSize;
	
	var $navPageSize;
	
	/** Define checkbox property */
	var $showCBX;
	var $colRef;
	
	/** Define RDPanel Property */
	var $showRDPanel;
	var $RDPanel_array;
	var $RDPanelPosition;
	var $colrefRD;
	
	/** Define the delete button variable */
	var $delcmd;
	var $delButton;
	var $delPosition;
	
	/** Define the add button variable */
	var $addcmd;	
	var $addButton;
	var $addPosition;
	var $isblank;
	
	/*******************************************************************************
	*                               Private Variable                               *
	*                   Dont use any variable below in outer class                 *
	*******************************************************************************/	
	
	/** Variable to draw data navigator. It's a simple navigator. The Code has taken from adoDB */
	var $pagenav;	

	/** ResultSet Variabel. */	
	var $dataset;
			
	/*******************************************************************************
	*                               Public methods                                 *
	*******************************************************************************/
	var $ajaxMod1 = -1;
	var $ajaxMod2 = -1;
	var $ajaxMod3 = -1;
	var $ajaxMod4 = -1;
	var $ajaxMod5 = -1;
	var $ajaxMod6 = -1;
	var $ajaxMod7 = -1;	
	var $ajaxMod8 = -1;	
	var $ajaxMod9 = -1;	
	var $ajaxMod10 = -1;	
	var $ajaxMod11 = -1;
	var $ajaxMod12 = -1;
	var $ajaxMod13 = false;
	var $ajaxMod15 = -1;
	var $ajaxMod16 = -1;
	var $ajaxMod17 = -1;	
	var $ajaxMod18 = -1;	
	var $nominalDiterima = 0;	
	var $backlink;
	var $cbxMod1 = false;
	var $opsiPlus1=false;
	var $showDetail = array(1000,1000);
	function opsiPlus1(){
		$this->opsiPlus1 = true;	
	}
	/**
	 * Constructor Class Table Data, All default value of variabel settle here.
	 */
	function HtmlTableColor(){
	  $this->field = array(new FieldHandler());  
	  $this->border = 0;
	  $this->cellpadding = 2;
	  $this->cellspacing = 1;
	  $this->frameborder = 0;		
		$this->showNavigator = true;
		$this->navPosition = "TOP";
		$this->navRowSize = 10;
		$this->navPageSize = 10;
		$this->showCBX = true;
		$this->showRD = false;
		$this->RDPanelPosition = "TOP";
		$this->colRefRD = 0;
		$this->colRef = 0;
		$this->addButton = false;
		$this->delButton = false;
		$this->isblank = false;
		$this->addPosition = "TOP";
		$this->delPosition = "TOP";
	}
	
	/**
	 * function to show the page navigator of data table
	 */
	function showPager($toggleOnOff,$position,$rowSize,$pageSize){
		$this->navRowSize = $rowSize;
		$this->navPosition = $position;
		$this->navPageSize = $pageSize;	
		$this->showNavigator = $toggleOnOff;
	}
	
	/** Function to show Add Button */
	function showAddButton($toggleOnOff,$position,$parameter,$isblank){
		$this->addButton = $toggleOnOff;
		$this->addPosition = $position;
		$this->addcmd = $parameter;
		$this->isblank = $isblank;
	}
	
	/** Function to show Delete Button */
	function showDelButton($toggleOnOff,$position,$parameter){
		$this->delButton = $toggleOnOff;
		$this->delPosition = $position;
		$this->delcmd = $parameter;
	}
	
	/**
	 * Function to show check box.
	 */	
	function showCheckBox($toggleOnOff,$colReference){
		$this->showCBX = $toggleOnOff;
		$this->colRef = $colReference;
	}
	var $labelNew='';
	var $btnNew =false;
	var $linkNew = '#';
	function showBtnNew($str,$link=''){
		$this->labelNew =$str;	
		$this->btnNew = true;
		$this->linkNew = $link;
		
	}
	
	/**
	 * Function to show RD Panel
	 */	
	function showRDPanel($toggleOnOff,$position,$colReference,$array){
		$this->showRD = $toggleOnOff;
		$this->colRefRD = $colReference;
		$this->RDPanel_array = $array;
		$this->RDPanelPosition = $position;
	}
		
	/**
	 * Function to draw table.
	 */
	function drawTable(){	
		$this->sort_func();	
	  $count_field = count($this->field);
	  for ($i = 0; $i <= $count_field - 1; $i++) {
			$sort = $_REQUEST[$this->field[$i]->name];
			if ($sort != ""){
				if ($sort == "DESC"){
					$this->field[$i]->sortby = FALSE;		
				}
				else{
					$this->field[$i]->sortby = TRUE;
				}
			}
	  }	  
		
	  $this->execQuery();
    if ($this->dataset->size() > 0 ){			
			echo "<form name=\"formtable\" action='".base_url.substr($_SERVER['REQUEST_URI'],1,strlen($_SERVER['REQUEST_URI']))."' method=\"GET\" enctype='multipart/form-data'>";
	
			// Draw a navigator, place it to the position. - TOP - BOTH
			$this->pagenav = new PageNav($this->dataset,$this->navRowSize,$this->navPageSize,$this->width);
			if (in_array($this->navPosition, array ("TOP", "BOTH"))){
				$this->pagenav->showPager($this->showNavigator);
			}
			else{
				$this->pagenav->showPager(false);
			}
			if($this->opsiPlus1==true){
				$this->pagenav->opsiPlus1=true;
			}
			if($this->btnNew){
				$this->pagenav->btnNew($this->labelNew,$this->linkNew);
			}

			if (in_array($this->RDPanelPosition, array ("TOP", "BOTH"))){
				$this->pagenav->showRDPanel($this->showRD,$this->RDPanel_array);
			}
			else{
				$this->pagenav->showRDPanel(false,$RDPanel_array);
			}
						
			if (in_array($this->addPosition, array ("TOP", "BOTH"))){
				$this->pagenav->showAddButton($this->addButton,$this->addcmd,$this->isblank);
			}
			else{
				$this->pagenav->showAddButton(false,$this->addcmd,$this->isblank);
			}
					
			if (in_array($this->delPosition, array ("TOP", "BOTH"))){
				$this->pagenav->showDelButton($this->delButton);
			}
			else{
				$this->pagenav->showDelButton(false);	
			}
			$this->pagenav->drawPageNavigator(0);
						
			// Draw the table  
			echo "<table width=\"$this->width\" border=\"$this->border\" cellpadding=\"0\" cellspacing=\"0\" frameborder=\"$this->frameborder\">";
			$this->parseDefHeader();
			$this->drawHeader();
			$this->drawData();			
			echo "</table>";
			
			// Draw a navigator, place it to the position. - BOTTOM - BOTH
			//if (in_array($this->navPosition, array ("BOTTOM", "BOTH"))){
			//	$this->pagenav->showPager($this->showNavigator);
			//	$this->pagenav->drawPageNavigator(1);			
			//}		
			if (in_array($this->navPosition, array ("BOTTOM", "BOTH"))){
				$this->pagenav->showPager($this->showNavigator);
			}
			else{
				$this->pagenav->showPager(false);
			}
			
			if (in_array($this->RDPanelPosition, array ("BOTTOM", "BOTH"))){
				$this->pagenav->showRDPanel($this->showRDPanel,$RDPanel_array);
			}
			else{
				$this->pagenav->showRDPanel(false,$this->RDPanel_array);
			}
						
			if (in_array($this->addPosition, array ("BOTTOM", "BOTH"))){
				$this->pagenav->showAddButton($this->addButton,$this->addcmd,$this->isblank);
			}
			else{
				$this->pagenav->showAddButton(false,$this->addcmd,$this->isblank);
			}
					
			if (in_array($this->delPosition, array ("BOTTOM", "BOTH"))){
				$this->pagenav->showDelButton($this->delButton);
			}
			else{
				$this->pagenav->showDelButton(false);	
			}
			$this->pagenav->drawPageNavigator(1);			
				
			echo "</form>";		
		}
	}
	
	/*******************************************************************************
	*                               Private methods                                *
	*******************************************************************************/
	
	/**
	 * Function to draw header.
	 */	
	function drawHeader(){
	  $count_column = $this->getColumnCount();
		/* 
			Cara draw headers:
			1.Baca semua field, cari yang (parent = "" atau isParent = true) and hidden = false;
			2.Draw field - field tersebut dg kondisi sbb:
				- Bila isParent false dan parent = "", maka rowspan = 2.
				- Bila isParent = true dan parent = "", maka hitung anaknya, dan colspan set sebanyak anak tersebut.
			3. Baca semua field, cari yang parent = "xxx" dan isParent = false;
			4. Draw field - field tsb.			  
		*/
	  if ($this->anyParent()){
	    $count_column = count($this->field);
	  	echo "<tr class=tbl_hdr>";
			
			if ($this->showCBX){
				echo "<th rowspan=\"2\" width=\"20\"><input type=\"checkbox\" name=\"cbxAll\" onClick='check(this)'></th>";
			}else{
				if ($this->showRD){
					echo "<th rowspan=\"2\" width=\"20\"></th>";
				}			
			}
			
	    for ($i = 0; $i <= $count_column-1; $i++) {
				if ((($this->field[$i]->isParent) | ($this->field[$i]->parentName == "")) & (!$this->field[$i]->hidden)){
					if ((!$this->field[$i]->isParent) & ($this->field[$i]->parentName == "")){
							echo "<th rowspan=\"2\" width=\"".$this->field[$i]->width."\">";
					}
					else{
						if (($this->field[$i]->isParent) & ($this->field[$i]->parentName == "")){
							$count_child = $this->countChild($this->field[$i]->name);
							echo "<th align=\"center\" colspan=\"$count_child\" width=\"".$this->field[$i]->width."\">";
						}
					}				
					//echo strtoupper(trim($this->field[$i]->headername));
					echo trim($this->field[$i]->headername);
					// draw sortable icon
					if (($this->field[$i]->sortable)&(strtoupper($this->field[$i]->name) == strtoupper($this->dataset->fieldName($i)))){
						$image = "";
						$parameter = $this->field[$i]->name;
						$value = "DESC";
						if ($this->field[$i]->sortby){
							$image = "images/asc2.gif";
						$value = "DESC";
						}
						else{
							$value = "ASC";
							$image = "images/desc2.gif";
						}
						$param = $this->make_parameter($parameter,$value);
						echo "<img src=\"$image\" onClick=\"OpenWindow('$param')\" width=\"10\" height=\"6\" class=\"sort\">";
					}
					echo "</th>";
	    	}  
			}			
			echo "</tr>";
			echo "<tr class=tbl_hdr>";
			
			// Baca yang parent = "xxx" 	  
	    for ($i = 0; $i <= $count_column-1; $i++) {
				if ((!$this->field[$i]->isParent) & ($this->field[$i]->parentName != "")){
					echo "<th align=\"center\" width=\"".$this->field[$i]->width."\">";
					//echo strtoupper(trim($this->field[$i]->headername));
					echo trim($this->field[$i]->headername);
					// draw sortable icon
					if ($this->field[$i]->sortable){
						$image = "";
						$parameter = $this->field[$i]->name;
						$value = "DESC";
						if ($this->field[$i]->sortby){
							$image = "images/asc2.gif";
						$value = "DESC";
						}
						else{
							$value = "ASC";
							$image = "images/desc2.gif";
						}
						$param = $this->make_parameter($parameter,$value);
						echo "<img src=\"$image\" onClick=\"OpenWindow('$param')\" width=\"10\" height=\"6\" class=\"sort\">";
					}
					echo "</th>";
				}  
			}			
	  	echo "</tr>";
	  }   
	  else{
			echo "<tr class=tbl_hdr>";
			
			if ($this->showCBX){
				echo "<th width=\"20\" nowrap><input type=\"checkbox\" name=\"cbxAll\" onClick='check(this)'></th>";
			}else{
				if ($this->showRD){
					echo "<th width=\"20\" nowrap style=\"background:url(".base_url."img/tab3.gif) repeat-x; height:22px;\"></th>";
				}
			}
			
			for ($i = 0; $i <= $count_column-1; $i++) {
				if (!$this->field[$i]->hidden){
					if( $cutat=strpos( $_SERVER['QUERY_STRING'], '&sort=' )){
						$urlcut = substr( $_SERVER['QUERY_STRING'], 0, $cutat );
					}
					else{
						$urlcut = $_SERVER['QUERY_STRING'];
					}
					$url_string='?'.$urlcut;									
				
					//echo "<th align=\"center\" width=\"".$this->field[$i]->width."\" class=div_tbl>"; //ganti disini css-nya
					if(strstr($_SERVER['REQUEST_URI'],'FormSearchSSP.php') || strstr($_SERVER['REQUEST_URI'],'FormSearchSandiRTE.php') || strstr($_SERVER['REQUEST_URI'],'FormSearchReception.php')){
						if(!$_GET['order']){
							$orderx='asc';
						}elseif($_GET['order']=='asc'){
							$orderx='desc';
						}else{
							$orderx='asc';
						}
					echo '<th width="'.$this->field[$i]->width.'" style="background:url('.base_url.'img/tab3.gif) repeat-x; height:22px; padding-left:7px; color:#ffffff; font-size:11px; font-family:arial; font-weight:bold; cursor: pointer;text-align:left" onclick="descascx(\''.str_replace(" ","_",trim($this->field[$i]->headername)).'\', \''.$url_string.'\', \''.$orderx.'\');">';
					}else{
					echo '<th width="'.$this->field[$i]->width.'" style="background:url('.base_url.'img/tab3.gif) repeat-x; height:22px; padding-left:7px; color:#ffffff; font-size:11px; font-family:arial; font-weight:bold; cursor: pointer;text-align:left" onclick="descasc(\''.strtolower($this->field[$i]->name).'\');"><input type="hidden" value="'.$_GET['order'].'" id="descasc'.$this->field[$i]->name.'">';					
					}
					echo trim($this->field[$i]->headername);
					// draw sortable icon
					if (($this->field[$i]->sortable)&(strtoupper($this->field[$i]->name) == strtoupper($this->dataset->fieldName($i)))){
						$image = "";
						$parameter = $this->field[$i]->name;
						$value = "DESC";
						if ($this->field[$i]->sortby){
							//$image = "images/asc2.gif";
							$image = "library/tbLite/develop/images/asc2.gif";
							$value = "DESC";
						}
						else{
							$value = "ASC";
							//$image = "images/desc2.gif";
							$image = "library/tbLite/develop/images/desc2.gif";
						}
						$param = $this->make_parameter($parameter,$value);
						echo "<img src=\"$image\" onClick=\"OpenWindow('$param')\" width=\"10\" height=\"6\" class=\"sort\">";
					}
					
					if( $cutat=strpos( $_SERVER['QUERY_STRING'], '&sort=' )){
						$urlcut = substr( $_SERVER['QUERY_STRING'], 0, $cutat );
					}
					else{
						$urlcut = $_SERVER['QUERY_STRING'];
					}
					$url_string='?'.$urlcut.'&sort='.trim($this->dataset->fieldName($i));									
					
					//echo '<br><a href="'.$url_string.'&order=ASC"><img border="0" src="library/tbLite/develop/images/asc.gif" class="sort" style="cursor:pointer;"></a> <a href="'.$url_string.'&order=DESC"><img border="0" src="library/tbLite/develop/images/desc.gif" class="sort" style="cursor:pointer;"></a>';					
					
					
					echo "</th>";
				}
			}  
			echo "</tr>";
	  }
	}
	
	/**
	 * Function to draw dataset.
	 */		
	function drawData(){
		$count_column = $this->getColumnCount();
		$loop = 0;
		while($this->dataset->next()){
			$loop = $loop +1;
		  if ($this->showNavigator){
				if ($loop < $this->pagenav->first_row){
					continue;
				}				
				if (($loop >= $this->pagenav->first_row)&($loop <= $this->pagenav->last_row)){
					$this->drawRow($count_column, $loop);
				}
				if ($loop > $this->pagenav->last_row){
					break;
				}
			}
			else{
				$this->drawRow($count_column,$loop);
			}
		}
		echo "<input type='hidden' id='total' value='".$loop."'>";
		
	}
	
	/**
	 * Sub Function of draw data
	 */
	function drawRow($count_column, $loop=''){
		if ($this->anyParent()){
			$count_column = count($this->field);;
			// cara draw multi column.
			echo "<tr class=tbl_row>";

			if($this->showCBX){
				if(is_array($this->colRef)){
					$cbxVal = "";
					for ($i=0;$i<count($this->colRef);$i++) {
						$data = $this->colRef[$i];
						$nilai = $this->dataset->get($this->field[$data]->name);
						$cbxVal .= $nilai.";";
					}
					$cbxVal = substr($cbxVal,0,strlen($cbxVal) - 1);				
				}else{
					$cbxVal = $this->dataset->get($this->field[$this->colRef]->name);
				}
				if ($cbxVal == ""){
					$cbxVal = "&nbsp;";
				}
				$onclick ="";
				if($this->ajaxMod1>-1){  
					$onclick = "onclick=\"javascript:showText(this,'".$loop."')\"";
				}elseif($this->ajaxMod3>-1){
					$onclick = "onclick=\"javascript:showText2(this,'".$loop."')\"";	
				}elseif($this->ajaxMod5>-1){
					$onclick = "onclick=\"javascript:showText3(this,'".$loop."')\"";	
				}elseif($this->ajaxMod8>-1){
					$onclick = "onclick=\"javascript:showText4(this,'".$loop."')\"";	
				}elseif($this->ajaxMod11>-1){
					$onclick = "onclick=\"javascript:showText5(this,'".$loop."')\"";	
				}elseif($this->ajaxMod15>-1){
					$onclick = "onclick=\"javascript:showText6(this,'".$loop."')\"";	
				}
				
				$onclick .= " class='cbxBiasa'";
				if($this->cbxMod1==true){
					echo "<td width=\"20\" nowrap><input type=\"checkbox\" name=\"cbx[".$loop."]\" value=\"$cbxVal\" id='cbx".$loop."' ".$onclick."></td>";
				}else{
					echo "<td width=\"20\" nowrap><input type=\"checkbox\" name=\"cbx[]\" value=\"$cbxVal\" id='cbx".$loop."' ".$onclick."></td>";
				}
			}else{
				if($this->showRD){					
					if(is_array($this->colRefRD)){
						$rdVal = "";
						for ($i=0;$i<count($this->colRefRD);$i++) {
							$data = $this->colRefRD[$i];
							$nilai = $this->dataset->get($this->field[$data]->name);
							$rdVal .= $nilai.";";
						}
						$rdVal = substr($rdVal,0,strlen($rdVal) - 1);				
					}else{
						$rdVal = $this->dataset->get($this->field[$this->colRefRD]->name);
					}				
					if ($rdVal == ""){
						$rdVal = "&nbsp;";
					}
					echo "<td width=\"20\" nowrap><input type=\"radio\" name=\"radiopanel\" value=\"$rdVal\"></td>";				
				}
			}
			
			for ($i = 0; $i <= $count_column-1; $i++) {
				if(!$this->field[$i]->isParent){
			
					$data = $this->dataset->get($this->field[$i]->name);
					if ($data == ""){
						$data = "&nbsp;";
					}
						
					//check dulu apakah hidden bernilai true.
					if ($this->field[$i]->hidden){
						echo "<input type=\"hidden\" name=\"".$this->field[$i]->name."\" value=\"$data\">";
					}
					else{
						
						$wrap = "<td";
						if (!$this->field[$i]->wrap){
							$wrap .=" nowrap";
						} 
						$wrap .= ">";
						echo $wrap;
						$align = $this->field[$i]->align;
						echo "<div align=\"".$align."\" class=div_tbl>";
	
						if ($this->field[$i]->linker != ""){
							$matches = $this->parseLink($this->field[$i]->linker);
							$count_match = count($matches);
							$mylink = $this->field[$i]->linker;
							for ($loop = 0; $loop <= $count_match-1; $loop++) {
								$datalink = $matches[$loop][0];
								$replace = substr($datalink,1);
								$mylink = str_replace($datalink, $this->dataset->get($replace),$mylink);
							}				
							echo "<a href=\"$mylink\" target=\"_blank\">$data</a>";
						}
						else{
							echo trim($data)==""?"&nbsp;":$data;
						}
						echo "</div>";
						echo "</td>";
					}
				}
			}
			echo "</tr>";
		}
		else{		
			
			if($this->ajaxMod13==10){
				$hari = $this->dataset->get($this->ajaxMod13);	
				if($hari>90){
					$tr ='<tr style="border-bottom: 1px solid #D7D7D7; background:#FAC7B8;">';
				}elseif($hari>70 && $hari<=90){
					$tr ='<tr style="border-bottom: 1px solid #D7D7D7; background:#F4F99B;">';	
				}else{
					$tr ='<tr style="border-bottom: 1px solid #D7D7D7; background:#FFF;">';
				}				
			}elseif($this->ajaxMod13==9){
				$hari = $this->dataset->get($this->ajaxMod13);	
				$tr =($hari>3) ? '<tr style="border-bottom: 1px solid #D7D7D7; background:#F4F99B;">' : '<tr style="border-bottom: 1px solid #FFF; background:#E5EEF5;">';
			}else{
				$kodedana = $this->dataset->get(14);
				$tr =($kodedana=='04') ? '<tr style="border-bottom: 1px solid #D7D7D7; background:#F4F99B;">' : '<tr style="border-bottom: 1px solid #FFF; background:#E5EEF5;">';
			}
			
			echo $tr;
			if($this->showCBX){
				if(is_array($this->colRef)){
					$cbxVal = "";
					for ($i=0;$i<count($this->colRef);$i++) {
						$data = $this->colRef[$i];
						$nilai = $this->dataset->get($data);
						$cbxVal .= $nilai.";";
					}
					$cbxVal = substr($cbxVal,0,strlen($cbxVal) - 1);				
				}else{
					$cbxVal = $this->dataset->get($this->field[$this->colRef]->name);
				}
				if ($cbxVal == ""){
					$cbxVal = "&nbsp;";
				}
	$onclick ="";
	if($this->ajaxMod1>-1){  
		$onclick = "onclick=\"javascript:showText(this,'".$loop."')\"";
	}elseif($this->ajaxMod3>-1){
		$onclick = "onclick=\"javascript:showText2(this,'".$loop."')\"";	
	}elseif($this->ajaxMod5>-1){
		$onclick = "onclick=\"javascript:showText3(this,'".$loop."')\"";	
	}elseif($this->ajaxMod8>-1){
		$onclick = "onclick=\"javascript:showText4(this,'".$loop."')\"";	
	}elseif($this->ajaxMod11>-1){
		$onclick = "onclick=\"javascript:showText5(this,'".$loop."')\"";	
	}elseif($this->ajaxMod15>-1){
		$onclick = "onclick=\"javascript:showText6(this,'".$loop."')\"";	
	}
	
	$onclick .= " class='cbxBiasa'";
	if($this->cbxMod1==true){
		echo "<td style=\"border-bottom: 1px solid #D7D7D7;\" width=\"20\" align=\"center\" nowrap><input type=\"checkbox\" name=\"cbx[".$loop."]\" id='cbx".$loop."' value=\"$cbxVal\" ".$onclick."></td>";
	}else{
		echo "<td style=\"border-bottom: 1px solid #D7D7D7;\" width=\"20\" align=\"center\" nowrap><input type=\"checkbox\" name=\"cbx[]\" id='cbx".$loop."' value=\"$cbxVal\" ".$onclick."></td>";
	}
	
			}else{
				if($this->showRD){
					if(is_array($this->colRefRD)){
						$rdVal = "";
						for ($i=0;$i<count($this->colRefRD);$i++) {
							$data = $this->colRefRD[$i];
							$nilai = $this->dataset->get($this->field[$data]->name);
							$rdVal .= $nilai.";";
						}
						$rdVal = substr($rdVal,0,strlen($rdVal) - 1);				
					}else{
						$rdVal = $this->dataset->get($this->field[$this->colRefRD]->name);
					}				
					if ($rdVal == ""){
						$rdVal = "&nbsp;";
					}
					echo "<td style=\"border-bottom: 1px solid #D7D7D7;\" width=\"20\" nowrap><input type=\"radio\" name=\"radiopanel\" value=\"$rdVal\"></td>";				
				}
			}
			
			for ($i = 0; $i <= $count_column-1; $i++) {
				$data = $this->dataset->get($i);
				if ($data == ""){
					$data = "&nbsp;";
				}
				
				//check dulu apakah hidden bernilai true.
				if ($this->field[$i]->hidden){
					echo "<input type=\"hidden\" name=\"".$this->field[$i]->name."\" value=\"$data\">";
				}
				else{
					$wrap = "<td";
					if (!$this->field[$i]->wrap){
						//$wrap .=" nowrap";
						$wrap .=" ";
					} 
					$wrap .= " style=\"border-bottom: 1px solid #D7D7D7;padding-right:20px;\" >";
					echo $wrap;
					$align = $this->field[$i]->align;
					echo "<div align=\"".$align."\" class=div_tbl style=\"font-size:11px; font-family:Arial; padding:6px 0px 6px 9px;\">";
					
					if ($this->field[$i]->linker != ""){
						$matches = $this->parseLink($this->field[$i]->linker);
						$count_match = count($matches);
						$mylink = $this->field[$i]->linker;
						for ($loop = 0; $loop <= $count_match-1; $loop++) {
							$datalink = $matches[$loop][0];
							$replace = substr($datalink,1);
							$mylink = str_replace($datalink, $this->dataset->get($replace),$this->field[$i]->linker);
						}
						if($this->field[$i]->target == "blank"){
							$wintarget = "_blank";
						}
						if($this->field[$i]->target == "self"){
							$wintarget = "_self";
						}						
						echo "<a href=\"$mylink\" target=\"$wintarget\">$data</a>";
					}
					else{
						if($this->ajaxMod1 == $i){
							$nilaiDHE = (str_replace(',','',$this->dataset->get(17))>0)? str_replace(',','',$this->dataset->get(17)):0;
							$kurs_peb = ($this->dataset->get(21)>0)? $this->dataset->get(21):0;
							$kurs_dhe = ($this->dataset->get(16)>0)? $this->dataset->get(16):0;
							$kurs = ($this->dataset->get(20)>0)? $this->dataset->get(20):0;
							echo "<input type='text' id='ajaxMod1Text$loop' kurs='".($kurs)."' kurs_peb='".($kurs_peb)."' kurs_dhe='".($kurs_dhe)."' nilaidhe='".($nilaiDHE)."' nilaitransfer='".($nilaiDHE)."' class='nominal' name='nominal[]' style='text-align:right' disabled onkeyup=\"javascript:numberFormatKoma(this,',','','')\" noid='".$loop."'>";
						}elseif($this->ajaxMod2 == $i){
							echo "<input type='text' id='ajaxMod2Text$loop' readonly name='sandiRTE[]' noid='".$loop."' onclick=\"javascript:showSandiKeterangan('ajaxMod2Text".$loop."')\" style='cursor:pointer' disabled>";
						}elseif($this->ajaxMod3 == $i){
							echo "<input type='text' id='ajaxMod3Text$loop' val='".$data."' nominalDiterima='".str_replace(',','',$this->dataset->get(3))."' name='danaEkspor[]' value='".$data."' disabled style='text-align:right' class='danaEkspor' no='".$loop."' onkeyup=\"javascript:numberFormatKoma(this,',','','')\">";
						}elseif($this->ajaxMod4 == $i){
							echo "<input type='text' id='ajaxMod4Text$loop' val='".$data."' name='danaNonEkspor[]' value='".$data."' disabled style='text-align:right' onkeyup=\"javascript:numberFormatKoma(this,',','','')\" readonly>";	
						}elseif($this->ajaxMod5 == $i){							
							if($data=='1'){
								echo "<input type='file' style='font-size:10px;' id='ajaxMod5Text$loop' name='uploadDok[".$loop."]' disabled>";	
							}
						}elseif($this->ajaxMod6 == $i && $this->dataset->get(14)=='1'){							
							echo "<input type='text' id='ajaxMod6Text$loop' name='sandiRTE[".$loop."]' val='".$data."' value='".$data."' onclick=\"javascript:showSandiKeterangan('ajaxMod6Text".$loop."')\" style='cursor:pointer;width:50px;' disabled>";
						}elseif($this->ajaxMod7 == $i){							
							if($this->dataset->get($this->ajaxMod5)=='1'){
								echo "<input type='hidden' value='".$data."' noid='".$loop."' class='kelengkapanDok' check=0 id='ajaxMod7Text$loop'>";
							}
							if($this->ajaxMod12==1 && $data=='1'){								
								echo "<a href='#' onclick=\"javascript:window.open('".base_url."view/".$this->dataset->get(16)."')\">Lihat</a>";	
							}else{
								echo $data;
							}
						}elseif($this->ajaxMod8 == $i){							
							echo "<input type='hidden' value='".$data."' noid='".$loop."' class='statusPeb' check=0 id='ajaxMod8Text$loop'>";							
							echo ($data=='1')?"Completed" :"Pending";
						}elseif($this->ajaxMod9 == $i){							
							if($this->dataset->get(14)=='1'){
								echo "<input type='checkbox' name='email[".$loop."]' value='1' noid='".$loop."' class='viaEmail' disabled id='ajaxMod9Text$loop' check=0 title='Centang Jika Upload Dokumen Lewat Email'>";													
							}
						}elseif($this->ajaxMod10 == $i){		
							echo "<input type='hidden' name='keterangan[]' noid='".$loop."' class='keterangan' check=0 id='ajaxMod10Text$loop' disabled>";				
							echo "<div id='link$loop' style='display:none'>";
							echo "<a href='javascript:popTextarea(\"$loop\",\"edit\",\"on\")' id='ajaxMod10Link$loop'>Edit</a>&nbsp;&nbsp;&nbsp;&nbsp;";
							echo "<a href='javascript:void(0)' onmouseover='javascript:popTextarea(\"$loop\",\"view\",\"on\")' 
									onmouseout='javascript:popTextarea(\"$loop\",\"view\",\"off\")'>Lihat</a></div><div  id='label$loop'>Edit &nbsp;&nbsp;&nbsp;&nbsp; Lihat</div>";
						}elseif($this->ajaxMod11 == $i){							
							echo "<input type='file' style='font-size:10px;' id='ajaxMod11Text$loop' name='uploadDok[]' disabled>";
						}elseif($this->ajaxMod15 == $i){							
							echo (trim($this->dataset->get($this->ajaxMod15))=="")? "<input type='text' maxlength='6' style='font-size:11px;width:50px;' id='ajaxMod15Text$loop' name='nopeb[]' disabled onkeyup=\"javascript:intOnly(this)\" val='".$data."' value='".$data."' class='nopeb'>":$data;
						}elseif($this->ajaxMod16 == $i){												
							echo (trim($this->dataset->get($this->ajaxMod15))=="")? 
								'<input name="tglpeb[]" type="text" id="ajaxMod16Text'.$loop.'" value="'.$data.'" val="'.$data.'" size="10" 
								  readonly disabled onClick="if(self.gfPop)gfPop.fPopCalendar(this)" style="width:60px;" class="tglpeb">':$data;		
						}elseif($this->ajaxMod17 == $i){															
							switch($data){
								case '1' : $str = "Single";break;
								case '2' : $str = "Multiple";break;
								default  : $str = "";
							}				
							echo $str;
						}elseif($this->ajaxMod18 == $i){															
							echo ($this->dataset->get($this->ajaxMod17)=="2")? "<input type='checkbox' value='1' noid='".$loop."' class='terakhir' disabled id='ajaxMod18Text$loop' check=0 title='Centang di akhir pemilihan multiple Dana Masuk' onclick=\"javascript:if($('#ajaxMod18Texts$loop').val()=='1'){ $('#ajaxMod18Texts$loop').val('0')}else{ $('#ajaxMod18Texts$loop').val('1')}\"><input type='hidden' name='terakhir[]' id='ajaxMod18Texts$loop' disabled>" : "<input type='hidden' name='terakhir[]' id='ajaxMod18Texts$loop' disabled>";
						}elseif($this->ajaxMod19 == $i){
							echo $data;							
							echo "<input type='hidden' value='".$this->dataset->get(20)."' id='ajaxMod19Text$loop'>";
						}elseif($i == $this->showDetail[0]){
							echo '<a href="javascript:void(0)" class="link tooltip" label="'.$this->dataset->get($this->showDetail[1]).'">'.$data.'</a>';
						}else{
							echo $data;
						}
					}
					echo "</div>";
					echo "</td>";
				}
			}
		}
		echo "</tr>";
	}

	/**
	 * Function to draw sum
	 */
	function drawFooter(){
	}
	
	/**
	 * Function to make parameter, so it will ensure you don't replace other parameter.
	 */
	function make_parameter($param,$value){
	  $php = $_SERVER['PHP_SELF'];
	  $data = $_SERVER['QUERY_STRING']; 	
	  
	  if ($data == ""){
	    $data = "?$param=$value";
	  }
	  else{
	    $data = "?$data";
			$pos = strpos($data, "$param=");
			if ($pos === false){
		  	$data .= "&$param=$value";
			}
			else{
		  	$potong = $param."=".$_REQUEST[$param];
		  	$data = str_replace($potong, "$param=$value", $data);
	    }
	  }  
	  return $php.$data;
	}
	
	/**
	 * Function	to open window;
	 */
  function sort_func(){
		echo "<script language=\"javascript\">\n";	
		echo "$(document).ready(function(){ $('input:checkbox').removeAttr('checked');});\n";	
		if($this->ajaxMod1>-1){
			echo "var checkAll=2;\n";
			echo "var habis=0;\n";
			echo "var clickAkhir=0;\n";
			echo "var nilaiAkhir=0;\n";
			echo "function showText(a,no){\n";			
			echo "	checked=0;\n";
			echo "	$('input:checkbox.cbxBiasa').each(function(){\n";
			echo "		if($(this).attr('checked')==true){ checked++;}\n";			
			echo "	})\n";			
			if($this->ajaxMod13==true){
				echo "nilaipembagian = money_format($('input#ajaxMod1Text'+no).attr('nilaitransfer')+'');\n";
				echo "	if($(a).attr('checked')==true){\n";
				echo "		$('input#ajaxMod1Text'+no).removeAttr('disabled');\n";
				echo "		$('input#ajaxMod2Text'+no).removeAttr('disabled');\n";		
				echo "		$('input#ajaxMod1Text'+no).val(nilaipembagian);\n";			
				echo "		$('input#ajaxMod2Text'+no).val('50000');\n";
				if($this->ajaxMod10>-1){
					echo "	$('#link'+no).css('display','inline');\n";
					echo "	$('#label'+no).css('display','none');\n";
					echo "	$('input#ajaxMod10Text'+no).removeAttr('disabled');\n";
				}
				echo "	}else{;\n";
				echo "		$('input#ajaxMod1Text'+no).val('');\n";
				echo "		$('input#ajaxMod1Text'+no).css('border','1px #9BA8B5 solid');\n";
				echo "		$('input#ajaxMod1Text'+no).attr('disabled','true');\n";		
				echo "		$('input#ajaxMod2Text'+no).val('');\n";
				echo "		$('input#ajaxMod2Text'+no).attr('disabled','true');\n";				

			}else{
				echo "	if(checkAll==0){ clickAkhir=0;}\n";				
				echo "	totalNominal=0;\n";
				echo "	$('input.nominal').each(function(){\n";
				echo "		if($(this).attr('disabled')==false){\n"; 				
				echo "			if(checked==1){\n";
				echo "				nilaiPenuh = ".($this->nominalDiterima).";\n";
				echo "				$(this).val(money_format(nilaiPenuh.toFixed(2)))\n";
				echo "				clickAkhir=0;\n";
				echo "				habis=0;\n";
				echo "			}else{\n";
				echo "				if(habis==0){\n";
				echo "					nilai = $(this).val().replace(',',''); nilai = nilai.replace(',','');nilai = nilai.replace(',','');nilai = nilai.replace(',','');\n";
				echo "					nilai = nilai.replace(',','');nilai = nilai.replace(',','');nilai = nilai.replace(',','');nilai = nilai.replace(',','');\n";
				echo "					nilai = eval(nilai)*$(this).attr('kurs');\n";
				echo "					nilaidhe = $(this).attr('nilaidhe');\n";				
				echo "					nilaidhe = eval(nilaidhe)/$(this).attr('kurs');\n";
				echo "					if(nilaidhe<=nilai){ $(this).val(money_format(nilaidhe.toFixed(2))); }\n";				
				echo "  			}\n";				
				echo "			}\n"; 
				echo "			nilai = $(this).val().replace(',',''); nilai = nilai.replace(',','');nilai = nilai.replace(',','');nilai = nilai.replace(',','');nilai = nilai.replace(',','');nilai = nilai.replace(',','');nilai = nilai.replace(',','');nilai = nilai.replace(',','');\n";				
				echo "			totalNominal += eval(nilai);\n";
				echo "		};\n";			
				echo "	})\n";						
				echo "	nilaiPenuh = ".($this->nominalDiterima).";\n";
				echo "	nilaiSisa = (".$this->nominalDiterima."-totalNominal);\n";
				echo "	nilaipembagian = (checked==1)? money_format(nilaiPenuh.toFixed(2)) : money_format(nilaiSisa.toFixed(2));\n";											
				echo "	nilaibagi = (checked==1)? nilaiPenuh.toFixed(2) : nilaiSisa.toFixed(2);\n";											
				echo "	nilaiSisaPembagian=0;\n";
				echo "	if(clickAkhir==no){ clickAkhir=0; habis=1; };\n";
				echo "	if($(a).attr('checked')==true){\n";
				echo "		$('input#ajaxMod1Text'+no).removeAttr('disabled');\n";
				echo "		$('input#ajaxMod2Text'+no).removeAttr('disabled');\n";						
				echo "  	if(nilaipembagian.indexOf('-')!=-1 || nilaipembagian=='0.00'){\n";
				echo "			nilaiSisaPembagian='1.00';\n";
				echo "			habis++;\n";
				echo "			nilaiAkhir--;\n";
				echo "			$('input#ajaxMod1Text'+clickAkhir).val(money_format(nilaiAkhir.toFixed(2)));\n";										
				echo " 	 	}else{\n";
				echo "			nilaiSisaPembagian=0;\n";
				echo " 			habis=(habis!=999999)?0:1;\n";
				echo "			clickAkhir = no;\n";
				echo "			nilaiAkhir = (checked==1)? nilaiPenuh.toFixed(2) : nilaiSisa.toFixed(2);\n";											
				echo " 	 	}\n";				
				echo "		$('input#ajaxMod1Text'+no).val((nilaiSisaPembagian==0 ? nilaipembagian : '0.00'));\n";	
				echo "		$('input#ajaxMod2Text'+no).val('0000');\n";		
				echo "		checkAll=2;\n";
				echo "		$('input#ajaxMod1Text'+no).val((nilaiSisaPembagian==0 ? nilaipembagian : nilaiSisaPembagian));\n";				
				if($this->ajaxMod10>-1){
					echo "	$('#link'+no).css('display','inline');\n";
					echo "	$('#label'+no).css('display','none');\n";
					echo "	$('input#ajaxMod10Text'+no).removeAttr('disabled');\n";
				}
				if($this->ajaxMod18>-1){
					echo "	$('input#ajaxMod18Text'+no).removeAttr('disabled');\n";
					echo "	$('input#ajaxMod18Texts'+no).removeAttr('disabled');\n";					
					echo "	$('input#ajaxMod18Text'+no).attr('check','1');\n";					
				}
				if($this->ajaxMod19>-1){
					echo "	nilaiPEB = $('input#ajaxMod1Text'+no).val(); nilaiPEB = nilaiPEB.replace(',',''); nilaiPEB = nilaiPEB.replace(',',''); nilaiPEB = nilaiPEB.replace(',',''); nilaiPEB = nilaiPEB.replace(',','');\n";
					echo "	kursPEB = $('input#ajaxMod1Text'+no).attr('kurs');\n";
					echo "	nilaiDHE = $('input#ajaxMod1Text'+no).attr('nilaidhe');\n";
					echo "	nilaiPEB = eval(kursPEB)*eval(nilaiDHE);\n";
					echo "	nilaiDHE = $('input#ajaxMod1Text'+no).attr('nilaidhe');\n";
                    echo "	CodeDana = $('input#cbx'+no).val(); CekCode = CodeDana.split(';');\n";
					#echo "	console.log('NILAI DHE :'+nilaiDHE+' NILAI PEB :'+nilaiPEB);\n";
					echo "	if(nilaiDHE<nilaiPEB && CekCode[6] == '0' ){\n";
					echo "		$('input#ajaxMod2Text'+no).val('').attr('placeholder','Isi Kode');\n";		
					echo "	}else if(CekCode[6] != '0' ){\n";
					echo "		$('input#ajaxMod2Text'+no).val('0240');\n";
                                        echo "		$('input#ajaxMod2Text'+no).attr('readonly','true');\n";		
	                                echo "	}else{\n";
					echo "		$('input#ajaxMod2Text'+no).val('0000');\n";					
					echo "	}";
					#echo "	console.log('NILAI DHE :'+nilaiDHE+' NILAI PEB :'+nilaiPEB);\n";
					//echo "	if(nilaiDHE<nilaiPEB){\n";
					//echo "		$('input#ajaxMod2Text'+no).val('').attr('placeholder','Isi Kode');\n";		
					//echo "	}else{\n";
					//echo "		$('input#ajaxMod2Text'+no).val('000xx0');\n";					
					//echo "	}";	
					//
				}
				
				echo "	}else{;\n";
				echo "		nilai = $('input#ajaxMod1Text'+no).val().replace(',',''); nilai = nilai.replace(',','');nilai = nilai.replace(',','');nilai = nilai.replace(',','');nilai = nilai.replace(',','');nilai = nilai.replace(',','');nilai = nilai.replace(',','');nilai = nilai.replace(',','');\n";
				echo "		$('input#ajaxMod1Text'+no).val('');\n";
				echo "		$('input#ajaxMod1Text'+no).css('border','1px #9BA8B5 solid');\n";
				echo "		$('input#ajaxMod1Text'+no).attr('disabled','true');\n";						
				#echo "	alert(nilai);\n";
				echo "		if(habis>0 && no!=clickAkhir && checked!=1){\n";				
				echo "			nilaiAkhir += eval(nilai);\n";
				#echo "			$('input#ajaxMod1Text'+clickAkhir).val(money_format(nilaiAkhir.toFixed(2)));\n";		
				echo "		}\n";
				echo "		$('input#ajaxMod2Text'+no).removeAttr('placeholder');\n";
				echo "		$('input#ajaxMod2Text'+no).val('');\n";
				echo "		$('input#ajaxMod2Text'+no).attr('disabled','true');\n";										
			}
			if($this->ajaxMod18>-1){
				echo "	$('input#ajaxMod18Text'+no).attr('disabled','disabled');\n";
				echo "	$('input#ajaxMod18Texts'+no).attr('disabled','disabled');\n";				
				echo "	$('input#ajaxMod18Text'+no).attr('check','0');\n";
				echo "	$('input#ajaxMod18Text'+no).removeAttr('checked');\n";
			}
			if($this->ajaxMod10>-1){
				echo "	$('#link'+no).css('display','none');\n";
				echo "	$('#label'+no).css('display','inline');\n";
				echo "	$('input#ajaxMod10Text'+no).attr('disabled','true');\n";		
			}
			echo "	}\n";
			echo "	totalNominal=0;\n";
			echo "	$('input.nominal').each(function(){\n";
			echo "		if($(this).attr('disabled')==false){\n"; 				
			echo "			nilai = $(this).val().replace(',',''); nilai = nilai.replace(',','');nilai = nilai.replace(',','');nilai = nilai.replace(',','');\n";
			echo "			nilai = nilai.replace(',','');nilai = nilai.replace(',','');nilai = nilai.replace(',','');nilai = nilai.replace(',','');\n";
			echo "			totalNominal += eval(nilai);\n";
			echo "		};\n";			
			echo "	})\n";	
			echo "	if(totalNominal!=nilaiPenuh && nilaibagi<1 && habis>0){ habis=999999;}\n";	
			#echo " console.log(totalNominal!=nilaiPenuh && nilaibagi<1 && habis>0);\n";			
			#echo " console.log('akhir :'+clickAkhir+' -- habis:'+habis+' -- hasil pembagian:'+nilaibagi+' -- total Nominal :'+totalNominal);\n";			
			echo "}\n";		
		}
		if($this->ajaxMod3>-1){
			echo "function showText2(a,no){\n";
			echo "	if($(a).attr('checked')==true){\n";
			echo "		$('input#ajaxMod3Text'+no).removeAttr('disabled');\n";
			echo "		$('input#ajaxMod4Text'+no).removeAttr('disabled');\n";		
			echo "		$('input#ajaxMod3Text'+no).addClass('ekspor');\n";
			echo "		$('input#ajaxMod4Text'+no).addClass('nonekspor');\n";		
			echo "	}else{;\n";
			echo "		$('input#ajaxMod3Text'+no).val($('input#ajaxMod3Text'+no).attr('val'));\n";		
			echo "		$('input#ajaxMod4Text'+no).val($('input#ajaxMod4Text'+no).attr('val'));\n";		
			echo "		$('input#ajaxMod3Text'+no).attr('disabled','true');\n";		
			echo "		$('input#ajaxMod4Text'+no).attr('disabled','true');\n";	
			echo "		$('input#ajaxMod3Text'+no).css('border','1px #9BA8B5 solid');\n";		
			echo "		$('input#ajaxMod4Text'+no).css('border','1px #9BA8B5 solid');\n";	
			echo "		$('input#ajaxMod3Text'+no).removeClass('ekspor');\n";
			echo "		$('input#ajaxMod4Text'+no).removeClass('nonekspor');\n";					
			echo "	}\n";		
			echo "}\n";					
		}
		if($this->ajaxMod5>-1){
			echo "function showText3(a,no){\n";
			echo "	if($(a).attr('checked')==true){\n";
			echo "		$('input#ajaxMod5Text'+no).removeAttr('disabled');\n";
			echo "		$('input#ajaxMod6Text'+no).removeAttr('disabled');\n";
			echo "		$('input#ajaxMod7Text'+no).attr('check','1');\n";			
			echo "		$('input#ajaxMod9Text'+no).attr('check','1');\n";						
			echo "	}else{;\n";
			echo "		$('input#ajaxMod5Text'+no).val('');\n";
			echo "		$('input#ajaxMod5Text'+no).attr('disabled','true');\n";	
			echo "		$('input#ajaxMod5Text'+no).css('border','none');\n";	
			echo "		$('input#ajaxMod6Text'+no).val($('input#ajaxMod6Text'+no).attr('val'));\n";
			echo "		$('input#ajaxMod6Text'+no).attr('disabled','true');\n";	
			echo "		$('input#ajaxMod7Text'+no).attr('check','0');\n";				
			echo "		$('input#ajaxMod9Text'+no).attr('check','0');\n";												
			echo "	}\n";		
			echo "}\n";		
		}	
		if($this->ajaxMod8>-1){
			echo "function showText4(a,no){\n";
			echo "	if($(a).attr('checked')==true){\n";
			echo "		$('input#ajaxMod8Text'+no).removeAttr('disabled');\n";
			echo "		$('input#ajaxMod8Text'+no).attr('check','1');\n";			
			echo "	}else{;\n";
			echo "		$('input#ajaxMod8Text'+no).attr('disabled','true');\n";	
			echo "		$('input#ajaxMod8Text'+no).attr('check','0');\n";				
			echo "	}\n";		
			echo "}\n";		
		}	
		if($this->ajaxMod11>-1){
			echo "function showText5(a,no){\n";
			echo "	$('input#ajaxMod11Text'+no).css('border','none');\n";
			echo "	if($(a).attr('checked')==true){\n";
			echo "		$('input#ajaxMod11Text'+no).removeAttr('disabled');\n";
			echo "		$('input#ajaxMod14Text'+no).attr('check','1');\n";
			echo "	}else{;\n";
			echo "		$('input#ajaxMod11Text'+no).attr('disabled','true');\n";	
			echo "		$('input#ajaxMod14Text'+no).attr('check','0');\n";
			echo "	}\n";		
			echo "}\n";		
		}	
		if($this->ajaxMod15>-1){
			echo "function showText6(a,no){\n";		
			echo "	if($(a).attr('checked')==true){\n";
			echo "		$('input#ajaxMod15Text'+no).removeAttr('disabled');\n";
			echo "		$('input#ajaxMod16Text'+no).removeAttr('disabled');\n";			
			echo "	}else{;\n";
			echo "		$('input#ajaxMod15Text'+no).attr('disabled','true');\n";	
			echo "		$('input#ajaxMod16Text'+no).attr('disabled','true');\n";	
			echo "		$('input#ajaxMod15Text'+no).val($('input#ajaxMod15Text'+no).attr('val'));\n";	
			echo "		$('input#ajaxMod16Text'+no).val($('input#ajaxMod16Text'+no).attr('val'));\n";	
			echo "	}\n";		
			echo "}\n";		
		}		
		echo "  function OpenWindow(parameter){\n";
		echo "    win = window.open(parameter,\"_self\");\n";
		echo "  }\n";
		echo "  function addWindow(parameter,isblank){\n";
		echo "    if(isblank == \"true\"){\n";
		echo "      win = window.open(parameter,\"_blank\");\n";
		echo "		}else{\n";
		echo "      win = window.open(parameter,\"_self\");\n";
		echo "    }\n";
		echo "  }\n";		
		echo "	var checkflag = \"false\"\n";
		echo "	function check(a) {\n";
		echo "	 no=1;\n";
		echo "		if($(a).attr('checked')==true){\n";
		echo "			$('input.cbxBiasa').each(function(){\n";
		echo "				$(this).attr('checked','true');\n";	
		if($this->ajaxMod1>-1){
			echo "				showText(this,no);\n";
		}elseif($this->ajaxMod3>-1){
			echo "				showText2(this,no);\n";
		}elseif($this->ajaxMod5>-1){
			echo "				showText3(this,no);\n";
		}elseif($this->ajaxMod8>-1){
			echo "				showText4(this,no);\n";
		}elseif($this->ajaxMod11>-1){
			echo "				showText5(this,no);\n";
		}elseif($this->ajaxMod15>-1){
			echo "				showText6(this,no);\n";
		}
		echo "	 			no++;\n";
		echo "				checkAll=1;\n";
		echo "			});\n";
		echo "		}else{\n";
		echo "			$('input.cbxBiasa').each(function(){\n";
		echo "				$(this).removeAttr('checked');\n";
		if($this->ajaxMod1>-1){
			echo "				showText(this,no);\n";
		}elseif($this->ajaxMod3>-1){
			echo "				showText2(this,no);\n";
		}elseif($this->ajaxMod5>-1){
			echo "				showText3(this,no);\n";
		}elseif($this->ajaxMod8>-1){
			echo "				showText4(this,no);\n";
		}elseif($this->ajaxMod11>-1){
			echo "				showText5(this,no);\n";
		}elseif($this->ajaxMod15>-1){
			echo "				showText6(this,no);\n";
		}
		echo "				checkAll=0;\n";
		echo "	 			no++;\n";
		echo "			});\n";
		echo "		}\n";
		echo "	}\n";
		$arrurl = explode("modul/",$_SERVER['REQUEST_URI']); 
		$current_url = "modul/".$arrurl[1];
		if(strstr($_SERVER['REQUEST_URI'],'FormSearchSSP.php') || strstr($_SERVER['REQUEST_URI'],'FormSearchSandiRTE.php') || strstr($_SERVER['REQUEST_URI'],'FormSearchReception.php')){
			echo "	function gopage(parameter)\n";
			echo "	{\n";
			echo "		for (i = 0; i < document.formtable.elements.length; i++){\n";
			echo "			if (document.formtable.elements[i].type == \"text\"){\n";
			echo "				var data = document.formtable.elements[i].name.substring(0,7);\n";
			echo "				if (data == \"txtpage\"){\n";
			echo "					parameter += '&' + document.formtable.elements[i].name + '=' + document.formtable.elements[i].value;\n";
			echo "				}\n";
			echo "			}\n";
			echo "		}\n";		
			echo "    win = window.open(parameter,\"_self\");\n";
			echo "	}\n";
		
		}else{
			echo "	function gopage()\n";
			echo "	{\n";
			echo "		hal = eval($('#textpage').val());\n";
			echo "		hal = (Number (hal))? hal : 1;\n";
			echo "		hal = (hal==0)?1:hal;\n
						urls = '".$current_url."';\n		
	
						str = urls.split('/');\n
						url = (str.length>=6)? '".base_url."'+str[0]+'/'+str[1]+'/'+str[2]+'/'+str[3]+'/'+str[4]+'/'+hal : '".base_url."'+str[0]+'/'+str[1]+'/'+str[2]+'/'+hal;\n					
						document.location = url;\n";
			echo "	}\n";
		}	
		
		echo "  function delButtonClick(){\n";
		echo "		document.formtable.action = \"$this->delcmd\";\n";
		echo "		document.formtable.method = \"post\";\n";
		echo "		document.formtable.submit();\n";		
		echo "	}\n";		
		
		echo " 	function RDPanelClick(a){\n";
		echo "  if(document.formtable.radiopanel != null){\n";
		echo "    radio = document.formtable.radiopanel;valid=0; tot =(!radio.length)? 1 : radio.length;\n";
		echo " 	  for(i=0;i<tot;i++){if(!radio.length){if(radio.checked==true)valid++;}else{if(radio[i].checked==true)valid++;}}\n";
		echo " 	  if(valid==0){ jAlert('Data belum dipilih!'); return false;}\n";
		echo "  }else{\n ";
		echo "    valid =0;\n";
		echo "    $('input:checkbox.cbxBiasa').each(function(){\n
					if($(this).attr('checked')==true) valid++;\n
				  })\n";
		echo " 	  if(valid==0){ jAlert('Data belum dipilih!'); return false;}\n";
		echo "  }\n";
		echo " 	var url = document.formtable.RDSelect.value;\n";
		echo " 	var konfirm;\n";		
		echo "	if(url == '".base_url."pilihan.php?cetak=terima') {\n";
		echo "		var wind = window.open('','pilihan','width=450,height=280,top=150,left=150,toolbar=no,minimize=no,status=no,memubar=no,location=no,scrollbars=yes','1');\n";
		echo "		document.formtable.action = '".base_url."pilihan.php?cetak=terima';\n";
		echo "		document.formtable.method = \"post\";\n";
		echo "		document.formtable.target = \"pilihan\";\n";
		echo "		document.formtable.submit();\n";
		echo "		document.formtable.target = \"\";\n";		
		echo "	} else if(url == '".base_url."pilihan.php?cetak=yes') {\n";
		echo "		var wind = window.open('','pilihan','width=450,height=280,top=150,left=150,toolbar=no,minimize=no,status=no,memubar=no,location=no,scrollbars=yes','1');\n";
		echo "		document.formtable.action = '".base_url."pilihan.php?cetak=yes';\n";
		echo "		document.formtable.method = \"post\";\n";
		echo "		document.formtable.target = \"pilihan\";\n";
		echo "		document.formtable.submit();\n";
		echo "		document.formtable.target = \"\";\n";		
		echo " 	} else if(url == '".base_url."cetak_pib.php?dok=pib'){\n";
		echo "		document.formtable.action = document.formtable.RDSelect.value;\n";
		echo " 		document.formtable.method = \"post\";\n";
		echo " 		document.formtable.target = \"_blank\";\n";
		echo "		document.formtable.submit();\n";				
		echo "	} else if(url == '".base_url."cetak_pib.php?dok=pdf'){\n";
		echo "		document.formtable.action = document.formtable.RDSelect.value;\n";
		echo " 		document.formtable.method = \"post\";\n";
		echo " 		document.formtable.target = \"_blank\";\n";
		echo "		document.formtable.submit();\n";
		echo "	} else if(url == '".base_url."modul/ssp/edit' && valid>1){\n";
		echo "		jAlert('Maaf proses tidak dapat dilakukan, silakan pilih salah satu!'); for(i=1;i<=total.value;i++){ document.getElementById('cbx'+i).checked=false} document.formtable.cbxAll.checked=false";
		echo "	 }else if(url == '".base_url."modul/ssp/send' ){\n";
		echo "      $(a).attr('disabled','disabled')\n";
		echo "		document.formtable.action = document.formtable.RDSelect.value;\n";
		echo "		document.formtable.method = \"post\";\n";
		echo "		document.formtable.submit();\n";
		echo "	 }else if(url == '".base_url."modul/danamasuk/update' ){\n";		
		if($this->ajaxMod3>-1){
			echo "	$('input.nonekspor').each(function(){\n";
			echo "		error=0;\n";			
			echo "		if($(this).attr('disabled')==false){\n";
			echo "			nilai = $(this).val().replace(',','');nilai = nilai.replace(',',''); nilai = nilai.replace(',','');nilai = nilai.replace(',','');\n";
			echo "			if(eval(nilai)<1){;\n";			
			echo "				error++;\n";		
			echo "				$(this).css('border','1px #F00 solid');\n";							
			echo "			}else{\n";
			echo "				$(this).css('border','1px #9BA8B5 solid');\n";											
			echo "			}\n";
			echo "		}\n";					
			echo "	})\n";	
			echo "	$('input.ekspor').each(function(){\n";
			echo "		if($(this).attr('disabled')==false){\n";
			echo "			nilai = $(this).val().replace(',','');nilai = nilai.replace(',',''); nilai = nilai.replace(',','');nilai = nilai.replace(',','');\n";
			echo "			if(eval(nilai)<1){;\n";			
			echo "				error++;\n";		
			echo "				$(this).css('border','1px #F00 solid');\n";							
			echo "			}else{\n";
			echo "				$(this).css('border','1px #9BA8B5 solid');\n";											
			echo "			}\n";
			echo "		}\n";					
			echo "	})\n";	
			echo "	if(error>0){ jAlert('Nilai Ekspor atau Non Ekspor tidak boleh bernilai 0 atau kurang dari 0!<br>Silakan Diperbaiki'); return false;}\n";
		}
		echo "		document.formtable.action = document.formtable.RDSelect.value;\n";
		echo "		document.formtable.method = \"post\";\n";
		echo "		document.formtable.submit();\n";
		echo "	 }else if(url == '".base_url."modul/danamasuk/pilihpeb' || url == '".base_url."modul/danamasuk/pilihpebcampuran' ){\n";
		echo "		if(valid>1){jAlert('Maaf proses tidak dapat dilakukan, silakan pilih salah satu!'); return false;}\n";		
		echo "     	$('input:checkbox.cbxBiasa').each(function(){ if($(this).attr('checked')==true){ id=$(this).val();} });\n";
		if($this->ajaxMod3>-1){
			echo "		$('input.ekspor').val($('input.ekspor').attr('val'));$('input.nonekspor').val($('input.nonekspor').attr('val'));\n";
			echo "		if($('input.ekspor').attr('val')<1 || $('input.nonekspor').attr('val')<1){jAlert('Sebelum melakukan pemilihan data PEB<br>Nilai Ekspor atau Non Ekspor tidak boleh bernilai 0!'); return false; }\n";	
		}
		echo "		document.formtable.action = document.formtable.RDSelect.value+'/'+id+'/'+calcMD5(id);\n";
		echo "		document.formtable.method = \"post\";\n";
		echo "		document.formtable.submit();\n";		
		echo "	 }else if(url == '".base_url."modul/danamasuk/eksporrte' ){\n";	
		echo "		total =0;err=0;\n";
		echo "		$('input.nominal').each(function(){\n";
		echo "			if($(this).attr('disabled')==false){\n";
		echo "				nilai = $(this).val().replace(',','');\n";
		echo "				nilai = nilai.replace(',','');\n";
		echo "				nilai = nilai.replace(',','');\n";
		echo "				nilai = nilai.replace(',','');\n";
		echo "				nilai = nilai.replace(',','');\n";		
		echo "				total += eval(nilai);\n";
		echo "				if(eval(nilai)<=0){\n";
		echo "					err++;\n";
		echo "					$(this).css('border','1px #F00 solid');\n";
		echo "				}else{\n";
		echo "					$(this).css('border','1px #9BA8B5 solid');\n";	
		echo "				}\n";
		echo "			}\n";
		echo "		});\n";				
		echo "		if(err>0){\n";
		echo "			jAlert('Nilai DHE tidak boleh bernilai 0 atau minus'); return false;\n";
		echo "		}\n";
		echo "		if(total>".$this->nominalDiterima."){\n";
		echo "			jAlert('Nilai DHE tidak boleh melebihi Nominal yang diizinkan!<br> Total nilai DHE : '+money_format(''+total+'')+' , sementara <br>Dana yang diizinkan sebesar : ".number_format($this->nominalDiterima,0,'',',')."'); return false;\n";
		echo "		}\n";
		echo "		document.formtable.action = document.formtable.RDSelect.value;\n";
		echo "		document.formtable.method = \"post\";\n";
		echo "		document.formtable.submit();\n";#
		echo "	 }else if(url == '".base_url."modul/peb/rtedanamasuk' ){\n";	
		echo "		if($('#statusPeb').val()==''){ jAlert('Status PEB Belum dipilih'); return false;}\n";
		echo "		total =0;err=0;err1=0\n";
		echo "		$('input.nominal').each(function(){\n";
		echo "			if($(this).attr('disabled')==false){\n";
		echo "				nilai = $(this).val().replace(',','');\n";
		echo "				nilai = nilai.replace(',','');\n";
		echo "				nilai = nilai.replace(',','');\n";
		echo "				nilai = nilai.replace(',','');\n";
		echo "				nilai = nilai.replace(',','');\n";		
		echo "				total += eval(nilai);\n";
		echo "				if(eval(nilai)<=0){\n";
		echo "					err++;\n";
		echo "					$(this).css('border','1px #F00 solid');\n";
		echo "				}else{\n";
		echo "					$(this).css('border','1px #9BA8B5 solid');\n";	
		echo "				}\n";
		echo "				sandiRTEs=$('input#ajaxMod2Text'+$(this).attr('noid'));";
		echo "				if(sandiRTEs.val()==''){\n";
		echo "					err1++;\n";
		echo "					sandiRTEs.css('border','1px #F00 solid');\n";
		echo "				}else{\n";
		echo "					sandiRTEs.css('border','1px #9BA8B5 solid');\n";	
		echo "				}\n";
		echo "			}\n";
		echo "		});\n";				
		echo "		if(err>0){\n";
		echo "			jAlert('Nilai DHE tidak boleh bernilai 0 atau minus'); return false;\n";
		echo "		}\n";
		echo "		if(err1>0){\n";
		echo "			jAlert('Sandi Keterangan harus diisi!'); return false;\n";
		echo "		}\n";		
		echo "		document.formtable.action = document.formtable.RDSelect.value;\n";
		echo "		document.formtable.method = \"post\";\n";
		echo "		document.formtable.submit();\n";
		echo "	 }else if(url == '".base_url."modul/peb/pilihdanamasukpending'){\n";	
		echo "		if(valid>1){jAlert('Maaf proses tidak dapat dilakukan, silakan pilih salah satu!'); return false;}\n";		
		echo "		err=0;\n";
		echo "		if($('input.viaEmail').size()>0){\n";
		echo "			$('input.viaEmail').each(function(){\n";
		echo "				if($(this).attr('check')=='1' && $(this).val()=='0'){\n";
		echo "					err++;\n";
		echo "					$('#ajaxMod11Text'+$(this).attr('noid')).css('border','1px #F00 solid');\n";
		echo "				}else{\n";
		echo "					$('#ajaxMod11Text'+$(this).attr('noid')).css('border','none');\n";
		echo "				}\n";
		echo "			});\n";		
		echo "			if(err>0){\n";
		echo "				jAlert('Pastikan dokumen sudah lengkap atau dikirim via email!'); return false;\n";
		echo "			}\n";
		echo "		}\n";		
		echo "		$('input.statusPeb').each(function(){\n;";
		echo "			if($(this).attr('check')==1){\n";
		echo "				if($(this).val()!='2'){\n";
		echo "					err++;\n";						
		echo "				}\n";	
		echo "			}\n";
		echo "		})\n";
		echo "		if(err>0){\n";
		echo "			jAlert('Hanya diperbolehkan untuk PEB yang berstatus Pending'); return false;\n";
		echo "		}\n";		
		echo "     	$('input:checkbox.cbxBiasa').each(function(){ if($(this).attr('checked')==true){ id=$(this).val();} });\n";
		echo "		document.formtable.action = document.formtable.RDSelect.value+'/'+id+'/'+calcMD5(id);\n";
		echo "		document.formtable.method = \"post\";\n";
		echo "		document.formtable.submit();\n";#
		echo "	 }else if(url == '".base_url."modul/peb/pilihdanamasuk' || url == '".base_url."modul/peb/pilihtanpadanamasuk'  ){\n";
		echo "		if(valid>1){jAlert('Maaf proses tidak dapat dilakukan, silakan pilih salah satu!'); return false;}\n";		
		echo "		if($('input.viaEmail').size()>0){\n";
		echo "			err=0;\n";
		echo "			$('input.viaEmail').each(function(){\n";
		echo "				if($(this).attr('check')=='1' && $(this).val()=='0'){\n";
		echo "					err++;\n";
		echo "					$('#ajaxMod11Text'+$(this).attr('noid')).css('border','1px #F00 solid');\n";
		echo "				}else{\n";
		echo "					$('#ajaxMod11Text'+$(this).attr('noid')).css('border','none');\n";
		echo "				}\n";
		echo "			});\n";		
		echo "			if(err>0){\n";
		echo "				jAlert('Pastikan dokumen sudah lengkap atau dikirim via email!'); return false;\n";
		echo "			}\n";
		echo "		}\n";	
		echo "     	$('input:checkbox.cbxBiasa').each(function(){ if($(this).attr('checked')==true){ id=$(this).val();} });\n";		
		echo "		document.formtable.action = document.formtable.RDSelect.value+'/'+id+'/'+calcMD5(id);\n";
		echo "		document.formtable.method = \"post\";\n";
		echo "		document.formtable.submit();\n";
		echo "	 }else if(url == '".base_url."modul/rte/tosend' ){\n";
		echo "		err=0;\n";
		echo "		no=1;\n";
		echo "		$('input.kelengkapanDok').each(function(){\n";				
		echo "			if($(this).attr('check')=='1'){\n";
		echo "				if($(this).val()==0 && $('#ajaxMod9Text'+no).val()==0){\n";	
		echo "					err++;\n";	
		echo "					$('#ajaxMod5Text'+$(this).attr('noid')).css('border','1px #F00 solid');\n";
		echo "				}else{\n";	
		echo "					$('#ajaxMod5Text'+$(this).attr('noid')).css('border','none');\n";
		echo "				}\n";		
		echo "			}\n";	
		echo "			no++;\n";		
		echo "		});\n";				
		echo "		if(err>0){ jAlert('Silakan Lengkapi terlebih dahulu dokumen yang akan dikirim ke Bank!'); return false;}else{\n";				
		echo "      	$(a).attr('disabled','disabled')\n";
		echo "			document.formtable.action = document.formtable.RDSelect.value;\n";
		echo "			document.formtable.method = \"post\";\n";
		echo "			document.formtable.submit();\n";
		echo "		}\n";
		echo "	 }else if(url == '".base_url."modul/rte/viaemail' ){\n";
		echo "		err=0;\n";
		echo "		$('input.viaEmail').each(function(){\n";				
		echo "			if($(this).attr('check')=='1' && $(this).val()=='2'){\n";		
		echo "				err++;\n";	
		echo "			}\n";	
		echo "		});\n";		
		echo "		if(err>0){ jAlert('Hanya diperbolehkan untuk RTE yang membutuhkan kelengkapan dokumen!'); return false;}else{\n";				
		echo "      	$(a).attr('disabled','disabled')\n";
		echo "			document.formtable.action = document.formtable.RDSelect.value;\n";
		echo "			document.formtable.method = \"post\";\n";
		echo "			document.formtable.submit();\n";
		echo "		}\n";
		echo "	 }else if(url == '".base_url."modul/rte/viaemail' ){\n";
		echo "		err=0;\n";
		echo "		$('input.viaEmail').each(function(){\n";				
		echo "			if($(this).attr('check')=='1' && $(this).val()=='2'){\n";		
		echo "				err++;\n";	
		echo "			}\n";	
		echo "		});\n";		
		echo "		if(err>0){ jAlert('Hanya diperbolehkan untuk RTE yang membutuhkan kelengkapan dokumen!'); return false;}else{\n";				
		echo "      	$(a).attr('disabled','disabled')\n";
		echo "			document.formtable.action = document.formtable.RDSelect.value;\n";
		echo "			document.formtable.method = \"post\";\n";
		echo "			document.formtable.submit();\n";
		echo "		}\n";
		echo "	 }else if(url == '".base_url."modul/peb/delete' ){\n";
		echo "		jConfirm('Apakah anda yakin untuk menghapus sejumlah '+valid+' Dokumen PEB?', 'Confirmation Dialog', function(r) {\n";
		echo "			if(r == true){\n";
		echo "				document.formtable.action = document.formtable.RDSelect.value;\n";
		echo "				document.formtable.method = \"post\";\n";
		echo "				document.formtable.submit();\n";
		echo "			};\n";
		echo "		});\n";		
		echo "	}else if(url == '".base_url."modul/danamasuk/tononekspor' ){\n";
		echo "		jSelect('Sandi keterangan ','', 'Confirmation Dialog', function(r) {\n";
		echo "			if(r != null){\n";
		echo "		 	var hidden = document.createElement(\"input\"); \n";
		echo "				hidden.type = \"hidden\"; \n";
		echo "				hidden.name = \"code\"; \n";
		echo "				hidden.value = r; \n";
		echo "				document.formtable.appendChild(hidden); \n";
	//	echo "				document.formtable.innerHTML = '<input type=\"hidden\" name=\"code\" value=\"'+r+'\" />';\n";
		echo "				document.formtable.action = document.formtable.RDSelect.value;\n";
		echo "				document.formtable.method = \"post\";\n";
		echo "				document.formtable.submit();\n";
		echo "			};\n";
		echo "		});\n";		
		echo "	}else {\n";
		echo "		document.formtable.action = document.formtable.RDSelect.value;\n";
		echo "		document.formtable.method = \"post\";\n";
		echo "		document.formtable.submit();\n";
		echo "	}\n";
		echo "}\n";
		$page = ($page = $_GET['page'])==""? 1 : $page;
		$id = ($id = $_GET['id']) ==""? "" : "/".$id."/".$_GET['seccode'];
		echo "function descasc(name){
				url = '".base_url."modul/".$_GET['mod']."/".$_GET['div']."/".$page."/';				
				if($('#descasc'+name).val()=='asc'){					
					document.location =url+name+'/desc".$id."';
				}else{				
					document.location = url+name+'/asc".$id."';
				}
			  }";
		echo "function descascx(name, url, order){
					document.location = url+'&sort='+name+'&order='+order;
			  }\n";	
		$orderby = ($sort = $_GET['sort'])==""? "" : "/".strtolower($sort)."/".$_GET['order'];	 
		echo 	"function goto(hal){					
					hal = (hal==0)?1:hal;
					url = '".base_url."modul/".$_GET['mod']."/".$_GET['div']."/'+hal+'".$orderby.$id."';
					document.location = url;
				}\n";
		echo "</script>\n";	
		
	}
	
	/**
	 * Function to delete rows
	 */
	function deleteRow(){
		$cbx = $_REQUEST['cbx'];
		if (count($cbx) > 0) {
			$colname = $this->field[$this->colRef]->name;
			$i=0;
			foreach ($cbx as $d) {
				//print_r($cbx[$i]);
				//echo $colname;
				$SQL = $command." WHERE $colname = '".$d."'";
				$result[$i] = $this->connection->execute($SQL);
				$i++;
			}
		}	
	}
		
	/**
	 * Function to get count of column that get in Query, so you can set the match cell. 
	 */
	function getColumnCount(){
	  return $this->dataset->columnSize();
	}
	
	/**
	 * Function to get current name of header.
	 */ 
	function parseDefHeader(){
	  $count_column = $this->getColumnCount();
	  for ($i = 0; $i <= $count_column-1; $i++) {
	    if ($this->field[$i]->headername == ""){
		  $this->field[$i]->headername = $this->dataset->fieldName($i);
		}
		$this->field[$i]->headername = str_replace("_", " ",$this->field[$i]->headername);
	  }
	}
		
	/**
	 * Function to completed the link url, so it can fit by regex.
	 */
	function parseLink($link){
	  preg_match_all("/\%\d+/", $link, $matches,PREG_SET_ORDER);
	  return $matches;
	}
			
	/**
	 * Function to check is there any parent in header.
	 */
	function anyParent(){
	  $count_field = count($this->field);
	  for ($i = 0; $i <= $count_field - 1; $i++) {
	    if ($this->field[$i]->isParent){
		  	return true;
			}
	  }
	  return $false; 	
	}
	
	/**
	 * Function to count the child that header have.
	 */
	function countChild($name){
	  $count_field = count($this->field);
	  $count = 0;
	  for ($i = 0; $i <= $count_field - 1; $i++) {
	    if ($this->field[$i]->parentName == $name){
		  	$count++;
			}
	  }
	  return $count;		
	}
	
	/**
	 * Set Query for collecting data.
	 */
	function execQuery(){
	  $this->connection->connect();
	  $tambahan = $this->applySort();
	  if ($tambahan != ""){
	  	$this->SQL .= $tambahan;
	  }	  
	  if(isset( $_GET['sort'] )){
		  $addorder = ' Order By '.$_GET['sort'].' '.$_GET['order'];
 	  }	  
	  $this->dataset = $this->connection->query($this->SQL.$addorder);
	}
	
	/**
	 * Apply sortable fields.
	 */
	function applySort(){
	  $strQuery = "";
	  $count_field = count($this->field);
	  for ($i = 0; $i <= $count_field - 1; $i++) {
	    if (($this->field[$i]->sortable) & ($this->field[$i]->name != "")){
				if ($strQuery == ""){
					$strQuery .= " ORDER BY ";
				}
				if ($this->field[$i]->sortby){
					$strQuery .= $this->field[$i]->name . " ASC,";
				}
				else{
					$strQuery .= $this->field[$i]->name . " DESC,";
				}
			}
	  }
	  $strQuery = substr($strQuery, 0, strlen($strQuery)-1); 	 
	  return $strQuery; 
	}		
}
?> 