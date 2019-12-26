<template>
  <div :class="{ disabled: disabled }">
    <toggle-button
      ref="toggle"
      :value="value"
      @input="onInput"
      :sync="true"
      :width="70"
      :height="35"
      :labels="{ checked: 'Yes', unchecked: 'No' }"
      :disabled="disabled"
      color="blue"
    ></toggle-button>
    <label @click="onClickLabel">
      <slot></slot>
    </label>
    <div class="clearfix" />
    <div class="information">
      <slot name="tooltip"></slot>
    </div>
  </div>
</template>

<script>
import { ToggleButton } from "vue-js-toggle-button";

export default {
  components: {
    ToggleButton,
  },
  props: {
    value: { default: false },
    disabled: { default: false },
  },
  methods: {
    onInput(input) {
      this.$emit("input", input);
    },
    onClickLabel() {
      if (!this.disabled) {
        this.$emit("input", !this.value);
      }
    },
  },
};
</script>

<style scoped>
.vue-js-switch {
  margin: 2px;
}
.vue-js-switch {
  font-size: 16px !important;
}
.disabled {
  color: #bcc3c9;
}
.information {
  margin-left: 80px;
  font-style: italic;
}
</style>
