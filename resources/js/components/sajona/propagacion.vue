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
                            :items="tiposPropagacion"
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
                            :items="tiposVariedad"
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
                            :items="tiposIncorporacion"
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
                            color="success"
                            class="white--text text-none"
                            tile
                            v-on:click="agregarPropagacion"
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

            tiposPropagacion    : ["Semilla", "Esquejes", "Ivvitro"],
            tiposIncorporacion  : ["Propia", "Comprada"],
            tiposVariedad       : [1, 2], // POR DEFINIR
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
                { text: "Fecha Siembra", value: "pro_fecha" },
                { text: "Tipo de Propagación", value: "pro_tipo_propagacion" },
                { text: "Tipo Incorporación", value: "pro_tipo_incorporacion" },
                { text: "Cantidad Siembra", value: "pro_cantidad_material" },
            ],
            dataSet: [],
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
        filterSearch(){
            this.loading = true;
            clearTimeout(this.debounce);
            this.debounce = setTimeout(() => {
                this.listar(this.buscar);
            }, 600);
        },
        listar(buscar = this.buscar) {
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
                    `/api/propagacion/listar?page=${page}&length=${itemsPerPage}&orderColumn=${sortBy}&order=${sortDesc}&buscar=${buscar}`
                )
                .then((response) => {
                    this.loading = false;
                    this.dataSet = response.data.data;
                    this.totalRegistros = response.data.total;
                    this.numberOfPages = response.data.totalPages;
                })
                .catch((errors) => {
                    this.loading = false;
                    console.log(errors.response.data);
                });
        },
        buscarIdLoteUltimo() {
            axios
                .get(`/api/propagacion/id-lote`)
                .then((response) => {
                    this.form.pro_id_lote = response.data.idLote;
                })
                .catch((errors) => {
                    console.log(errors.response.data);
                });
        },
        agregarPropagacion() {
            axios
                .post(`/api/propagacion`, this.form)
                .then((response) => {
                    this.$swal(
                        response.data.message,
                        '',
                        'success'
                    );
                    this.listar();
                    this.limpiarCampo();
                    this.buscarIdLoteUltimo();
                })
                .catch((errors) => {
                    this.errors = errors.response.data.errors;
                });
        },
        limpiarCampo() {
            this.$refs["pro_fecha"].reset();
            this.$refs["pro_cantidad_material"].reset();
            this.$refs["pro_id_lote"].reset();
            this.$refs["pro_cantidad_plantas_madres"].reset();
            this.$refs["pro_tipo_propagacion"].reset();
            this.$refs["pro_variedad"].reset();
            this.$refs["pro_tipo_incorporacion"].reset();
        },
    },
    mounted() {
        window.axios.defaults.headers.common[
            "Authorization"
        ] = `Bearer ${this.token}`;
        this.listar();
        this.buscarIdLoteUltimo();
    },
};
</script>
