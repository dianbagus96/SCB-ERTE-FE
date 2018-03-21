<?php

/*******************************************************************************
* Class   : SearchTable                                                         *
* File    : searchtbl.class.php                                                *
* Version	: 1.3                                                                *
* Date		: 16-09-2005                                                         *
* Author	: Martin             					                                       *
* License	: Freeware                                                           *
*                                                                              *
* Class for drawing a table automaticly when get input from SQL(select command)*
* So, You will more faster and easier when developing a simple output as table *                                                                             *                                                                             *
*                                                                              *
*                                                                              *
* You may use, modify and redistribute this software as you wish.              *
*******************************************************************************/
class SearchTable extends HTMLTable{

	/**
	 * Function to draw row.
	 */	
	function drawRow($count_column, $loop){
		if ($this->anyParent()){
			$count_column = count($this->field);;
			// cara draw multi column.
			echo "<tr>";

			if($this->showCBX){
				$cbxVal = $this->dataset->get($this->field[$this->colRef]->name);
				if ($cbxVal == ""){
					$cbxVal = "&nbsp;";
				}
				echo "<td nowrap><input type=\"checkbox\" name=\"cbx[]\" value=\"$cbxVal\"></td>";
			}
			
			for ($i = 0; $i <= $count_column-1; $i++) {
				if(!$this->field[$i]->isParent){
			
					$data = $this->dataset->get($this->field[$i]->name);
					if ($data == ""){
						$data = "&nbsp;";
					}
						
					//check dulu apakah hidden bernilai true.
					if ($this->field[$i]->hidden){
						echo "<input type=\"hidden\" name=\"$this->field[$i]->name\" value=\"$data\">";
					}
					else{
						
						$wrap = "<td";
						if (!$this->field[$i]->wrap){
							$wrap .=" nowrap";
						} 
						$wrap .= ">";
						echo $wrap;
						echo "<div align=\"$this->field[$i]->align\">";
	
						if ($this->field[$i]->linker != ""){
							$matches = $this->parseLink($this->field[$i]->linker);
							$count_match = count($matches);
							$mylink = $this->field[$i]->linker;
							for ($loop = 0; $loop <= $count_match-1; $loop++) {
								$datalink = $matches[$loop][0];
								$replace = substr($datalink,1);
								$mylink = str_replace($datalink, $this->dataset->get($replace),$this->field[$i]->linker);
							}				
							echo "<a href=\"$mylink\">$data</a>";
						}
						else{
							echo $data;
						}
						echo "</div>";
						echo "</td>";
					}
				}
			}
			echo "</tr>";
		}
		else{		
			if($loop % 2 == 0){
				echo '<tr style="border-bottom: 1px solid #D7D7D7; background:#E5EEF5;">';
			}else{
				echo '<tr>';			
			}
			//echo "<tr>";
			if($this->showCBX){
				$cbxVal = $this->dataset->get($this->field[$this->colRef]->name);
				if ($cbxVal == ""){
					$cbxVal = "&nbsp;";
				}
				echo "<td height=\"20\"><input type=\"checkbox\" name=\"cbx[]\" value=\"$cbxVal\"></td>";
			}
			
			for ($i = 0; $i <= $count_column-1; $i++) {
				$data = $this->dataset->get($i);
				if ($data == ""){
					$data = "&nbsp;";
				}
				
				//check dulu apakah hidden bernilai true.
				if ($this->field[$i]->hidden){
					echo "<input type=\"hidden\" name=\"$this->field[$i]->name\" value=\"$data\">";
				}
				else{
					$wrap = "<td height=\"20\"";
					if (!$this->field[$i]->wrap){
						$wrap .=" nowrap";
					} 
					$wrap .= " style=\"border-bottom: 1px solid #D7D7D7;\" >";
					echo $wrap;
					echo "<div align=\"$this->field[$i]->align\" style=\"font-size:11px; font-family:Arial; padding:6px 0px 6px 9px;\">";
					
					if ($this->field[$i]->linker != ""){
						$matches = $this->parseLink($this->field[$i]->linker);
						$count_match = count($matches);
						$mylink = $this->field[$i]->linker;
						for ($loop = 0; $loop < $count_match; $loop++) {
							$datalink = "'".$matches[$loop][0]."'";
							$mylink = str_replace($datalink, "'".$this->strFilter($this->dataset->get($loop))."'",$mylink);																					
						}
						echo "<a href=\"$mylink\" style=\"color: #005D9A; text-decoration: none;\">$data</a>";
					}
					else{
						echo $data;
					}
					echo "</div>";
					echo "</td>";
				}
			}
		}
		echo "</tr>";
	}
	function strFilter($value){
		  $value = stripslashes(strip_tags($value));
		  $value = str_replace(array('delete',
			 'DELETE',
			 'rm -',
			 '!',
			 '|',
			 '?',
			 '=',
			 '`',	 
			 '"',	 
			 '\\\\',
			 '\\',
			 '//',	 
			 ':',
			 '*',
			 '>',
			 '<'
			 ), ' ', $value);	 
			if(strpos($value,"''")===false){
				$value = str_replace("'","''",$value); 
			}else{
				$value = str_replace("'","",$value); 
			}
		  return trim($value);	
		}
}	
?> 
