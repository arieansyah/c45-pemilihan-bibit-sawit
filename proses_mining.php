<?php
function format_decimal($value){
    return round($value, 3);
}

//fungsi utama
function proses_DT($db_object, $parent, $kasus_cabang1, $kasus_cabang2, $kasus_cabang3) {
    echo "
    <button class='btn btn-primary btn-round'>
        Cabang 1
    </button>";
    pembentukan_tree($db_object, $parent, $kasus_cabang1);
    echo "
    <button class='btn btn-primary btn-round'>
        Cabang 2
    </button>";
    pembentukan_tree($db_object, $parent, $kasus_cabang2);
    echo "
    <button class='btn btn-primary btn-round'>
        Cabang 3
    </button>";
    pembentukan_tree($db_object, $parent, $kasus_cabang3);
}

//fungsi proses dalam suatu kasus data
function pembentukan_tree($db_object, $N_parent, $kasus) {
    //mengisi kondisi
    if ($N_parent != '') {
        $kondisi = $N_parent . " AND " . $kasus;
    } else {
        $kondisi = $kasus;
    }
    echo "<button class='btn btn-default btn-sm'>".$kondisi."</button>";
    //cek data heterogen / homogen???
    $cek = cek_heterohomogen($db_object, 'kualitas', $kondisi);
    if ($cek == 'homogen') {
        echo "<br><button class='btn btn-info'>LEAF
            <i class='material-icons'>
                arrow_forward
            </i>
        </button>
        ";
        $sql_keputusan = $db_object->db_query("SELECT DISTINCT(kualitas) FROM "
                . "data_latih_fajar WHERE $kondisi");
        $row_keputusan = $db_object->db_fetch_array($sql_keputusan);
        $keputusan = $row_keputusan['0'];
        //insert atau lakukan pemangkasan cabang
        pangkas($db_object, $N_parent, $kasus, $keputusan);
    }//jika data masih heterogen
    else if ($cek == 'heterogen') {
        //cek jumlah data
        // $jumlah = jumlah_data($kondisi);
        // if($jumlah<=3){
        //     echo "<br>LEAF ";
        //     $NBaik = $kondisi." AND kualitas='baik'";
        //     $NCukup = $kondisi." AND kualitas='kurang'";
        //     $jumlahbaik = jumlah_data("$NBaik");
        //     $jumlahcukup = jumlah_data("$NCukup");
        //     if($jumlahbaik <= $jumlahcukup){
        //         $keputusan = 'kurang';
        //     }else{
        //         $keputusan = 'baik';
        //     }
        //     //insert atau lakukan pemangkasan cabang
        //     pangkas($N_parent , $kasus , $keputusan);
        // }
        // //lakukan perhitungan
        // else{
        //jika kondisi tidak kosong kondisi_kualitas=tambah and
        $kualitas = '';
        if ($kondisi != '') {
            $kualitas = $kondisi . " AND ";
        }
        $jml_baik = jumlah_data($db_object, "$kualitas kualitas='baik'");
        $jml_cukup = jumlah_data($db_object, "$kualitas kualitas='cukup'");
        $jml_jelek = jumlah_data($db_object, "$kualitas kualitas='jelek'");
        
        $jml_total = $jml_baik + $jml_cukup + $jml_jelek;
        $entropy_all = hitung_entropy($jml_baik, $jml_cukup, $jml_jelek);

        echo "
        <div class='card' style='width: 20rem;'>
            <ul class='list-group list-group-flush'>
                <li class='list-group-item'>Jumlah Data = " . $jml_total . "</li>
                <li class='list-group-item'>Jumlah <strong>BAIK</strong> = " . $jml_baik . "</li>
                <li class='list-group-item'>Jumlah <strong>CUKUP</strong> = " . $jml_cukup . "</li>
                <li class='list-group-item'>Jumlah <strong>JELEK</strong> = " . $jml_jelek . "</li>
                <li class='list-group-item'>Entropy All = " . $entropy_all . "</li>
            </ul>
        </div>";
        //hitung entropy semua
        // echo "Entropy All = " . $entropy_all . "<br>";

        // $nilai_status_pernikahan = array();
        // $nilai_status_pernikahan = cek_nilaiAtribut($db_object, 'status_pernikahan',$kondisi);
        // $jmlStatusPernikahan = count($nilai_status_pernikahan);

        echo "<div class='table-responsive'>
                <table class='table table-bordered table-dark' id='sample-table-1'>
                    <thead>";
        echo "<tr>"
                . "<th>Nilai Atribut</th> "
                . "<th>Jumlah data</th> "
                . "<th>Jumlah 'Baik'</th> "
                . "<th>Jumlah 'Cukup'</th> "
                . "<th>Jumlah 'Jelek'</th> "
                . "<th>Entropy</th> "
                . "<th>Gain</th>"
                . "<tr>";
        echo "</thead>"
        . " <tbody>";

        $db_object->db_query("TRUNCATE gain");
        //hitung gain atribut KATEGORIKAL
        hitung_gain($db_object, $kondisi, "daun", $entropy_all, "daun='panjang'", "daun='sedang'", "daun='pendek'", "", "");
        
        //hitung gain atribut KATEGORIKAL
        // if($jmlStatusPernikahan!=1){
        //     $NA1StatusPernikahan="status_pernikahan='$nilai_status_pernikahan[0]'";
        //     $NA2StatusPernikahan="";
        //     $NA3StatusPernikahan="";
        //     if($jmlStatusPernikahan==2){
        //             $NA2StatusPernikahan="status_pernikahan='$nilai_status_pernikahan[1]'";
        //     }else if ($jmlStatusPernikahan==3){
        //             $NA2StatusPernikahan="status_pernikahan='$nilai_status_pernikahan[1]'";
        //             $NA3StatusPernikahan="status_pernikahan='$nilai_status_pernikahan[2]'";
        //     }				
        //     hitung_gain($db_object, $kondisi , "status_pernikahan", $entropy_all , $NA1StatusPernikahan, $NA2StatusPernikahan, $NA3StatusPernikahan, "" , "");	
        // }

        //hitung gain atribut Numerik
        //batang
        hitung_gain($db_object, $kondisi, "batang", $entropy_all, "batang='besar'", "batang='kecil'", "batang='cukup'", "", "");
        
        //akar
        hitung_gain($db_object, $kondisi, "akar", $entropy_all, "akar='baik'", "akar='cukup'", "akar='kecil'", "", "");
        // //penyakit
        // hitung_gain($db_object, $kondisi, "penyakit", $entropy_all, "penyakit='baik'", "penyakit='cukup'", "penyakit='jelek'", "", "");
        // //km_batang
        // hitung_gain($db_object, $kondisi, "km_batang", $entropy_all, "km_batang='baik'", "km_batang='cukup'", "km_batang='jelek'", "", "");
        // //py_daun
        // hitung_gain($db_object, $kondisi, "py_daun", $entropy_all, "py_daun='baik'", "py_daun='cukup'", "py_daun='jelek'", "", "");
        
        echo "</tbody>";
        echo "</table>";
        //ambil nilai gain terBesar
        $sql_max = $db_object->db_query("SELECT MAX(gain) FROM gain");
        $row_max = $db_object->db_fetch_array($sql_max);
        $max_gain = $row_max[0];
        $sql = $db_object->db_query("SELECT * FROM gain WHERE gain=$max_gain");
        $row = $db_object->db_fetch_array($sql);
        //echo $row[2];

        // gue tandai lu
        $atribut = $row[2];
        echo "
            <div class='alert alert-warning' role='alert'>
                Atribut terpilih = <strong>" . $atribut . "</strong>, dengan nilai gain = <strong>" . $max_gain . "</strong><br>
            </div>
        ";
        //echo "<br>================================<br>";

        //jika max gain = 0 perhitungan dihentikan dan mengambil keputusan
        if ($max_gain == 0) {
            echo "<br>LEAF ";
            $NBaik = $kondisi . " AND kualitas='baik'";
            $NCukup = $kondisi . " AND kualitas='cukup'";
            $NJelek = $kondisi . " AND kualitas='jelek'";
            $jumlahbaik = jumlah_data($db_object, "$NBaik");
            $jumlahcukup = jumlah_data($db_object, "$NCukup");
            $jumlahjelek = jumlah_data($db_object, "$NJelek");
            if($jumlahbaik >= $jumlahcukup && $jumlahbaik >= $jumlahjelek) {
                $keputusan = 'baik';
            }
            elseif ($jumlahcukup >= $jumlahjelek && $jumlahcukup >= $jumlahbaik) {
                $keputusan = 'cukup';
            }
            else {
                $keputusan = 'jelek';
            }
            //insert atau lakukan pemangkasan cabang
            pangkas($db_object, $N_parent, $kasus, $keputusan);
        }
        //jika max_gain >0 lanjut..
        else {
            //status rumah terpilih
            if ($atribut == "daun") {
                proses_DT($db_object, $kondisi, "($atribut='panjang')", "($atribut='sedang')", "($atribut='pendek')");
            }
            if ($atribut == "batang") {
                proses_DT($db_object, $kondisi, "($atribut='besar')", "($atribut='cukup')", "($atribut='kecil')");
            }
            if ($atribut == "akar") {
                proses_DT($db_object, $kondisi, "($atribut='baik')", "($atribut='cukup')", "($atribut='kecil')");
            }
            // if ($atribut == "penyakit") {
            //     proses_DT($db_object, $kondisi, "($atribut='baik')", "($atribut='cukup')", "($atribut='jelek')");
            // }
            // if ($atribut == "km_batang") {
            //     proses_DT($db_object, $kondisi, "($atribut='baik')", "($atribut='cukup')", "($atribut='jelek')");
            // }
            // if ($atribut == "py_daun") {
            //     proses_DT($db_object, $kondisi, "($atribut='baik')", "($atribut='cukup')", "($atribut='jelek')");
            // }   
        }//end 
        //else jika max_gain>0
        // }// end jumlah<3
    }//end else if($cek=='heterogen'){
}

