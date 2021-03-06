<?php
/*
- perlu digabungkan dengan fungsi add, delete button
- perlu usaha untuk mengambil nilai checkbox.
*/
/*******************************************************************************
* Class   : PageNav				                                                     *
* File    : pagenav.class.php			                                             *
* Version	: 1.3                                                                *
* Date		: 16-09-2005                                                         *
* Author	: Martin             	\				                                       *
* License	: Freeware                                                           *
*                                                                              *
* Class for helping table to create a simple page navigator.  								 *
* This Navigator also has a form to jump to specified page.                    *
*                                                                              *
* You may use, modify and redistribute this software as you wish.              *
*******************************************************************************/
class PageNav{

	/*******************************************************************************
	*                               Public Variable                                *
	*******************************************************************************/

	/** define the width of navigator table, usually the width is same as the data table width */
	var $width;
			
	/*******************************************************************************
	*                               Private Variable                               *
	*                   Dont use any variable below in outer class                 *
	*******************************************************************************/		
	
	/** define compound id*/
	var $cmpn;
	
	/** define page id*/
	var $page;
	
	/** define recordset handler*/
	var $data;
	
	/** define record count */
	var $rowsum;
	
	/** define the first page id*/
	var $first_page_row;
	
	/** define the last page id*/
	var $last_page_row;
	
	/** define data bound*/
	var $first_row;
	var $last_row;
	
	/** define compound parameter*/
	var $first_cmpn_page;
	var $last_cmpn_page;
	var $first_page;
	var $last_page;
	var $first_cmpn;
	var $last_cmpn;
	
	/** define rowcount that will display in one page */
	var $row_per_page;
	
	/** define page count that will save to one compound */
	var $page_per_cmpn;
	
	/** define window that will opening when button add clicked */
	var $addWindow;
	
	var $showNavigator;
	
	var $addButton;
	
	var $delButton;
	
	var $RDPanel;
	
	var $RDPanel_array;
	var $opsiPlus1=false;
	var $opsiPlus2=false;
	var $opsiPlus3=false;
	/**
	 * Constructor, define the default value for some variable.
	 * format : PageNav(ResultSet $dataset, int $rowLimit, int $pageLimit)
	 * input  : - $dataset, set navigator to a resultset, show it can count the parameter itself
	 *          - $ rowLimit, define row count that will display in one page
	 *          - $ pageLimit, define how many page that will enclosed on one compound
	 *          - $ width, define table width that will contain the navigator
	 */
	function PageNav($dataset,$rowLimit,$pageLimit,$width){
	  $this->data = $dataset;
		$this->width = $width;
		$this->row_per_page = $rowLimit;//20;
		$this->page_per_cmpn = $pageLimit;
		$this->showNavigator = false;
		$this->addButton = false;
		$this->delButton = false;
		$this->defineNavigator(2);		
		$this->RDPanel = false;
		$this->RDPanel_array;
	}
	
	/*******************************************************************************
	*                               Public methods                                 *
	*******************************************************************************/
	
	/**
	 * function to show the page navigator of data table
	 */
	function showPager($toggleOnOff){
		$this->showNavigator = $toggleOnOff;
	}
	
	/** Function to show Add Button */
	function showAddButton($toggleOnOff,$parameter){
		$this->addButton = $toggleOnOff;
		$this->addwindow = $parameter;
	}
	
	/** Function to show Delete Button */
	function showDelButton($toggleOnOff){
		$this->delButton = $toggleOnOff;
	}
	
