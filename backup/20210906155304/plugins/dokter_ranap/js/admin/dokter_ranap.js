// sembunyikan form dan notif
$("#form_rincian").hide();
$("#form_soap").hide();
$("#form_sep").hide();
$("#histori_pelayanan").hide();
$("#notif").hide();
$('#provider').hide();
$('#aturan_pakai').hide();
$('#daftar_racikan').hide();
$("#info_tambahan").hide();
$("#form_kontrol").hide();

$("#display").on("click",".riwayat_perawatan", function(event){
  var baseURL = mlite.url + '/' + mlite.admin;
  event.preventDefault();
  var no_rkm_medis = $(this).attr("data-no_rkm_medis");
  window.open(baseURL + '/pasien/riwayatperawatan/' + no_rkm_medis + '?t=' + mlite.token);
});

$('#manage').on('click', '#submit_periode_rawat_inap', function(event){
  var baseURL = mlite.url + '/' + mlite.admin;
  event.preventDefault();
  var url    = baseURL + '/dokter_ranap/display?t=' + mlite.token;
  var periode_rawat_inap  = $('input:text[name=periode_rawat_inap]').val();
  var periode_rawat_inap_akhir  = $('input:text[name=periode_rawat_inap_akhir]').val();
  var status_pulang = 'all';

  if(periode_rawat_inap == '') {
    alert('Tanggal awal masih kosong!')
  }
  if(periode_rawat_inap_akhir == '') {
    alert('Tanggal akhir masih kosong!')
  }

  $.post(url, {periode_rawat_inap: periode_rawat_inap, periode_rawat_inap_akhir: periode_rawat_inap_akhir, status_pulang: status_pulang} ,function(data) {
  // tampilkan data
    $("#form").show();
    $("#display").html(data).show();
    $("#form_rincian").hide();
    $("#form_soap").hide();
    $("#form_sep").hide();
    $("#notif").hide();
    $("#rincian").hide();
    $("#sep").hide();
    $("#soap").hide();
    $('.periode_rawat_inap').datetimepicker('remove');
  });

});

$('#manage').on('click', '#masuk_periode_rawat_inap', function(event){
  var baseURL = mlite.url + '/' + mlite.admin;
  event.preventDefault();
  var url    = baseURL + '/dokter_ranap/display?t=' + mlite.token;
  var periode_rawat_inap  = $('input:text[name=periode_rawat_inap]').val();
  var periode_rawat_inap_akhir  = $('input:text[name=periode_rawat_inap_akhir]').val();
  var status_pulang = 'masuk';

  if(periode_rawat_inap == '') {
    alert('Tanggal awal masih kosong!')
  }
  if(periode_rawat_inap_akhir == '') {
    alert('Tanggal akhir masih kosong!')
  }

  $.post(url, {periode_rawat_inap: periode_rawat_inap, periode_rawat_inap_akhir: periode_rawat_inap_akhir, status_pulang: status_pulang} ,function(data) {
  // tampilkan data
    $("#form").show();
    $("#display").html(data).show();
    $("#form_rincian").hide();
    $("#form_soap").hide();
    $("#form_sep").hide();
    $("#notif").hide();
    $("#rincian").hide();
    $("#sep").hide();
    $("#soap").hide();
    $('.periode_rawat_inap').datetimepicker('remove');
  });

});

$('#manage').on('click', '#pulang_periode_rawat_inap', function(event){
  var baseURL = mlite.url + '/' + mlite.admin;
  event.preventDefault();
  var url    = baseURL + '/dokter_ranap/display?t=' + mlite.token;
  var periode_rawat_inap  = $('input:text[name=periode_rawat_inap]').val();
  var periode_rawat_inap_akhir  = $('input:text[name=periode_rawat_inap_akhir]').val();
  var status_pulang = 'pulang';

  if(periode_rawat_inap == '') {
    alert('Tanggal awal masih kosong!')
  }
  if(periode_rawat_inap_akhir == '') {
    alert('Tanggal akhir masih kosong!')
  }

  $.post(url, {periode_rawat_inap: periode_rawat_inap, periode_rawat_inap_akhir: periode_rawat_inap_akhir, status_pulang: status_pulang} ,function(data) {
  // tampilkan data
    $("#form").show();
    $("#display").html(data).show();
    $("#form_rincian").hide();
    $("#form_soap").hide();
    $("#form_sep").hide();
    $("#notif").hide();
    $("#rincian").hide();
    $("#sep").hide();
    $("#soap").hide();
    $('.periode_rawat_inap').datetimepicker('remove');
  });

});

$('#manage').on('click', '#lunas_periode_rawat_inap', function(event){
  var baseURL = mlite.url + '/' + mlite.admin;
  event.preventDefault();
  var url    = baseURL + '/dokter_ranap/display?t=' + mlite.token;
  var periode_rawat_inap  = $('input:text[name=periode_rawat_inap]').val();
  var periode_rawat_inap_akhir  = $('input:text[name=periode_rawat_inap_akhir]').val();
  var status_periksa = 'lunas';

  if(periode_rawat_inap == '') {
    alert('Tanggal awal masih kosong!')
  }
  if(periode_rawat_inap_akhir == '') {
    alert('Tanggal akhir masih kosong!')
  }

  $.post(url, {periode_rawat_inap: periode_rawat_inap, periode_rawat_inap_akhir: periode_rawat_inap_akhir, status_periksa: status_periksa} ,function(data) {
  // tampilkan data
    $("#form").show();
    $("#display").html(data).show();
    $("#form_rincian").hide();
    $("#form_soap").hide();
    $("#form_sep").hide();
    $("#notif").hide();
    $("#rincian").hide();
    $("#sep").hide();
    $("#soap").hide();
    $('.periode_rawat_inap').datetimepicker('remove');
  });

});