//==============================================================================
//fungsi cek nilai atribut
function cek_nilaiAtribut($db_object, $field , $kondisi){
    //sql disticnt		
    $hasil = array();
    if($kondisi==''){
            $sql = $db_object->db_query("SELECT DISTINCT($field) FROM data_latih_fajar");					
    }else{
            $sql = $db_object->db_query("SELECT DISTINCT($field) FROM data_latih_fajar WHERE $kondisi");					
    }
    $a=0;
    while($row = $db_object->db_fetch_array($sql)){
            $hasil[$a] = $row['0'];
            $a++;
    }	
    return $hasil;
}

//fungsi cek heterogen data
function cek_heterohomogen($db_object, $field, $kondisi) {
    //sql disticnt
    if ($kondisi == '') {
        $sql = $db_object->db_query("SELECT DISTINCT($field) FROM data_latih_fajar");
    } else {
        $sql = $db_object->db_query("SELECT DISTINCT($field) FROM data_latih_fajar WHERE $kondisi");
    }
    //jika jumlah data 1 maka homogen
    if ($db_object->db_num_rows($sql) == 1) {
        $nilai = "homogen";
    } else {
        $nilai = "heterogen";
    }
    return $nilai;
}

//fungsi menghitung jumlah data
function jumlah_data($db_object, $kondisi) {
    //sql
    if ($kondisi == '') {
        $sql = "SELECT COUNT(*) FROM data_latih_fajar $kondisi";
    } else {
        $sql = "SELECT COUNT(*) FROM data_latih_fajar WHERE $kondisi";
    }

    $query = $db_object->db_query($sql);
    $row = $db_object->db_fetch_array($query);
    $jml = $row['0'];
    return $jml;
}

