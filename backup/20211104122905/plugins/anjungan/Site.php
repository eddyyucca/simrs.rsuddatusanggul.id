<?php

namespace Plugins\Anjungan;

use Systems\SiteModule;

class Site extends SiteModule
{
    public function routes()
    {
        $this->route('anjungan', 'getIndex');
        $this->route('anjungan/pasien', 'getDisplayAPM');
        $this->route('anjungan/loket', 'getDisplayAntrianLoket');
        $this->route('anjungan/poli', 'getDisplayAntrianPoli');
        $this->route('anjungan/laboratorium', 'getDisplayAntrianLaboratorium');
        $this->route('anjungan/ajax', 'getAjax');
        $this->route('anjungan/presensi', 'getPresensi');
        $this->route('anjungan/presensi/upload', 'getUpload');
        $this->route('anjungan/bed', 'getDisplayBed');
    }

    public function getIndex()
    {
        echo $this->draw('index.html');
        exit();
    }
    public function getDisplayAPM()
    {
        $title = 'Display Antrian Poliklinik';
        $logo  = $this->settings->get('settings.logo');
        $poliklinik = $this->db('poliklinik')->toArray();
        $penjab = $this->db('penjab')->toArray();

        $_username = $this->core->getUserInfo('fullname', null, true);
        $tanggal       = getDayIndonesia(date('Y-m-d')).', '.dateIndonesia(date('Y-m-d'));
        $username      = !empty($_username) ? $_username : $this->core->getUserInfo('username');

        $content = $this->draw('display.antrian.html', [
          'title' => $title,
          'logo' => $logo,
          'powered' => 'Powered by <a href="https://basoro.org/">KhanzaLITE</a>',
          'username' => $username,
          'tanggal' => $tanggal,
          'running_text' => $this->settings->get('anjungan.text_anjungan'),
          'poliklinik' => $poliklinik,
          'penjab' => $penjab
        ]);

        $assign = [
            'title' => $this->settings->get('settings.nama_instansi'),
            'desc' => $this->settings->get('settings.alamat'),
            'content' => $content
        ];

        $this->setTemplate("canvas.html");

        $this->tpl->set('page', ['title' => $assign['title'], 'desc' => $assign['desc'], 'content' => $assign['content']]);

    }

    public function getDisplayAntrianPoli()
    {
        $title = 'Display Antrian Poliklinik';
        $logo  = $this->settings->get('settings.logo');
        $display = $this->_resultDisplayAntrianPoli();

        $_username = $this->core->getUserInfo('fullname', null, true);
        $tanggal       = getDayIndonesia(date('Y-m-d')).', '.dateIndonesia(date('Y-m-d'));
        $username      = !empty($_username) ? $_username : $this->core->getUserInfo('username');

        $content = $this->draw('display.antrian.poli.html', [
          'title' => $title,
          'logo' => $logo,
          'powered' => 'Powered by <a href="https://basoro.org/">KhanzaLITE</a>',
          'username' => $username,
          'tanggal' => $tanggal,
          'running_text' => $this->settings->get('anjungan.text_poli'),
          'display' => $display
        ]);

        $assign = [
            'title' => $this->settings->get('settings.nama_instansi'),
            'desc' => $this->settings->get('settings.alamat'),
            'content' => $content
        ];

        $this->setTemplate("canvas.html");

        $this->tpl->set('page', ['title' => $assign['title'], 'desc' => $assign['desc'], 'content' => $assign['content']]);

    }

