<template>
    <div>
        <loadingGeneral v-bind:overlayLoading="overlayLoading" />
        <h4>Parametros</h4>
        <v-card>
            <v-tabs v-model="tab" background-color="grey darken-4" dark right>
                <v-tab v-for="(modulo, index) in parametros" v-bind:key="index" v-on:click="radioGroupParametros = null; dataSet = []">
                    {{ modulo.modulo }}
                </v-tab>
            </v-tabs>

            <v-tabs-items v-model="tab" flex class="">
                <v-tab-item
                    v-for="(modulo, index) in parametros"
                    v-bind:key="index"
                >
                    <v-card flat class="pa-6">
                        <h4>Campos</h4>
                        <v-radio-group v-model="radioGroupParametros">
                            <v-radio
                                v-for="(campo, index) in modulo.campos"
                                v-bind:key="index"
                                :label="campo.campo"
                                :value="campo.value"
                                v-on:click="fnBuscar(radioGroupParametros,buscar)"
                            ></v-radio>
                        </v-radio-group>
                        <v-btn
                            color="red lighten-2"
                            dark
                            v-on:click="fnBuscar(radioGroupParametros,buscar)"
                            :disabled="!$can(['LISTAR'])"
                        >
                            Buscar
                        </v-btn>
                        <v-btn
                            color="success"
                            dark
                            v-on:click="fnCambiaTitulo(radioGroupParametros, 'NUEVO')"
                            v-if="radioGroupParametros != null"
                            :disabled="!$can(['CREAR'])"
                        >
                            <v-icon> add </v-icon>Nuevo
                        </v-btn>
                    </v-card>
                </v-tab-item>
            </v-tabs-items>
        </v-card>

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
                        :disabled="!$can(['LISTAR'])"
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
                    item-key="id"
                    :footer-props="{
                        'items-per-page-options': [3, 5, 10, 15],
                    }"
                    sort-by="id"
                    :sort-desc="true"
                    no-data-text="Sin registros"
                    :disable-sort="!$can(['LISTAR'])"
                >
                    <template v-slot:item.actions="{ item }">
                        <v-icon
                            class="mr-2"
                            @click="fnShowParametro(item)"
                            v-if="$can(['VER', 'EDITAR'])"
                        >
                            mdi-pencil
                        </v-icon>
                    </template>
                </v-data-table>
            </v-col>
        </v-row>

        <!-- Modal crear o editar parametro. -->
        <v-dialog
            v-model="modalParametros"
            width="700"
            persistent
        >
            <v-card>
                <v-card-title class="text-h5 py-2">
                    {{ tituloCampoParametro }}
                    <v-spacer></v-spacer>
                    <v-btn color="black" icon @click="modalParametros = false">
                        <v-icon dark> close </v-icon>
                    </v-btn>
                </v-card-title>
                <v-card-text>
                    <v-divider></v-divider>
                    <v-row>

                        <v-col cols="4" sm="2" class="pt-4">
                            <v-subheader>Nombre</v-subheader>
                        </v-col>
                        <v-col cols="8" sm="4" class="pt-4">
                            <v-text-field
                                type="text"
                                v-model="formModalParametros.nombre"
                                ref="nombre"
                                :error-messages="errors.nombre"
                            ></v-text-field>
                        </v-col>
                        <v-col cols="4" sm="2" class="pt-4">
                            <v-subheader>Estado</v-subheader>
                        </v-col>
                        <v-col cols="8" sm="4" class="pt-4">
                            <v-select
                                v-model="formModalParametros.estado"
                                ref="estado"
                                :items="['ACTIVO', 'INACTIVO']"
                                :error-messages="errors.estado"
                            ></v-select>
                        </v-col>

                        <v-col cols="4" sm="2" class="pt-0">
                            <v-subheader>Descripción</v-subheader>
                        </v-col>
                        <v-col cols="8" sm="10" class="pt-0">
                            <v-text-field
                                type="text"
                                v-model="formModalParametros.descripcion"
                                ref="descripcion"
                                :error-messages="errors.descripcion"
                            ></v-text-field>
                        </v-col>
                    </v-row>
                </v-card-text>

                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn
                        type="submit"
                        small
                        color="success"
                        class="white--text text-none"
                        tile
                        v-on:click="fnAccion(accion)"
                        :disabled="!$can(['CREAR', 'EDITAR'])"
                    >
                        <v-icon> save </v-icon>{{ accion }}
                    </v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
    </div>
