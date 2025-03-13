import 'bootstrap/dist/css/bootstrap.min.css';
import './bootstrap';
import { createApp } from 'vue';
import App from './components/App.vue';
import Home from './components/Home/Home.vue';
import Bookings from './components/Home/Bookings.vue';
import Admin from './components/Admin/Admin.vue';



const app = createApp({});

app.component('Home', Home);
app.component('Bookings', Bookings);
app.component('Admin', Admin);

app.mount('#app');