    public function _resultDisplayAntrianPoli()
    {
        $date = date('Y-m-d');
        $tentukan_hari=date('D',strtotime(date('Y-m-d')));
        $day = array(
          'Sun' => 'AKHAD',
          'Mon' => 'SENIN',
          'Tue' => 'SELASA',
          'Wed' => 'RABU',
          'Thu' => 'KAMIS',
          'Fri' => 'JUMAT',
          'Sat' => 'SABTU'
        );
        $hari=$day[$tentukan_hari];

        $poliklinik = str_replace(",","','", $this->settings->get('anjungan.display_poli'));
        $query = $this->db()->pdo()->prepare("SELECT a.kd_dokter, a.kd_poli, b.nm_poli, c.nm_dokter, a.jam_mulai, a.jam_selesai FROM jadwal a, poliklinik b, dokter c WHERE a.kd_poli = b.kd_poli AND a.kd_dokter = c.kd_dokter AND a.hari_kerja = '$hari'  AND a.kd_poli IN ('$poliklinik')");
        $query->execute();
        $rows = $query->fetchAll(\PDO::FETCH_ASSOC);;

        $result = [];
        if (count($rows)) {
            foreach ($rows as $row) {
                $row['dalam_pemeriksaan'] = $this->db('reg_periksa')
                  ->select('no_reg')
                  ->select('nm_pasien')
                  ->join('pasien', 'pasien.no_rkm_medis = reg_periksa.no_rkm_medis')
                  ->where('tgl_registrasi', $date)
                  ->where('stts', 'Berkas Diterima')
                  ->where('kd_poli', $row['kd_poli'])
                  ->where('kd_dokter', $row['kd_dokter'])
                  ->limit(1)
                  ->oneArray();
                $row['dalam_antrian'] = $this->db('reg_periksa')
                  ->select(['jumlah' => 'COUNT(DISTINCT reg_periksa.no_rawat)'])
                  ->join('poliklinik', 'poliklinik.kd_poli = reg_periksa.kd_poli')
                  ->where('reg_periksa.tgl_registrasi', date('Y-m-d'))
                  ->where('reg_periksa.kd_poli', $row['kd_poli'])
                  ->where('reg_periksa.kd_dokter', $row['kd_dokter'])
                  ->oneArray();
                $row['sudah_dilayani'] = $this->db('reg_periksa')
                  ->select(['count' => 'COUNT(DISTINCT reg_periksa.no_rawat)'])
                  ->join('poliklinik', 'poliklinik.kd_poli = reg_periksa.kd_poli')
                  ->where('reg_periksa.tgl_registrasi', date('Y-m-d'))
                  ->where('reg_periksa.kd_poli', $row['kd_poli'])
                  ->where('reg_periksa.kd_dokter', $row['kd_dokter'])
                  ->where('reg_periksa.stts', 'Sudah')
                  ->oneArray();
                $row['sudah_dilayani']['jumlah'] = 0;
                if(!empty($row['sudah_dilayani'])) {
                  $row['sudah_dilayani']['jumlah'] = $row['sudah_dilayani']['count'];
                }
                $row['selanjutnya'] = $this->db('reg_periksa')
                  ->select('reg_periksa.no_reg')
                  //->select(['no_urut_reg' => 'ifnull(MAX(CONVERT(RIGHT(reg_periksa.no_reg,3),signed)),0)'])
                  ->select('pasien.nm_pasien')
                  ->join('pasien', 'pasien.no_rkm_medis = reg_periksa.no_rkm_medis')
                  ->where('reg_periksa.tgl_registrasi', $date)
                  ->where('reg_periksa.stts', 'Belum')
                  ->where('reg_periksa.kd_poli', $row['kd_poli'])
                  ->where('reg_periksa.kd_dokter', $row['kd_dokter'])
                  ->asc('reg_periksa.no_reg')
                  ->toArray();
                $row['get_no_reg'] = $this->db('reg_periksa')
                  ->select(['max' => 'ifnull(MAX(CONVERT(RIGHT(no_reg,3),signed)),0)'])
                  ->where('tgl_registrasi', $date)
                  ->where('kd_poli', $row['kd_poli'])
                  ->where('kd_dokter', $row['kd_dokter'])
                  ->oneArray();
                $row['diff'] = (strtotime($row['jam_selesai'])-strtotime($row['jam_mulai']))/60;
                $row['interval'] = 0;
                if($row['diff'] == 0) {
                  $row['interval'] = round($row['diff']/$row['get_no_reg']['max']);
                }
                if($row['interval'] > 10){
                  $interval = 10;
                } else {
                  $interval = $row['interval'];
                }
                foreach ($row['selanjutnya'] as $value) {
                  //$minutes = $value['no_reg'] * $interval;
                  //$row['jam_mulai'] = date('H:i',strtotime('+10 minutes',strtotime($row['jam_mulai'])));
                }

                $result[] = $row;
            }
        }

        return $result;
    }

