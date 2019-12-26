import * as Sentry from '@sentry/browser';

const { test, expect, document } = global;
document.head.innerHTML = '<meta name="csrf-token" content="token">';
document.body.innerHTML = '<div id="app"></div>';

console.error = jest.fn(error => {
});

jest.mock('@sentry/browser', () => {
  return {
    init: jest.fn(() => false),
    Integrations: {
      Vue: jest.fn(() => false),
    },
  };
});
process.env.MIX_SENTRY_DSN_PUBLIC = 'testing';

require('../../resources/js/app');

test('Vue is loaded', () => {
  expect(window).toHaveProperty('Vue');
});

test('Sentry mounts', () => {
  expect(Sentry.init).toHaveBeenCalled()
});
