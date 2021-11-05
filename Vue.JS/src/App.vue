<template>
  
<v-app>
    <v-system-bar height="23"></v-system-bar>
    <v-app-bar app color="primary" dark>
      

      <div class="d-flex align-center">
        <a href='/'>
        <v-img
          alt="Vuetify Logo"
          class="shrink mr-2"
          contain
          src="https://optimak.com.tr/wp-content/uploads/2020/09/optimakkonveyorstrec-sarmahat-sonu-1.png"
          transition="scale-transition"
          width="60"
   
        /></a>
        Toplantı Salonları Randevu Paneli
     
 
      </div>
      
      <v-spacer></v-spacer>
       
      <v-btn text v-bind:href="this.$route.name == 'yonetim' ? '/': '/yonetim'">
        <span class="mr-2">{{this.$route.name == 'yonetim' ? 'ANASAYFA': 'YÖNETİM'}}</span>
        <v-icon>mdi-open-in-new</v-icon>
      </v-btn>
    </v-app-bar>

    <v-main>
      <router-view />
    </v-main>
  </v-app>
</template>

<script>
//import router from "./router";


export default {
  name: 'App',

  components: {
 
  },

  data: () => ({
    //
  }),
};
</script>
