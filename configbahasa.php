<?php
session_start();
#TABLE
$bhs['Kategori'] 					= array("Kategori","Category");
$bhs['Cari'] 						= array("Cari","Search");
$bhs['Batal']						= array("Batal","Cancel");
$bhs['Proses']						= array("Proses","Process");
$bhs['Baca'] 						= array("Baca","Read");

#HOME
$bhs['Welcome'] 					= array("Selamat Datang","Welcome");
$bhs['Login Terakhir'] 				= array("Login terakhir pada : ","Your last login at : ");
$bhs['Alamat'] 						= array("Alamat","Address");
$bhs['Profil Perusahaan'] 			= array("Profil Perusahaan / <em>Wajib Pajak<em>","Company Profile / Tax Payer");
$bhs['Nama Perusahaan'] 			= array("Nama Perusahaan / <em>Wajib Pajak<em>","Company Name / Tax Payer");
$bhs['Kota'] 						= array("Kota","City");
$bhs['Kode Pos'] 					= array("Kode Pos","Postal Code");
$bhs['Ringkasan'] 					= array("Ringkasan","Summary");
$bhs['Ringkasan Transaksi'] 		= array("Ringkasan Transaksi","Transaction Summary");
$bhs['Ringkasan Dana Masuk'] 		= array("Ringkasan Dana Masuk","Summary of Incoming Funds ");
$bhs['Ringkasan PEB'] 				= array("Ringkasan PEB","Summary of PEB");
$bhs['Ringkasan RTE'] 				= array("Ringkasan RTE","Summary of RTE");
$bhs['Bahasa']		 				= array("Bahasa","Language");

#RTE
#==================================================================================
#DANA MASUK
#menu Dana Masuk
$bhs['Dana Masuk'] 					= array("Dana Masuk","Incoming Funds");
$bhs['Split'] 						= array("Split Dana","Split Funds");
$bhs['Dana Masuk Baru'] 			= array("Dana Masuk Baru","New Incoming Funds");
$bhs['Ekspor'] 						= array("Ekspor","Export");
$bhs['Non Ekspor'] 					= array("Non Ekspor","Non Export");
$bhs['Uang Muka']					= array("Uang Muka Penuh / Parsial","Partial / Advance Payment");
$bhs['Dana Terlaporkan'] 			= array("Dana Terlaporkan","Reported Funds");
#konten Dana Masuk Baru
$bhs['Dana>3'] 						= array("Dana masuk lebih dari 3 hari","Incoming Funds more than 3 Days");
$bhs['Dana<3'] 						= array("Dana masuk kurang dari 3 hari","Incoming Funds less than 3 Days");
$bhs['Nama Pengirim'] 				= array("Nama Pengirim","Sender's Name");
$bhs['Valuta Transfer'] 			= array("Valuta Transfer","Currency Transfer");
$bhs['Nominal Transfer'] 			= array("Nominal Transfer","Amount Transfer");
$bhs['Valuta Diterima'] 			= array("Valuta Diterima","Currency Received");
$bhs['Nominal Diterima'] 			= array("Nominal Diterima","Amount Received");
$bhs['Tanggal Transaksi'] 			= array("Tanggal Transaksi","Date of the Transaction");
$bhs['Tanggal Transaksi DHE'] 		= array("Tanggal Transaksi DHE","Date of the Transaction DHE");
$bhs['Nama Bank'] 					= array("Nama Bank Pengirim","Bank sender's Name");
$bhs['Berita'] 						= array("Keterangan","Information");
$bhs['Tipe Dana']					= array("Tipe Dana","Fund's Type");
$bhs['Status Dana']					= array("Status Dana","Fund's Status");
$bhs['No. Rek'] 					= array("No. Rekening","Account Number");
$bhs['No. Ref'] 					= array("No. Referensi","Reference Number");
$bhs['Pembayaran'] 					= array("Pembayaran","Payment");
$bhs['Tgl Pelaporan'] 				= array("Tanggal Pelaporan","Reporting Date");
#proses
$bhs['Pilih Proses'] 				= array("Pilih Proses","Select Process");
$bhs['Alokasi Ekspor'] 				= array("- Alokasikan sebagai Ekspor","- Allocate as an Export");
$bhs['Alokasi Non Ekspor'] 			= array("- Alokasikan sebagai Non Ekspor","- Allocate as an Non Export");
$bhs['Cetak Report'] 				= array("- Cetak Report","- Print Report");

#Menu Ekspor
#proses
$bhs['Pilih PEB'] 					= array("- Pilih PEB","- Select PEB");
$bhs['Alokasi Penuh'] 				= array("- Alokasi Uang Muka Penuh","- Allocate as Full Advance Payment");
$bhs['Alokasi Parsial'] 			= array("- Alokasi Uang Muka Parsial","- Allocate as Partial Advance Payment");
$bhs['Batal Ekspor'] 				= array("- Batalkan Alokasi Ekspor","- Cancel the Allocation of Export");
$bhs['Split Dana'] 					= array("- Split Dana","- Split Funds");
#Menu Non Ekspor
$bhs['Batal Non Ekspor'] 			= array("- Batalkan Alokasi Non Ekspor","- Cancel the Allocation of Non Export");
$bhs['Sandi Keterangan'] 			= array("Sandi Keterangan","Information Code");

