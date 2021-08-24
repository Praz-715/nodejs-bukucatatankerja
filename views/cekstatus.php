<?php

$koneksi = mysqli_connect("localhost", "root", "", "bukucatatan");
function query($query){

	global $koneksi;
	$result = mysqli_query($koneksi, $query);
	$rows = [];
	while( $row = mysqli_fetch_assoc($result)){
		$rows[] = $row;
	}
	return $rows;
}
date_default_timezone_set("Asia/Jakarta");
$tanggalupload = query("SELECT DISTINCT DATE_FORMAT(upload, '%d-%m-%Y') as upload FROM harian ORDER BY id DESC");
$hariini = date("Y-m-d");
// var_dump($hariini);
$data_hari_ini = query("SELECT id, lokasi, tipeaset, sn, status, keterangan, DATE_FORMAT(upload, '%d-%m-%Y') as upload FROM harian WHERE DATE(upload) = '$hariini' ORDER BY lokasi ASC");

if(isset($_GET['dari']) AND  isset($_GET['sampai']) ){
    $dari   = $_GET['dari'];
    $sampai = $_GET['sampai'];
    if(strlen($_GET['dari']) > 1 AND strlen($_GET['sampai']) > 1){
        $data_hari_ini = query("SELECT id, lokasi, tipeaset, sn, status, keterangan, DATE_FORMAT(upload, '%d-%m-%Y') as upload FROM harian WHERE DATE_FORMAT(upload, '%d-%m-%Y') BETWEEN '$sampai' AND '$dari'");
    }elseif(strlen($_GET['dari']) > 1){
        $data_hari_ini = query("SELECT id, lokasi, tipeaset, sn, status, keterangan, DATE_FORMAT(upload, '%d-%m-%Y') as upload FROM harian WHERE DATE_FORMAT(upload, '%d-%m-%Y') = '$dari'");
    }elseif(strlen($_GET['sampai']) > 1){
        $data_hari_ini = query("SELECT id, lokasi, tipeaset, sn, status, keterangan, DATE_FORMAT(upload, '%d-%m-%Y') as upload FROM harian WHERE DATE_FORMAT(upload, '%d-%m-%Y') = '$sampai'");
    }
}elseif(isset($_GET['carilokasi'])){
    $lokasi = strtoupper($_GET['carilokasi']);
    $data_hari_ini = query("SELECT id, lokasi, tipeaset, sn, status, keterangan, DATE_FORMAT(upload, '%d-%m-%Y') as upload FROM harian WHERE lokasi LIKE '%$lokasi%' OR sn LIKE '%$lokasi%'");
    if($lokasi == 'SEMUA'){
        $data_hari_ini = query("SELECT id, lokasi, tipeaset, sn, status, keterangan, DATE_FORMAT(upload, '%d-%m-%Y') as upload FROM harian");
    }
}
// var_dump($data_hari_ini);die;


