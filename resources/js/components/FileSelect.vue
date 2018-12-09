<template>
  <label>
    <div class="select-button">
      <span v-if="value">Selected File: {{ value.name }}</span>
      <span v-else>{{ $t('rom.loader.file_select') }}</span>
    </div>
    <input type="file" @change="handleFileChange">
  </label>
</template>

<script>
export default {
  props: {
    value: File,
  },
  methods: {
    handleFileChange(e) {
      this.$emit('input', e.target.files[0]);
    },
  },
};
</script>

<style scoped>
.select-button {
  padding: 1rem;

  color: white;
  border: 1px solid white;
  border-radius: .3rem;

  text-align: center;
  font-weight: bold;
  font-size: 1rem;
  cursor: pointer
}

input[type="file"] {
  display: none;
}
</style>
