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
          <div class="card-header bg-info"><h3 class="card-title">{{ $t('rom.loader.title') }}</h3></div>
          <div class="card-body bg-dark">
            <file-select @input="handleFileLoad"></file-select>
            <p v-html="$t('rom.loader.content')" />
          </div>
        </div>

        <div v-if="fileLoaded && !filePatched" class="card border-info">
          <div class="card-header bg-info"><h3 class="card-title">{{ $t('randomizer.title') }}</h3></div>
          <div class="card-body bg-dark">
            <p v-html="$t('randomizer.content')" />
            <button class="btn btn-success text-center" @click="randomizeRom">{{ $t('randomizer.generate.title') }}</button>
          </div>
        </div>

        <div v-if="fileLoaded && filePatched" class="card border-info">
          <div class="card-header bg-info card-heading-btn">
            <h3 class="card-title float-left">{{ $t('randomizer.details.title') }}</h3>
            <div class="btn-toolbar float-right">
              <a class="btn btn-dark border-secondary" role="button" @click="clearRandomized">
                {{ $t('randomizer.generate.back') }}
              </a>
              <a class="btn btn-dark border-secondary ml-3" role="button"
                v-tooltip="$t('randomizer.generate.regenerate_tooltip')" @click="randomizeRom">
                {{ $t('randomizer.generate.regenerate') }}
              </a>
            </div>
          </div>
          <div class="card-body bg-dark">
            <div class="row">
              <div class="col-md-8">
                <div v-if="gameDetails.version">{{ $t('rom.info.version') }}: {{ gameDetails.version }}</div>
                <div v-if="gameDetails.created">
                  {{ $t('rom.info.generated') }}: <timeago :datetime="gameDetails.created" :auto-update="60" :locale="$i18n.locale"></timeago>
                </div>
                <div v-if="gameDetails.hash">{{ $t('rom.info.permalink') }}: <a :href="permalink">{{ permalink }}</a></div>
              </div>
              <div class="col-md-4">
                <button class="btn btn-success text-center" @click="saveRom">{{ $t('randomizer.details.save_rom') }}</button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import FileSelect from './FileSelect.vue';
import { mapActions } from 'vuex';
import FileSaver from 'file-saver';

export default {
  components: {
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
      error: null,
    };
  },
  methods: {
    ...mapActions([
      'loadFile',
      'clearFile',
      'loadFromCache',
      'randomize',
      'clearRandomized',
    ]),
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
      this.randomize().catch(error => {
        this.error = error.message;
      });
    },
    saveRom() {
      FileSaver.saveAs(new Blob([this.$store.state.patchedFile]), 'G2r-' + this.gameDetails.hash + '.nes');
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
    permalink() {
      return window.location.origin + '/' + (document.documentElement.lang || 'en') + '/h/' + this.gameDetails.hash;
    },
  },
};
</script>
