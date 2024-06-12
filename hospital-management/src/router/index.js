import { createRouter, createWebHistory } from 'vue-router';
import LoginPage from '@/components/LoginPage.vue';
import RegistrationPage from '@/components/RegistrationPage.vue';
import DashboardPage from '@/components/DashboardPage.vue';
import PatientsPage from '@/components/PatientsPage.vue';
import DoctorsPage from '@/components/DoctorsPage.vue';
import AppointmentsPage from '@/components/AppointmentsPage.vue';
import MedicalRecordsPage from '@/components/MedicalRecordsPage.vue';
import MyProfile from '@/components/MyProfile.vue';

const routes = [
  { path: '/', redirect: '/login'},
  { path: '/login', component: LoginPage },
  { path: '/register', component: RegistrationPage },
  { path: '/dashboard', component: DashboardPage },
  { path: '/patients', component: PatientsPage },
  { path: '/doctors', component: DoctorsPage },
  { path: '/appointments', component: AppointmentsPage },
  { path: '/records', component: MedicalRecordsPage },
  { path: '/my-profile', component: MyProfile },
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

export default router;
