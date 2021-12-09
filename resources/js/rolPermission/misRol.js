export const misRol = {
    methods: {
        async buscaNombreRolUser() {
            try {
                let response = await axios.get("/api/busca-nombre-rol-user");
                this.cRol = response.data.data;
            } catch (errors) {
                if (errors.response.status == 401) {
                    clearInterval(this.intervalId);
                    alert("La session ha caducado");
                    this.logout();
                }
            }
        },
    },
};
