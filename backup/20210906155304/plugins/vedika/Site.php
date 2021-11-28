<?php

namespace Plugins\Vedika;

use Systems\SiteModule;

class Site extends SiteModule
{

    public function routes()
    {
        $this->route('vedika', 'getIndex');
        $this->route('vedika/css', 'getCss');
        $this->route('vedika/javascript', 'getJavascript');
        $this->route('vedika/pdf/(:str)', 'getPDF');
        $this->route('vedika/downloadpdf/(:str)', 'getDownloadPDF');
        $this->route('vedika/catatan/(:str)', 'getCatatan');
    }

    public function getIndex()
    {
        $page = [
            'title' => 'Vedika LITE',
            'desc' => 'Dashboard Verifikasi Digital Klaim BPJS',
            'content' => $this->_getManage($page = 1)
        ];

        $this->setTemplate('fullpage.html');
        $this->tpl->set('page', $page);
    }

    public function _getManage($page = 1)
    {
      $this->_addHeaderFiles();
      $start_date = date('Y-m-d');
      if(isset($_GET['start_date']) && $_GET['start_date'] !='')
        $start_date = $_GET['start_date'];
      $end_date = date('Y-m-d');
      if(isset($_GET['end_date']) && $_GET['end_date'] !='')
        $end_date = $_GET['end_date'];
      $query = $this->db()->pdo()->prepare("SELECT reg_periksa.*, pasien.*, dokter.nm_dokter, poliklinik.nm_poli, penjab.png_jawab FROM reg_periksa, pasien, dokter, poliklinik, penjab WHERE reg_periksa.no_rkm_medis = pasien.no_rkm_medis AND reg_periksa.kd_dokter = dokter.kd_dokter AND reg_periksa.kd_poli = poliklinik.kd_poli AND reg_periksa.kd_pj = penjab.kd_pj AND reg_periksa.tgl_registrasi BETWEEN '$start_date' AND '$end_date'");
      $query->execute();
      $rows = $query->fetchAll();

      $this->assign['list'] = [];
      if (count($rows)) {
          foreach ($rows as $row) {
              $berkas_digital = $this->db('berkas_digital_perawatan')
                ->join('master_berkas_digital', 'master_berkas_digital.kode=berkas_digital_perawatan.kode')
                ->where('berkas_digital_perawatan.no_rawat', $row['no_rawat'])
                ->asc('master_berkas_digital.nama')
                ->toArray();
              $galleri_pasien = $this->db('mlite_pasien_galleries_items')
                ->join('mlite_pasien_galleries', 'mlite_pasien_galleries.id = mlite_pasien_galleries_items.gallery')
                ->where('mlite_pasien_galleries.slug', $row['no_rkm_medis'])
                ->toArray();

              $berkas_digital_pasien = array();
              if (count($galleri_pasien)) {
                  foreach ($galleri_pasien as $galleri) {
                      $galleri['src'] = unserialize($galleri['src']);

                      if (!isset($galleri['src']['sm'])) {
                          $galleri['src']['sm'] = isset($galleri['src']['xs']) ? $galleri['src']['xs'] : $galleri['src']['lg'];
                      }

                      $berkas_digital_pasien[] = $galleri;
                  }
              }

              $row = htmlspecialchars_array($row);
              $row['no_sep'] = $this->_getSEPInfo('no_sep', $row['no_rawat']);
              $row['no_peserta'] = $this->_getSEPInfo('no_kartu', $row['no_rawat']);
              $row['no_rujukan'] = $this->_getSEPInfo('no_rujukan', $row['no_rawat']);
              $row['kd_penyakit'] = $this->_getDiagnosa('kd_penyakit', $row['no_rawat'], $row['status_lanjut']);
              $row['nm_penyakit'] = $this->_getDiagnosa('nm_penyakit', $row['no_rawat'], $row['status_lanjut']);
              $row['berkas_digital'] = $berkas_digital;
              $row['berkas_digital_pasien'] = $berkas_digital_pasien;
              $row['sepURL'] = url(['vedika', 'sep', $row['no_sep']]);
              $row['pdfURL'] = url(['vedika', 'pdf', $this->convertNorawat($row['no_rawat'])]);
              $row['downloadURL'] = url(['vedika', 'downloadpdf', $this->convertNorawat($row['no_rawat'])]);
              $row['catatanURL'] = url(['vedika', 'catatan', $this->convertNorawat($row['no_rawat'])]);
              $row['resumeURL']  = url(['vedika', 'resume', $this->convertNorawat($row['no_rawat'])]);
              $row['billingURL'] = url(['vedika', 'billing', $this->convertNorawat($row['no_rawat'])]);
              $this->assign['list'][] = $row;
          }
      }

      $this->assign['vedika_username'] = $this->settings->get('vedika.username');
      $this->assign['vedika_password'] = $this->settings->get('vedika.password');

      $this->assign['searchUrl'] =  url(['vedika', 'manage', $page.'?start_date='.$start_date.'&end_date='.$end_date]);
      return $this->draw('manage.html', ['vedika' => $this->assign]);

    }

