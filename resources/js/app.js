require("./bootstrap");

// Import libraries
window.Vue = require("vue").default;
import vuetify from "./vuetify";
import VueSweetalert2 from 'vue-sweetalert2';
import 'sweetalert2/dist/sweetalert2.min.css';
import router from "./routes";
import App from "./components/App";

Vue.use(VueSweetalert2);

function loggedIn() {
    return localStorage.getItem("TOKEN_SAJONA");
}
router.beforeEach((to, from, next) => {
    if (to.matched.some(record => record.meta.requiresAuth)) {
        if (!loggedIn()) {
            next({
            path: '/',
            query: { redirect: to.fullPath }
            })
        } else {
            next()
        }
    } else if(to.matched.some(record => record.meta.guest)) {
        if (loggedIn()) {
            next({
            path: '/inicio/',
            query: { redirect: to.fullPath }
            })
        } else {
            next()
        }
    } else {
        next()
    }
})

const app = new Vue({
    el: "#app",
    router: router,
    vuetify,
    data:{
        arrPermisos: []
    },
    render(h) {
        return h(App);
    },
});