//fungsi pemangkasan cabang
function pangkas($db_object, $PARENT, $KASUS, $LEAF) {
    //PEMANGKASAN CABANG
//    $sql_pangkas = $db_object->db_query("SELECT * FROM t_keputusan "
//            . "WHERE parent=\"$PARENT\" AND keputusan=\"$LEAF\"");
//    $row_pangkas = $db_object->db_fetch_array($sql_pangkas);
//    $jml_pangkas = $db_object->db_num_rows($sql_pangkas);
    //jika keputusan dan parent belum ada maka insert
//    if ($jml_pangkas == 0) {
        $sql_in = "INSERT INTO t_keputusan "
                . "(parent,akar,keputusan)"
                . " VALUES (\"$PARENT\" , \"$KASUS\" , \"$LEAF\")";
        $db_object->db_query($sql_in);
        echo $db_object->db_error($sql_in);
        // echo "1".$sql_in;
//    }
    //jika keputusan dan parent sudah ada maka delete
//    else {
//        $db_object->db_query("DELETE FROM t_keputusan WHERE id='$row_pangkas[0]'");
//        $exPangkas = explode(" AND ", $PARENT);
//        $jmlEXpangkas = count($exPangkas);
//        $temp = array();
//        for ($a = 0; $a < ($jmlEXpangkas - 1); $a++) {
//            $temp[$a] = $exPangkas[$a];
//        }
//        $imPangkas = implode(" AND ", $temp);
//        $akarPangkas = $exPangkas[$jmlEXpangkas - 1];
//        $que_pangkas = $db_object->db_query("SELECT * FROM t_keputusan "
//                . "WHERE parent=\"$imPangkas\" AND keputusan=\"$LEAF\"");
//        $baris_pangkas = $db_object->db_fetch_array($que_pangkas);
//        $jumlah_pangkas = $db_object->db_num_rows($que_pangkas);
//        if ($jumlah_pangkas == 0) {
//            $sql_in2 = "INSERT INTO t_keputusan "
//                    . "(parent,akar,keputusan)"
//                    . " VALUES (\"$imPangkas\" , \"$akarPangkas\" , \"$LEAF\")";
//            $db_object->db_query($sql_in2);
//            //echo "2".$sql_in2;
//        } else {
//            pangkas($db_object, $imPangkas, $akarPangkas, $LEAF);
//        }
//    }
    echo "
        <button class='btn btn-success btn-sm'>Keputusan = " . $LEAF . "
        </button>
        <hr>
    ";
}