    public function getPDF($id)
    {
      $berkas_digital = $this->db('berkas_digital_perawatan')
        ->join('master_berkas_digital', 'master_berkas_digital.kode=berkas_digital_perawatan.kode')
        ->where('berkas_digital_perawatan.no_rawat', $this->revertNorawat($id))
        ->asc('master_berkas_digital.nama')
        ->toArray();

      $galleri_pasien = $this->db('mlite_pasien_galleries_items')
        ->join('mlite_pasien_galleries', 'mlite_pasien_galleries.id = mlite_pasien_galleries_items.gallery')
        ->where('mlite_pasien_galleries.slug', $this->getRegPeriksaInfo('no_rkm_medis', $this->revertNorawat($id)))
        ->toArray();

      $berkas_digital_pasien = array();
      if (count($galleri_pasien)) {
          foreach ($galleri_pasien as $galleri) {
              $galleri['src'] = unserialize($galleri['src']);

              if (!isset($galleri['src']['sm'])) {
                  $galleri['src']['sm'] = isset($galleri['src']['xs']) ? $galleri['src']['xs'] : $galleri['src']['lg'];
              }

              $berkas_digital_pasien[] = $galleri;
          }
      }

      $no_rawat = $this->revertNorawat($id);
      $query = $this->db()->pdo()->prepare("select no,nm_perawatan,pemisah,if(biaya=0,'',biaya),if(jumlah=0,'',jumlah),if(tambahan=0,'',tambahan),if(totalbiaya=0,'',totalbiaya),totalbiaya from billing where no_rawat='$no_rawat'");
      $query->execute();
      $rows = $query->fetchAll();
      $total=0;
      foreach ($rows as $key => $value) {
        $total = $total+$value['7'];
      }
      $total = $total;
      $this->tpl->set('total', $total);

      $settings = $this->settings('settings');
      $this->tpl->set('settings', $this->tpl->noParse_array(htmlspecialchars_array($settings)));

      $this->tpl->set('billing', $rows);
      //$this->tpl->set('instansi', $instansi);

      $print_sep = array();
      if(!empty($this->_getSEPInfo('no_sep', $no_rawat))) {
        $print_sep['bridging_sep'] = $this->db('bridging_sep')->where('no_sep', $this->_getSEPInfo('no_sep', $no_rawat))->oneArray();
        $batas_rujukan = $this->db('bridging_sep')->select('DATE_ADD(tglrujukan , INTERVAL 85 DAY) AS batas_rujukan')->where('no_sep', $id)->oneArray();
        $print_sep['batas_rujukan'] = $batas_rujukan['batas_rujukan'];
      }

      $print_sep['logoURL'] = url(MODULES.'/pendaftaran/img/bpjslogo.png');
      $this->tpl->set('print_sep', $print_sep);

      $resume_pasien = $this->db('resume_pasien')
        ->join('dokter', 'dokter.kd_dokter = resume_pasien.kd_dokter')
        ->where('no_rawat', $this->revertNorawat($id))
        ->oneArray();
      $this->tpl->set('resume_pasien', $resume_pasien);

      $pasien = $this->db('pasien')
        ->join('kecamatan', 'kecamatan.kd_kec = pasien.kd_kec')
        ->join('kabupaten', 'kabupaten.kd_kab = pasien.kd_kab')
        ->where('no_rkm_medis', $this->getRegPeriksaInfo('no_rkm_medis', $this->revertNorawat($id)))
        ->oneArray();
      $reg_periksa = $this->db('reg_periksa')
        ->join('dokter', 'dokter.kd_dokter = reg_periksa.kd_dokter')
        ->join('poliklinik', 'poliklinik.kd_poli = reg_periksa.kd_poli')
        ->join('penjab', 'penjab.kd_pj = reg_periksa.kd_pj')
        ->where('stts', '<>', 'Batal')
        ->where('no_rawat', $this->revertNorawat($id))
        ->oneArray();
      $rujukan_internal = $this->db('rujukan_internal_poli')
        ->join('poliklinik', 'poliklinik.kd_poli = rujukan_internal_poli.kd_poli')
        ->join('dokter', 'dokter.kd_dokter = rujukan_internal_poli.kd_dokter')
        ->where('no_rawat', $this->revertNorawat($id))
        ->oneArray();
      $diagnosa_pasien = $this->db('diagnosa_pasien')
        ->join('penyakit', 'penyakit.kd_penyakit = diagnosa_pasien.kd_penyakit')
        ->where('no_rawat', $this->revertNorawat($id))
        ->toArray();
      $prosedur_pasien = $this->db('prosedur_pasien')
        ->join('icd9', 'icd9.kode = prosedur_pasien.kode')
        ->where('no_rawat', $this->revertNorawat($id))
        ->toArray();
      $pemeriksaan_ralan = $this->db('pemeriksaan_ralan')
        ->where('no_rawat', $this->revertNorawat($id))
        ->asc('tgl_perawatan')
        ->asc('jam_rawat')
        ->toArray();
      $pemeriksaan_ranap = $this->db('pemeriksaan_ranap')
        ->where('no_rawat', $this->revertNorawat($id))
        ->asc('tgl_perawatan')
        ->asc('jam_rawat')
        ->toArray();

      $this->tpl->set('pasien', $pasien);
      $this->tpl->set('reg_periksa', $reg_periksa);
      $this->tpl->set('rujukan_internal', $rujukan_internal);
      $this->tpl->set('diagnosa_pasien', $diagnosa_pasien);
      $this->tpl->set('prosedur_pasien', $prosedur_pasien);
      $this->tpl->set('pemeriksaan_ralan', $pemeriksaan_ralan);
      $this->tpl->set('pemeriksaan_ranap', $pemeriksaan_ranap);

      $this->tpl->set('berkas_digital', $berkas_digital);
      $this->tpl->set('berkas_digital_pasien', $berkas_digital_pasien);
      echo $this->tpl->draw(MODULES.'/vedika/view/pdf.html', true);
      exit();
    }

