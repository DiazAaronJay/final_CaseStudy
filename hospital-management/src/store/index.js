import { createStore } from 'vuex';
import axiosInstance from '@/axiosInstance';

const store = createStore({
  state: {
    user: null,
    role: null,
  },
  mutations: {
    setUser(state, user) {
      state.user = user;
    },
    setRole(state, role) {
      state.role = role;
    },
  },
  actions: {
    async login({ commit }, credentials) {
      try {
        const response = await axiosInstance.post('/login', credentials);
        const { token, user } = response.data;

        localStorage.setItem('token', token);
        axiosInstance.defaults.headers['Authorization'] = `Bearer ${token}`;

        commit('setUser', user);

        const roleResponse = await axiosInstance.get('/user/role');
        commit('setRole', roleResponse.data.role);

        return true;
      } catch (error) {
        console.error('Login failed', error);
        return false;
      }
    },
    async logout({ commit }) {
      try {
        await axiosInstance.post('/logout');
        localStorage.removeItem('token');
        commit('setUser', null);
        commit('setRole', null);
        delete axiosInstance.defaults.headers['Authorization'];
      } catch (error) {
        console.error('Logout failed', error);
      }
    },
  },
  getters: {
    isAuthenticated: (state) => !!state.user,
    userRole: (state) => state.role,
  },
});

export default store;
