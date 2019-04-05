<?php
//session_start();
if (!isset($_SESSION['c45_id'])) {
    header("location:index.php?menu=forbidden");
}

include_once "database.php";
include_once "fungsi.php";
?>
<div class="content"><!-- start: PAGE -->
    <div class="main-content">
        <div class="container">
            <!-- start: PAGE HEADER -->
            <div class="row">
                <div class="col-sm-12">
                    <h1>Hasil </h1>
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

            if (isset($_POST['delete'])) {
                $sql = "TRUNCATE data_hasil_klasifikasi";
                $db_object->db_query($sql);
                ?>
                <script> location.replace("?menu=hasil_klasifikasi&pesan_success=Data hasil berhasil dihapus");</script>
                <?php
            }

            $sql = "SELECT hasil.* 
            FROM hasil_prediksi hasil ";
            $query = $db_object->db_query($sql);
            $jumlah = $db_object->db_num_rows($query);
            ?>

            <div class="row">
                <div class="col-md-12">
                    <?php
                    if (!empty($pesan_error)) {
                        display_error($pesan_error);
                    }
                    if (!empty($pesan_success)) {
                        display_success($pesan_success);
                    }


                    echo "Jumlah data: " . $jumlah . "<br>";
                    if ($jumlah == 0) {
                        echo "Data kosong...";
                    } else {
                        ?>
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered table-hover" id="sample-table-1">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Nama</th>
                                        <th>Daun</th>
                                        <th>Batang</th>
                                        <th>Akar</th>
                                        <th>Hasil</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $no = 1;
                                    while ($row = $db_object->db_fetch_array($query)) {
                                        echo "<tr>";
                                        echo "<td>" . $no . "</td>";
                                        echo "<td>" . ucwords($row['nama']) . "</td>";
                                        echo "<td>" . ucwords($row['daun']) . "</td>";
                                        echo "<td>" . ucwords($row['batang']) . "</td>";
                                        echo "<td>" . ucwords($row['akar']) . "</td>";
                                        if ($row['kualitas'] == 'baik') {
                                            echo "<td><button type='button' class='btn btn-success'>" . ucwords($row['kualitas']) . "</button></td>";
                                        }elseif ($row['kualitas'] == 'cukup') {
                                            echo "<td><button type='button' class='btn btn-warning'>" . ucwords($row['kualitas']) . "</button></td>";
                                        }else {
                                            echo "<td><button type='button' class='btn btn-danger'>" . ucwords($row['kualitas']) . "</button></td>";
                                        }

                                        echo "</tr>";
                                        $no++;
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                        <?php
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>