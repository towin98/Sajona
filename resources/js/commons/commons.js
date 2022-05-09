export const commons = {
    methods: {
        async fnBuscarParametro(parametrica){
            try {
                let response = await axios.get(`/sajona/parametro/buscar?parametrica=${parametrica}`);
                let data = response.data.data;
                for (let i = 0; i < data.length; i++) {
                    if (data[i].estado != "ACTIVO") {
                        data[i].disabled = true;
                    }
                }
                return data;
            } catch (errors) {
                // this.$swal(
                //     'Error.',
                //     '',
                //     'error'
                // );
                return [];
            }
        },
        fnResponseError(errores){
            if (errores.response.status == 500 ||
                errores.response.status == 403 ||
                errores.response.status == 409 ||
                errores.response.status == 404)
            {
                let mensaje = "El sistema a generado un Error";
                if (errores.response.data.message != undefined) {
                    mensaje = errores.response.data.message;
                }
                this.$swal({
                    icon: 'error',
                    title: `${mensaje}`,
                    text: `${errores.response.data.errors}`,
                })
                return '';
            }else{
                if (errores.response.status == 422) {
                    return errores.response.data.errors;
                }
            }
        }
    },
};
