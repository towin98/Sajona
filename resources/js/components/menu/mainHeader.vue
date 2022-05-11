<template>
    <div>
        <div class="d-flex">
            <v-spacer></v-spacer>
            <v-icon size="20" color="black">home</v-icon>
            <h4>Modulos</h4>
            <h4>/</h4>
            <v-icon size="20" color="black">navigate_next</v-icon
            >
            <h4 class="text-capitalize">{{ proceso }}</h4>
        </div>
        <div class="d-flex">
            <v-btn
                icon
                class="success--text"
                tile
                title="Generar reporte en Excel"
            >
                <v-icon> task </v-icon>
            </v-btn>
            <v-btn icon class="black--text" tile title="Generar reporte en Txt">
                <v-icon> text_snippet </v-icon>
            </v-btn>
            <v-btn icon color="red" tile title="Generar reporte en PDF">
                <v-icon> picture_as_pdf </v-icon>
            </v-btn>
            <v-spacer></v-spacer>
            <v-btn small color="primary" class="white--text" tile>
                <v-icon> print </v-icon>Imprimir
            </v-btn>
            <!-- <v-btn small color="success" class="white--text" tile>
                <v-icon> add_circle </v-icon>Nuevo
            </v-btn> -->
        </div>
        <v-progress-linear
            color="red"
            rounded
            value="100"
        ></v-progress-linear>
    </div>
</template>
<script>
export default {
    props: ["proceso"],
};
</script>
