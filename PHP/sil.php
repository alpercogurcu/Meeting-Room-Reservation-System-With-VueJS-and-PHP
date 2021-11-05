<?php
header('Access-Control-Allow-Origin: *');
require("baglanti.php");

if ($_POST) {
    $randevu_id = $_POST['randevu_id'];

    $secretKey = $_POST['secretKey'];
    if ($secretKey == 'Manolya') {

        $sql = $db->prepare("Delete from randevu_bilgileri where id=:randevu_id");
        $sql->execute(array("randevu_id" => $randevu_id));
      echo 'Başarılı';
    }
}
