export const misRol = {
    methods: {
        // async canRol(rol) {

        //     let response = await axios.post("/api/can-rol", rol);
        //     return response.data.data
        // },
        async buscaNombreRolUser() {

            let response = await axios.get("/api/busca-nombre-rol-user");
            return response.data.data
        },
    },
};
