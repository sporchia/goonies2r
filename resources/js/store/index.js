import axios from 'axios';
import BPS from '../bps';
import localforage from 'localforage';
import SparkMD5 from 'spark-md5';
import Vuex from 'vuex';

export default new Vuex.Store({
  strict: process.env.NODE_ENV !== 'production',
  state: {
    romFile: null,
    patchedFile: null,
    patchedMeta: {},
    fileLoaded: false,
    filePatched: false,
    loaded: false,
  },
  mutations: {
    clearFile(state) {
      state.romFile = null;
      state.fileLoaded = false;
    },
    loadFile(state, payload) {
      state.romFile = payload.romFile;
      state.fileLoaded = true;
    },
    loadPatchedFile(state, payload) {
      state.patchedFile = payload.patchedFile;
      state.patchedMeta = payload.patchedMeta;
      state.filePatched = true;
    },
    setUnpatched(state) {
      state.filePatched = false;
    },
    setLoaded(state, payload) {
      state.loaded = payload.loaded;
    },
  },
  actions: {
    clearFile({ commit }) {
      commit('clearFile');
    },
    loadFromCache({ commit, dispatch }) {
      return new Promise((resolve, reject) => {
        return localforage.getItem('g2.base_rom').then(buffer => {
          return dispatch('loadFile', new Blob([buffer]));
        }).then(() => {
          commit('setLoaded', {
            loaded: true,
          });
          resolve();
        }).catch(error => {
          commit('setLoaded', {
            loaded: true,
          });
          reject(error);
        });
      });
    },
    loadFile({ commit }, file) {
      return new Promise((resolve, reject) => {
        const fileReader = new FileReader();

        fileReader.onload = function(event) {
          let arrayBuffer = event.target.result;

          if (typeof arrayBuffer === 'undefined') {
            reject(new Error('Could not load file'));
            return;
          }

          if (SparkMD5.ArrayBuffer.hash(arrayBuffer) !== 'd38325cffb9ba2e6f57897c0e9564cc0') {
            reject(new Error('Uploaded file MD5 does not match, perhaps the wrong file?'));
            return;
          }

          commit('loadFile', {
            romFile: arrayBuffer,
          });

          localforage.setItem('g2.base_rom', arrayBuffer);

          resolve(arrayBuffer);
        };

        fileReader.readAsArrayBuffer(file);
      });
    },
    clearRandomized({ commit }) {
      commit('setUnpatched');
    },
    loadFromHash({ commit, state }, hash) {
      return new Promise((resolve, reject) => {
        commit('setUnpatched');

        axios.post(`/hash`, {
          hash: hash,
        }, {
          responseType: 'arraybuffer',
        }).then(patch => {
          const patcher = new BPS(patch.data);
          const patchedFile = patcher.apply(state.romFile);

          commit('loadPatchedFile', {
            patchedFile: patchedFile,
            patchedMeta: patcher.meta,
          });
          resolve(patchedFile);
        }).catch(error => {
          reject(error);
        });
      });
    },
    randomize({ commit, state }) {
      return new Promise((resolve, reject) => {
        commit('setUnpatched');

        axios.post(`/randomize`, [], {
          responseType: 'arraybuffer',
        }).then(patch => {
          const patcher = new BPS(patch.data);
          const patchedFile = patcher.apply(state.romFile);

          commit('loadPatchedFile', {
            patchedFile: patchedFile,
            patchedMeta: patcher.meta,
          });
          resolve(patchedFile);
        }).catch(error => {
          reject(error);
        });
      });
    },
  },
});
