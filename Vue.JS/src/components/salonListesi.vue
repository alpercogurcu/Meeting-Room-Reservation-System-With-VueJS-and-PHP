<template>
  <div>
    <v-card max-width="100%" class="mx-auto">
      <v-container>
        <div v-if="salonlar.length > 0">
          {{ picker }} || {{ salonlar[0].hafta }}. HAFTA
        </div>
        <br />
        <br />
        <v-row justify="center">
          <v-date-picker
            v-model="picker"
            v-bind:full-width="
              this.$vuetify.breakpoint.name == 'xs' ? true : false
            "
            :events="dateEvents"
            event-color="green lighten-1"
          ></v-date-picker>
        </v-row>
        <v-row dense>
          <v-col v-for="(salon, i) in salonlar" :key="i" cols="12">
            <v-card elevation="2">
              <v-card-title>
                {{ salon.salonAD }}
              </v-card-title>
              <v-card-subtitle
                >Açıklama: {{ salon.salonAciklama }}
                <br />

                <v-btn
                  v-for="(saat, j) in salon.salonSaatleri"
                  :key="j"
                  class="ml-2 mt-5"
                  rounded
                  small
                  v-bind:color="
                    saat.randevu != '-1' ? 'orange darken-4' : 'primary'
                  "
                  @click.native="
                    randevuDetay(
                      salon.salonSaatleri[j].randevu,
                      salon,
                      saat.baslangicsaati
                    )
                  "
                >
                  {{ saat.baslangicsaati }} - {{ saat.bitissaati }}
                </v-btn>
              </v-card-subtitle>
            </v-card>
          </v-col>
        </v-row>
      </v-container>
    </v-card>

    <v-dialog v-model="dialog" max-width="500px">
      <v-card>
        <v-card-title class="text-h5">
          Toplantı Salonu Bilgileri
        </v-card-title>
        <v-card-text>
          <v-form ref="form" v-model="vform">
            {{ aktifRandevu.toplantiTipi }}
            <v-text-field
              v-model="randevuBilgileri.personel"
              label="Personel Ad Soyad"
              required
              :readonly="aktifRandevu == '-1' ? false : true"
            ></v-text-field>

            <v-text-field
              v-model="randevuBilgileri.bolum"
              label="Bölüm"
              required
              :readonly="aktifRandevu == '-1' ? false : true"
            ></v-text-field>

            <v-text-field
              v-model="randevuBilgileri.gorev"
              label="Görev"
              required
              :readonly="aktifRandevu == '-1' ? false : true"
            ></v-text-field>

            <v-textarea
              v-model="randevuBilgileri.aciklama"
              label="Açıklama"
              required
              :readonly="aktifRandevu == '-1' ? false : true"
            ></v-textarea>

            <v-select
              v-model="randevuBilgileri.toplantiTipi"
              :items="toplantiTipleri"
              label="Toplantı Tipi"
              required
              :readonly="aktifRandevu == '-1' ? false : true"
            ></v-select>
          </v-form>
        </v-card-text>

        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn color="error" class="mr-4" @click="dialog = false">
            Kapat
          </v-btn>
          <v-btn
            :disabled="aktifRandevu == '-1' ? false : true"
            color="success"
            class="mr-4"
            @click="randevuKayit()"
          >
            Kaydet
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>

    <v-dialog v-model="dialogWait" hide-overlay persistent width="300">
      <v-card color="primary" dark>
        <v-card-text>
          <br />
          Lütfen Bekleyin
          <v-progress-linear
            indeterminate
            color="white"
            class="mb-0"
          ></v-progress-linear>
          <br />
        </v-card-text>
      </v-card>
    </v-dialog>

    <v-dialog
      transition="dialog-bottom-transition"
      max-width="600"
      v-model="dialogSuccess"
    >
      <template v-slot:default="dialogSuccess">
        <v-card>
          <v-toolbar color="primary" dark>Randevu Başarıyla Alındı</v-toolbar>

          <div class="success" style="display:flex; width:100%; height:100%;">
            <div style="padding:50px; margin:10px;" class="white--text text-h4">
              Randevu Başarıyla Kaydedildi!
            </div>
          </div>

          <v-card-actions class="justify-end">
            <v-btn text @click="dialogSuccess.value = false">Kapat</v-btn>
          </v-card-actions>
        </v-card>
      </template>
    </v-dialog>
  </div>
