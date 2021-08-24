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

    if (isset($_GET['id'])){
        $id     = $_GET['id'];
        $datanya = query("SELECT * FROM harian WHERE id=$id");
        // var_dump($datanya);
        // var_dump(strpos($datanya[0]['keterangan'], "M") +1);
        // die;
    }else{
        header('location:cekstatus.php');
    }

    if (isset($_POST['submit'])){
        $lokasi     = strtoupper($_POST['lokasi']);
        $tipeaset   = strtoupper($_POST['tipeaset']);
        $sn         = strtoupper($_POST['sn']);
        $status     = strtoupper($_POST['status']);
        $keterangan = "";

        if ($status == "OK"){
            if (isset($_POST['instalok'])){
                $keterangan .= "Instal OK, ";
            }
            $keterangan .= $_POST['keteranganok'];
        }elseif($status == "RUSAK"){
            if (isset($_POST['mbrusak'])){
                $keterangan .= "MB Rusak, ";
            }
            $keterangan .= $_POST['keteranganrusak'];
        }elseif($status == "SUPPLIER"){
            $keterangan .= $_POST['keterangansupplier'] . ', supplier ' . strtoupper($_POST['namasupplier']);
        }elseif($status == "IR"){
            if (in_array($tipeaset, ['CPU QUICK', 'CPU Q3', 'CPU FLEG'])){
                if(isset($_POST['12v'])){
                    $keterangan .= $_POST['12v'] . ', ';
                }
                if(isset($_POST['9v'])){
                    $keterangan .= $_POST['9v'] . ', ';
                }
                if(isset($_POST['5v'])){
                    $keterangan .= $_POST['5v'] . ', ';
                }
                if(isset($_POST['max232'])){
                    $keterangan .= $_POST['max232'] . ', ';
                }
                if(isset($_POST['sn75176'])){
                    $keterangan .= $_POST['sn75176'] . ', ';
                }
                if(isset($_POST['uln2003'])){
                    $keterangan .= $_POST['uln2003'] . ', ';
                }
                if(isset($_POST['6n137'])){
                    $keterangan .= $_POST['6n137'] . ', ';
                }
                if(isset($_POST['sdcard'])){
                    $keterangan .= $_POST['sdcard'] . ', ';
                }
                if(isset($_POST['cmos'])){
                    $keterangan .= $_POST['cmos'];
                }
            }elseif(in_array($tipeaset, ['CPU ZYREX', 'CPU SERVER', 'MINI PC'])){
                if(isset($_POST['mbh81'])){
                    $keterangan .= $_POST['mbh81'] . ', ';
                }
                if(isset($_POST['psu'])){
                    $keterangan .= $_POST['psu'] . ', ';
                }
                if(isset($_POST['ram'])){
                    $keterangan .= $_POST['ram'] . ', ';
                }
                if(isset($_POST['iocard'])){
                    $keterangan .= $_POST['iocard'] . ', ';
                }
                if(isset($_POST['ethernetcard'])){
                    $keterangan .= $_POST['ethernetcard'] . ', ';
                }
                if(isset($_POST['hdd'])){
                    $keterangan .= $_POST['hdd'] . ', ';
                }
                if(isset($_POST['cmos'])){
                    $keterangan .= $_POST['cmos'] . ', ';
                }
                if(isset($_POST['tombolpower'])){
                    $keterangan .= $_POST['sdcard'] . ', ';
                }
                if(isset($_POST['fanpro'])){
                    $keterangan .= $_POST['cmos'];
                }
            }
        }

        // $iniquerynya = "UPDATE harian VALUES(NULL, '$lokasi', '$tipeaset', '$sn', '$status', '$keterangan', current_timestamp(), current_timestamp())";
        $iniquerynya = "UPDATE harian SET
                        lokasi      = '$lokasi',
                        tipeaset    = '$tipeaset',
                        sn          = '$sn',
                        status      = '$status',
                        keterangan  = '$keterangan',
                        diperbarui  = current_timestamp()
                        WHERE id    = '$id'
        ";
        mysqli_query($koneksi, $iniquerynya);

        if (mysqli_affected_rows($koneksi) > 0){
            header("location:edit.php?id='$id'");
            setcookie('pesan', 'berhasil diubah '. $lokasi . ' - ' . $tipeaset . ' - ' . $sn . ' - ' . $status, time() + 3);
        }

    }


?>


