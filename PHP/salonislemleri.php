<?php
header('Access-Control-Allow-Origin: *');
if ($_POST) {

    require("baglanti.php");

    $islem = $_POST['islem'];


    if ($islem == 'ekle') {

        
     if(isset($_POST['salonadi']) && isset($_POST['aciklama']))
     {
        $salonadi = $_POST['salonadi'];
        $aciklama = $_POST['aciklama'];
     
            $sql_ins = $db->prepare("insert into toplanti_salonlari (adi, aciklama) values (:salonadi, :aciklama)");
            $sql_ins->execute(array("salonadi" => $salonadi, "aciklama" => $aciklama));

            if ($sql_ins) {
                echo 'Başarılı';
            } else {
                echo 'Hata';
            }
     
    }
    } else if ($islem == 'guncelle') {

        if(  isset($_POST['salon_id'])  && isset($_POST['baslangicSaati'])  && isset($_POST['bitisSaati'])  && isset($_POST['hazirlikSuresi'])  && isset($_POST['toplantiSuresi'])   )
        {

            $salon_id = $_POST['salon_id'];
            $baslangicSaati = $_POST['baslangicSaati'];
            $bitisSaati = $_POST['bitisSaati'];
            $hazirlikSuresi = $_POST['hazirlikSuresi'];
            $toplantiSuresi = $_POST['toplantiSuresi'];

        $sql = $db->prepare("Select * from genel_ayar where salon_id=:salon_id");
        $sql->execute(array("salon_id" => $salon_id));
    
        if ($sql->rowCount() > 0) {
                $sql_update = $db->prepare("Update genel_ayar set baslangic_saati=:baslangic_saati, bitis_saati=:bitis_saati, hazirlik_suresi=:hazirlik_suresi, toplanti_suresi=:toplanti_suresi where salon_id=:salon_id");
                $sql_update->execute(array("baslangic_saati" => $baslangicSaati, "bitis_saati" => $bitisSaati, "hazirlik_suresi"=>$hazirlikSuresi, "toplanti_suresi"=> $toplantiSuresi, "salon_id" => $salon_id));

                if($sql_update)
                {
                    echo 'Başarılı';

                    $sql = $db->prepare("Delete from randevu_bilgileri where toplanti_salonu=:salon_id");
                    $sql->execute(array("salon_id" => $salon_id));
                }
                else
                {
                    echo 'Hata';
                }
        }
        else{
            $sql_ins = $db->prepare("insert into genel_ayar (baslangic_saati, bitis_saati, hazirlik_suresi, toplanti_suresi, salon_id) values (:baslangic_saati, :bitis_saati, :hazirlik_suresi, :toplanti_suresi, :salon_id)");
            $sql_ins->execute(array("baslangic_saati" => $baslangicSaati, "bitis_saati" => $bitisSaati, "hazirlik_suresi"=>$hazirlikSuresi, "toplanti_suresi"=> $toplantiSuresi, "salon_id" => $salon_id));
        
            if($sql_ins)
            {
                echo 'Başarılı';
                $sql = $db->prepare("Delete from randevu_bilgileri where toplanti_salonu=:salon_id");
                $sql->execute(array("salon_id" => $salon_id));
            }
            else
            {
                echo 'Hata';
            }
        }

    }
    else
    {
        echo 'Yetersiz / Hatalı Parametre';
    }
    } else if ($islem == 'sil') {

     if(isset($_POST['id']))
      {
            $id = $_POST['id'];

            $sql_del = $db->prepare("delete from toplanti_salonlari where id = :id");
            $sql_del->execute(array("id" => $id));

      
            if ($sql_del) {
                echo 'Başarılı';
            } else {
                echo 'Hata';
            }
        }
    }
}