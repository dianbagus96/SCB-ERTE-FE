base_url = 'https://standardchartered.ebank-services.com/RTE/';
var normal = {'border' : '1px #9BA8B5 solid','color' : '#000'} 
var harus_isi = {'border' : '1px #CC6600 solid','color' : '#FF0000'} 
$(document).ready(function(){
	
	$('.tooltip').mousemove(function(){
		$(this).append("<div class='title'>"+$(this).attr('label')+"</div>");	
		
	}).mouseout(function(){
	  $('.title').remove();
	});					   
	
	$('input:text, textarea').each(function(){
		$(this).keypress(function(e){
			if ( e.which == 13 ) return false;	
		});												 
	})
	$('input.nospace').keydown(function(e) {
		if (e.keyCode == 32) {
			return false;
		}
	});
	$('input:text, textarea, select').attr('autocomplete','off');
	
	$('input:text,input:file, textarea, select').keyup(function(){
		if($(this).attr('readonly')==false && $(this).hasClass('notvalid')){
			$(this).css(normal).attr('autocomplete','off').removeAttr('placeholder').removeClass('exist');
		}
	}).change(function(){
		if($(this).hasClass('notvalid')){
			$(this).css(normal).attr('autocomplete','off').removeAttr('placeholder').removeClass('exist');		
		}
	})
	
	$('input.danaEkspor').keyup(function(){
		var id = $(this).attr('no');
		var nominalDiterima = $(this).attr('nominalDiterima');		
		var nilai = $(this).val().replace(',','');
		var nilai = nilai.replace(',','');nilai = nilai.replace(',','');nilai = nilai.replace(',','');nilai = nilai.replace(',','');nilai = nilai.replace(',','');
		var nilai = nilai.replace(',','');nilai = nilai.replace(',','');nilai = nilai.replace(',','');nilai = nilai.replace(',','');nilai = nilai.replace(',','');
		var nilai = nilai.replace(',','');
		$('#ajaxMod4Text'+id).val( money_format(''+eval(nominalDiterima)-eval(nilai)+''));		
	})
	
	if($('#cekrte').attr('checked')==true){
		$('#noRekRTE').show();	
		$('#noRek').removeAttr("disabled");	
		$('#groupAccount').show();				
	}else{
		$('#noRekRTE').hide();
		$('#groupAccount').hide();		
	//	$('#noRek').val('');
	}
	
	if($('select#role').val()=='5'){
		$('#listRekening').show();
	}else{
		$('#listRekening').hide();		
	}
	
	$('#cekrte').click(function(){
		if($(this).attr('checked')==true){
			$('#noRekRTE').show();	
			$('#groupAccount').show();
			$('#noRek').val($('input.noReksAwal').val());			
			$('#account_group').removeAttr('disabled');	
			$('#noRek').removeAttr("disabled");	
		}else{
			$('#noRek').val('');
			$('#noRekRTE').hide();	
			$('#groupAccount').hide();					
			$('#account_group').attr('disabled','disabled');	
			$('#noRek').attr("disabled","disabled");	
		}	
	});	
	
	$('input.amount').keyup(function(){
		var  total = 0;
		$('input.amount').each(function(){
			nilai = $(this).val().replace(",",""); nilai = nilai.replace(",",""); nilai = nilai.replace(",","");
			nilai = nilai.replace(",",""); nilai = nilai.replace(",",""); nilai = nilai.replace(",","");	
			
			total += eval(nilai);
		})
		var total = money_format(total+'');
		$('input#total').val(total);	
	})
	
	if($('select#role').size()){
		var val = $('select#role').val();
		$('#listRekening').hide();				
		$('#account_group').attr('disabled','disabled');
		if(val==4 || val==5){
			$('tbody#grouparea').attr('aktif','0');
			$('tbody#grouparea').hide();
			if(val==5){ 
				$('#listRekening').show(); 
				$('#account_group').removeAttr('disabled');
			}				
		}else{
			$('tbody#grouparea').attr('aktif','1');
			$('tbody#grouparea').show();		
		}	
	}
})

function disclaimer(){
	message =           "User Agreement/Disclaimer\n\n";
	message = message + "This website contains information pertaining to MP3 �s (e-Payment DJP System)  of EDII Bank Customers and the services provided in this website are exclusively for the use of DB customers.\n";
	message = message + "EDII Bank shall not be liable or responsible to any person for the accuracy or veracity of any information or data contained in this website and nothing herein shall be, or deemed to be a representation or warranty by EDII Bank of any matters in this website. EDII Bank shall not be nor deemed to be an agent or representative of PT EDI Indonesia in respect of this website.\n";
	jAlert(message);
}
//--ardi for cek html element on input
function hoj(d,tipe) {	
	var arr = new Array();
	switch(tipe){
		case 0 	: arr = Array("<",">","=","&","(",")","[","]","{","}","+","?",";");break;
		case 1	: arr = Array("<",">","=","&","(",")","[","]","{","}","+","?",";","-","'","`","$","%","@","#","/","\\","*","~","|","_",",",".");break;	
		case 2	: arr = Array("<",">","=","&","[","]","{","}","+","?",";","`","$","%","@","#","/","\\","*","~","|");break;
	}
	for(a in arr){		
		if(d == arr[a]) return false;			
	}
}
function htmlEntity(str) {
  var aa = str;
  var bb = 0;  
  for (i = 0; i < aa.length; i++) 
  	bb += (hoj(aa.charAt(i),0)==false)? 1 : 0;
  return(bb>0)?false :true;
}
function filterUserId(str) {
  var aa = str;
  var bb = 0;  
  for (i = 0; i < aa.length; i++){ 
  	bb += (hoj(aa.charAt(i),1)==false)? 1 : 0;		
  }
  return(bb>0)?false :true;
}

function filterGroupId(str) {
  var aa = str;
  var bb = 0;  
  for (i = 0; i < aa.length; i++) 
  	bb += (hoj(aa.charAt(i),1)==false)? 1 : 0;
  return(bb>0)?false :true;
}

function filterStr(str){
  var aa = str;
  var bb = 0;  
  for (i = 0; i < aa.length; i++){ 
  	bb += (hoj(aa.charAt(i),2)==false)? 1 : 0;		
  }
  return(bb>0)?false :true;
}

//--------adri for cek date format


/**
 * DHTML date validation script. Courtesy of SmartWebby.com (http://www.smartwebby.com/dhtml/datevalidation.asp)
 */
// Declaring valid date character, minimum year and maximum year
var dtCh= "-";
var minYear=1900;
var maxYear=2100;

function isInteger(s){
	var i;
    for (i = 0; i < s.length; i++){   
        // Check that current character is number.
        var c = s.charAt(i);
        if (((c < "0") || (c > "9"))) return false;
    }
    // All characters are numbers.
    return true;
}

function stripCharsInBag(s, bag){
	var i;
    var returnString = "";
    // Search through string's characters one by one.
    // If character is not in bag, append to returnString.
    for (i = 0; i < s.length; i++){   
        var c = s.charAt(i);
        if (bag.indexOf(c) == -1) returnString += c;
    }
    return returnString;
}

function daysInFebruary (year){
	// February has 29 days in any year evenly divisible by four,
    // EXCEPT for centurial years which are not also divisible by 400.
    return (((year % 4 == 0) && ( (!(year % 100 == 0)) || (year % 400 == 0))) ? 29 : 28 );
}
function DaysArray(n) {
	for (var i = 1; i <= n; i++) {
		this[i] = 31
		if (i==4 || i==6 || i==9 || i==11) {this[i] = 30}
		if (i==2) {this[i] = 29}
   } 
   return this
}

function isDate(dtStr){
	var daysInMonth = DaysArray(12)
	var pos1=dtStr.indexOf(dtCh)
	var pos2=dtStr.indexOf(dtCh,pos1+1)
	var strDay=dtStr.substring(0,pos1)
	var strMonth=dtStr.substring(pos1+1,pos2)
	var strYear=dtStr.substring(pos2+1)
	strYr=strYear
	if (strDay.charAt(0)=="0" && strDay.length>1) strDay=strDay.substring(1)
	if (strMonth.charAt(0)=="0" && strMonth.length>1) strMonth=strMonth.substring(1)
	for (var i = 1; i <= 3; i++) {
		if (strYr.charAt(0)=="0" && strYr.length>1) strYr=strYr.substring(1)
	}
	var month=parseInt(strMonth)
	var day=parseInt(strDay)
	var year=parseInt(strYr)
	if (pos1==-1 || pos2==-1){	
		return false
	}
	if (strMonth.length<1 || month<1 || month>12){	
		return false
	}
	if (strDay.length<1 || day<1 || day>31 || (month==2 && day>daysInFebruary(year)) || day > daysInMonth[month]){	
		return false
	}
	if (strYear.length != 4 || year==0 || year<minYear || year>maxYear){		
		return false
	}
	if (dtStr.indexOf(dtCh,pos2+1)!=-1 || isInteger(stripCharsInBag(dtStr, dtCh))==false){	
		return false
	}
return true
}

function ValidateForm(){
	var dt=document.frmSample.txtDate
	if (isDate(dt.value)==false){
		dt.focus()
		return false
	}
    return true
 }


