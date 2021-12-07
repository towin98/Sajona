require('./bootstrap');

// Import libraries
window.Vue = require('vue').default;
import vuetify from './vuetify';
import router from './routes';
import App from './components/App';


function loggedIn(){
    return localStorage.getItem('token')
}

router.beforeEach((to, from, next) => {
    // console.log(to);
    if (to.matched.some(record => record.meta.requiresAuth)) {
        // this route requires auth, check if logged in
        // if not, redirect to login page.
        if (!loggedIn()) {
            // console.log("ok");
            next({
            path: '/',
            query: { redirect: to.fullPath }
            })
        } else {
            // console.log("ok2");
            next()
        }
    } else if(to.matched.some(record => record.meta.guest)) {
        // console.log("holita");
        if (loggedIn()) {
            next({
            path: '/sajona/',
            query: { redirect: to.fullPath }
            })
        } else {
            next()
        }
    } else {
        // console.log("hola2");
        next() // make sure to always call next()!
    }
})

const app = new Vue({
    el: '#app',
    router:router,
    vuetify,
    render(h) {return h(App)}
});
