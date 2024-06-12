<template>
  <div class="dashboard">
    <h1>Welcome to the Dashboard</h1>
    <nav v-if="role === 'admin'">
      <router-link to="/patients" class="nav-link">Manage Patients</router-link>
      <router-link to="/doctors" class="nav-link">Manage Doctors</router-link>
      <router-link to="/my-profile" class="nav-link">My Profile</router-link>
      <router-link to="/appointments" class="nav-link">View Appointments</router-link>
      <router-link to="/records" class="nav-link">View Medical Records</router-link>
    </nav>
    <nav v-if="role === 'doctor'">
      <router-link to="/appointments" class="nav-link">Manage Appointments</router-link>      
      <router-link to="/my-profile" class="nav-link">My Profile</router-link>
      <router-link to="/records" class="nav-link">View Medical Records</router-link>
    </nav>
    <nav v-if="role === 'patient'">
      <router-link to="/appointments" class="nav-link">Book Appointments</router-link>     
      <router-link to="/my-profile" class="nav-link">My Profile</router-link>
      <router-link to="/records" class="nav-link">View Medical Records</router-link>
    </nav>
    <button @click="logout" class="logout-button">Logout</button>
  </div>
</template>

<script>
import axiosInstance from '@/axiosInstance';

export default {
  data() {
    return {
      role: '',
    };
  },
  created() {
    const user = JSON.parse(localStorage.getItem('user'));
    this.role = user ? user.role : '';
  },
  methods: {
    async logout() {
      try {
        await axiosInstance.post('/logout');
        localStorage.removeItem('token');
        localStorage.removeItem('user');
        delete axiosInstance.defaults.headers['Authorization'];
        this.$router.push('/login');
      } catch (error) {
        console.error('Logout failed', error);
      }
    }
  }
};
</script>

<style scoped>
.dashboard {
  display: flex;
  flex-direction: column;
  align-items: center;
  padding: 20px;
  background-color: #f0f2f5;
  border-radius: 8px;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}

h1 {
  font-size: 24px;
  margin-bottom: 20px;
}

nav {
  display: flex;
  flex-direction: column;
  gap: 10px;
  width: 100%;
  max-width: 400px;
  margin-bottom: 20px;
}

.nav-link {
  display: block;
  padding: 10px;
  background-color: #ffffff;
  border: 1px solid #dcdcdc;
  border-radius: 8px;
  text-align: center;
  text-decoration: none;
  color: #3498db;
  transition: background-color 0.3s;
}

.nav-link:hover {
  background-color: #e8f4f8;
}

.logout-button {
  padding: 10px 20px;
  background-color: #e74c3c;
  border: none;
  border-radius: 8px;
  color: #ffffff;
  cursor: pointer;
  transition: background-color 0.3s;
}

.logout-button:hover {
  background-color: #c0392b;
}
</style>
