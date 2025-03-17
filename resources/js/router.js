import { createRouter, createWebHistory } from 'vue-router';
import Dashboard from './components/Admin/Dashboard/Dashboard.vue';
import AdminBookings from './components/Admin/Dashboard/AdminBookings.vue';


const routes = [
    { path: '/admin/dashboard', component: Dashboard },
    { path: '/admin/bookings', component: AdminBookings },
    

];

const router = createRouter({
    history: createWebHistory(),
    routes
});

export default router;
