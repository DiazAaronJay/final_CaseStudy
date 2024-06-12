<template>
  <div class="container">
    <!-- Quick Links Navigation -->
    <div class="row mb-4">
      <div class="col-md-12">
        <router-link to="/dashboard" class="btn btn-primary me-3">Home</router-link>
        <router-link to="/patients" class="btn btn-primary me-3">Manage Patients</router-link>
        <router-link to="/appointments" class="btn btn-primary me-3">View Appointments</router-link>
        <router-link to="/records" class="btn btn-primary me-3">View Medical Records</router-link>
        <router-link to="/my-profile" class="btn btn-primary me-3">My Profile</router-link>
      </div>
    </div>

    <div class="row justify-content-center">
      <div class="col-md-10">
        <div class="card shadow-sm p-4">
          <h2 class="text-center mb-4">Manage Doctors</h2>
          <button @click="showModal = true" class="btn btn-primary mb-3">New Doctor</button>

          <table class="table">
            <thead>
              <tr>
                <th>Full Name</th>
                <th>Email</th>
                <th>Specialization</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr v-if="doctors.length === 0">
                <td colspan="4" class="text-center">No Doctor Data</td>
              </tr>
              <tr v-for="doctor in doctors" :key="doctor.id">
                <td>{{ doctor.fullname }}</td>
                <td>{{ doctor.email }}</td>
                <td>{{ doctor.specialization }}</td>
                <td>
                  <button @click="editDoctor(doctor)" class="btn btn-sm btn-primary">Edit</button>
                  <button @click="confirmDelete(doctor.id)" class="btn btn-sm btn-danger">Delete</button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- New Doctor Modal -->
    <transition name="modal">
      <div v-if="showModal" class="modal-backdrop">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title">New Doctor</h5>
            </div>
            <div class="modal-body">
              <form @submit.prevent="createDoctor">
                <div class="row mb-3">
                  <div class="col-md-12">
                    <label for="completeName" class="form-label">Complete Name</label>
                    <input type="text" class="form-control" id="completeName" v-model="newDoctor.fullname" required>
                  </div>
                </div>
                <div class="row mb-3">
                  <div class="col-md-12">
                    <label for="email" class="form-label">Email Address</label>
                    <input type="text" class="form-control" id="email" v-model="newDoctor.email">
                  </div>
                </div>
                <div class="row mb-3">
                  <div class="col-md-12">
                    <label for="specialization" class="form-label">Specialization</label>
                    <input type="text" class="form-control" id="specialization" v-model="newDoctor.specialization">
                  </div>
                </div>
                <button type="submit" class="btn btn-primary">Add</button>
                <button type="button" class="btn btn-secondary" @click="showModal = false">Cancel</button>
              </form>
            </div>
          </div>
        </div>
      </div>
    </transition>

    <!-- Edit Doctor Modal -->
    <transition name="modal">
      <div v-if="showUpdateModal" class="modal-backdrop">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title">Edit Doctor</h5>
            </div>
            <div class="modal-body">
              <form @submit.prevent="updateDoctor">
                <div class="row mb-3">
                  <div class="col-md-12">
                    <label for="completeName" class="form-label">Complete Name</label>
                    <input type="text" class="form-control" id="completeName" v-model="editedDoctor.fullname" required>
                  </div>
                </div>
                <div class="row mb-3">
                  <div class="col-md-12">
                    <label for="email" class="form-label">Email Address</label>
                    <input type="text" class="form-control" id="email" v-model="editedDoctor.email">
                  </div>
                </div>
                <div class="row mb-3">
                  <div class="col-md-12">
                    <label for="specialization" class="form-label">Specialization</label>
                    <input type="text" class="form-control" id="specialization" v-model="editedDoctor.specialization">
                  </div>
                </div>
                <button type="submit" class="btn btn-primary">Save</button>
                <button type="button" class="btn btn-secondary" @click="showUpdateModal = false">Cancel</button>
              </form>
            </div>
          </div>
        </div>
      </div>
    </transition>

    <!-- Delete Confirmation Modal -->
    <transition name="modal">
      <div v-if="showDeleteModal" class="modal-backdrop">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title">Confirm Delete</h5>
            </div>
            <div class="modal-body">
              <p>Are you sure you want to delete this doctor?</p>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-danger" @click="deleteConfirmed">Yes, Delete</button>
              <button type="button" class="btn btn-secondary" @click="showDeleteModal = false">Cancel</button>
            </div>
          </div>
        </div>
      </div>
    </transition>

    <!-- Toast Notification -->
    <div class="toast" :class="{ show: isToastVisible, success: isToastSuccess, error: !isToastSuccess }">{{ toastMessage }}</div>
  </div>
</template>

<script>
import axiosInstance from '@/axiosInstance';

export default {
  data() {
    return {
      doctors: [],
      showModal: false,
      showUpdateModal: false,
      editedDoctor: {},
      newDoctor: {
        fullname: '',
        email: '',
        specialization: ''
      },
      isToastVisible: false,
      isToastSuccess: false,
      toastMessage: '',
      showDeleteModal: false,
      doctorIdToDelete: null
    };
  },
  created() {
    this.fetchDoctors();
  },
  methods: {
    async fetchDoctors() {
      try {
        const response = await axiosInstance.get('/doctors');
        this.doctors = response.data;
        console.log(this.doctors);
      } catch (error) {
        console.error('Error fetching doctors', error);
      }
    },
    async deleteDoctor(doctorId) {
      try {
        await axiosInstance.delete(`/doctors/${doctorId}`);
        this.doctors = this.doctors.filter(doctor => doctor.id !== doctorId);
        this.showDeleteModal = false;
        this.showToast('Doctor deleted successfully!', true);
      } catch (error) {
        console.error('Error deleting doctor', error);
        this.showToast('Error deleting doctor. Please try again.', false);
      }
    },
    confirmDelete(doctorId) {
      this.showDeleteModal = true;
      this.doctorIdToDelete = doctorId;
    },
    async deleteConfirmed() {
      if (this.doctorIdToDelete) {
        await this.deleteDoctor(this.doctorIdToDelete);
        this.doctorIdToDelete = null;
      }
    },
    async createDoctor() {
      try {
        const doctorData = {
          fullname: this.newDoctor.fullname,
          email: this.newDoctor.email,
          specialization: this.newDoctor.specialization,
        };
        const response = await axiosInstance.post('/doctors', doctorData);
        this.doctors.push(response.data);
        this.newDoctor = { firstName: '', lastName: '', email: '', specialization: '' };
        this.showModal = false;
        this.showToast('Doctor added successfully!', true);
      } catch (error) {
        console.error('Error creating doctor', error);
        this.showToast('Error adding doctor. Please try again.', false);
      }
    },
    async editDoctor(doctor) {
      this.editedDoctor = { ...doctor };
      this.showUpdateModal = true;
    },
    async updateDoctor() {
      try {
        const response = await axiosInstance.put(`/doctors/${this.editedDoctor.id}`, this.editedDoctor);
        const index = this.doctors.findIndex(doctor => doctor.id === this.editedDoctor.id);
        this.doctors.splice(index, 1, response.data);
        this.showUpdateModal = false;
        this.showToast('Doctors updated successfully!', true);
      } catch (error) {
        console.error('Error updating doctor', error);
        this.showToast('Error updating doctor. Please try again.', false);
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
/* Theme Colors */
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

.btn-danger {
  background-color: #e74c3c;
  border-color: #e74c3c;
}

.btn-danger:hover {
  background-color: #c0392b;
  border-color: #c0392b;
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

.btn {
  margin: 4px;
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

