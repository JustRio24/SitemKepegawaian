<!DOCTYPE html>
<html>

<?php
// $this->load->view('backend/template/header');
function rupiah($angka)
{
  $hasil_rupiah = "Rp " . number_format($angka, 0, ',', '.') . ",-";
  return $hasil_rupiah;
}

function nmbulan($angka)
{
  switch ($angka) {
    case 1: return "Januari"; break;
    case 2: return "Februari"; break;
    case 3: return "Maret"; break;
    case 4: return "April"; break;
    case 5: return "Mei"; break;
    case 6: return "Juni"; break;
    case 7: return "Juli"; break;
    case 8: return "Agustus"; break;
    case 9: return "September"; break;
    case 10: return "Oktober"; break;
    case 11: return "November"; break;
    case 12: return "Desember"; break;
  }
}

// --- LOGIKA PERHITUNGAN TAMPILAN ---

// 1. Hitung Durasi Jam Lembur (Baru)
// Rumus: Total Uang Lembur dibagi Rate Overtime Jabatan
$rate_lembur = isset($gaji['overtime']) ? $gaji['overtime'] : 0; 
$total_jam_lembur = 0;

if ($rate_lembur > 0 && $gaji['gaji_lembur'] > 0) {
    // Pembulatan 1 angka di belakang koma (misal 5.5 Jam)
    $total_jam_lembur = round($gaji['gaji_lembur'] / $rate_lembur, 1); 
}

// 2. Hitung Detail Denda
$nilai_denda = isset($gaji['denda']) ? $gaji['denda'] : 0; 
$total_menit_telat = ($nilai_denda > 0) ? ($nilai_denda / 2000) : 0;

// 3. Hitung Dasar Pengenaan Pajak & BPJS
$gaji_kotor_dasar = $gaji['gaji_pokok'] + $gaji['gaji_lembur'];

// 4. Hitung PPh (0.5%)
$pph_persen = 0.5;
$nilai_pph = floor($gaji_kotor_dasar * ($pph_persen / 100));

// 5. Hitung BPJS (2%)
$bpjs_persen = 2;
$nilai_bpjs = floor($gaji_kotor_dasar * ($bpjs_persen / 100));

?>

