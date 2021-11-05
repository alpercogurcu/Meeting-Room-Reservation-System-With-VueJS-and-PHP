import Vue from 'vue'
import VueRouter from 'vue-router'

import salonListesi from '../components/salonListesi'
import yonetim from '../components/yonetim'

Vue.use(VueRouter);

const routes =[
    {
        path:'/',
        name:'index',
        component: salonListesi,
        meta:{
            reload:true,
        },
    },
    {
        path:'/yonetim',
        name:'yonetim',
        component: yonetim,
        meta:{
            reload:true,
        },
    }
]


const router = new VueRouter({
    mode: 'history',
    base: process.env.BASE_URL,
    routes
  })

  export default router