    public function getDisplayAntrianLoket()
    {
        $title = 'Display Antrian Loket';
        $logo  = $this->settings->get('settings.logo');
        $display = '';

        $_username = $this->core->getUserInfo('fullname', null, true);
        $tanggal       = getDayIndonesia(date('Y-m-d')).', '.dateIndonesia(date('Y-m-d'));
        $username      = !empty($_username) ? $_username : $this->core->getUserInfo('username');

        $show = isset($_GET['show']) ? $_GET['show'] : "";
        switch($show){
          default:
            $display = 'Depan';
            $content = $this->draw('display.antrian.loket.html', [
              'title' => $title,
              'logo' => $logo,
              'powered' => 'Powered by <a href="https://basoro.org/">KhanzaLITE</a>',
              'username' => $username,
              'tanggal' => $tanggal,
              'show' => $show,
              'vidio' => $this->settings->get('anjungan.vidio'),
              'running_text' => $this->settings->get('anjungan.text_loket'),
              'display' => $display
            ]);
          break;
          case "panggil_loket":
            $display = 'Panggil Loket';

            $_username = $this->core->getUserInfo('fullname', null, true);
            $tanggal       = getDayIndonesia(date('Y-m-d')).', '.dateIndonesia(date('Y-m-d'));
            $username      = !empty($_username) ? $_username : $this->core->getUserInfo('username');

            $setting_antrian_loket = str_replace(",","','", $this->settings->get('anjungan.antrian_loket'));
            $loket = explode(",", $this->settings->get('anjungan.antrian_loket'));
            $get_antrian = $this->db('mlite_antrian_loket')->select('noantrian')->where('type', 'Loket')->where('postdate', date('Y-m-d'))->desc('start_time')->oneArray();
            $noantrian = 0;
            if(!empty($get_antrian['noantrian'])) {
              $noantrian = $get_antrian['noantrian'];
            }

            //$antriloket = $this->db('antriloket')->oneArray();
            //$tcounter = $antriloket['antrian'];
            $antriloket = $this->settings->get('anjungan.panggil_loket_nomor');
            $tcounter = $antriloket;
            $_tcounter = 1;
            if(!empty($tcounter)) {
              $_tcounter = $tcounter + 1;
            }
            if(isset($_GET['loket'])) {
              $this->db('mlite_antrian_loket')
                ->where('type', 'Loket')
                ->where('noantrian', $tcounter)
                ->where('postdate', date('Y-m-d'))
                ->save(['end_time' => date('H:i:s')]);
              /*$this->db()->pdo()->exec("DELETE FROM `antriloket`");
              $this->db('antriloket')->save([
                'loket' => $_GET['loket'],
                'antrian' => $_tcounter
              ]);*/
              $this->db('mlite_settings')->where('module', 'anjungan')->where('field', 'panggil_loket')->save(['value' => $_GET['loket']]);
              $this->db('mlite_settings')->where('module', 'anjungan')->where('field', 'panggil_loket_nomor')->save(['value' => $_tcounter]);
            }
            if(isset($_GET['antrian'])) {
              /*$this->db()->pdo()->exec("DELETE FROM `antriloket`");
              $this->db('antriloket')->save([
                'loket' => $_GET['reset'],
                'antrian' => $_GET['antrian']
              ]);*/
              $this->db('mlite_settings')->where('module', 'anjungan')->where('field', 'panggil_loket')->save(['value' => $_GET['reset']]);
              $this->db('mlite_settings')->where('module', 'anjungan')->where('field', 'panggil_loket_nomor')->save(['value' => $_GET['antrian']]);
            }
            $hitung_antrian = $this->db('mlite_antrian_loket')
              ->where('type', 'Loket')
              ->like('postdate', date('Y-m-d'))
              ->toArray();
            $counter = strlen($tcounter);
            $xcounter = [];
            for($i=0;$i<$counter;$i++){
            	$xcounter[] = '<audio id="suarabel'.$i.'" src="{?=url()?}/plugins/anjungan/suara/'.substr($tcounter,$i,1).'.wav" ></audio>';
            };

            $content = $this->draw('display.antrian.loket.html', [
              'title' => $title,
              'logo' => $logo,
              'powered' => 'Powered by <a href="https://basoro.org/">KhanzaLITE</a>',
              'username' => $username,
              'tanggal' => $tanggal,
              'show' => $show,
              'loket' => $loket,
              'namaloket' => 'a',
              'panggil_loket' => 'panggil_loket',
              'antrian' => $tcounter,
              'hitung_antrian' => $hitung_antrian,
              'xcounter' => $xcounter,
              'noantrian' =>$noantrian,
              'display' => $display
            ]);
          break;
          case "panggil_cs":
            $display = 'Panggil CS';
            $loket = explode(",", $this->settings->get('anjungan.antrian_cs'));
            $get_antrian = $this->db('mlite_antrian_loket')->select('noantrian')->where('type', 'CS')->where('postdate', date('Y-m-d'))->desc('start_time')->oneArray();
            $noantrian = 0;
            if(!empty($get_antrian['noantrian'])) {
              $noantrian = $get_antrian['noantrian'];
            }

            //$antriloket = $this->db('antrics')->oneArray();
            //$tcounter = $antriloket['antrian'];
            $antriloket = $this->settings->get('anjungan.panggil_cs_nomor');
            $tcounter = $antriloket;
            $_tcounter = 1;
            if(!empty($tcounter)) {
              $_tcounter = $tcounter + 1;
            }
            if(isset($_GET['loket'])) {
              $this->db('mlite_antrian_loket')
                ->where('type', 'CS')
                ->where('noantrian', $tcounter)
                ->where('postdate', date('Y-m-d'))
                ->save(['end_time' => date('H:i:s')]);
              /*$this->db()->pdo()->exec("DELETE FROM `antrics`");
              $this->db('antrics')->save([
                'loket' => $_GET['loket'],
                'antrian' => $_tcounter
              ]);*/
              $this->db('mlite_settings')->where('module', 'anjungan')->where('field', 'panggil_cs')->save(['value' => $_GET['loket']]);
              $this->db('mlite_settings')->where('module', 'anjungan')->where('field', 'panggil_cs_nomor')->save(['value' => $_tcounter]);
            }
            if(isset($_GET['antrian'])) {
              /*$this->db()->pdo()->exec("DELETE FROM `antrics`");
              $this->db('antrics')->save([
                'loket' => $_GET['reset'],
                'antrian' => $_GET['antrian']
              ]);*/
              $this->db('mlite_settings')->where('module', 'anjungan')->where('field', 'panggil_cs')->save(['value' => $_GET['reset']]);
              $this->db('mlite_settings')->where('module', 'anjungan')->where('field', 'panggil_cs_nomor')->save(['value' => $_GET['antrian']]);
            }
            $hitung_antrian = $this->db('mlite_antrian_loket')
              ->where('type', 'CS')
              ->like('postdate', date('Y-m-d'))
              ->toArray();
            $counter = strlen($tcounter);
            $xcounter = [];
            for($i=0;$i<$counter;$i++){
              $xcounter[] = '<audio id="suarabel'.$i.'" src="{?=url()?}/plugins/anjungan/suara/'.substr($tcounter,$i,1).'.wav" ></audio>';
            };

            $content = $this->draw('display.antrian.loket.html', [
              'title' => $title,
              'logo' => $logo,
              'powered' => 'Powered by <a href="https://basoro.org/">KhanzaLITE</a>',
              'username' => $username,
              'tanggal' => $tanggal,
              'show' => $show,
              'loket' => $loket,
              'namaloket' => 'b',
              'panggil_loket' => 'panggil_cs',
              'antrian' => $tcounter,
              'hitung_antrian' => $hitung_antrian,
              'xcounter' => $xcounter,
              'noantrian' =>$noantrian,
              'display' => $display
            ]);
          break;
        }

        $assign = [
            'title' => $this->settings->get('settings.nama_instansi'),
            'desc' => $this->settings->get('settings.alamat'),
            'content' => $content
        ];

        $this->setTemplate("canvas.html");

        $this->tpl->set('page', ['title' => $assign['title'], 'desc' => $assign['desc'], 'content' => $assign['content']]);

        //exit();
    }

