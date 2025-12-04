<?php
defined('BASEPATH') or exit('No direct script access allowed');

class pegawai extends CI_Controller
{
  public function __construct()
  {
    parent::__construct();
    if ($this->session->userdata('masuk_user') != TRUE) {
      $url = base_url('auth');
      redirect($url);
    };
    $this->load->library('form_validation');
    $this->load->model('User_model');
    $this->load->model('Admin_model');
  }

  public function index()
  {
    $data['title'] = 'Dashboard';
    // mengambil data user berdasarkan email yang ada di session
    $data['user'] = $this->db->get_where('user', ['email' => $this->session->userdata('email')])->row_array();
    $data['konfirmasi_absen'] = $this->User_model->konfirmasiAbsenById($data['user']['id']);




    $this->load->view('backend/f_template/header', $data);
    $this->load->view('backend/f_template/topbar', $data);
    $this->load->view('backend/f_template/sidebar', $data);
    $this->load->view('backend/user/dashboard/index', $data);
    $this->load->view('backend/f_template/footer');
  }

  public function visi_misi()
  {
    $data['title'] = 'Dashboard';
    // mengambil data user berdasarkan email yang ada di session
    $data['user'] = $this->db->get_where('user', ['email' => $this->session->userdata('email')])->row_array();

    $this->load->view('backend/f_template/header', $data);
    $this->load->view('backend/f_template/topbar', $data);
    $this->load->view('backend/f_template/sidebar', $data);
    $this->load->view('backend/user/dashboard/visimisi', $data);
    $this->load->view('backend/f_template/footer');
  }

  public function sejarah()
  {
    $data['title'] = 'Dashboard';
    // mengambil data user berdasarkan email yang ada di session
    $data['user'] = $this->db->get_where('user', ['email' => $this->session->userdata('email')])->row_array();

    $this->load->view('backend/f_template/header', $data);
    $this->load->view('backend/f_template/topbar', $data);
    $this->load->view('backend/f_template/sidebar', $data);

    $this->load->view('backend/user/dashboard/sejarah', $data);
    $this->load->view('backend/f_template/footer');
  }
  public function edit_profil($id)
  {
    $data['title'] = 'Edit Profil';
    // mengambil data user berdasarkan email yang ada di session
    $data['user'] = $this->db->get_where('user', ['email' => $this->session->userdata('email')])->row_array();
    $nama = $this->input->post('nama', true);


    //foto dan ktp 
    $upload_image = $_FILES['userfilefoto']['name'];
    if ($upload_image) {
      $config['upload_path']          = './gambar/pegawai/';
      $config['allowed_types']        = 'gif|jpg|png|PNG';
      $config['max_size']             = 10000;
      $config['max_width']            = 10000;
      $config['max_height']           = 10000;
      $this->load->library('upload', $config);

      if ($this->upload->do_upload('userfilefoto')) {
        $new_image = $this->upload->data('file_name');
        $new_image1 = $this->upload->data('file_name');
        $data = $this->db->set('foto', $new_image);
      } else {
        echo $this->upload->display_errors();
      }
      $data = [
        "nama_pegawai" => $nama,

      ];
      $this->db->where('id_user', $id);
      $this->db->update('tb_pegawai', $data);

      $data1 = $this->db->set('image', $new_image1);

      $data1 = [
        "name" => $nama,

      ];
      $this->db->where('id', $id);
      $this->db->update('user', $data1);


      $this->session->set_flashdata('flash', 'Berhasil diperbarui');
      redirect('pegawai');
    } else {
      $data = [
        "nama_pegawai" => $nama,

      ];
      $this->db->where('id_user', $id);
      $this->db->update('tb_pegawai', $data);
      $data1 = [
        "name" => $nama,

      ];
      $this->db->where('id', $id);
      $this->db->update('user', $data1);
      $this->session->set_flashdata('flash', 'Berhasil diperbarui');
      redirect('pegawai');
    }
    // 
  }
  public function edit_password($id)
  {
    $data['title'] = 'Edit Password';
    // mengambil data user berdasarkan email yang ada di session
    $data['user'] = $this->db->get_where('user', ['email' => $this->session->userdata('email')])->row_array();
    $password_lama = $this->input->post('password_lama', true);
    $password_baru = $this->input->post('password_baru', true);
    $password_baru1 = $this->input->post('password_baru1', true);
    if (password_verify($password_lama, $data['user']['password'])) {
      if ($password_baru == $password_baru1) {
        $data = [
          "password" => password_hash($password_baru, PASSWORD_DEFAULT),
        ];
        $this->db->where('id', $id);
        $this->db->update('user', $data);
        $this->session->set_flashdata('flash', 'Password Berhasil Diubah!');
        redirect('pegawai');
      } else {
        $this->session->set_flashdata('flash', 'Konfirmasi Password Berbeda!');
        redirect('pegawai');
      }
    } else {
      $this->session->set_flashdata('flash', 'Password Lama Salah!');
      redirect('pegawai');
    }
  }

