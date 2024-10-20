<script setup>
  import Form from './FormComponent.vue';
  import { server, makeErrorMsg  } from '../API.js';

  defineProps({
    caption: {type:String, default:'Request new password'},
    okText: {type:String, default:'Ok'},
  })
</script>

<script>
export default{
  data(){
    return {
      email: '',
      ok: false
    }
  },
  methods:{
    requestPassword(){
      server('/api/forgot-password', {email: this.email}, 'POST', 'application/json')
      .then(this.ok=true)
      .catch(makeErrorMsg)
    }
  }
}
</script>

<template>
  <Form 
    :caption="caption"
    :ok-text="okText"
    @form-submitted="requestPassword"
  >
    <p 
      v-if="ok"
    >
      Password reset email has been sent.
    </p>
    <label>
      Email :
      <input v-model="email">
    </label>
  </Form>
</template>

<style scoped>
#app p{
  color: var(--momo-red);
}
#app form{
    margin: 20vh auto;
}
#app p{
    margin: 0;
}
</style>