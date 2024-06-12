<template>
    <div v-if="isVisible" class="custom-toaster" :class="[type]">
      <p>{{ message }}</p>
    </div>
  </template>
  
  <script>
  export default {
    props: {
      message: String,
      type: {
        type: String,
        default: 'success' // Default type is success, can be success, error, or info
      }
    },
    data() {
      return {
        isVisible: false
      };
    },
    methods: {
      show() {
        this.isVisible = true;
        setTimeout(() => {
          this.hide();
        }, 3000); // Hides after 3 seconds
      },
      hide() {
        this.isVisible = false;
      }
    }
  };
  </script>
  
  <style scoped>
  .custom-toaster {
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
  
  .success {
    background-color: #4caf50;
  }
  
  .error {
    background-color: #f44336;
  }
  
  .info {
    background-color: #2196f3;
  }
  
  .custom-toaster.show {
    opacity: 1;
  }
  </style>
  