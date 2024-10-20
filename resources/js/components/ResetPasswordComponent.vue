<script setup>
  import Form from './FormComponent.vue';
  import { store } from '../store.js';
  import {server, makeErrorMsg } from '../API.js';
</script>

<script>
export default{
  data(){
    return {
      password: '',
      password_confirmation: '',
      email: ''
    }
  },
  methods:{
    resetPassword(){
      var query = window.location.hash,
        token = query.substring(query.indexOf('=')+1);
      server('/api/reset-password', {
        token: token, 
        password: this.password, 
        password_confirmation: this.password_confirmation,
        email: this.email
      }, 'POST')
      .then( store.router.push('home') )
      .catch(makeErrorMsg)
    }
  }
}
</script>

<template>
  <Form
    caption="Reset password"
    ok-text="Ok"
    @form-submitted="resetPassword"
  >
    <label>Email:
      <input 
        v-model="email"
      >
    </label>
    <label>New password:
      <input 
        v-model="password" 
        type="password"
      >
    </label>
    <label>Confirm new password:
      <input 
        v-model="password_confirmation" 
        type="password"
      >
    </label>
  </Form>
</template>