  public function absen_harian()
  {
    $data['title'] = 'Dashboard';
    // mengambil data user berdasarkan email yang ada di session
    $data['user'] = $this->db->get_where('user', ['email' => $this->session->userdata('email')])->row_array();
    $data['pegawai'] = $this->User_model->PegawaiById($data['user']['id']);
    $isi_lembur = $this->User_model->cek_lemburById($data['pegawai']['id_pegawai']);
    $isi = $this->User_model->AbsenByStatusId($data['user']['id']);


    if ($isi_lembur) {
      $data['cek_lembur'] = $isi_lembur;
    } else {
      $data['cek_lembur']['id_pegawai'] = "";
    }

    if ($isi) {
      $data['absen'] = $isi;
    } else {
      $data['absen']['keterangan'] = "";
      $data['absen']['id_pegawai'] = "peg";
    }



    $this->load->view('backend/f_template/header', $data);
    $this->load->view('backend/f_template/topbar', $data);
    $this->load->view('backend/f_template/sidebar', $data);
    $this->load->view('backend/user/absensekarang/index', $data);
    $this->load->view('backend/f_template/footer');
  }


  public function konfirmasi_absen()
  {
    $data['title'] = 'Konfirmasi Absen';
    // mengambil data user berdasarkan email yang ada di session
    $data['user'] = $this->db->get_where('user', ['email' => $this->session->userdata('email')])->row_array();
    $data['konfirmasi_absen'] = $this->User_model->konfirmasiAbsenById($data['user']['id']);

    $this->load->view('backend/f_template/header', $data);
    $this->load->view('backend/f_template/topbar', $data);
    $this->load->view('backend/f_template/sidebar', $data);
    $this->load->view('backend/user/konfirmasi_absen/index', $data);
    $this->load->view('backend/f_template/footer');
  }

  public function absen_bulanan()
  {
    $data['title'] = 'Absen Bulanan';
    // mengambil data user berdasarkan email yang ada di session
    $data['user'] = $this->db->get_where('user', ['email' => $this->session->userdata('email')])->row_array();
    $thn = $this->input->post('th');
    $bln = $this->input->post('bln');
    $data['blnselected'] = $bln;
    $data['thnselected'] = $thn;
    $data['pegawai'] = $this->db->get_where('tb_pegawai', ['id_user' => $data['user']['id']])->row_array();
    $id_peg = $data['pegawai']['id_pegawai'];
    // $data['petugas'] = $this->db->get_where('user')->result_array();
    // 
    $data['list_th'] = $this->Admin_model->getTahun();
    $data['list_bln'] = $this->Admin_model->getBln();
    $data['pegawai'] = $this->db->get_where('tb_pegawai', ['id_user' => $data['user']['id']])->row_array();
    $isi = $this->Admin_model->getAllpegawaiByid($id_peg);
    if ($isi == null) {
      $data['detail_pegawai']['nama_pegawai'] = '';
      $data['detail_pegawai']['namjab'] = '';
    } else {
      $data['detail_pegawai'] = $isi;
    }


    if ($bln < 10) {
      $thnpilihan1 = $thn . '-' . '0' . $bln . '-' . '01';
      $thnpilihan2 = $thn . '-' . '0' . $bln . '-' . '31';
    } else {
      $thnpilihan1 = $thn . '-' . $bln . '-' . '01';
      $thnpilihan2 = $thn . '-' . $bln . '-' . '31';
    }
    // 
    $data['absen'] = $this->Admin_model->getAllAbsen($thnpilihan1, $thnpilihan2, $id_peg);

    $data['blnnya'] = $bln;
    $data['thn'] = $thn;

    $this->load->view('backend/f_template/header', $data);
    $this->load->view('backend/f_template/topbar', $data);
    $this->load->view('backend/f_template/sidebar', $data);
    $this->load->view('backend/user/absen_bulanan/index', $data);
    $this->load->view('backend/f_template/footer', $data);
  }
  public function detail_absen($id)
  {
    $data['title'] = 'Detail Absensi';
    // mengambil data user berdasarkan email yang ada di session
    $data['user'] = $this->db->get_where('user', ['email' => $this->session->userdata('email')])->row_array();
    $data['detail_absensi'] = $this->Admin_model->getDetailAbsen($id);

    $this->load->view('backend/f_template/header', $data);
    $this->load->view('backend/f_template/topbar', $data);
    $this->load->view('backend/f_template/sidebar', $data);
    $this->load->view('backend/user/absen_bulanan/detail', $data);
    $this->load->view('backend/f_template/footer', $data);
  }

