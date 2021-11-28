<?php

namespace Plugins\Master\Src;

use Systems\Lib\QueryWrapper;

class Dokter
{

    protected function db($table)
    {
        return new QueryWrapper($table);
    }

    public function getIndex()
    {

      $totalRecords = $this->db('dokter')
        ->select('kd_dokter')
        ->toArray();
      $offset         = 10;
      $return['halaman']    = 1;
      $return['jml_halaman']    = ceil(count($totalRecords) / $offset);
      $return['jumlah_data']    = count($totalRecords);

      $return['list'] = $this->db('dokter')
        ->join('spesialis', 'spesialis.kd_sps=dokter.kd_sps')
        ->desc('kd_dokter')
        ->limit(10)
        ->toArray();

      return $return;

    }

    public function anyForm()
    {
        $return['pegawai'] = $this->db('pegawai')->toArray();
        $return['gol_drh'] = ['-','A','B','O','AB'];
        $return['agama'] = ['ISLAM', 'KRISTEN', 'PROTESTAN', 'HINDU', 'BUDHA', 'KONGHUCU', 'KEPERCAYAAN'];
        $return['stts_nikah'] = ['SINGLE','MENIKAH','JANDA','DUDHA','JOMBLO'];
        $return['spesialis'] = $this->db('spesialis')->toArray();
        if (isset($_POST['kd_dokter'])){
          $return['form'] = $this->db('dokter')->where('kd_dokter', $_POST['kd_dokter'])->oneArray();
        } else {
          $return['form'] = [
            'kd_dokter' => '',
            'nm_dokter' => '',
            'jk' => '',
            'tmp_lahir' => '',
            'tgl_lahir' => '',
            'gol_drh' => '',
            'agama' => '',
            'almt_tgl' => '',
            'no_telp' => '',
            'stts_nikah' => '',
            'kd_sps' => '',
            'alumni' => '',
            'no_ijn_praktek' => '',
            'status' => ''
          ];
        }

        return $return;
    }

    public function anyDisplay()
    {

        $perpage = '10';
        $totalRecords = $this->db('dokter')
          ->select('kd_dokter')
          ->toArray();
        $offset         = 10;
        $return['halaman']    = 1;
        $return['jml_halaman']    = ceil(count($totalRecords) / $offset);
        $return['jumlah_data']    = count($totalRecords);

        $return['list'] = $this->db('dokter')
          ->join('spesialis', 'spesialis.kd_sps=dokter.kd_sps')
          ->desc('kd_dokter')
          ->offset(0)
          ->limit($perpage)
          ->toArray();

        if(isset($_POST['cari'])) {
          $return['list'] = $this->db('dokter')
            ->join('spesialis', 'spesialis.kd_sps=dokter.kd_sps')
            ->like('kd_dokter', '%'.$_POST['cari'].'%')
            ->orLike('nm_dokter', '%'.$_POST['cari'].'%')
            ->desc('kd_dokter')
            ->offset(0)
            ->limit($perpage)
            ->toArray();
          $jumlah_data = count($return['list']);
          $jml_halaman = ceil($jumlah_data / $offset);
        }
        if(isset($_POST['halaman'])){
          $offset     = (($_POST['halaman'] - 1) * $perpage);
          $return['list'] = $this->db('dokter')
            ->join('spesialis', 'spesialis.kd_sps=dokter.kd_sps')
            ->desc('kd_dokter')
            ->offset($offset)
            ->limit($perpage)
            ->toArray();
          $return['halaman'] = $_POST['halaman'];
        }

        return $return;
    }

    public function postSave()
    {
      if (!$this->db('dokter')->where('kd_dokter', $_POST['kd_dokter'])->oneArray()) {
        $query = $this->db('dokter')->save($_POST);
      } else {
        $query = $this->db('dokter')->where('kd_dokter', $_POST['kd_dokter'])->save($_POST);
      }
      return $query;
    }

    public function postHapus()
    {
      return $this->db('dokter')->where('kd_dokter', $_POST['kd_dokter'])->delete();
    }

}
