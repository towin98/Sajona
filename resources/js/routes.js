import Vue from 'vue';
import VueRouter from 'vue-router';

Vue.use(VueRouter);

/*auth*/
import login from './components/auth/login.vue'
import recuperarPass from './components/auth/recuperarPass.vue'

/*Menu sajona*/
import menuSajona from './components/menu/menu.vue'
import inicio from './components/sajona/inicio.vue'
import propagacion from './components/sajona/propagacion.vue'
import plantaMadre from './components/sajona/plantaMadre.vue'
import trasplanteBolsa from './components/sajona/trasplanteBolsa.vue'
import trasplanteCampo from './components/sajona/trasplanteCampo.vue'
import cosecha from './components/sajona/cosecha.vue'
import postCosecha from './components/sajona/postCosecha.vue'
import bajas from './components/sajona/bajas.vue'
import reportes from './components/sajona/reportes.vue'

import parametros from './components/config/parametros.vue'
import alerta from './components/config/alerta.vue'
import cambioClave from './components/config/cambioClave.vue'
/*Menu end*/

import errors from './components/errors/404.vue'

const router = new VueRouter({
    mode: 'history',
    routes: [
        {
            path: '/',
            component: login,
            name: 'login',
            meta: {guest: true}
        },
        {
            path: '/auth/recuperar-clave',
            component: recuperarPass,
            name: 'recuperar-clave',
            meta: {guest: true}
        },
        {
            path: '/modulos',
            component: menuSajona,
            meta: {requiresAuth: true},
            children:[

                {
                    path: 'dashboard',
                    component: inicio,
                    name: 'inicio',
                },
                {
                    path: 'propagacion',
                    component: propagacion,
                    name: 'propagacion',
                },
                {
                    path: 'planta-madre',
                    component: plantaMadre,
                    name: 'planta-madre',
                },
                {
                    path: 'trasplante-bolsa',
                    component: trasplanteBolsa,
                    name: 'trasplante-bolsa',
                },
                {
                    path: 'trasplante-campo',
                    component: trasplanteCampo,
                    name: 'trasplante-campo',
                },
                {
                    path: 'cosecha',
                    component: cosecha,
                    name: 'cosecha',
                },
                {
                    path: 'post-cosecha',
                    component: postCosecha,
                    name: 'post-cosecha',
                },
                {
                    path: 'bajas',
                    component: bajas,
                    name: 'bajas',
                },
                {
                    path: 'reportes',
                    component: reportes,
                    name: 'reportes',
                }
            ],
        },
        {
            path: '/sistema',
            component: menuSajona,
            meta: {requiresAuth: true},
            children:[
                {
                    path: 'parametros',
                    component: parametros,
                    name: 'parametros',
                },
                {
                    path: 'alerta',
                    component: alerta,
                    name: 'alerta',
                },
                {
                    path: 'cambio-clave',
                    component: cambioClave,
                    name: 'cambio-clave',
                }
            ],
        },
        { path: '*', component: errors }
    ]
})
export default router;