  public function ambil_absen()
  {
    // ... (Load user, title, dll sama seperti sebelumnya) ...

    date_default_timezone_set('Asia/Jakarta');
    $tgl_skrng = date('Y-m-d');
    $waktu = date('H:i:s');

    // --- BATAS WAKTU ---
    $jam_buka = '06:00:00';
    $jam_batas = '08:10:00';

    // 1. Cek: Belum waktunya absen
    if ($waktu < $jam_buka) {
      $this->session->set_flashdata('s_absenggl', 'Absen Gagal. Absen baru dibuka pukul 06:00.');
      redirect('pegawai/absen-harian');
      return;
    }

    $lat = $this->input->post('lat', true);
    $long = $this->input->post('long', true);
    $id_peg = $this->input->post('id_peg', true);

    // Koordinat Kantor
    $lat_kantor = -3.042176;
    $long_kantor = 104.842945;
    $jarak = $this->distance($lat, $long, $lat_kantor, $long_kantor);

    if ($jarak <= 100) {
      // ... (Kode upload foto selfie Anda, copy paste yang lama) ...

      // 2. LOGIKA HITUNG DURASI TELAT
      $kode_keterangan = 1; // 1 = Absen Masuk
      $status_kehadiran = 0; // 0 = On Time
      $durasi_telat = null; // Default kosong jika tidak telat

      if ($waktu > $jam_batas) {
        $status_kehadiran = 1; // 1 = Terlambat

        // Hitung selisih
        $awal  = new DateTime($jam_batas); // 08:10:00
        $akhir = new DateTime($waktu);     // Jam user absen
        $diff  = $awal->diff($akhir);

        // Format durasi menjadi Jam:Menit:Detik (Contoh: 01:05:00)
        $durasi_telat = $diff->format('%H:%I:%S');

        $durasi_format = $diff->format('%H Jam %I Menit %S Detik');
        $pesan_flash   = 'Absen Masuk Berhasil (Terlambat ' . $durasi_format . ').';
      } else {
        $pesan_flash = 'Absen Masuk Anda Berhasil.';
      }

      $data_insert = [
        "id_pegawai"   => $id_peg,
        "tanggal"      => $tgl_skrng,
        "waktu"        => $waktu,
        "keterangan"   => $kode_keterangan,
        "status"       => $status_kehadiran,
        "durasi_telat" => $durasi_telat, // <--- DATA DISIMPAN DI SINI
        // Jangan lupa masukkan kolom foto jika ada
      ];

      // Jika ada foto dari upload logic
      if (isset($new_image)) {
        $data_insert['foto_selfie_masuk'] = $new_image;
      }

      $this->db->insert('tb_presents', $data_insert);
      $this->session->set_flashdata('flash', $pesan_flash);
      redirect('pegawai/absen-harian');
    } else {
      $this->session->set_flashdata('s_absenggl', 'Absen Gagal, Anda Terlalu Jauh Dari Kantor');
      redirect('pegawai/absen-harian');
    }
  }

