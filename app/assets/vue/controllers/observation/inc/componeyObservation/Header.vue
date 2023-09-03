<script>
import { ref } from 'vue'

export default {
  setup() {
    const count = ref(0)

    // expose to template and other options API hooks
    return {
      count
    }
  },

  methods: {
    increment() {
      this.count++
    }
  },

  mounted() {
    console.log(this.count) //
  },

}
</script>


<template>
  <div class="project-page d-flex justify-content-between align-items-center flex-wrap">
    <div class="project mb-4">
      <ul class="nav nav-tabs" role="tablist">
        <li class="nav-item">
          <a class="nav-link active" data-bs-toggle="tab" href="#AllStatus" role="tab">All Status
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link" data-bs-toggle="tab" href="#OnProgress" role="tab">On Progress</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" data-bs-toggle="tab" href="#Pending" role="tab">Pending</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" data-bs-toggle="tab" href="#Closed" role="tab">Closed</a>
        </li>
      </ul>
    </div>
    <div class="mb-4">
      <a href="javascript:void(0);" class="btn btn-primary btn-rounded fs-18">+ New Project</a>

    </div>
  </div>

</template>