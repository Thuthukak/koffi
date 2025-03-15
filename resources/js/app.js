import 'bootstrap/dist/css/bootstrap.min.css';
import './bootstrap';
import { createApp } from 'vue';
import App from './components/App.vue';
import Home from './components/Home/Home.vue';
import Bookings from './components/Home/Bookings.vue';
import Admin from './components/Admin/Admin.vue';
import Dashboard from './components/Admin/Dashboard.vue'; 
import { library } from "@fortawesome/fontawesome-svg-core";
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";  

import { faBars, 
        faMoon, 
        faSun, 
        faGlobe, 
        faUser, 
        faBell, 
        faCog, 
        faHome, 
        faSignOutAlt 
    } from "@fortawesome/free-solid-svg-icons";

library.add(faBars, 
            faMoon, 
            faSun, 
            faGlobe, 
            faUser, 
            faBell, 
            faCog, 
            faHome, 
            faSignOutAlt);



const app = createApp({});

app.component('Home', Home);
app.component('Bookings', Bookings);
app.component('Admin', Admin);
app.component('Dashboard', Dashboard);
app.component("font-awesome-icon", FontAwesomeIcon);

app.mount('#app');