?>
<!doctype html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-eOJMYsd53ii+scO/bJGFsiCZc+5NDVN2yr8+0RDqr0Ql0h+rP48ckxlpbzKgwra6" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.24/css/jquery.dataTables.min.css">
    <title>Hello, world!</title>
  </head>
  <body>
    <div class="container">
        <nav class="navbar navbar-expand-lg navbar-light mb-2" style="background-color: #e3f2fd;">
            <div class="container-fluid">
                <a class="navbar-brand text-danger" href="index.php">MAS TEGUH</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="#">Cek Status nya</a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            Cek Status
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                            <li><a class="dropdown-item" href="#">OKE</a></li>
                            <li><a class="dropdown-item" href="#">RUSAK</a></li>
                            <li><a class="dropdown-item" href="#">IR</a></li>
                        </ul>
                    </li>
                </ul>
                <form class="d-flex" method="GET">
                    <input class="form-control me-2" name="carilokasi" type="search" placeholder="Cari Lokasi" aria-label="Search" style="text-transform:uppercase">
                    <button class="btn btn-outline-success" type="submit">Search</button>
                </form>
                </div>
            </div>
        </nav>
        <!-- CARD -->
        <div class="card">
            <div class="card-header">Cek Stusnya</div>
            <div class="card-body">
                <form class="row g-3" method="GET">
                    <div class="col-md-3">
                        <select class="form-select" name="dari" id="dari">
                            <?php if(isset($_GET['dari']) AND strlen($_GET['dari']) > 1 ): ?>
                                <option selected hidden value="<?= $_GET['dari'] ?>"><?= $_GET['dari'] ?></option>
                            <?php else: ?>
                                <option selected hidden value="">Dari</option>
                            <?php endif ?>
                            <?php foreach($tanggalupload as $upload): ?>
                                <option value="<?=$upload['upload']?>"><?=$upload['upload']?></option>
                            <?php endforeach ?>
                        </select>                    
                    </div>
                    <div class="col-md-3">
                        <select class="form-select" name="sampai" id="sampai">
                            <?php if(isset($_GET['sampai']) AND strlen($_GET['sampai']) > 1 ): ?>
                                <option selected hidden value="<?= $_GET['sampai'] ?>"><?= $_GET['sampai'] ?></option>
                            <?php else: ?>
                                <option selected hidden value="">Sampai</option>
                            <?php endif ?>
                            <?php foreach($tanggalupload as $upload): ?>
                                <option value="<?=$upload['upload']?>"><?=$upload['upload']?></option>
                            <?php endforeach ?>
                        </select> 
                    </div>
                    <div class="col-md-3">
                        <div class="d-grid gap-2 d-md-block">
                            <button type="submit" class="btn btn-primary mb-3">Confirm</button>
                            <a href="cekstatus.php" class="btn btn-warning mb-3">Reset</a>
                        </div>
                    </div>
                </form>
            </div>
            <!-- START TABLE -->
            <?php if(!empty($data_hari_ini)): ?>
            <div class="container table-responsive">
                <table id="myTable" class="display">
                    <thead>
                        <tr>
                            <th>Lokasi</th>
                            <th>Tipe Aset</th>
                            <th>Serial Number</th>
                            <th>Status</th>
                            <th>Keterangan</th>
                            <th>Upload</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($data_hari_ini as $data): ?>
                        <tr>
                            <td>
                                <a href="edit.php?id=<?= $data['id'] ?>">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pencil-square" viewBox="0 0 16 16">
                                    <path d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z"/>
                                    <path fill-rule="evenodd" d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5v11z"/>
                                </svg>
                                </a><?= $data['lokasi'] ?> 
                            </td>
                            <td><?= $data['tipeaset'] ?></td>
                            <td><?= $data['sn'] ?></td>
                            <td><?= $data['status'] ?></td>
                            <td><?= $data['keterangan'] ?></td>
                            <td><?= $data['upload'] ?></td>
                        </tr>
                        <?php endforeach ?>
                    </tbody>
                </table>
            </div>
            
            <?php else: ?>
                <div class="alert alert-danger" role="alert">
                DATA TIDAK DITEMUKAN
                </div>
            <?php endif ?>
            <!-- END TABLE -->
        </div>
        <!-- END CARD -->
    </div>
    

    <!-- Optional JavaScript; choose one of the two! -->

    <!-- Option 1: Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta3/dist/js/bootstrap.bundle.min.js" integrity="sha384-JEW9xMcG8R+pH31jmWH6WWP0WintQrMb4s7ZOdauHnUtxwoG2vI5DkLtS3qm9Ekf" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/jquery-3.5.1.js"></script>
    <script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#myTable').DataTable({
                "paging": false,
                "scrollY": '60vh',
                "scrollCollapse": true,
                });
        } );
    </script>
    
    <!--
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.1/dist/umd/popper.min.js" integrity="sha384-SR1sx49pcuLnqZUnnPwx6FCym0wLsk5JZuNx2bPPENzswTNFaQU1RDvt3wT4gWFG" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta3/dist/js/bootstrap.min.js" integrity="sha384-j0CNLUeiqtyaRmlzUHCPZ+Gy5fQu0dQ6eZ/xAww941Ai1SxSY+0EQqNXNE6DZiVc" crossorigin="anonymous"></script>
    -->
  </body>
</html>
