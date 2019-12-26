import { shallowMount } from '@vue/test-utils';
import FileSelect from '../../../resources/js/components/FileSelect';

const { test, expect } = global;

test('is a Vue instance', () => {
  const wrapper = shallowMount(FileSelect, {
    mocks: {
      $t: key => key,
    },
  });

  expect(wrapper.isVueInstance()).toBeTruthy();
});

test('emits event when file is selected', () => {
  const wrapper = shallowMount(FileSelect, {
    mocks: {
      $t: key => key,
    },
  });

  wrapper.vm.handleFileChange({
    target: {
      files: [
        'dummyFileName',
      ],
    },
  });

  expect(wrapper.emitted().input).toEqual([['dummyFileName']]);
});