// ketika tombol simpan diklik
$("#form_soap").on("click", "#simpan_soap", function(event){
  var baseURL = mlite.url + '/' + mlite.admin;
  event.preventDefault();

  var no_rawat        = $('input:text[name=no_rawat]').val();
  var tgl_perawatan   = $('input:text[name=tgl_perawatan]').val();
  var jam_rawat       = $('input:text[name=jam_rawat]').val();
  var suhu_tubuh      = $('input:text[name=suhu_tubuh]').val();
  var tensi           = $('input:text[name=tensi]').val();
  var nadi            = $('input:text[name=nadi]').val();
  var respirasi       = $('input:text[name=respirasi]').val();
  var tinggi          = $('input:text[name=tinggi]').val();
  var berat           = $('input:text[name=berat]').val();
  var gcs             = $('input:text[name=gcs]').val();
  var alergi          = $('input:text[name=alergi]').val();
  var alergi          = $('input:text[name=alergi]').val();
  var keluhan         = $('textarea[name=keluhan]').val();
  var pemeriksaan     = $('textarea[name=pemeriksaan]').val();
  var penilaian       = $('textarea[name=penilaian]').val();
  var rtl             = $('textarea[name=rtl]').val();

  var url = baseURL + '/dokter_ranap/savesoap?t=' + mlite.token;
  $.post(url, {no_rawat : no_rawat,
  tgl_perawatan: tgl_perawatan,
  jam_rawat: jam_rawat,
  suhu_tubuh : suhu_tubuh,
  tensi : tensi,
  nadi : nadi,
  respirasi : respirasi,
  tinggi : tinggi,
  berat : berat,
  gcs : gcs,
  alergi : alergi,
  keluhan : keluhan,
  pemeriksaan : pemeriksaan,
  penilaian : penilaian,
  rtl : rtl
  }, function(data) {
    // tampilkan data
    $("#display").hide();
    var url = baseURL + '/dokter_ranap/soap?t=' + mlite.token;
    $.post(url, {no_rawat : no_rawat,
    }, function(data) {
      // tampilkan data
      $("#soap").html(data).show();
    });
    $('input:text[name=suhu_tubuh]').val("");
    $('input:text[name=tensi]').val("");
    $('input:text[name=nadi]').val("");
    $('input:text[name=respirasi]').val("");
    $('input:text[name=tinggi]').val("");
    $('input:text[name=berat]').val("");
    $('input:text[name=gcs]').val("");
    $('input:text[name=kesadaran]').val("");
    $('input:text[name=alergi]').val("");
    $('input:text[name=imun_ke]').val("");
    $('textarea[name=keluhan]').val("");
    $('textarea[name=pemeriksaan]').val("");
    $('textarea[name=penilaian]').val("");
    $('textarea[name=rtl]').val("");
    $('input:text[name=tgl_perawatan]').val("{?=date('Y-m-d')?}");
    $('input:text[name=tgl_registrasi]').val("{?=date('Y-m-d')?}");
    $('input:text[name=jam_rawat]').val("{?=date('H:i:s')?}");
    $('#notif').html("<div class=\"alert alert-success alert-dismissible fade in\" role=\"alert\" style=\"border-radius:0px;margin-top:-15px;\">"+
    "Data soap telah disimpan!"+
    "<button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-label=\"Close\">&times;</button>"+
    "</div>").show();
  });
});

// ketika tombol hapus ditekan
$("#soap").on("click",".edit_soap", function(event){
  var baseURL = mlite.url + '/' + mlite.admin;
  event.preventDefault();
  var no_rawat        = $(this).attr("data-no_rawat");
  var tgl_perawatan   = $(this).attr("data-tgl_perawatan");
  var jam_rawat       = $(this).attr("data-jam_rawat");
  var suhu_tubuh      = $(this).attr("data-suhu_tubuh");
  var tensi           = $(this).attr("data-tensi");
  var nadi            = $(this).attr("data-nadi");
  var respirasi       = $(this).attr("data-respirasi");
  var tinggi          = $(this).attr("data-tinggi");
  var berat           = $(this).attr("data-berat");
  var gcs             = $(this).attr("data-gcs");
  var kesadaran       = $(this).attr("data-kesadaran");
  var alergi          = $(this).attr("data-alergi");
  var imun_ke         = $(this).attr("data-imun_ke");
  var keluhan         = $(this).attr("data-keluhan");
  var pemeriksaan     = $(this).attr("data-pemeriksaan");
  var penilaian       = $(this).attr("data-penilaian");
  var rtl             = $(this).attr("data-rtl");

  $('input:text[name=tgl_perawatan]').val(tgl_perawatan);
  $('input:text[name=jam_rawat]').val(jam_rawat);
  $('input:text[name=suhu_tubuh]').val(suhu_tubuh);
  $('input:text[name=tensi]').val(tensi);
  $('input:text[name=nadi]').val(nadi);
  $('input:text[name=respirasi]').val(respirasi);
  $('input:text[name=tinggi]').val(tinggi);
  $('input:text[name=berat]').val(berat);
  $('input:text[name=gcs]').val(gcs);
  $('input:text[name=kesadaran]').val(kesadaran);
  $('input:text[name=alergi]').val(alergi);
  $('input:text[name=imun_ke]').val(imun_ke);
  $('textarea[name=keluhan]').val(keluhan);
  $('textarea[name=pemeriksaan]').val(pemeriksaan);
  $('textarea[name=penilaian]').val(penilaian);
  $('textarea[name=rtl]').val(rtl);

});

