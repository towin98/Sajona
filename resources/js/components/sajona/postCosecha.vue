<template>
    <div>
        <loadingGeneral v-bind:overlayLoading="overlayLoading"/>
        <h4>Post Cosecha</h4>
        <v-container fluid>
            <v-card elevation="2">
                <v-card-title class="rounded-sm">
                    <span class="text-h6 font-weight-bold">{{ titleAccion }}</span>
                </v-card-title>
                <v-row class="pl-4 pr-4">

                    <v-col cols="6" sm="2" class="py-0 pl-0">
                        <v-subheader>Fecha cosecha</v-subheader>
                    </v-col>
                    <v-col cols="6" sm="2" class="py-0 pl-0">
                        <v-text-field
                            type="date"
                            v-model="form.cos_fecha_cosecha"
                            ref="cos_fecha_cosecha"
                            dense
                            :error-messages="errors.cos_fecha_cosecha"
                            readonly
                            :disabled="titleAccion == 'Nuevo'"
                        ></v-text-field>
                    </v-col>
                    <v-col cols="6" sm="2" class="py-0 pl-0">
                        <v-subheader>F.I Secado</v-subheader>
                    </v-col>
                    <v-col cols="6" sm="2" class="py-0 pl-0">
                        <v-text-field
                            type="date"
                            v-model="form.post_fecha_ini_secado"
                            ref="post_fecha_ini_secado"
                            dense
                            :error-messages="errors.post_fecha_ini_secado"
                            :disabled="titleAccion == 'Nuevo'"
                        ></v-text-field>
                    </v-col>
                    <v-col cols="6" sm="2" class="py-0 pl-0">
                        <v-subheader>F.F Secado</v-subheader>
                    </v-col>
                    <v-col cols="6" sm="2" class="py-0 pl-0">
                        <v-text-field
                            type="date"
                            v-model="form.post_fecha_fin_secado"
                            ref="post_fecha_fin_secado"
                            :error-messages="errors.post_fecha_fin_secado"
                            dense
                            :disabled="titleAccion == 'Nuevo'"
                        ></v-text-field>
                    </v-col>

                    <v-col cols="6" sm="2" class="py-0 pl-0">
                        <v-subheader>Id Lote</v-subheader>
                    </v-col>
                    <v-col cols="6" sm="4" class="py-0 pl-0">
                        <v-text-field
                            type="number"
                            v-model="form.id_lote"
                            ref="id_lote"
                            dense
                            readonly
                            :error-messages="errors.id_lote"
                            :disabled="titleAccion == 'Nuevo'"
                        ></v-text-field>
                    </v-col>

                    <!-- Resultado Cantidad de plantas - menos la bajas -->
                    <v-col cols="6" sm="2" class="py-0 pl-0">
                        <v-subheader>N° Plantas</v-subheader>
                    </v-col>
                    <v-col cols="6" sm="4" class="py-0 pl-0">
                        <v-text-field
                            type="number"
                            v-model="form.cos_numero_plantas"
                            ref="cos_numero_plantas"
                            dense
                            :error-messages="errors.cos_numero_plantas"
                            disabled
                        ></v-text-field>
                    </v-col>

                    <!-- POR DEFINIR ESTADO, ESTE VALOR ES TRAIDO DE COSECHA -->
                    <v-col cols="6" sm="2" class="py-0 pl-0">
                        <v-subheader>Estado de Cosecha</v-subheader>
                    </v-col>
                    <v-col cols="6" sm="4" class="py-0 pl-0">
                        <v-select
                            v-model="form.cos_estado_cosecha"
                            ref="cos_estado_cosecha"
                            :items="['Estado 1', 'Estado 2']"
                            dense
                            :error-messages="errors.cos_estado_cosecha"
                            :disabled="titleAccion == 'Nuevo'"
                        ></v-select>
                    </v-col>

                    <!-- Automatico, TRAER VALOR DESDE COSECHA  -->
                    <v-col cols="6" sm="2" class="py-0 pl-0">
                        <v-subheader>Días de Floración</v-subheader>
                    </v-col>
                    <v-col cols="6" sm="4" class="py-0 pl-0">
                        <v-text-field
                            v-model="form.cos_dias_floracion"
                            ref="cos_dias_floracion"
                            dense
                            :error-messages="errors.cos_dias_floracion"
                            readonly
                            :disabled="titleAccion == 'Nuevo'"
                        ></v-text-field>
                    </v-col>

                    <!--  VALOR DE COSECHA -->
                    <v-col cols="6" sm="2" class="py-0 pl-0">
                        <v-subheader>Peso verde campo</v-subheader>
                    </v-col>
                    <v-col cols="6" sm="2" class="py-0 pl-0">
                        <v-text-field
                            type="text"
                            v-model="form.cos_peso_verde"
                            ref="cos_peso_verde"
                            readonly
                            dense
                            :error-messages="errors.cos_peso_verde"
                            :disabled="titleAccion == 'Nuevo'"
                        ></v-text-field>
                    </v-col>

                    <v-col cols="6" sm="2" class="py-0 pl-0">
                        <v-subheader>Peso flor verde</v-subheader>
                    </v-col>
                    <v-col cols="6" sm="2" class="py-0 pl-0">
                        <v-text-field
                            type="number"
                            v-model="form.post_peso_flor_verde"
                            ref="post_peso_flor_verde"
                            v-on:keyup="getPorcentajeHumedad(),getBiomasa()"
                            dense
                            :error-messages="errors.post_peso_flor_verde"
                            :disabled="titleAccion == 'Nuevo'"
                        ></v-text-field>
                    </v-col>


                    <v-col cols="6" sm="2" class="py-0 pl-0">
                        <v-subheader>Peso flor seco</v-subheader>
                    </v-col>
                    <v-col cols="6" sm="2" class="py-0 pl-0">
                        <v-text-field
                            type="number"
                            v-model="form.post_peso_flor_seco"
                            ref="post_peso_flor_seco"
                            dense
                            v-on:keyup="getPorcentajeHumedad()"
                            :error-messages="errors.post_peso_flor_seco"
                            :disabled="titleAccion == 'Nuevo'"
                        ></v-text-field>
                    </v-col>

                    <v-col cols="6" sm="2" class="pl-0 pb-0">
                        <v-subheader>Biomasa</v-subheader>
                    </v-col>
                    <v-col cols="6" sm="4" class="pl-0 pb-0">
                        <v-text-field
                            type="number"
                            v-model="form.post_peso_biomasa"
                            ref="post_peso_biomasa"
                            dense
                            :error-messages="errors.post_peso_biomasa"
                            label="Peso verde campo (Cosecha) - peso flor verde"
                            :disabled="titleAccion == 'Nuevo'"
                            readonly
                        ></v-text-field>
                    </v-col>

                    <v-col cols="6" sm="2" class="pl-0 pb-0">
                        <v-subheader>Porcentaje Humedad</v-subheader>
                    </v-col>
                    <v-col cols="6" sm="4" class="pl-0 pb-0">
                        <v-text-field
                            type="number"
                            v-model="form.post_porcentaje_humedad"
                            ref="post_porcentaje_humedad"
                            dense
                            :error-messages="errors.post_porcentaje_humedad"
                            :rules="post_porcentaje_humedad"
                            label="Peso flor verde - peso flor seco"
                            :disabled="titleAccion == 'Nuevo'"
                            readonly
                        ></v-text-field>
                    </v-col>
                    <v-col cols="6" sm="2" class="py-0 pl-0">
                        <v-subheader>% CBD</v-subheader>
                    </v-col>
                    <v-col cols="6" sm="4" class="py-0 pl-0">
                        <v-text-field
                            type="number"
                            v-model="form.post_cbd"
                            ref="post_cbd"
                            dense
                            :error-messages="errors.post_cbd"
                            :disabled="titleAccion == 'Nuevo'"
                        ></v-text-field>
                    </v-col>

                    <v-col cols="6" sm="2" class="py-0 pl-0">
                        <v-subheader>% THC</v-subheader>
                    </v-col>
                    <v-col cols="6" sm="4" class="py-0 pl-0">
                        <v-text-field
                            type="number"
                            v-model="form.post_thc"
                            ref="post_thc"
                            dense
                            :error-messages="errors.post_thc"
                            :disabled="titleAccion == 'Nuevo'"
                        ></v-text-field>
                    </v-col>
                    <v-col cols="6" sm="2" class="py-0 pl-0">
                        <v-subheader>Observaciones</v-subheader>
                    </v-col>
                    <v-col cols="6" sm="10">
                        <v-textarea
                            v-model="form.post_observacion"
                            ref="post_observacion"
                            label="Observaciones"
                            outlined
                            dense
                            :error-messages="errors.post_observacion"
                            rows="2"
                            :disabled="titleAccion == 'Nuevo'"
                            >
                        </v-textarea>
                    </v-col>
                    <v-col cols="12" class="d-flex justify-end" v-if="form.id_lote != ''">
                        <v-btn
                            type="submit"
                            small
                            color="success"
                            class="white--text text-none"
                            tile
                            v-on:click="guardarPostCosecha"
                        >
                            <v-icon> save </v-icon>Guardar
                        </v-btn>
                    </v-col>

                    <!-- Table -->
                    <v-col cols="12">
                        <v-card-title>
                            <v-text-field
                                type="text"
                                append-icon="mdi-magnify"
                                label="Buscar"
                                single-line
                                hide-details
                                v-model="buscar"
                                @input="filterSearch"
                            ></v-text-field>
                        </v-card-title>
                        <v-data-table
                            :page="page"
                            :headers="headers"
                            :items="dataSet"
                            :options.sync="options"
                            :server-items-length="totalRegistros"
                            :loading="loading"
                            class="elevation-1"
                            :items-per-page="5"
                            item-key="id_lote"
                            :footer-props="{
                                'items-per-page-options': [3, 5, 10, 15],
                            }"
                            sort-by="id_lote"
                            :sort-desc="true"
                            no-data-text="Sin registros"
                        >
                        <template v-slot:item.actions="{ item }">
                            <v-icon
                                small
                                class="mr-2"
                                @click="editPostCosecha(item)"
                            >
                                mdi-pencil
                            </v-icon>
                            <v-icon
                                small
                                @click="deletePostCosecha(item)"
                            >
                                mdi-delete
                            </v-icon>
                        </template>
                        </v-data-table>
                    </v-col>
                </v-row>
            </v-card>
        </v-container>
    </div>
