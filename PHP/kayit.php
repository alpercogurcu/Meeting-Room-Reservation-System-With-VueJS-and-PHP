<?php
header('Access-Control-Allow-Origin: *');
require("baglanti.php");

if ($_POST) {


    $salon_id = $_POST["salon_id"];
    $baslangicsaati = $_POST["baslangicsaati"];
    $personel = $_POST['personel'];
    $bolum = $_POST["bolum"];
    $gorev = $_POST["gorev"];
    $aciklama = $_POST["aciklama"];
    $toplantiTipi = $_POST["toplantiTipi"];
    $tarih = $_POST["tarih"];


    $sql = $db->prepare("Select * from randevu_bilgileri where baslangic_saati=:baslangicSaati and tarih=:tarih and toplanti_salonu=:salon_id");
    $sql->execute(array("baslangicSaati" => $baslangicsaati, "tarih" => $tarih, "salon_id" => $salon_id));

    if ($sql->rowCount() > 0) {
        echo 'Böyle bir kayıt var.';
    } else {
        $sql_ins = $db->prepare("insert into randevu_bilgileri (personel, bolum,gorev, baslangic_saati, toplanti_salonu, aciklama, toplantiTipi, tarih) values (:personel, :bolum, :gorev, :baslangic_saati,:salon_id, :aciklama, :toplantiTipi, :tarih)");
        $sql_ins->execute(array("personel" => $personel, "bolum" => $bolum, "gorev"=> $gorev, "baslangic_saati" => $baslangicsaati, "salon_id" => $salon_id, "aciklama" => $aciklama, "toplantiTipi" => $toplantiTipi, "tarih"=> $tarih));

        if($sql_ins)
        {
            echo 'Eklendi';
        }
        else
        {
            echo 'Hata';
        }
    }
}
