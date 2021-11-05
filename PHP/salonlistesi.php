<?php

header('Access-Control-Allow-Origin: *');

if ($_GET)
    $_TARIH = $_GET['tarih'];

require("baglanti.php");
date_default_timezone_set('Europe/Istanbul');


class Haftabul
{
    var $damga;
    function __construct($tarih)
    {
        if (is_int($tarih))
            $this->damga = $tarih;
        else {
            if (preg_match("/^[\d]{4}\/([0]?\d|1[0-2])\/([1-2][0-9]|3[0-1]|[0]?[1-9])$/", $tarih)) {
                $gecici = explode("/", $tarih);
               
                if (checkdate($gecici[1], $gecici[2], $gecici[0]))
                    $this->damga = mktime(0, 0, 0, $gecici[1], $gecici[2], $gecici[0]);
            }
            else
            {
                $gecici = explode("-", $tarih);
               
                if (checkdate($gecici[1], $gecici[2], $gecici[0]))
                    $this->damga = mktime(0, 0, 0, $gecici[1], $gecici[2], $gecici[0]);
            }
        }
    }
    function KacinciHafta()
    {
        if (isset($this->damga))
            $tdamga = $this->damga;
        else
            return (false);
        $gun    = date("z", $tdamga) + 1;
        $hafta    = ($gun >= 7 ? ceil($gun / 7) : 1);
        return $hafta;
    }
}




//echo $a->tarihDonderYilAyGun();

function ToplantiSaatleriGetir($tarih)
{

    $a = new Haftabul(date($tarih));
    // echo $a->KacinciHafta() . ". HAFTA TOPLANTI SALONU MÜSAİTLİK DURUMLARI<br>";
    //                                                 HERZAMANMÜSAİT
    require("baglanti.php");

    $sql = $db->query("Select * from genel_ayar ")->fetch(PDO::FETCH_ASSOC);
    if ($sql) {
        $baslangic_saati = $sql['baslangic_saati'];
        $bitis_saati =  $sql['bitis_saati'];
        $hazirlik_suresi = $sql['hazirlik_suresi'];
        $toplanti_suresi = $sql['toplanti_suresi'];

        $toplanti_dakika = date('i', strtotime($toplanti_suresi));
        $hazirlik_dakika = date('i', strtotime($hazirlik_suresi));
        $baslangic_saati_time = date('H:i:s', strtotime($baslangic_saati));


        $toplanti_salonlari = $db->query("Select * from toplanti_salonlari", PDO::FETCH_ASSOC);







        $returnAnaArray = array();

        $returnArr = array(
            "hafta" => $a->KacinciHafta(),

        );

        if ($toplanti_salonlari->rowCount()) {
            foreach ($toplanti_salonlari as $salon) {

                $returnArr["salonID"] = $salon["id"];
                $returnArr["salonAD"] = $salon["adi"];
                $returnArr["salonAciklama"] = $salon["aciklama"];


                $baslangic_saati_time = date('H:i:s', strtotime($baslangic_saati));
                $salonSaatleriArray = array();


                while ($baslangic_saati_time < date('H:i:s', strtotime($bitis_saati))) {

                    $salonSaatleriArray1 = array();

                    $bitis_toplanti_saati = date('H:i:s', strtotime('+' . $toplanti_dakika . ' minutes',  strtotime($baslangic_saati_time)));
                    $baslangicbitis =  $baslangic_saati_time . ' - ' . $bitis_toplanti_saati;

                    $randevu_saatleri = $db->prepare("Select * from randevu_bilgileri where tarih=:tarih and toplanti_salonu=:salonid");
                    $randevu_saatleri->execute(array("tarih" => $tarih, "salonid" => $salon["id"]));
                    $result_randevusaatleri = $randevu_saatleri->fetchAll(PDO::FETCH_ASSOC);


                    if (count($result_randevusaatleri) < 1) {
                     //   $RandevuYok = array("id" => "-1");
                     //   $salonSaatleriArray[$baslangicbitis] = $RandevuYok;
                     $salonSaatleriArray1["baslangicsaati"] = $baslangic_saati_time;
                     $salonSaatleriArray1["bitissaati"] = $bitis_toplanti_saati;
                     $salonSaatleriArray1["randevu"] = "-1";

                    } else {

                        for ($i = 0; $i < count($result_randevusaatleri); $i++) {

                            if ($baslangic_saati_time == $result_randevusaatleri[$i]["baslangic_saati"]) {
                                $randevuBilgileriArray = array("randevuBilgileri" => $result_randevusaatleri[$i]);
                                $salonSaatleriArray1["baslangicsaati"] = $baslangic_saati_time;
                                $salonSaatleriArray1["bitissaati"] = $bitis_toplanti_saati;
                                $salonSaatleriArray1["randevu"] = $result_randevusaatleri[$i];
                                
                                //   array_push($salonSaatleriArray,$baslangicbitis);
               
                                break;
                            } else {
                              
                                $salonSaatleriArray1["baslangicsaati"] = $baslangic_saati_time;
                                $salonSaatleriArray1["bitissaati"] = $bitis_toplanti_saati;
                                $salonSaatleriArray1["randevu"] = "-1";

                                
                             //   $RandevuYok = array("id" => "-1");
                                //      array_push($salonSaatleriArray,$baslangicbitis);

                                //    $salonSaatleriArray[$baslangicbitis] = $RandevuYok;
                            }
                        }
                    }

                    array_push($salonSaatleriArray, $salonSaatleriArray1);

                    $baslangic_saati_time = date('H:i:s', strtotime('+' . $hazirlik_dakika . ' minutes', strtotime($bitis_toplanti_saati))); // date('H:i:s', strtotime('+'.$toplanti_dakika + $hazirlik_dakika .' minutes',  strtotime($baslangic_saati_time)));


                }
               // print_r($salonSaatleriArray);
                $returnArr["salonSaatleri"] = $salonSaatleriArray;
                array_push($returnAnaArray, $returnArr);
            }

            $json = json_encode($returnAnaArray, JSON_UNESCAPED_UNICODE);
            echo $json;
        }


        /*  while($baslangic_saati_time < date('H:i:s', strtotime($bitis_saati)))
       {
        echo '<br>'.$baslangic_saati_time.'<br>';
        
        $baslangic_saati_time = date('H:i:s', strtotime('+'.$toplanti_dakika .' minutes',  strtotime($baslangic_saati_time)));
        }

       if($baslangic_saati_time < date('H:i:s', strtotime($bitis_saati)))
       {
           echo 'küçük';
       }
       else
       {
           echo 'büyük';
       }
       
        echo $baslangic_saati_time;*/
        // echo date('h:i:s', strtotime("+15 minutes", strtotime($baslangic_saati)));
        // return $baslangic_saati;

    } else {
        return 'Ayar tanımlı değil.';
    }
}

if (isset($_TARIH)) {
    ToplantiSaatleriGetir($_TARIH);
} else {
    ToplantiSaatleriGetir(date('Y/m/d'));
}
/*

$sql = $db->query("Select * from genel_ayar")->fetch(PDO::FETCH_ASSOC);
if( $sql )
print_r($sql);

echo '<br>'. $sql['baslangic_saati'];*/