</template>
<script>
import loadingGeneral from "../loadingGeneral/loadingGeneral.vue";
import parametros from "./parametrosJson.json";
export default {
    components: {
        loadingGeneral,
    },
    data() {
        return {
            overlayLoading: false,
            titleAccion: "Parametros Módulos",

            accion: "",

            // Variables modal
            modalParametros: false,
            formModalParametros: {
                nombre          : "",
                descripcion     : "",
                estado          : "",
                parametrica     : ""
            },
            // Variable para almacenar id del registro actualizar.
            idParametro : '',
            errors : {},
            tituloCampoParametro : '',
            // fin variables modal
            tab: null,
            radioGroupParametros: null,

            // Variables data table
            page: 1,
            totalRegistros: 0,
            buscar: "",
            options: {},
            loading: false, // Loading datatable
            headers: [
                { text: "Id", align: "start", value: "id" },
                { text: "Nombre", value: "nombre" },
                { text: "Descripción", value: "descripcion" },
                { text: "Estado", value: "estado" },
                { text: "Acciones", value: "actions" },
            ],
            dataSet: [],
            contador: 0,
            // fin variable data table.
            parametros: parametros,
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
        filterSearch() {
            if (this.contador > 0) {
                this.loading = true;
                clearTimeout(this.debounce);
                this.debounce = setTimeout(() => {
                    this.fnBuscar(this.radioGroupParametros, this.buscar);
                }, 800);
            }
            this.contador++;
        },
        fnBuscar(parametrica, buscar) {
            if (parametrica == null) {
                this.$swal({
                    icon: 'info',
                    title: `Acción requerida.`,
                    text: `Por favor seleccione un campo del módulo para realizar la busqueda.`,
                });
                return;
            }

            this.overlayLoading = true;
            let { page, itemsPerPage, sortBy, sortDesc } = this.options;

            // Obteniendo rangos de consultado paginación.
            this.start = itemsPerPage * (page - 1);
            this.length = this.start + itemsPerPage;

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
                    `/sajona/parametro/buscar?parametrica=${parametrica}&length=${this.length}&start=${this.start}&orderColumn=${sortBy}&order=${sortDesc}&buscar=${buscar}`
                )
                .then((response) => {
                    this.loading = false;
                    this.dataSet = response.data.data;
                    this.totalRegistros = response.data.total;
                    this.overlayLoading = false;
                })
                .catch((errors) => {
                    this.dataSet = [];
                    this.loading = false;
                    this.overlayLoading = false;
                    this.fnResponseError(errors);
                });
        },
        fnAccion(accion) {
            if (accion == "GUARDAR") {
                this.fnGuardar();
            }else{
                this.fnEditar();
            }
        },
        fnGuardar(){
            this.formModalParametros.parametrica = this.radioGroupParametros;
            axios
                .post(`/sajona/parametro`, this.formModalParametros)
                .then((response) => {
                    this.errors = "";
                    this.$swal(
                        response.data.message,
                        '',
                        'success'
                    );
                    this.fnBuscar(this.radioGroupParametros, this.buscar);
                    this.limpiarCampos();
                    this.modalParametros = false;
                })
                .catch((errores) => {
                    this.errors = errores.response.data.errors;
                    if (errores.response.status == 500) {
                        if (errores.response.data.errors[0] != undefined) {
                            this.$swal({
                                icon: 'error',
                                title: `${errores.response.data.message}`,
                                text: `${errores.response.data.errors[0]}`,
                            })
                        }
                    }
                });
        },
        /* Envia peticion con data para actualizar registro. */
        fnEditar(){
            this.formModalParametros.parametrica = this.radioGroupParametros;
            axios
                .put(`/sajona/parametro/${this.idParametro}`, this.formModalParametros)
                .then((response) => {
                    this.errors = "";
                    this.modalParametros = false;
                    this.$swal(
                        response.data.message,
                        '',
                        'success'
                    );
                    this.fnBuscar(this.radioGroupParametros, this.buscar);
                    this.limpiarCampos();
                })
                .catch((errores) => {
                    this.errors = errores.response.data.errors;
                    if (errores.response.data.errors[0] != undefined) {
                        this.$swal({
                            icon: 'error',
                            title: `${errores.response.data.message}`,
                            text: `${errores.response.data.errors[0]}`,
                        })
                    }
                });
        },
        /* Muestra un registro de un parametro */
        fnShowParametro(item){
            this.fnCambiaTitulo(this.radioGroupParametros, 'EDITAR');
            axios
                .get(`/sajona/parametro/${this.radioGroupParametros}/${item.id}`)
                .then((response) => {
                    let data = response.data.data;
                    this.errors = "";

                    this.formModalParametros.nombre = data.nombre;
                    this.formModalParametros.descripcion = data.descripcion;
                    this.formModalParametros.estado = data.estado;

                    this.idParametro = item.id;
                    this.modalParametros = true;
                })
                .catch((errores) => {
                    this.errors = errores.response.data.errors;
                    if (errores.response.data.errors[0] != undefined) {
                        this.$swal({
                            icon: 'error',
                            title: `${errores.response.data.message}`,
                            text: `${errores.response.data.errors[0]}`,
                        })
                    }
                });
        },
        fnCambiaTitulo(campo, accion) {

            if (accion == 'EDITAR') {
                this.accion = "ACTUALIZAR";
            }else{
                this.modalParametros = true;
                this.accion = "GUARDAR";
                this.limpiarCampos();
            }

            switch (campo) {
                case 'pr_tipo_propagacion':
                    this.tituloCampoParametro = (accion == 'EDITAR') ? 'Editar Tipo de Propagación' : 'Nuevo Tipo de Propagación';
                break;
                case 'pr_variedad':
                    this.tituloCampoParametro = (accion == 'EDITAR') ? 'Editar Variedad' : 'Nueva Variedad';
                break;
                case 'pr_tipo_incorporacion':
                    this.tituloCampoParametro = (accion == 'EDITAR') ? 'Editar Tipo de Incorporación' : 'Nueva Tipo de Incorporación';
                break;

                case 'pr_tipo_lote':
                    this.tituloCampoParametro = (accion == 'EDITAR') ? 'Editar Tipo de lote' : 'Nuevo Tipo de lote';
                break;
                case 'pr_ubicacion':
                    this.tituloCampoParametro = (accion == 'EDITAR') ? 'Editar Ubicación' : 'Nuevo Ubicación';
                break;
                case 'pr_estado_cosecha':
                    this.tituloCampoParametro = (accion == 'EDITAR') ? 'Editar Estado Cosecha' : 'Nuevo Estado Cosecha';
                break;
                case 'pr_fase_cultivo':
                    this.tituloCampoParametro = (accion == 'EDITAR') ? 'Editar Fase de Cultivo' : 'Nueva Fase de Cultivo';
                break;
                case 'pr_motivo_perdida':
                    this.tituloCampoParametro = (accion == 'EDITAR') ? 'Editar Motivo de Perdida' : 'Nuevo Motivo de Perdida';
                break;
                default:
                    this.tituloCampoParametro = (accion == 'EDITAR') ? 'Editar Por definir nombre' : 'Nuevo Por definir nombre';
                break;
            }
        },
        limpiarCampos() {
            this.formModalParametros.nombre   = '';
            this.formModalParametros.descripcion   = '';
            this.formModalParametros.estado   = '';
        },
    },
    mounted() {
        window.axios.defaults.headers.common[
            "Authorization"
        ] = `Bearer ${this.token}`;
    },
};
</script>
