<script setup>
import UserPanel from '../components/UserPanel.vue'
import Header from '../components/Header.vue'

</script>

<template>
  <main>
    <div class="container">
      <div class="row">
        <div class="col-12">
          <Header/>
        </div>
        <div class="col-4">
          <user-panel/>
        </div>
      </div>
    </div>
  </main>
</template>
