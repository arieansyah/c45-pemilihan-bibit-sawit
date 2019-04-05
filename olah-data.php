<?php
error_reporting(E_ALL ^ (E_NOTICE | E_WARNING | E_DEPRECATED));
if (($_SESSION['c45_id'])==2) {
    header("location:index.php?menu=forbidden");
}

include_once "database.php";
//include_once "import/excel_reader2.php";
include_once "fungsi.php";

include_once "library/php-excel-reader/excel_reader2.php";

include_once "library/SpreadsheetReader.php";
//object database class
$db_object = new database();
?>


<div class="row">
    <div class="col-sm-12">
        <h1>Olah Data </h1>        
    </div>
</div>
<!-- end: PAGE HEADER -->
<!-- start: PAGE CONTENT -->
<?php
if(isset($_POST['Submit'])){


    $mimes = ['application/vnd.ms-excel','text/xls','text/xlsx','application/vnd.oasis.opendocument.spreadsheet'];
  
    $Reader = new SpreadsheetReader('import/oke.xls');      
    $totalSheet = count($Reader->sheets());  

    for($i=0;$i<$totalSheet;$i++){
      

      $Reader->ChangeSheet($i);


      foreach ($Reader as $Row)

      {

        $kode = isset($Row[1]) ? $Row[1] : '';

        $daun = isset($Row[2]) ? $Row[2] : '';
        $batang = isset($Row[3]) ? $Row[3] : '';
        $akar = isset($Row[4]) ? $Row[4] : '';
        $kualitas = isset($Row[5]) ? $Row[5] : '';

        $query = "insert into data_latih(code, daun, batang, akar, kualitas) values('".$kode."','".$daun."','".$batang."','".$akar."','".$kualitas."')";

        $db_object->db_query($query);

       }

    }
    // if(in_array($_FILES["file"]["type"],$mimes)){
  
  

  
  
    // }else { 
  
    //   die("<br/>Sorry, File type is not allowed. Only Excel file."); 
  
    // }
  
  
  }  

if(isset($_POST['delete_all'])){
    $sql = "TRUNCATE data_latih";
            $result = $db_object->db_query($sql);
        
    if ($result) {
        ?>
        <script> location.replace("?menu=olah-data&pesan_success=Data berhasil dihapus");</script>
        <?php
    } else {
        echo $db_object->db_error($sql);
    display_error("failed");
        ?>
        <script> location.replace("?menu=olah-data&pesan_error=Data gagal dihapus");</script>
        <?php
    }

    
}


$query = $db_object->db_query("SELECT * FROM data_latih order by(id)");
$jumlah = $db_object->db_num_rows($query);
echo "<br><br>";

if(isset($_REQUEST['pesan_success'])){
    display_success($_REQUEST['pesan_success']);
}

if(isset($_REQUEST['pesan_error'])){
    display_error($_REQUEST['pesan_error']);
}

?>

<form method="post" enctype="multipart/form-data" action="">
    <!-- <div class="col-md-6">
        <div class="form-group form-file-upload form-file-multiple">
            <input type="file" class="inputFileHidden" id="file" name="file">
            <div class="input-group">
                <input id="file" name="file" type="text" class="form-control inputFileVisible" placeholder="Upload Data">
                <span class="input-group-btn">
                    <button type="button" class="btn btn-fab btn-round btn-primary">
                        <i class="material-icons">attach_file</i>
                    </button>
                </span>
            </div>
        </div>
    </div> -->
    <div class="form-group">
        <?php
            if ($jumlah == 0) {
                echo '<input name="Submit" type="submit" value="Upload Data" class="btn btn-success">';
            }
         ?>
        <input name="delete_all" type="submit" value="Delete All Data" class="btn btn-danger">
        <a href="index.php?menu=olah-data" class="btn btn-default">
            <i class="fa fa-refresh"></i>Refresh</a>
    </div>
</form>
<?php
if ($jumlah == 0) {
    echo "<center><h3>Data Latih masih kosong...</h3></center>";
} else {
    ?>
    <div class="table-responsive">
        <table class="table table-striped table-bordered table-hover" id="sample-table-1">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Kode</th>
                    <th>Daun</th>
                    <th>Batang</th>
                    <th>Akar</th>
                    <th>Kualitas</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $no = 1;
                while ($row = $db_object->db_fetch_array($query)) {
                    echo "<tr>";
                    echo "<td>" . $no . "</td>";
                    echo "<td>" . $row['code'] . "</td>";
                    echo "<td>" . $row['daun'] . "</td>";
                    echo "<td>" . $row['batang'] . "</td>";
                    echo "<td>" . $row['akar'] . "</td>";
                    echo "<td>" . $row['kualitas'] . "</td>";
                    echo "</tr>";
                    $no++;
                }
                ?>
            </tbody>
        </table>
        <?php
    }
    ?>