    public function getDisplayAntrianLaboratorium()
    {
        $logo  = $this->settings->get('settings.logo');
        $title = 'Display Antrian Laboratorium';
        $display = $this->_resultDisplayAntrianLaboratorium();

        $_username = $this->core->getUserInfo('fullname', null, true);
        $tanggal       = getDayIndonesia(date('Y-m-d')).', '.dateIndonesia(date('Y-m-d'));
        $username      = !empty($_username) ? $_username : $this->core->getUserInfo('username');

        $content = $this->draw('display.antrian.laboratorium.html', [
          'logo' => $logo,
          'title' => $title,
          'powered' => 'Powered by <a href="https://basoro.org/">KhanzaLITE</a>',
          'username' => $username,
          'tanggal' => $tanggal,
          'running_text' => $this->settings->get('anjungan.text_laboratorium'),
          'display' => $display
        ]);

        $assign = [
            'title' => $this->settings->get('settings.nama_instansi'),
            'desc' => $this->settings->get('settings.alamat'),
            'content' => $content
        ];

        $this->setTemplate("canvas.html");

        $this->tpl->set('page', ['title' => $assign['title'], 'desc' => $assign['desc'], 'content' => $assign['content']]);

        //exit();
    }

    public function _resultDisplayAntrianLaboratorium()
    {
        $date = date('Y-m-d');
        $tentukan_hari=date('D',strtotime(date('Y-m-d')));
        $day = array(
          'Sun' => 'AKHAD',
          'Mon' => 'SENIN',
          'Tue' => 'SELASA',
          'Wed' => 'RABU',
          'Thu' => 'KAMIS',
          'Fri' => 'JUMAT',
          'Sat' => 'SABTU'
        );
        $hari=$day[$tentukan_hari];

        $poliklinik = $this->settings('settings', 'laboratorium');
        $rows = $this->db('reg_periksa')
          ->join('pasien', 'pasien.no_rkm_medis=reg_periksa.no_rkm_medis')
          ->where('tgl_registrasi', date('Y-m-d'))
          ->where('kd_poli', $poliklinik)
          ->asc('no_reg')
          ->toArray();

        return $rows;
    }

