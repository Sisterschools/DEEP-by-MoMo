<script setup>
import { server, makeErrorMsg } from '../API.js'
import  { store } from '../store.js'
import Form from './FormComponent.vue'
</script>

<script>
export default{
  data(){
    return{
      title: '',
      country: '',
      language: '',
      name: '',
      email: '',
      address: '',
      description: '',
      phone_number: '',
      website: '',
      founding_year: '',
      student_capacity: '',
      photo: null
    }
  },
  mounted(){
    if( this.$route.params.id ){
      server( '/api/schools/' + this.$route.params.id, [], 'GET', store.token )
      .then( ( json ) => {
        this.updateData(this, json.data)
      })
      .catch(makeErrorMsg)
    }
  },
  methods:{
    getPhoto( event ){
      if( event.target && event.target.files )
        this.photo = event.target.files[0]
    },
    updateData( t, inp ){
      for( var i in inp){
        if(typeof inp[i] == 'object')
          this.updateData(t, inp[i])
        else if( typeof t[i] != 'undefined' && i != 'photo')
          t[i] = inp[i]
      }
    },
    addOrUpdateSchool(){
      var uri = '/api/schools',
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
        title: this.title,
        country: this.country,
        language: this.language,
        email: this.email,
        address: this.address,
        description: this.description,
        phone_number: this.phone_number,
        website: this.website,
        founding_year: this.founding_year,
        student_capacity: this.student_capacity,
        role: 'school' 
      }

      if( this.photo )
        data = Object.assign(data, {photo: this.photo})

      server( uri, data, method, store.token, contentType, putAsPost )
      .then( ( ) => {
        store.router.push('/')
      })
      .catch(makeErrorMsg)
    },
  }
}
</script>

<template>
  <div>
    <Form 
      cancel="Cancel"
      :caption="$route.params.id? 'Update school' : 'New school'"
      @form-submitted="addOrUpdateSchool"
    >
      <label>
        Name of the school : 
        <input 
          v-model="title"
          required
          maxlength="255"
          autocomplete="false"
        >
      </label>
      <label>
        Photo :
        <input 
          type="file" 
          @change="getPhoto"
        >
      </label>
      <label v-if="! $route.params.id">
        Name of contact person : 
        <input 
          v-model="name"
          required
          autocomplete="false"
        >
      </label>
      <label>
        Email to log in : 
        <input 
          v-model="email"
          required
          type="email"
          autocomplete="email"
        >
      </label>
      <label>
        Address : 
        <textarea 
          v-model="address"
          autocomplete="false"
        />
      </label>
      <label>
        Country :
        <input
          v-model="country"
        >
      </label>
      <label>
        Language code :
        <input
          v-model="language"
          maxlength="3"
        >
      </label>
      <label>
        Description : 
        <input 
          v-model="description"
          autocomplete="false"
        >
      </label>
      <label>
        Phone : 
        <input 
          v-model="phone_number"
          type="tel"
          maxlength="20"
          autocomplete="false"
        >
      </label>
      <label>
        Website : 
        <input 
          v-model="website"
          type="text"
          autocomplete="false"
        >
      </label>
      <label>
        Founding year : 
        <input 
          v-model="founding_year"
          min="1800"
          max="2024"
          autocomplete="false"
        >
      </label>
      <label>
        Student capacity : 
        <input 
          v-model="student_capacity"
          autocomplete="false"
        >
      </label>
    </Form>
  </div>
</template>