<template>
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-md-6 col-lg-4">
        <div class="card shadow-sm p-4">
          <form @submit.prevent="login">
            <h2 class="text-center mb-4">Login</h2>
            <div class="mb-3">
              <label for="email" class="form-label">Email</label>
              <input v-model="email" id="email" type="email" class="form-control" placeholder="Enter your email" required>
            </div>
            <div class="mb-3">
              <label for="password" class="form-label">Password</label>
              <input v-model="password" id="password" type="password" class="form-control" placeholder="Enter your password" required>
            </div>
            <button type="submit" class="btn btn-glass w-100 mb-3">Login</button>
            <div class="line"><hr>or<hr></div>
            <router-link to="/register" class="d-block text-center router-link-custom">Don't have an account? Register here.</router-link>
          </form>
        </div>
      </div>
    </div>
    <div class="toast" :class="{ show: isToastVisible, success: isToastSuccess }">{{ toastMessage }}</div>
  </div>
</template>

<script>
import axiosInstance from '@/axiosInstance';

export default {
  data() {
    return {
      email: '',
      password: '',
      isToastVisible: false,
      isToastSuccess: false,
      toastMessage: '',
    };
  },
  methods: {
    async login() {
      try {
        const response = await axiosInstance.post('/login', {
          email: this.email,
          password: this.password,
        });
        const token = response.data.token;
        const user = response.data.user;
        
        localStorage.setItem('token', token);
        localStorage.setItem('user', JSON.stringify(user));
        axiosInstance.defaults.headers['Authorization'] = `Bearer ${token}`;
        
        this.toastMessage = 'Login successful!';
        this.isToastSuccess = true;
        this.showToast();
        setTimeout(() => {
          this.$router.push('/dashboard');
        }, 1000);
      } catch (error) {
        console.error('Login failed', error);
        this.toastMessage = 'Login failed. Please try again.';
        this.isToastSuccess = false;
        this.showToast();
      }
    },
    showToast() {
      this.isToastVisible = true;
      setTimeout(() => {
        this.isToastVisible = false;
      }, 3000);
    }
  }
};
</script>
  
  <style scoped>
  body {
    background-color: #e0e0e0;
    color: #2c3e50;
  }
  
  .card {
    border-radius: 10px;
    background: #ffffff;
    border: none;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
  }
  
  .form-control {
    background: #f8f8f8;
    border: 1px solid #ccc;
    color: #2c3e50;
  }
  
  .form-floating > label,
  .form-label {
    color: #666;
  }
  
  .form-control:focus {
    border-color: #3498db;
    box-shadow: none;
  }
  
  .btn-glass {
    background: rgba(62, 188, 202, 0.6);
    border: 1px solid rgba(62, 188, 202, 0.8);
    color: #ffffff;
    backdrop-filter: blur(10px);
    transition: background 0.3s, border 0.3s;
  }
  
  .btn-glass:hover {
    background: rgba(62, 188, 202, 0.8);
    border: 1px solid rgba(62, 188, 202, 1);
  }
  
  .router-link-custom {
    color: #3498db;
    transition: color 0.3s;
  }
  
  .router-link-custom:hover {
    color: #2980b9;
  }
  
  .line {
    display: flex;
    padding: 0 10px;
    align-items: center;
  }
  
  hr {
    flex-grow: 1;
    border: none;
    border-top: 1px solid #0A4D68;
    margin: 0 10px;
  }
  
  .toast {
    position: fixed;
    bottom: 20px;
    left: 50%;
    transform: translateX(-50%);
    padding: 10px 20px;
    border-radius: 5px;
    color: #fff;
    z-index: 9999;
    opacity: 0;
    transition: opacity 0.5s ease;
  }
  
  .toast.show {
    opacity: 1;
  }
  
  .toast.success {
    background-color: #27ae60;
  }
  
  .toast.error {
    background-color: #e74c3c;
  }
  </style>
  