#Menu Uang Muka

$bhs['Bentuk PEB'] 					= array("- Kirim PEB 90+","- Send PEB 90+");
$bhs['Batalkan Uang Muka'] 			= array("- Batalkan Alokasi Uang Muka","- Cancel the Allocation of Advance Payment");

$bhs['Jenis Uang Muka'] 			= array("Pilih Jenis Uang Muka","Select the type of Advance Payment");
$bhs['Single'] 						= array("- Satu PEB","- Single PEB");
$bhs['Multiple'] 					= array("- Banyak PEB","- Multiple PEB");
#===================================================================================
#PEB
#menu PEB
$bhs['PEB Baru'] 					= array("PEB Baru","New PEB");
$bhs['PEB Terlaporkan'] 			= array("PEB Terlaporkan","PEB Reported");

$bhs['PEB<70'] 						= array("PEB < 70 hari","PEB < 70 days");
$bhs['70<PEB<90'] 					= array("70 hari < PEB < 90 hari","70 days < PEB < 90 days");
$bhs['PEB>90'] 						= array("PEB > 90 hari","PEB > 90 days");

$bhs['Simpan Perubahan'] 			= array("- Simpan perubahan","- Save the Changes");
$bhs['Pilih Dana Masuk'] 			= array("- Pilih Dana Masuk","- Choose the Fund");
$bhs['Hapus PEB'] 					= array("- Hapus PEB","- Delete PEB");

$bhs['Bentuk RTE'] 					= array("- Bentuk RTE","- Create RTE");
$bhs['Upload Pemberitahuan']		= array("- Upload Dokumen Pendukung","- Upload Supporting Documents");
$bhs['Batalkan PEB90+'] 			= array("- Batalkan PEB 90+","- Cancel PEB 90+");


$bhs['Eksportir'] 					= array("Eksportir","Exporter");
$bhs['No. PEB'] 					= array("No. PEB","PEB Number");
$bhs['Tanggal PEB'] 				= array("Tanggal PEB","Date of PEB");
$bhs['Valuta'] 						= array("Valuta","Currency");
$bhs['Nilai PEB'] 					= array("Nilai PEB","PEB Amount");
$bhs['Status PEB'] 					= array("Status PEB","PEB Status");
$bhs['Dokumen'] 					= array("Dokumen","Documents");
$bhs['Upload Dokumen']				= array("Upload Dokumen","Upload Documents");
$bhs['Via Email'] 					= array("Lewat Email","Via Email");

$bhs['Input PEB'] 					= array("Input PEB Baru","Create New PEB");
$bhs['informasi']					= array("INFORMASI","INFORMATION");

#menu RTE
$bhs['RTE Baru'] 					= array("RTE Baru","New RTE");
$bhs['RTE Terkirim'] 				= array("RTE Terkirim","RTE Sent to Bank");
$bhs['RTE Uang Muka'] 				= array("RTE Uang Muka","RTE Advance/Payment");

$bhs['No. Identifikasi'] 			= array("No. Identifikasi","Identification Number");
$bhs['Nama Penerima DHE'] 			= array("Nama Penerima DHE","Beneficiary Name");
$bhs['Sandi Kantor Pabean'] 		= array("Sandi Kantor Pabean","Customs Office Code");
$bhs['Nilai DHE'] 					= array("Nilai DHE","DHE Amount");
$bhs['Kelengkapan Dokumen'] 		= array("Kelengkapan Dokumen","Completeness of Documents");

$bhs['Simpan Sandi Keterangan'] 	= array("- Simpan Sandi Keterangan","- Save Information Code");
$bhs['Kirim ke Bank'] 				= array("- Kirim ke Bank","- Sent to Bank");
$bhs['Batal RTE'] 					= array("- Batalkan RTE","- Cancel RTE");
$bhs['Batal Uangmuka'] 					= array("- Batalkan RTE Uang Muka","- Cancel RTE Advance/Payment");
#pilih PEB
$bhs['Simpan Pemilihan PEB'] 		= array("- Simpan Pemilihan PEB","- Save Selection PEB");
$bhs['notif1'] 						= array("gunakan separator (;) untuk muliti pencarian","use the separator (;) for muliti searching");
$bhs['Pilih Status Dana Masuk']			= array("Pilih Status Dana Masuk","Select Fund Status");
#pilih Danamasuk
$bhs['Simpan Pemilihan Dana Masuk'] = array("- Simpan Pemilihan Dana Masuk","- Save Selection Incoming Funds");
$bhs['Pilih Pending'] 				= array("Pilih Dana Masuk Pending","Select Incoming Pending Funds");
$bhs['Uang Muka Dilaporkan'] 		= array("Uang Muka Dilaporkan","Reported Advance Payment");
$bhs['Pilih Status PEB']			= array("Pilih Status PEB","Select PEB Status");
$bhs['Ket'] 						= array("Ket","Note");
$bhs['Terakhir'] 					= array("Terakhir","Last Choise");
#Upload
$bhs['note2'] 						= array("Nama File Harus <b>FT30H.TXT</b> atau <b>FT30DK.TXT</b>","File name must be <b>FT30H.TXT</b> or <b>FT30DK.TXT</b>");
$bhs['note3'] 						= array(
											"Tutorial untuk mendapatkan file <b>FT30H.TXT</b> dan <b>FT30DK.TXT</b>, silakan download dengan menekan tombol dibawah :",
											"For tutorial to get <b>FT30H.TXT</b> and <b>FT30DK.TXT</b> file, please download tutorial below :"
											);