// ketika tombol hapus ditekan
$("#soap").on("click",".hapus_soap", function(event){
  var baseURL = mlite.url + '/' + mlite.admin;
  event.preventDefault();
  var url = baseURL + '/dokter_ranap/hapussoap?t=' + mlite.token;
  var no_rawat = $(this).attr("data-no_rawat");
  var tgl_perawatan = $(this).attr("data-tgl_perawatan");
  var jam_rawat = $(this).attr("data-jam_rawat");

  // tampilkan dialog konfirmasi
  bootbox.confirm("Apakah Anda yakin ingin menghapus data ini?", function(result){
    // ketika ditekan tombol ok
    if (result){
      // mengirimkan perintah penghapusan
      $.post(url, {
        no_rawat: no_rawat,
        tgl_perawatan: tgl_perawatan,
        jam_rawat: jam_rawat
      } ,function(data) {
        var url = baseURL + '/dokter_ranap/soap?t=' + mlite.token;
        $.post(url, {no_rawat : no_rawat,
        }, function(data) {
          // tampilkan data
          $("#soap").html(data).show();
        });
        $('input:text[name=suhu_tubuh]').val("");
        $('input:text[name=tensi]').val("");
        $('input:text[name=nadi]').val("");
        $('input:text[name=respirasi]').val("");
        $('input:text[name=tinggi]').val("");
        $('input:text[name=berat]').val("");
        $('input:text[name=gcs]').val("");
        $('input:text[name=kesadaran]').val("");
        $('input:text[name=alergi]').val("");
        $('input:text[name=imun_ke]').val("");
        $('textarea[name=keluhan]').val("");
        $('textarea[name=pemeriksaan]').val("");
        $('textarea[name=penilaian]').val("");
        $('textarea[name=rtl]').val("");
        $('input:text[name=tgl_perawatan]').val("{?=date('Y-m-d')?}");
        $('input:text[name=tgl_registrasi]').val("{?=date('Y-m-d')?}");
        $('input:text[name=jam_rawat]').val("{?=date('H:i:s')?}");
        $('#notif').html("<div class=\"alert alert-danger alert-dismissible fade in\" role=\"alert\" style=\"border-radius:0px;margin-top:-15px;\">"+
        "Data rincian riwayat telah dihapus!"+
        "<button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-label=\"Close\">&times;</button>"+
        "</div>").show();
      });
    }
  });
});

// ketika tombol simpan diklik
$("#form_kontrol").on("click", "#simpan_kontrol", function(event){
  var baseURL = mlite.url + '/' + mlite.admin;
  event.preventDefault();

  var no_rkm_medis    = $('input:text[name=no_rkm_medis]').val();
  var no_rawat        = $('input:text[name=no_rawat]').val();
  var tanggal_rujukan = $('input:text[name=tanggal_rujukan]').val();
  var tanggal_datang  = $('input:text[name=tanggal_datang]').val();
  var diagnosa        = $('input:text[name=diagnosa]').val();
  var terapi          = $('input:text[name=terapi]').val();
  var alasan1         = $('textarea[name=alasan1]').val();
  var rtl1            = $('textarea[name=rtl1]').val();

  var url = baseURL + '/dokter_ranap/savekontrol?t=' + mlite.token;
  $.post(url, {no_rawat : no_rawat,
  no_rkm_medis   : no_rkm_medis,
  tanggal_rujukan       : tanggal_rujukan,
  tanggal_datang  : tanggal_datang,
  diagnosa : diagnosa,
  terapi  : terapi,
  alasan1      : alasan1,
  rtl1          : rtl1
  }, function(data) {
    // tampilkan data
    $("#display").hide();
    var url = baseURL + '/dokter_ranap/kontrol?t=' + mlite.token;
    $.post(url, {no_rkm_medis : no_rkm_medis,
    }, function(data) {
      // tampilkan data
      $("#kontrol").html(data).show();
    });
    $('input:text[name=nm_perawatan]').val("");
    $('input:text[name=biaya]').val("");
    $('input:text[name=diagnosa_klinis]').val("");
    $('input:text[name=nama_provider]').val("");
    $('input:text[name=nama_provider2]').val("");
    $('input:text[name=kode_provider]').val("");
    $('input:text[name=kode_provider2]').val("");
    $('input:text[name=racikan]').val("");
    $('input:text[name=nama_racik]').val("");
    $('#notif').html("<div class=\"alert alert-success alert-dismissible fade in\" role=\"alert\" style=\"border-radius:0px;margin-top:-15px;\">"+
    "Data surat kontrol telah disimpan!"+
    "<button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-label=\"Close\">&times;</button>"+
    "</div>").show();
  });
});

