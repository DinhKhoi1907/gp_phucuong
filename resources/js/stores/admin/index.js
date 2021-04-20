import Vue from 'vue';
import Vuex from 'vuex';

import auth from './auth';
import layout from './layout';
import user from './users';
import info from './infos';
import news_category from './categories';
import createLogger from '../../plugins/logger';

Vue.use(Vuex);

const debug = process.env.NODE_ENV === 'debuger';

const fnIsObject = (obj) => {
  if (typeof obj !== "undefined" 
    && typeof obj === "object" 
    && Object.keys(obj).length) {
    return true;
  }

  return false;
}

const defaultState = () => {
  return {
    errors: [],
    links: {},
    meta: {},
    perPage: 15,
    moduleActive: {
      name: '',
      actionList: ''
    },
    logo: '/front/img/logo.png',
    collectionData: {
      current_page: 1,
      from: 0,
      to:0,
      total: 0
    }
  }
}

export default new Vuex.Store({
  state: {
    cfApp: defaultState()
  },
  getters: {
    resourcePaginationData(state) {
      return {
        links: state.cfApp.links,
        meta: state.cfApp.meta
      }
    },
    collectionPaginationData(state) {
      const colData = {
        current_page: 1,
        from: 0,
        to:0,
        total: 0
      };
      if (fnIsObject(state.cfApp.collectionData)) {
        return state.cfApp.collectionData;
      }

      return colData;
    },
    isNotEmptyList(state) {
      if (fnIsObject(state.cfApp.meta) 
        && state.cfApp.meta.hasOwnProperty('total')) {
        return (parseInt(state.cfApp.meta.total) > 0);
      }

      return false;
    },
    moduleNameActive(state) {
      let mName = '';
      if (fnIsObject(state.cfApp.moduleActive) 
        && state.cfApp.moduleActive.hasOwnProperty('name')) {
          mName = state.cfApp.moduleActive.name;
      }

      return mName;
    },
    moduleActionListActive(state) {
      let mAction = '';
      if (fnIsObject(state.cfApp.moduleActive)
        && state.cfApp.moduleActive.hasOwnProperty('actionList')) {
          mAction = state.cfApp.moduleActive.actionList;
      }

      return mAction;
    }
  },
  mutations: {
    configApp(state, configs) {
      if (fnIsObject(configs.links)) {
        state.cfApp.links = configs.links;
      }
      if (fnIsObject(configs.meta)) {
        state.cfApp.meta = configs.meta;
      }
      if (fnIsObject(configs.moduleActive))
      {
        state.cfApp.moduleActive = configs.moduleActive;
      }
      if (fnIsObject(configs.collectionData))
      {
        state.cfApp.collectionData = configs.collectionData;
      }
    }
  },
  actions: {
    setConfigApp({
      commit
    }, configs) {
      const links = (configs.hasOwnProperty('links')) ? configs.links: undefined;
      const meta = (configs.hasOwnProperty('meta')) ? configs.meta: undefined;
      const moduleActive = (configs.hasOwnProperty('moduleActive')) ? configs.moduleActive: undefined;
      const collectionData = (configs.hasOwnProperty('collectionData')) ? configs.collectionData: undefined;

      var _configs = {
        ...defaultState(),
        ...{
          links: links,
          meta: meta,
          moduleActive: moduleActive,
          collectionData: collectionData
        }
      };

      commit('configApp', _configs);
    }
  },
  modules: {
    auth,
    layout,
    user,
    info,
    news_category
  },
  strict: debug,
  plugins: debug ? [createLogger()] : []
});