</template>
<script>
import loadingGeneral   from '../loadingGeneral/loadingGeneral.vue';
export default {
    components:{
        loadingGeneral
    },
    data() {
        return {
            token               : localStorage.getItem("TOKEN_SAJONA"),
            overlayLoading      : false,
            titleAccion         : 'Nuevo',

            post_porcentaje_humedad: [
                valor => valor > 0  || "El valor porcentaje humedad debe ser positivo.",
            ],

            form: {
                pos_id                  : '',
                cos_id                  : '',
                cos_fecha_cosecha       : '',
                post_fecha_ini_secado   : '',
                post_fecha_fin_secado   : '',
                id_lote                 : '',
                cos_numero_plantas      : '',
                cos_estado_cosecha      : '',
                cos_dias_floracion      : '',
                cos_peso_verde          : '',
                post_peso_flor_verde    : '',
                post_peso_biomasa            : '',
                post_peso_flor_seco     : '',
                post_cbd                : '',
                post_thc                : '',
                post_porcentaje_humedad : '',
                post_observacion        : ''
            },
            errors: {
            },

            // Tabla filtro.
            debounce              : null,
            buscar                : '',
            // Table listar
            page                : 1,
            totalRegistros      : 0,
            loading             : true,
            options             : {},
            headers             : [
                { text: "Id Lote", align: "start", value: "id_lote" },
                { text: "fecha Inicio Secado", value: "post_fecha_ini_secado" },
                { text: "Fecha Fin Secado", value: "post_fecha_fin_secado" },
                { text: "Estado", value: "estado" },
                { text: "Acciones", value: "actions" }
            ],
            dataSet: [],
            startData : 0,
            length    : 0
        };
    },

    watch: {
        options: {
            handler() {
                this.buscarPostCosechas();
            },
        },
        deep: true,
    },
    methods: {
        filterSearch(){
            this.loading = true;
            clearTimeout(this.debounce);
            this.debounce = setTimeout(() => {
                this.buscarPostCosechas(this.buscar);
            }, 800);
        },
        buscarPostCosechas(buscar = this.buscar) {
            this.overlayLoading = true;
            let { page, itemsPerPage, sortBy, sortDesc } = this.options;

            // Obteniendo rangos de consultado paginación.
            this.start = itemsPerPage * (page - 1);
            this.length= this.start + itemsPerPage;

            if (sortDesc[0] == true) {
                sortBy = sortBy[0];
                sortDesc = "DESC";
            } else if (sortDesc[0] == false) {
                sortBy = sortBy[0];
                sortDesc = "ASC";
            } else {
                sortBy = "";
                sortDesc = "";
            }

            this.loading = true;
            axios
                .get(
                    `/sajona/post-cosecha/buscar?length=${this.length}&start=${this.start}&orderColumn=${sortBy}&order=${sortDesc}&buscar=${buscar}`
                )
                .then((response) => {
                    this.loading = false;
                    this.dataSet = response.data.data;
                    this.totalRegistros = response.data.total;
                    this.overlayLoading = false;
                })
                .catch((errors) => {
                    this.loading = false;
                    this.overlayLoading = false;
                    // console.log(errors.response.data);
                });
        },
        editPostCosecha(item){
            this.titleAccion = 'Editar';
            this.overlayLoading = true;
            axios
                .get(`/sajona/post-cosecha/${item.cos_id}`)
                .then((response) => {
                    let data = response.data.data;
                    this.form = {
                        ... data
                    };
                    this.overlayLoading = false;
                })
                .catch((errors) => {
                    this.overlayLoading = false;
                });

        },
        deletePostCosecha(item){
            this.titleAccion = 'Nuevo'
            if (item.pos_id == "") { // Id de post cosecha
                this.$swal(
                    `El lote[${item.id_lote}] aun no tiene una post cosecha.`,
                    'Solo los lotes que tengan post cosecha pueden ser eliminados.',
                    'info'
                );
            }else{
                this.$swal({
                title: 'Quiere eliminar la post cosecha?',
                text: `Se removera la post cosecha del lote ${item.id_lote}!`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Si, Eliminarlo!'
                }).then((result) => {
                    if (result.isConfirmed) {

                        this.overlayLoading = true;
                        axios
                            .post(`/sajona/post-cosecha/delete`,{pos_id : item.pos_id})
                            .then((response) => {
                                this.overlayLoading = false;
                                this.$swal(
                                    response.data.message,
                                    '',
                                    'success'
                                );
                                this.buscarPostCosechas();
                            })
                            .catch((errors) => {
                                let text = '';
                                if (errors.response.data.errors.pos_id === undefined) {
                                    text = errors.response.data.errors;
                                }else{
                                    text = errors.response.data.errors.pos_id;
                                }
                                this.$swal({
                                    icon: 'error',
                                    title: `${errors.response.data.message}`,
                                    text: `${text}`,
                                })
                                this.overlayLoading = false;
                            });
                    }
                });
            }
        },
        getPorcentajeHumedad(){
            this.form.post_porcentaje_humedad = this.form.post_peso_flor_verde - this.form.post_peso_flor_seco;
        },
        getBiomasa(){
            this.form.post_peso_biomasa = this.form.cos_peso_verde - this.form.post_peso_flor_verde;
        },
        guardarPostCosecha() {
            axios
                .post(`/sajona/post-cosecha`, this.form)
                .then((response) => {
                    this.errors = "";
                    this.$swal(
                        response.data.message,
                        '',
                        'success'
                    );
                    this.buscarPostCosechas();
                    this.limpiarCampos();
                })
                .catch((errores) => {
                    this.errors = errores.response.data.errors;
                });
        },
        limpiarCampos() {
            this.form.pos_id                  = '';
            this.form.cos_id                  =  '',
            this.form.cos_fecha_cosecha       =  '',
            this.form.post_fecha_ini_secado   =  '',
            this.form.post_fecha_fin_secado   =  '',
            this.form.id_lote                 =  '',
            this.form.cos_numero_plantas      =  '',
            this.$refs["cos_estado_cosecha"].reset();
            this.form.cos_dias_floracion      =  '',
            this.form.cos_peso_verde          =  '',
            this.form.post_peso_flor_verde    =  '',
            this.form.post_peso_biomasa       =  '',
            this.form.post_peso_flor_seco     =  '',
            this.form.post_cbd                =  '',
            this.form.post_thc                =  '',
            this.form.post_porcentaje_humedad =  '',
            this.form.post_observacion        =  ''
        },
    },
    mounted() {
        window.axios.defaults.headers.common[
            "Authorization"
        ] = `Bearer ${this.token}`;
    },
};
</script>