// tombol batal diklik
$("#form_rincian").on("click", "#selesai", function(event){
  bersih();
  $("#form_rincian").hide();
  $("#form_soap").hide();
  $("#form").show();
  $("#display").show();
  $("#rincian").hide();
  $("#soap").hide();
  $('#aturan_pakai').hide();
  $('#daftar_racikan').hide();
  $("#info_tambahan").hide();
  $("#form_kontrol").hide();
  $("#kontrol").hide();
});

// tombol batal diklik
$("#form_soap").on("click", "#selesai_soap", function(event){
  bersih();
  $("#form_rincian").hide();
  $("#form_soap").hide();
  $("#form").show();
  $("#display").show();
  $("#rincian").hide();
  $("#soap").hide();
  $('#aturan_pakai').hide();
  $('#daftar_racikan').hide();
  $("#info_tambahan").hide();
  $("#form_kontrol").hide();
  $("#kontrol").hide();
});

// tombol batal diklik
$("#form_kontrol").on("click", "#selesai_kontrol", function(event){
  bersih();
  $("#form_rincian").hide();
  $("#form_soap").hide();
  $("#form").show();
  $("#display").show();
  $("#rincian").hide();
  $("#soap").hide();
  $('#aturan_pakai').hide();
  $('#daftar_racikan').hide();
  $("#info_tambahan").hide();
  $("#form_kontrol").hide();
  $("#kontrol").hide();
});

// ketika inputbox pencarian diisi
$('input:text[name=layanan]').on('input',function(e){
  var baseURL = mlite.url + '/' + mlite.admin;
  var url    = baseURL + '/dokter_ranap/layanan?t=' + mlite.token;
  var layanan = $('input:text[name=layanan]').val();

  if(layanan!="") {
      $.post(url, {layanan: layanan} ,function(data) {
      // tampilkan data yang sudah di perbaharui
        $("#layanan").html(data).show();
        $("#obat").hide();
        $("#racikan").hide();
      });
  }

});
// end pencarian

// ketika inputbox pencarian diisi
$('input:text[name=obat]').on('input',function(e){
  var baseURL = mlite.url + '/' + mlite.admin;
  var url    = baseURL + '/dokter_ranap/obat?t=' + mlite.token;
  var obat = $('input:text[name=obat]').val();

  if(obat!="") {
      $.post(url, {obat: obat} ,function(data) {
      // tampilkan data yang sudah di perbaharui
        $("#obat").html(data).show();
        $("#layanan").hide();
        $("#racikan").hide();
        $("#radiologi").hide();
        $("#laboratorium").hide();
      });
  }

});
// end pencarian

// ketika inputbox pencarian diisi
$('input:text[name=racikan]').on('input',function(e){
  var baseURL = mlite.url + '/' + mlite.admin;
  var url    = baseURL + '/dokter_ranap/racikan?t=' + mlite.token;
  var racikan = $('input:text[name=racikan]').val();

  if(racikan!="") {
      $.post(url, {racikan: racikan} ,function(data) {
      // tampilkan data yang sudah di perbaharui
        $("#racikan").html(data).show();
        $("#layanan").hide();
        $("#obat").hide();
        $("#radiologi").hide();
        $("#laboratorium").hide();
      });
  }

});
// end pencarian

// ketika inputbox pencarian diisi
$('.nama_brng').on('input',function(e){
  var baseURL = mlite.url + '/' + mlite.admin;
  var url    = baseURL + '/dokter_ranap/obatracikan?t=' + mlite.token;
  var obat = $('.nama_brng').val();

  if(obat!="") {
      $.post(url, {obat: obat} ,function(data) {
      // tampilkan data yang sudah di perbaharui
        $("#obat_racikan").html(data).show();
        $("#layanan").hide();
        $("#racikan").hide();
        $("#radiologi").hide();
        $("#laboratorium").hide();
      });
  }

});
// end pencarian

// ketika inputbox pencarian diisi
$('input:text[name=laboratorium]').on('input',function(e){
  var baseURL = mlite.url + '/' + mlite.admin;
  var url    = baseURL + '/dokter_ranap/laboratorium?t=' + mlite.token;
  var laboratorium = $('input:text[name=laboratorium]').val();

  if(laboratorium!="") {
      $.post(url, {laboratorium: laboratorium} ,function(data) {
      // tampilkan data yang sudah di perbaharui
        $("#laboratorium").html(data).show();
        $("#layanan").hide();
        $("#obat").hide();
        $("#racikan").hide();
        $("#radiologi").hide();
      });
  }

});
// end pencarian

// ketika inputbox pencarian diisi
$('input:text[name=radiologi]').on('input',function(e){
  var baseURL = mlite.url + '/' + mlite.admin;
  var url    = baseURL + '/dokter_ranap/radiologi?t=' + mlite.token;
  var radiologi = $('input:text[name=radiologi]').val();

  if(radiologi!="") {
      $.post(url, {radiologi: radiologi} ,function(data) {
      // tampilkan data yang sudah di perbaharui
        $("#radiologi").html(data).show();
        $("#layanan").hide();
        $("#obat").hide();
        $("#laboratorium").hide();
        $("#racikan").hide();
      });
  }

});
// end pencarian

