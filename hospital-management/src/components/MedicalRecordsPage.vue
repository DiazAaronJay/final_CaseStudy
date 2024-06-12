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
        <router-link v-if="userRole === 'admin' || userRole === 'doctor' || userRole === 'patient'" to="/my-profile" class="btn btn-primary me-3">My Profile</router-link>
      </div>
    </div>
    <div class="row justify-content-center">
      <div class="col-md-10">
        <div class="card shadow p-4">
          <h3 class="text-center mb-4">Manage Medical Records</h3>
          <div v-if="userRole === 'admin'">
            <h3>All Medical Records</h3>
            <table class="table">
              <thead>
                <tr>
                  <th>Patient Name</th>
                  <th>Description</th>
                  <th>Doctor</th>
                </tr>
              </thead>
              <tbody>
                <tr v-if="records.length === 0">
                  <td colspan="3" class="text-center">No Medical Records</td>
                </tr>
                <tr v-for="record in records" :key="record.id">
                  <td>{{ record.patient_name }}</td>
                  <td>{{ record.description }}</td>
                  <td>{{ record.doctor }}</td>
                </tr>
              </tbody>
            </table>
          </div>
          <div v-else-if="userRole === 'doctor'">
            <table class="table">
              <thead>
                <tr>
                  <th>Patient Name</th>
                  <th>Description</th>
                  <th>Doctor</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                <tr v-if="records.length === 0">
                  <td colspan="4" class="text-center">No Medical Records</td>
                </tr>
                <tr v-for="record in records" :key="record.id">
                  <td>{{ record.patient_name }}</td>
                  <td>{{ record.description }}</td>
                  <td>{{ record.doctor }}</td>
                  <td>
                    <button class="btn btn-sm btn-primary" @click="openUpdateRecordModal(record)">Update</button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
          <div v-else-if="userRole === 'patient'">
            <h3>My Medical Records</h3>
            <table class="table">
              <thead>
                <tr>
                  <th>Doctor</th>
                  <th>Description</th>
                </tr>
              </thead>
              <tbody>
                <tr v-if="records.length === 0">
                  <td colspan="2" class="text-center">No Medical Records</td>
                </tr>
                <tr v-for="record in records" :key="record.id">
                  <td>{{ record.doctor }}</td>
                  <td>{{ record.description }}</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
    <!-- Update Record Modal -->
    <transition name="modal">
      <div v-if="showUpdateRecordModal" class="modal-backdrop">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title">Update Medical Record</h5>
            </div>
            <div class="modal-body">
              <form @submit.prevent="updateMedicalRecord">
                <div class="mb-3">
                  <label for="description" class="form-label">Description:</label>
                  <textarea v-model="updateRecordData.description" required class="form-control"></textarea>
                </div>
                <button type="submit" class="btn btn-primary">Update</button>
                <button type="button" class="btn btn-secondary" @click="closeUpdateRecordModal">Cancel</button>
              </form>
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
  name: 'MedicalRecordsPage',
  data() {
    return {
      records: [],
      userRole: '',
      isToastVisible: false,
      isToastSuccess: false,
      toastMessage: '',
      showUpdateRecordModal: false,
      updateRecordData: {
        id: '',
        description: ''
      }
    };
  },
  created() {
    this.fetchRecords();
    this.fetchUserRole();
  },
  methods: {
    async fetchRecords() {
      try {
        const response = await axiosInstance.get('/records');
        this.records = response.data;
        console.log(this.records);
      } catch (error) {
        console.error('Error fetching medical records', error);
      }
    },
    async fetchUserRole() {
      try {
        const response = await axiosInstance.get('/user/role');
        this.userRole = response.data.role;
      } catch (error) {
        console.error('Error fetching user role', error);
      }
    },
    openUpdateRecordModal(record) {
      this.updateRecordData.id = record.id;
      this.updateRecordData.description = record.description;
      this.showUpdateRecordModal = true;
    },
    closeUpdateRecordModal() {
      this.showUpdateRecordModal = false;
      this.updateRecordData = { id: '', description: '' };
    },
    async updateMedicalRecord() {
      try {
        await axiosInstance.put(`/records/${this.updateRecordData.id}`, {
          description: this.updateRecordData.description
        });
        this.showToast('Medical record updated successfully', true);
        this.closeUpdateRecordModal();
        this.fetchRecords();
      } catch (error) {
        console.error('Error updating medical record', error);
        this.showToast('Failed to update medical record', false);
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
.table {
  width: 100%;
  border-collapse: collapse;
}

.table th, .table td {
  border: 1px solid #dddddd;
  padding: 8px;
  text-align: left;
}

.table th {
  background-color: #f2f2f2;
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
