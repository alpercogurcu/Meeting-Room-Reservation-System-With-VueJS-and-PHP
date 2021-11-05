<template>
  <div>
    <div v-if="!login">
      <v-app id="inspire">
        <v-main>
          <v-container fluid fill-height @keydown.enter="Login">
            <v-layout align-center justify-center>
              <v-flex xs12 sm8 md4>
                <v-card class="elevation-12">
                  <v-alert color="red" dark v-if="loginWarning">
                    Hatalı Kullanıcı adı veya Parola
                  </v-alert>
                  <v-toolbar dark color="primary">
                    <v-toolbar-title>Yönetim Giriş Paneli</v-toolbar-title>
                  </v-toolbar>
                  <v-card-text>
                    <v-form>
                      <v-text-field
                        prepend-icon="mdi-account"
                        v-model="kullaniciAdi"
                        name="login"
                        label="Kullanıcı Adı"
                        type="text"
                      ></v-text-field>
                      <v-text-field
                        id="password"
                        prepend-icon="mdi-lock-alert"
                        v-model="parola"
                        name="password"
                        label="Parola"
                        type="password"
                      ></v-text-field>
                    </v-form>
                  </v-card-text>
                  <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn color="primary" @click="Login">Giriş Yap</v-btn>
                  </v-card-actions>
                </v-card>
              </v-flex>
            </v-layout>
          </v-container>
        </v-main>
      </v-app>
    </div>
    <div v-else>
      Yönetici Paneli
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
                  <v-card-title class="d-flex">
                    <div class="mr-auto">
                      {{ salon.salonAD }}
                    </div>
                    <div class="ml-auto">
                      <v-btn
                        class="mx-2"
                        fab
                        dark
                        x-small
                        color="primary"
                        @click.native="
                          salonDetayGoster(salon.salonAyarlari, salon.salonID)
                        "
                      >
                        <v-icon dark>
                          mdi-pencil
                        </v-icon>
                      </v-btn>

                      <v-btn
                        class="mx-2"
                        fab
                        dark
                        x-small
                        color="red"
                        @click="dialogResultSalonSil.salonadi = salon.salonAD; dialogResultSalonSil.salon_id = salon.salonID; dialogResultSalonSil.Show=true;"
                      >
                        <v-icon dark>
                          mdi-minus
                        </v-icon>
                      </v-btn>
                    </div>
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

              <v-col cols="12">
                <v-card elevation="2">
                  <v-card-title>
                    Yeni Salon Ekle
                  </v-card-title>

                  <v-card-subtitle>
                    <br />
                    <v-btn
                      class="mx-2"
                      dark
                      x-large
                      color="success"
                      block
                      @click="dialogSalonEkle = true"
                    >
                      <v-icon dark>
                        mdi-plus
                      </v-icon>
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
                ></v-text-field>

                <v-text-field
                  v-model="randevuBilgileri.bolum"
                  label="Bölüm"
                  required
                ></v-text-field>

                <v-text-field
                  v-model="randevuBilgileri.gorev"
                  label="Görev"
                  required
                ></v-text-field>

                <v-textarea
                  v-model="randevuBilgileri.aciklama"
                  label="Açıklama"
                  required
                ></v-textarea>

                <v-select
                  v-model="randevuBilgileri.toplantiTipi"
                  :items="toplantiTipleri"
                  label="Toplantı Tipi"
                  required
                ></v-select>
              </v-form>
            </v-card-text>

            <v-card-actions>
              <v-spacer></v-spacer>
              <v-btn
                color="primary"
                class="mr-4"
                @click="randevuSil"
                :disabled="aktifRandevu != '-1' ? false : true"
              >
                RANDEVUYU SİL
              </v-btn>
              <v-btn color="error" class="mr-4" @click="dialog = false">
                Kapat
              </v-btn>
              <v-btn
                :disabled="aktifRandevu != '-1' ? false : true"
                color="success"
                class="mr-4"
                @click="randevuKayit()"
              >
                Güncelle
              </v-btn>
            </v-card-actions>
          </v-card>
        </v-dialog>

        <v-dialog v-model="dialogSalonEkle" max-width="500px">
          <v-card>
            <v-card-title class="text-h5">
              Toplantı Salonu Ekle
            </v-card-title>
            <v-card-text>
              <v-form ref="form" v-model="vform">
                {{ aktifRandevu.toplantiTipi }}
                <v-text-field
                  v-model="salonBilgileri.salonadi"
                  label="Salon Adı"
                  required
                ></v-text-field>

                <v-text-field
                  v-model="salonBilgileri.aciklama"
                  label="Salon Açıklaması"
                  required
                ></v-text-field>
              </v-form>
            </v-card-text>

            <v-card-actions>
              <v-spacer></v-spacer>
              <v-btn color="success" class="mr-4" @click="salonKayit">
                Salonu Kaydet
              </v-btn>
              <v-btn
                color="error"
                class="mr-4"
                @click="dialogSalonEkle = false"
              >
                Kapat
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
              <v-toolbar color="primary" dark>BAŞARILI!</v-toolbar>

              <div
                class="success"
                style="display:flex; width:100%; height:100%;"
              >
                <div
                  style="padding:50px; margin:10px;"
                  class="white--text text-h4"
                >
                  {{ dialogSuccessText }}
                </div>
              </div>

              <v-card-actions class="justify-end">
                <v-btn text @click="dialogSuccess.value = false">Kapat</v-btn>
              </v-card-actions>
            </v-card>
          </template>
        </v-dialog>

        <v-dialog v-model="dialogSalonDuzenle" max-width="500px">
          <v-card>
            <v-card-title class="text-h5">
              Toplantı Salonu Bilgileri
            </v-card-title>
            <v-card-text>
              <v-form ref="form" v-model="vform">
                <v-menu
                  ref="menuBaslangic"
                  v-model="menuBaslangicSaati"
                  :close-on-content-click="false"
                  :nudge-right="40"
                  :return-value.sync="salonAyarSaatleri.baslangicsaati"
                  transition="scale-transition"
                  offset-y
                  max-width="290px"
                  min-width="290px"
                >
                  <template v-slot:activator="{ on, attrs }">
                    <v-text-field
                      v-model="salonAyarSaatleri.baslangicsaati"
                      label="Başlangıç Saatini Seçin"
                      prepend-icon="mdi-clock-time-four-outline"
                      readonly
                      v-bind="attrs"
                      v-on="on"
                    ></v-text-field>
                  </template>
                  <v-time-picker
                    v-if="menuBaslangicSaati"
                    v-model="salonAyarSaatleri.baslangicsaati"
                    format="24hr"
                    scrollable
                    use-seconds
                    full-width
                    @click:minute="
                      $refs.menuBaslangic.save(salonAyarSaatleri.baslangicsaati)
                    "
                  ></v-time-picker>
                </v-menu>

                <v-menu
                  ref="menuBitis"
                  v-model="menuBitisSaati"
                  :close-on-content-click="false"
                  :nudge-right="40"
                  :return-value.sync="salonAyarSaatleri.bitisSaati"
                  transition="scale-transition"
                  offset-y
                  max-width="290px"
                  min-width="290px"
                >
                  <template v-slot:activator="{ on, attrs }">
                    <v-text-field
                      v-model="salonAyarSaatleri.bitisSaati"
                      label="Bitiş Saatini Seçin"
                      prepend-icon="mdi-clock-time-four-outline"
                      readonly
                      v-bind="attrs"
                      v-on="on"
                    ></v-text-field>
                  </template>
                  <v-time-picker
                    v-if="menuBitisSaati"
                    v-model="salonAyarSaatleri.bitisSaati"
                    format="24hr"
                    scrollable
                    use-seconds
                    full-width
                    @click:minute="
                      $refs.menuBitis.save(salonAyarSaatleri.bitisSaati)
                    "
                  ></v-time-picker>
                </v-menu>

                <v-menu
                  ref="menuHazirlik"
                  v-model="menuHazirlikSuresi"
                  :close-on-content-click="false"
                  :nudge-right="40"
                  :return-value.sync="salonAyarSaatleri.hazirlikSuresi"
                  transition="scale-transition"
                  offset-y
                  max-width="290px"
                  min-width="290px"
                >
                  <template v-slot:activator="{ on, attrs }">
                    <v-text-field
                      v-model="salonAyarSaatleri.hazirlikSuresi"
                      label="Salon Hazırlık Süresini Seçin"
                      prepend-icon="mdi-clock-time-four-outline"
                      readonly
                      v-bind="attrs"
                      v-on="on"
                    ></v-text-field>
                  </template>
                  <v-time-picker
                    v-if="menuHazirlikSuresi"
                    v-model="salonAyarSaatleri.hazirlikSuresi"
                    format="24hr"
                    scrollable
                    use-seconds
                    full-width
                    @click:minute="
                      $refs.menuHazirlik.save(salonAyarSaatleri.hazirlikSuresi)
                    "
                  ></v-time-picker>
                </v-menu>

                <v-menu
                  ref="menuToplanti"
                  v-model="menuToplantiSuresi"
                  :close-on-content-click="false"
                  :nudge-right="40"
                  :return-value.sync="salonAyarSaatleri.toplantiSuresi"
                  transition="scale-transition"
                  offset-y
                  max-width="290px"
                  min-width="290px"
                >
                  <template v-slot:activator="{ on, attrs }">
                    <v-text-field
                      v-model="salonAyarSaatleri.toplantiSuresi"
                      label="Salon Toplantı Süresini Seçin"
                      prepend-icon="mdi-clock-time-four-outline"
                      readonly
                      v-bind="attrs"
                      v-on="on"
                    ></v-text-field>
                  </template>
                  <v-time-picker
                    v-if="menuToplantiSuresi"
                    v-model="salonAyarSaatleri.toplantiSuresi"
                    format="24hr"
                    scrollable
                    use-seconds
                    full-width
                    @click:minute="
                      $refs.menuToplanti.save(salonAyarSaatleri.toplantiSuresi)
                    "
                  ></v-time-picker>
                </v-menu>
              </v-form>
            </v-card-text>

            <v-card-actions>
              <v-spacer></v-spacer>

              <v-btn
                color="error"
                class="mr-4"
                @click="dialogSalonDuzenle = false"
              >
                Kapat
              </v-btn>
              <v-btn
                color="success"
                class="mr-4"
                @click="salonAyarSaatleriGuncelle"
              >
                Güncelle
              </v-btn>
            </v-card-actions>
          </v-card>
        </v-dialog>

        <v-dialog v-model="dialogResultSalonSil.Show" max-width="290">
          <v-card>
            <v-card-title class="text-h5">
              SALON İŞLEMLERİ
            </v-card-title>

            <v-card-text>
             {{dialogResultSalonSil.salonadi}} adlı salonu <b>silmek</b> istediğinizden emin misiniz?
            </v-card-text>

            <v-card-actions>
              <v-spacer></v-spacer>

              <v-btn
                color="green darken-1"
                text
                @click.native="
                  dialogResultSalonSil.Show = false;
                  salonSil(dialogResultSalonSil.salon_id);
                "
              >
                SALONU SİL
              </v-btn>

              <v-btn
                color="green darken-1"
                text
                @click="
                  dialogResultSalonSil.Show = false;

                "
              >
                KAPAT
              </v-btn>
            </v-card-actions>
          </v-card>
        </v-dialog>
      </div>
    </div>
  </div>
