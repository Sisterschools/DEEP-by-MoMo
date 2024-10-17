import { createApp } from 'vue';
import { store } from './store.js'
import App from './app.vue'
import Layout from './components/LayoutComponent.vue';
import ListSchools from './components/ListSchoolsComponent.vue';
import ViewSchool from './components/ViewSchoolComponent.vue';
import EditSchool from './components/EditSchoolComponent.vue';
import ViewTeacher from './components/ViewTeacherComponent.vue';
import EditTeacher from './components/EditTeacherComponent.vue';
import AddTeacher from './components/AddTeacherComponent.vue';
import ViewStudent from './components/ViewStudentComponent.vue';
import EditStudent from './components/EditStudentComponent.vue';
import AddStudent from './components/AddStudentComponent.vue';
import { createWebHashHistory, createRouter } from 'vue-router';

import ResetPassword from './components/ResetPasswordComponent.vue';
import RequestPasswordReset from './components/RequestResetPasswordComponent.vue';

const routes = [
    { path: '/', name:'home', component: Layout },
    { path: '/forgot-password', name:'forgotpassword', component: RequestPasswordReset},
    { path: '/reset-password', name:'resetpassword', component: ResetPassword },
    { path: '/list-schools', name:'listschools', component: ListSchools },
    { path: '/view-school/:id', name:'viewschool', component: ViewSchool },
    { path: '/edit-school/:id', name:'editschool', component: EditSchool},
    { path: '/school/add', name:'addschool', component: EditSchool },
    { path: '/view-teacher/:id', name:'viewteacher', component: ViewTeacher },
    { path: '/edit-teacher/:id', name:'editteacher', component: EditTeacher},
    { path: '/teacher/add', name:'addteacher', component: AddTeacher },
    { path: '/view-student/:id', name:'viewstudent', component: ViewStudent },
    { path: '/edit-student/:id', name:'editstudent', component: EditStudent},
    { path: '/student/add', name:'addstudent', component: AddStudent },
  ]  

const router = createRouter({
  history: createWebHashHistory(),
  routes,
})

var allow = ['resetpassword', 'forgotpassword']

router.beforeEach(async (to) => {
  if (
    !store.token &&
    to.name !== 'home' &&
    allow.indexOf(to.name) == -1
  ) {
    if(allow.indexOf(to.name) == -1)
      return { name: 'home' }
  }
})

store.router = router;

createApp(App)
  // Register the v-focus directive
  .directive('focus', {
    mounted(el) {
      el.focus()
    }
  })
  .use(router)
  .mount('#app')