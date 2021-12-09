require("./bootstrap");

// Import libraries
window.Vue = require("vue").default;
import vuetify from "./vuetify";
import router from "./routes";
import App from "./components/App";

function loggedIn() {
    return localStorage.getItem("token");
}
router.beforeEach((to, from, next) => {
    // console.log(to);
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
    render(h) {
        return h(App);
    },
});
