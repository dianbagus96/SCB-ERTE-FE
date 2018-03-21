<?php
	class Search {
		
		var $SQL;
		
		var $db;
		
		var $form;
		
		//berupa array
		var $column;
		var $colm;
		var $task;
		var $impid;
		var $kantor;
		
		var $_mylist = array() ;
		
		function Search($db,$form,$column){
			$this->db = $db;
			$this->SQL = "";
			$this->form = $form;
			$this->colm = $column;
			$this->column = explode(";",$column); // $this->column = $column;
			$this->define_script();
		}
		
		function set_param($task,$impid,$kantor){
			$this->task = $task;
			$this->impid = $impid;
			$this->kantor = $kantor;
		}
		
		function clear_List(){
			unset($this->_mylist);
		}
		
		function add_list($column,$value){
			$this->_myList[] = array($column,$value);
		}
		
		function add_categori($array){
			$this->_myList = $array;
		}
				
		function drawSearch(){
			if ($this->SQL != ""){
				$this->define_search();
				$this->drawForm();
				$this->drawTable();
			}
		}
		
		// ini coba, klo salah hapus lagi
		function define_link(){
			$link = "javascript:set_values(";
			for($i=0;$i<count($this->column);$i++){
				$link .= "'%$i',";
			}
			$link = substr($link,0,strlen($link)-1);
			$link .= ")";
			return $link;
		}
		
		function define_script(){
			echo "<script>";
			echo "function setCheck(vale){\n";
			echo "	for (j = 0; j < opener.document.$this->form.elements.length; j++){\n";
			//(opener.document.$this->form.elements[j].type == \"checkbox\")
			echo "		if ((opener.document.$this->form.elements[j].name == \"chkbx[]\") && (opener.document.$this->form.elements[j].value == vale)){\n";
			echo "			opener.document.$this->form.elements[j].checked = true;\n";
			echo "		}\n";
			echo "	}\n";
			echo "}\n";
			
			$myval = "";
			for($i=0;$i<count($this->column);$i++){
				$myval .= "myvalue$i,";
			}
			$myval = substr($myval,0,strlen($myval)-1);		 
	  	$txt = "function set_values($myval){\n";
			echo $txt;
			//echo "function set_values(myvalue){";
		 	//echo "  opener.document.$this->form.$this->column.value=myvalue;";
			for($i=0;$i<count($this->column);$i++){
				$data = $this->column[$i];
				if($data == "chkbx[]"){
					echo "for (j = 0; j < opener.document.$this->form.elements.length; j++){\n";
					echo "	if(opener.document.$this->form.elements[j].name == \"chkbx[]\"){\n";					
					echo "	  opener.document.$this->form.elements[j].checked = false;\n";
					echo "  }\n";						
					echo "}\n";	
					echo "for(var i = 0; i < myvalue$i.length;i++){\n";
					echo "  var nilai = myvalue$i.charAt(i);\n";
					echo "  setCheck(nilai);\n";
					echo "}\n";
				}else{
					echo "  opener.document.$this->form.$data.value=myvalue$i;\n";
				}
			}			
		 	echo "  window.close();\n";
		 	echo "}\n";
		 	echo "</script>";		
		}
		
		function define_search(){
			$search_col = $_REQUEST['lstCari'];
			$search_raw = $_REQUEST['txtCari'];
			$search_cbx = $_REQUEST['cbx'];
			
			$tambahan = "";
			if ($search_raw != ""){
				if ($search_cbx){
					$tambahan = " where upper($search_col) = upper('$search_raw') ";	
				}
				else{
					$tambahan = " where upper($search_col) like upper('%$search_raw%') ";
				}
				
				// analisa apakah ada kata where di SQL statement
				$hasil = strpos(strtoupper($this->SQL),"WHERE");
				if($hasil === false){ // klo ga ada, disisipkan kata prioritas pertama group, kedua oleh kata order
					$group = strpos(strtoupper($this->SQL),"GROUP");
					if($group === false){
						$order = strpos(strtoupper($this->SQL),"ORDER");
						if ($order === false){
							$this->SQL .= $tambahan;
						}else{
							$tambahan .= "order ";
							$this->SQL = str_replace(" ORDER ",$tambahan,strtoupper($this->SQL));
						}
					}else{
						$tambahan .= "group ";
						$this->SQL = str_replace(" GROUP ",$tambahan,strtoupper($this->SQL));
					}
				}else{ // klo ada di replace, diasumsikan selalu apa pattern spt ini where xxx...
					$tambahan .= "and ";
					$this->SQL = str_replace(" WHERE ",$tambahan,strtoupper($this->SQL));
				}				
			}
		}
		
		function drawForm(){
			$php = $_SERVER['PHP_SELF'];
			echo "<form name=\"frmcari\" action=\"$php\" method=\"GET\">";
			echo "<table width=\"100%\"  border=\"0\" cellspacing=\"0\" cellpadding=\"5\">";
			echo "	<tr class=tbl_hdr>";
			echo "		<td width=\"95%\">Kolom : ";
			echo "			<select name=\"lstCari\">";

			if (count($this->_myList) > 0) {
				for ($i=0;$i<count($this->_myList);$i++) {
					$data = $this->_myList[$i];
					$col = $data[0];
					$row = $data[1];
					if ($i == 0){
						echo "	  <option value=\"$col\" selected>$row</option>";
					}else{
						echo "	  <option value=\"$col\">$row</option>";
					}
				}
			}	
			echo "			</select>&nbsp;Cari : ";
			echo "			<input type=\"text\" name=\"txtCari\">&nbsp;";
			echo "			<input type=\"checkbox\" name=\"cbxMatch\" value=\"true\">seluruh kata";
			$form = $this->form;
			$colm = $this->colm;
			$task = $this->task;
			$impid = $this->impid;
			$kantor = $this->kantor;
			echo "			<input name=\"frm\" type=\"hidden\" value=\"$form\">";
			echo "			<input name=\"colm\" type=\"hidden\" value=\"$colm\">";
			echo "			<input name=\"task\" type=\"hidden\" value=\"$task\">";
			echo "			<input name=\"impid\" type=\"hidden\" value=\"$impid\">";
			echo "			<input name=\"kantor\" type=\"hidden\" value=\"$kantor\">";
			echo "		</td>";
			echo "		<td width=\"5%\"><input type=\"submit\" name=\"submitCari\" value=\"Cari\"></td>";
			echo "	</tr>";
			echo "</table>";
			echo "</form>";
		}
		
		function drawTable(){
			$table = new SearchTable();
			$table->connection = $this->db;
			$table->width = "100%";
			//echo  $this->SQL;
			$table->SQL = $this->SQL;
			$table->border = 0;
			$table->cellpadding = 2;
			$table->cellspacing = 1;
			$table->frameborder = 0;
			$table->showPager(true,"TOP",10,10);
			$table->showCheckBox(false,0);
			$table->showAddButton(false,$F_HANDLER->TOP,"",false);
			$table->showDelButton(false,$F_HANDLER->TOP,"",false);
			$link = $this->define_link();
			$table->field[0]->linker = $link;
				
			$table->drawTable();		
		}
	}
?>

