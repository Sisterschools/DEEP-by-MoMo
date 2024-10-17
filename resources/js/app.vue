<script setup>
import "external-svg-loader";
import { store } from './store.js'
import school from '../svg/school.svg';
import program from '../svg/program.svg'
import project from '../svg/project.svg'
import student from '../svg/student.svg'
import teacher from '../svg/teacher.svg'

import ErrorPopup from './components/ErrorPopupComponent.vue';
import UserMenu from './components/UserMenuComponent.vue';

</script>

<script>
export default{
  methods:{
    logout(){
      store.token = null
      store.router.push("/")
    },
    addNew(){
      store.router.push( store.addNew )
    }
  }
}
</script>

<template>
  <div class="header">
    <user-menu  
      v-if="store.token"
      :user="store.userData ? store.userData : {}" 
      class="user-details"
    >
      <li @click="logout">
        - Logout
      </li>
      <li>
        - Settings
      </li>
    </user-menu>
  </div>
  <div>
    <nav v-if="store.token">
      <RouterLink 
        to="/"
      >
        <svg 
          :data-src="school" 
          class="icon" 
        />
      </RouterLink>
      &nbsp;
      <RouterLink 
        v-if="store.userData.role == 'admin'"
        to="/"
      >
        <svg 
          :data-src="project" 
          class="icon" 
        />
      </RouterLink>
      &nbsp;
      <RouterLink 
        v-if="store.userData.role == 'admin'"
        to="/"
      >
        <svg 
          :data-src="program" 
          class="icon" 
        />
      </RouterLink>
      <div 
        v-if="store.isListComponent && store.hasNew"
        class="white bg-blue"
        @click="addNew"
      >
        &#10010;
      </div>
    </nav>
    <main>
      <RouterView 
        v-slot="{ Component }" 
      >
        <component 
          :is="Component"
        />
      </RouterView>
    </main>
    <ErrorPopup />
  </div>
</template>