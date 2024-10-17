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
      password: null,
      password_confirmation: null,
      role: 'teacher'
    }
  },
  mounted(){
    server( '/api/teachers/' + this.$route.params.id, null, 'GET', store.token )
    .then( ( json ) => {
      this.updateData(this, json.data)
    })
    .catch(makeErrorMsg)
  },
  methods:{
    updateData( t, inp ){
      for( var i in inp){
        if(typeof inp[i] == 'object')
          this.updateData(t, inp[i])
        else if( typeof t[i] != 'undefined' && i != 'photo')
          t[i] = inp[i]
      }
    },
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
        password: this.password,
        password_confirmation: this.password_confirmation,
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