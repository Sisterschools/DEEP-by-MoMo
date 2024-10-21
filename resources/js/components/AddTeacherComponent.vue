<script setup>
  import { server, makeErrorMsg } from '../API.js'
  import  { store } from '../store.js'
  import Form from './FormComponent.vue'
</script>

<script>
export default{
  data(){
    return{
      name: null,
      phone_number: null,
      email: null,
      bio: null,
      role: 'teacher'
    }
  },
  methods:{
    addOrUpdateUser(){
      var uri = '/api/teachers',
        method = 'POST',
        id = this.$route.params.id,
        contentType = 'multipart/form-data',
        putAsPost = false

      if( id ){
        uri += '/' + id
        putAsPost = true
      }

      var data = { 
        name: this.name,
        email: this.email,
        phone_number: this.phone_number,
        bio: this.bio,
        school_ids: [store.currentSchool],
        role: 'teacher' 
      }

      server( uri, data, method, store.token, contentType, putAsPost )
      .then( ( ) => {
        store.router.push('/')
      })
      .catch(makeErrorMsg)
    }
  }
}
</script>

<template>
  <div>
    <Form 
      cancel="Cancel"
      :caption="$route.params.id? 'Update teacher' : 'New teacher'"
      @form-submitted="addOrUpdateUser"
    >  
      <label>
        Name : 
        <input 
          v-model="name"
          required
          maxlength="255"
          autocomplete="false"
        >
      </label>
      <label>
        Email : 
        <input 
          v-model="email"
          required
          maxlength="255"
          autocomplete="false"
        >
      </label>
      <label>
        Phone : 
        <input 
          v-model="phone_number"
          required
          maxlength="255"
          autocomplete="false"
        >
      </label>
      <label>
        Bio :
        <textarea v-model="bio" />
      </label>
    </Form>
  </div>
</template>