// ketika baris data diklik
$("#layanan").on("click", ".pilih_layanan", function(event){
  var baseURL = mlite.url + '/' + mlite.admin;
  event.preventDefault();

  var kd_jenis_prw = $(this).attr("data-kd_jenis_prw");
  var nm_perawatan = $(this).attr("data-nm_perawatan");
  var biaya = $(this).attr("data-biaya");
  var kat = $(this).attr("data-kat");

  $('input:hidden[name=kd_jenis_prw]').val(kd_jenis_prw);
  $('input:text[name=nm_perawatan]').val(nm_perawatan);
  $('input:text[name=biaya]').val(biaya);
  $('input:hidden[name=kat]').val(kat);

  $("#layanan").hide();
  $('#provider').show();
  $('#aturan_pakai').hide();
  $('#racikan').hide();
  $("#laboratorium").hide();
  $("#radiologi").hide();
  $('#daftar_racikan').hide();
  $("#info_tambahan").hide();
});

// ketika baris data diklik
$("#obat").on("click", ".pilih_obat", function(event){
  var baseURL = mlite.url + '/' + mlite.admin;
  event.preventDefault();

  var kode_brng = $(this).attr("data-kode_brng");
  var nama_brng = $(this).attr("data-nama_brng");
  var biaya = $(this).attr("data-ralan");
  var stok = $(this).attr("data-stok");
  var stokminimal = $(this).attr("data-stokminimal");
  var kat = $(this).attr("data-kat");

  if(stok < stokminimal) {
    alert('Stok obat ' + nama_brng + ' tidak mencukupi.');
    $('input:hidden[name=kd_jenis_prw]').val();
    $('input:text[name=nm_perawatan]').val();
    $('input:text[name=biaya]').val();
    $('input:hidden[name=kat]').val();
  } else {
    $('input:hidden[name=kd_jenis_prw]').val(kode_brng);
    $('input:text[name=nm_perawatan]').val(nama_brng);
    $('input:text[name=biaya]').val(biaya);
    $('input:hidden[name=kat]').val(kat);
  }

  $('#obat').hide();
  $('#racikan').hide();
  $("#laboratorium").hide();
  $("#radiologi").hide();
  $('#daftar_racikan').hide();
  $('#aturan_pakai').show();
  $("#info_tambahan").hide();
});

// ketika baris data diklik
$("#racikan").on("click", ".pilih_racikan", function(event){
  var baseURL = mlite.url + '/' + mlite.admin;
  event.preventDefault();

  var kd_racik = $(this).attr("data-kd_racik");
  var nm_racik = $(this).attr("data-nm_racik");
  var kat = $(this).attr("data-kat");

  $('input:hidden[name=kd_jenis_prw]').val(kd_racik);
  $('input:text[name=nm_perawatan]').val(nm_racik);
  $('input:text[name=biaya]').val('');
  $('input:hidden[name=kat]').val(kat);

  $('#racikan').hide();
  $("#laboratorium").hide();
  $("#radiologi").hide();
  $('#aturan_pakai').show();
  $('#daftar_racikan').show();
  $("#info_tambahan").hide();
});

// ketika baris data diklik
$("#obat_racikan").on("click", ".pilih_obat_racikan", function(event){
  var baseURL = mlite.url + '/' + mlite.admin;
  event.preventDefault();

  var kode_brng = $(this).attr("data-kode_brng");
  var nama_brng = $(this).attr("data-nama_brng");
  var biaya = $(this).attr("data-ralan");
  var stok = $(this).attr("data-stok");

  if(stok < 10) {
    alert('Stok obat ' + nama_brng + ' tidak mencukupi.');
    $('input:hidden[name=kode_brng]').val();
    $('input:text[name=nama_brng]').val();
    $('input:text[name=biaya]').val();
  } else {
    $('input:hidden[name=kode_brng]').val(kode_brng);
    $('input:text[name=nama_brng]').val(nama_brng);
    $('input:text[name=biaya]').val(biaya);
  }

  $('#obat').hide();
  $('#racikan').hide();
  $("#laboratorium").hide();
  $("#radiologi").hide();
  //$('#daftar_racikan').hide();
  $('#aturan_pakai').show();
  $("#info_tambahan").hide();
});

// ketika baris data diklik
$("#laboratorium").on("click", ".pilih_laboratorium", function(event){
  var baseURL = mlite.url + '/' + mlite.admin;
  event.preventDefault();

  var kd_jenis_prw = $(this).attr("data-kd_jenis_prw");
  var nm_perawatan = $(this).attr("data-nm_perawatan");
  var biaya = $(this).attr("data-biaya");
  var kat = $(this).attr("data-kat");

  $('input:hidden[name=kd_jenis_prw]').val(kd_jenis_prw);
  $('input:text[name=nm_perawatan]').val(nm_perawatan);
  $('input:text[name=biaya]').val(biaya);
  $('input:hidden[name=kat]').val(kat);

  $("#layanan").hide();
  $('#provider').show();
  $('#aturan_pakai').hide();
  $('#racikan').hide();
  $("#laboratorium").hide();
  $("#radiologi").hide();
  $('#daftar_racikan').hide();
  $("#info_tambahan").show();
});