	/** Function to show Radio Button Panel */
	function showRDPanel($toggleOnOff,$array){
		$this->RDPanel = $toggleOnOff;
		$this->RDPanel_array = $array;
	}
	var $btnNew=0;
	var $labelNew='';
	var $linkNew ='';
	var $btnsize;
	function btnNew($str,$linkNew,$btn){
		$this->labelNew = $str;
		$this->btnNew=true;	
		$this->linkNew= $linkNew;
		$this->btnsize = $btn;
	}
	/**
	 * Draw a navigator on a specified table, it will equip with a form to jum to specified page.
	 * it will display the navigator like this : 1 << 1 2 3 4 5 6 7 8 9 10 >> 15
	 */
	function drawPageNavigator($number){
		// TODO : Harus buat replacer Query String
		echo "<table width=\"$this->width\" border=\"0\" cellpadding=\"5\" cellspacing=\"0\" frameborder=\"0\">";
		echo "<tr>";
	  echo "<td>";
		if($this->RDPanel){
			// add and parse array to combobox
			echo "<select name=\"RDSelect\">";
			if (count($this->RDPanel_array) > 0) {
				for ($i=0;$i<count($this->RDPanel_array);$i++) {
					$array = $this->RDPanel_array[$i];
					$col = $array[0];
					$row = $array[1];
					if ($i == 0){
						echo "	  <option value=\"$col\" selected>$row</option>";
					}else{
						echo "	  <option value=\"$col\">$row</option>";
					}
				}
			}	      
			echo "</select>";
			// add button and ref to RDPanelClick()			
			echo '<button type="button" onClick="RDPanelClick(this)" name="btnRDPanel" class="btn_2" style="width:75px">'.$_SESSION['lang']['Proses'][$_SESSION['bahasa_sess']].'</button>';			
			echo '<span id="loadingproses"></span>';
			if($this->opsiPlus1==1){
				echo '<select name="statusPeb" id="statusPeb">
						<option value="">'.$_SESSION['lang']['Pilih Status PEB'][$_SESSION['bahasa_sess']].'</option>
						<option value="1"> - Completed</option>
						<option value="2"> - Pending</option>
						</select>';
			}
			if($this->opsiPlus2==1){
				echo '<select name="jnsUangMuka" id="jnsUangMuka">
						<option value="">'.$_SESSION['lang']['Jenis Uang Muka'][$_SESSION['bahasa_sess']].'</option>
						<option value="1">'.$_SESSION['lang']['Single'][$_SESSION['bahasa_sess']].'</option>
						<option value="2">'.$_SESSION['lang']['Multiple'][$_SESSION['bahasa_sess']].'</option>
						</select>';
			}
			if($this->btnNew){
				echo '<button type="button" onClick="javascript:window.location.href=\''.$this->linkNew.'\'" name="btnRDPanel" class="btn_'.$this->btnsize.'" style="width:105px">'.$this->labelNew.'</button>';
			}
			if($this->opsiPlus3==1){
				echo '<select name="terakhir" id="statusMultiple">
						<option value="">Pilih Status Multiple</option>
						<option value="1"> - Berlanjut</option>
						<option value="2"> - Terakhir</option>
						</select>';
			}			
		}
		if ($this->addButton){
			echo"<input type=\"button\" name=\"btnAdd\" value = \"add\" onClick=\"addWindow('$this->addwindow')\">";
		}
		if ($this->delButton){
			echo"<input type=\"button\" name=\"btnDel\" value=\"Hapus data terpilih!\" onClick=\"delButtonClick()\">";
		}
		echo"</td>";

  	echo "<td align=\"right\" style=\"font-family: arial; font-size: 11px; color:#333333;\">";
		/* display page navigator */
		if ($this->showNavigator){
			if ($this->rowsum > 0){
				// Jump Form
				$php = $_SERVER['PHP_SELF'];
				$myparam = $_SERVER['PHP_SELF']."?".$_SERVER['QUERY_STRING'];
				$myparam = preg_replace("/\&txtpage$number=\d+/","",$myparam);
				str_replace($potong, "$param=$value", $myparam);
				echo "<span style='float:left;margin-left:-30px;margin-top:5px;'>Total Rows : ".$this->rowsum." | ";
				echo "Total Pages : ".$this->very_last_page."</span>";
				echo "Go to page ";
				echo "<input type=\"textbox\" id='textpage'  name=\"txtpage$number\" style='text-align:center'  size=\"3\" value= ". ($_GET['txtpage0']? $_GET['txtpage0'] : $_GET['page']) ."> ";
				if(strstr($_SERVER['REQUEST_URI'],'FormSearchSSP.php') || strstr($_SERVER['REQUEST_URI'],'FormSearchSandiRTE.php') || strstr($_SERVER['REQUEST_URI'],'FormSearchReception.php') || strstr($_SERVER['REQUEST_URI'],'FormSearchSTT.php')){
					echo '<button class="btn_5" type="button" style="margin-right:0px;width:45px" name="btnPage" onClick="gopage(\''.$myparam.'\')">Go</button>';
				}else{
					echo '<button class="btn_5" type="button" style="margin-right:0px;width:45px" name="btnPage" onClick="gopage()">Go</button>';									
				}				
				echo "&nbsp&nbsp";
				
				// First - Previous
				$param = $this->makeParameter("cmpn","");
				$param = $this->makeParam($param,"page",1);				
				if( strstr($_SERVER['REQUEST_URI'],'FormSearchSSP.php') || strstr($_SERVER['REQUEST_URI'],'FormSearchSandiRTE.php') || 
					strstr($_SERVER['REQUEST_URI'],'FormSearchReception.php') || strstr($_SERVER['REQUEST_URI'],'FormSearchSTT.php')){
					$cmp = $this->cmpn - 1;
					$param = $this->makeParameter("page","");
					$param = $this->makeParam($param,"cmpn",$cmp);				
					echo "<a href=\"$param\" style=\"text-decoration: none;\"> &laquo; </a>";
		
					// Main Navigator
					for ($i=$this->first_page;$i<=$this->last_page;$i++){
						if ($i == $this->page){
							echo "<font style=\"font-size: 11px;\">";
						}
						else{	
							$param = $this->makeParameter("cmpn","");		
							$param = $this->makeParam($param,"page",$i);
							echo "<a href=\"$param\" style=\"text-decoration: none;\">";
						}
						echo $i." ";
						if ($i == $this->page){
							echo "</font>";
						}
						else{
							echo "</a>";
						}
					}
					
					// Next - Last
					$cmp = $this->cmpn + 1;
					$param = $this->makeParameter("page","");
					$param = $this->makeParam($param,"cmpn",$cmp);							
					//echo "<a href=\"$param\"> &raquo; </a>";
					$param = $this->makeParameter("cmpn","");				
					$param = $this->makeParam($param,"page",$this->very_last_page);
					echo "<a href=\"$param\" style=\"text-decoration: none;\"> &raquo; </a>";
				}else{					
					$cmp = $this->cmpn - 1;
					$param = $this->makeParameter("page","");
					$param = $this->makeParam($param,"cmpn",$cmp);				
					echo ($this->first_page!=1)?"<a href=\"javascript:goto('1')\" class='linkpage'> &laquo; </a>":"";
					echo "<a href=\"javascript:goto('".($this->first_page-1)."')\" class='linkpage'> &laquo; </a>";
		
					// Main Navigator
					for ($i=$this->first_page;$i<=$this->last_page;$i++){
						if ($i == $this->page){
							echo "<font class='currentpage'>";
						}
						else{	
							$param = $this->makeParameter("cmpn","");		
							$param = $this->makeParam($param,"page",$i);
							echo "<a href=\"javascript:goto('".$i."')\" class='linkpage'>";
						}
						echo $i." ";
						if ($i == $this->page){
							echo "</font>";
						}
						else{
							echo "</a>";
						}
					}
					
					// Next - Last
					$cmp = $this->cmpn + 1;
					$param = $this->makeParameter("page","");
					$param = $this->makeParam($param,"cmpn",$cmp);	
					$param = $this->makeParameter("cmpn","");				
					$param = $this->makeParam($param,"page",$this->very_last_page);
					echo "<a href=\"javascript:goto('".($this->last_page+1)."')\" class='linkpage'> &raquo; </a>";
					echo ($this->very_last_page>$this->last_page)? "<a href=\"javascript:goto('".($this->very_last_page)."')\" style=\"text-decoration: none;\"> &raquo; </a>" : "";
				
				}
			}
		}
		echo "</td>";

  	echo "</tr>";
		echo "</table>";
	}
	