    public function getCatatan($id)
    {
      echo $this->tpl->draw(MODULES.'/vedika/view/catatan.html', true);
      exit();
    }

    public function getDownloadPDF($id)
    {
      $apikey = '82b115a1-22c8-4f9c-b2a9-b9e763243ed9';
      $value = url().'/vedika/pdf/'.$id; // can aso be a url, starting with http..

      $bridging_sep = $this->db('bridging_sep')->where('no_rawat', $this->revertNorawat($id))->oneArray();

      // Convert the HTML string to a PDF using those parameters.  Note if you have a very long HTML string use POST rather than get.  See example #5
      $result = file_get_contents("http://api.html2pdfrocket.com/pdf?apikey=" . urlencode($apikey) . "&value=" . urlencode($value));

      // Save to root folder in website
      //file_put_contents('mypdf-1.pdf', $result);

      // Output headers so that the file is downloaded rather than displayed
      // Remember that header() must be called before any actual output is sent
      header('Content-Description: File Transfer');
      header('Content-Type: application/pdf');
      header('Expires: 0');
      header('Cache-Control: must-revalidate');
      header('Pragma: public');
      header('Content-Length: ' . strlen($result));

      // Make the file a downloadable attachment - comment this out to show it directly inside the
      // web browser.  Note that you can give the file any name you want, e.g. alias-name.pdf below:
      header('Content-Disposition: attachment; filename=' . 'e-vedika-'.$bridging_sep['tglsep'].'-'.$bridging_sep['no_sep'].'.pdf' );

      // Stream PDF to user
      echo $result;
      exit();
    }

    private function _getSEPInfo($field, $no_rawat)
    {
        $row = $this->db('bridging_sep')->where('no_rawat', $no_rawat)->oneArray();
        return $row[$field];
    }

    private function _getDiagnosa($field, $no_rawat, $status_lanjut)
    {
        $row = $this->db('diagnosa_pasien')->join('penyakit', 'penyakit.kd_penyakit = diagnosa_pasien.kd_penyakit')->where('diagnosa_pasien.no_rawat', $no_rawat)->where('diagnosa_pasien.prioritas', 1)->where('diagnosa_pasien.status', $status_lanjut)->oneArray();
        return $row[$field];
    }

    public function getRegPeriksaInfo($field, $no_rawat)
    {
        $row = $this->db('reg_periksa')->where('no_rawat', $no_rawat)->oneArray();
        return $row[$field];
    }

    public function convertNorawat($text)
    {
        setlocale(LC_ALL, 'en_EN');
        $text = str_replace('/', '', trim($text));
        return $text;
    }

    public function revertNorawat($text)
    {
        setlocale(LC_ALL, 'en_EN');
        $tahun = substr($text, 0, 4);
        $bulan = substr($text, 4, 2);
        $tanggal = substr($text, 6, 2);
        $nomor = substr($text, 8, 6);
        $result = $tahun.'/'.$bulan.'/'.$tanggal.'/'.$nomor;
        return $result;
    }

    public function getJavascript()
    {
        header('Content-type: text/javascript');
        echo $this->draw(MODULES.'/vedika/js/scripts.js');
        exit();
    }

    public function getCss()
    {
        header('Content-type: text/css');
        echo $this->draw(MODULES.'/vedika/css/styles.css');
        exit();
    }

    private function _addHeaderFiles()
    {
        // CSS
        $this->core->addCSS(url('assets/css/jquery-ui.css'));
        $this->core->addCSS(url('assets/css/jquery.timepicker.css'));
        $this->core->addCSS(url('assets/css/dataTables.bootstrap.min.css'));

        // JS
        $this->core->addJS(url('assets/jscripts/jquery-ui.js'), 'footer');
        $this->core->addJS(url('assets/jscripts/jquery.timepicker.js'), 'footer');
        $this->core->addJS(url('assets/jscripts/jquery.dataTables.min.js'), 'footer');
        $this->core->addJS(url('assets/jscripts/dataTables.bootstrap.min.js'), 'footer');

        // MODULE SCRIPTS
        $this->core->addCSS(url(['vedika', 'css']));
        $this->core->addJS(url(['vedika', 'javascript']), 'footer');
    }

}
