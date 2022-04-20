export const commons = {
    methods: {
        async fnBuscarParametro(parametrica){
            try {
                let response = await axios.get(`/sajona/parametro/buscar?parametrica=${parametrica}`);
                return response.data.data;
            } catch (errors) {
                this.$swal(
                    'Error.',
                    '',
                    'error'
                );
                return [];
            }
        },
        fnResponseError(errores){
            if (errores.response.status == 500 ||
                errores.response.status == 403 ||
                errores.response.status == 409 ||
                errores.response.status == 404)
            {
                let mensaje = "El sistema a generado un Error.";
                if (errores.response.data.message != undefined) {
                    mensaje = errores.response.data.message;
                }
                this.$swal({
                    icon: 'error',
                    title: `${mensaje}`,
                    text: `${errores.response.data.errors}`,
                })
            }else{
                if (errores.response.status == 422) {
                    this.errors = errores.response.data.errors;
                }
            }
        }
    },
};
