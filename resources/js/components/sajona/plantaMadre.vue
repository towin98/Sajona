<template>
    <div>
        <loadingGeneral v-bind:overlayLoading="overlayLoading" />
        <h4>Lote / PLanta madre</h4>
        <v-container fluid>
            <v-card elevation="2">
                <v-card-title class="rounded-sm py-2">
                    <span class="text-h6 font-weight-bold">Nuevo</span>
                </v-card-title>
                <v-divider></v-divider>

                <v-form
                    v-on:submit.prevent="buscarLotes"
                    ref="form"
                    lazy-validation
                >
                    <v-row class="mt-3 pl-4 pr-4">
                        <v-col cols="6" sm="2" class="pa-0">
                            <v-subheader>Fecha de inicio</v-subheader>
                        </v-col>
                        <v-col cols="6" sm="4" class="pa-0">
                            <v-menu
                                v-model="menuDateInicio"
                                :close-on-content-click="false"
                                :nudge-right="40"
                                transition="scale-transition"
                                offset-y
                                min-width="auto"
                            >
                                <template v-slot:activator="{ on, attrs }">
                                    <v-text-field
                                        v-model="form.fecha_inicio"
                                        append-icon="mdi-calendar"
                                        v-bind="attrs"
                                        v-on="on"
                                        ref="fecha_inicio"
                                        :error-messages="errors.fecha_inicio"
                                        dense
                                        :disabled="!$can(['LISTAR'])"
                                    >
                                    </v-text-field>
                                </template>
                                <v-date-picker
                                    v-model="form.fecha_inicio"
                                    @input="menuDateInicio = false"
                                    locale="es-CO"
                                >
                                </v-date-picker>
                            </v-menu>
                        </v-col>

                        <v-col cols="6" sm="2" class="pa-0">
                            <v-subheader>Fecha de fin</v-subheader>
                        </v-col>
                        <v-col cols="6" sm="4" class="pa-0">
                            <v-menu
                                v-model="menuDateFin"
                                :close-on-content-click="false"
                                :nudge-right="40"
                                transition="scale-transition"
                                offset-y
                                min-width="auto"
                            >
                                <template v-slot:activator="{ on, attrs }">
                                    <v-text-field
                                        v-model="form.fecha_fin"
                                        append-icon="mdi-calendar"
                                        v-bind="attrs"
                                        v-on="on"
                                        ref="fecha_fin"
                                        :error-messages="errors.fecha_fin"
                                        dense
                                        :disabled="!$can(['LISTAR'])"
                                    >
                                    </v-text-field>
                                </template>
                                <v-date-picker
                                    v-model="form.fecha_fin"
                                    @input="menuDateFin = false"
                                    locale="es-CO"
                                >
                                </v-date-picker>
                            </v-menu>
                        </v-col>
                        <v-col cols="12" class="d-flex justify-center">
                            <v-btn
                                type="button"
                                small
                                color="red"
                                class="white--text text-none mr-2"
                                tile
                                v-on:click="fnLimpiarFechaIniFin"
                                :disabled="!$can(['LISTAR'])"
                            >
                                <v-icon> clear </v-icon>Limpiar
                            </v-btn>
                            <v-btn
                                type="submit"
                                small
                                color="#00bcd4"
                                class="white--text text-none"
                                tile
                                :disabled="!$can(['LISTAR'])"
                            >
                                <v-icon> search </v-icon>Buscar
                            </v-btn>
                        </v-col>
                    </v-row>
                </v-form>

                <v-row>
                    <v-col cols="12">
                        <v-card-title>
                            <v-text-field
                                type="text"
                                append-icon="mdi-magnify"
                                label="Buscar"
                                single-line
                                hide-details
                                :disabled="!$can(['LISTAR'])"
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
                            item-key="pro_id_lote"
                            :footer-props="{
                                'items-per-page-options': [3, 5, 10, 15],
                            }"
                            sort-by="pro_id_lote"
                            :sort-desc="true"
                            no-data-text="Sin registros"
                            :disable-sort="!$can(['LISTAR'])"
                        >
                            <template v-slot:item.pro_fecha="{ item }">
                                <v-chip
                                    :color="item.color"
                                    dark
                                >
                                    {{ item.pro_fecha }}
                                </v-chip>
                            </template>
                            <template v-slot:item.dias_transcurridos="{ item }">
                                <v-chip
                                    :color="item.color"
                                    dark
                                >
                                    {{ item.dias_transcurridos }}
                                </v-chip>
                            </template>
                            <template v-slot:item.esquejes_semilla="{ item }">
                                <a @click="fnShowEsquejesSemilla(item)">Clic</a>
                            </template>

                        </v-data-table>
                    </v-col>
                </v-row>
            </v-card>
        </v-container>

        <!-- Modal de esquejes semilla -->
        <v-dialog v-model="modal" persistent width="600px">
            <v-card>
                <v-card-title class="text-h5 py-2">
                    {{ this.modalInfo.pm_pro_id_lote }}
                    <v-spacer></v-spacer>
                    <v-btn color="black" icon @click="modal = false">
                        <v-icon dark>
                            close
                        </v-icon>
                    </v-btn>
                </v-card-title>
                <v-divider></v-divider>
                <v-card-text>
                    <v-row>

                        <v-col cols="0" sm="2"></v-col>
                        <v-col cols="6" sm="3" class="pa-0 pt-4">
                            <v-subheader>Fecha Esqueje</v-subheader>
                        </v-col>
                        <v-col cols="6" sm="4" class="pt-4">
                            <v-menu
                                v-model="menuFechaEsqueje"
                                :close-on-content-click="false"
                                :nudge-right="40"
                                transition="scale-transition"
                                offset-y
                                min-width="auto"
                            >
                                <template v-slot:activator="{ on, attrs }">
                                    <v-text-field
                                        v-model="modalInfo.pm_fecha_esquejacion"
                                        append-icon="mdi-calendar"
                                        readonly
                                        v-bind="attrs"
                                        v-on="on"
                                        ref="pm_fecha_esquejacion"
                                        :error-messages="modalErrors.pm_fecha_esquejacion"
                                        dense
                                    >
                                    </v-text-field>
                                </template>
                                <v-date-picker
                                    v-model="modalInfo.pm_fecha_esquejacion"
                                    @input="menuFechaEsqueje = false"
                                    locale="es-CO"
                                >
                                </v-date-picker>
                            </v-menu>
                        </v-col>
                        <v-col cols="0" sm="3"></v-col>

                        <v-col cols="12" sm="4">
                            <v-subheader>Cantidad Planta Madre</v-subheader>
                            <v-text-field
                                type="number"
                                v-model="modalInfo.pro_cantidad_plantas_madres"
                                ref="pro_cantidad_plantas_madres"
                                dense
                                disabled
                            ></v-text-field>
                        </v-col>
                        <v-col cols="12" sm="4">
                            <v-subheader>Cantidad Esquejes</v-subheader>
                            <v-text-field
                                type="number"
                                v-model="modalInfo.pm_cantidad_esquejes"
                                ref="pm_cantidad_esquejes"
                                dense
                                :error-messages="modalErrors.pm_cantidad_esquejes"
                            ></v-text-field>
                        </v-col>
                        <v-col cols="12" sm="4">
                            <v-subheader>Cantidad Semillas</v-subheader>
                            <v-text-field
                                type="number"
                                v-model="modalInfo.pm_cantidad_semillas"
                                ref="pm_cantidad_semillas"
                                dense
                                :error-messages="modalErrors.pm_cantidad_semillas"
                            ></v-text-field>
                        </v-col>
                        <v-col cols="12" class="d-flex justify-center">
                            <v-btn
                                type="submit"
                                small
                                color="success"
                                class="white--text text-none"
                                tile
                                :disabled="!$can(['CREAR', 'EDITAR'])"
                                v-on:click="guardarPlantaMadre"
                            >
                                <v-icon> save </v-icon>Guardar
                            </v-btn>
                        </v-col>

                    </v-row>
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
            overlayLoading  : false,
            menuDateInicio  : false,
            menuDateFin     : false,
            menuFechaEsqueje: false,
            form: {
                fecha_inicio: "",
                fecha_fin: "",
            },

            errors: {
                fecha_inicio: "",
                fecha_fin: "",
            },

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
                { text: "Id Lote", align: "start", value: "pro_id_lote" },
                { text: "Fecha Propagación", value: "pro_fecha" },
                { text: "Fecha Esqueje", value: "pm_fecha_esquejacion" },
                { text: "Cantidad Plantas Madres", value: "pro_cantidad_plantas_madres" },
                { text: "Estado", value: "estado_lote", sortable: false },
                { text: "Días transcurridos", value: "dias_transcurridos", sortable: false },
                { text: "Esquejes-Semilla", value: "esquejes_semilla", sortable: false },
            ],
            dataSet: [],
            /* end variables Table. */

            /* Variables para modal */
            modal: false,
            modalInfo: {
                pm_pro_id_lote: "",
                pm_fecha_esquejacion: "",
                pro_cantidad_plantas_madres: "",
                pm_cantidad_semillas: "",
                pm_cantidad_esquejes: "",
            },

            modalErrors: ''
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
        buscarLotes() {
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
                    `/sajona/planta-madre/buscar-lotes?fecha_inicio=${this.form.fecha_inicio}&fecha_fin=${this.form.fecha_fin}&page=${page}&length=${itemsPerPage}&orderColumn=${sortBy}&order=${sortDesc}&buscar=${this.buscar}`
                )
                .then((response) => {
                    this.loading = false;
                    this.dataSet = response.data.data;
                    this.totalRegistros = response.data.total;
                    this.numberOfPages = response.data.totalPages;

                    // Limpiando mensajes de error del formulario.
                    const errors = {
                        fecha_inicio: "",
                        fecha_fin: "",
                    };
                    this.errors = errors;
                    this.overlayLoading = false;
                })
                .catch((errores) => {
                    this.overlayLoading = false;
                    this.loading = false;
                    this.dataSet = [];
                    if (errores.response.status == 422) {
                        this.errors = errores.response.data.errors;
                    }
                });
        },
        filterSearch() {
            clearTimeout(this.debounce);
            this.debounce = setTimeout(() => {
                this.buscarLotes(this.buscar);
            }, 800);
        },
        fnShowEsquejesSemilla(item) {
            this.modalErrors = '';
            this.overlayLoading = true;
            axios
                .get(`/sajona/planta-madre/${item.pro_id_lote}`)
                .then((response) => {
                    this.modal = true;
                    this.modalInfo.pm_pro_id_lote       = response.data.data.pro_id_lote;

                    if (response.data.data.pm_fecha_esquejacion != '') {
                        this.modalInfo.pm_fecha_esquejacion = response.data.data.pm_fecha_esquejacion;
                    }else{
                        this.modalInfo.pm_fecha_esquejacion = '';
                    }

                    this.modalInfo.pro_cantidad_plantas_madres = response.data.data.pro_cantidad_plantas_madres;
                    this.modalInfo.pm_cantidad_semillas = response.data.data.pm_cantidad_semillas;
                    this.modalInfo.pm_cantidad_esquejes = response.data.data.pm_cantidad_esquejes;
                    this.overlayLoading = false;
                })
                .catch((errores) => {
                    this.modalErrors = this.fnResponseError(errores);
                    this.overlayLoading = false;
                });
        },
        guardarPlantaMadre(){
            this.overlayLoading = true;
            axios
                .post(`/sajona/planta-madre`, this.modalInfo)
                .then((response) => {
                    this.$swal(
                        response.data.message,
                        '',
                        'success'
                    );
                    this.overlayLoading = false;
                    this.modal = false;
                    this.modalErrors = '';
                    this.buscarLotes();
                })
                .catch((errores) => {
                    this.modalErrors = this.fnResponseError(errores);
                    this.overlayLoading = false;
                });
        },
        fnLimpiarFechaIniFin() {
            this.form.fecha_inicio = "";
            this.form.fecha_fin = "";
        }
    }
};
</script>