// ketika baris data diklik
$("#radiologi").on("click", ".pilih_radiologi", function(event){
  var baseURL = mlite.url + '/' + mlite.admin;
  event.preventDefault();

  var kd_jenis_prw = $(this).attr("data-kd_jenis_prw");
  var nm_perawatan = $(this).attr("data-nm_perawatan");
  var biaya = $(this).attr("data-biaya");
  var kat = $(this).attr("data-kat");

  $('input:hidden[name=kd_jenis_prw]').val(kd_jenis_prw);
  $('input:text[name=nm_perawatan]').val(nm_perawatan);
  $('input:text[name=biaya]').val(biaya);
  $('input:hidden[name=kat]').val(kat);

  $("#layanan").hide();
  $('#provider').show();
  $('#aturan_pakai').hide();
  $('#racikan').hide();
  $("#laboratorium").hide();
  $("#radiologi").hide();
  $('#daftar_racikan').hide();
  $("#info_tambahan").show();
});

// ketika tombol simpan diklik
$("#form_rincian").on("click", "#simpan_rincian", function(event){
  var baseURL = mlite.url + '/' + mlite.admin;
  event.preventDefault();

  var no_rawat        = $('input:text[name=no_rawat]').val();
  var kd_jenis_prw 	  = $('input:hidden[name=kd_jenis_prw]').val();
  var provider        = $('select[name=provider]').val();
  var kode_provider   = $('input:text[name=kode_provider]').val();
  var kode_provider2   = $('input:text[name=kode_provider2]').val();
  var tgl_perawatan   = $('input:text[name=tgl_perawatan]').val();
  var jam_rawat       = $('input:text[name=jam_rawat]').val();
  var biaya           = $('input:text[name=biaya]').val();
  var aturan_pakai    = $('input:text[name=aturan_pakai]').val();
  var kat             = $('input:hidden[name=kat]').val();
  var jml             = $('input:text[name=jml]').val();
  var nama_racik      = $('input:text[name=nama_racik]').val();
  var keterangan      = $('textarea[name=keterangan]').val();
  var kode_brng       = JSON.stringify($('select[name=kode_brng]').serializeArray());
  var kandungan       = JSON.stringify($('input:text[name=kandungan]').serializeArray());
  var diagnosa_klinis = $('input:text[name=diagnosa_klinis]').val();
  var informasi_tambahan = $('textarea[name=informasi_tambahan]').val();

  var url = baseURL + '/dokter_ranap/savedetail?t=' + mlite.token;
  $.post(url, {no_rawat : no_rawat,
  kd_jenis_prw   : kd_jenis_prw,
  provider       : provider,
  kode_provider  : kode_provider,
  kode_provider2 : kode_provider2,
  tgl_perawatan  : tgl_perawatan,
  jam_rawat      : jam_rawat,
  biaya          : biaya,
  aturan_pakai   : aturan_pakai,
  kat            : kat,
  jml            : jml,
  nama_racik     : nama_racik,
  keterangan     : keterangan,
  kode_brng      : kode_brng,
  kandungan      : kandungan,
  informasi_tambahan : informasi_tambahan,
  diagnosa_klinis      : diagnosa_klinis
  }, function(data) {
    // tampilkan data
    $("#display").hide();
    var url = baseURL + '/dokter_ranap/rincian?t=' + mlite.token;
    $.post(url, {no_rawat : no_rawat,
    }, function(data) {
      // tampilkan data
      $("#rincian").html(data).show();
    });
    $('input:hidden[name=kd_jenis_prw]').val("");
    $('input:text[name=nm_perawatan]').val("");
    $('input:hidden[name=kat]').val("");
    $('input:text[name=biaya]').val("");
    $('input:text[name=diagnosa_klinis]').val("");
    $('#informasi_tambahan').val("");
    $('input:text[name=nama_provider]').val("");
    $('input:text[name=nama_provider2]').val("");
    $('input:text[name=kode_provider]').val("");
    $('input:text[name=kode_provider2]').val("");
    $('input:text[name=racikan]').val("");
    $('input:text[name=nama_racik]').val("");
    $('#kode_brng').val("");
    $('#keterangan').val("");
    $('input:text[name=kandungan]').val("");
    $('.row_racikan').remove();
    $('#notif').html("<div class=\"alert alert-success alert-dismissible fade in\" role=\"alert\" style=\"border-radius:0px;margin-top:-15px;\">"+
    "Data pasien telah disimpan!"+
    "<button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-label=\"Close\">&times;</button>"+
    "</div>").show();
  });
});

