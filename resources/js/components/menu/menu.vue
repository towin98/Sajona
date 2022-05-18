<template>
    <div>
        <v-app id="inspire">
            <loadingGeneral v-bind:overlayLoading="overlayLoading" />
            <v-app-bar app dark class="grey darken-4" dense clipped-left>
                <v-app-bar-nav-icon
                    dark
                    @click="drawer = !drawer"
                ></v-app-bar-nav-icon>
                <v-spacer></v-spacer>
                <v-btn color="white" class="black--text" rounded>
                    <v-icon> date_range </v-icon>{{ date }}
                </v-btn>

                <v-menu offset-y rounded="lg">
                    <template v-slot:activator="{ on, attrs }">
                        <v-btn v-bind="attrs" v-on="on">
                            <v-icon size="40"> account_circle </v-icon>
                            {{ cRol }}
                        </v-btn>
                    </template>
                    <v-list>
                        <v-list-item link>
                            <v-list-item-title v-on:click="logout"
                                >Cerrar Sesión</v-list-item-title
                            >
                        </v-list-item>
                    </v-list>
                </v-menu>
            </v-app-bar>
            <v-navigation-drawer
                v-model="drawer"
                app
                class="grey darken-4"
                clipped
            >
                <v-list dense dark>

                    <v-list-group
                        v-if="this.sistemaGroup"
                        prepend-icon="view_module"
                        :value="menu.modulos.active"
                        v-on:click="titleProceso = 'Modulos'">

                        <template v-slot:activator>
                            <v-list-item-content>
                                <v-list-item-title v-text="'Modulos'"></v-list-item-title>
                            </v-list-item-content>
                        </template>

                        <v-list-item
                            :to="{ name: 'inicio' }"
                            v-on:click="titleProceso = 'inicio'"
                        >
                            <v-list-item-icon>
                                <v-icon>navigate_next</v-icon>
                            </v-list-item-icon>

                            <v-list-item-content>
                                <v-list-item-title>Inicio</v-list-item-title>
                            </v-list-item-content>
                        </v-list-item>

                        <v-list-item
                            v-if="this.propagacion"
                            :to="{ name: 'propagacion' }"
                            v-on:click="titleProceso = 'Propagación'"
                        >
                            <v-list-item-icon>
                                <v-icon>navigate_next</v-icon>
                            </v-list-item-icon>

                            <v-list-item-content>
                                <v-list-item-title>Propagación</v-list-item-title>
                            </v-list-item-content>
                        </v-list-item>

                        <v-list-item
                            v-if="this.plantaMadre"
                            :to="{ name: 'planta-madre' }"
                            v-on:click="titleProceso = 'Planta Madre'"
                        >
                            <v-list-item-icon>
                                <v-icon>navigate_next</v-icon>
                            </v-list-item-icon>

                            <v-list-item-content>
                                <v-list-item-title>Planta Madre</v-list-item-title>
                            </v-list-item-content>
                        </v-list-item>

                        <v-list-item
                            v-if="this.transplanteBolsa"
                            :to="{ name: 'transplante-bolsa' }"
                            v-on:click="titleProceso = 'Transplante Bolsa'"
                        >
                            <v-list-item-icon>
                                <v-icon>navigate_next</v-icon>
                            </v-list-item-icon>

                            <v-list-item-content>
                                <v-list-item-title
                                    >Transplante a bolsa</v-list-item-title
                                >
                            </v-list-item-content>
                        </v-list-item>

                        <v-list-item
                            v-if="this.transplanteCampo"
                            :to="{ name: 'transplante-campo' }"
                            v-on:click="titleProceso = 'Transplante Campo'"
                        >
                            <v-list-item-icon>
                                <v-icon>navigate_next</v-icon>
                            </v-list-item-icon>

                            <v-list-item-content>
                                <v-list-item-title
                                    >Transplante a campos</v-list-item-title
                                >
                            </v-list-item-content>
                        </v-list-item>

                        <v-list-item
                            v-if="this.cosecha"
                            :to="{ name: 'cosecha' }"
                            v-on:click="titleProceso = 'Cosecha'"
                        >
                            <v-list-item-icon>
                                <v-icon>navigate_next</v-icon>
                            </v-list-item-icon>

                            <v-list-item-content>
                                <v-list-item-title>Cosecha</v-list-item-title>
                            </v-list-item-content>
                        </v-list-item>

                        <v-list-item
                            v-if="this.postCosecha"
                            :to="{ name: 'post-cosecha' }"
                            v-on:click="titleProceso = 'Post Cosecha'"
                        >
                            <v-list-item-icon>
                                <v-icon>navigate_next</v-icon>
                            </v-list-item-icon>

                            <v-list-item-content>
                                <v-list-item-title>Post Cosecha</v-list-item-title>
                            </v-list-item-content>
                        </v-list-item>

                        <v-list-item
                            v-if="this.bajas"
                            :to="{ name: 'bajas' }"
                            v-on:click="titleProceso = 'Bajas'"
                        >
                            <v-list-item-icon>
                                <v-icon>navigate_next</v-icon>
                            </v-list-item-icon>

                            <v-list-item-content>
                                <v-list-item-title>Bajas</v-list-item-title>
                            </v-list-item-content>
                        </v-list-item>

                        <v-list-item
                            v-if="this.reportes"
                            :to="{ name: 'reportes' }"
                            v-on:click="titleProceso = 'Reporte'"
                        >
                            <v-list-item-icon>
                                <v-icon>navigate_next</v-icon>
                            </v-list-item-icon>

                            <v-list-item-content>
                                <v-list-item-title>Reportes</v-list-item-title>
                            </v-list-item-content>
                        </v-list-item>
                    </v-list-group>

                    <!-- Menu Sistema -->
                    <v-list-group
                        v-if="this.sistemaGroup"
                        prepend-icon="settings"
                        :value="menu.sistema.active"
                        v-on:click="titleProceso = 'Sistema'">
                        <template v-slot:activator>
                            <v-list-item-content>
                                <v-list-item-title v-text="'Sistema'"></v-list-item-title>
                            </v-list-item-content>
                        </template>

                        <v-list-item
                            v-if="this.parametros"
                            link
                            :to="{ name: 'parametros' }"
                            v-on:click="titleProceso = 'Parametros'">

                            <v-list-item-icon>
                                <v-icon>navigate_next</v-icon>
                            </v-list-item-icon>

                            <v-list-item-content>
                                <v-list-item-title>Parametros</v-list-item-title>
                            </v-list-item-content>
                        </v-list-item>

                        <v-list-item
                            v-if="this.alerta"
                            link
                            :to="{ name: 'alerta' }"
                            v-on:click="titleProceso = 'alerta'">

                            <v-list-item-icon>
                                <v-icon>navigate_next</v-icon>
                            </v-list-item-icon>

                            <v-list-item-content>
                                <v-list-item-title>Alerta</v-list-item-title>
                            </v-list-item-content>
                        </v-list-item>

                        <v-list-item
                            v-if="this.cambioClave"
                            link
                            :to="{ name: 'cambio-clave' }"
                            v-on:click="titleProceso = 'Cambio Clave'">

                            <v-list-item-icon>
                                <v-icon>navigate_next</v-icon>
                            </v-list-item-icon>

                            <v-list-item-content>
                                <v-list-item-title>Cambio de Clave</v-list-item-title>
                            </v-list-item-content>
                        </v-list-item>

                    </v-list-group>
                </v-list>

            </v-navigation-drawer>
            <v-main>
                <v-container>
                    <mainHeader v-bind:proceso="titleProceso"></mainHeader>
                    <router-view></router-view>
                </v-container>
            </v-main>
        </v-app>
    </div>
