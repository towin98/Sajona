<template>
    <div>
        <loadingGeneral v-bind:overlayLoading="overlayLoading" />
        <h4>Bajas</h4>
        <v-container fluid>
            <v-card elevation="2">
                <v-card-title class="rounded-sm py-2">
                    <span class="text-h6 font-weight-bold">Listando de Bajas de lotes</span>
                </v-card-title>
                <v-divider></v-divider>

                <!-- start Data table -->
                <v-row>
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
                            :pageCount="numberOfPages"
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
                            <template v-slot:item.acciones="{ item }">
                                <v-icon
                                    color="primary"
                                    class="mr-2"
                                    @click="consultarLotesBajas(item.id_lote)"
                                    title="Editar Bajas del lote"
                                >
                                    mdi-pencil
                                </v-icon>
                            </template>
                        </v-data-table>
                    </v-col>
                </v-row>
                <!-- end Data table -->
            </v-card>
        </v-container>

        <!-- Modal bajas -->
        <v-dialog v-model="modal" persistent width="1000px">
            <v-card>
                <v-card-title class="text-h5 py-2">
                    Editar
                    <v-spacer></v-spacer>
                    <v-btn color="black" icon @click="modal = false" title="Cerrar Modal de bajas.">
                        <v-icon dark>
                            close
                        </v-icon>
                    </v-btn>
                </v-card-title>
                <v-divider></v-divider>
                <v-card-text>

                    <h1 class="pt-4 pb-4"> Lote: {{ id_lote }} </h1>

                    <v-row v-for="(baja, index) in dataBajasModal.bajas" v-bind:key="index">
                        <v-col cols="3" sm="3">
                            <v-text-field
                                type="date"
                                v-model="baja.bj_fecha"
                                ref="bj_fecha"
                                filled
                                label="Fecha de baja"
                                :error-messages="(error.errores[index].bj_fecha != undefined) ? error.errores[index].bj_fecha : ''"
                            ></v-text-field>
                        </v-col>
                        <v-col cols="2" sm="2">
                            <v-text-field
                                type="number"
                                v-model="baja.bj_cantidad"
                                ref="bj_cantidad"
                                filled
                                :error-messages="(error.errores[index].bj_cantidad != undefined) ? error.errores[index].bj_cantidad : ''"
                                label="Cantidad Bajas"
                            ></v-text-field>
                        </v-col>

                        <v-col cols="2" sm="2" class="">
                            <v-select
                                v-model="baja.bj_fase_cultivo"
                                ref="bj_fase_cultivo"
                                :items="['Esquejes','Bolsa', 'Campo', 'Cosecha']"
                                :error-messages="(error.errores[index].bj_fase_cultivo != undefined) ? error.errores[index].bj_fase_cultivo : ''"
                                filled
                                label="Fase de Cultivo"
                            ></v-select>
                        </v-col>
                        <v-col cols="4" sm="4">
                            <v-textarea
                                v-model="baja.bj_observacion"
                                ref="bj_observacion"
                                :error-messages="(error.errores[index].bj_observacion != undefined) ? error.errores[index].bj_observacion : ''"
                                label="Observaciones"
                                rows="1"
                                filled>
                            </v-textarea>
                        </v-col>
                        <v-col cols="1" sm="1" class="mt-3">
                            <v-btn
                                class="mx-2"
                                fab
                                dark
                                small
                                color="red"
                                title="Elimina registro de baja."
                                v-on:click="fnEliminarBaja(index)">
                                <v-icon dark>
                                    cancel
                                </v-icon>
                            </v-btn>
                        </v-col>
                    </v-row>
                    <h1 class="text-center" v-if="dataBajasModal.bajas.length == 0">Sin Bajas</h1>
                    <div class="d-flex justify-end">
                        <v-btn
                            type="submit"
                            small
                            color="success"
                            class="white--text text-none"
                            tile
                            title="Añade un nueva fila para agregar baja."
                            v-on:click="fnNuevaBaja"
                        >
                        <v-icon> add </v-icon>Añadir Baja
                        </v-btn>
                    </div>

                    <div class="d-flex justify-center">
                        <v-btn
                            type="submit"
                            small
                            color="success"
                            class="white--text text-none"
                            tile
                            title="Guarda todos los registros de Bajas."
                            v-on:click="guardarBajas"
                        >
                        <v-icon> save </v-icon>Guardar
                        </v-btn>
                    </div>
                </v-card-text>
            </v-card>
        </v-dialog>
    </div>
