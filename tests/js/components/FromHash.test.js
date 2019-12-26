import { shallowMount, createLocalVue } from '@vue/test-utils';
import Vuex from 'vuex';
import FileSaver from 'file-saver';
import FromHash from '../../../resources/js/components/FromHash';

const { test, expect } = global;
const localVue = createLocalVue();
localVue.use(Vuex);

describe('FromHash.vue', () => {
  let actions;
  let store;
  let mocks;
  let state;

  beforeEach(() => {
    actions = {
      loadFile: jest.fn(),
      clearFile: jest.fn(),
      loadFromHash: jest.fn(),
      loadFromCache: jest.fn(),
    };

    state = {
      romFile: null,
      patchedFile: null,
      patchedMeta: {},
      fileLoaded: false,
      filePatched: false,
      loaded: false,
    };

    mocks = {
      $t: key => key,
    };

    store = new Vuex.Store({
      actions,
      state,
    });
  });

  test('is a Vue instance', () => {
    const wrapper = shallowMount(FromHash, {
      propsData: {
        hash: 'foo',
      },
      mocks,
      store,
      localVue,
    });

    expect(wrapper.isVueInstance()).toBeTruthy();
  });

  test('discards load from cache miss', () => {
    actions.loadFromCache.mockRejectedValue(new Error('test error'));

    const wrapper = shallowMount(FromHash, {
      propsData: {
        hash: 'foo',
      },
      mocks,
      store,
      localVue,
    });

    expect(wrapper.isVueInstance()).toBeTruthy();
  });

  test('clears the file if it cannot load', () => {
    actions.loadFile.mockRejectedValue(new Error('test error'));

    const wrapper = shallowMount(FromHash, {
      propsData: {
        hash: 'foo',
      },
      mocks,
      store,
      localVue,
    });

    wrapper.vm.handleFileLoad('dummy').then(() => {
      expect(actions.loadFile).toHaveBeenCalled();
      expect(wrapper.vm.error).toBe('test error');
      expect(actions.clearFile).toHaveBeenCalled();
    });
  });

  test('loads from hash', () => {
    actions.loadFile.mockResolvedValue('ok');

    const wrapper = shallowMount(FromHash, {
      propsData: {
        hash: 'foo',
      },
      mocks,
      store,
      localVue,
    });

    wrapper.vm.handleFileLoad('dummy').then(() => {
      expect(actions.loadFile).toHaveBeenCalled();
      expect(actions.loadFromHash).toHaveBeenCalled();
    });
  });

  test('fileLoaded false in initial state', () => {
    const wrapper = shallowMount(FromHash, {
      propsData: {
        hash: 'foo',
      },
      mocks,
      store,
      localVue,
    });

    expect(wrapper.vm.fileLoaded).toBe(false);
  });

  test('filePatched false in initial state', () => {
    const wrapper = shallowMount(FromHash, {
      propsData: {
        hash: 'foo',
      },
      mocks,
      store,
      localVue,
    });

    expect(wrapper.vm.filePatched).toBe(false);
  });

  test('storeLoaded false in initial state', () => {
    const wrapper = shallowMount(FromHash, {
      propsData: {
        hash: 'foo',
      },
      mocks,
      store,
      localVue,
    });

    expect(wrapper.vm.storeLoaded).toBe(false);
  });

  test('gameDetails empty in initial state', () => {
    const wrapper = shallowMount(FromHash, {
      propsData: {
        hash: 'foo',
      },
      mocks,
      store,
      localVue,
    });

    expect(wrapper.vm.gameDetails).toEqual({});
  });

  test('saveRom triggers file save', () => {
    const spy = spyOn(FileSaver, 'saveAs').and.stub();

    const wrapper = shallowMount(FromHash, {
      propsData: {
        hash: 'foo',
      },
      mocks,
      store,
      localVue,
    });

    wrapper.vm.saveRom();

    expect(spy).toHaveBeenCalled();
  });
});
