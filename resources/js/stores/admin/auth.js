import axios from 'axios'
import { 
  SET_AUTHENTICATED, 
  SET_USER, 
  SET_ERROR 
} from './mutation-types';
import {
  API_AUTH_SANCTUM_CSRF_COOKIE,
  API_AUTH_LOGIN,
  API_AUTH_LOGOUT,
  API_AUTH_USER
} from './api-paths';

const options = {
    init: 'init',
    login: 'login',
    logout: 'logout'
  };

export default {
    namespaced: true,
    state: {
      authenticated: false,
      user: null,
      redirectUrl: 'admin/users',
      redirectLogoutUrl: 'admin/login',
      errors:[]
    },
    getters: {
      authenticated(state) {
        return state.authenticated
      },
      user(state) {
        return state.user
      },
      errors(state) {
        return state.errors
      },
      isError(state) {
        return state.errors.length
      }
    },

    mutations: {
        [SET_AUTHENTICATED](state, value) {
            state.authenticated = value
        },

        [SET_USER](state, value) {
            state.user = value
        },

        [SET_ERROR](state, value) {
          state.errors = value
        }
    },

    actions: {
        async signIn ({ dispatch }, credentials) {
            await axios.get(API_AUTH_SANCTUM_CSRF_COOKIE);
            await axios.post(API_AUTH_LOGIN, credentials);

            return dispatch('admin', {type: options.login})
        },

        async signOut ({ dispatch }) {
          await axios.post(API_AUTH_LOGOUT)

          return dispatch('admin', {type:options.logout});
        },

        admin ({ commit }, options) {
            return axios.get(API_AUTH_USER).then((response) => {
                commit(SET_AUTHENTICATED, true);
                commit(SET_USER, response.data);
                commit(SET_ERROR, []);
            }).catch((error) => {
                commit(SET_AUTHENTICATED, false);
                commit(SET_USER, null);
                if (typeof options === 'object') {
                  const type = options.hasOwnProperty('type');
                  type ? ((options.type==='login') ? commit(SET_ERROR,
                  [
                    {msgCommon: 'Login failed!'}
                  ]):null): null;
                }
            })
        },

        redirectLoginSuccess({state}) {
            window.location = window.location.origin + '/' + state.redirectUrl;
        },

        redirectLogoutSuccess({state}) {
            window.location = window.location.origin + '/' + state.redirectLogoutUrl;
        }
    }
}