/*email*/
function cekEmail(str) {
		var at="@"
		var dot="."
		var lat=str.indexOf(at)
		var lstr=str.length
		var ldot=str.indexOf(dot)
		Err =0;
		if (str.indexOf(at)==-1){
		   Err++;		   
		}
		if (str.indexOf(at)==-1 || str.indexOf(at)==0 || str.indexOf(at)==lstr){
		  Err++;
		}
		if (str.indexOf(dot)==-1 || str.indexOf(dot)==0 || str.indexOf(dot)==lstr){
		    Err++;
		}
		if (str.indexOf(at,(lat+1))!=-1){
		    Err++;
		}
		if (str.substring(lat-1,lat)==dot || str.substring(lat+1,lat+2)==dot){
			Err++;
		}
		 if (str.indexOf(dot,(lat+2))==-1){
		   Err++;
		 }		
		 if (str.indexOf(" ")!=-1){
		    Err++;
		 }
 		 return (Err>0)?false :true;					
	}
//---
function cekPhone(nomor)
{
	var notValid = 0;
	var illegalChars= /[\(\)\<\>\#\$\%\*\~\`\;\:\\\"\[\]]/ ;
	if(nomor.search(/[a-zA-Z]+/)!=-1) notValid++;
	if(nomor.match(illegalChars)) notValid++;
	return (notValid>0)? false :true;	
}

function popWajibPajak(){
	window.open(base_url+'FormSearchSSP.php?task=wp&frm=frmSSP&colm=npwp;nama;address;city;zipcode','_blank','width=960,height=480,top=150,left=150,toolbar=no,minimize=no,status=no,memubar=no,location=no,scrollbars=yes','1');
}

			  
function popIDCabang(){
	window.open(base_url+'FormSearchSSP.php?task=cabang&frm=frmPengguna&colm=branchsspcp;nmbranch','_blank','width=640,height=480,top=150,left=150,toolbar=no,minimize=no,status=no,memubar=no,location=no,scrollbars=yes','1');
}
 
function LegalNotice(){
	window.open(base_url+'legalnotice.php','_blank','width=1000,height=480,top=150,left=150,toolbar=no,minimize=no,status=no,memubar=no,location=no,scrollbars=yes','1');
}

function requirement(){
	window.open(base_url+'requirement.php','_blank','width=1000,height=480,top=150,left=150,toolbar=no,minimize=no,status=no,memubar=no,location=no,scrollbars=yes','1');
}

function popPengguna(){
	window.open(base_url+'FormSearchSSP.php?task=user&frm=frmPengguna&colm=wpnpwp;wpnama;wpalamat;wpkota;wpzipcode','_blank','width=640,height=480,top=150,left=150,toolbar=no,minimize=no,status=no,memubar=no,location=no,scrollbars=yes','1');
}
function popIDPengguna(){
	$("#warningUserid").html('');
	window.open(base_url+'FormSearchSSP.php?task=userID&frm=frmPengguna&colm=id;wpnpwp;wpnama;wpalamat;wpkota;wpzipcode;pic;picEmail;picPhone;picFax;noRek;CSMEmail;grouping','_blank','width=960,height=480,top=150,left=150,toolbar=no,minimize=no,status=no,memubar=no,location=no,scrollbars=yes','1');
}
function popKodeMap(){
	window.open(base_url+'FormSearchSSP.php?task=map&frm=frmSSP&colm=kdmap;kdjnsbyr;urjnsbyr;acc02','_blank','width=960,height=480,top=150,left=150,toolbar=no,minimize=no,status=no,memubar=no,location=no,scrollbars=yes','1');
}
function popDatPeny(){
	window.open(base_url+'FormSearchSSP.php?task=datP&frm=frmSSP&colm=paynpwp;paynama;payaccount;tipeacc','_blank','width=960,height=480,top=150,left=150,toolbar=no,minimize=no,status=no,memubar=no,location=no,scrollbars=yes','1');
}
function popDatPenyUpload(){
	window.open(base_url+'FormSearchSSP.php?task=datP&frm=frmUploadz&colm=paynpwp;paynama;payaccount;tipeacc','_blank','width=960,height=480,top=150,left=150,toolbar=no,minimize=no,status=no,memubar=no,location=no,scrollbars=yes','1');
}
function popDatPenyAcc(){
	window.open(base_url+'FormSearchSSP.php?task=datAcc&frm=frmAcc&colm=npwp_p','_blank','width=640,height=480,top=150,left=150,toolbar=no,minimize=no,status=no,memubar=no,location=no,scrollbars=yes','1');
}
function popReception(){
	window.open(base_url+'FormSearchReception.php?frm=down_recption&colm=kdmap_d;mspjk_d;thpjk_d;kdjnsbyr_d;nmjnsbyr_d;npwp_penyetor_d;nama_penyetor_d','_blank','width=960,height=480,top=150,left=150,toolbar=no,minimize=no,status=no,memubar=no,location=no,scrollbars=yes','1');
}

function popAccount(){	
	window.open(base_url+'FormSearchSSP.php?frm=taxrek&task=taxrekening&colm=groupid;deptid;a;b','_blank','width=950,height=480,top=150,left=100,toolbar=no,minimize=no,status=no,memubar=no,location=no,scrollbars=yes','1');
}
function popGroupid(){	
	window.open(base_url+'FormSearchSSP.php?frm=groupid&task=groupid&colm=groupid;groupname;deptid;a;b','_blank','width=950,height=480,top=150,left=100,toolbar=no,minimize=no,status=no,memubar=no,location=no,scrollbars=yes','1');
}

function popUserGroup(groupid){ 	
	window.open(base_url+'FormSearchSSP.php?frm=usergroup&task=usergroup&groupid='+groupid+'&colm=checker;checkername;b;c;d','_blank','width=950,height=480,top=150,left=100,toolbar=no,minimize=no,status=no,memubar=no,location=no,scrollbars=yes','1');
}

function popUserDept(deptid){ 
	window.open(base_url+'FormSearchSSP.php?frm=usergroup&task=userdept&deptid='+deptid+'&colm=checker;checkername;b;c;d','_blank','width=950,height=480,top=150,left=100,toolbar=no,minimize=no,status=no,memubar=no,location=no,scrollbars=yes','1');
}

function popPIC(groupid){	
	window.open(base_url+'FormSearchSSP.php?frm=usergroup&task=pic&colm=pic;a;b;c;d','_blank','width=950,height=480,top=150,left=100,toolbar=no,minimize=no,status=no,memubar=no,location=no,scrollbars=yes','1');
}

function popChecker(checker){ 
	window.open(base_url+'FormSearchSSP.php?frm=checker&task=checker&deptid='+checker+'&colm=a;b;c;d;e','_blank','width=950,height=480,top=150,left=100,toolbar=no,minimize=no,status=no,memubar=no,location=no,scrollbars=yes','1');
}

function popKpbc(){	
	window.open(base_url+'FormSearchSSP.php?frm=groupid&task=kpbc&colm=kdkntr;namakpbc;a;b','kpbc','width=650,height=380,top=150,left=100,toolbar=no,minimize=no,status=no,memubar=no,location=no,scrollbars=yes','1');
}

function showSandiKeterangan(no){
	window.open(base_url+'FormSearchSandiRTE.php?frm=formtable&colm='+no,no,'width=960,height=480,top=150,left=150,toolbar=no,minimize=no,status=no,memubar=no,location=no,scrollbars=yes','1');
}

function showSTT(no){
	window.open(base_url+'FormSearchSTT.php?frm=formtable&colm='+no,no,'width=960,height=480,top=150,left=150,toolbar=no,minimize=no,status=no,memubar=no,location=no,scrollbars=yes','1');
}

function setReadonly(a){		
	$(a).attr('readonly','readonly')
}
function cekField(nilai){
	if(nilai=="TGLBYR" || nilai =='WAKTU' || nilai =='TGL'|| nilai =='tgl_PEB'||nilai =='tgl_transaksi'||nilai =='tglInput'||nilai=='TGL_PEB' || nilai=='TGL_SEND'){
		document.frmCari.q.style.display="none";
		document.frmCari.space.style.display="inline";
		document.frmCari.popcal1.style.display="inline";
		document.frmCari.popcal2.style.display="inline";
		document.frmCari.tg1.style.display="inline";
		document.frmCari.tg2.style.display="inline";		
		document.frmCari.search1.style.display="inline";
		document.frmCari.search0.style.display="none";
	} else {
		document.frmCari.q.style.display="inline";
		document.frmCari.space.style.display="none";
		document.frmCari.popcal1.style.display="none";
		document.frmCari.popcal2.style.display="none";
		document.frmCari.tg1.style.display="none";
		document.frmCari.tg2.style.display="none";		
		document.frmCari.search0.style.display="inline";
		document.frmCari.search1.style.display="none";		
	}
}

function cekForm(tgl){
	var message = '';
	if(tgl==1){
		var tgl1 =document.frmCari.tg1.value;
		var tgl2 =document.frmCari.tg2.value;	
		if(!tgl1){
			message = message + "- Field Tanggal Pertama belum diisi! \n";
		}else if(isDate(tgl1)==false){
			message = message + "- Format Tanggal harus dd-mm-yyyy! \n";
		}
		
		if(!tgl2){
			message = message + "- Field Tanggal Kedua belum diisi! \n";
		}else if(isDate(tgl2)==false){
			message = message + "- Format Tanggal harus dd-mm-yyyy!! \n";
		}
	}
	if (document.frmCari.q.value.length==0) {
		
	}else if(htmlEntity(document.frmCari.q.value)==false){
		message = message + "- Kata kunci yang diisi mengandung karakter ilegal! \n";
	}	
	if(message.length > 0){
		jAlert("Pengisian data berikut belum benar :\n" + message); 
		return false;
	}else{
		document.frmCari.submit();
	}
}

function cekCabang(){
	var message = '';
	
	if (document.frmCabang.kode.value.length==0) {
		message = message + "- Kode Cabang field belum diisi! \n";
	}
	
	if(message.length > 0){
		jAlert("Pengisian data berikut belum benar :\n" + message); 
		return false;
	}
}
var npwpTersedia=0;
function cekNPWP(a,type){
  var str = a.value;
  $.ajax({
	  type :'POST',
	  url  :base_url+'cekComProf.php?modul='+type,
	  data : 'npwp='+str,
	  success : function(msg){
		var   hasil = jQuery.parseJSON(msg);
		  if(hasil.jumlah>0){
			  $("#warningNPWP").hide()
			  $("#warningNPWP").html(' <b style=color:#900;>*NPWP already exists!</b>').fadeIn(1000);
			  npwpTersedia=1;
		  }else{
			  $("#warningNPWP").fadeOut('slow');//.html('');				
			  npwpTersedia=0;
		  }
	  }
  });		
}

var res=0;
function cekFormWP(idform){
	var message = '';
	var err=0;
	
	var result = validateForm(idform);
	err = result[0];
	message = result[1];
	
	if(message==""){
		cekTaxPayer();		
		if(res>0){
			message = message + "- Ada duplikasi NPWP! \n";
		}
	}
		
	if($('#warningUserid').html()=="" && message.length==0){
		message = message + "- Silakan check User ID terlebih dahulu! \n";	
	}
	
	if(message.length > 0){
		jAlert("Pengisian data berikut belum benar :\n" + message); 
		return false;
	}else{				
		if(err==0){
			$('form#'+idform).submit();
		}		
	}
		
}

function cekFormPe(){
	var message = '';
	
	if (document.frmWP.npwp.value.length==0) {
		message = message + "- Field NPWP belum diisi! \n";
	}else if (!Number (document.frmWP.npwp.value)) {
		message = message + "- Field NPWP harus diisi dalam bentuk angka! \n";
	}else if(npwpTersedia>0){
		message = message + "- NPWP sudah tersedia! \n";
	}
	
	if (document.frmWP.nama.value.length==0) {
		message = message + "- Field Nama Wajib Pajak belum diisi! \n";
	}else if(htmlEntity(document.frmWP.nama.value) == false){
		message = message + "- Field Nama Wajib Pajak mengandung karakter ilegal! \n";
	}
	
	if (document.frmWP.account.value.length==0) {
		message = message + "- Field Account belum diisi! \n";
	}else if (!Number (document.frmWP.account.value)) {
		message = message + "- Field Account harus diisi dalam bentuk angka! \n";
	}	
	if(message.length > 0){
		jAlert("Pengisian data berikut belum benar :\n" + message); 
		return false;
	}else{
		document.frmWP.submit();
	}
}

//buat administrator
function cekFormPengguna1(idform){
	var message = '';
	var err=0;
	
	result = validateForm(idform);
	err = result[0];
	message = result[1];
		
	if($('#warningUserid').html()=="" && message.length==0){
		message = message + "- Silakan check User ID terlebih dahulu! \n";	
	}
	
	if(message.length > 0){
		jAlert("Pengisian data berikut belum benar :\n" + message); 
		return false;
	}else{
				
		if(err==0){
			$('form#'+idform).submit();
		}		
	}
}

function cekFormPengguna2(idform){
	
	var message = '';
	var err=0;
	
	result = validateForm(idform);
	err = result[0];
	message = result[1];
	
	if($('#warningUserid').html()=="" && message.length==0){
		message = message + "- Silakan check User ID terlebih dahulu! \n";	
	}
		
	if(message.length > 0){
		jAlert("Pengisian data berikut belum benar :\n" + message); 
		return false;
	}else{
		if(err==0){
			$('form#'+idform).submit();
		}		
	}	
	
}

function validatePass(idform){
	var intRegex = /[0-9 -()+]+$/; 
	
	var message = '';
	var err=0;	
	var pertama;						
	$('form#'+idform+' input:text,form#'+idform+' input:password,form#'+idform+' input:file,form#'+idform+' textarea, form#'+idform+' select').each(function(){
		var val = $.trim($(this).val());	
		var cekerr = 0;						
		if($(this).attr('disabled')==false){
			if($(this).hasClass('isi') && val==""){			
				$(this).css(harus_isi);
				$(this).addClass('notvalid');
				$(this).attr('placeholder','harus diisi');				
				message +="- Field "+$(this).attr('label')+" harus diisi! \n";	
				cekerr++			
			}else if($(this).hasClass('pass')){			
				// tanpa validasi
			}else if($(this).attr('class')=='isi' && filterStr(val)==false){
				$(this).css(harus_isi);
				message +="- Field "+$(this).attr('label')+" berisi karakter ilegal! \n";		
				cekerr++
			}else if($(this).hasClass('email') && val!="" && cekEmail(val)==false){
				$(this).css(harus_isi);		
				message +="- Field "+$(this).attr('label')+" tidak valid! \n";	
				cekerr++;		
			}else if($(this).hasClass('number') && val!="" && (intRegex.test(val)==false || val.indexOf('.')>-1) ){
				$(this).css(harus_isi);						
				message +="- Field "+$(this).attr('label')+" harus diisi dengan angka! \n";
				cekerr++;
			}else if($(this).hasClass('exist')){
				$(this).css(harus_isi);						
				message +="- Field "+$(this).attr('label')+" sudah tersedia! \n";
				cekerr++;
			}else if($(this).hasClass('date') && val!="" && isDate(val)==false){
				$(this).css(harus_isi);		
				message +="- Field "+$(this).attr('label')+" tidak valid! \n";			
				cekerr++;
			}else if(($(this).hasClass('money') || $(this).hasClass('phone')) && val!="" && cekPhone(val)==false){
				$(this).css(harus_isi);		
				message +="- Field "+$(this).attr('label')+" harus angka! \n";			
				cekerr++;
			}else if(($(this).hasClass('tujuh')) && val<7){
				$(this).css(harus_isi);		
				message +="- Field "+$(this).attr('label')+" minimal 7! \n";			
				cekerr++;
			}else if(($(this).hasClass('enam')) && val<6){
				$(this).css(harus_isi);		
				message +="- Field "+$(this).attr('label')+" minimal 6! \n";			
				cekerr++;
			}else if(($(this).hasClass('dua')) && val<2){
				$(this).css(harus_isi);		
				message +="- Field "+$(this).attr('label')+" minimal 2! \n";			
				cekerr++;
			}else if(($(this).hasClass('sebulan')) && val>30){
				$(this).css(harus_isi);		
				message +="- Field "+$(this).attr('label')+" maksimal 30! \n";			
				cekerr++;
			}else if(($(this).hasClass('tiga')) && val>3){
				$(this).css(harus_isi);		
				message +="- Field "+$(this).attr('label')+" maksimal 3! \n";			
				cekerr++;
			}else{
				if(val!="" && $(this).attr('class')=="" && filterStr(val)==false){
					$(this).css(harus_isi);		
					message +="- Field "+$(this).attr('label')+" berisi karakter ilegal! \n";			
					cekerr++;
				}else{
					$(this).css(normal);
				}
			}	
			
			if($.trim($(this).attr('fix'))!="" && $(this).attr('fix')!=val.length && cekerr==0 && message==""){
				$(this).css(harus_isi);		
				message +="- Field "+$(this).attr('label')+" harus berisi "+$(this).attr('fix')+" karakter! \n";			
			}	
			if(err==0 && cekerr==1){
				$(this).focus();
			}
			err +=cekerr;	
		}else{
			$(this).css(normal);
		}
	})		
	return Array(err,message);	
}

function cekSetPass(idform){
	
	var message = '';
	var err=0;
	
	var result = validatePass(idform);
	err = result[0];
	message = result[1];
		
	if(message.length > 0){
		jAlert("Pengisian data berikut belum benar :\n" + message); 
		return false;
	}else{
		if(err==0){
			$('form#'+idform).submit();
		}		
	}	
	
}

//------------------ CEK VALIDASI BUAT SSP ENTRY
function cekSSP(idform){
	
	var message = '';
	var err=0;
	
	var result = validateForm(idform);
	err = result[0];
	message = result[1];
	if(message==""){
		var ErrTgl=0;
		var tgl =document.frmSSP.tglbyr.value;
		var arrtgl = tgl.split("-");
		var now = new Date();
		var t = now.getDate();
		var b = now.getMonth();
		var thn = now.getFullYear();	
		b++;
		ErrTgl +=(arrtgl[2]<thn)?1:0;
		ErrTgl +=(arrtgl[1]<b)?1:0;
		ErrTgl +=(arrtgl[2]==thn && arrtgl[1]==b && arrtgl[0]<t )?1:0;		
		
		if(ErrTgl>0){
			message = message + "- Tanggal yang diisi lebih kecil dari tanggal sekarang! \n";				
		} 
		var txamt = $('#txamt').val(); txmat = txamt.replace("."); txmat = txamt.replace("."); txmat = txamt.replace("."); txmat = txamt.replace("."); txmat = txamt.replace(".");
		if(txamt==0){
			message = message + "- Total Payment tidak boleh bernilai nol! \n";				
		}	
	}
			
	if(message.length > 0){
		jAlert("Pengisian data berikut belum benar :\n" + message); 
		return false;
	}else{
		if(err==0){
			$('form#'+idform).submit();
		}		
	}	
	
}

function set_datetimeclock(id) {
	var sekarang = new Date();
	var tanggal = sekarang.getDate();
	var hari = sekarang.getDay();
	if (hari == 0) hari = 'Sunday'; 
	if (hari == 1) hari = 'Senin';
	if (hari == 2) hari = 'Selasa';
	if (hari == 3) hari = 'Rabu';
	if (hari == 4) hari = 'Kamis';
	if (hari == 5) hari = 'Jumat';
	if (hari == 6) hari = 'Sabtu';
	var bulan = sekarang.getMonth();
	if (bulan == 0) bulan = 'January'; if (bulan == 1) bulan = 'February';
	if (bulan == 2) bulan = 'March'; if (bulan == 3) bulan = 'April';
	if (bulan == 4) bulan = 'May'; if (bulan == 5) bulan = 'June';
	if (bulan == 6) bulan = 'July'; if (bulan == 7) bulan = 'August';
	if (bulan == 8) bulan = 'September'; if (bulan == 9) bulan = 'October';
	if (bulan == 10) bulan = 'November'; if (bulan == 11) bulan = 'December';
	var tahun = sekarang.getFullYear();
	var detik = sekarang.getSeconds();
	if (detik < 10) detik = '0' + detik;
	var menit = sekarang.getMinutes();
	if (menit < 10) menit = '0' + menit;
	var jam = sekarang.getHours();
	if (jam < 10) jam = '0' + jam;
	if(tanggal > 3){
		var hh = 'th';
	}else if(tanggal == 1){
		var hh = 'st';
	}else if(tanggal == 2){
		var hh = 'nd';
	}else if(tanggal == 3){
		var hh = 'rd';
	}
	var showdate = '<b>' + tanggal + ' ' + bulan + ' ' + tahun + ' ' + jam + ':' + menit + ':' + detik +'</b>';
	document.getElementById(id).innerHTML = showdate;
	setTimeout('set_datetimeclock(\''+id+'\')', 1000);
}

function browser(){
	var nVer = navigator.appVersion;
	var nAgt = navigator.userAgent;
	var browserName  = navigator.appName;
	var fullVersion  = ''+parseFloat(navigator.appVersion); 
	var majorVersion = parseInt(navigator.appVersion,10);
	var nameOffset,verOffset,ix;
	
	// In MSIE, the true version is after "MSIE" in userAgent
	if ((verOffset=nAgt.indexOf("MSIE"))!=-1) {
	 browserName = "Microsoft Internet Explorer";
	 fullVersion = nAgt.substring(verOffset+5);
	}
	// In Opera, the true version is after "Opera" 
	else if ((verOffset=nAgt.indexOf("Opera"))!=-1) {
	 browserName = "Opera";
	 fullVersion = nAgt.substring(verOffset+6);
	}
	// In Chrome, the true version is after "Chrome" 
	else if ((verOffset=nAgt.indexOf("Chrome"))!=-1) {
	 browserName = "Chrome";
	 fullVersion = nAgt.substring(verOffset+7);
	}
	// In Safari, the true version is after "Safari" 
	else if ((verOffset=nAgt.indexOf("Safari"))!=-1) {
	 browserName = "Safari";
	 fullVersion = nAgt.substring(verOffset+7);
	}
	// In Firefox, the true version is after "Firefox" 
	else if ((verOffset=nAgt.indexOf("Firefox"))!=-1) {
	 browserName = "Firefox";
	 fullVersion = nAgt.substring(verOffset+8);
	}
	// In most other browsers, "name/version" is at the end of userAgent 
	else if ( (nameOffset=nAgt.lastIndexOf(' ')+1) < (verOffset=nAgt.lastIndexOf('/')) ) 
	{
	 browserName = nAgt.substring(nameOffset,verOffset);
	 fullVersion = nAgt.substring(verOffset+1);
	 if (browserName.toLowerCase()==browserName.toUpperCase()) {
	  browserName = navigator.appName;
	 }
	}
	// trim the fullVersion string at semicolon/space if present
	if ((ix=fullVersion.indexOf(";"))!=-1) fullVersion=fullVersion.substring(0,ix);
	if ((ix=fullVersion.indexOf(" "))!=-1) fullVersion=fullVersion.substring(0,ix);
	
	majorVersion = parseInt(''+fullVersion,10);
	if (isNaN(majorVersion)) {
	 fullVersion  = ''+parseFloat(navigator.appVersion); 
	 majorVersion = parseInt(navigator.appVersion,10);
	}
			
	return browserName;
}

function browser_ver(){
	var nVer = navigator.appVersion;
	var nAgt = navigator.userAgent;
	var browserName  = navigator.appName;
	var fullVersion  = ''+parseFloat(navigator.appVersion); 
	var majorVersion = parseInt(navigator.appVersion,10);
	var nameOffset,verOffset,ix;
	
	// In MSIE, the true version is after "MSIE" in userAgent
	if ((verOffset=nAgt.indexOf("MSIE"))!=-1) {
	 browserName = "Microsoft Internet Explorer";
	 fullVersion = nAgt.substring(verOffset+5);
	}
	// In Opera, the true version is after "Opera" 
	else if ((verOffset=nAgt.indexOf("Opera"))!=-1) {
	 browserName = "Opera";
	 fullVersion = nAgt.substring(verOffset+6);
	}
	// In Chrome, the true version is after "Chrome" 
	else if ((verOffset=nAgt.indexOf("Chrome"))!=-1) {
	 browserName = "Chrome";
	 fullVersion = nAgt.substring(verOffset+7);
	}
	// In Safari, the true version is after "Safari" 
	else if ((verOffset=nAgt.indexOf("Safari"))!=-1) {
	 browserName = "Safari";
	 fullVersion = nAgt.substring(verOffset+7);
	}
	// In Firefox, the true version is after "Firefox" 
	else if ((verOffset=nAgt.indexOf("Firefox"))!=-1) {
	 browserName = "Firefox";
	 fullVersion = nAgt.substring(verOffset+8);
	}
	// In most other browsers, "name/version" is at the end of userAgent 
	else if ( (nameOffset=nAgt.lastIndexOf(' ')+1) < (verOffset=nAgt.lastIndexOf('/')) ) 
	{
	 browserName = nAgt.substring(nameOffset,verOffset);
	 fullVersion = nAgt.substring(verOffset+1);
	 if (browserName.toLowerCase()==browserName.toUpperCase()) {
	  browserName = navigator.appName;
	 }
	}
	// trim the fullVersion string at semicolon/space if present
	if ((ix=fullVersion.indexOf(";"))!=-1) fullVersion=fullVersion.substring(0,ix);
	if ((ix=fullVersion.indexOf(" "))!=-1) fullVersion=fullVersion.substring(0,ix);
	
	majorVersion = parseInt(''+fullVersion,10);
	if (isNaN(majorVersion)) {
	 fullVersion  = ''+parseFloat(navigator.appVersion); 
	 majorVersion = parseInt(navigator.appVersion,10);
	}
			
	return majorVersion;
	
}

function cekTaxPayer()
{
	var npwp = $("#npwp").val();
	$.ajax({
	   type:"POST",			  
	   url: base_url+"cektax.php",
	   data:"div=npwp&npwp="+npwp,
	   success:function(msg){ 
			res = msg;					
	   }		   
	});
}
function cekDataPEB(npwp)
{
var npwp = $("#npwp").val();

	$.ajax({
	   type:"POST",			  
	   url: base_url+"cekTax.php",
	   data:"div=npwp_peb&npwp="+npwp,
	   success:function(msg){ 
	   hasil = jQuery.parseJSON(msg);
		$("#eksportir").val(hasil.NAMA);
		$("#alamat").val(hasil.ADDRESS);				
	   }		   
	});
}

var pilCabang;
$(document).ready(function(){
	pilCabang = $("#pilCabang").attr('value');
	//ubah();
	$('select').click(function(){
		$(this).each(function(){
			$(this).removeAttr('fase');	
		})	
	})	
	$('#gbrClndr').click(function(){
		$('select').each(function(){
			$(this).attr('fase','1');	
		})	
	})			
});
userTersedia=0;
function cekUserIdAsAdmin(){
	if($('table#cekadmin').size()<1){ return false;}	
	if(($('#uid').attr('ket')==null || $('#uid').attr('ket')=="") && $('#uid').val()!="" ){
		uid = $('#uid').val();
		datanya='';
		if($('#id').size()>0){			
			id = $('#id').val();		
			if(uid!=null && id!=null){
				datanya = 'userid='+uid+'&id='+id; 
			}
		}else{
			datanya ='userid='+uid;
		}
		$.ajax({
			type :'POST',
			url  :base_url+'cekComProf.php?modul=userid',
			data : datanya,
			success : function(msg){
				hasil = jQuery.parseJSON(msg);
				if(hasil.jumlah>0){
					$("#warningUserid").hide()
					$("#warningUserid").html(' <b style=color:#900;>*User ID already exists!</b>').fadeIn(1000);
					userTersedia=1;
				}else if(hasil.user_set > uid.length){
					$("#warningUserid").hide()
					$("#warningUserid").html(' <b style=color:#900;>*Minimum user id length '+hasil.user_set+' ! </b>').fadeIn(1000);
					userTersedia=1;
				}else{
					$("#warningUserid").hide()
					$("#warningUserid").html(' <b style=color:#009966;>*User ID accepted</b>').fadeIn(1000);
					userTersedia=0;
				}
			}
		});	
	}	
}

function cekBacaUpload(idform){
	message = '';
	err=0;
	
	result = validateForm(idform);
	err = result[0];
	message = result[1];
		
	if(message.length > 0){
		jAlert("Pengisian data berikut belum benar :\n" + message); 
		return false;
	}else{
		if(err==0){
			$('form#'+idform).submit();
		}		
	}
}
function cekFormEmail(idform)
{
	message = '';
	err=0;
	
	result = validateForm(idform);
	err = result[0];
	message = result[1];
		
	if(message.length > 0){
		jAlert("Pengisian data berikut belum benar :\n" + message); 
		return false;
	}else{
		if(err==0){
			$('form#'+idform).submit();
		}		
	}
}

function cekFormKdmap(idform)
{
	message = '';
	err=0;
	
	result = validateForm(idform);
	err = result[0];
	message = result[1];
		
	if(message.length > 0){
		jAlert("Pengisian data berikut belum benar :\n" + message); 
		return false;
	}else{
		if(err==0){
			$('form#'+idform).submit();
		}		
	}
}

function cekFormCabang(idform)
{
	message = '';
	err=0;
	
	result = validateForm(idform);
	err = result[0];
	message = result[1];
		
	if(message.length > 0){
		jAlert("Pengisian data berikut belum benar :\n" + message); 
		return false;
	}else{
		if(err==0){
			$('form#'+idform).submit();
		}		
	}
}

function cekFormRekening(idform)
{
	message = '';
	err=0;
	
	result = validateForm(idform);
	err = result[0];
	message = result[1];
		
	if(message.length > 0){
		jAlert("Pengisian data berikut belum benar :\n" + message); 
		return false;
	}else{
		if(err==0){
			$('form#'+idform).submit();
		}		
	}
}

function cekPassword(idform)
{
	message = '';
	err=0;
	
	result = validateForm(idform);
	err = result[0];
	message = result[1];
	
	if(message==""){
		pass1 = $("#pwdbaru1").val();		
		pass2 = $("#pwdbaru2").val();	
		
		if(pass1 && pass2){
			if(pass1 != pass2){
				$("#pwdbaru1").css(harus_isi);
				$("#pwdbaru2").css(harus_isi);				
				message += "- Password Not Match\n";
			}
		}
	}
	if(message.length > 0){
		jAlert("Pengisian data berikut belum benar :\n" + message); 
		return false;
	}else{
		if(err==0){
			$('form#'+idform).submit();
		}		
	}
	
}

function cekUpload()
{
	if(!$("#userfile").val())jAlert("File belum anda dipilih!");
	else $("#frmUpload").submit();	
}

function cekPrivilege(check,type,jenis)
{
	// type = 1 : edit, 0 : insert
	// jenis = 1 : admin super : 0 : admin nasabah
	var only = {
				'background-color' : '#E0E0E0',
				'border' : '1px #999 solid',
				'padding' : '2px'
			  }

	val = check.value;		
	$("input:checkbox").attr('checked',false);
	$('#noRekRTE').hide();
	$('#groupAccount').hide();	
	if(val!=3){
		$("#cekssp").attr('disabled',true);
		$("#ceksspcp").attr('disabled',true);
		$("#upload").attr('disabled',true);
		$("#cekrte").attr('disabled',true);
		
		$("#file_mandiri").attr('disabled',true);
		$("#file_citi").attr('disabled',true);
		$("#file_niaga").attr('disabled',true);
		
		$("span#cekbox").css('color','#CCC');	
		if(val==5){
			$('#noRekRTE').show();		
			$('#groupAccount').show();	
			$('#listRekening').show();			
		}else{
			$('#noRekRTE').hide();
			$('#groupAccount').hide();		
			//$('#noRek').val('');
			$('#listRekening').hide();		
		}
	}else{
		$("input:checkbox").attr('disabled',false);	
		$("#file_mandiri").attr('checked','checked');
		$("span#cekbox").css('color','#000');			
	}
	if(type==0 && jenis==1){
		$('input#noRek').val('');
		if(val!=3){
			$('#id').val('').removeAttr('style');											
			$('tbody#detailCompanyProfile input:text').each(function(){
				$(this).attr('readonly',true).css(only).val('')									
			});	
			$("#branch").html('<option>-- PILIH CABANG --</option>').css(only);
			$('#grouping0').removeAttr('checked').attr('disabled','disabled');
			$('#grouping1').removeAttr('checked').attr('disabled','disabled');
		}else{
			$('#id').val('').removeAttr('style');				
			//$("#branch").html(pilCabang).removeAttr('style');							
			$('tbody#detailCompanyProfile input:text').each(function(){
				$(this).removeAttr('readonly',true).removeAttr('style').val('')									
			});		
			$('#grouping0').removeAttr('disabled').attr('checked','checked');
			$('#grouping1').removeAttr('checked').removeAttr('disabled');
			
		}
	}else{
		$('#listRekening').hide();		
	}
}

function checkboxUpload(a)
{
	if(a.checked==true) $("#cekupload").css('display','inline');	
	else $("#cekupload").css('display','none');	
}

function getComProf(a,type) //user for form_admin line 521 and form_ssp line
{
	str = a.value;	
	$.ajax({
		type :'POST',
		url  :base_url+'cekComProf.php?modul=npwp',
		data : 'npwp='+str,
		success : function(msg){
			hasil = jQuery.parseJSON(msg)			
			if(hasil.group != null){
				if(type == 1) $("#id").val(hasil.group); 
				pilihCabang = '<option value='+hasil.kodeCabang+'>'+hasil.kodeCabang+' - '+hasil.cabang+'</option>';
				
				 var only = {
					'background-color' : '#E0E0E0',
					'border' : '1px #999 solid',
					'padding' : '2px'
				  }
				
				$("#branch").html(pilihCabang); 
				$("#wpnama").val(hasil.nama); 
				$("#wpalamat").val(hasil.alamat);
				$("#wpkota").val(hasil.kota); 
				$("#wpzipcode").val(hasil.kodePos); 
				$("#pic").val(hasil.pic); 
				$("#picEmail").val(hasil.picEmail);
				$("#picPhone").val(hasil.picPhone); 
				$("#picFax").val(hasil.picFax);
				$("#noRek").val(hasil.noRek);
				$("#CSMEmail").val(hasil.CSMEmail);				
				
				if(type == 1) $("#id").attr('readonly',true).css(only)
				$("#wpnama").attr('readonly',true).css(only)
				$("#wpalamat").attr('readonly',true).css(only)
				$("#wpkota").attr('readonly',true).css(only)
				$("#wpzipcode").attr('readonly',true).css(only) // untuk  me-readonly setelah memilih data di popup agar tidak bisa di edit*/
				$("#pic").attr('readonly',true).css(only) 
				$("#picEmail").attr('readonly',true).css(only)
				$("#picPhone").attr('readonly',true).css(only)
				$("#picFax").attr('readonly',true).css(only)
				$("#branch").attr('readonly',true).css(only)
				$("#noRek").attr('readonly',true).css(only)
				$("#CSMEmail").attr('readonly',true).css(only)
				
				if(hasil.group_account==1){
					$('#grouping0').removeAttr('checked').attr('disabled','disabled');
					$('#grouping1').val(1).attr('checked','checked').removeAttr('disabled');
				}else{
					$('#grouping0').val(1).attr('checked','checked').removeAttr('disabled');
					$('#grouping1').removeAttr('checked').attr('disabled','disabled');
				}	
				
			}else{
				if(type == 1) $("#id").removeAttr("readonly").removeAttr('style'); 				
				$("#wpnama").removeAttr("readonly").removeAttr('style'); 
				$("#wpalamat").removeAttr("readonly").removeAttr('style'); 
				$("#wpkota").removeAttr("readonly").removeAttr('style'); 
				$("#wpzipcode").removeAttr("readonly").removeAttr('style'); 
				$("#pic").removeAttr("readonly").removeAttr('style'); 
				$("#picEmail").removeAttr("readonly").removeAttr('style'); 
				$("#picPhone").removeAttr("readonly").removeAttr('style'); 
				$("#picFax").removeAttr("readonly").removeAttr('style'); 
				$("#option").removeAttr('disabled').removeAttr('style')
				$("#branch").removeAttr('style');
				
				$("#noRek").removeAttr("readonly").removeAttr('style'); 
				$("#CSMEmail").removeAttr("readonly").removeAttr('style');
				$('#grouping0').removeAttr('disabled');
				$('#grouping1').removeAttr('disabled');
			}
		} 
	});	
}
function getTaxPay(a){
	str = a.value;	
	$.ajax({
		type :'POST',
		url  : base_url+'cekComProf.php?modul=taxpay',
		data : 'npwp='+str,
		success : function(msg){
			hasil = jQuery.parseJSON(msg)			
			var only = {
					'background-color' : '#E0E0E0',
					'border' : '1px #999 solid',
					'padding' : '2px'
				  }
			
			if(hasil.nama != null){
				$("#nama").val(hasil.nama); 
				$("#address").val(hasil.alamat);
				$("#city").val(hasil.kota); 
				$("#zipcode").val(hasil.kodePos); 
				
			}else{
				
				$("#nama").val('');
				$("#address").val('');
				$("#city").val('');
				$("#zipcode").val('');
			}
		} 
	});	
}

function getDepositor(a){
	str = a.value;	
	$.ajax({
		type :'POST',
		url  :base_url+'cekComProf.php?modul=frmdp',
		data : 'npwp='+str,
		success : function(msg){
			hasil = jQuery.parseJSON(msg)			
			var only = {
					'background-color' : '#E0E0E0',
					'border' : '1px #999 solid',
					'padding' : '2px'
				  }
			if(hasil.nama != null){
				$("#paynama").val(hasil.nama); 
				$("#payaccount").val(hasil.account);				
			}else{
				$("#paynama").val(''); 
				$("#payaccount").val('');
			}
		} 
	});	
}

String.prototype.reverse = function() {
	var s = "";
	var i = this.length;
	while (i>0) {
			s += this.substring(i-1,i);
			i--;
	}
	return s;
}
function numberFormat(obj,separator,frontsymbol,endsymbol){
  obj.value=obj.value.replace(frontsymbol,"");
  obj.value=obj.value.replace(endsymbol,"");
  var i;
  for (i=0;i<obj.value.length;i++){
	  if (obj.value.charAt(i)==separator){
		  obj.value=obj.value.replace(separator,"");
	  }
  }
	  
  //end
  var strvalue=parseFloat(obj.value);
  if (isNaN(strvalue)){strvalue=0;}
  var s=new String(strvalue);
  var p="";
  var j=0;
  for (i=s.length-1;i>=0;i-=1){
	  p+=s.substr(i,1);
	  j++
	  if (j>2){
		  p+=separator;
		  j=0;
	  }
  }
  p=p.reverse();
  if (p.substr(0,1)==separator){
	  p=p.substr(1,p.length-1);
  }
  obj.value=frontsymbol+p+endsymbol;
}

function intOnly(i) 
{
	if(i.value.length>0){
		i.value = i.value.replace(/[^\d]+/g, ''); 
	}
}

function numberFormatKoma(obj,separator,frontsymbol,endsymbol){ // PAKE KOMA
  var nilai = obj.value;
  if(nilai.indexOf(".")>-1){ 
	var arrnilai = obj.value.split(".");
	if(arrnilai[1].length<=2){
		return false;
	}else{		
		obj.value= frontsymbol+nilai.substring(0,(nilai.length-1))+endsymbol;
		return false;
	}
  }
  obj.value=obj.value.replace(frontsymbol,"");
  obj.value=obj.value.replace(endsymbol,"");
  var i;
  for (i=0;i<obj.value.length;i++){
	  if (obj.value.charAt(i)==separator){
		  obj.value=obj.value.replace(separator,"");
	  }
  }	  
  //end
  var strvalue=parseFloat(obj.value);
  if (isNaN(strvalue)){strvalue=0;}
  var s=new String(strvalue);
  var p="";
  var j=0;
  for (i=s.length-1;i>=0;i-=1){
	  p+=s.substr(i,1);
	  j++
	  if (j>2){
		  p+=separator;
		  j=0;
	  }
  }
  p=p.reverse();
  if (p.substr(0,1)==separator){
	  p=p.substr(1,p.length-1);
  }
  obj.value=frontsymbol+p+endsymbol;
}
function cekReceptionParam(){
	var kdmap = $('#kdmap_d').val();
	if(!kdmap){
		msg = "- Parameter Download XLS Harus Diisi!";
		jAlert("Perbaiki kesalahan berikut:\n"+msg);
	}else{	
		$("#formDownRec").submit();
	}
}
function money_format(numbers)
{
	var arrnumber = new Array();
	if(numbers.indexOf('.')!=-1){
		arrnumber = numbers.split('.');
		number = arrnumber[0];
	}else{
		number = numbers;	
	}
	
	if (isNaN(number)) return "";
	var str = new String(number);
	var result = "" ,len = str.length;           
	for(var i=len-1;i>=0;i--)
	{           
	   if ((i+1)%3 == 0 && i+1!= len) result += ",";
		result += str.charAt(len-1-i);
	}       
	return (numbers.indexOf('.')!=-1)? result+'.'+arrnumber[1] : result;
}

function viaEmail(id,objek,modul){
	isi = ($(objek).attr('checked')==true)? 1 :0;
	$.ajax({
		type:"POST",
		data:"fileupload="+isi+"&id="+id+"&modul="+modul,
		url: base_url+"viaemail.php"		
	})
	$('font#link'+$(objek).attr('noid')).html(0)
}

function inquiry(cek){	
	npwp = $('#npwp').val();	    	
	if(npwp.length==15 && Number (npwp)){
		$("#warningNPWP").html(" <b style=color:#009966;> Harap tunggu..</b> &nbsp;<img src='"+base_url+"/img/loading.gif' style='border:none'>").fadeIn(1000);
		$.ajax({
			type : "POST",
			data : "npwp="+npwp+"&modul=ceknpwp&cek="+cek,
			url : base_url+"inquiry.php",
			success : function(msg){				
				hasil = jQuery.parseJSON(msg);
				if(hasil.KET=='1'){
					$('#nama').val(hasil.NAMA);
					$('#address').val(hasil.ALAMAT);
					$('#city').val(hasil.KOTA);
					$('#zipcode').val(hasil.KODEPOS).removeAttr('readonly').removeAttr('style');
					$('#account').removeAttr('readonly').removeAttr('style');
					$("#warningNPWP").hide()
				    $("#warningNPWP").html(' <b style=color:#009966;> '+hasil.RESPONSECODE+'</b>').fadeIn(1000);					
				}else if(hasil.KET=='0'){										
					$("#warningNPWP").hide()
				    $("#warningNPWP").html(' <b style=color:#900;> '+hasil.RESPONSECODE+'</b>').fadeIn(1000);
				}else{
					inquiry(2);	
				}			
			}
		})
	}else{
		jAlert("NPWP harus numeric dan berjumlah 15 digit!");	
	}
}
function addRowUangMuka(){
	var jumRow = $('#jumlahRow').attr('jum');
	var startId = $('#startId').attr('jum');
	startId++;
	jumRow++;
	var warna = (jumRow%2==0)? '#FFF': '#E5EEF5';
	var str = '<tr style="border-bottom: 1px solid #D7D7D7; background:'+warna+';"><td style="border-bottom: 1px solid #D7D7D7"><input type="checkbox" id="cbx'+startId+'" onclick=javascript:showText7(this,"'+startId+'") class="cbxBiasa"></td><td style="border-bottom: 1px solid #D7D7D7">'+jumRow+'.</td><td style="border-bottom: 1px solid #D7D7D7"><div align="LEFT" class="div_tbl" style="font-size:11px; font-family:Arial; padding:6px 0px 6px 9px;"><select name="jenisuang[]"><option value="1">Ekspor</option><option value="2">Non Ekspor</option><option value="3">Uang Muka</option></select></div></td><td style="border-bottom: 1px solid #D7D7D7"><div align="LEFT" class="div_tbl" style="font-size:11px; font-family:Arial; padding:6px 0px 6px 9px;"><input type="text" id="ajaxMod1TextJs'+startId+'" name="uangmuka[]" style="text-align:right" disabled="" onkeyup=javascript:numberFormat(this,",","","") class="sisabagi"></div></td><td style="border-bottom: 1px solid #D7D7D7;"><select name="statusUangMuka[]"><option value="">-</option><option value="1">Single PEB</option><option value="2">Multiple PEB</option></select></td><td style="border-bottom: 1px solid #D7D7D7;"><select name="jnsPembayaranUangMuka[]"><option value="">-</option><option value="1">Uang Muka Penuh</option><option value="2">Uang Muka Parsial</option></select></td></tr>';
	$('tbody#addRowArea').append(str)
	$('#jumlahRow').attr('jum',jumRow);
	$('#startId').attr('jum',startId);
} 

function delRowUangMuka(){
	var jumRow = $('#jumlahRow').attr('jum');
	if(jumRow>1){
		var startId = $('#startId').attr('jum');
		if($('tbody#addRowArea').size()>0){		
			$('tr').last().remove()									  
		}
		jumRow--;	
		startId--;
		$('#startId').attr('jum',startId);
		$('#jumlahRow').attr('jum',jumRow);
	}
}
function addRowSplit(){

	var jumRow = $('#jumlahRow').attr('jum');
	var startId = $('#startId').attr('jum');
	startId++;
	jumRow++;
	var warna = (jumRow%2==0)? '#FFF': '#E5EEF5';
	var str = '<tr style="border-bottom: 1px solid #D7D7D7; background:'+warna+';"><td style="border-bottom: 1px solid #D7D7D7"></td><td style="border-bottom: 1px solid #D7D7D7">'+jumRow+'.</td><td style="border-bottom: 1px solid #D7D7D7"><div align="LEFT" class="div_tbl" style="font-size:11px; font-family:Arial; padding:6px 0px 6px 9px;"><select name="jenisuang[]" idstt="'+startId+'" onchange="cekSTT(this,\''+startId+'\')"><option value="1">Ekspor</option><option value="2">Non Ekspor</option></select></div></td><td style="border-bottom: 1px solid #D7D7D7"><div align="LEFT" class="div_tbl" style="font-size:11px; font-family:Arial; padding:6px 0px 6px 9px;"><input type="text" id="ajaxMod1TextJs'+startId+'" name="uangmuka[]" style="text-align:right" onkeyup=javascript:numberFormatKoma(this,",","","") class="sisabagi"><td><input style="display:none" type="text" name="idstt[]" size="5" id="idstt'+startId+'" onclick="javascript:showSTT(\'idstt'+startId+'\')"/></td></div></td><td style="border-bottom: 1px solid #D7D7D7;"></td></tr>';
	$('#addRowArea').append(str);
	$('#jumlahRow').attr('jum',jumRow);
	$('#startId').attr('jum',startId);
} 
function delRowSplit(){
	var jumRow = $('#jumlahRow').attr('jum');
	if(jumRow>1){
		var startId = $('#startId').attr('jum');
		if($('tbody#addRowArea').size()>0){		
			$('tr').last().remove()									  
		}
		jumRow--;	
		startId--;
		$('#startId').attr('jum',startId);
		$('#jumlahRow').attr('jum',jumRow);
	}
}

function cekPEB(type,idform){
	var message = '';
	var err=0;
	var result = validateForm(idform);
	err = result[0];
	message = result[1];
		
	if(message.length > 0){
		jAlert("Pengisian data berikut belum benar :\n" + message); 
		return false;
	}else{
		car	= $('#car').val();	
		//alert(base_url+"cekTax.php");
		$.ajax({
			type : "POST",
			data : "div=car&aju="+car,
			url: base_url+"cekTax.php",
			success:function(total){				
				if(total==1 && type==1){					
					$('#car').css('border','1px #F00 solid');
					$('font#warningCAR').hide();
					$('font#warningCAR').html("<b style=color:#FF0000;> CAR sudah Tersedia </b>").fadeIn();
					return false;	
				}else{
					$('#car').removeAttr('style');
					$('font#warningCAR').hide();
					
					if(message!=""){
						jAlert("Pengisian data berikut belum benar :\n" + message); 		
						return false;
					}else{
						$('#frmPEB').submit();	
					}	
				}
	
			}
		});		
	}
	
	
}

var jumInv;
function addInvoice(){
	jumInv = $('#jumInvoice').val();
	jumInv++;
	var str = "<tr id='rowinv"+(jumInv)+"' ><td height='20' width='170' style='border-bottom: 1px solid #D7D7D7;'></td><td style='border-bottom: 1px solid #D7D7D7;'><span style='padding:6px 0px 6px 9px;'><input type='text' name='Inv[]' maxlength='30' size='30' class='invoice' /> <strong>Tgl : &nbsp;</strong><input type='text' name='tglInv_"+jumInv+"' readonly size='10' /><a href='javascript:void(0)' onClick='if(self.gfPop)gfPop.fPopCalendar(document.frmPEB.tglInv_"+jumInv+"); return false;'><img name='popcal' align='absmiddle' src='"+base_url+"img/calbtn.gif' alt='Pilih tanggal' width='34' height='22' border='0' id='gbrClndr'></a> <input type='button' value='Hapus' onclick='removeInvoice("+(jumInv)+")'></span></td></tr>";
	$('#areaInvoice').append(str);	
	$('#jumInvoice').val(jumInv);
}

function removeInvoice(id){	
	$('tr#rowinv'+id).remove();
	jumInv--;
	$('#jumInvoice').val(jumInv);
}

function cekGroupidAsAdmin(){	
	var groupid = $.trim($('#groupid').val());
	var only = {
				'background-color' : '#E0E0E0',
				'border' : '1px #999 solid',
				'padding' : '2px'
			  }
			  
	if($.trim(groupid)!="" ){		
		$.ajax({
			type :'POST',
			url  :base_url+'cekTax.php',
			data : 'div=groupid&groupid='+groupid,
			success : function(msg){				
				hasil = jQuery.parseJSON(msg);
				if(hasil.TOTAL>0){
					$("#warningGroupid").hide()
					$("#warningGroupid").html(' <b style=color:#009966;> * Group Id tesedia</b>').fadeIn(1000);					
					$('#groupname').val($.trim(hasil.NAMA));
					$('#groupname').removeAttr('readonly').removeAttr('style');
				}else{
					$("#warningGroupid").hide()
					$("#warningGroupid").html(' <b style=color:#009966;> * Group Id Baru</b>').fadeIn(1000);					
					$('#groupname').removeAttr('readonly').removeAttr('style');
				}
			}
		});	
	}	
}
function cekGroupid(type,idform){
	var message = '';
	var err=0;
	
	result = validateForm(idform);
	err = result[0];
	message = result[1];

	if(!$('#cbxSsp').attr('checked') && !$('#cbxSspcp').attr('checked') && !$('#cbxUpSsp').attr('checked')){
		message += "- Priviledge harus diisi!";
	}
	if(message.length > 0){
		jAlert("Pengisian data berikut belum benar :\n" + message); 
		return false;
	}else{
		groupid = $.trim($('#groupid').val());
		$.ajax({
			type :'POST',
			url  :base_url+'cekTax.php',
			data : 'div=groupid&groupid='+groupid,
			success : function(r){				
				hasil = jQuery.parseJSON(r);
				if(hasil.TOTAL>0 && type==1){
					$("#warningGroupid").hide()
					$("#warningGroupid").html(' <b style=color:#FF0000;> * Group Id sudah tesedia</b>').fadeIn(1000);										
					return false;
				}else{									
					if(message!=""){
						jAlert("Pengisian data berikut belum benar :\n" + message); 		
						return false;
					}else{
						$('#'+idform).submit();	
					}	
				}
			}
		});		
			
	}
}

function cekChecker(idform){
	message = '';
	err=0;
	
	result = validateForm(idform);
	err = result[0];
	message = result[1];

	if(!$('#cbxSsp').attr('checked') && !$('#cbxSspcp').attr('checked') && !$('#cbxUpSsp').attr('checked')){
		message += "- Priviledge harus diisi!";
	}
	if(message.length > 0){
		jAlert("Pengisian data berikut belum benar :\n" + message); 
		return false;
	}else{
		$('#'+idform).submit();				
	}
}

function panelManual(idForm){
	url = $('form#'+idForm+' select#panelSelect').val();
	cek = 0;
	id = "";
	idgroup = "";
	
	$('form#'+idForm+' input.radiotable').each(function(){
		if($(this).attr('checked')==true){
			cek++;
			id = $(this).attr('id');
			idgroup = $(this).attr('idgroup');
		}													  
	})
	
	if(cek==0){
		jAlert("Data Belum dipilih!");	
		return false;
	}else{		
		if(url == base_url+'modul/taxauthorize/manageoperatordept/'){
			url += id+'/'+calcMD5(id);	
		}
		$('form#'+idForm).attr('action',url);
		$('form#'+idForm).attr('method','post');
		
		if(url == base_url+'modul/taxauthorize/deletedepartement/'+idgroup+'/9ed'+idgroup+calcMD5('')){
			jConfirm('Apakah anda yakin untuk menghapus Departement ini?', 'Confirmation Dialog', function(r) {
				if(r == true){					
					$('form#'+idForm).submit();
				}
			})
			return false;
		}else if(url == base_url+'modul/taxauthorize/deleteoperatordept/'+id+'/'+calcMD5(id)){						
			jConfirm('Apakah anda yakin untuk membatalkan Checker ini?', 'Confirmation Dialog', function(r) {
				if(r == true){					
					$('form#'+idForm).submit();
				}
			})
			return false;
		}else if(url == base_url+'modul/taxauthorize/deletemaker/'+idgroup+'/9ed'+idgroup+calcMD5('')){	
			jConfirm('Apakah anda yakin untuk membatalkan Operator ini?', 'Confirmation Dialog', function(r) {
				if(r == true){					
					$('form#'+idForm).submit();
				}
			})
			return false;
		}else{			
			$('form#'+idForm).submit();
		}
	}
}

function cekDepartement(type,deptid_old,idform){
	
	message = '';
	err=0;
	
	result = validateForm(idform);
	err = result[0];
	message = result[1];
	
	if(message.length > 0){
		jAlert("Pengisian data berikut belum benar :\n" + message); 
		return false;
	}else{
		deptid = $.trim($('#deptid').val());		
		groupid = $.trim($('#groupid').val());
		
		$.ajax({
			type :'POST',
			url  :base_url+'cekTax.php',
			data : 'div=deptid&deptid='+deptid+'&deptid_old='+deptid_old+'&groupid='+groupid,
			success : function(r){				
				hasil = jQuery.parseJSON(r);
				if((hasil.TOTAL>0 && type==1) || (hasil.TOTAL>0 && type==0 && deptid!=deptid_old)){
					$("#warningDeptid").hide()
					$("#warningDeptid").html(' <b style=color:#FF0000;> * Dept Id sudah tesedia</b>').fadeIn(1000);										
					return false;
				}else{
								
					if(message!=""){
						jAlert("Pengisian data berikut belum benar :\n" + message); 		
						return false;
					}else{
						$('#'+idform).submit();				
					}	
				}
			}
		});	
		
	}
}

function cancelMakerOfChecker(maker,checker){	
	jConfirm('Apakah anda yakin untuk membatalkan Maker ini?', 'Confirmation Dialog', function(r) {
		if(r == true){					
			$.ajax({
				type :'POST',
				url  :base_url+'cekTax.php',
				data : 'div=cancelMaker&maker='+maker+'&checker='+checker,
				success : function(r){				
					window.location.href=window.location.href;
				}
			});	
		}
	})
	
}

function isReleaser(val){
	
	$('#listRekening').hide();				
	$('#account_group').attr('disabled','disabled');
	if(val==4 || val==5){
		$('tbody#grouparea').attr('aktif','0');
		$('tbody#grouparea').hide();
		if(val==5){ 
			$('#listRekening').show(); 
			$('#account_group').removeAttr('disabled');
		}				
	}else{
		$('tbody#grouparea').attr('aktif','1');
		$('tbody#grouparea').show();		
	}	
}

function cekRole(val){
	if($('#upload_ssp').size()>0){
		if(val=='1'){
			$('#upload_ssp').removeAttr('disabled');	
		}else{
			$('#upload_ssp').removeAttr('checked').attr('disabled','disabled');	
		}	
	}
}
//--------------------------- FORM SSPCP
function autoKPBC(a){
	kode = a.value;
	if(kode.length>4){
		$.ajax({
			data : "mod=kpbc&kode="+kode,
			type : "POST",
			url : base_url+"ajaxsspcp.php?wkwkwk",
			success : function(msg){
				msg = $.trim(msg);
				$('#namakpbc').val(msg);	
			} 	
		})	
	}
}

function getJPN(a){
	kode = a.value;
	$.ajax({
		data : "mod=jpn&kode="+kode,
		type : "POST",
		url : base_url+"ajaxsspcp.php?wkwkwk",
		success : function(msg){	
			msg = $.trim(msg);		
			$('select#jpn').html(msg);	
		} 	
	});	
	if(kode=='02'){
		$('#defaultPNBP').val('60,000');	
	}else{
		$('#defaultPNBP').val('100,000');	
	}
	getTotalSSPCP();	
}

function getNomor(a){
	kode = a.value;
	$.ajax({
		data : "mod=nomor&kode="+kode,
		type : "POST",
		url : base_url+"ajaxsspcp.php?wkwkwk",
		success : function(msg){
			msg = $.trim(msg);			
			if(msg=='car'){
				$('#car').show();
				$('#cicilan').hide();
				$('#spkbm').hide();
				$('#car input').each(function(){ $(this).removeAttr('disabled').addClass('isi number')})
				$('#cicilan input').each(function(){ $(this).attr('disabled','disabled').removeAttr('class')})
				$('#spkbm input').each(function(){ $(this).attr('disabled','disabled').removeAttr('class')})
			}else if(msg== 'cicilan'){
				$('#car').hide();
				$('#cicilan').show();
				$('#spkbm').hide();
				$('#car input').each(function(){ $(this).attr('disabled','disabled').removeAttr('class')})
				$('#cicilan input').each(function(){ $(this).removeAttr('disabled').addClass('isi number')})
				$('#spkbm input').each(function(){ $(this).attr('disabled','disabled').removeAttr('class')})
			}else if(msg == 'spkbm'){
				$('#car').hide();
				$('#cicilan').hide();
				$('#spkbm').show();	
				$('#car input').each(function(){ $(this).attr('disabled','disabled').removeAttr('class')})
				$('#cicilan input').each(function(){ $(this).attr('disabled','disabled').removeAttr('class')})
				$('#spkbm input').each(function(){ $(this).removeAttr('disabled').addClass('isi number')})
			}
			
			
		} 	
	})	
	
}

function getNPWP(a){
	kode = a.value;
	if(kode.length>13){
		$.ajax({
			data : "mod=npwp&kode="+kode,
			type : "POST",
			url : base_url+"ajaxsspcp.php?wkwkwk",
			success : function(msg){
				msg = $.trim(msg);				
				comp = jQuery.parseJSON(msg);				
				$('#nama').val(comp.nama);	
				$('#address').val(comp.address);	
				$('#city').val(comp.city);	
				$('#zipcode').val(comp.zipcode);	
				
			} 	
		})	
	}
}
function getTotalSSPCP(){
	total = 0;
	$('input.amount').each(function(){
		nilai = $(this).val().replace(",","");			
		total += eval(nilai);
	})
	total = money_format(total+'');
	$('input#total').val(total);
}

function validateForm(idform){
	var intRegex = /[0-9 -()+]+$/; 
	
	message = '';
	err=0;	
	var pertama;						
	$('form#'+idform+' input:text,form#'+idform+' input:password,form#'+idform+' input:file,form#'+idform+' textarea, form#'+idform+' select').each(function(){
		val = $.trim($(this).val());	
		cekerr = 0;						
		if($(this).attr('disabled')==false){
			if($(this).hasClass('isi') && val==""){			
				$(this).css(harus_isi);
				$(this).addClass('notvalid');
				$(this).attr('placeholder','harus diisi');				
				message +="- Field "+$(this).attr('label')+" harus diisi! \n";	
				cekerr++			
			}else if($(this).hasClass('pass')){			
				// tanpa validasi
			}else if($(this).attr('class')=='isi' && filterStr(val)==false){
				$(this).css(harus_isi);
				message +="- Field "+$(this).attr('label')+" berisi karakter ilegal! \n";		
				cekerr++
			}else if($(this).hasClass('email') && val!="" && cekEmail(val)==false){
				$(this).css(harus_isi);		
				message +="- Field "+$(this).attr('label')+" tidak valid! \n";	
				cekerr++;		
			}else if($(this).hasClass('number') && val!="" && (intRegex.test(val)==false || val.indexOf('.')>-1) ){
				$(this).css(harus_isi);						
				message +="- Field "+$(this).attr('label')+" harus diisi dengan angka! \n";
				cekerr++;
			}else if($(this).hasClass('exist')){
				$(this).css(harus_isi);						
				message +="- Field "+$(this).attr('label')+" sudah tersedia! \n";
				cekerr++;
			}else if($(this).hasClass('date') && val!="" && isDate(val)==false){
				$(this).css(harus_isi);		
				message +="- Field "+$(this).attr('label')+" tidak valid! \n";			
				cekerr++;
			}else if(($(this).hasClass('money') || $(this).hasClass('phone')) && val!="" && cekPhone(val)==false){
				$(this).css(harus_isi);		
				message +="- Field "+$(this).attr('label')+" harus angka! \n";			
				cekerr++;
			}else{
				if(val!="" && $(this).attr('class')=="" && filterStr(val)==false){
					$(this).css(harus_isi);		
					message +="- Field "+$(this).attr('label')+" berisi karakter ilegal! \n";			
					cekerr++;
				}else{
					$(this).css(normal);
				}
			}	
			
			if($.trim($(this).attr('fix'))!="" && $(this).attr('fix')!=val.length && cekerr==0 && message==""){
				$(this).css(harus_isi);		
				message +="- Field "+$(this).attr('label')+" harus berisi "+$(this).attr('fix')+" karakter! \n";			
			}	
			if(err==0 && cekerr==1){
				$(this).focus();
			}
			err +=cekerr;	
		}else{
			$(this).css(normal);
		}
	})		
	return Array(err,message);	
}

function cekSSPCP(idform){	
	message = '';
	err=0;
	
	result = validateForm(idform);
	err = result[0];
	message = result[1];
		
	if(message.length > 0){
		jAlert("Pengisian data berikut belum benar :\n" + message); 
		return false;
	}else{
		if(err==0){
			$('form#'+idform).submit();
		}		
	}
}

function cekFormDanaMasuk(idform){
	message = '';
	err=0;
	
	result = validateForm(idform);
	err = result[0];
	message = result[1];
		
	if(message.length > 0){
		jAlert("Pengisian data berikut belum benar :\n" + message); 
		return false;
	}else{
		if(err==0){
			$('form#'+idform).submit();
		}		
	}
}

function cekFormNPWP(idform){
	message = '';
	err=0;
	
	result = validateForm(idform);
	err = result[0];
	message = result[1];
		
	if(message.length > 0){
		jAlert("Pengisian data berikut belum benar :\n" + message); 
		return false;
	}else{
		if(err==0){
			$('form#'+idform).submit();
		}		
	}	
}
function cekActivation(idform){
	message = '';
	err=0;
	
	result = validateForm(idform);
	err = result[0];
	message = result[1];
		
	if(message.length > 0){
		jAlert("Pengisian data berikut belum benar :\n" + message); 
		return false;
	}else{
		if(err==0){
			$('form#'+idform).submit();
		}		
	}	
}

function cekValidation(idform){
	otp = prompt("Please input the Security Code : ");
	if($.trim(otp)!=''){
		$('span#loadingproses').html("<img scr='"+base_url+"img/loading.gif' style='border:none'>").fadeIn(1000);
		$.ajax({
			type : "POST",
			data : "otp="+otp,
			url : base_url+"security/validation",
			success:function(msg){
				hsl = jQuery.parseJSON(msg);
				jAlert("Message : "+hsl.status);
				if(hsl.kode!='0000'){
					return false;
				}
				$('#'+idform).submit();
			}
		});
	}
}

function cekSTT(a,b){
	var stt = $(a).attr('idstt');
	if(a.value=='2'){
		$('#idstt'+stt).show();
	}else{
		$('#idstt'+stt).hide();
	}
}