<template>
    <div class="container">
      <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
          <div class="card shadow-sm p-4">
            <form @submit.prevent="register">
              <h2 class="text-center mb-4">Patient Registration</h2>
              <div class="row mb-3">
                <div class="col-md-12">
                  <div class="form-floating">
                    <input v-model="fullName" id="full_name" type="text" class="form-control" placeholder="Enter your complete name" required>
                    <label for="full_name">Full Name</label>
                  </div>
                </div>
              </div>
              <div class="row mb-3">
                <div class="col-md-12">
                  <div class="form-floating">
                    <input v-model="email" id="email" type="email" class="form-control" placeholder="Enter your email" required>
                    <label for="email">Email</label>
                  </div>
                </div>
              </div>
              <div class="row mb-3">
                <div class="col-md-6">
                  <div class="form-floating">
                    <input :type="showPassword ? 'text' : 'password'" v-model="password" id="password" class="form-control" placeholder="Enter your password" required>
                    <label for="password">Password</label>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-floating">
                    <input :type="showPassword ? 'text' : 'password'" v-model="password_confirmation" id="password_confirmation" class="form-control" placeholder="Confirm your password" required>
                    <label for="password_confirmation">Confirm Password</label>
                  </div>
                </div>
              </div>
              <div class="form-check mb-3">
                <input class="form-check-input" type="checkbox" id="showPassword" v-model="showPassword">
                <label class="form-check-label" for="showPassword">
                  Show Password
                </label>
              </div>
              <button type="submit" class="btn btn-glass w-100 mb-3">Register</button>
              <div class="line"><hr>or<hr></div>
              <router-link to="/login" class="d-block text-center router-link-custom">Already have an account? Login here.</router-link>
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
            fullName: '',
            email: '',
            password: '',
            password_confirmation: '',
            role: '',
            showPassword: false,
            isToastVisible: false,
            isToastSuccess: false,
            toastMessage: ''
            };
        },
        methods: {
            async register() {
            try {
                await axiosInstance.post('/register', {
                name: this.fullName,
                email: this.email,
                password: this.password,
                password_confirmation: this.password_confirmation,
                });
                this.toastMessage = 'Registration successful!';
                this.isToastSuccess = true;
                this.showToast();
                setTimeout(() => {
                    this.$router.push('/login');
                }, 500);
            } catch (error) {
                console.error('Registration failed', error);
                this.toastMessage = 'Registration failed. Please try again.';
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
  
  .form-control,
  .form-select {
    background: #f8f8f8;
    border: 1px solid #ccc;
    color: #2c3e50;
  }
  
  .form-floating > label {
    color: #666;
  }
  
  .form-floating > .form-control:focus,
  .form-floating > .form-select:focus {
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
  