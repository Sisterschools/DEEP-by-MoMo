<script setup>
  import ListSchoolsComponent from './ListSchoolsComponent.vue'
  import ViewSchoolComponent from './ViewSchoolComponent.vue'
  import ViewProgram from './ViewProgramme.vue'
  
  defineProps({
    userData: { type:{}, required: true, default: null}
  })
</script>

<script>
  export default{
    computed: {
      whichComponent(){
        if(this.userData.role == 'admin')
          return ListSchoolsComponent;
        else if( this.userData.role == 'school')
          return ViewSchoolComponent
        else
          return ViewProgram
      }
    },
  }
</script>

<template>
  <div>
    <component 
      :is="whichComponent" 
      :user-data="userData" 
    />
  </div>
</template>