</template>
<script>
import loadingGeneral from "../loadingGeneral/loadingGeneral.vue";
export default {
    components: {
        loadingGeneral,
    },
    data() {
        return {
            token: localStorage.getItem("token"),
            overlayLoading   : false,
            /* start Variables Modal bajas*/
            modal     : false,
            /* end Variables Modal bajas*/

            /* Variables Table. */
            // Tabla filtro.
            debounce: null,
            buscar: "",
            // Table listar
            page: 1,
            totalRegistros: 0,
            numberOfPages: 0,
            loading: false,
            options: {},
            headers: [
                { text: "Id Lote", align: "start", value: "id_lote" },
                { text: "Fecha Ultima Baja", value: "bj_fecha" },
                { text: "Descartes", value: "descartes" },
                { text: "Cantidad", value: "cantidadSemillasEsquejes" },
                { text: "Acciones", value: "acciones", sortable: false },
            ],
            dataSet: [],
            /* end variables Table. */

            id_lote : "",

            dataBajasModal:{
                bajas: [],
                id_lote: ""
            },

            error:{
                errores: [],
            }
        };
    },
    watch: {
        options: {
            handler() {
                this.filterSearch();
            },
        },
        deep: true,
    },
    methods: {
        buscarBajas() {
            this.overlayLoading = true;
            this.loading = true;
            let { page, itemsPerPage, sortBy, sortDesc } = this.options;

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

            axios
                .get(
                    `/sajona/baja/buscar?page=${page}&length=${itemsPerPage}&orderColumn=${sortBy}&order=${sortDesc}&buscar=${this.buscar}`
                )
                .then((response) => {
                    this.loading = false;
                    this.dataSet = response.data.data;
                    this.totalRegistros = response.data.total;
                    this.numberOfPages = response.data.last_page;

                    this.overlayLoading = false;
                })
                .catch((errors) => {
                    this.overlayLoading = false;
                    this.loading = false;
                    this.dataSet = [];
                });
        },
        filterSearch() {
            clearTimeout(this.debounce);
            this.debounce = setTimeout(() => {
                this.buscarBajas(this.buscar);
            }, 600);
        },
        consultarLotesBajas(id_lote){
            this.overlayLoading = true;
            axios
                .get(`/sajona/baja/${id_lote}`)
                .then((response) => {

                    this.id_lote = id_lote;
                    if (response.data.data.length == 0) {
                        this.dataBajasModal.id_lote = id_lote;
                        this.error.errores        = [];
                        this.dataBajasModal.bajas = [];
                        this.fnNuevaBaja();
                    }else{
                        this.error.errores          = [];
                        this.dataBajasModal.bajas   = response.data.data;
                        this.dataBajasModal.id_lote = id_lote;

                        // Rellenando json de errores vacios
                        for (let i = 0; i < response.data.data.length; i++) {
                            this.fnErrorJson();
                        }
                    }
                    this.modal = true;
                    this.overlayLoading = false;
                })
                .catch((errors) => {
                    this.overlayLoading = false;
                });
        },
        guardarBajas(){
            this.overlayLoading = true;
            axios
                .post(`/sajona/baja`, this.dataBajasModal)
                .then((response) => {
                    this.$swal(
                        response.data.message,
                        '',
                        'success'
                    );
                    this.overlayLoading = false;
                    this.modal = false;
                    this.buscarBajas();
                })
                .catch((errors) => {
                    this.error.errores = errors.response.data.errores;
                    this.overlayLoading = false;
                });
        },
        fnNuevaBaja(){
            this.dataBajasModal.bajas.push({
                bj_pro_id_lote  : "",
                bj_fecha        : "",
                bj_cantidad     : "",
                bj_fase_cultivo : "",
                bj_observacion  : ""
            });
            this.fnErrorJson();
        },
        // Método agrega una posición en el json de errores.
        fnErrorJson(){
            this.error.errores.push({
            });
        },
        fnEliminarBaja(index) {
            this.dataBajasModal.bajas.splice(index, 1);
            this.error.errores.splice(index, 1);
        },
    },
};
</script>
