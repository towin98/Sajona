<script>
let arrPermisos = [];
export default {
    arrPermisos,
    methods: {
        async buscaNombreRolUser() {
            try {
                let response = await axios.get("/sajona/busca-nombre-rol-user");
                this.cRol = response.data.data;
            } catch (errors) {
                if (
                    errors.response.status == 401 ||
                    errors.response.status == 500
                ) {
                    this.logout();
                    clearInterval(this.intervalId);
                    this.$swal("La Sesión ha caducado.", "", "info");
                }
            }
        },
        $can(permissionName = []) {
            let permissionsEncontrado = false;
            permissionName.forEach(permiso => {
                if (arrPermisos.indexOf(permiso) !== -1) {
                    permissionsEncontrado = true;
                }
            });
            return permissionsEncontrado;
        },
        async $fnPermisosUsuarios(){
            return arrPermisos;
        },
        async $fnConsultaPermisosUsuario(){
            try {
                let response = await axios.get("/sajona/permisos-usuario");
                arrPermisos = response.data.data;
            } catch (errors) {
                // this.$swal(
                //     'Error consultando permisos de usuario.',
                //     '',
                //     'error'
                // );
            }
        }
    },
};
</script>
