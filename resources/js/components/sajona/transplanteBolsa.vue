<template>
    <div>
        <loadingGeneral v-bind:overlayLoading="overlayLoading" />
        <h4>Transplante Bolsa</h4>
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
                                        readonly
                                        v-bind="attrs"
                                        v-on="on"
                                        ref="fecha_inicial"
                                        :error-messages="errors.fecha_inicial"
                                        dense
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
                                        readonly
                                        v-bind="attrs"
                                        v-on="on"
                                        ref="fecha_final"
                                        :error-messages="errors.fecha_final"
                                        dense
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
                                type="submit"
                                small
                                color="#00bcd4"
                                class="white--text text-none"
                                tile
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
                            :items-per-page="3"
                            item-key="pro_id_lote"
                            :footer-props="{
                                'items-per-page-options': [3, 5, 10, 15],
                            }"
                            sort-by="pro_id_lote"
                            :sort-desc="true"
                            no-data-text="Sin registros"
                        >
                            <template v-slot:item.consultar="{ item }">
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
                    Nuevo
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
                            <v-subheader>Cantidad Buenas</v-subheader>
                        </v-col>
                        <v-col cols="6" sm="3" class="pa-0 pt-4">
                            <v-text-field
                                type="number"
                                v-model="modalInfo.cantidad_buenas"
                                ref="cantidad_buenas"
                                dense
                                disabled
                            ></v-text-field>
                        </v-col>

                        <v-col cols="6" sm="3" class="pa-0">
                            <v-subheader>Tipo Lote</v-subheader>
                        </v-col>
                        <v-col cols="6" sm="3" class="pa-0">
                            <v-select
                                v-model="modalInfo.tp_tipo_lote"
                                ref="tp_tipo_lote"
                                :items="['Planta Madre', 'Planta Comercial']"
                                :error-messages="modalErrors.tp_tipo_lote"
                                dense
                            ></v-select>
                        </v-col>

                        <v-col cols="6" sm="3" class="pa-0">
                            <v-subheader>Cantidad Área</v-subheader>
                        </v-col>
                        <v-col cols="6" sm="3" class="pa-0">
                            <v-text-field
                                type="number"
                                v-model="modalInfo.tp_cantidad_area"
                                ref="tp_cantidad_area"
                                dense
                                :error-messages="modalErrors.tp_cantidad_area"
                            ></v-text-field>
                        </v-col>

                        <v-col cols="6" sm="3" class="pa-0">
                            <v-subheader>Cantidad Área</v-subheader>
                        </v-col>
                        <v-col cols="6" sm="3" class="pa-0">
                            <v-select
                                v-model="modalInfo.tp_ubicacion"
                                ref="tp_ubicacion"
                                :items="['Casa Malla','Planta Madre']"
                                :error-messages="modalErrors.tp_ubicacion"
                                dense
                            ></v-select>
                        </v-col>

                        <v-col cols="12" class="d-flex justify-center">
                            <v-btn
                                type="submit"
                                small
                                color="success"
                                class="white--text text-none"
                                tile
                                v-on:click="guardarTransplante"
                            >
                                <v-icon> save </v-icon>Guardar
                            </v-btn>
                        </v-col>

                    </v-row>
                </v-card-text>
            </v-card>
        </v-dialog>

        <!-- Modal Notificación -->
        <v-dialog v-model="modalNotificacion" width="500">

            <v-card>
                <v-card-title class="text-h5 lighten-2 py-0">
                    Notificación <v-icon size="50"> toggle_on </v-icon>
                </v-card-title>
                <v-divider></v-divider>
                <v-card-text>
                    <v-row class="pl-4 pr-4 mb-4 pt-5">
                        <v-col cols="12" sm="6" class="pa-0">
                            <v-subheader>Cantidad Malas</v-subheader>
                        </v-col>
                        <v-col cols="12" sm="6" class="pa-0">
                            <v-text-field
                                v-model="infoNoti.CantidadMalas"
                                outlined
                                dense
                                disabled>
                            </v-text-field>
                        </v-col>

                        <v-col cols="12" sm="6" class="pa-0">
                            <v-subheader>Porcentaje Malas</v-subheader>
                        </v-col>
                        <v-col cols="12" sm="6" class="pa-0">
                            <v-text-field
                                v-model="infoNoti.porcentajeMalas"
                                outlined
                                dense
                                disabled>
                            </v-text-field>
                        </v-col>

                        <v-col cols="12" sm="6" class="pa-0">
                            <v-subheader>Porcentaje Buenas</v-subheader>
                        </v-col>
                        <v-col cols="12" sm="6" class="pa-0">
                            <v-text-field
                                v-model="infoNoti.porcentajeBuenas"
                                outlined
                                dense
                                disabled>
                            </v-text-field>
                        </v-col>
                    </v-row>
                </v-card-text>

                <v-divider></v-divider>

                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn color="success" class="white--text text-none" @click="modalNotificacion = false">
                        OK
                    </v-btn>
                </v-card-actions>
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
            contador: 0,
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
                { text: "Fecha Transplante a Bolsa", value: "fecha_transplante" },
                { text: "Acción", value: "accion" },
                { text: "Consultar", value: "consultar", sortable: false },
            ],
            dataSet: [],
            /* end variables Table. */

            /* Variables modal*/
            calendarioFechaTransplante : false,
            modal     : false,
            modalInfo : {
                tp_tipo          : 'Transplante Bolsa',
                tp_pm_id         : '',
                tp_fecha         : '',
                cantidad_buenas  : '',
                tp_tipo_lote     : '',
                tp_ubicacion     : '',
                tp_cantidad_area : ''
            },
            modalErrors : '',
            /* fin Variables modal */

            /* Variables modal notificaciones */
            modalNotificacion : false,
            // Info del modal notificaciones.
            infoNoti : {
                CantidadMalas : '',
                porcentajeMalas : '',
                porcentajeBuenas : ''
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
        buscarTransplantes() {
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
                    `/sajona/transplante-bolsa/buscar?fecha_inicial=${this.form.fecha_inicial}&fecha_final=${this.form.fecha_final}&page=${page}&length=${itemsPerPage}&orderColumn=${sortBy}&order=${sortDesc}&buscar=${this.buscar}`
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
            if (this.contador > 0) {
                clearTimeout(this.debounce);
                this.debounce = setTimeout(() => {
                    this.buscarTransplantes(this.buscar);
                }, 600);
            }
            this.contador++;
        },
        consultarTransplante(item) {
            this.modalErrors = '';
            this.overlayLoading = true;
            axios
                .get(`/sajona/transplante-bolsa/${item.pm_id}`)
                .then((response) => {
                    this.modal = true;

                    if (response.data.data.tp_fecha != '') {
                        this.modalInfo.tp_fecha = response.data.data.tp_fecha;
                    }else{
                        this.modalInfo.tp_fecha = '';
                    }

                    // Cargando Data.
                    this.modalInfo.tp_pm_id = response.data.data.tp_pm_id;
                    this.modalInfo.cantidad_buenas = response.data.data.cantidad_buenas; // label
                    this.modalInfo.tp_tipo_lote = response.data.data.tp_tipo_lote;
                    this.modalInfo.tp_ubicacion = response.data.data.tp_ubicacion;
                    this.modalInfo.tp_cantidad_area = response.data.data.tp_cantidad_area;

                    this.overlayLoading = false;
                })
                .catch((errors) => {
                    this.overlayLoading = false;
                });
        },
        guardarTransplante(){
            this.overlayLoading = true;
            axios
                .post(`/sajona/transplante-bolsa`, this.modalInfo)
                .then((response) => {
                    this.modalNotificacion = true;
                    // response.data.message
                    this.overlayLoading = false;
                    this.modal = false;
                    this.modalErrors = '';
                    this.buscarTransplantes();

                    this.infoNoti.CantidadMalas = response.data.notificacion.cantidad_malas;
                    this.infoNoti.porcentajeMalas = response.data.notificacion.porcentaje_malas;
                    this.infoNoti.porcentajeBuenas = response.data.notificacion.porcentaje_buenas;
                })
                .catch((errors) => {
                    this.modalErrors = errors.response.data.errors;
                    this.overlayLoading = false;
                });
        }
    },
};
</script>