</template>

<script>
//   {{ picker }} || {{ salonlar[0].hafta }}. HAFTA
import axios from "axios";

export default {
  name: "salonListesi",
  data() {
    return {
      vform: "",
      salonlar: [],
      randevuBilgileri: {
        salon_id: "",
        baslangicsaati: "",
        personel: "",
        bolum: "",
        gorev: "",
        aciklama: "",
        toplantiTipi: "",
      },
      dateEvents: [],
      aktifRandevu: [],
      dialog: false,
      dialogWait: false,
      dialogSuccess: false,
      errors: [],
      toplantiTipleri: [
        "Müşteri İle",
        "Tedarikçi İle",
        "Bankacı İle",
        "Personel İle",
        "Diğer (Açıklama Belirtin)",
      ],
      picker: new Date(Date.now() - new Date().getTimezoneOffset() * 60000)
        .toISOString()
        .substr(0, 10),
    };
  },
  methods: {
    async getPosts() {
      this.dialogWait = true;
      await axios
        .get(
          "http://192.168.2.70/toplanti/yenisalonlistesi.php?tarih=" +
            this.picker
        )
        .then((response) => {
          this.salonlar = response.data;
        
          for (var i = 0; i < response.data.length; i++) {
            for (var k = 0; k < response.data[i].salonSaatleri.length; k++) {
              if (response.data[i].salonSaatleri[k].randevu != "-1") {
                console.log(response.data[i].salonSaatleri[k].randevu.tarih);
              }
            //  console.log(response.data[i].salonSaatleri[k].randevu);
            }
          }
          //  console.log(response.data.length)
        })
        .catch((error) => {
          this.errors.push(error);
        });
      this.dialogWait = false;
    },

    randevuDetay(randevu, salonbilgileri, baslangicsaati) {
      this.aktifRandevu = randevu;
      this.randevuBilgileri.salon_id = salonbilgileri.salonID;

      if (randevu == -1) {
        this.randevuBilgileri.personel = "";
        this.randevuBilgileri.bolum = "";
        this.randevuBilgileri.gorev = "";
        this.randevuBilgileri.aciklama = "";
        this.randevuBilgileri.toplantiTipi = "";
        this.randevuBilgileri.baslangicsaati = baslangicsaati;
      } else {
        this.randevuBilgileri.personel = randevu.personel;
        this.randevuBilgileri.bolum = randevu.bolum;
        this.randevuBilgileri.gorev = randevu.gorev;
        this.randevuBilgileri.aciklama = randevu.aciklama;
        this.randevuBilgileri.toplantiTipi = randevu.toplantiTipi;
        this.randevuBilgileri.baslangicsaati = baslangicsaati;

        console.log(this.randevuBilgileri);
      }
      this.dialog = true;
    },

    randevuKayit() {
      this.dialogWait = true;
      let data = new FormData();
      data.append("salon_id", this.randevuBilgileri.salon_id);
      data.append("baslangicsaati", this.randevuBilgileri.baslangicsaati);
      data.append("personel", this.randevuBilgileri.personel);
      data.append("bolum", this.randevuBilgileri.bolum);
      data.append("gorev", this.randevuBilgileri.gorev);
      data.append("aciklama", this.randevuBilgileri.aciklama);
      data.append("toplantiTipi", this.randevuBilgileri.toplantiTipi);
      data.append("tarih", this.picker);

      let config = {
        header: {
          "Content-Type": "multipart/form-data",
        },
      };

      axios
        .post("http://192.168.2.70/toplanti/kayit.php", data, config)
        .then((res) => {
          if (res.data == "Eklendi") {
            this.dialog = false;
            this.dialogWait = false;
            this.dialogSuccess = true;
            this.getPosts();
          }
          console.log(res);
        });

      console.log(this.aktifRandevu);
    },
  },
  watch: {
    picker(val) {
      this.picker = val;
      this.getPosts();
    },
  },

  created() {
    this.getPosts();
  },
};
</script>

<style></style>
