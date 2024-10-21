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
      role: 'teacher',
    }
  },
  mounted(){
    server( '/api/students/' + this.$route.params.id, null, 'GET', store.token )
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
      var uri = '/api/students',
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
        // bio: this.bio,
        school_ids: [store.currentSchool],
        role: 'student' 
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
      :caption="$route.params.id? 'Update student' : 'New Student'"
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
    </Form>
  </div>
</template>