    public function getAjax()
    {
      $show = isset($_GET['show']) ? $_GET['show'] : "";
      switch($show){
       default:
        break;
        case "tampilloket":
          $result = $this->db('mlite_antrian_loket')->select('noantrian')->where('type', 'Loket')->where('postdate', date('Y-m-d'))->desc('start_time')->oneArray();
        	$noantrian = $result['noantrian'];
        	if($noantrian > 0) {
        		$next_antrian = $noantrian + 1;
        	} else {
        		$next_antrian = 1;
        	}
        	echo '<div id="nomernya" align="center">';
          echo '<h1 class="display-1">';
          echo 'A'.$next_antrian;
          echo '</h1>';
          echo '['.date('Y-m-d').']';
          echo '</div>';
          echo '<br>';
        break;
        case "printloket":
          $result = $this->db('mlite_antrian_loket')->select('noantrian')->where('type', 'Loket')->where('postdate', date('Y-m-d'))->desc('start_time')->oneArray();
        	$noantrian = $result['noantrian'];
        	if($noantrian > 0) {
        		$next_antrian = $noantrian + 1;
        	} else {
        		$next_antrian = 1;
        	}
        	echo '<div id="nomernya" align="center">';
          echo '<h1 class="display-1">';
          echo 'A'.$next_antrian;
          echo '</h1>';
          echo '['.date('Y-m-d').']';
          echo '</div>';
          echo '<br>';
          ?>
          <script>
        	$(document).ready(function(){
        		$("#btnKRM").on('click', function(){
        			$("#formloket").submit(function(){
        				$.ajax({
        					url: "<?php echo url().'/anjungan/ajax?show=simpanloket&noantrian='.$next_antrian; ?>",
        					type:"POST",
        					data:$(this).serialize(),
        					success:function(data){
        						setTimeout('$("#loading").hide()',1000);
        						//window.location.href = "{?=url('anjungan/pasien')?}";
        						}
        					});
        				return false;
        			});
        		});
        	})
        	</script>
          <?php
        break;
        case "simpanloket":
          $this->db('mlite_antrian_loket')
            ->save([
              'kd' => NULL,
              'type' => 'Loket',
              'noantrian' => $_GET['noantrian'],
              'postdate' => date('Y-m-d'),
              'start_time' => date('H:i:s'),
              'end_time' => '00:00:00'
            ]);
          //redirect(url('anjungan/pasien'));
        break;
        case "tampilcs":
          $result = $this->db('mlite_antrian_loket')->select('noantrian')->where('type', 'CS')->where('postdate', date('Y-m-d'))->desc('start_time')->oneArray();
        	$noantrian = $result['noantrian'];
        	if($noantrian > 0) {
        		$next_antrian = $noantrian + 1;
        	} else {
        		$next_antrian = 1;
        	}
        	echo '<div id="nomernya" align="center">';
          echo '<h1 class="display-1">';
          echo 'B'.$next_antrian;
          echo '</h1>';
          echo '['.date('Y-m-d').']';
          echo '</div>';
          echo '<br>';
        break;
        case "printcs":
          $result = $this->db('mlite_antrian_loket')->select('noantrian')->where('type', 'CS')->where('postdate', date('Y-m-d'))->desc('start_time')->oneArray();
        	$noantrian = $result['noantrian'];
        	if($noantrian > 0) {
        		$next_antrian = $noantrian + 1;
        	} else {
        		$next_antrian = 1;
        	}
        	echo '<div id="nomernya" align="center">';
          echo '<h1 class="display-1">';
          echo 'B'.$next_antrian;
          echo '</h1>';
          echo '['.date('Y-m-d').']';
          echo '</div>';
          echo '<br>';
          ?>
          <script>
        	$(document).ready(function(){
        		$("#btnKRMCS").on('click', function(){
        			$("#formcs").submit(function(){
        				$.ajax({
        					url: "<?php echo url().'/anjungan/ajax?show=simpancs&noantrian='.$next_antrian; ?>",
        					type:"POST",
        					data:$(this).serialize(),
        					success:function(data){
        						setTimeout('$("#loading").hide()',1000);
        						window.location.href = "{?=url('anjungan/pasien')?}";
        						}
        					});
        				return false;
        			});
        		});
        	})
        	</script>
          <?php
        break;
        case "simpancs":
          $this->db('mlite_antrian_loket')
            ->save([
              'kd' => NULL,
              'type' => 'CS',
              'noantrian' => $_GET['noantrian'],
              'postdate' => date('Y-m-d'),
              'start_time' => date('H:i:s'),
              'end_time' => '00:00:00'
            ]);
          redirect(url('anjungan/pasien'));
        break;
        case "loket":
          //$antrian = $this->db('antriloket')->oneArray();
          //echo $antrian['loket'];
          echo $this->settings->get('anjungan.panggil_loket');
        break;
        case "antriloket":
          //$antrian = $this->db('antriloket')->oneArray();
          //$antrian = $antrian['antrian'] - 1;
          $antrian = $this->settings->get('anjungan.panggil_loket_nomor') - 1;
          if($antrian == '-1') {
            echo '0';
          } else {
            echo $antrian;
          }
        break;
        case "cs":
          //$antrian = $this->db('antrics')->oneArray();
          //echo $antrian['loket'];
          echo $this->settings->get('anjungan.panggil_cs');
        break;
        case "antrics":
          //$antrian = $this->db('antrics')->oneArray();
          //$antrian = $antrian['antrian'] - 1;
          $antrian = $this->settings->get('anjungan.panggil_cs_nomor') - 1;
          if($antrian == '-1') {
            echo '0';
          } else {
            echo $antrian;
          }
        break;
        case "get-skdp":
          if(!empty($_POST['no_rkm_medis'])){
              $data = array();
              $query = $this->db('skdp_bpjs')
                ->join('dokter', 'dokter.kd_dokter = skdp_bpjs.kd_dokter')
                ->join('booking_registrasi', 'booking_registrasi.tanggal_periksa = skdp_bpjs.tanggal_datang')
                ->join('poliklinik', 'poliklinik.kd_poli = booking_registrasi.kd_poli')
                ->join('pasien', 'pasien.no_rkm_medis = skdp_bpjs.no_rkm_medis')
                ->where('skdp_bpjs.no_rkm_medis', $_POST['no_rkm_medis'])
                ->where('booking_registrasi.kd_poli', $_POST['kd_poli'])
                ->desc('skdp_bpjs.tanggal_datang')
                ->oneArray();
              if(!empty($query)){
                  $data['status'] = 'ok';
                  $data['result'] = $query;
              }else{
                  $data['status'] = 'err';
                  $data['result'] = '';
              }
              echo json_encode($data);
          }
        break;

        case "get-daftar":
          if(!empty($_POST['no_rkm_medis_daftar'])){
              $data = array();
              $query = $this->db('pasien')
                ->where('no_rkm_medis', $_POST['no_rkm_medis_daftar'])
                ->oneArray();
              if(!empty($query)){
                  $data['status'] = 'ok';
                  $data['result'] = $query;
              }else{
                  $data['status'] = 'err';
                  $data['result'] = '';
              }
              echo json_encode($data);
          }
        break;

        case "get-poli":
          if(!empty($_POST['no_rkm_medis'])){
              $data = array();
              if($this->db('reg_periksa')->where('no_rkm_medis', $_POST['no_rkm_medis'])->where('tgl_registrasi', $_POST['tgl_registrasi'])->oneArray()) {
                $data['status'] = 'exist';
                $data['result'] = '';
                echo json_encode($data);
              } else {
                $tanggal = $_POST['tgl_registrasi'];
                $tentukan_hari = date('D',strtotime($tanggal));
                $day = array('Sun' => 'AKHAD', 'Mon' => 'SENIN', 'Tue' => 'SELASA', 'Wed' => 'RABU', 'Thu' => 'KAMIS', 'Fri' => 'JUMAT', 'Sat' => 'SABTU');
                $hari=$day[$tentukan_hari];
                $query = $this->db('jadwal')
                  ->select(['kd_poli' => 'jadwal.kd_poli'])
                  ->select(['nm_poli' => 'poliklinik.nm_poli'])
                  ->select(['jam_mulai' => 'jadwal.jam_mulai'])
                  ->select(['jam_selesai' => 'jadwal.jam_selesai'])
                  ->join('poliklinik', 'poliklinik.kd_poli = jadwal.kd_poli')
                  ->join('dokter', 'dokter.kd_dokter = jadwal.kd_dokter')
                  ->like('jadwal.hari_kerja', $hari)
                  ->toArray();
                if(!empty($query)){
                    $data['status'] = 'ok';
                    $data['result'] = $query;
                }else{
                    $data['status'] = 'err';
                    $data['result'] = '';
                }
                echo json_encode($data);
              }
          }
        break;
        case "get-dokter":
          if(!empty($_POST['kd_poli'])){
              $tanggal = $_POST['tgl_registrasi'];
              $tentukan_hari = date('D',strtotime($tanggal));
              $day = array('Sun' => 'AKHAD', 'Mon' => 'SENIN', 'Tue' => 'SELASA', 'Wed' => 'RABU', 'Thu' => 'KAMIS', 'Fri' => 'JUMAT', 'Sat' => 'SABTU');
              $hari=$day[$tentukan_hari];
              $data = array();
              $result = $this->db('jadwal')
                ->select(['kd_dokter' => 'jadwal.kd_dokter'])
                ->select(['nm_dokter' => 'dokter.nm_dokter'])
                ->select(['kuota' => 'jadwal.kuota'])
                ->join('poliklinik', 'poliklinik.kd_poli = jadwal.kd_poli')
                ->join('dokter', 'dokter.kd_dokter = jadwal.kd_dokter')
                ->where('jadwal.kd_poli', $_POST['kd_poli'])
                ->like('jadwal.hari_kerja', $hari)
                ->oneArray();
              $check_kuota = $this->db('reg_periksa')
                ->select(['count' => 'COUNT(DISTINCT no_rawat)'])
                ->where('kd_poli', $_POST['kd_poli'])
                ->where('tgl_registrasi', $_POST['tgl_registrasi'])
                ->oneArray();
              $curr_count = $check_kuota['count'];
              $curr_kuota = $result['kuota'];
              $online = $curr_kuota/2;
              if($curr_count > $online) {
                $data['status'] = 'limit';
              } else {
                $query = $this->db('jadwal')
                  ->select(['kd_dokter' => 'jadwal.kd_dokter'])
                  ->select(['nm_dokter' => 'dokter.nm_dokter'])
                  ->join('poliklinik', 'poliklinik.kd_poli = jadwal.kd_poli')
                  ->join('dokter', 'dokter.kd_dokter = jadwal.kd_dokter')
                  ->where('jadwal.kd_poli', $_POST['kd_poli'])
                  ->like('jadwal.hari_kerja', $hari)
                  ->toArray();
                if(!empty($query)){
                    $data['status'] = 'ok';
                    $data['result'] = $query;
                }else{
                    $data['status'] = 'err';
                    $data['result'] = '';
                }
                echo json_encode($data);
              }
          }
        break;
        case "get-namapoli":
          //$_POST['kd_poli'] = 'INT';
          if(!empty($_POST['kd_poli'])){
              $data = array();
              $result = $this->db('poliklinik')->where('kd_poli', $_POST['kd_poli'])->oneArray();
              if(!empty($result)){
                  $data['status'] = 'ok';
                  $data['result'] = $result;
              }else{
                  $data['status'] = 'err';
                  $data['result'] = '';
              }
              echo json_encode($data);
          }
        break;
        case "get-namadokter":
          //$_POST['kd_dokter'] = 'DR001';
          if(!empty($_POST['kd_dokter'])){
              $data = array();
              $result = $this->db('dokter')->where('kd_dokter', $_POST['kd_dokter'])->oneArray();
              if(!empty($result)){
                  $data['status'] = 'ok';
                  $data['result'] = $result;
              }else{
                  $data['status'] = 'err';
                  $data['result'] = '';
              }
              echo json_encode($data);
          }
        break;
        case "post-registrasi":
          if(!empty($_POST['no_rkm_medis'])){
              $data = array();
              $date = date('Y-m-d');

              $_POST['no_reg']     = $this->core->setNoReg($_POST['kd_dokter'], $_POST['kd_poli']);
              $_POST['hubunganpj'] = $this->core->getPasienInfo('keluarga', $_POST['no_rkm_medis']);
              $_POST['almt_pj']    = $this->core->getPasienInfo('alamat', $_POST['no_rkm_medis']);
              $_POST['p_jawab']    = $this->core->getPasienInfo('namakeluarga', $_POST['no_rkm_medis']);
              $_POST['stts']       = 'Belum';

              $cek_stts_daftar = $this->db('reg_periksa')->where('no_rkm_medis', $_POST['no_rkm_medis'])->count();
              $_POST['stts_daftar'] = 'Baru';
              if($cek_stts_daftar > 0) {
                $_POST['stts_daftar'] = 'Lama';
              }

              $biaya_reg = $this->db('poliklinik')->where('kd_poli', $_POST['kd_poli'])->oneArray();
              $_POST['biaya_reg'] = $biaya_reg['registrasi'];
              if($_POST['stts_daftar'] == 'Lama') {
                $_POST['biaya_reg'] = $biaya_reg['registrasilama'];
              }

              $cek_status_poli = $this->db('reg_periksa')->where('no_rkm_medis', $_POST['no_rkm_medis'])->where('kd_poli', $_POST['kd_poli'])->count();
              $_POST['status_poli'] = 'Baru';
              if($cek_status_poli > 0) {
                $_POST['status_poli'] = 'Lama';
              }

              $tanggal = new \DateTime($this->core->getPasienInfo('tgl_lahir', $_POST['no_rkm_medis']));
              $today = new \DateTime($date);
              $y = $today->diff($tanggal)->y;
              $m = $today->diff($tanggal)->m;
              $d = $today->diff($tanggal)->d;

              $umur="0";
              $sttsumur="Th";
              if($y>0){
                  $umur=$y;
                  $sttsumur="Th";
              }else if($y==0){
                  if($m>0){
                      $umur=$m;
                      $sttsumur="Bl";
                  }else if($m==0){
                      $umur=$d;
                      $sttsumur="Hr";
                  }
              }
              $_POST['umurdaftar'] = $umur;
              $_POST['sttsumur'] = $sttsumur;
              $_POST['status_lanjut']   = 'Ralan';
              $_POST['kd_pj']           = $this->settings->get('anjungan.carabayar_umum');
              $_POST['status_bayar']    = 'Belum Bayar';
              $_POST['no_rawat'] = $this->core->setNoRawat();
              $_POST['jam_reg'] = date('H:i:s');

              $query = $this->db('reg_periksa')->save($_POST);

              $result = $this->db('reg_periksa')
                ->select('reg_periksa.no_rkm_medis')
                ->select('pasien.nm_pasien')
                ->select('pasien.alamat')
                ->select('reg_periksa.tgl_registrasi')
                ->select('reg_periksa.jam_reg')
                ->select('reg_periksa.no_rawat')
                ->select('reg_periksa.no_reg')
                ->select('poliklinik.nm_poli')
                ->select('dokter.nm_dokter')
                ->select('reg_periksa.status_lanjut')
                ->select('penjab.png_jawab')
                ->join('poliklinik', 'poliklinik.kd_poli = reg_periksa.kd_poli')
                ->join('dokter', 'dokter.kd_dokter = reg_periksa.kd_dokter')
                ->join('penjab', 'penjab.kd_pj = reg_periksa.kd_pj')
                ->join('pasien', 'pasien.no_rkm_medis = reg_periksa.no_rkm_medis')
                ->where('reg_periksa.tgl_registrasi', $_POST['tgl_registrasi'])
                ->where('reg_periksa.no_rkm_medis', $_POST['no_rkm_medis'])
                ->oneArray();

              if(!empty($result)){
                  $data['status'] = 'ok';
                  $data['result'] = $result;
              }else{
                  $data['status'] = 'err';
                  $data['result'] = '';
              }
              echo json_encode($data);
          }
        break;
      }
      exit();
    }