  public function ambil_absen_pulang()
  {
    $data['title'] = 'Dashboard';
    $data['user'] = $this->db->get_where('user', ['email' => $this->session->userdata('email')])->row_array();

    $lat = $this->input->post('lat', true);
    $long = $this->input->post('long', true);
    $id_peg = $this->input->post('id_peg', true);
    $id_presents = $this->input->post('id_presents', true);
    $keterangan = $this->input->post('keterangan', true);

    // Koordinat Kantor
    $lat_kantor = -2.9795731113284303;
    $long_kantor = 104.73111003716011;

    // Setting Waktu
    date_default_timezone_set('Asia/Jakarta');
    $tgl_skrng = date('Y-m-d');
    $waktu = date('H:i:s');
    $jam_sekarang = date('H'); // Mengambil jam saja (format 00-23)

    // --- LOGIKA BARU: Cek apakah sudah jam 5 sore (17:00) ke atas ---
    if ($jam_sekarang >= 17) {

      // Cek Jarak
      $jarak = $this->distance($lat, $long, $lat_kantor, $long_kantor);

      // Toleransi jarak (misal 10 km / 10000 meter seperti di kode asli, 
      // walau biasanya absen radiusnya meter, misal 0.1 atau 100m)
      if ($jarak <= 100) {

        // Siapkan array data update dasar
        $data_update = [
          "id_pegawai" => $id_peg,
          "tanggal" => $tgl_skrng,
          "waktu" => $waktu, // Pastikan di DB kolom ini untuk jam pulang
          "keterangan" => $keterangan,
          "status" => 2, // Biasanya status 2 menandakan sudah pulang (sesuaikan dengan logic Anda)
        ];

        // Upload foto selfie
        $upload_image = $_FILES['userfotoselfie']['name'];
        if ($upload_image) {
          $config['upload_path']          = './gambar/Absensi/';
          $config['allowed_types']        = 'gif|jpg|png|PNG|jpeg';
          $config['max_size']             = 10000; // Sekitar 10MB
          $config['file_name']            = 'pulang_' . time() . '_' . $id_peg; // Rename file agar unik

          $this->load->library('upload', $config);

          if ($this->upload->do_upload('userfotoselfie')) {
            $new_image = $this->upload->data('file_name');
            // Masukkan nama file ke array update
            $data_update['foto_selfie_pulang'] = $new_image;
          } else {
            echo $this->upload->display_errors();
            return; // Hentikan proses jika upload gagal
          }
        }

        // Eksekusi Update ke Database
        $this->db->where('id_presents', $id_presents);
        $this->db->update('tb_presents', $data_update);

        $this->session->set_flashdata('flash', 'Absen Pulang Berhasil');
        redirect('pegawai/absen-harian');
      } else {
        // Jika jarak terlalu jauh
        $this->session->set_flashdata('flash_error', 'Anda Terlalu Jauh Dari Kantor');
        redirect('pegawai/absen-harian');
      }
    } else {
      // --- JIKA BELUM JAM 5 SORE ---
      $this->session->set_flashdata('flash_error', 'Belum waktunya pulang. Absen pulang hanya bisa dilakukan setelah jam 17:00 WIB.');
      redirect('pegawai/absen-harian');
    }
  }