</template>

<script>
window.Vue = require("vue").default;
import permisos from "../../rolPermission/Permissions.vue";
import { commons }  from '../../commons/commons.js';
// Creamos un Mixin Global para poder obtener los permisos desde cualquier vista.
Vue.mixin(permisos);
Vue.mixin(commons);
import mainHeader from "./mainHeader.vue";
import loadingGeneral from "../loadingGeneral/loadingGeneral.vue";
export default {

    name: "menuSajona",
    components: {
        loadingGeneral,
        mainHeader,
    },
    data() {
        return {
            drawer: null,
            token: localStorage.getItem("TOKEN_SAJONA"),
            date: "",

            // Variables para dedicidir si se muestra menu
            menu : {
                modulos : {
                    active : ""
                },
                sistema : {
                    active : ""
                },
            },

            propagacion      : false,
            plantaMadre      : false,
            transplanteBolsa : false,
            transplanteCampo : false,
            cosecha          : false,
            postCosecha      : false,
            bajas            : false,
            reportes         : false,
            sistemaGroup     : false,
            parametros       : false,
            alerta           : false,
            cambioClave      : false,
            // FIN Variables para dedicidir si se muestra menu.

            /* AQUI VAN VARIABLES CON RESPECTO A PERMISOS Y ROLES START*/
            cRol: "",
            intervalId: 0,
            /* END VARIABLES */

            titleProceso: "",
            overlayLoading: false,
        };
    },
    methods: {
        logout() {
            this.overlayLoading = true;
            axios
                .post("/sajona/logout")
                .then((response) => {
                    clearInterval(this.intervalId);
                    localStorage.removeItem("TOKEN_SAJONA");
                    this.$router.push("/");
                    this.overlayLoading = false;
                })
                .catch((errors) => {
                    clearInterval(this.intervalId);
                    localStorage.removeItem("TOKEN_SAJONA");
                    this.$router.push("/");
                    this.overlayLoading = false;
                });
        },
    },
    async created() {
        window.axios.defaults.headers.common["Authorization"] = `Bearer ${this.token}`;

        // Obteniendo nombre de la ruta.
        if (this.$route.name) {
            this.titleProceso = this.$route.name.replace("-", " ");

            switch (this.$route.name) {
                case 'parametros':
                case 'alerta':
                case 'cambio-clave':
                    this.menu.sistema.active = true; // Desplegando menu de Sistema
                break;
                case 'alerta':
                case 'inicio':
                case 'propagacion':
                case 'planta-madre':
                case 'transplante-bolsa':
                case 'transplante-campo':
                case 'cosecha':
                case 'post-cosecha':
                case 'bajas':
                case 'reportes':
                    this.menu.modulos.active = true; // Desplegando menu de Modulos
                break;
            }
        }
        this.overlayLoading = true;
        await this.$fnConsultaPermisosUsuario();

        const fecha = new Date();
        const month = fecha.toLocaleString("es-CO", { month: "long" });

        this.date = `${month.substring(0, 3)}/${fecha.getFullYear()}`;

        /* DEPENDIENDO DEL ROL DEL USUARIO SE MUESTRA MENU. */
        await this.buscaNombreRolUser();
        this.overlayLoading = false;

        this.intervalId = setInterval(async () => {
            await this.buscaNombreRolUser();
        }, 20000);

        switch (this.cRol) {
            case "Agronomo":
                this.propagacion      = true;
                this.plantaMadre      = true;
                this.transplanteBolsa = true;
                this.transplanteCampo = true;
                this.cosecha          = true;
                this.postCosecha      = true;
                this.bajas            = true;
                this.reportes         = true;
                this.sistemaGroup     = true;
                this.parametros       = true;
                this.alerta           = true;
                this.cambioClave      = true;
                break;
            case "Gerente":
                this.propagacion      = true;
                this.sistemaGroup     = true;
                this.cambioClave      = true;
                // this.plantaMadre       = true;
                // this.transplanteBolsa  = true;
                // this.transplanteCampo  = true;
                // this.cosecha           = true;
                // this.postCosecha       = true;
                // this.bajas             = true;
                // this.reportes          = true;
                break;
            case "Auxiliar":
                this.propagacion      = true;
                this.bajas            = true;
                this.sistemaGroup     = true;
                this.cambioClave      = true;
                break;
        }
    },
    mounted() {},
};
</script>
<style>
.v-list-item--active {
    background: rgba(82, 82, 82, 0.479);
}
.v-list-item--active {
    color :white !important;
}
</style>
