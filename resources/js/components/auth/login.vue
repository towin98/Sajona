<template>
    <v-app id="inspire">
        <v-container fluid fill-height style="background: #424242">
            <v-app-bar
                app
                class="elevation-0"
                dense
                style="background: #424242"
            >
                <v-spacer></v-spacer>
                <v-btn color="red" class="white--text" rounded>
                    <v-icon> date_range </v-icon>{{ date }}
                </v-btn>
            </v-app-bar>

            <v-layout align-center justify-center>
                <v-flex xs12 sm8 md4>
                    <v-card class="elevation-0" style="background: #424242">
                        <v-toolbar
                            class="d-flex justify-center rounded-0 elevation-0"
                            style="background: #424242"
                        >
                            <v-toolbar-title class="white--text">
                                <div class="d-flex justify-center">
                                    <v-icon size="80" dark>
                                        account_circle
                                    </v-icon>
                                </div>
                                <div class="justify-center">
                                    <h3>INICIO DE SESIÓN</h3>
                                </div>
                            </v-toolbar-title>
                        </v-toolbar>
                        <v-card-text class="mt-11">
                            <v-form>
                                <v-text-field
                                    prepend-icon="person"
                                    name="login"
                                    v-model="formData.email"
                                    label="Usuario"
                                    :error-messages="errors.email"
                                    type="text"
                                    dark
                                ></v-text-field>

                                <v-text-field
                                    id="password"
                                    prepend-icon="lock"
                                    name="password"
                                    v-model="formData.password"
                                    label="Contraseña"
                                    :error-messages="errors.password"
                                    type="password"
                                    @keyup.enter="login"
                                    dark
                                ></v-text-field>
                            </v-form>
                        </v-card-text>
                        <v-layout justify-end>
                            <v-card-actions class="mt-4">
                                <v-spacer></v-spacer>
                                <router-link
                                    :to="{ name: 'recuperar-clave' }"
                                    class="pr-3 text-decoration-none text-subtitle-2 white--text">
                                    <span>Recuperar Contraseña</span>
                                </router-link>
                                <v-btn
                                    color="success"
                                    class="text-none"
                                    v-on:click="login"
                                    >Ingresar
                                    <v-icon size="20" dark>
                                        done
                                    </v-icon></v-btn
                                >
                            </v-card-actions>
                        </v-layout>
                    </v-card>
                </v-flex>
                <loadingGeneral v-bind:overlayLoading="overlayLoading" />
            </v-layout>
        </v-container>
    </v-app>
</template>
<script>
import loadingGeneral from "../loadingGeneral/loadingGeneral.vue";
export default {
    components: { loadingGeneral },
    data() {
        return {
            formData: {
                email: "",
                password: "",
                device_name: "browser",
                closeSesion: "NO"
            },
            errors: {
                email: "",
                password: "",
            },

            date: "",
            overlayLoading: false,
        };
    },
    methods: {
        login(closeSesion = 'NO') {
            this.formData.closeSesion = closeSesion;
            this.overlayLoading = true;
            axios
                .post("/sajona/login", this.formData)
                .then((response) => {
                    localStorage.setItem("TOKEN_SAJONA", response.data.access_token);
                    this.$router.push("/modulos/dashboard");
                    this.overlayLoading = false;
                    this.$swal("Bienvenido");
                })
                .catch((errors) => {
                    if (errors.response.status == 500) {
                        this.overlayLoading = false;
                        this.$swal({
                            icon: "error",
                            title: "Intentalo más tarde.",
                        });
                    } else {
                        this.overlayLoading = false;
                        if (errors.response.status == 423) {
                            this.$swal({
                                title: errors.response.data.message,
                                text: errors.response.data.errors,
                                icon: 'warning',
                                showCancelButton: true,
                                confirmButtonColor: '#3085d6',
                                cancelButtonColor: '#d33',
                                cancelButtonText: 'N0',
                                confirmButtonText: 'SI'
                                }).then((result) => {
                                    if (result.isConfirmed) {
                                        this.login('SI');
                                    }
                                });
                        }else if(errors.response.status == 409 || errors.response.status == 422){
                            this.errors = errors.response.data.errors;
                        }
                    }
                });
        }
    },
    mounted() {
        const fecha = new Date();
        const month = fecha.toLocaleString("es-CO", { month: "long" });

        this.date = `${month.substring(0, 3)}/${fecha.getFullYear()}`;
    },
};
</script>
<style>
input {
    padding-left: 10px !important;
}
</style>
