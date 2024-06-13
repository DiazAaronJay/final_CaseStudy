<template>
  <div class="container">
    <!-- Quick Links Navigation -->
    <div class="row mb-4">
      <div class="col-md-12">
        <!-- this is the dashboard  -->
        <router-link to="/dashboard" class="btn btn-primary me-3">Home</router-link>
        <router-link v-if="userRole === 'admin'" to="/patients" class="btn btn-primary me-3">Manage Patients</router-link>
        <router-link v-if="userRole === 'admin'" to="/doctors" class="btn btn-primary me-3">Manage Doctors</router-link>
        <router-link v-if="userRole === 'admin' || userRole === 'doctor' || userRole === 'patient'" to="/my-profile" class="btn btn-primary me-3">My Profile</router-link>
        <router-link v-if="userRole === 'admin' || userRole === 'doctor' || userRole === 'patient'" to="/records" class="btn btn-primary me-3">View Medical Records</router-link>
      </div>
    </div>
    <div class="row justify-content-center">
      <div class="col-md-10">
        <div class="card shadow p-4">
          <h3 class="text-center mb-4 text-center">Manage Appointments</h3>
          <div v-if="userRole === 'admin'">
            <h3>All Appointments</h3>
            <table class="table">
              <thead>
                <tr>
                  <th>Patient Name</th>
                  <th>Doctor Name</th>
                  <th>Appointment Date</th>
                  <th>Status</th>
                  <th>Reason</th>
                </tr>
              </thead>
              <tbody>
                <tr v-if="appointments.length === 0">
                  <td colspan="4" class="text-center">No Appointments</td>
                </tr>
                <tr v-for="appointment in appointments" :key="appointment.id">
                  <td>{{ appointment.patient.name }}</td>
                  <td>{{ appointment.doctor.name }}</td>
                  <td>{{ appointment.appointment_date }}</td>
                  <td>{{ appointment.status }}</td>
                  <td>{{ appointment.reason }}</td>
                </tr>
              </tbody>
            </table>
          </div>
          <div v-else-if="userRole === 'doctor'">
            <h3>My Appointments</h3>
            <table class="table">
              <thead>
                <tr>
                  <th>Patient Name</th>
                  <th>Appointment Date</th>
                  <th>Status</th>
                  <th>Reason</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                <tr v-if="appointments.length === 0">
                  <td colspan="4" class="text-center">No Appointments</td>
                </tr>
                <tr v-for="appointment in appointments" :key="appointment.id">
                  <td>{{ appointment.patient.name }}</td>
                  <td>{{ appointment.appointment_date }}</td>
                  <td>{{ appointment.status }}</td>
                  <td>{{ appointment.reason }}</td>
                  <td>
                    <div v-if="appointment.status === 'scheduled'">
                      <button class="btn btn-sm btn-primary" @click="openCompleteAppointmentModal(appointment.id)">Complete</button>
                      <button class="btn btn-sm btn-danger" @click="updateAppointmentStatus(appointment.id, 'canceled')">Cancel</button>
                    </div>
                    <div v-else-if="appointment.status === 'completed'" class="text-success">
                      Completed
                    </div>
                    <div v-else-if="appointment.status === 'cancelled'" class="text-danger">
                      Cancelled
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
          <div v-else>
            <button v-if="userRole === 'patient'" class="btn btn-primary mb-3" @click="toggleAppointments">{{ showAppointments ? 'Book appointment' : 'My appointments' }}</button>
            <div v-if="showAppointments">
              <h3>My Appointments</h3>
              <table class="table">
                <thead>
                  <tr>
                    <th>Doctor Name</th>
                    <th>Appointment Date</th>
                    <th>Status</th>
                    <th>Reason</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-if="appointments.length === 0">
                    <td colspan="5" class="text-center">No Appointments</td>
                  </tr>
                  <tr v-for="appointment in appointments" :key="appointment.id">
                    <td>{{ appointment.doctor.name }}</td>
                    <td>{{ appointment.appointment_date }}</td>
                    <td>{{ appointment.status }}</td>
                    <td>{{ appointment.reason }}</td>
                    <td>
                      <div v-if="appointment.status === 'completed'" class="text-success">
                        Completed
                      </div>
                      <div v-else-if="appointment.status === 'cancelled'" class="text-danger">
                        Cancelled
                      </div>
                      <div v-else-if="appointment.status === 'scheduled'" class="text-danger">
                        <button class="btn btn-sm btn-danger" @click="confirmCancelAppointment(appointment.id)">Cancel</button>
                      </div> 
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
            <div v-else>
              <h3>Book Appointment</h3>
              <form @submit.prevent="bookAppointment">
                <div class="row mb-3">
                  <div class="col-md-6">
                    <label for="doctor" class="form-label">Doctor:</label>
                    <select v-if="doctors.length > 0" v-model="newAppointment.doctor_id" required class="form-select">
                      <option value="">Select a doctor</option>
                      <option v-for="doctor in doctors" :value="doctor.user_id" :key="doctor.id">{{ doctor.fullname }}</option>
                    </select>
                    <select v-else disabled class="form-select">
                      <option value="" disabled selected>No Doctor Available</option>
                    </select>
                  </div>
                  <div class="col-md-6">
                    <label for="appointment_date" class="form-label">Date:</label>
                    <input type="date" v-model="newAppointment.appointment_date" required class="form-control" />
                  </div>
                </div>
                <div class="row mb-3">
                  <div class="col-md-12">
                    <label for="reason" class="form-label">Reason:</label>
                    <select v-model="newAppointment.reason" required class="form-select">
                        <option value="Check-up">Check-up</option>
                        <option value="Follow-up">Follow-up</option>
                        <option value="Procedure">Procedure</option>
                    </select>
                  </div>
                </div>
                <button type="submit" class="btn btn-primary">Book</button>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
    <!-- Cancel Confirmation Modal -->
    <transition name="modal">
      <div v-if="showCancelConfirmation" class="modal-backdrop">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title">Confirm Cancel Appointment</h5>
            </div>
            <div class="modal-body">
              <p>Are you sure you want to cancel this appointment?</p>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-danger" @click="cancelAppointment(this.appointmentIdToCancel, 'cancelled')">Yes, Cancel</button>
              <button type="button" class="btn btn-secondary" @click="cancelCancelAppointment()">No, Go Back</button>
            </div>
          </div>
        </div>
      </div>
    </transition>
    <!-- Complete Appointment Modal -->
    <transition name="modal">
      <div v-if="showCompleteAppointmentModal" class="modal-backdrop">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title">Complete Appointment</h5>
              <button type="button" class="btn-close" @click="closeCompleteAppointmentModal"></button>
            </div>
            <div class="modal-body">
              <form @submit.prevent="completeAppointment">
                <div class="mb-3">
                  <label for="patientId" class="form-label">Patient ID</label>
                  <input type="text" v-model="completeAppointmentData.patient_id" class="form-control" readonly>
                </div>
                <div class="mb-3">
                  <label for="doctorName" class="form-label">Doctor</label>
                  <input type="text" v-model="completeAppointmentData.doctor_name" class="form-control" readonly>
                </div>
                <div class="mb-3">
                  <label for="description" class="form-label">Description</label>
                  <textarea v-model="completeAppointmentData.description" class="form-control" required></textarea>
                </div>
                <button type="submit" class="btn btn-primary">Complete</button>
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
  name: 'AppointmentsPage',
  data() {
    return {
      appointments: [],
      doctors: [],
      newAppointment: {
        doctor_id: '',
        appointment_date: '',
        reason: '',
      },
      completeAppointmentData: {
        patient_id: '',
        doctor_name: '',
        appointment_id: '',
        description: '',
        status: '',
      },
      userRole: '',
      isToastVisible: false,
      isToastSuccess: false,
      toastMessage: '',
      showAppointments: false,
      showCancelConfirmation: false,
      showCompleteAppointmentModal: false,
      appointmentIdToCancel: null,
    };
  },
  created() {
    this.fetchAppointments();
    this.fetchDoctors();
    this.fetchUserRole(); 
  },
  methods: {
    async fetchAppointments() {
      try {
        const response = await axiosInstance.get('/appointments');
        this.appointments = response.data;
      } catch (error) {
        console.error('Error fetching appointments', error);
      }
    },
    async fetchDoctors() {
      try {
        const response = await axiosInstance.get('/doctors');
        this.doctors = response.data;
      } catch (error) {
        console.error('Error fetching doctors', error);
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
    async bookAppointment() {
      try {
        const response = await axiosInstance.post('/appointments', this.newAppointment);
        this.appointments.push(response.data);
        this.newAppointment = {doctor_id: '', appointment_date: '', reason: ''}  
        this.fetchAppointments();
        this.showToast('Appointment booked successfully', true);
      } catch (error) {
        console.error('Error booking appointment', error);
        this.showToast('Failed to book appointment', false);
      }
    },
    async updateAppointmentStatus(appointmentId, status) {
      try {
        await axiosInstance.put(`/appointments/${appointmentId}`, { status });
        // const appointment = this.appointments.find(a => a.id === appointmentId);
        this.showToast('Appointment status updated successfully', true);
      } catch (error) {
        console.error('Error updating appointment status', error);
        this.showToast('Failed to update appointment status', false);
      }
    },
    confirmCancelAppointment(appointmentId) {
      this.showCancelConfirmation = true;
      this.appointmentIdToCancel = appointmentId;
    },
    cancelCancelAppointment() {
      this.showCancelConfirmation = false;
      this.appointmentIdToCancel = null;
    },
    async cancelAppointment(appointmentId, status) {
      try {
        await axiosInstance.put(`/appointments/${this.appointmentIdToCancel}`, { status });
        this.appointments = this.appointments.filter(a => a.id !== appointmentId);
        this.showToast('Appointment has been cancelled', true);
        this.cancelCancelAppointment();
        this.fetchAppointments();
      } catch (error) {
        console.error('Error canceling appointment', error);
        this.showToast('Failed to cancel appointment', false);
      }
    },
    showToast(message, isSuccess) {
      this.toastMessage = message;
      this.isToastSuccess = isSuccess;
      this.isToastVisible = true;
      setTimeout(() => {
        this.isToastVisible = false;
      }, 3000);
    },
    toggleAppointments() {
      this.showAppointments = !this.showAppointments;
    },
    openCompleteAppointmentModal(appointmentId) {
      const appointment = this.appointments.find(a => a.id === appointmentId);
      const doctor = this.doctors.find(d => d.user_id === appointment.doctor_id);
      this.completeAppointmentData.patient_id = appointment.patient_id;
      this.completeAppointmentData.doctor_name = doctor.fullname;
      this.completeAppointmentData.appointment_id = appointmentId;
      this.completeAppointmentData.description = '';
      this.completeAppointmentData.status = 'completed';
      this.showCompleteAppointmentModal = true;
    },
    closeCompleteAppointmentModal() {
      this.showCompleteAppointmentModal = false;
      this.completeAppointmentData = {
        patient_id: '',
        doctor_name: '',
        appointment_id: '',
        description: ''
      };
    },
    async completeAppointment() {
      try {
        const response = await axiosInstance.put(`/appointments/${this.completeAppointmentData.appointment_id}`, {
          patient_id: this.completeAppointmentData.patient_id,
          doctor_name: this.completeAppointmentData.doctor_name,
          description: this.completeAppointmentData.description,
          status: this.completeAppointmentData.status
        });
        // const appointment = this.appointments.find(a => a.id === this.completeAppointmentData.appointment_id);
        // appointment.status = 'completed';
        this.fetchAppointments();
        console.log(response);
        this.showToast('Appointment marked as complete', true);
        this.closeCompleteAppointmentModal();
      } catch (error) {
        console.error('Error completing appointment', error);
        this.showToast('Failed to complete appointment', false);
      }
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
