<template>
    <div>
        <loadingGeneral v-bind:overlayLoading="overlayLoading"/>
        <h4>Cosecha</h4>
        <v-container fluid>
            <v-card elevation="2">
                <v-card-title class="rounded-sm">
                    <span class="text-h6 font-weight-bold">Nuevo</span>
                </v-card-title>
                <v-row class="pl-4 pr-4">
                    <v-col cols="6" sm="2" class="py-0 pl-0">
                        <v-subheader>Fecha Transplante Terreno</v-subheader>
                    </v-col>
                    <v-col cols="6" sm="4" class="py-0 pl-0">
                        <v-text-field
                            type="date"
                            v-model="form.tp_fecha"
                            ref="tp_fecha"
                            dense
                            :error-messages="errors.tp_fecha"
                            readonly
                        ></v-text-field>
                    </v-col>
                    <v-col cols="6" sm="2" class="py-0 pl-0">
                        <v-subheader>Fecha de Cosecha</v-subheader>
                    </v-col>
                    <v-col cols="6" sm="4" class="py-0 pl-0">
                        <v-text-field
                            type="date"
                            v-model="form.cos_fecha_cosecha"
                            ref="cos_fecha_cosecha"
                            :error-messages="errors.cos_fecha_cosecha"
                            dense
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
                            :error-messages="errors.id_lote"
                        ></v-text-field>
                    </v-col>

                    <!-- Resultadno Cantidad de plantas menos la bajas -->
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
                            readonly
                        ></v-text-field>
                    </v-col>

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
                        ></v-select>
                    </v-col>

                    <!-- Automatico, dias desde que se transplanto a campo hasta hoy.  -->
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
                        ></v-text-field>
                    </v-col>

                    <v-col cols="6" sm="2" class="py-0 pl-0">
                        <v-subheader>Ubicación</v-subheader>
                    </v-col>
                    <v-col cols="6" sm="4" class="py-0 pl-0">
                        <v-text-field
                            type="text"
                            v-model="form.tp_ubicacion"
                            ref="tp_ubicacion"
                            readonly
                            dense
                            :error-messages="errors.tp_ubicacion"
                        ></v-text-field>
                    </v-col>

                    <v-col cols="6" sm="2" class="py-0 pl-0">
                        <v-subheader>Peso verde Campo (Gramos)</v-subheader>
                    </v-col>
                    <v-col cols="6" sm="4" class="py-0 pl-0">
                        <v-text-field
                            type="number"
                            v-model="form.cos_peso_verde"
                            ref="cos_peso_verde"
                            dense
                            :error-messages="errors.cos_peso_verde"
                        ></v-text-field>
                    </v-col>
                    <v-col cols="6" sm="2" class="py-0 pl-0">
                        <v-subheader>Observaciones</v-subheader>
                    </v-col>
                    <v-col cols="6" sm="10">
                        <v-textarea
                            v-model="form.cos_observacion"
                            ref="cos_observacion"
                            label="Observacion"
                            outlined
                            dense
                            :error-messages="errors.cos_observacion"
                            rows="1"
                            >
                        </v-textarea>
                    </v-col>
                    <v-col cols="12" class="d-flex justify-end">
                        <v-btn
                            type="submit"
                            small
                            color="success"
                            class="white--text text-none"
                            tile
                            v-if="form.id_lote != ''"
                            v-on:click="guardarCosecha"
                        >
                            <v-icon> save </v-icon>Guardar
                        </v-btn>
                    </v-col>

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
                                @click="editCosecha(item)"
                            >
                                mdi-pencil
                            </v-icon>
                            <v-icon
                                small
                                @click="deleteCosecha(item)"
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
            token               : localStorage.getItem("token"),
            overlayLoading      : false,

            form: {
                tp_id              : '',
                tp_fecha           : '',
                cos_fecha_cosecha  : '',
                id_lote            : '',
                cos_numero_plantas : '',
                cos_estado_cosecha : '',
                cos_dias_floracion : '',
                tp_ubicacion       : '',
                cos_peso_verde     : '',
                cos_observacion    : '',
                hoy                : new Date().toLocaleDateString()
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
                { text: "Cantidad Planta Madre", value: "pro_cantidad_plantas_madres" },
                { text: "Fecha transplante Bolsa", value: "tp_fecha" },
                { text: "Fecha de cosecha", value: "cos_fecha_cosecha" },
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
                this.buscarCosechas();
            },
        },
        deep: true,
    },
    methods: {
        filterSearch(){
            this.loading = true;
            clearTimeout(this.debounce);
            this.debounce = setTimeout(() => {
                this.buscarCosechas(this.buscar);
            }, 800);
        },
        buscarCosechas(buscar = this.buscar) {
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
                    `/sajona/cosecha/buscar?length=${this.length}&start=${this.start}&orderColumn=${sortBy}&order=${sortDesc}&buscar=${buscar}`
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
        editCosecha(item){
            this.overlayLoading = true;
            axios
                .get(`/sajona/cosecha/${item.tp_id}`)
                .then((response) => {
                    let data = response.data.data;
                    this.form = data;
                    this.form.id_lote       = data.id_lote;
                    this.form.tp_id = item.tp_id; // Importante
                    this.overlayLoading = false;
                })
                .catch((errors) => {
                    this.overlayLoading = false;
                });

        },
        deleteCosecha(item){
            console.log(item);
            if (item.cos_fecha_cosecha == "") {
                this.$swal(
                    `El lote[${item.id_lote}] aun no tiene una cosecha.`,
                    'Solo los lotes que tengan cosecha pueden ser eliminados.',
                    'info'
                );
            }else{
                this.$swal({
                title: 'Quiere eliminar la cosecha?',
                text: `Se removera la cosecha del lote ${item.id_lote}!`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Si, Eliminarlo!'
                }).then((result) => {
                    if (result.isConfirmed) {

                        this.overlayLoading = true;
                        axios
                            .post(`/sajona/cosecha/delete/`,{tp_id : item.tp_id})
                            .then((response) => {
                                this.overlayLoading = false;
                                this.$swal(
                                    response.data.message,
                                    '',
                                    'success'
                                );
                                this.buscarCosechas();
                            })
                            .catch((errors) => {
                                this.overlayLoading = false;
                            });
                    }
                });
            }
        },
        guardarCosecha() {
            axios
                .post(`/sajona/cosecha`, this.form)
                .then((response) => {
                    this.errors = "";
                    this.$swal(
                        response.data.message,
                        '',
                        'success'
                    );
                    this.buscarCosechas();
                    this.limpiarCampos();
                })
                .catch((errores) => {
                    this.errors = errors.response.data.errors;
                });
        },
        limpiarCampos() {
            this.form.tp_id = '';
            this.$refs["tp_fecha"].reset();
            this.$refs["cos_fecha_cosecha"].reset();
            this.$refs["id_lote"].reset();
            this.form.id_lote = '';
            this.$refs["cos_numero_plantas"].reset();
            this.$refs["cos_estado_cosecha"].reset();
            this.$refs["cos_dias_floracion"].reset();
            this.$refs["tp_ubicacion"].reset();
            this.$refs["cos_peso_verde"].reset();
            this.$refs["cos_observacion"].reset();
        },
    },
    mounted() {
        window.axios.defaults.headers.common[
            "Authorization"
        ] = `Bearer ${this.token}`;
    },
};
</script>
