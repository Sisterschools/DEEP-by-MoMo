<script setup>
  import { store } from '../store.js'
  import ListComponent from './ListComponent.vue'
  import { server, makeErrorMsg } from '../API.js'

</script>

<script>
export default{
  data(){
    return {
      items: [],
      selectableRows: store.userData.role == 'admin'
    }
  },  
  mounted(){
    store.hasNew = store.userData.role == 'admin'
    store.addNew = '/school/add'
    server( '/api/schools', null, 'GET', store.token )
    .then( ( json ) => {
      this.items = json.data
    } )
    .catch(makeErrorMsg)
  },
  unmounted(){
    store.addNew = false
    store.hasNew = false
  },
  methods:{
    selectSchool( id ){
      store.router.push({name: 'viewschool', params: {id}})
    },
    deleteSchool( value ){
      if(prompt('Delete ' + value + '? Type the number :') == value)
        return server('/api/schools/' + value, [], 'DELETE', store.token)
      else
        return Promise.reject()
    },
    deleteSchools(ids){
      new Promise((resolve) => {
        ids.reduce( (previous, value) => previous.then(this.deleteSchool(value).catch(makeErrorMsg)), Promise.resolve())
        .then(resolve)
      })
      .catch(console.log)
      .then(() => {
        store.router.push({name: 'listschools'})
      })
    }
  }
}
</script>

<template>
  <ListComponent 
    :items="items"
    entity="school"
    :select-columns="true"
    :columns="{
      id: {type:'id', visible:false},
      photo:{ type:'img', as: 'logo'},
      title:{type:'string', as:'name'}, 
      website: {type:'string'}, 
      language: {type:'string'},
      country: {type:'string'},
      phone_number:{ as:'phone'}, 
      founding_year:{ as:'founded'}, 
      student_capacity:{ as:'# students'}, 
      user:{ as:'admin', subItem:{user: 'user.email'}}, 
      address:{ }, 
      description:{ },
      created_at:{ type:'date', visible:false}, 
      updated_at:{ type:'date', visible:false}, 
    }"
    :selectable-rows="selectableRows"
    :shift-click="true"
    caption="Schools"
    @rowclick="selectSchool"
    @delete="deleteSchools"
  />
</template>