// ketika tombol hapus ditekan
$("#rincian").on("click",".hapus_detail", function(event){
  var baseURL = mlite.url + '/' + mlite.admin;
  event.preventDefault();
  var url = baseURL + '/dokter_ranap/hapusdetail?t=' + mlite.token;
  var no_rawat = $(this).attr("data-no_rawat");
  var kd_jenis_prw = $(this).attr("data-kd_jenis_prw");
  var tgl_perawatan = $(this).attr("data-tgl_perawatan");
  var jam_rawat = $(this).attr("data-jam_rawat");
  var provider = $(this).attr("data-provider");

  // tampilkan dialog konfirmasi
  bootbox.confirm("Apakah Anda yakin ingin menghapus data ini?", function(result){
    // ketika ditekan tombol ok
    if (result){
      // mengirimkan perintah penghapusan
      $.post(url, {
        no_rawat: no_rawat,
        kd_jenis_prw: kd_jenis_prw,
        tgl_perawatan: tgl_perawatan,
        jam_rawat: jam_rawat,
        provider: provider
      } ,function(data) {
        var url = baseURL + '/dokter_ranap/rincian?t=' + mlite.token;
        $.post(url, {no_rawat : no_rawat,
        }, function(data) {
          // tampilkan data
          $("#rincian").html(data).show();
        });
        $('#notif').html("<div class=\"alert alert-danger alert-dismissible fade in\" role=\"alert\" style=\"border-radius:0px;margin-top:-15px;\">"+
        "Data rincian rawat jalan telah dihapus!"+
        "<button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-label=\"Close\">&times;</button>"+
        "</div>").show();
      });
    }
  });
});

// ketika tombol hapus ditekan
$("#rincian").on("click",".hapus_resep_obat", function(event){
  var baseURL = mlite.url + '/' + mlite.admin;
  event.preventDefault();
  var url = baseURL + '/dokter_ranap/hapusresep?t=' + mlite.token;
  var no_resep = $(this).attr("data-no_resep");
  var no_rawat = $(this).attr("data-no_rawat");
  var tgl_peresepan = $(this).attr("data-tgl_peresepan");
  var jam_peresepan = $(this).attr("data-jam_peresepan");

  // tampilkan dialog konfirmasi
  bootbox.confirm("Apakah Anda yakin ingin menghapus data ini?", function(result){
    // ketika ditekan tombol ok
    if (result){
      // mengirimkan perintah penghapusan
      $.post(url, {
        no_resep: no_resep,
        no_rawat: no_rawat,
        tgl_peresepan: tgl_peresepan,
        jam_peresepan: jam_peresepan
      } ,function(data) {
        var url = baseURL + '/dokter_ranap/rincian?t=' + mlite.token;
        $.post(url, {no_rawat : no_rawat,
        }, function(data) {
          // tampilkan data
          $("#rincian").html(data).show();
        });
        $('#notif').html("<div class=\"alert alert-danger alert-dismissible fade in\" role=\"alert\" style=\"border-radius:0px;margin-top:-15px;\">"+
        "Data rincian rawat jalan telah dihapus!"+
        "<button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-label=\"Close\">&times;</button>"+
        "</div>").show();
      });
    }
  });
});

// ketika tombol hapus ditekan
$("#rincian").on("click",".hapus_resep_dokter", function(event){
  var baseURL = mlite.url + '/' + mlite.admin;
  event.preventDefault();
  var url = baseURL + '/dokter_ranap/hapusresep?t=' + mlite.token;
  var no_resep = $(this).attr("data-no_resep");
  var no_rawat = $(this).attr("data-no_rawat");
  var kd_jenis_prw = $(this).attr("data-kd_jenis_prw");

  // tampilkan dialog konfirmasi
  bootbox.confirm("Apakah Anda yakin ingin menghapus data ini?", function(result){
    // ketika ditekan tombol ok
    if (result){
      // mengirimkan perintah penghapusan
      $.post(url, {
        no_resep: no_resep,
        no_rawat: no_rawat,
        kd_jenis_prw: kd_jenis_prw
      } ,function(data) {
        var url = baseURL + '/dokter_ranap/rincian?t=' + mlite.token;
        $.post(url, {no_rawat : no_rawat,
        }, function(data) {
          // tampilkan data
          $("#rincian").html(data).show();
        });
        $('#notif').html("<div class=\"alert alert-danger alert-dismissible fade in\" role=\"alert\" style=\"border-radius:0px;margin-top:-15px;\">"+
        "Data rincian rawat jalan telah dihapus!"+
        "<button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-label=\"Close\">&times;</button>"+
        "</div>").show();
      });
    }
  });
});

// ketika tombol hapus ditekan
$("#rincian").on("click",".copy_resep", function(event){
  var baseURL = mlite.url + '/' + mlite.admin;
  event.preventDefault();
  var url = baseURL + '/dokter_ranap/copyresep?t=' + mlite.token;
  var no_resep  = $(this).attr("data-no_resep");

  $.post(url, {no_resep: no_resep} ,function(data) {
    // tampilkan data
    $("#display_copy_resep").html(data).show();
  });

});