<!doctype html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-BmbxuPwQa2lc/FVzBcNJ7UAyJxM6wuqIj61tLrc4wSX0szH/Ev+nYRRuWlolflfl" crossorigin="anonymous">

    <title>Hello, world!</title>
    <style>
        .box, .embeded, .cpupc{
        /* color: #fff; */
        /* padding: 20px; */
        display: none;
        /* margin-top: 20px; */
        }
    </style>
  </head>
  <body>
    <div class="container">
        <nav class="navbar navbar-expand-lg navbar-light" style="background-color: #e3f2fd;">
            <div class="container-fluid">
                <a class="navbar-brand text-danger" href="#">MAS TEGUH</a>
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
                <!-- <form class="d-flex">
                    <input class="form-control me-2" type="search" placeholder="Search" aria-label="Search">
                    <button class="btn btn-outline-success" type="submit">Search</button>
                </form> -->
                </div>
            </div>
        </nav>
        <div class="row">
            <div class="col-md-6">
                <div class="card mt-1">
                    <!-- <img src="cap.jpg" width="12" class="card-img-top" alt="..."> -->
                    <div class="card-header">
                        <h5 class="card-title">Input barang yang sudah di cek</h5>
                        <?php if (isset($_COOKIE['pesan'])) : ?>
                            <div class="alert alert-success" role="alert">
                            <?= $_COOKIE['pesan'] ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="card-body">
                    <form method="POST">
                        <div class="mb-3">
                            <div class="row">
                                <div class="col-md-6">
                                    <label for="lokasi" class="form-label">Lokasi</label>
                                    <input type="text" name="lokasi" class="form-control" id="lokasi" style="text-transform:uppercase" required value="<?= $datanya[0]['lokasi'] ?>">
                                    <div id="lokasiHelp" class="form-text">cukup singkatannya saja</div>
                                </div>
                                <div class="col-md-6">
                                    <label for="tipeaset" class="form-label">Tipe Aset</label>
                                    <input class="form-control border-input" name="tipeaset" style="text-transform:uppercase" list="datalistOptions" id="tipeaset" placeholder="Type to search..." / required value="<?= $datanya[0]['tipeaset'] ?>">
                                    <datalist id="datalistOptions">
                                        <option value="CPU QUICK"></option>
                                        <option value="CPU Q3"></option>
                                        <option value="CPU FLEG"></option>
                                        <option value="CPU ZYREX"></option>
                                        <option value="CPU SERVER"></option>
                                        <option value="MINI PC"></option>
                                        <option value="HUB"></option>
                                        <option value="PSU"></option>
                                        <option value="MODEM"></option>
                                    </datalist>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="sn" class="form-label">No Seri</label>
                            <input type="text" class="form-control" name="sn" id="sn" style="text-transform:uppercase" required value="<?= $datanya[0]['sn'] ?>">
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="status" id="inlineRadio1" value="ok" <?php if($datanya[0]['status'] == "OK"){echo "checked";} ?>>
                            <label class="form-check-label" for="inlineRadio1">OK</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="status" id="inlineRadio2" value="rusak" <?php if($datanya[0]['status'] == "RUSAK"){echo "checked";} ?>>
                            <label class="form-check-label" for="inlineRadio2">RUSAK</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="status" id="inlineRadio3" value="supplier" <?php if($datanya[0]['status'] == "SUPPLIER"){echo "checked";} ?>>
                            <label class="form-check-label" for="inlineRadio3">SUPPLIER</label>
                        </div>                        
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="status" id="inlineRadio4" value="ir" <?php if($datanya[0]['status'] == "IR"){echo "checked";} ?>>
                            <label class="form-check-label" for="inlineRadio4">IR</label>
                        </div>       
                        <br><br>
                        <div class="ok box">
                            <div class="mb-3">
                                <label for="keteranganok" class="form-label">Keterangan OK</label>
                                <!-- <div class="form-check">
                                    <input class="form-check-input" type="checkbox" value="instalok" name="instalok" id="instalok">
                                    <label class="form-check-label" for="instalok">
                                        Instal OK
                                    </label>
                                </div> -->
                                <!-- <label for="keterangan" class="form-label">Lainnya</label> -->
                                <textarea class="form-control" name="keteranganok" id="keteranganok" rows="2s" placeholder="Lainnya"><?php if($datanya[0]['status'] == "OK"){echo $datanya[0]['keterangan'];} ?></textarea>
                            </div>
                        </div>                
                        <div class="rusak box">
                            <div class="mb-3">
                                <label for="keteranganrusak" class="form-label">Keterangan RUSAK</label>
                                <!-- <div class="form-check">
                                    <input class="form-check-input" type="checkbox" value="mbrusak" name="mbrusak" id="mbrusak" checked>
                                    <label class="form-check-label" for="mbrusak">
                                        MB Rusak
                                    </label>
                                </div> -->
                                <textarea class="form-control" name="keteranganrusak" id="keteranganrusak" rows="2s" placeholder="Lainnya"><?php if($datanya[0]['status'] == "RUSAK"){echo $datanya[0]['keterangan'];} ?></textarea>
                            </div>
                        </div>                
                        <div class="supplier box">
                            <!-- <select class="form-select" name="namasupplier">
                                <option selected hidden value="-">Pilih supplier</option>
                                <option value="sanye">Sanye</option>
                                <option value="zyrex">Zyrex</option>
                                <option value="nts">NTS</option>
                                <option value="whs">WHS</option>
                                <option value="sap">SAP</option>
                            </select> -->
                            <div class="mb-3">
                                <label for="keterangansupplier" class="form-label">Keterangan dikirim supplier</label>
                                <textarea class="form-control" name="keterangansupplier" id="keterangansupplier" rows="2s"><?php if($datanya[0]['status'] == "SUPPLIER"){echo $datanya[0]['keterangan'];} ?></textarea>
                            </div>
                        </div>                
                        <div class="ir box">
                            <div class="card border-warning mb-3 embeded">
                                <div class="card-header">Untuk embeded</div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" value="12v" name="12v" id="12v" <?php if(strpos($datanya[0]['keterangan'], "2v")){echo "checked";} ?>>
                                                <label class="form-check-label" for="12v">
                                                    12v
                                                </label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" value="09v" name="9v" id="9v" <?php if(strpos($datanya[0]['keterangan'], "9v")){echo "checked";} ?>>
                                                <label class="form-check-label" for="9v">
                                                    09v
                                                </label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" value="05v" name="5v" id="5v" <?php if(strpos($datanya[0]['keterangan'], "5v")){echo "checked";} ?>>
                                                <label class="form-check-label" for="5v">
                                                    05v
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" value="MAX232" name="max232" id="max232" <?php if(strpos($datanya[0]['keterangan'], "AX232")){echo "checked";} ?>>
                                                <label class="form-check-label" for="max232">
                                                    MAX232
                                                </label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" value="SN75176" name="sn75176" id="sn75176" <?php if(strpos($datanya[0]['keterangan'], "N75176")){echo "checked";} ?>>
                                                <label class="form-check-label" for="sn75176">
                                                    SN75176
                                                </label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" value="ULN2003" name="uln2003" id="uln2003" <?php if(strpos($datanya[0]['keterangan'], "LN2003")){echo "checked";} ?>>
                                                <label class="form-check-label" for="uln2003">
                                                    ULN2003
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" value="6N137" name="6n137" id="6n137" <?php if(strpos($datanya[0]['keterangan'], "N137")){echo "checked";} ?>>
                                                <label class="form-check-label" for="6n137">
                                                    6N137
                                                </label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" value="SDCARD" name="sdcard" id="sdcard" <?php if(strpos($datanya[0]['keterangan'], "DCARD")){echo "checked";} ?>>
                                                <label class="form-check-label" for="sdcard">
                                                    SDCARD
                                                </label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" value="CMOS" name="cmos" id="cmos" <?php if(strpos($datanya[0]['keterangan'], "MOS")){echo "checked";} ?>>
                                                <label class="form-check-label" for="cmos">
                                                    CMOS
                                                </label>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                            <div class="card border-warning mb-3 cpupc">
                                <div class="card-header">Untuk cpu pc</div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" value="mbh81" name="mbh81" id="mbh81" <?php if(strpos($datanya[0]['keterangan'], "h81")){echo "checked";} ?>>
                                                <label class="form-check-label" for="mbh81">
                                                    mbh81
                                                </label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" value="psu" name="psu" id="psu" <?php if(strpos($datanya[0]['keterangan'], "su")){echo "checked";} ?>>
                                                <label class="form-check-label" for="psu">
                                                    psu
                                                </label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" value="ram" name="ram" id="ram" <?php if(strpos($datanya[0]['keterangan'], "am")){echo "checked";} ?>>
                                                <label class="form-check-label" for="ram">
                                                    ram
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" value="iocard" name="iocard" id="iocard" <?php if(strpos($datanya[0]['keterangan'], "ocard")){echo "checked";} ?>>
                                                <label class="form-check-label" for="iocard">
                                                    iocard
                                                </label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" value="ethernetcard" name="ethernetcard" id="ethernetcard" <?php if(strpos($datanya[0]['keterangan'], "thernetcard")){echo "checked";} ?>>
                                                <label class="form-check-label" for="ethernetcard">
                                                    ethernetcard
                                                </label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" value="hdd" name="hdd" id="hdd" <?php if(strpos($datanya[0]['keterangan'], "dd")){echo "checked";} ?>>
                                                <label class="form-check-label" for="hdd">
                                                    hdd
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" value="cmos" name="cmos" id="cmos" <?php if(strpos($datanya[0]['keterangan'], "mos")){echo "checked";} ?>>
                                                <label class="form-check-label" for="cmos">
                                                    cmos
                                                </label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" value="tombolpower" name="tombolpower" id="tombolpower" <?php if(strpos($datanya[0]['keterangan'], "ombolpower")){echo "checked";} ?>>
                                                <label class="form-check-label" for="tombolpower">
                                                    tombolpower
                                                </label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" value="fanpro" name="fanpro" id="fanpro" <?php if(strpos($datanya[0]['keterangan'], "anpro")){echo "checked";} ?>>
                                                <label class="form-check-label" for="fanpro">
                                                    fanpro
                                                </label>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>        
                        <div class="d-grid gap-2 d-md-block">        
                            <button type="submit" name="submit" class="btn btn-primary">Edit</button>
                            <a href="cekstatus.php" class="btn btn-success">Cek Status</a>
                        </div>
                    </form>
                    </div>
                </div>
            </div>
        </div>
        <!-- <h1>Hello, world!</h1> -->
    </div>

    <!-- Optional JavaScript; choose one of the two! -->

    <!-- Option 1: Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta2/dist/js/bootstrap.bundle.min.js" integrity="sha384-b5kHyXgcpbZJO/tY9Ul7kGkf1S0CWuKcCD38l8YkeH8z8QjE0GmW1gYU5S9FOnJ0" crossorigin="anonymous"></script>

    <!-- Option 2: Separate Popper and Bootstrap JS -->
    <!--
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.6.0/dist/umd/popper.min.js" integrity="sha384-KsvD1yqQ1/1+IA7gi3P0tyJcT3vR+NdBTt13hSJ2lnve8agRGXTTyNaBYmCR/Nwi" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta2/dist/js/bootstrap.min.js" integrity="sha384-nsg8ua9HAw1y0W1btsyWgBklPnCUAFLuTMS2G72MMONqmOymq585AcH49TLBQObG" crossorigin="anonymous"></script>
    -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script>
    $(document).ready(function(){
        if ($('input[type="radio"]:checked').length  > 0){
            var inputValue = $('input[type="radio"]:checked').attr("value");
            var targetBox = $("." + inputValue);
            $(".box").not(targetBox).hide();
            $(targetBox).show();

            if(inputValue == "ir"){
                var embeded = ['CPU QUICK', 'CPU Q3', 'CPU FLEG'];
                var tipeaset = document.getElementById("tipeaset").value;
                if( embeded.includes(tipeaset) ){
                    $('.cpupc').hide();
                    $('.embeded').show();
                }else if (["CPU ZYREX", "CPU SERVER", "MINI PC"].includes(tipeaset)){
                    $('.embeded').hide();
                    $('.cpupc').show();
                }else{
                    $('.embeded').hide();
                    $('.cpupc').hide();
                }
            }
        }
        $('input[type="radio"]').click(function(){
            var inputValue = $(this).attr("value");
            var targetBox = $("." + inputValue);
            $(".box").not(targetBox).hide();
            $(targetBox).show();

            if(inputValue == "ir"){
                if(inputValue == "ir"){
                var embeded = ['CPU QUICK', 'CPU Q3', 'CPU FLEG'];
                var tipeaset = document.getElementById("tipeaset").value;
                if( embeded.includes(tipeaset) ){
                    $('.cpupc').hide();
                    $('.embeded').show();
                }else if (["CPU ZYREX", "CPU SERVER", "MINI PC"].includes(tipeaset)){
                    $('.embeded').hide();
                    $('.cpupc').show();
                }else{
                    $('.embeded').hide();
                    $('.cpupc').hide();
                }
            }
            }
        });
        $('#tipeaset').change(function(){
            var embeded = ['CPU QUICK', 'CPU Q3', 'CPU FLEG'];
            var tipeaset = document.getElementById("tipeaset").value;
            // console.log(tipeaset);
            if( embeded.includes(tipeaset) ){
                if(tipeaset == "CPU QUICK"){
                    $('#sn').val('CQS/');
                }else if(tipeaset == "CPU Q3"){
                    $('#sn').val('Q3/');
                }else if(tipeaset == "CPU FLEG"){
                    $('#sn').val('CFG/');
                }
                $('.cpupc').hide();
                $('.embeded').show();
            }else if (["CPU ZYREX", "CPU SERVER", "MINI PC"].includes(tipeaset)){
                $('#sn').val('SECP ');
                $('.embeded').hide();
                $('.cpupc').show();
            }else{
                // $('#sn').val('');
                $('.embeded').hide();
                $('.cpupc').hide();
            }

        });
    });
    </script>
  </body>
</html>
