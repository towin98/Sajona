<template>
    <div>
        <loadingGeneral v-bind:overlayLoading="overlayLoading" />
        <h4>Alerta</h4>
        <v-card elevation="2">
            <v-card-title class="rounded-sm">
                <span class="text-h6 font-weight-bold">Alerta de procesos</span>
            </v-card-title>
            <v-divider></v-divider>
            <v-card-text>

                <v-row>
                    <v-col cols="12" sm="4">
                        <div class="text-center">Rango alerta en días para el módulo propagación</div>

                        <v-row class="mt-4">
                            <!-- <v-col cols="6">
                                <v-text-field
                                    type="number"
                                    v-model="form.min_rang_propagacion"
                                    ref="min_rang_propagacion"
                                    dense
                                    label="Rag. Min"
                                    :error-messages="errors.min_rang_propagacion"
                                    :disabled="!$can(['CREAR', 'EDITAR'])"
                                ></v-text-field>
                            </v-col> -->
                            <v-col cols="6">
                                <v-text-field
                                    type="number"
                                    v-model="form.max_rang_propagacion"
                                    ref="max_rang_propagacion"
                                    dense
                                    label="Rag. max"
                                    :error-messages="errors.max_rang_propagacion"
                                    :disabled="!$can(['CREAR', 'EDITAR'])"
                                ></v-text-field>
                            </v-col>
                        </v-row>
                    </v-col>
                    <v-col cols="12" sm="4">
                        <div class="text-center">Rango alerta en días para el módulo Trans. Bolsa</div>

                        <v-row class="mt-4">
                            <!-- <v-col cols="6">
                                <v-text-field
                                    type="number"
                                    v-model="form.min_rang_bolsa"
                                    ref="min_rang_bolsa"
                                    dense
                                    label="Rag. Min"
                                    :error-messages="errors.min_rang_bolsa"
                                    :disabled="!$can(['CREAR', 'EDITAR'])"
                                ></v-text-field>
                            </v-col> -->
                            <v-col cols="6">
                                <v-text-field
                                    type="number"
                                    v-model="form.max_rang_bolsa"
                                    ref="max_rang_bolsa"
                                    dense
                                    label="Rag. Max"
                                    :error-messages="errors.max_rang_bolsa"
                                    :disabled="!$can(['CREAR', 'EDITAR'])"
                                ></v-text-field>
                            </v-col>
                        </v-row>

                    </v-col>
                    <v-col cols="12" sm="4">
                        <div class="text-center">Rango alerta en días para el módulo Trans. Campo</div>

                        <v-row class="mt-4">
                            <!-- <v-col cols="6">
                                <v-text-field
                                    type="number"
                                    v-model="form.min_rang_campo"
                                    ref="min_rang_campo"
                                    dense
                                    label="Rag. Min"
                                    :error-messages="errors.min_rang_campo"
                                    :disabled="!$can(['CREAR', 'EDITAR'])"
                                ></v-text-field>
                            </v-col> -->
                            <v-col cols="6">
                                <v-text-field
                                    type="number"
                                    v-model="form.max_rang_campo"
                                    ref="max_rang_campo"
                                    dense
                                    label="Rag. max"
                                    :error-messages="errors.max_rang_campo"
                                    :disabled="!$can(['CREAR', 'EDITAR'])"
                                ></v-text-field>
                            </v-col>
                        </v-row>

                    </v-col>
                </v-row>

            </v-card-text>
            <v-card-actions class="flex justify-end text-none">
                <v-btn  color="success"
                        :disabled="!$can(['CREAR', 'EDITAR'])"
                        v-on:click="fnAccion"> Guardar </v-btn>
            </v-card-actions>
        </v-card>
    </div>
</template>
<script>
import loadingGeneral from '../loadingGeneral/loadingGeneral.vue';
export default {
    components:{
        loadingGeneral
    },
    data() {
        return {
            form : {
                id                   : '',
                // min_rang_propagacion : 0,
                max_rang_propagacion : 0,

                // min_rang_bolsa : 0,
                max_rang_bolsa : 0,

                // min_rang_campo : 0,
                max_rang_campo : 0
            },
            errors: {
            },

            // Variable loading
            overlayLoading      : false,
        }
    },
    methods: {
        /* Muestra datos alerta si existe*/
        fnShow(){
            this.overlayLoading = true;
            axios
                .get(`/sajona/sistema/alerta`)
                .then((response) => {
                    const data = response.data.data;
                    if (data.length == 0) {
                    }else{
                        this.form.id                   = data.id;
                        // this.form.min_rang_propagacion = data.min_rang_propagacion;
                        this.form.max_rang_propagacion = data.max_rang_propagacion;
                        // this.form.min_rang_bolsa       = data.min_rang_bolsa;
                        this.form.max_rang_bolsa       = data.max_rang_bolsa;
                        // this.form.min_rang_campo       = data.min_rang_campo;
                        this.form.max_rang_campo       = data.max_rang_campo;
                    }
                    this.overlayLoading = false;
                })
                .catch((errores) => {
                    this.fnResponseError(errores);
                    this.overlayLoading = false;
                });
        },
        fnAccion(){
            if (this.form.id == "") {
                this.fnStore();
            }else{
                this.fnUpdate();
            }
        },
        fnStore() {
            this.overlayLoading = true;
            axios
                .post(`/sajona/sistema/alerta`, this.form)
                .then((response) => {
                    this.$swal(
                        response.data.message,
                        '',
                        'success'
                    );
                    this.errors = {};
                    this.overlayLoading = false;
                })
                .catch((errores) => {
                    this.errors = this.fnResponseError(errores);
                    this.overlayLoading = false;
                });
        },
        fnUpdate(){
            this.overlayLoading = true;
            axios
                .put(`/sajona/sistema/alerta/${this.form.id}`, this.form)
                .then((response) => {
                    this.errors = {};
                    this.$swal(
                        response.data.message,
                        '',
                        'success'
                    );
                    this.overlayLoading = false;
                })
                .catch((errores) => {
                    this.errors = this.fnResponseError(errores);
                    this.overlayLoading = false;
                });
        },
    },
    mounted(){
        this.fnShow();
    }
}
</script>
