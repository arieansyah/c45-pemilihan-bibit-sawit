<?php
error_reporting(E_ALL ^ (E_NOTICE | E_WARNING | E_DEPRECATED));
include_once "database.php";
include_once "fungsi.php";
//object database class
$db_object = new database();
?>

<div class="row">
    <div class="col-sm-12">
        <h1>Pohon Keputusan </h1>
    </div>
</div>
<!-- end: PAGE HEADER -->
<!-- start: PAGE CONTENT -->
<?php
if (isset($_GET['act'])) {
    $action = $_GET['act'];
    $id = $_GET['id'];
    if ($action == 'delete') {
        $db_object->db_query("TRUNCATE t_keputusan");
        header('location:index.php?menu=pohon_keputusan');
    }
}

$query = $db_object->db_query("select * from t_keputusan order by(id)");
$jumlah = $db_object->db_num_rows($query);
//jika pohon keputusan kosong
if ($jumlah == 0) {
    echo "<center><h3> Pohon keputusan belum terbentuk...</h3></center>";
} else {
    //hanya kaprodi yang bisa menghapus pohon keputusan dan menguji akurasi
    if ($_SESSION['koperasi_c45_level'] == 1) {
        ?>
        <p>
            <a href="?menu=pohon_keputusan&act=delete" class="btn btn-danger" onClick="return confirm('Anda yakin akan hapus pohon keputusan?')">
                Hapus Pohon Keputusan
            </a>
            <!--<a href="?menu=pohon_tree" >Lihat Pohon Keputusan</a> |-->
            <a href="?menu=uji_rule" class="btn btn-default">Uji Rule</a>
        </p>
        <?php
    }
    echo "Jumlah rule : " . $jumlah . "<br>";
    ?>
    <div class="table-responsive">
        <table class="table table-striped table-bordered table-hover" id="sample-table-1">
            <thead>
                <tr>
                    <th>Id</th>
                    <th>Aturan</th>

                </tr>
            </thead>
            <tbody>
                <?php
                $warna1 = '#ffc';
                $warna2 = '#eea';
                $warna = $warna1;
                $no = 1;
                while ($row = $db_object->db_fetch_array($query)) {
                    ?>
                    <tr>
                        <td align='center'><?php echo $row['id']; ?></td>
                        <td><?php
                            echo "IF ";
                            if ($row['parent'] != '') {
                                echo $row['parent'] . " AND ";
                            }
                            echo $row['akar'] . " THEN Label = " . $row['keputusan'];
                            ?>
                        </td>
                    </tr>
                    <?php
                    $no++;
                }
                ?>
            </tbody>
        </table>
    </div>
    <?php
}
?>