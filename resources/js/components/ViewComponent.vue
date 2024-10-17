<script setup>
import { RouterLink } from 'vue-router';

  defineProps({
    id: {type:String, default: '0'},
    entity: {type:String, default: ''},
    data: {type:Object, required: true, default: new Object}
  })
</script>

<template>
  <div class="taright">
    <RouterLink 
      class="blue no-underline bold" 
      :to="{path: '/edit-' + entity +'/' + id}"
    >
      &#128393;
    </RouterLink>
  </div>
  <slot name="first" />
  <div>
    <slot name="second" />
  </div>
</template>

<style scoped>
.taright{
  text-align: right;
  margin-right: 1.5em;
  margin-top: 1.5em;
}
</style>