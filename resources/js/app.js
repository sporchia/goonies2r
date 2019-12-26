import * as Sentry from "@sentry/browser";
import * as Integrations from "@sentry/integrations";
import FromHash from "./components/FromHash.vue";
import Randomizer from "./components/Randomizer.vue";
import Streams from "./components/Streams.vue";
import Vue from "vue";
import VueInternationalization from "vue-i18n";
import VueTimeago from "vue-timeago";
import VTooltip from "v-tooltip";

if (process.env.MIX_SENTRY_DSN_PUBLIC) {
  Sentry.init({
    dsn: process.env.MIX_SENTRY_DSN_PUBLIC,
    integrations: [new Integrations.Vue({ Vue })],
  });
}

require("./polyfill");
require("./bootstrap");

window.Vue = Vue;

const store = require("./store").default;
const Locale = require("./vue-i18n-locales.generated").default;

Vue.component("randomizer", Randomizer);
Vue.component("streams", Streams);
Vue.component("from-hash", FromHash);

Vue.use(VueInternationalization);
Vue.use(VueTimeago, {
  locale: "en", // Default locale
});
Vue.use(VTooltip);

const i18n = new VueInternationalization({
  locale: document.documentElement.lang,
  fallbackLocale: "en",
  messages: Locale,
});

// ignore adsense
Vue.config.ignoredElements = ["ins"];

/* eslint-disable no-unused-vars */
const app = new Vue({
  el: "#app",
  i18n: i18n,
  store: store,
});
/* eslint-enable no-unused-vars */
