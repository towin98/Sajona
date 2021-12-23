export const misRol = {
    methods: {
        async buscaNombreRolUser() {
            try {
                let response = await axios.get("/api/busca-nombre-rol-user");
                this.cRol = response.data.data;
            } catch (errors) {
                if (errors.response.status == 401 || errors.response.status == 500) {
                    this.logout();
                    clearInterval(this.intervalId);
                    this.$swal(
                        'La Sesión ha caducado.',
                        '',
                        'info'
                    );
                }
            }
        },
    },
};
