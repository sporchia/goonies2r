<template>
  <div v-if="storeLoaded" class="container">
    <div class="row justify-content-center text-white">
      <div class="col-md-8">
        <div
          v-if="error"
          class="alert alert-danger alert-dismissible fade show"
          role="alert"
        >
          {{ error }}
          <button
            type="button"
            class="close"
            data-dismiss="alert"
            aria-label="Close"
          >
            <span aria-hidden="true">&times;</span>
          </button>
        </div>

        <div v-if="!fileLoaded" class="card border-info">
          <div class="card-header bg-info">
            <h3 class="card-title">{{ $t("rom.loader.title") }}</h3>
          </div>
          <div class="card-body bg-dark">
            <file-select @input="handleFileLoad"></file-select>
            <p v-html="$t('rom.loader.content')" />
          </div>
        </div>

        <div v-show="loading" class="center">
          <div class="loading" />
          <h1>Loading...</h1>
        </div>

        <div
          v-if="fileLoaded && !filePatched && !loading"
          class="card border-info"
        >
          <div class="card-header bg-info">
            <h3 class="card-title">{{ $t("randomizer.title") }}</h3>
          </div>
          <div class="card-body bg-dark">
            <p v-html="$t('randomizer.content')" />
            <div class="row">
              <div class="col-lg-6 col-md-12 mb-3">
                <Toggle
                  :value="options.shuffleGoonies"
                  @input="setShuffleGoonies"
                >
                  {{ $t("randomizer.options.goonies.title") }}
                  <template v-slot:tooltip>{{
                    $t("randomizer.options.goonies.description")
                  }}</template>
                </Toggle>
              </div>
              <div class="col-lg-6 col-md-12 mb-3">
                <Toggle :value="options.shuffleAnnie" @input="setShuffleAnnie">
                  {{ $t("randomizer.options.annie.title") }}
                  <template v-slot:tooltip>{{
                    $t("randomizer.options.annie.description")
                  }}</template>
                </Toggle>
              </div>
              <div class="col-lg-6 col-md-12 mb-3">
                <Toggle :value="options.shuffleItems" @input="setShuffleItems">
                  {{ $t("randomizer.options.items.title") }}
                  <template v-slot:tooltip>{{
                    $t("randomizer.options.items.description")
                  }}</template>
                </Toggle>
              </div>
            </div>
          </div>
          <div class="card-footer bg-secondary">
            <button
              class="btn btn-success text-center float-right"
              @click="randomizeRom"
            >
              {{ $t("randomizer.generate.title") }}
            </button>
          </div>
        </div>

        <div v-if="fileLoaded && filePatched" class="card border-info">
          <div class="card-header bg-info card-heading-btn">
            <h3 class="card-title float-left">
              {{ $t("randomizer.details.title") }}
            </h3>
            <div class="btn-toolbar float-right">
              <a
                class="btn btn-dark border-secondary"
                role="button"
                @click="clearRandomized"
                >{{ $t("randomizer.generate.back") }}</a
              >
              <a
                class="btn btn-dark border-secondary ml-3"
                role="button"
                v-tooltip="$t('randomizer.generate.regenerate_tooltip')"
                @click="randomizeRom"
                >{{ $t("randomizer.generate.regenerate") }}</a
              >
            </div>
          </div>
          <div class="card-body bg-dark">
            <div class="row">
              <div class="col-md-8">
                <div v-if="gameDetails.version">
                  {{ $t("rom.info.version") }}: {{ gameDetails.version }}
                </div>
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
                  {{ $t("rom.info.generated") }}:
                  <timeago
                    :datetime="gameDetails.created"
                    :auto-update="60"
                    :locale="$i18n.locale"
                  ></timeago>
                </div>
                <div v-if="gameDetails.hash">
                  {{ $t("rom.info.permalink") }}:
                  <a :href="permalink">{{ permalink }}</a>
                </div>
              </div>
              <div class="col-md-4">
                <button class="btn btn-success text-center" @click="saveRom">
                  {{ $t("randomizer.details.save_rom") }}
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { mapActions, mapState } from "vuex";
import FileSaver from "file-saver";
import FileSelect from "./FileSelect.vue";
import Toggle from "./Toggle.vue";

export default {
  components: {
    Toggle,
    FileSelect,
  },
  mounted() {
    this.loadFromCache().catch(error => {
      // we were unable to load base from from localStorage.
      // purposefully ignoring
    });
  },
  data() {
    return {
      loading: false,
      error: null,
    };
  },
  methods: {
    ...mapActions([
      "loadFile",
      "clearFile",
      "loadFromCache",
      "randomize",
      "clearRandomized",
    ]),
    setShuffleGoonies(value) {
      this.$store.dispatch("setOptions", { shuffleGoonies: value });
    },
    setShuffleAnnie(value) {
      this.$store.dispatch("setOptions", { shuffleAnnie: value });
    },
    setShuffleItems(value) {
      this.$store.dispatch("setOptions", { shuffleItems: value });
    },
    handleFileLoad(file) {
      this.error = null;
      this.loadFile(file)
        .then(res => {
          // file loaded
        })
        .catch(error => {
          this.error = error.message;
          this.clearFile();
        });
    },
    randomizeRom() {
      this.error = null;
      this.loading = true;
      this.randomize()
        .catch(error => {
          this.error = error.message;
        })
        .finally(() => {
          this.loading = false;
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
    ...mapState({
      options: state => state.options,
    }),
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
    permalink() {
      return (
        window.location.origin +
        "/" +
        (document.documentElement.lang || "en") +
        "/h/" +
        this.gameDetails.hash
      );
    },
  },
};
</script>
