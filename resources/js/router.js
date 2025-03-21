import { createRouter, createWebHistory } from 'vue-router';
import Dashboard from './components/Admin/Dashboard.vue';


const routes = [
    { path: '/admin/dashboard', component: Dashboard },
    

];

const router = createRouter({
    history: createWebHistory(),
    routes
});

export default router;