  public function ambil_absen_lembur()
  {
    $data['title'] = 'Dashboard';
    // mengambil data user berdasarkan email yang ada di session
    $data['user'] = $this->db->get_where('user', ['email' => $this->session->userdata('email')])->row_array();

    $lat = $this->input->post('lat', true);
    $long = $this->input->post('long', true);
    $id_peg = $this->input->post('id_peg', true);
    $id_presents = $this->input->post('id_presents', true);
    $keterangan = $this->input->post('keterangan', true);
    $lat_kantor = -2.9795731113284303;
    $long_kantor =   104.73111003716011;
    date_default_timezone_set('Asia/Jakarta');
    $tgl_skrng = date('Y-m-d');
    $waktu =  date('H:i:s');
    //Upload foto selfie
    $jarak = $this->distance($lat, $long, $lat_kantor, $long_kantor);
    if ($jarak <= 100) {
      $upload_image = $_FILES['userfotoselfie']['name'];
      if ($upload_image) {
        $config['upload_path']          = './gambar/Absensi/';
        $config['allowed_types']        = 'gif|jpg|png|PNG|jpeg';
        $config['max_size']             = 10000;
        $config['max_width']            = 10000;
        $config['max_height']           = 10000;
        $this->load->library('upload', $config);

        if ($this->upload->do_upload('userfotoselfie')) {
          $new_image = $this->upload->data('file_name');
          $data = $this->db->set('foto_selfie_pulang', $new_image);
        } else {
          echo $this->upload->display_errors();
        }
      }
      $data = [
        "id_pegawai" => $id_peg,
        "tanggal" => $tgl_skrng,
        "waktu" => $waktu,
        "keterangan" => $keterangan,
      ];

      $this->db->where('id_presents', $id_presents);
      $this->db->update('tb_presents', $data);
      $this->session->set_flashdata('flash', 'Absen Lembur Anda Berhasil Masuk');
      redirect('pegawai/absen-harian');
    } else {
      echo 'Anda Terlalu Jauh Dari Kantor';
    }
  }

  function distance($lat1, $lon1, $lat2, $lon2)
  {
      $earthRadius = 6371000; // meter

      $dLat = deg2rad($lat2 - $lat1);
      $dLon = deg2rad($lon2 - $lon1);

      $a = sin($dLat / 2) * sin($dLat / 2) +
          cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
          sin($dLon / 2) * sin($dLon / 2);

      $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
      $distance = $earthRadius * $c;

      return $distance; // meter
  }



  public function cuti_pegawai()
  {
    $data['title'] = 'Dashboard';
    // mengambil data user berdasarkan email yang ada di session
    $data['user'] = $this->db->get_where('user', ['email' => $this->session->userdata('email')])->row_array();

    $id_peg = $this->input->post('id_peg', true);
    $jenis_izin = $this->input->post('jenisizin', true);
    $keterangan = $this->input->post('penjelasan', true);


    date_default_timezone_set('Asia/Jakarta');
    $tgl_skrng = date('Y-m-d');
    $waktu =  date('H:i:s');
    //Upload foto selfie

    $upload_image = $_FILES['suratsakit']['name'];
    if ($upload_image) {
      $config['upload_path']          = './gambar/Absensi/suratdokter/';
      $config['allowed_types']        = 'gif|jpg|png|PNG|jpeg';
      $config['max_size']             = 10000;
      $config['max_width']            = 10000;
      $config['max_height']           = 10000;
      $this->load->library('upload', $config);

      if ($this->upload->do_upload('suratsakit')) {
        $new_image = $this->upload->data('file_name');
        $data = $this->db->set('foto_selfie_masuk', $new_image);
      } else {
        echo $this->upload->display_errors();
      }
    }
    $data = [
      "id_pegawai" => $id_peg,
      "tanggal" => $tgl_skrng,
      "waktu" => $waktu,
      "keterangan" => $jenis_izin,
      "keterangan_izin" => $keterangan,
    ];

    $this->db->insert('tb_presents', $data);
    $this->session->set_flashdata('flash', 'Izin Anda Akan Diproses');
    redirect('pegawai/absen-harian');
  }


