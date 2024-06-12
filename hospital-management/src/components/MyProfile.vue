<template>
  <div class="container">
    <!-- Quick Links Navigation -->
    <div class="row mb-4">
      <div class="col-md-12">
        <router-link to="/dashboard" class="btn btn-primary me-3">Home</router-link>
        <router-link v-if="userRole === 'admin'" to="/patients" class="btn btn-primary me-3">Manage Patients</router-link>
        <router-link v-if="userRole === 'admin'" to="/doctors" class="btn btn-primary me-3">Manage Doctors</router-link>
        <router-link v-if="userRole === 'doctor'" to="/appointments" class="btn btn-primary me-3">Manage Appointments</router-link>
        <router-link v-if="userRole === 'patient'" to="/appointments" class="btn btn-primary me-3">Book Appointments</router-link>
        <router-link v-if="userRole === 'admin' || userRole === 'doctor' || userRole === 'patient'" to="/records" class="btn btn-primary me-3">View Medical Records</router-link>
      </div>
    </div>
    <div class="row justify-content-center">
      <div class="col-md-10">
        <div class="card shadow p-4">
          <h3 class="text-center mb-4 text-center">My Profile</h3>
          <form @submit.prevent="updateUserProfile">
            <div v-if="userRole === 'admin'">
              <div class="mb-3">
                <label for="adminName" class="form-label">Admin Name</label>
                <input type="text" v-model="user.name" class="form-control">
              </div>
              <div class="mb-3">
                <label for="adminEmail" class="form-label">Admin Email</label>
                <input type="email" v-model="user.email" class="form-control">
              </div>
            </div>
            <div v-else-if="userRole === 'doctor'">
              <div class="mb-3">
                <label for="doctorName" class="form-label">Doctor Name</label>
                <input type="text" v-model="user.name" class="form-control">
              </div>
              <div class="mb-3">
                <label for="doctorEmail" class="form-label">Doctor Email</label>
                <input type="email" v-model="user.email" class="form-control">
              </div>
              <div class="mb-3">
                <label for="specialization" class="form-label">Specialization</label>
                <input type="text" v-model="user.specialization" class="form-control">
              </div>
            </div>
            <div v-else>
              <div class="mb-3">
                <label for="patientName" class="form-label">Patient Name</label>
                <input type="text" v-model="user.name" class="form-control">
              </div>
              <div class="mb-3">
                <label for="patientEmail" class="form-label">Patient Email</label>
                <input type="email" v-model="user.email" class="form-control">
              </div>
              <div class="mb-3">
                <label for="phone" class="form-label">Phone</label>
                <input type="text" v-model="user.phone" class="form-control">
              </div>
              <div class="mb-3">
                <label for="address" class="form-label">Address</label>
                <textarea v-model="user.address" class="form-control"></textarea>
              </div>
            </div>
            <button type="submit" class="btn btn-primary">Update Profile</button>
          </form>
        </div>
      </div>
    </div>
    <!-- Toast Notification -->
    <div class="toast" :class="{ show: isToastVisible, success: isToastSuccess, error: !isToastSuccess }">{{ toastMessage }}</div>
  </div>
</template>

<script>
import axiosInstance from '@/axiosInstance';

export default {
  name: 'MyProfilePage',
  data() {
    return {
      userRole: '',
      user: {
        name: '',
        email: '',
        specialization: '',
        phone: '',
        address: '',
      }, 
      isToastVisible: false,
      isToastSuccess: false,
      toastMessage: ''
    };
  },
  created() {
    this.fetchUserRole(); 
    this.fetchProfileDetails();
  },
  methods: {
    async fetchUserRole() {
      try {
        const response = await axiosInstance.get('/user/role');
        this.userRole = response.data.role; 
      } catch (error) {
        console.error('Error fetching user role', error);
      }
    },
    async fetchProfileDetails() {
      try {
        const response = await axiosInstance.get('/profile');
        const userData = response.data;
        console.log(userData);

        if (this.userRole === 'doctor') {
          this.user.name = userData.doctors.fullname;
          this.user.email = userData.doctors.email;
          this.user.specialization = userData.doctors.specialization;
        } else if (this.userRole === 'patient') {
          this.user.name = userData.patients.fullname;
          this.user.email = userData.patients.email;
          this.user.phone = userData.patients.phone;
          this.user.address = userData.patients.address;
        } else {
          this.user.name = userData.admin.name;
          this.user.email = userData.admin.email;
        }
      } catch (error) {
        console.error('Error fetching profile details', error);
      }
    },
    async updateUserProfile() {
      try {
        let fieldsToUpdate = ['name', 'email']; 

        if (this.userRole === 'doctor') {
          fieldsToUpdate.push('specialization');
        } else if (this.userRole === 'patient') {
          fieldsToUpdate.push('phone', 'address');
        }

        const userData = {};
        fieldsToUpdate.forEach(field => {
          userData[field] = this.user[field];
        });

        const response = await axiosInstance.put('/profile/update', userData);
        this.fetchProfileDetails();
        this.showToast(response.data.message, true);
      } catch (error) {
        console.error('Error updating user profile', error);
        this.showToast('Failed to update user profile', false);
      }
    },

    showToast(message, isSuccess) {
      this.toastMessage = message;
      this.isToastSuccess = isSuccess;
      this.isToastVisible = true;
      setTimeout(() => {
        this.isToastVisible = false;
      }, 3000);
    }
  }
};
</script>

<style scoped>
.btn-primary {
  background: rgba(62, 188, 202, 0.6);
  border: 1px solid rgba(62, 188, 202, 0.8);
  color: #ffffff;
  backdrop-filter: blur(10px);
  transition: background 0.3s, border 0.3s;
}

.btn-primary:hover {
  background: rgba(62, 188, 202, 0.8);
  border: 1px solid rgba(62, 188, 202, 1);
}

.btn {
  margin: 4px;
}
/* Modal styles */
.modal-backdrop {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(0, 0, 0, 0.5);
  display: flex;
  justify-content: center;
  align-items: center;
}

.modal-dialog {
  background: white;
  border-radius: 5px;
  overflow: hidden;
  max-width: 500px;
  width: 100%;
  transform: translateY(-50px);
  transition: transform 0.3s ease-out, opacity 0.3s ease-out;
}

.modal-enter-active, .modal-leave-active {
  transition: opacity 0.4s, transform 0.1s;
}

.modal-enter-active, .modal-leave-to /* .modal-leave-active in <2.1.8 */ {
  opacity: 0;
}

.modal-enter-active .modal-dialog, .modal-leave-to .modal-dialog {
  transform: translateY(-10px);
}

.modal-content {
  padding: 20px;
  background: white;
  border-radius: 5px;
}

.modal-header .btn-close {
  background: none;
  border: none;
}
/* Toast styles */
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