    public function getPresensi()
    {

      $title = 'Presensi Pegawai';
      $logo  = $this->settings->get('settings.logo');

      $tanggal       = getDayIndonesia(date('Y-m-d')).', '.dateIndonesia(date('Y-m-d'));

      $content = $this->draw('presensi.html', [
        'title' => $title,
        'notify' => $this->core->getNotify(),
        'logo' => $logo,
        'powered' => 'Powered by <a href="https://basoro.org/">KhanzaLITE</a>',
        'tanggal' => $tanggal,
        'running_text' => $this->settings->get('anjungan.text_poli'),
        'jam_jaga' => $this->db('jam_jaga')->group('jam_masuk')->toArray()
      ]);

      $assign = [
          'title' => $this->settings->get('settings.nama_instansi'),
          'desc' => $this->settings->get('settings.alamat'),
          'content' => $content
      ];

      $this->setTemplate("canvas.html");

      $this->tpl->set('page', ['title' => $assign['title'], 'desc' => $assign['desc'], 'content' => $assign['content']]);
    }

    public function getGeolocation()
    {

      $idpeg = $this->db('barcode')->where('barcode', $this->core->getUserInfo('username', null, true))->oneArray();

      if(isset($_GET['lat'], $_GET['lng'])) {
          if(!$this->db('mlite_geolocation_presensi')->where('id', $idpeg['id'])->where('tanggal', date('Y-m-d'))->oneArray()) {
              $this->db('mlite_geolocation_presensi')
                ->save([
                  'id' => $idpeg['id'],
                  'tanggal' => date('Y-m-d'),
                  'latitude' => $_GET['lat'],
                  'longitude' => $_GET['lng']
              ]);
          }
      }

      exit();
    }