  public function laporan_tpp_bulanan()
  {
    $data['title'] = 'Cetak Payrol Bulanan';

    $data['user'] = $this->db->get_where('user', ['email' => $this->session->userdata('email')])->row_array();
    $dataa['pegawai'] = $this->db->get_where('tb_pegawai', ['id_user' => $data['user']['id']])->row_array();
    $thn = $this->input->post('th');
    $bln = $this->input->post('bln');
    $data['blnselected'] = $bln;
    $data['thnselected'] = $thn;
    $data['pegawai'] = $this->Admin_model->getAllpegawai();


    $data['list_th'] = $this->Admin_model->getTahun();
    $data['list_bln'] = $this->Admin_model->getBln();

    if ($bln < 10) {
      $thnpilihan1 = $thn . '-' . '0' . $bln . '-' . '01';
      $thnpilihan2 = $thn . '-' . '0' . $bln . '-' . '31';
    } else {
      $thnpilihan1 = $thn . '-' . $bln . '-' . '01';
      $thnpilihan2 = $thn . '-' . $bln . '-' . '31';
    }
    // 
    $isi = $this->Admin_model->getAllGajiByDateID($thnpilihan1, $thnpilihan2, $dataa['pegawai']['id_pegawai']);

    if ($isi == null) {

      $data['gaji']['id_pegawai'] = '';
      $data['gaji']['nama_pegawai'] = '';
      $data['gaji']['namjab'] = '';
      $data['gaji']['gaji_pokok'] = '';
      $data['gaji']['gaji_lembur'] = '';
      $data['gaji']['bonus'] = '';
      $data['gaji']['keterangan'] = '';
      $data['gaji']['gaji_bersih'] = '';
    } else {
      $data['gaji'] = $isi;
    }
    $data['blnnya'] = $bln;
    $data['thn'] = $thn;

    $this->load->view('backend/f_template/header', $data);
    $this->load->view('backend/f_template/topbar', $data);
    $this->load->view('backend/f_template/sidebar', $data);
    $this->load->view('backend/user/laporan/laporan_tpp', $data);
    $this->load->view('backend/f_template/footer', $data);
  }

  
  public function detail_laporan_tpp_bulanan($id_pegawai, $bln, $thn)
  {
    $data['title'] = 'detail Laporan Payrol Bulanan';

    $data['user'] = $this->db->get_where('user', ['email' => $this->session->userdata('email')])->row_array();

    $data['pegawai'] = $this->Admin_model->getAllpegawai();
    $data['blnselected'] = $bln;
    $data['thnselected'] = $thn;
    $data['id_pegawai'] = $id_pegawai;

    if ($bln < 10) {
      $thnpilihan1 = $thn . '-' . '0' . $bln . '-' . '01';
      $thnpilihan2 = $thn . '-' . '0' . $bln . '-' . '31';
    } else {
      $thnpilihan1 = $thn . '-' . $bln . '-' . '01';
      $thnpilihan2 = $thn . '-' . $bln . '-' . '31';
    }


    // var_dump($thnpilihan2);
    // die;
    // 
    $data['gaji'] = $this->Admin_model->getAllGajiByDateID($thnpilihan1, $thnpilihan2, $id_pegawai);
    $data['absen'] = $this->Admin_model->getAllLemburPegawaiById($thnpilihan1, $thnpilihan2, $id_pegawai);
    // var_dump($data['gaji']);
    // die;


    $this->load->view('backend/f_template/header', $data);
    $this->load->view('backend/f_template/topbar', $data);
    $this->load->view('backend/f_template/sidebar', $data);
    $this->load->view('backend/user/laporan/detail_laporan_tpp', $data);
    $this->load->view('backend/f_template/footer', $data);
  }
  public function cetak_payrol_pegawai($id_pegawai, $bln, $thn)
  {
    $data['title'] = 'Lembur Bulanan';
    // mengambil data user berdasarkan email yang ada di session

    $data['blnselected'] = $bln;
    $data['thnselected'] = $thn;

    // $data['petugas'] = $this->db->get_where('user')->result_array();
    // 


    if ($bln < 10) {
      $thnpilihan1 = $thn . '-' . '0' . $bln . '-' . '01';
      $thnpilihan2 = $thn . '-' . '0' . $bln . '-' . '31';
    } else {
      $thnpilihan1 = $thn . '-' . $bln . '-' . '01';
      $thnpilihan2 = $thn . '-' . $bln . '-' . '31';
    }
    // 
    $data['gaji'] = $this->Admin_model->getAllGajiByDateID($thnpilihan1, $thnpilihan2, $id_pegawai);
    $data['absen'] = $this->Admin_model->getAllLemburPegawaiById($thnpilihan1, $thnpilihan2, $id_pegawai);
    // var_dump($data['absen']);
    // die;

    $data['blnnya'] = $bln;
    $data['thn'] = $thn;
    $this->load->view('backend/admin/laporan/cetak', $data);
  }
}
