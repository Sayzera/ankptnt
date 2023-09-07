
<script>
import { ref } from 'vue'
import observationItem from "./ObservationItem.vue";



export default {
  props:[
      'greetingMessage'
  ],
  setup() {
    const count = ref(0)
    return {
      count
    }
  },

  methods: {
    sayHello() {
      alert(1)
    },

  },

  components: {
    'observation-item': observationItem
  },

  // get emit value
  mounted() {

  }



}
</script>

<template>
  <observation-item></observation-item>
</template>

