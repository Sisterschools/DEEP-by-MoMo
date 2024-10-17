<script setup>
  import { server, makeErrorMsg } from '../API.js';
  import { store } from '../store.js';
  import { dateFormat } from '../dateFormat.js';

  import View from './ViewComponent.vue';
  import List from './ListComponent.vue';
</script>

<script>
export default{
  data(){
    return{
      data: [],
      teachers: [],
      students: []
    }
  },
  beforeMount(){
    this.getSchool()
    this.getTeachers()
    this.getStudents()
  },
  methods:{
    getSchool(){
      server('/api/schools/' + this.$route.params.id, [], 'GET', store.token )
      .then( ( response ) => {
        this.data = response.data
        store.currentSchool = this.$route.params.id
      })
      .catch(makeErrorMsg)
    },
    getTeachers(){
      server('/api/schools/' + this.$route.params.id + '/teachers', [], 'GET', store.token )
      .then( (response) => this.teachers = response.data)
      .catch(makeErrorMsg)
    },
    getStudents(){
      server('/api/schools/' + this.$route.params.id + '/students', [], 'GET', store.token )
      .then( (response) => this.students = response.data)
      .catch(makeErrorMsg)
    },
    editStudent( id ){
      store.router.push('/edit-student/' + id)
    },
    editTeacher( id ){
      store.router.push('/edit-teacher/' + id)
    },
    deleteTeacher( value ){
      return server('/api/teachers/' + value, [], 'DELETE', store.token)
    },
    deleteStudent( value ){
      return server('/api/students/' + value, [], 'DELETE', store.token)
    },
    deleteTeachers(ids){
      new Promise((resolve) => {
        ids.reduce( (previous, value) => previous.then(this.deleteTeacher(value).catch(makeErrorMsg)), Promise.resolve())
        .then(resolve)
      })
      .catch(console.log)
      .then(() => {
        this.getTeachers()
      })
    },
    deleteStudents(ids){
      new Promise((resolve) => {
        ids.reduce( (previous, value) => previous.then(this.deleteStudent(value).catch(makeErrorMsg)), Promise.resolve())
        .then(resolve)
      })
      .catch(console.log)
      .then(() => {
        this.getStudents()
      })
    }
  }
}
</script>

<template>
  <View 
    :id="$route.params.id" 
    entity="school"
    :data="data"
  >
    <template #first>
      <div>
        <img 
          alt="logo"
          :src="data['photo'] ? data['photo'] : 'img/icon2024original.png'"
        >
        <div class="view">
          <div 
            v-for="(d, index) in data"
            :key="d"
          >
            <div 
              v-if="index == 'user'"
            >
              <span>{{ index.replaceAll('_', ' ') }}&nbsp;:&nbsp;</span>
              <span>{{ data[index].name }}, {{ data[index].email }} </span>
            </div>
            <div v-else-if="index == 'created_at' || index == 'updated_at'">
              <span>{{ index.replaceAll('_', ' ') }}&nbsp;:&nbsp;</span>
              <span>{{ dateFormat(d, { year: 'numeric', month: 'short', day:'numeric', }) }}</span>
            </div>       
            <div v-else-if="index != 'photo'">
              <span>{{ index.replaceAll('_', ' ') }}&nbsp;:&nbsp;</span>
              <span>{{ d }} </span>
            </div> 
          </div>
        </div>
      </div>
    </template>
    <template #second>
      <div>
        <List 
          entity="project" 
          caption="Project"
          :columns="{}"
          :items="[]"
        />
      </div>
      <div class="cols">
        <List 
          v-if="store.userData.role == 'school' || store.userData.role == 'admin'"
          entity="teacher" 
          caption="Teachers"
          :selectable-rows="true"
          :shift-click="true"
          :show-header="false"
          :columns="{id: {type:'id', visible:false},photo: {type:'img'}, name: {}, user:{ as:'login', subItem:{user: 'user.email'}}}"
          :items="teachers"
          :caption-is-foldable="true"
          @rowclick="editTeacher"
          @delete="deleteTeachers"
        />
        <List 
          v-if="store.userData.role == 'school' || store.userData.role == 'admin'"
          entity="student" 
          caption="Students"
          :selectable-rows="true"
          :shift-click="true"
          :show-header="false"
          :columns="{id: {type:'id', visible:false},photo: {type:'img'}, name: {}, user:{ as:'login', subItem:{user: 'user.email'}}}"
          :items="students"
          :caption-is-foldable="true"
          @rowclick="editStudent"
          @delete="deleteStudents"
        />
      </div>
    </template>
  </View>
</template>

<style scoped>
img{
  margin: 0 4em;
  float: left;
  width: 12em;

}
div > span:nth-child(1){
  display: inline-block;
  width: 33.33%;
  max-width: 12em;
  font-weight: bolder;
}
div > span:nth-child(2){
  display: inline-block;
  width: 66.667%;
}
.view > div{
  width: 50%;
  float: left;
}
</style>