</template>

<script>
import axios from "axios";

export default {
  name: "yonetim",
  data() {
    return {
      login: false,
      loginWarning: false,
      kullaniciAdi: "",
      parola: "",
      vform: "",
      salonlar: [],
      randevuBilgileri: {
        id: "",
        salon_id: "",
        baslangicsaati: "",
        personel: "",
        bolum: "",
        gorev: "",
        aciklama: "",
        toplantiTipi: "",
      },

      salonBilgileri: {
        salonadi: "",
        aciklama: "",
      },

      salonSaatleri: {
        id: "",
        baslangicSaati: "",
        bitisSaati: "",
        hazirlikSuresi: "",
        toplantiSuresi: "",
        salon_id: "",
      },

      salonAyarSaatleri: {
        baslangicsaati: "",
        bitisSaati: "",
        hazirlikSuresi: "",
        toplantiSuresi: "",
        salon_id: "",
      },
      menuBaslangicSaati: false,
      menuBitisSaati: false,
      menuHazirlikSuresi: false,
      menuToplantiSuresi: false,

      dateEvents: [],
      aktifRandevu: [],
      dialog: false,
      dialogWait: false,
      dialogSuccess: false,
      dialogSuccessText: "",
      dialogSalonEkle: false,
      dialogSalonDuzenle: false,

      dialogResultSalonSil: {
        Show: false,
        salon_id: '',
        salonadi :'',
      },

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
    DialogSuccessShow(text) {
      this.dialogSuccessText = text;
      this.dialogSuccess = true;
    },

    Login() {
      if (this.kullaniciAdi == "admin" && this.parola == "OPT*.") {
        this.login = true;
      } else {
        this.loginWarning = true;
        this.parola = "";
        setTimeout(() => {
          this.loginWarning = false;
        }, 2000);
      }
    },
    async getPosts() {
      this.dialogWait = true;
      await axios
        .get(
          "http://192.168.2.70/toplanti/yenisalonlistesi.php?tarih=" +
            this.picker
        )
        .then((response) => {
          this.salonlar = response.data;

          /*
          for (var i = 0; i < response.data.length; i++) {"
            for (var k = 0; k < response.data[i].salonSaatleri.length; k++) {
              if (response.data[i].salonSaatleri[k].randevu != "-1") {
                console.log(response.data[i].salonSaatleri[k].randevu.tarih);
              }
            //  console.log(response.data[i].salonSaatleri[k].randevu);
            }
          } */
          //  console.log(response.data.length)
        })
        .catch((error) => {
          console.log(error + " error");
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
        this.randevuBilgileri.id = randevu.id;
        this.randevuBilgileri.personel = randevu.personel;
        this.randevuBilgileri.bolum = randevu.bolum;
        this.randevuBilgileri.gorev = randevu.gorev;
        this.randevuBilgileri.aciklama = randevu.aciklama;
        this.randevuBilgileri.toplantiTipi = randevu.toplantiTipi;
        this.randevuBilgileri.baslangicsaati = baslangicsaati;
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

            //   this.dialogSuccess = true;
            this.getPosts();
          }
        });
    },

    // RANDEVU-RANDEVU-RANDEVU-RANDEVU-RANDEVU-RANDEVU-RANDEVU-RANDEVU-RANDEVU-RANDEVU-RANDEVU-RANDEVU-RANDEVU-RANDEVU-
    randevuSil() {
      let data = new FormData();
      data.append("randevu_id", this.randevuBilgileri.id);
      data.append("secretKey", "Manolya");

      let config = {
        header: {
          "Content-Type": "multipart/form-data",
        },
      };

      axios
        .post("http://192.168.2.70/toplanti/sil.php", data, config)
        .then((res) => {
          console.log(res);
          if (res.data == "Başarılı") {
            this.dialog = false;
            this.dialogWait = false;
            this.DialogSuccessShow("Randevu Başarıyla Silindi!");
            //            this.dialogSuccess = true;
            this.getPosts();
          }
        });
    },

    // SALON-SALON-SALON-SALON-SALON-SALON-SALON-SALON-SALON-SALON-SALON-SALON-SALON-SALON-SALON-SALON-SALON-SALON-SALON-SALON-
    salonKayit() {
      let config = {
        header: {
          "Content-Type": "multipart/form-data",
        },
      };

      let data = new FormData();
      data.append("salonadi", this.salonBilgileri.salonadi);
      data.append("aciklama", this.salonBilgileri.aciklama);
      data.append("islem", "ekle");

      axios
        .post("http://192.168.2.70/toplanti/salonislemleri.php", data, config)
        .then((res) => {
          console.log(res.data);
          if (res.data == "Başarılı") {
            this.dialogSalonEkle = false;
            this.dialogWait = false;
            this.DialogSuccessShow("Salon Başaryla Eklendi!");
            this.dialogSuccess = true;
            this.getPosts();
          }
        });
    },

    salonSil(id) {
  

        let config = {
          header: {
            "Content-Type": "multipart/form-data",
          },
        };

        let data = new FormData();
        data.append("id", id);
        data.append("islem", "sil");

        axios
          .post("http://192.168.2.70/toplanti/salonislemleri.php", data, config)
          .then((res) => {
            console.log(res.data);
            if (res.data == "Başarılı") {
              this.dialogWait = false;
              this.DialogSuccessShow("Salon Başarıyla Silindi!");
              this.dialogSuccess = true;
              this.getPosts();
            }
          });
      
    },

    salonDetayGoster(salon, salonID) {
      var pbaslangicSaati = salon.baslangicSaati;
      let bitisSaati = salon.bitisSaati;
      let hazirlikSuresi = salon.hazirlikSuresi;
      let toplantiSuresi = salon.toplantiSuresi;

      this.salonAyarSaatleri.baslangicsaati = pbaslangicSaati;
      this.salonAyarSaatleri.bitisSaati = bitisSaati;
      this.salonAyarSaatleri.hazirlikSuresi = hazirlikSuresi;
      this.salonAyarSaatleri.toplantiSuresi = toplantiSuresi;
      this.salonAyarSaatleri.salon_id = salonID;
      this.dialogSalonDuzenle = true;
      console.log(salonID);
      console.log(salon);
    },

    salonAyarSaatleriGuncelle() {
      console.log("giriyo mu lan bura");
      let config = {
        header: {
          "Content-Type": "multipart/form-data",
        },
      };

      let data = new FormData();
      data.append("salon_id", this.salonAyarSaatleri.salon_id);
      data.append("baslangicSaati", this.salonAyarSaatleri.baslangicsaati);
      data.append("bitisSaati", this.salonAyarSaatleri.bitisSaati);
      data.append("hazirlikSuresi", this.salonAyarSaatleri.hazirlikSuresi);
      data.append("toplantiSuresi", this.salonAyarSaatleri.toplantiSuresi);
      data.append("islem", "guncelle");

      axios
        .post("http://192.168.2.70/toplanti/salonislemleri.php", data, config)
        .then((res) => {
          console.log(res.data + " --- res data");
          if (res.data == "Başarılı") {
            this.dialogWait = false;
            this.DialogSuccessShow(
              "Salon Başarıyla Düzenlendi! Salona Ait Tüm Randevular Silindi!"
            );
            this.dialogSuccess = true;
            this.getPosts();
          }
        });
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
