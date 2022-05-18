<template>
    <div>
        <loadingGeneral v-bind:overlayLoading="overlayLoading" />
        <h4>Cambio de clave</h4>
        <v-card elevation="2" max-width="344" class="mx-auto">
            <v-card-title class="rounded-sm text-center" tag="div">
                <span class="text-h6 font-weight-bold">Cambio de Clave</span>
            </v-card-title>
            <v-divider></v-divider>
            <v-card-text>
                <v-form v-model="valid" ref="form" lazy-validation>
                    <v-row>
                        <v-col cols="12">
                            <v-text-field
                                :type="showPassword1 ? 'text' : 'password'"
                                v-model="form.claveActual"
                                prepend-icon="lock"
                                :append-icon="showPassword1 ? 'mdi-eye' : 'mdi-eye-off'"
                                :error-messages="errors.claveActual"
                                hint="Al menos 8 caracteres"
                                label="Ingrese Clave Actual"
                                @click:append="showPassword1 = !showPassword1"
                                :rules="[rules.required, rules.min]"
                            ></v-text-field>
                        </v-col>
                        <v-col cols="12">
                            <v-text-field
                                :type="showPassword2 ? 'text' : 'password'"
                                v-model="form.password"
                                prepend-icon="lock"
                                :append-icon="showPassword2 ? 'mdi-eye' : 'mdi-eye-off'"
                                :error-messages="errors.password"
                                hint="Al menos 8 caracteres"
                                label="Ingrese Clave Nueva"
                                @click:append="showPassword2 = !showPassword2"
                                :rules="[rules.required, rules.min]"
                            ></v-text-field>
                        </v-col>
                        <v-col cols="12">
                            <v-text-field
                                :type="showPassword3 ? 'text' : 'password'"
                                v-model="form.password_confirmation"
                                prepend-icon="lock"
                                :append-icon="showPassword3 ? 'mdi-eye' : 'mdi-eye-off'"
                                :error-messages="errors.password_confirmation"
                                hint="Al menos 8 caracteres"
                                label="Repita la Clave Nueva"
                                @click:append="showPassword3 = !showPassword3"
                                :rules="[rules.required, rules.min, rules.passMatch]"
                            ></v-text-field>
                        </v-col>
                    </v-row>
                </v-form>
            </v-card-text>
            <v-card-actions class="flex justify-center text-none">
                <v-btn  color="primary"
                    v-on:click="fnCambiarClave"> Cambiar Clave de Acceso
                </v-btn>
            </v-card-actions>
        </v-card>
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
            valid: true,
            form : {
                claveActual   : '',
                password    : '',
                password_confirmation : '',
            },
            errors : {},
            overlayLoading: false,

            showPassword1 :false,
            showPassword2 :false,
            showPassword3 :false,

            rules: {
                required: value => !!value || 'Requerido.',
                min: v => (v && v.length >= 8) || 'Min 8 carácteres',
                passMatch: value => (this.form.password == value) || 'Claves no coinciden',
            },
        }
    },
    methods:{
        fnCambiarClave(){
            this.overlayLoading = true;
            axios
                .post("/sajona/password/change", this.form)
                .then((response) => {
                    this.overlayLoading = false;
                    this.$swal(
                        '',
                        response.data.message,
                        'success'
                    );
                    this.fnLimpiarCampos();
                })
                .catch((errors) => {
                    this.overlayLoading = false;
                    if (errors.response.status == 500) {
                        this.$swal({
                            icon: 'error',
                            title: `Error inesperado`,
                            text: `${errors.response.data.errors}`,
                        });
                    }else{
                        this.errors = errors.response.data.errors;
                    }
                });
        },
        fnLimpiarCampos(){
            this.$refs.form.reset();
            this.errors = {};
        }
    }
}
</script>
