<script setup>
  import { server, makeErrorMsg } from '../API.js'
  import { store } from '../store.js'
  import View from './ViewComponent.vue';
  import List from './ListComponent.vue';
</script>

<script>
export default{
  mounted(){
    this.getSchool()
  },
  methods:{
    getSchool(){
      server('/api/teachers/' + this.$route.params.id, [], 'GET', store.token )
      .then( ( response ) => {
        console.log( response )
      })
      .catch(makeErrorMsg)
    }
  }
}
</script>

<template>
  <View 
    :id="$route.params.id" 
    entity="teacher"
  >
    <div>
      <div 
        v-for="(d, index) in data"
        :key="d"
      >
        {{ d }} 
        {{ index }} 
      </div>
    </div>
    <div class="cols">
      <List entity="student" />
    </div>
  </View>
</template>