    public function getUpload()
    {
      if ($photo = isset_or($_FILES['webcam']['tmp_name'], false)) {
          $img = new \Systems\Lib\Image;
          if ($img->load($photo)) {
              if ($img->getInfos('width') < $img->getInfos('height')) {
                  $img->crop(0, 0, $img->getInfos('width'), $img->getInfos('width'));
              } else {
                  $img->crop(0, 0, $img->getInfos('height'), $img->getInfos('height'));
              }

              if ($img->getInfos('width') > 512) {
                  $img->resize(512, 512);
              }
              $gambar = uniqid('photo').".".$img->getInfos('type');
          }

          if (isset($img) && $img->getInfos('width')) {

              $img->save(WEBAPPS_PATH."/presensi/".$gambar);

              $urlnya         = WEBAPPS_URL.'/presensi/'.$gambar;
              $barcode        = $_GET['barcode'];

              $idpeg          = $this->db('barcode')->where('barcode', $barcode)->oneArray();
              $jam_jaga       = $this->db('jam_jaga')->join('pegawai', 'pegawai.departemen = jam_jaga.dep_id')->where('pegawai.id', $idpeg['id'])->where('jam_jaga.shift', $_GET['shift'])->oneArray();
              $jadwal_pegawai = $this->db('jadwal_pegawai')->where('id', $idpeg['id'])->where('h'.date('j'), $_GET['shift'])->oneArray();

              $set_keterlambatan  = $this->db('set_keterlambatan')->toArray();
              $toleransi      = $set_keterlambatan['toleransi'];
              $terlambat1     = $set_keterlambatan['terlambat1'];
              $terlambat2     = $set_keterlambatan['terlambat2'];

              $valid = $this->db('rekap_presensi')->where('id', $idpeg['id'])->where('shift', $jam_jaga['shift'])->like('jam_datang', '%'.date('Y-m-d').'%')->oneArray();

              if($valid){
                  $this->notify('failure', 'Anda sudah presensi untuk tanggal '.date('Y-m-d'));
              //}elseif((!empty($idpeg['id']))&&(!empty($jam_jaga['shift']))&&($jadwal_pegawai)&&(!$valid)) {
              }elseif((!empty($idpeg['id']))) {
                  $cek = $this->db('temporary_presensi')->where('id', $idpeg['id'])->oneArray();

                  if(!$cek){
                      if(empty($urlnya)){
                          $this->notify('failure', 'Pilih shift dulu...!!!!');
                      }else{

                          $status = 'Tepat Waktu';

                          if((strtotime(date('Y-m-d H:i:s'))-strtotime(date('Y-m-d').' '.$jam_jaga['jam_masuk']))>($toleransi*60)) {
                            $status = 'Terlambat Toleransi';
                          }
                          if((strtotime(date('Y-m-d H:i:s'))-strtotime(date('Y-m-d').' '.$jam_jaga['jam_masuk']))>($terlambat1*60)) {
                            $status = 'Terlambat I';
                          }
                          if((strtotime(date('Y-m-d H:i:s'))-strtotime(date('Y-m-d').' '.$jam_jaga['jam_masuk']))>($terlambat2*60)) {
                            $status = 'Terlambat II';
                          }

                          if(strtotime(date('Y-m-d H:i:s'))-(date('Y-m-d').' '.$jam_jaga['jam_masuk'])>($toleransi*60)) {
                            $awal  = new \DateTime(date('Y-m-d').' '.$jam_jaga['jam_masuk']);
                            $akhir = new \DateTime();
                            $diff = $akhir->diff($awal,true); // to make the difference to be always positive.
                            $keterlambatan = $diff->format('%H:%I:%S');

                          }

                          $insert = $this->db('temporary_presensi')
                            ->save([
                              'id' => $idpeg['id'],
                              'shift' => $jam_jaga['shift'],
                              'jam_datang' => date('Y-m-d H:i:s'),
                              'jam_pulang' => NULL,
                              'status' => $status,
                              'keterlambatan' => $keterlambatan,
                              'durasi' => '',
                              'photo' => $urlnya
                            ]);

                          if($insert) {
                            $this->notify('success', 'Presensi Masuk jam '.$jam_jaga['jam_masuk'].' '.$status.' '.$keterlambatan);
                          }
                      }
                  }elseif($cek){

                      $status = $cek['status'];
                      if((strtotime(date('Y-m-d H:i:s'))-strtotime(date('Y-m-d').' '.$jam_jaga['jam_pulang']))<0) {
                        $status = $cek['status'].' & PSW';
                      }

                      $awal  = new \DateTime($cek['jam_datang']);
                      $akhir = new \DateTime();
                      $diff = $akhir->diff($awal,true); // to make the difference to be always positive.
                      $durasi = $diff->format('%H:%I:%S');

                      $ubah = $this->db('temporary_presensi')
                        ->where('id', $idpeg['id'])
                        ->save([
                          'jam_pulang' => date('Y-m-d H:i:s'),
                          'status' => $status,
                          'durasi' => $durasi
                        ]);

                      if($ubah) {
                          $presensi = $this->db('temporary_presensi')->where('id', $cek['id'])->oneArray();
                          $insert = $this->db('rekap_presensi')
                            ->save([
                              'id' => $presensi['id'],
                              'shift' => $presensi['shift'],
                              'jam_datang' => $presensi['jam_datang'],
                              'jam_pulang' => $presensi['jam_pulang'],
                              'status' => $presensi['status'],
                              'keterlambatan' => $presensi['keterlambatan'],
                              'durasi' => $presensi['durasi'],
                              'keterangan' => '-',
                              'photo' => $presensi['photo']
                            ]);
                          if($insert) {
                              $this->notify('success', 'Presensi pulang telah disimpan');
                              $this->db('temporary_presensi')->where('id', $cek['id'])->delete();
                          }
                      }
                  }
              }else{
                  $this->notify('failure', 'ID Pegawai atau jadwal shift tidak sesuai. Silahkan pilih berdasarkan shift departemen anda!');
              }
          }
      }
      //echo 'Upload';
      exit();
    }

