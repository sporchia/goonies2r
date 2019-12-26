const { test, expect, document } = global;
document.head.innerHTML = '<meta name="csrf-token" content="token">';

require('../../resources/js/bootstrap');

test('Popper is loaded', () => {
  expect(window).toHaveProperty('Popper');
});

test('jQuery is loaded', () => {
  expect(window).toHaveProperty('$');
});

test('Axios is loaded', () => {
  expect(window).toHaveProperty('axios');
});
