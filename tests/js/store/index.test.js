import store from "../../../resources/js/store";

const { test, expect } = global;

test("setOptions changes the internal options", () => {
  store.commit("setOptions", {
    shuffleItems: true,
  });

  expect(store.state.options.shuffleItems).toBe(true);
});

test("clearFile clears romFile", () => {
  store.commit("clearFile");

  expect(store.state.romFile).toBe(null);
});

test("clearFile clears fileLoaded", () => {
  store.commit("clearFile");

  expect(store.state.fileLoaded).toBe(false);
});