    public function getDisplayBed()
    {
        $title = 'Display Bed Management';
        $logo  = $this->settings->get('settings.logo');
        $display = $this->_resultDisplayBed();

        $_username = $this->core->getUserInfo('fullname', null, true);
        $tanggal       = getDayIndonesia(date('Y-m-d')).', '.dateIndonesia(date('Y-m-d'));
        $username      = !empty($_username) ? $_username : $this->core->getUserInfo('username');

        $content = $this->draw('display.bed.html', [
          'title' => $title,
          'logo' => $logo,
          'powered' => 'Powered by <a href="https://basoro.org/">KhanzaLITE</a>',
          'username' => $username,
          'tanggal' => $tanggal,
          'running_text' => $this->settings->get('anjungan.text_poli'),
          'display' => $display
        ]);

        $assign = [
            'title' => $this->settings->get('settings.nama_instansi'),
            'desc' => $this->settings->get('settings.alamat'),
            'content' => $content
        ];

        $this->setTemplate("canvas.html");

        $this->tpl->set('page', ['title' => $assign['title'], 'desc' => $assign['desc'], 'content' => $assign['content']]);

    }

    public function _resultDisplayBed()
    {
        $query = $this->db()->pdo()->prepare("SELECT a.nm_bangsal, b.kelas , a.kd_bangsal FROM bangsal a, kamar b WHERE a.kd_bangsal = b.kd_bangsal AND b.statusdata = '1' GROUP BY b.kd_bangsal , b.kelas");
        $query->execute();
        $rows = $query->fetchAll(\PDO::FETCH_ASSOC);;

        $result = [];
        if (count($rows)) {
            foreach ($rows as $row) {
                $row['kosong'] = $this->db('kamar')
                  ->select(['jumlah' => 'COUNT(kamar.status)'])
                  ->join('bangsal', 'bangsal.kd_bangsal = kamar.kd_bangsal')
                  ->where('bangsal.kd_bangsal', $row['kd_bangsal'])
                  ->where('kamar.kelas',$row['kelas'])
                  ->where('kamar.status','KOSONG')
                  ->where('kamar.statusdata','1')
                  ->group(array('kamar.kd_bangsal','kamar.kelas'))
                  ->oneArray();
                $row['isi'] = $this->db('kamar')
                  ->select(['jumlah' => 'COUNT(kamar.status)'])
                  ->join('bangsal', 'bangsal.kd_bangsal = kamar.kd_bangsal')
                  ->where('bangsal.kd_bangsal', $row['kd_bangsal'])
                  ->where('kamar.kelas',$row['kelas'])
                  ->where('kamar.status','ISI')
                  ->where('kamar.statusdata','1')
                  ->group(array('kamar.kd_bangsal','kamar.kelas'))
                  ->oneArray();
                $result[] = $row;
            }
        }

        return $result;
    }

}
