<?php
//session_start();
    if (!isset($_SESSION['c45_id'])) {
        header("location:index.php?menu=forbidden");
    }

    if (($_SESSION['c45_id'])==2) {
        header("location:index.php?menu=forbidden");
    }


    include_once "database.php";
    include_once "fungsi.php";
    include_once "proses_mining.php";
//include_once "fungsi_proses.php";
?>
<div class="content"><!-- start: PAGE -->
    <div class="main-content">
        <div class="container">
            <div class="row">
                <div class="col-sm-12">
                    <h1>Prediksi </h1>
                </div>
            </div>
            <?php
            //object database class
            $db_object = new database();
            
            $pesan_error = $pesan_success = "";
            if (isset($_GET['pesan_error'])) {
                $pesan_error = $_GET['pesan_error'];
            }
            if (isset($_GET['pesan_success'])) {
                $pesan_success = $_GET['pesan_success'];
            }
            
            //if (!isset($_POST['submit'])) {
                ?>
        <div class="row">
            <div class="col-md-9">
                <form method="post" action="" class="form-horizontal">
                <div class="card">
                    <div class="card-header card-header-text card-header-danger">
                        <div class="card-text">
                            <h4 class="card-title">Prediksi</h4>
                        </div>
                    </div>
                    <div class="card-body">
    
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header card-header-text card-header-primary">
                                    <div class="card-text">
                                    <h4 class="card-title">Nama</h4>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="form-group">
                                        <label class="bmd-label-floating">Nama</label>
                                        <input type="text" name="nama" id="form-field-1" class="form-control" 
                                            value="<?php echo isset($_POST['nama'])?$_POST['nama']:"" ?>" required="">
                                    </div>
                                </div>
                            </div>
                        </div>
    
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header card-header-text card-header-primary">
                                    <div class="card-text">
                                    <h4 class="card-title">Daun</h4>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="form-group"> 
    
                                        <div class="form-check form-check-radio">
                                            <label class="form-check-label">                                    
                                                <input type="radio" class="form-check-input" value="panjang" name="daun" 
                                                    <?php echo isset($_POST['daun'])?($_POST['daun']=='panjang'?"checked":""):""; ?> required="">
                                                    Panjang
                                                <span class="circle">
                                                    <span class="check"></span>
                                                </span>
                                            </label>
                                        </div>
    
                                        <div class="form-check form-check-radio">
                                            <label class="form-check-label">                                    
                                                <input type="radio" class="form-check-input" value="sedang" name="daun" 
                                                    <?php echo isset($_POST['daun'])?($_POST['daun']=='sedang'?"checked":""):""; ?> required="">
                                                    Sedang
                                                <span class="circle">
                                                    <span class="check"></span>
                                                </span>
                                            </label>
                                        </div>
    
                                        <div class="form-check form-check-radio">
                                            <label class="form-check-label">                                    
                                                <input type="radio" class="form-check-input" value="pendek" name="daun" 
                                                    <?php echo isset($_POST['daun'])?($_POST['daun']=='pendek'?"checked":""):""; ?> required="">
                                                    Pendek
                                                <span class="circle">
                                                    <span class="check"></span>
                                                </span>
                                            </label>
                                        </div>
    
                                    </div>
                                </div>
                            </div>
                        </div>
    
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header card-header-text card-header-primary">
                                    <div class="card-text">
                                    <h4 class="card-title">Batang</h4>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="form-group"> 
    
                                        <div class="form-check form-check-radio">
                                            <label class="form-check-label">                                    
                                                <input type="radio" class="form-check-input" value="besar" name="batang" 
                                                    <?php echo isset($_POST['batang'])?($_POST['batang']=='besar'?"checked":""):""; ?> required="">
                                                    Besar
                                                <span class="circle">
                                                    <span class="check"></span>
                                                </span>
                                            </label>
                                        </div>
    
                                        <div class="form-check form-check-radio">
                                            <label class="form-check-label">                                    
                                                <input type="radio" class="form-check-input" value="cukup" name="batang" 
                                                    <?php echo isset($_POST['batang'])?($_POST['batang']=='cukup'?"checked":""):""; ?> required="">
                                                    Cukup
                                                <span class="circle">
                                                    <span class="check"></span>
                                                </span>
                                            </label>
                                        </div>
    
                                        <div class="form-check form-check-radio">
                                            <label class="form-check-label">                                    
                                                <input type="radio" class="form-check-input" value="kecil" name="batang" 
                                                    <?php echo isset($_POST['batang'])?($_POST['batang']=='kecil'?"checked":""):""; ?> required="">
                                                    Kecil
                                                <span class="circle">
                                                    <span class="check"></span>
                                                </span>
                                            </label>
                                        </div>
    
                                    </div>
                                </div>
                            </div>
                        </div>
    
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header card-header-text card-header-primary">
                                    <div class="card-text">
                                    <h4 class="card-title">Akar</h4>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="form-group"> 
    
                                        <div class="form-check form-check-radio">
                                            <label class="form-check-label">                                    
                                                <input type="radio" class="form-check-input" value="baik" name="akar" 
                                                    <?php echo isset($_POST['akar'])?($_POST['akar']=='baik'?"checked":""):""; ?> required="">
                                                    Baik
                                                <span class="circle">
                                                    <span class="check"></span>
                                                </span>
                                            </label>
                                        </div>
    
                                        <div class="form-check form-check-radio">
                                            <label class="form-check-label">                                    
                                                <input type="radio" class="form-check-input" value="cukup" name="akar" 
                                                    <?php echo isset($_POST['akar'])?($_POST['akar']=='cukup'?"checked":""):""; ?> required="">
                                                    Cukup
                                                <span class="circle">
                                                    <span class="check"></span>
                                                </span>
                                            </label>
                                        </div>
    
                                        <div class="form-check form-check-radio">
                                            <label class="form-check-label">                                    
                                                <input type="radio" class="form-check-input" value="kecil" name="akar" 
                                                    <?php echo isset($_POST['akar'])?($_POST['akar']=='kecil'?"checked":""):""; ?> required="">
                                                    Kecil
                                                <span class="circle">
                                                    <span class="check"></span>
                                                </span>
                                            </label>
                                        </div>
    
                                    </div>
                                </div>
                            </div>
                        </div>
    
                        <div class="col-md-12">
                            <div class="form-group">
                                <div class="pull-right">
                                    <input name="submit" type="submit" value="Submit" class="btn btn-primary btn-lg">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>             
                </form>
            </div>

            <?php 
                if (isset($_POST['submit'])) {
                    echo "<div class='col-md-3'>
                        <div class='card'>
                            <div class='card-header card-header-text card-header-danger'>
                                <div class='card-text'>
                                <h4 class='card-title'>Hasil Prediksi</h4>
                                </div>
                            </div>
                            <div class='card-body'>
                            ";
                                $success = true;
                                $input_error = false;
                                $pesan_gagal = $pesan_sukses = "";
                
                                if (!$input_error) {
                                    $n_nama = $_POST['nama'];
                                    $n_daun = $_POST['daun'];
                                    $n_batang = $_POST['batang'];
                                    $n_akar = $_POST['akar'];
                
                                    $hasil = klasifikasi($db_object, $n_daun, $n_batang, $n_akar);
                                    //simpan ke table hasil
                                    $sql_in_hasil = "INSERT INTO hasil_prediksi
                                                (nama, daun, batang, akar, kualitas)
                                                VALUES
                                                ('$n_nama', '" . $n_daun . "', '" . $n_batang . "', '" . $n_akar . "', '" . $hasil['keputusan'] . "')";
                
                                    $success = $db_object->db_query($sql_in_hasil);
                
                                    if ($success) {
                                        echo "<center>"
                                        . "<h2 class='typoh2'>"
                                        . ucwords($hasil['keputusan'])
                                        . "</h2>"
                                        . "</center>";
                                    } else {
                                        echo $db_object->db_error($sql_in_hasil);
                                        display_error("failed");
                                    }
                                }
                        echo "
                            </div>
                        </div>
                    </div>";
                }
            ?>
        </div>                
        </div>
    </div>
</div>