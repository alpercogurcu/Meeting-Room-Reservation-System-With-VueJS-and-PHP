import Vue from 'vue';
import Vuetify from 'vuetify/lib/framework';

Vue.use(Vuetify);


import tr from 'vuetify/lib/locale/tr';
//import trTr from 'vuetify/src/locale/tr';

Vue.component('my-component', {
    methods: {
      changeLocale () {
        this.$vuetify.lang.current = 'tr'
      },
    },
  })

export default new Vuetify({
    lang:{
        locales: {tr},
        current: 'tr',
    }
});
