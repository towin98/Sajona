<template>
    <div>
        <loadingGeneral v-bind:overlayLoading="overlayLoading" />
        <h4>Transplante a Campo</h4>
        <v-container fluid>
            <v-card elevation="2">
                <v-card-title class="rounded-sm py-2">
                    <span class="text-h6 font-weight-bold">Listado</span>
                </v-card-title>
                <v-divider></v-divider>

                <v-form
                    v-on:submit.prevent="buscarTransplantes"
                    ref="form"
                    lazy-validation
                >
                    <v-row class="mt-3 pl-4 pr-4">
                        <v-col cols="6" sm="2" class="pa-0">
                            <v-subheader>Fecha de inicial</v-subheader>
                        </v-col>
                        <v-col cols="6" sm="4" class="pa-0">
                            <v-menu
                                v-model="menuDateInicial"
                                :close-on-content-click="false"
                                :nudge-right="40"
                                transition="scale-transition"
                                offset-y
                                min-width="auto"
                            >
                                <template v-slot:activator="{ on, attrs }">
                                    <v-text-field
                                        v-model="form.fecha_inicial"
                                        append-icon="mdi-calendar"
                                        v-bind="attrs"
                                        v-on="on"
                                        ref="fecha_inicial"
                                        :error-messages="errors.fecha_inicial"
                                        dense
                                        :disabled="!$can(['LISTAR'])"
                                    >
                                    </v-text-field>
                                </template>
                                <v-date-picker
                                    v-model="form.fecha_inicial"
                                    @input="menuDateInicial = false"
                                    locale="es-CO"
                                >
                                </v-date-picker>
                            </v-menu>
                        </v-col>

                        <v-col cols="6" sm="2" class="pa-0">
                            <v-subheader>Fecha de final</v-subheader>
                        </v-col>
                        <v-col cols="6" sm="4" class="pa-0">
                            <v-menu
                                v-model="menuDateFinal"
                                :close-on-content-click="false"
                                :nudge-right="40"
                                transition="scale-transition"
                                offset-y
                                min-width="auto"
                            >
                                <template v-slot:activator="{ on, attrs }">
                                    <v-text-field
                                        v-model="form.fecha_final"
                                        append-icon="mdi-calendar"
                                        v-bind="attrs"
                                        v-on="on"
                                        ref="fecha_final"
                                        :error-messages="errors.fecha_final"
                                        dense
                                        :disabled="!$can(['LISTAR'])"
                                    >
                                    </v-text-field>
                                </template>
                                <v-date-picker
                                    v-model="form.fecha_final"
                                    @input="menuDateFinal = false"
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
                                'items-per-page-options': [5,10,30,50],
                            }"
                            sort-by="id_lote"
                            :sort-desc="true"
                            no-data-text="Sin registros"
                            :disable-sort="!$can(['LISTAR'])"
                        >
                            <template v-slot:item.fecha_propagacion="{ item }">
                                <v-chip
                                    :color="item.color"
                                    dark
                                >
                                    {{ item.fecha_propagacion }}
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
                            <template v-slot:item.transplante_campo_accion="{ item }" v-if="$can(['VER'])">
                                <a @click="consultarTransplante(item)">Clic</a>
                            </template>
                        </v-data-table>
                    </v-col>
                </v-row>
                <!-- end Data table -->
            </v-card>
        </v-container>

        <!-- Modal Transplante -->
        <v-dialog v-model="modal" persistent width="800px">
            <v-card>
                <v-card-title class="text-h5 py-2">
                    {{ this.modalInfo.id_lote }}
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
                        <v-col cols="6" sm="3" class="pa-0 pt-4">
                            <v-subheader>Fecha Transplante</v-subheader>
                        </v-col>
                        <v-col cols="6" sm="3" class="pa-0 pt-4">
                            <v-menu
                                v-model="calendarioFechaTransplante"
                                :close-on-content-click="false"
                                :nudge-right="40"
                                transition="scale-transition"
                                offset-y
                                min-width="auto"
                            >
                                <template v-slot:activator="{ on, attrs }">
                                    <v-text-field
                                        v-model="modalInfo.tp_fecha"
                                        append-icon="mdi-calendar"
                                        readonly
                                        v-bind="attrs"
                                        v-on="on"
                                        ref="tp_fecha"
                                        :error-messages="modalErrors.tp_fecha"
                                        dense
                                    >
                                    </v-text-field>
                                </template>
                                <v-date-picker
                                    v-model="modalInfo.tp_fecha"
                                    @input="calendarioFechaTransplante = false"
                                    locale="es-CO"
                                >
                                </v-date-picker>
                            </v-menu>
                        </v-col>

                        <v-col cols="6" sm="3" class="pa-0 pt-4">
                            <v-subheader>Cantidad Transplante Campo</v-subheader>
                        </v-col>
                        <v-col cols="6" sm="3" class="pa-0 pt-4">
                            <v-text-field
                                type="number"
                                v-model="modalInfo.cantidad_transplante_campo"
                                ref="cantidad_transplante_campo"
                                dense
                                disabled
                            ></v-text-field>
                        </v-col>

                        <v-col cols="12" class="d-flex justify-center">
                            <v-btn
                                type="submit"
                                small
                                color="success"
                                class="white--text text-none"
                                tile
                                v-on:click="guardarTransplante"
                                :disabled="!$can(['CREAR', 'EDITAR'])"
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
            token: localStorage.getItem("TOKEN_SAJONA"),
            overlayLoading   : false,
            menuDateInicial  : false,
            menuDateFinal    : false,
            menuFechaEsqueje : false,
            form: {
                fecha_inicial: "",
                fecha_final  : "",
            },

            errors: {
                fecha_inicial: "",
                fecha_final: "",
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
                { text: "Id Lote", align: "start", value: "id_lote" },
                { text: "Fecha Propagación", value: "fecha_propagacion" },
                { text: "Fecha Transplante Bolsa", value: "fecha_transplante_bolsa" },
                { text: "Fecha Transplante Campo", value: "fecha_transplante_Campo" },
                { text: "Cantidad Transplante Campo", value: "cantidad_transplante_campo" },
                { text: "Estado", value: "estado_lote", sortable: false },
                { text: "Días transcurridos", value: "dias_transcurridos", sortable: false },
                { text: "Transplante Campo", value: "transplante_campo_accion", sortable: false },
            ],
            dataSet: [],
            start     : 0,
            length    : 0,
            /* end variables Table. */

            /* Variables modal*/
            calendarioFechaTransplante : false,
            modal     : false,
            modalInfo : {
                id_lote          : '',
                tp_fecha         : '',
            },
            modalErrors : '',
            /* fin Variables modal */
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
        buscarTransplantes() {
            this.overlayLoading = true;
            this.loading = true;
            let { page, itemsPerPage, sortBy, sortDesc } = this.options;

            // Obteniendo rangos de consultado paginación.
            this.start  = itemsPerPage * (page - 1);
            this.length = itemsPerPage;

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
                    `/sajona/transplante-campo/buscar?fecha_inicial=${this.form.fecha_inicial}&fecha_final=${this.form.fecha_final}&length=${this.length}&start=${this.start}&orderColumn=${sortBy}&order=${sortDesc}&buscar=${this.buscar}`
                )
                .then((response) => {
                    this.loading = false;
                    this.dataSet = response.data.data;
                    this.totalRegistros = response.data.total;
                    this.numberOfPages = response.data.totalPages;

                    // Limpiando mensajes de error del formulario.
                    const errors = {
                        fecha_inicial: "",
                        fecha_final: "",
                    };
                    this.errors = errors;
                    this.overlayLoading = false;
                })
                .catch((errors) => {
                    this.overlayLoading = false;
                    this.loading = false;
                    this.dataSet = [];
                    this.errors = errors.response.data.errors;
                });
        },
        filterSearch() {
                this.overlayLoading = true;
                clearTimeout(this.debounce);
                this.debounce = setTimeout(() => {
                    this.buscarTransplantes(this.buscar);
                }, 600);
        },
        consultarTransplante(item) {
            this.modalErrors = '';
            this.overlayLoading = true;
            axios
                .get(`/sajona/transplante-campo/${item.pm_id}`)
                .then((response) => {
                    this.modal = true;

                    if (response.data.data.tp_fecha != '') {
                        this.modalInfo.tp_fecha = response.data.data.tp_fecha;
                    }else{
                        this.modalInfo.tp_fecha = '';
                    }

                    // Cargando Data.
                    this.modalInfo.id_lote = response.data.data.pm_pro_id_lote;
                    this.modalInfo.cantidad_transplante_campo = response.data.data.cantidad_transplante_campo; // label

                    this.overlayLoading = false;
                })
                .catch((errores) => {
                    this.modalErrors = this.fnResponseError(errores);
                    this.overlayLoading = false;
                });
        },
        guardarTransplante(){
            this.overlayLoading = true;
            axios
                .post(`/sajona/transplante-campo`, this.modalInfo)
                .then((response) => {
                    this.$swal(
                        response.data.message,
                        '',
                        'success'
                    );
                    this.overlayLoading = false;
                    this.modal = false;
                    this.modalErrors = '';
                    this.buscarTransplantes();
                })
                .catch((errores) => {
                    this.modalErrors = this.fnResponseError(errores);
                    this.overlayLoading = false;
                });
        },
        fnLimpiarFechaIniFin() {
            this.form.fecha_inicial = "";
            this.form.fecha_final = "";
        }
    },
};
</script>