$bhs['note4'] 						= array("Sumber Data : ","Data Source from file : ");
$bhs['note5'] 						= array("Jumlah Data Upload : ","Number of Data Upload : ");
$bhs['note6'] 						= array("Keterangan Hasil Baca Data","Notes of Reading Result");
$bhs['note7'] 						= array("Keterangan Hasil Upload","Notes of Uploading Result");
$bhs['note8'] 						= array("Jumlah Data Upload : ","Number of Upload Data : ");
$bhs['note9'] 						= array("Jumlah berhasil : ","Number of Successful Data : ");
$bhs['note10'] 						= array("Jumlah data gagal : ","Number of Failed Data : ");
$bhs['errpeb1']						= array("Pesan Error : NPWP yang diinput bukan NPWP Perusahaan ini!","Error message: NPWP inputted is not Company's NPWP!");
$bhs['errpeb2']						= array("Pesan Error : Nomor Aju/CAR, kode Dok dan Id  sudah tersedia sebelumnya sebanyak : ","Error message: Aju/CAR Number, Doc Code and Id  has been available before for amount : ");
$bhs['errpeb3']						= array("Pesan Error : Nomor Aju/CAR sudah tersedia sebelumnya sebanyak :","Error message: No Aju / CAR has been available before as much as :");
$bhs['errpeb4']						= array("Pesan Error : NPWP yang diinput bukan NPWP Perusahaan ini sebanyak : ","Error message: NPWP inputted is not Company's NPWP as much as :");
$bhs['errpeb5']						= array("Pesan Error : Nomor Aju/CAR PEB belum tersedia sebanyak : ","Error message: No Aju / CAR of PEB hasn't been available as much as : ");

#PESAN
$bhs['RTE Simpan']					= array("RTE Berhasil di Simpan","RTE has been created Successfully");
$bhs['RTE tanpa Dana Masuk Simpan']	= array("RTE tanpa Dana Masuk Berhasil di Simpan","RTE without incoming Funds has been created Successfully");
$bhs['Kirim PEB']	= array("PEB 90+ Berhasil Di kirim","PEB 90+ has been send Successfully");
$bhs['RTE Uang Muka Simpan']		= array("RTE Uang Muka Berhasil di Simpan","RTE Advance/Payment has been created Successfully");
$bhs['Dokumen Simpan']				= array("Dokumen Berhasil di Simpan","Documents has been created Successfully");

$bhs['NPWP sudah Terdaftar']		= array("NPWP yang anda isi sudah terdaftar sebelumnya!","NPWP has been exist!");
$bhs['Dokumen tidak diupload']		= array("Tidak ada dokumen yang diupload!","There is no Document uploaded!");

$bhs['Perubahan Near Matching']		= array("Perubahan Near Matching berhasil","Near Matching value has been updated");
$bhs['Perubahan Selisih']		= array("Perubahan Selisih berhasil","Difference value has been updated");
$bhs['Perubahan Tersimpan']			= array("Perubahan Tersimpan","Change has been saved");
$bhs['Tidak Ada Perubahan']			= array("Tidak Ada Perubahan Tersimpan","There's no item Changed");
$bhs['Alokasi PEB90+']				= array("Alokasi PEB 90+ Berhasil","PEB 90+'s alocation is successful");

$bhs['PEB Hapus']					= array("PEB berhasil dihapus","PEB deleted successfully");
$bhs['PEB90+ Batal']				= array("PEB 90+ berhasil dibatalkan","PEB 90+ canceled successfully");
$bhs['RTE Batal']					= array("RTE berhasil dibatalkan","RTE Advance/Payment canceled successfully");
$bhs['RTE Uang Muka Batal']			= array("RTE Uang Muka berhasil dibatalkan","RTE Advance/Payment canceled successfully");

$bhs['Tanggal Jatuhtempo']				= array("Tanggal Jatuh Tempo","Pay Date");
$bhs['Jenis Pembayaran']				= array("Jenis Pembayaran","Pay Type");
$bhs['status uangmuka']				= array("Status Uang Muka","Advance Payment Status");



$_SESSION['lang'] = $bhs;
?>