// ketika tombol hapus ditekan
$("#rincian").on("click","#simpan_copy_resep", function(event){
//$('form').on('submit', function(event){
  //alert('submit copy');
  var baseURL = mlite.url + '/' + mlite.admin;
  event.preventDefault();
  var url_save = baseURL + '/dokter_ranap/savecopyresep?t=' + mlite.token;
  var url = baseURL + '/dokter_ranap/rincian?t=' + mlite.token;
  var no_rawat = $('input:text[name=no_rawat]').val();
  var tgl_perawatan   = $('input:text[name=tgl_perawatan]').val();
  var jam_rawat       = $('input:text[name=jam_reg]').val();
  var kode_brng       = JSON.stringify($('input:hidden[name=kode_brng_copyresep]').serializeArray());
  var jml       = JSON.stringify($('input:text[name=jml_copyresep]').serializeArray());
  var aturan_pakai       = JSON.stringify($('input:hidden[name=aturan_copyresep]').serializeArray());

  $.post(url_save, {no_rawat : no_rawat,
    tgl_perawatan : tgl_perawatan,
    jam_rawat : jam_rawat,
    kode_brng : kode_brng,
    jml : jml,
    aturan_pakai : aturan_pakai
  }, function(data) {
    //alert(data);
    //if(data == 'ErrorError') {
    //  alert('Stok tidak mencukupi pada satu atau lebih obat.');
    //} else {
      $.post(url, {no_rawat : no_rawat,
      }, function(data) {
        // tampilkan data
        $("#rincian").html(data).show();
      });
      $('#notif').html("<div class=\"alert alert-success alert-dismissible fade in\" role=\"alert\" style=\"border-radius:0px;margin-top:-15px;\">"+
      "Data pasien telah disimpan!"+
      "<button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-label=\"Close\">&times;</button>"+
      "</div>").show();

    //}
  });

});

function bersih(){
  $('input:text[name=no_rawat]').val("");
  $('input:text[name=no_rkm_medis]').val("");
  $('input:text[name=nm_pasien]').val("");
  $('input:text[name=tgl_perawatan]').val("{?=date('Y-m-d')?}");
  $('input:text[name=tgl_registrasi]').val("{?=date('Y-m-d')?}");
  $('input:text[name=tgl_lahir]').val("");
  $('input:text[name=jenis_kelamin]').val("");
  $('input:text[name=alamat]').val("");
  $('input:text[name=telepon]').val("");
  $('input:text[name=pekerjaan]').val("");
  $('input:text[name=layanan]').val("");
  $('input:text[name=obat]').val("");
  $('input:text[name=nama_jenis]').val("");
  $('input:text[name=jumlah_jual]').attr("disabled", true);
  $('input:text[name=potongan]').attr("disabled", true);
  $('input:text[name=harga_jual]').val("");
  $('input:text[name=total]').val("");
  $('input:text[name=no_reg]').val("");
  $('input:text[name=racikan]').val("");
  $('input:text[name=nama_racik]').val("");
  $('#kode_brng').val("");
  $('#keterangan').val("");
  $('input:text[name=kandungan]').val("");
  $('input:text[name=nm_perawatan]').val("");
}

$(document).click(function (event) {
    $('.dropdown-menu[data-parent]').hide();
});
$(document).on('click', '.table-responsive [data-toggle="dropdown"]', function () {
    if ($('body').hasClass('modal-open')) {
        throw new Error("This solution is not working inside a responsive table inside a modal, you need to find out a way to calculate the modal Z-index and add it to the element")
        return true;
    }

    $buttonGroup = $(this).parent();
    if (!$buttonGroup.attr('data-attachedUl')) {
        var ts = +new Date;
        $ul = $(this).siblings('ul');
        $ul.attr('data-parent', ts);
        $buttonGroup.attr('data-attachedUl', ts);
        $(window).resize(function () {
            $ul.css('display', 'none').data('top');
        });
    } else {
        $ul = $('[data-parent=' + $buttonGroup.attr('data-attachedUl') + ']');
    }
    if (!$buttonGroup.hasClass('open')) {
        $ul.css('display', 'none');
        return;
    }
    dropDownFixPosition($(this).parent(), $ul);
    function dropDownFixPosition(button, dropdown) {
        var dropDownTop = button.offset().top + button.outerHeight();
        dropdown.css('top', dropDownTop-60 + "px");
        dropdown.css('left', button.offset().left+7 + "px");
        dropdown.css('position', "absolute");

        dropdown.css('width', dropdown.width());
        dropdown.css('heigt', dropdown.height());
        dropdown.css('display', 'block');
        dropdown.appendTo('body');
    }
});

$('body').on('hidden.bs.modal', '.modal', function () {
    $(this).removeData('bs.modal');
});

$(document).ready(function () {
  var strip_tags = function(str) {
    return (str + '').replace(/<\/?[^>]+(>|$)/g, '')
  };
  var truncate_string = function(str, chars) {
    if ($.trim(str).length <= chars) {
      return str;
    } else {
      return $.trim(str.substr(0, chars)) + '...';
    }
  };
  $('select').selectator('destroy');
  $('.databarang_ajax').selectator({
    labels: {
      search: 'Cari obat...'
    },
    load: function (search, callback) {
      if (search.length < this.minSearchLength) return callback();
      $.ajax({
        url: '{?=url()?}/admin/dokter_ranap/ajax?show=databarang&nama_brng=' + encodeURIComponent(search) + '&t={?=$_SESSION['token']?}',
        type: 'GET',
        dataType: 'json',
        success: function(data) {
          callback(data.slice(0, 100));
          console.log(data);
        },
        error: function() {
          callback();
        }
      });
    },
    delay: 300,
    minSearchLength: 1,
    valueField: 'kode_brng',
    textField: 'nama_brng'
  });  $('select').selectator();
});
