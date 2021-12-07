<template>
    <div>
        <v-app id="inspire">
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
                <v-list dense rounded dark temporary>
                    <v-list-item :to="{ name: 'inicio' }">
                        <v-list-item-icon>
                            <v-icon>home</v-icon>
                        </v-list-item-icon>

                        <v-list-item-content>
                            <v-list-item-title>Inicio</v-list-item-title>
                        </v-list-item-content>
                    </v-list-item>
                    <v-list-item
                        v-if="this.propagacion"
                        :to="{ name: 'propagacion' }"
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
                    >
                        <v-list-item-icon>
                            <v-icon>navigate_next</v-icon>
                        </v-list-item-icon>

                        <v-list-item-content>
                            <v-list-item-title
                                >Cosecha</v-list-item-title
                            >
                        </v-list-item-content>
                    </v-list-item>

                    <v-list-item
                        v-if="this.postCosecha"
                        :to="{ name: 'post-cosecha' }"
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
                    >
                        <v-list-item-icon>
                            <v-icon>navigate_next</v-icon>
                        </v-list-item-icon>

                        <v-list-item-content>
                            <v-list-item-title>Reportes</v-list-item-title>
                        </v-list-item-content>
                    </v-list-item>
                </v-list>
            </v-navigation-drawer>
            <v-main>
                <v-container>
                    <router-view></router-view>
                </v-container>
            </v-main>
        </v-app>
    </div>
</template>

<script>
import { misRol } from "../../rolPermission/misRol.js";
export default {
    name: "menuSajona",
    data() {
        return {
            drawer: null,
            token: localStorage.getItem("token"),
            date: "",

            propagacion         : false,
            plantaMadre         : false,
            transplanteBolsa    : false,
            transplanteCampo    : false,
            cosecha             : false,
            postCosecha         : false,
            bajas               : false,
            reportes            : false,

            cRol                : ''
        };
    },
    mixins: [misRol],
    methods: {
        logout() {
            axios
                .post("/api/logout")
                .then((response) => {
                    localStorage.removeItem("token");
                    this.$router.push("/");
                })
                .catch((errors) => {
                    localStorage.removeItem("token");
                    this.$router.push("/");
                    console.log(errors);
                });
        },
    },
    async created(){
        const fecha = new Date();
        const month = fecha.toLocaleString("es-CO", { month: "long" });

        this.date = `${month.substring(0, 3)}/${fecha.getFullYear()}`;
        window.axios.defaults.headers.common["Authorization"] = `Bearer ${this.token}`;

        /* DEPENDIENDO DEL ROL DEL USUARIO SE MUESTRA MENU. */
        this.cRol = await this.buscaNombreRolUser();

        switch (this.cRol) {
            case 'Agronomo':
                this.propagacion       = true;
                this.plantaMadre       = true;
                this.transplanteBolsa  = true;
                this.transplanteCampo  = true;
                this.cosecha           = true;
                this.postCosecha       = true;
                this.bajas             = true;
                this.reportes          = true;
                break;
            case 'Gerente':
                this.propagacion       = true;
                this.plantaMadre       = true;
                this.transplanteBolsa  = true;
                this.transplanteCampo  = true;
                this.cosecha           = true;
                this.postCosecha       = true;
                this.bajas             = true;
                this.reportes          = true;
                break;
            case 'Auxiliar':
                this.propagacion       = true;
                break;
        }

        // this.propagacion       = await  this.canRol({ roles:'Agronomo,Gerente,Auxiliar'});
        // this.plantaMadre       = await  this.canRol({ roles:'Agronomo,Gerente,Auxiliar'});
        // this.transplanteBolsa  = await  this.canRol({ roles:'Agronomo,Gerente,Auxiliar'});
        // this.transplanteCampo  = await  this.canRol({ roles:'Agronomo,Gerente,Auxiliar'});
        // this.cosecha           = await  this.canRol({ roles:'Agronomo,Gerente,Auxiliar'});
        // this.postCosecha       = await  this.canRol({ roles:'Agronomo,Gerente,Auxiliar'});
        // this.bajas             = await  this.canRol({ roles:'Agronomo,Gerente,Auxiliar'});
        // this.reportes          = await  this.canRol({ roles:'Agronomo,Gerente,Auxiliar'});
    },

    async beforeMount(){
    },
    async mounted() {
        window.onunload = function (){
            localStorage.clear();
        };
    },
};
</script>