	/*******************************************************************************
	*                               Private methods                                *
	*******************************************************************************/
		
	/**
	 * Define the parameter value that needed to draw a navigator
	 */
	function defineNavigator($number){
		$this->rowsum = $this->data->size();
		$this->very_last_page = ceil($this->rowsum/$this->row_per_page);
		$this->page = $_REQUEST['page'];
		$this->cmpn = $_REQUEST['cmpn'];
		
		for ($i=0;$i<$number;$i++) {
			$el = "txtpage$i";
		  $txtPage = $_REQUEST[$el];
			if ($txtPage != ""){
				$this->page = $txtPage;
				break;
			}
		}
		
		$cmpnsum = ceil($this->very_last_page/$this->page_per_cmpn);
		$c = (empty($this->cmpn));
		$d = (empty($this->page));
		
		//define compound & page
		if (empty($this->cmpn) && empty($this->page)){
			$this->cmpn = 1;
			$this->page =1 ;
		}

		if (empty($this->cmpn) && !empty($this->page)){
			if ($this->page > $this->very_last_page){
				$this->page = $this->very_last_page;
			}
			if ($this->page < 1){
				$this->page = 1;
			}
			$this->cmpn=ceil($this->page / $this->page_per_cmpn);
		}
				
		if (!empty($this->cmpn) && empty($this->page)){
			if ($this->cmpn < 1){
				$this->cmpn = 1;
			}
			if ($this->cmpn > $cmpnsum){
				$this->cmpn = $cmpnsum;
			}
			$this->page = (($this->cmpn - 1)*$this->page_per_cmpn) + 1;
		}
		
		if ($this->cmpn < 1) {
			$this->cmpn = 1;
		}
		
		if ($this->cmpn > $cmpnsum){
			$this->cmpn = $cmpnsum;
		}		

		if (($this->page == "")||($this->page < 1)){
			$this->page = 1;
		}
					
		//define pages for page navigator
		$this->first_page = (($this->cmpn - 1) * $this->page_per_cmpn) + 1;
		$this->last_page = $this->cmpn * $this->page_per_cmpn;
		if ($this->last_page > $this->very_last_page){
			$this->last_page = $this->very_last_page;
		}
	
		//define records to display
		$this->first_row = (($this->page - 1)  * $this->row_per_page) + 1;
		$this->last_row = $this->page * $this->row_per_page;
		if ($this->last_row > $this->rowsum){
			$this->last_row = $this->rowsum;
		}
	}
	
	/**
	 * Function to make parameter to be replace in Query String.
	 */
	function makeParameter($param,$value){
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
	
	function makeParam($query,$param,$value){
	  $data = $query;
		$pos = strpos($data, "$param=");
		if ($pos === false){
		 	$data .= "&$param=$value";
		}
		else{
		 	$potong = $param."=".$_REQUEST[$param];
		 	$data = str_replace($potong, "$param=$value", $data);
	  }
		$data = preg_replace("/\&txtpage\d=\d+/","",$data);
		$data = preg_replace("/txtpage\d=/","",$data);		
		$data = preg_replace("/\?\&/","?",$data);
	  return $data;	
	}	
}
?>
