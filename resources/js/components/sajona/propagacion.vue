<template>
    <div>
        <loadingGeneral v-bind:overlayLoading="overlayLoading"/>
        <h4>Propagación</h4>
        <v-container fluid>
            <v-card elevation="2">
                <v-card-title class="rounded-sm">
                    <span class="text-h6 font-weight-bold">Agregar</span>
                </v-card-title>
                <v-row class="pl-4 pr-4">
                    <v-col cols="6" sm="2" class="pa-0">
                        <v-subheader>Fecha de propagación</v-subheader>
                    </v-col>
                    <v-col cols="6" sm="4" class="pa-0">
                        <v-menu
                            v-model="menuDate"
                            :close-on-content-click="false"
                            :nudge-right="40"
                            transition="scale-transition"
                            offset-y
                            min-width="auto"
                        >
                            <template v-slot:activator="{ on, attrs }">
                                <v-text-field
                                    v-model="form.pro_fecha"
                                    :rules="rulesFecha"
                                    append-icon="mdi-calendar"
                                    readonly
                                    v-bind="attrs"
                                    v-on="on"
                                    ref="pro_fecha"
                                    :error-messages="errors.pro_fecha"
                                    dense
                                >
                                </v-text-field>
                            </template>
                            <v-date-picker
                                v-model="form.pro_fecha"
                                @input="menuDate = false"
                                locale="es-CO"
                            >
                            </v-date-picker>
                        </v-menu>
                    </v-col>

                    <v-col cols="6" sm="2" class="pa-0">
                        <v-subheader>Tipo de propagación</v-subheader>
                    </v-col>
                    <v-col cols="6" sm="4" class="pa-0">
                        <v-select
                            v-model="form.pro_tipo_propagacion"
                            ref="pro_tipo_propagacion"
                            :rules="rulesTipoPropagacion"
                            :items="itemsTipoPropagacion"
                            item-value="id"
                            item-text="nombre"
                            :error-messages="errors.pro_tipo_propagacion"
                            dense
                        ></v-select>
                    </v-col>

                    <v-col cols="6" sm="2" class="pa-0">
                        <v-subheader>Cantidad de Material</v-subheader>
                    </v-col>
                    <v-col cols="6" sm="4" class="pa-0">
                        <v-text-field
                            type="number"
                            v-model="form.pro_cantidad_material"
                            ref="pro_cantidad_material"
                            :rules="rulesCantidadMaterial"
                            :error-messages="errors.pro_cantidad_material"
                            dense
                        ></v-text-field>
                    </v-col>

                    <v-col cols="6" sm="2" class="pa-0">
                        <v-subheader>Variedad</v-subheader>
                    </v-col>
                    <v-col cols="6" sm="4" class="pa-0">
                        <v-select
                            v-model="form.pro_variedad"
                            ref="pro_variedad"
                            :rules="rulesVariedad"
                            :items="itemsVariedad"
                            item-value="id"
                            item-text="nombre"
                            :error-messages="errors.pro_variedad"
                            dense
                        ></v-select>
                    </v-col>

                    <v-col cols="6" sm="2" class="pa-0">
                        <v-subheader>Id lote</v-subheader>
                    </v-col>
                    <v-col cols="6" sm="4" class="pa-0">
                        <v-text-field
                            v-model="form.pro_id_lote"
                            ref="pro_id_lote"
                            readonly
                            :error-messages="errors.pro_id_lote"
                            dense
                        ></v-text-field>
                    </v-col>

                    <v-col cols="6" sm="2" class="pa-0">
                        <v-subheader>Tipo incorporación</v-subheader>
                    </v-col>
                    <v-col cols="6" sm="4" class="pa-0">
                        <v-select
                            v-model="form.pro_tipo_incorporacion"
                            ref="pro_tipo_incorporacion"
                            :rules="rulesTipoIncorporacion"
                            :items="itemsTipoIncorporacion"
                            item-value="id"
                            item-text="nombre"
                            :error-messages="errors.pro_tipo_incorporacion"
                            dense
                        ></v-select>
                    </v-col>

                    <v-col cols="6" sm="2" class="pa-0">
                        <v-subheader>Cantidad plantas madres</v-subheader>
                    </v-col>
                    <v-col cols="6" sm="4" class="pa-0">
                        <v-text-field
                            type="number"
                            v-model="form.pro_cantidad_plantas_madres"
                            ref="pro_cantidad_plantas_madres"
                            :rules="rulesCantidadPlantasMadres"
                            :error-messages="
                                errors.pro_cantidad_plantas_madres
                            "
                            dense
                        ></v-text-field>
                    </v-col>
                    <v-col cols="12" class="d-flex justify-end">
                        <v-btn
                            type="submit"
                            small
                            color="red darken-4"
                            class="white--text text-none mr-3"
                            tile
                            v-on:click="limpiarCampo"
                        >
                            <v-icon> format_clear </v-icon>Limpiar
                        </v-btn>
                        <v-btn
                            type="submit"
                            small
                            color="success"
                            class="white--text text-none"
                            tile
                            :disabled="!$can(['CREAR', 'EDITAR'])"
                            v-on:click="fnAccion"
                        >
                            <v-icon> save </v-icon>{{ accion }}
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
                                :disabled="!$can(['LISTAR'])"
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
                            <template v-slot:item.acciones="{ item }">
                                <v-icon
                                    color="primary"
                                    class="mr-2"
                                    @click="consultarPropagacion(item.pro_id_lote)"
                                    title="Editar Propagación"
                                    v-if="$can(['VER', 'EDITAR'])"
                                    small
                                >
                                    mdi-pencil
                                </v-icon>
                                <v-icon
                                    color="red"
                                    class="mr-2"
                                    @click="fnDelete(item.pro_id_lote)"
                                    title="Eliminar Propagación"
                                    v-if="$can(['ELIMINAR'])"
                                    small
                                >
                                    delete
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
            overlayLoading      : false,
            // Validaciones
            rulesFecha: [
                (value) =>
                    !!value || "El campo fecha de propagación es requerido.",
                // (value) => (value && value.length >= 3) || "Min 3 characters",
            ],
            rulesCantidadMaterial: [
                (value) =>
                    !!value || "El campo cantidad material es requerido.",
            ],
            rulesCantidadPlantasMadres: [
                (value) =>
                    !!value ||
                    "El campo cantidad de plantas madres es requerido.",
            ],
            rulesTipoPropagacion: [
                (value) =>
                    !!value || "El campo tipo de propagacion es requerido.",
            ],
            rulesVariedad: [
                (value) => !!value || "El campo variedad es requerido.",
            ],
            rulesTipoIncorporacion: [
                (value) =>
                    !!value || "El campo tipo incorporacion es requerido.",
            ],
            accion : 'Guardar',

            form: {
                pro_fecha: "",
                pro_cantidad_material: "",
                pro_id_lote: "",
                pro_cantidad_plantas_madres: "",
                pro_tipo_propagacion: "",
                pro_variedad: "",
                pro_tipo_incorporacion: "",
            },
            errors: {
                pro_fecha: "",
                pro_cantidad_material: "",
                pro_id_lote: "",
                pro_cantidad_plantas_madres: "",
                pro_tipo_propagacion: "",
                pro_variedad: "",
                pro_tipo_incorporacion: "",
            },

            menuDate: false,

            // Tabla filtro.
            debounce              : null,
            buscar                : '',
            // Table listar
            page                : 1,
            totalRegistros      : 0,
            numberOfPages       : 0,
            loading             : true,
            options             : {},
            headers             : [
                { text: "Id Lote", align: "start", value: "pro_id_lote" },
                { text: "Fecha Propagación", value: "pro_fecha" },
                { text: "Tipo de Propagación", value: "pro_tipo_propagacion" },
                { text: "Tipo Incorporación", value: "pro_tipo_incorporacion" },
                { text: "Cantidad Propagada", value: "pro_cantidad_material" },
                { text: "Estado", value: "estado_lote", sortable: false },
                { text: "Días transcurridos", value: "dias_transcurridos", sortable: false },
                { text: "Acciones", value: "acciones", sortable: false }
            ],
            dataSet: [],

            /* VARIABLES CAMPOS PARAMETROS START */
            itemsTipoPropagacion  : [],
            itemsVariedad         : [],
            itemsTipoIncorporacion: [],
            /* END CAMPOS PARAMETROS */
        };
    },
    watch: {
        options: {
            handler() {
                this.listar();
            },
        },
        deep: true,
    },
    methods: {
        fnAccion(){
            if (this.accion === "Guardar") {
                this.agregarPropagacion();
            }else{
                this.actualizarPropagacion();
            }
        },
        filterSearch(){
            this.loading = true;
            clearTimeout(this.debounce);
            this.debounce = setTimeout(() => {
                this.listar(this.buscar);
            }, 800);
        },
        listar(buscar = this.buscar) {
            this.overlayLoading = true;
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

            this.loading = true;
            axios
                .get(
                    `/sajona/propagacion/listar?page=${page}&length=${itemsPerPage}&orderColumn=${sortBy}&order=${sortDesc}&buscar=${buscar}`
                )
                .then((response) => {
                    this.loading = false;
                    this.dataSet = response.data.data;
                    this.totalRegistros = response.data.total;
                    this.numberOfPages = response.data.totalPages;
                    this.overlayLoading = false;
                })
                .catch((errores) => {
                    this.loading = false;
                    this.overlayLoading = false;
                });
        },
        buscarIdLoteUltimo() {
            this.overlayLoading = true;
            axios
                .get(`/sajona/propagacion/id-lote`)
                .then((response) => {
                    this.form.pro_id_lote = response.data.idLote;
                    this.overlayLoading = false;
                })
                .catch((errores) => {
                    this.overlayLoading = false;
                });
        },
        agregarPropagacion() {
            axios
                .post(`/sajona/propagacion`, this.form)
                .then((response) => {
                    this.$swal(
                        response.data.message,
                        '',
                        'success'
                    );
                    this.listar();
                    this.limpiarCampo();
                    this.errors = '';
                })
                .catch((errores) => {
                    this.errors = this.fnResponseError(errores);
                });
        },
        consultarPropagacion(id_lote){
            this.overlayLoading = true;
            this.accion = "Actualizar";
            axios
                .get(`/sajona/propagacion/mostrar/${id_lote}`)
                .then((response) => {
                    let data = response.data.data;

                    this.form = {
                        pro_fecha                     : data.pro_fecha,
                        pro_cantidad_material         : data.pro_cantidad_material,
                        pro_id_lote                   : data.pro_id_lote,
                        pro_cantidad_plantas_madres   : data.pro_cantidad_plantas_madres,
                        pro_tipo_propagacion          : data.pro_tipo_propagacion,
                        pro_variedad                  : data.pro_variedad,
                        pro_tipo_incorporacion        : data.pro_tipo_incorporacion,
                    };

                    this.errors = "";
                    this.overlayLoading = false;
                })
                .catch((errores) => {
                    this.errors = this.fnResponseError(errores);
                    this.overlayLoading = false;
                });
        },
        fnDelete(pro_id_lote){
            this.accion = 'Guardar';
            this.limpiarCampo();
            this.$swal({
                title: '¿Quiere eliminar la Propagación?',
                text: `Se va a eliminar el lote ${pro_id_lote}, ¿está seguro?.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Si, Eliminarlo!'
                }).then((result) => {
                    if (result.isConfirmed) {

                        this.overlayLoading = true;
                        axios
                            .post(`/sajona/propagacion/delete/`,{pro_id_lote : pro_id_lote})
                            .then((response) => {
                                this.overlayLoading = false;
                                this.$swal(
                                    response.data.message,
                                    '',
                                    'success'
                                );
                                this.listar();
                            })
                            .catch((errores) => {
                                this.overlayLoading = false;
                                this.errors = this.fnResponseError(errores);
                            });
                    }
                });
        },
        actualizarPropagacion(){
            this.overlayLoading = true
            axios
                .put(`/sajona/propagacion/actualizar/${this.form.pro_id_lote}`, this.form)
                .then((response) => {
                    this.errors = "";
                    this.$swal(
                        response.data.message,
                        '',
                        'success'
                    );
                    this.listar();
                    this.limpiarCampo();
                    this.overlayLoading = false
                })
                .catch((errores) => {
                    this.errors = this.fnResponseError(errores);
                    this.overlayLoading = false
                });
        },
        limpiarCampo() {
            this.overlayLoading = true;
            this.buscarIdLoteUltimo();
            this.accion = "Guardar";

            this.$refs["pro_fecha"].reset();
            this.$refs["pro_cantidad_material"].reset();
            this.$refs["pro_id_lote"].reset();
            this.$refs["pro_cantidad_plantas_madres"].reset();
            this.$refs["pro_tipo_propagacion"].reset();
            this.$refs["pro_variedad"].reset();
            this.$refs["pro_tipo_incorporacion"].reset();
            this.overlayLoading = false;
        },
    },
    async created(){
        // Aqui se carga informacion de campos parametros.
        this.itemsTipoPropagacion   = await this.fnBuscarParametro('pr_tipo_propagacion');
        this.itemsVariedad          = await this.fnBuscarParametro('pr_variedad');
        this.itemsTipoIncorporacion = await this.fnBuscarParametro('pr_tipo_incorporacion');
    },
    mounted() {
        this.buscarIdLoteUltimo();
    },
};
</script>