//fungsi menghitung gain
function hitung_gain($db_object, $kasus, $atribut, $ent_all, $kondisi1, $kondisi2, $kondisi3, $kondisi4, $kondisi5) {
    $data_kasus = '';
    if ($kasus != '') {
        $data_kasus = $kasus . " AND ";
    }

    //untuk atribut 2 nilai atribut	
    if ($kondisi3 == '') {
        $j_baik1 = jumlah_data($db_object, "$data_kasus kualitas='baik' AND $kondisi1");
        $j_cukup1 = jumlah_data($db_object, "$data_kasus kualitas='cukup' AND $kondisi1");
        $j_jelek1 = jumlah_data($db_object, "$data_kasus kualitas='jelek' AND $kondisi1");
        $jml1 = $j_baik1 + $j_cukup1 + $j_jelek1;

        $j_baik2 = jumlah_data($db_object, "$data_kasus kualitas='baik' AND $kondisi2");
        $j_cukup2 = jumlah_data($db_object, "$data_kasus kualitas='cukup' AND $kondisi2");
        $j_jelek2 = jumlah_data($db_object, "$data_kasus kualitas='jelek' AND $kondisi2");
        $jml2 = $j_baik2 + $j_cukup2 + $j_jelek2;

        $j_baik3 = jumlah_data($db_object, "$data_kasus kualitas='baik' AND $kondisi3");
        $j_cukup3 = jumlah_data($db_object, "$data_kasus kualitas='cukup' AND $kondisi3");
        $j_jelek3 = jumlah_data($db_object, "$data_kasus kualitas='jelek' AND $kondisi3");
        $jml3 = $j_baik3 + $j_cukup3 + $j_jelek3;
        
        // $j_lancar2 = jumlah_data($db_object, "$data_kasus kualitas='baik' AND $kondisi2");
        // $j_macet2 = jumlah_data($db_object, "$data_kasus kualitas='cukup' AND $kondisi2");
        // $jml2 = $j_lancar2 + $j_macet2 ;
        //hitung entropy masing-masing kondisi
        $jml_total = $jml1 + $jml2 + $jml3;
        $ent1 = hitung_entropy($j_baik1, $j_cukup1, $j_jelek1);
        $ent2 = hitung_entropy($j_baik2, $j_cukup2, $j_jelek2);
        $ent3 = hitung_entropy($j_baik3, $j_cukup3, $j_jelek3);


        $gain = $ent_all - ((($jml1 / $jml_total) * $ent1) + (($jml2 / $jml_total) * $ent2) + (($jml3 / $jml_total) * $ent3));
        //desimal 3 angka dibelakang koma
        $gain = format_decimal($gain);

        echo "<tr>";
        echo "<td>" . $kondisi1 . "</td>";
        echo "<td>" . $jml1 . "</td>";
        echo "<td>" . $j_baik1 . "</td>";
        echo "<td>" . $j_cukup1 . "</td>";
        echo "<td>" . $j_jelek1 . "</td>";
        echo "<td>" . $ent1 . "</td>";
        echo "<td>&nbsp;</td>";
        echo "</tr>";

        echo "<tr>";
        echo "<td>" . $kondisi2 . "</td>";
        echo "<td>" . $jml2 . "</td>";
        echo "<td>" . $j_baik2 . "</td>";
        echo "<td>" . $j_cukup2 . "</td>";
        echo "<td>" . $j_jelek2 . "</td>";
        echo "<td>" . $ent2 . "</td>";
        echo "<td>" . $gain . "</td>";
        echo "</tr>";

        echo "<tr>";
        echo "<td>" . $kondisi3 . "</td>";
        echo "<td>" . $jml3 . "</td>";
        echo "<td>" . $j_baik3 . "</td>";
        echo "<td>" . $j_cukup3 . "</td>";
        echo "<td>" . $j_jelek3 . "</td>";
        echo "<td>" . $ent3 . "</td>";
        echo "<td>" . $gain . "</td>";
        echo "</tr>";

        echo "<tr><td colspan='8'></td></tr>";
    }
     //untuk atribut 3 nilai atribut
     else if($kondisi4==''){
        $j_baik1 = jumlah_data($db_object, "$data_kasus kualitas='baik' AND $kondisi1");
        $j_cukup1 = jumlah_data($db_object, "$data_kasus kualitas='cukup' AND $kondisi1");
        $j_jelek1 = jumlah_data($db_object, "$data_kasus kualitas='jelek' AND $kondisi1");
        $jml1 = $j_baik1 + $j_cukup1 + $j_jelek1;

        $j_baik2 = jumlah_data($db_object, "$data_kasus kualitas='baik' AND $kondisi2");
        $j_cukup2 = jumlah_data($db_object, "$data_kasus kualitas='cukup' AND $kondisi2");
        $j_jelek2 = jumlah_data($db_object, "$data_kasus kualitas='jelek' AND $kondisi2");
        $jml2 = $j_baik2 + $j_cukup2 + $j_jelek2;

        $j_baik3 = jumlah_data($db_object, "$data_kasus kualitas='baik' AND $kondisi3");
        $j_cukup3 = jumlah_data($db_object, "$data_kasus kualitas='cukup' AND $kondisi3");
        $j_jelek3 = jumlah_data($db_object, "$data_kasus kualitas='jelek' AND $kondisi3");
        $jml3 = $j_baik3 + $j_cukup3 + $j_jelek3;
        
     	//hitung entropy masing-masing kondisi
     	$jml_total = $jml1 + $jml2 + $jml3;
     	$ent1 = hitung_entropy($j_baik1, $j_cukup1, $j_jelek1);
        $ent2 = hitung_entropy($j_baik2, $j_cukup2, $j_jelek2);
        $ent3 = hitung_entropy($j_baik3, $j_cukup3, $j_jelek3);
     	$gain = $ent_all - ((($jml1/$jml_total)*$ent1) + (($jml2/$jml_total)*$ent2) + (($jml3/$jml_total)*$ent3));							
     	//desimal 3 angka dibelakang koma
     	$gain = format_decimal($gain);				
     	echo "<tr>";
     	echo "<td>" . $kondisi1 . "</td>";
        echo "<td>" . $jml1 . "</td>";
        echo "<td>" . $j_baik1 . "</td>";
        echo "<td>" . $j_cukup1 . "</td>";
        echo "<td>" . $j_jelek1 . "</td>";
        echo "<td>" . $ent1 . "</td>";
     	echo "<td>&nbsp;</td>";
     	echo "</tr>";
     	echo "<tr>";
     	echo "<td>".$kondisi2."</td>";
     	echo "<td>".$jml2."</td>";
     	echo "<td>" . $j_baik2 . "</td>";
        echo "<td>" . $j_cukup2 . "</td>";
        echo "<td>" . $j_jelek2 . "</td>";
     	echo "<td>".$ent2."</td>";
     	echo "<td>&nbsp;</td>";
     	echo "</tr>";
     	echo "<tr>";
     	echo "<td>".$kondisi3."</td>";
     	echo "<td>".$jml3."</td>";
     	echo "<td>" . $j_baik3 . "</td>";
        echo "<td>" . $j_cukup3 . "</td>";
        echo "<td>" . $j_jelek3 . "</td>";
     	echo "<td>".$ent3."</td>";
     	echo "<td>".$gain."</td>";
     	echo "</tr>";
     	echo "<tr><td colspan='8'></td></tr>";
     }
    // //untuk atribut 4 nilai atribut
    // //untuk atribut 5 nilai atribut	
    
    $sql_in_hasil = $db_object->db_query("INSERT INTO gain (node_id, atribut, gain) VALUES ('1','$atribut','$gain')");
    echo $db_object->db_error($sql_in_hasil);

}

