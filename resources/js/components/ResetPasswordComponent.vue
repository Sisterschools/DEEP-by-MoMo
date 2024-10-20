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
      email: '',
      token: ''
    }
  },
  mounted(){
    var query = window.location.hash
    this.token = query.substring(query.indexOf('=')+1, query.indexOf('&')),
    this.email = query.substring(query.indexOf('&email=')+7);
  },
  methods:{
    resetPassword(){
      server('/api/reset-password', {
        token: this.token, 
        password: this.password, 
        password_confirmation: this.password_confirmation,
        email: this.email
      }, 'POST')
      .then( () => {
        server('/api/login', {
          email: this.email, 
          password: this.password
        }, 
        'POST')
        .then( ( json ) => {
          this.invalid = false
          store.token = json.access_token
          store.userData = json.data
          store.router.push('/')
          /*  
            Different users can login using a different tab: so start the title with their name
          */
          var title = document.querySelector('title').innerText
          document.querySelector('title').innerText = json.data.name + ' : ' + title
          
        })      
      })
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
        type="hidden"
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