<head>
  <title>CETAK PAYROL PEGAWAI</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
  <style>
    body { font-family: Arial, sans-serif; }
    .table-bordered { border-color: #000 !important; }
    .potongan-row { color: #dc3545; } /* Warna merah untuk potongan */
  </style>
</head>

<body <?php if ($this->uri->segment(2) === 'cetak-payrol-pegawai') : ?> onload="window.print()" <?php else : endif; ?>>
  <center>
    <table id="" class="table table-bordered" style="width: 100%; max-width: 800px;">
      <h4 class="text-center">..::PT PANCA KARYA UTAMA::..</h4>
      <center>
        <h6 class="text-center"><small>
            Project Management & Construction<br>
            Jl. Sungai Sahang No.3654, Lorok Pakjo, Kec. Ilir Bar. I, Kota Palembang, Sumatera Selatan 30151<br>
            e-mail : pancakaryautamabussiness@gmail.com <br>
            Palembang - Indonesia
          </small>
        </h6>
      </center>
      <hr style="border: 1px solid black;">
      
      <tr height="30px" style="background-color: #f2f2f2;">
        <td colspan=11 class="align-middle">
            <h6><b>DATA PEGAWAI</b></h6>
        </td>
      </tr>
      <tr>
        <td colspan=2 width="200"><b>NO PEGAWAI</b></td>
        <td colspan=4>: <?= strtoupper($gaji['id_pegawai']) ?></td>
      </tr>
      <tr>
        <td colspan=2><b>NAMA PEGAWAI</b></td>
        <td colspan=4>: <?= strtoupper($gaji['nama_pegawai']) ?></td>
      <tr>
        <td colspan=2><b>PERIODE</b></td>
        <td colspan=4>: <?= nmbulan(substr($gaji['periode'], 5, 2)) ?> <?= substr($gaji['periode'], 0, 4) ?></td>
      </tr>
      <tr>
        <td colspan=2><b>JABATAN</b>
        <td colspan=4>: <?= strtoupper($gaji['namjab']) ?></td>
      </tr>

      <tr height="30px" style="background-color: #f2f2f2;">
        <td colspan=11 class="align-middle">
            <h6><b>PENERIMAAN</b></h6>
        </td>
      </tr>

      <tr>
        <th width=44 scope=col class="text-center">1</th>
        <th width=300 scope=col>Gaji Pokok </th>
        <th width=508 scope=col colspan="5" class="text-end"><?= rupiah($gaji['gaji_pokok']) ?></th>
      </tr>
      <tr>
        <th width=44 scope=col class="text-center">2</th>
        <th width=300 scope=col>
            Upah Lembur <br>
            <small style="font-weight: normal; font-size: 11px; color: #555;">(Total Durasi: <?= $total_jam_lembur ?> Jam)</small>
        </th>
        <th width=508 scope=col colspan="5" class="text-end"><?= rupiah($gaji['gaji_lembur']) ?></th>
      </tr>
      <tr>
        <th width=44 scope=col class="text-center">3</th>
        <th width=300 scope=col>Bonus</th>
        <th width=508 scope=col colspan="5" class="text-end"><?= rupiah($gaji['bonus']) ?></th>
      </tr>

      <tr height="30px" style="background-color: #f2f2f2;">
        <td colspan=11 class="align-middle">
            <h6><b>POTONGAN</b></h6>
        </td>
      </tr>
      
      <tr>
        <th width=44 scope=col class="text-center">4</th>
        <th width=300 scope=col>
            Denda Keterlambatan <br>
            <small class="text-muted"><i>(Total Telat: <?= $total_menit_telat ?> Menit)</i></small>
        </th>
        <th width=508 scope=col colspan="5" class="text-end potongan-row"> - <?= rupiah($nilai_denda) ?></th>
      </tr>
      
      <tr>
        <th width=44 scope=col class="text-center">5</th>
        <th width=300 scope=col>Pajak Penghasilan (PPh 21) - <?= $pph_persen ?>%</th>
        <th width=508 scope=col colspan="5" class="text-end potongan-row"> - <?= rupiah($nilai_pph) ?></th>
      </tr>

      <tr>
        <th width=44 scope=col class="text-center">6</th>
        <th width=300 scope=col>BPJS Ketenagakerjaan - <?= $bpjs_persen ?>%</th>
        <th width=508 scope=col colspan="5" class="text-end potongan-row"> - <?= rupiah($nilai_bpjs) ?></th>
      </tr>

      <tr height="50px">
        <td colspan=10 class="align-middle">
          <center>
            <h5 class="align-middle">
                <b>TOTAL GAJI BERSIH (TAKE HOME PAY) : <br>
                <?= rupiah($gaji['gaji_bersih']) ?> 
                </b>
            </h5>
          </center>
        </td>
      </tr>
      
      <tr height="30px" style="background-color: #f2f2f2;">
         <td colspan=11><b>DETAIL KEHADIRAN</b></td>
      </tr>

      <tr>
        <td colspan=4 align='left' valign="top">
            <b>Catatan / Keterangan :</b><br>
            <?= !empty($gaji['keterangan']) ? strtoupper($gaji['keterangan']) : "-" ?>
        </td>
        <td colspan=7>
            <table width="100%" style="border: none;">
                <tr>
                    <td width="50%">Masuk Kerja</td>
                    <td>: <?= $absen['masuk'] ?> Hari</td>
                </tr>
                <tr>
                    <td>Lembur</td>
                    <td>: <?= $total_jam_lembur ?> Jam</td>
                </tr>
                <tr>
                    <td>Sakit</td>
                    <td>: <?= $absen['sakit'] ?> Hari</td>
                </tr>
                <tr>
                    <td>Izin / Alpa</td>
                    <td>: <?= $absen['izin'] ?> Hari</td>
                </tr>
            </table>
        </td>
      </tr>
    </table> 
    <br>

    <table width="625">
      <tr>
        <td width="430"></td>
        <td class="text" align="center">
          Palembang, <?= date('d F Y') ?> <br>
          <b>Penanggung Jawab (HRD)</b>
          <br><br><br><br><br>
          <b>( .......................................... )</b>
        </td>
      </tr>
    </table>
  </center>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
</body>

</html>