//fungsi menghitung entropy
function hitung_entropy($nilai1, $nilai2, $nilai3) {
    $total = $nilai1 + $nilai2 + $nilai3;
    //jika salah satu nilai 0, maka entropy 0
//    if ($nilai1 == 0 || $nilai2 == 0 || $nilai3 == 0 || $nilai4 == 0) {
//        $entropy = 0;
//    }
//    else {
    $atribut1 = (-($nilai1 / $total) * (log(($nilai1 / $total), 2)));
    $atribut2 = (-($nilai2 / $total) * (log(($nilai2 / $total), 2)));
    $atribut3 = (-($nilai3 / $total) * (log(($nilai3 / $total), 2)));
    
    $atribut1 = is_nan($atribut1)?0:$atribut1;
    $atribut2 = is_nan($atribut2)?0:$atribut2;
    $atribut3 = is_nan($atribut3)?0:$atribut3;
    
        $entropy = $atribut1 + $atribut2 + $atribut3;
//    }
    //desimal 3 angka dibelakang koma
    $entropy = format_decimal($entropy);
    return $entropy;
}

//fungsi hitung rasio
function hitung_rasio($db_object, $kasus , $atribut , $gain , $nilai1 , $nilai2 , $nilai3 , $nilai4 , $nilai5){				
    $data_kasus = '';
    if($kasus!=''){
        $data_kasus = $kasus." AND ";
    }
    //menentukan jumlah nilai
    $jmlNilai=5;
    //jika nilai 5 kosong maka nilai atribut-nya 4
    if($nilai5==''){
        $jmlNilai=4;
    }
    //jika nilai 4 kosong maka nilai atribut-nya 3
    if($nilai4==''){
        $jmlNilai=3;
    }
    $db_object->db_query("TRUNCATE rasio_gain");		
    if($jmlNilai==3){
        $opsi11 = jumlah_data($db_object, "$data_kasus ($atribut='$nilai2' OR $atribut='$nilai3')");
        $opsi12 = jumlah_data($db_object, "$data_kasus $atribut='$nilai1'");
        $tot_opsi1=$opsi11+$opsi12;
        $opsi21 = jumlah_data($db_object, "$data_kasus ($atribut='$nilai3' OR $atribut='$nilai1')");
        $opsi22 = jumlah_data($db_object, "$data_kasus $atribut='$nilai2'");
        $tot_opsi2=$opsi21+$opsi22;
        $opsi31 = jumlah_data($db_object, "$data_kasus ($atribut='$nilai1' OR $atribut='$nilai2')");
        $opsi32 = jumlah_data($db_object, "$data_kasus $atribut='$nilai3'");
        $tot_opsi3=$opsi31+$opsi32;			
        //hitung split info
        $opsi1 = (-($opsi11/$tot_opsi1)*(log(($opsi11/$tot_opsi1),2))) + (-($opsi12/$tot_opsi1)*(log(($opsi12/$tot_opsi1),2)));
        $opsi2 = (-($opsi21/$tot_opsi2)*(log(($opsi21/$tot_opsi2),2))) + (-($opsi22/$tot_opsi2)*(log(($opsi22/$tot_opsi2),2)));
        $opsi3 = (-($opsi31/$tot_opsi3)*(log(($opsi31/$tot_opsi3),2))) + (-($opsi32/$tot_opsi3)*(log(($opsi32/$tot_opsi3),2)));
        //desimal 3 angka dibelakang koma
        $opsi1 = format_decimal($opsi1);
        $opsi2 = format_decimal($opsi2);
        $opsi3 = format_decimal($opsi3);										
        //hitung rasio
        $rasio1 = $gain/$opsi1;
        $rasio2 = $gain/$opsi2;
        $rasio3 = $gain/$opsi3;
        //desimal 3 angka dibelakang koma
        $rasio1 = format_decimal($rasio1);
        $rasio2 = format_decimal($rasio2);
        $rasio3 = format_decimal($rasio3);
            //cetak
            echo "Opsi 1 : <br>jumlah ".$nilai2."/".$nilai3." = ".$opsi11.
                    "<br>jumlah ".$nilai1." = ".$opsi12.
                    "<br>Split = ".$opsi1.
                    "<br>Rasio = ".$rasio1."<br>";
            echo "Opsi 2 : <br>jumlah ".$nilai3."/".$nilai1." = ".$opsi21.
                    "<br>jumlah ".$nilai2." = ".$opsi22.
                    "<br>Split = ".$opsi2.
                    "<br>Rasio = ".$rasio2."<br>";
            echo "Opsi 3 : <br>jumlah ".$nilai1."/".$nilai2." = ".$opsi31.
                    "<br>jumlah ".$nilai3." = ".$opsi32.
                    "<br>Split = ".$opsi3.
                    "<br>Rasio = ".$rasio3."<br>";

            //insert 
            $db_object->db_query("INSERT INTO rasio_gain VALUES 
                                    ('' , 'opsi1' , '$nilai1' , '$nilai2 , $nilai3' , '$rasio1'),
                                    ('' , 'opsi2' , '$nilai2' , '$nilai3 , $nilai1' , '$rasio2'),
                                    ('' , 'opsi3' , '$nilai3' , '$nilai1 , $nilai2' , '$rasio3')");
    }
    
    $sql_max = $db_object->db_query("SELECT MAX(rasio_gain) FROM rasio_gain");
    $row_max = $db_object->db_fetch_array($sql_max);	
    $max_rasio = $row_max['0'];
    $sql = $db_object->db_query("SELECT * FROM rasio_gain WHERE rasio_gain=$max_rasio");
    $row = $db_object->db_fetch_array($sql);	
    $opsiMax = array();
    $opsiMax[0] = $row[2];
    $opsiMax[1] = $row[3];		
    echo "<br>=========================<br>";
    return $opsiMax;		
}


function klasifikasi($db_object, $n_daun, $n_batang, $n_akar) {

    $sql = $db_object->db_query("SELECT * FROM t_keputusan");
    $keputusan = $id_rule_keputusan = "";
    while ($row = $db_object->db_fetch_array($sql)) {
        //menggabungkan parent dan akar dengan kata AND
        if ($row['parent'] != '') {
            $rule = $row['parent'] . " AND " . $row['akar'];
        } else {
            $rule = $row['akar'];
        }
        //mengubah parameter
        $rule = str_replace("<=", " k ", $rule);
        $rule = str_replace("=", " s ", $rule);
        $rule = str_replace(">", " l ", $rule);
        //mengganti nilai
        // $rule = str_replace("status_pernikahan", "'$n_status_pernikahan'", $rule);
        $rule = str_replace("daun", "'$n_daun'", $rule);
        $rule = str_replace("batang", "'$n_batang'", $rule);
        $rule = str_replace("akar", "'$n_akar'", $rule);
        // $rule = str_replace("penyakit", "'$n_penyakit'", $rule);
        // $rule = str_replace("km_batang", "'$km_batang'", $rule);
        // $rule = str_replace("py_daun", "'$py_daun'", $rule);
        //menghilangkan '
        $rule = str_replace("'", "", $rule);
        //explode and
        $explodeAND = explode(" AND ", $rule);
        $jmlAND = count($explodeAND);
        //menghilangkan ()
        $explodeAND = str_replace("(", "", $explodeAND);
        $explodeAND = str_replace(")", "", $explodeAND);
        //deklarasi bol
        $bolAND=array();
        $n=0;
        while($n<$jmlAND){
            //explode or
            $explodeOR = explode(" OR ",$explodeAND[$n]);
            $jmlOR = count($explodeOR);	
            //deklarasi bol
            $bol=array();
            $a=0;
            while($a<$jmlOR){				
                //pecah  dengan spasi
                $exrule2 = explode(" ",$explodeOR[$a]);
                $parameter = $exrule2[1];				
                if($parameter=='s'){
                    //pecah  dengan s
                    $explodeRule = explode(" s ",$explodeOR[$a]);
                    //nilai true false						
                    if($explodeRule[0]==$explodeRule[1]){
                            $bol[$a]="Benar";
                    }else if($explodeRule[0]!=$explodeRule[1]){
                            $bol[$a]="Salah";
                    }
                }else if($parameter=='k'){
                    //pecah  dengan k
                    $explodeRule = explode(" k ",$explodeOR[$a]);
                    //nilai true false
                    if($explodeRule[0]<=$explodeRule[1]){
                            $bol[$a]="Benar";
                    }else{
                            $bol[$a]="Salah";
                    }
                }else if($parameter=='l'){
                    //pecah dengan s
                    $explodeRule = explode(" l ",$explodeOR[$a]);
                    //nilai true false
                    if($explodeRule[0]>$explodeRule[1]){
                            $bol[$a]="Benar";
                    }else{
                            $bol[$a]="Salah";
                    }
                }				
                $a++;
            }
            //isi false
            $bolAND[$n]="Salah";
            $b=0;			
            while($b<$jmlOR){
                //jika $bol[$b] benar bolAND benar
                if($bol[$b]=="Benar"){
                        $bolAND[$n]="Benar";
                }
                $b++;
            }			
            $n++;
        }
        //isi boolrule
        $boolRule="Benar";
        $a=0;
        while($a<$jmlAND){			
                //jika ada yang salah boolrule diganti salah
                if($bolAND[$a]=="Salah"){
                        $boolRule="Salah";
                        break;
                }						
                $a++;
        }		
        if($boolRule=="Benar"){
            $keputusan=$row['keputusan'];
            $id_rule_keputusan=$row['id'];
            break;
        }
        //jika tidak ada rule yang memenuhi kondisi data uji 
        //maka ambil rule paling bawah(ambil konisi yg paling panjang)????....
        if ($keputusan == '') {
            $que = $db_object->db_query("SELECT parent FROM t_keputusan");
            $jml = array();
            $exParent = array();
            $i = 0;
            while ($row_baris = $db_object->db_fetch_array($que)) {
                $exParent = explode(" AND ", $row_baris['parent']);
                $jml[$i] = count($exParent);
                $i++;
            }
            $maxParent = max($jml);
            $sql_query = $db_object->db_query("SELECT * FROM t_keputusan");
            while ($row_bar = $db_object->db_fetch_array($sql_query)) {
                $explP = explode(" AND ", $row_bar['parent']);
                $jmlT = count($explP);
                if ($jmlT == $maxParent) {
                    $keputusan = $row_bar['keputusan'];
                    $id_rule[$it] = $row_bar['id'];
                    $id_rule_keputusan = $row_bar['id'];
                    break;
                }
            }
        }
    }//end loop t_keputusan

    //echo $rule;

    return array('keputusan' => $keputusan, 'id_rule' => $id_rule_keputusan);
}
