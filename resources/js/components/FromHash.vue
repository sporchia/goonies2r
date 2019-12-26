<template>
  <div v-if="storeLoaded" class="container">
    <div class="row justify-content-center text-white">
      <div class="col-md-8">
        <div v-if="error" class="alert alert-danger alert-dismissible fade show" role="alert">
          {{ error }}
          <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>

        <div v-if="!fileLoaded" class="card border-info">
          <div class="card-header bg-info">
            <h3 class="card-title">{{ $t('rom.loader.title') }}</h3>
          </div>
          <div class="card-body bg-dark">
            <file-select @input="handleFileLoad"></file-select>
            <p v-html="$t('rom.loader.content')" />
          </div>
        </div>

        <div v-if="fileLoaded && filePatched" class="card border-info">
          <div class="card-header bg-info">
            <h3 class="card-title">{{ $t('randomizer.details.title') }}: {{ hash }}</h3>
          </div>
          <div class="card-body bg-dark">
            <div class="row">
              <div class="col-md-8">
                <div
                  v-if="gameDetails.version"
                >{{ $t('rom.info.version') }}: {{ gameDetails.version }}</div>
                <template v-if="gameDetails.meta">
                  <div>
                    {{ $t("randomizer.options.goonies.title") }}:
                    {{ gameDetails.meta.shuffleGoonies }}
                  </div>
                  <div>
                    {{ $t("randomizer.options.items.title") }}:
                    {{ gameDetails.meta.shuffleItems }}
                  </div>
                  <div>
                    {{ $t("randomizer.options.annie.title") }}:
                    {{ gameDetails.meta.shuffleAnnie }}
                  </div>
                </template>
                <div v-if="gameDetails.created">
                  {{ $t('rom.info.generated') }}:
                  <timeago :datetime="gameDetails.created" :auto-update="60" :locale="$i18n.locale"></timeago>
                </div>
              </div>
              <div class="col-md-4">
                <button
                  class="btn btn-success text-center"
                  @click="saveRom"
                >{{ $t('randomizer.details.save_rom') }}</button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import FileSelect from "./FileSelect.vue";
import { mapActions } from "vuex";
import FileSaver from "file-saver";

export default {
  components: {
    FileSelect,
  },
  props: ["hash"],
  mounted() {
    this.loadFromCache()
      .then(() => {
        this.loadFromHash(this.hash);
      })
      .catch(error => {
        // we were unable to load base from from localStorage.
        // purposefully ignoring
      });
  },
  data() {
    return {
      error: null,
    };
  },
  methods: {
    ...mapActions(["loadFile", "clearFile", "loadFromCache", "loadFromHash"]),
    handleFileLoad(file) {
      this.error = null;
      return this.loadFile(file)
        .then(res => {
          this.loadFromHash(this.hash);
        })
        .catch(error => {
          this.error = error.message;
          this.clearFile();
        });
    },
    saveRom() {
      FileSaver.saveAs(
        new Blob([this.$store.state.patchedFile]),
        "G2r-" + this.gameDetails.hash + ".nes"
      );
    },
  },
  computed: {
    fileLoaded() {
      return this.$store.state.fileLoaded;
    },
    filePatched() {
      return this.$store.state.filePatched;
    },
    gameDetails() {
      return this.$store.state.patchedMeta;
    },
    storeLoaded() {
      return this.$store.state.loaded;
    },
  },
};
</script>
