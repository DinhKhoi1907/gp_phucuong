import axios from 'axios';
import AppConfig from 'api@admin/constants/app-config';
import {
  apiGetUserById,
  apiUpdateUser,
  apiInsertUser
} from 'api@admin/user';
import {
  USERS_MODAL_SET_OPEN_MODAL,
  USERS_MODAL_SET_CLOSE_MODAL,
  USERS_MODAL_SET_IS_OPEN_MODAL,
  USERS_MODAL_SET_LOADING,
  USERS_MODAL_INSERT_USER_SUCCESS,
  USERS_MODAL_UPDATE_USER_SUCCESS,
  USERS_MODAL_INSERT_USER_FAILED,
  USERS_MODAL_SET_ERROR
} from '../types/mutation-types';
import {
  ACTION_SET_LOADING,
  ACTION_SHOW_MODAL,
  ACTION_CLOSE_MODAL,
  ACTION_IS_OPEN_MODAL,
  ACTION_INSERT_USER,
  ACTION_RELOAD_GET_USER_LIST,
  ACTION_RESET_NOTIFICATION_INFO
} from '../types/action-types';

const INIT_STATE = {
  isOpen: false,
  action: null,
  classShow: 'modal fade',
  styleCss: '',
  user: null,
  userId: 0,
  loading: false,
  insertSuccess: false,
  errors: []
}

export default {
  namespaced: true,
  state: Object.assign({}, INIT_STATE),
  getters: {
    isOpen(state) {
      return state.isOpen
    },
    classShow(state) {
      return state.classShow
    },
    styleCss(state) {
      return state.styleCss
    },
    user(state) {
      return state.user
    },
    loading(state) {
      return state.loading
    },
    insertSuccess(state) {
      return state.insertSuccess
    },
    errors(state) {
      return state.errors
    },
    isError(state) {
      return state.errors.length
    }
  },

  mutations: {
    [USERS_MODAL_SET_OPEN_MODAL](state, payload) {
      state.action = payload;
      state.classShow = 'modal fade show';
      state.styleCss = 'display:block';
      state.insertSuccess = false;
    },

    [USERS_MODAL_SET_CLOSE_MODAL](state) {
      state.action = 'closeModal';
      state.classShow = 'modal fade';
      state.styleCss = 'display:none';
      state.userId = 0;
      state.user = null;
    },

    [USERS_MODAL_SET_IS_OPEN_MODAL](state, payload) {
      state.isOpen = payload
    },

    [USERS_MODAL_SET_LOADING](state, payload) {
      state.loading = payload
    },

    [USERS_MODAL_INSERT_USER_SUCCESS](state, payload) {
      state.insertSuccess = payload
    },

    [USERS_MODAL_INSERT_USER_FAILED](state, payload) {
      state.insertSuccess = payload
    },

    [USERS_MODAL_UPDATE_USER_SUCCESS](state, payload) {
      state.insertSuccess = payload
    },

    [USERS_MODAL_SET_ERROR](state, payload) {
      state.errors = payload
    }
  },

  actions: {
    [ACTION_SHOW_MODAL]({
      dispatch,
      commit
    }, payload) {
      commit(USERS_MODAL_SET_OPEN_MODAL, payload.action);

      dispatch(ACTION_IS_OPEN_MODAL, true);
    },

    [ACTION_CLOSE_MODAL]({
      dispatch,
      commit
    }) {
      commit(USERS_MODAL_SET_CLOSE_MODAL);

      dispatch(ACTION_IS_OPEN_MODAL, false);
    },

    [ACTION_IS_OPEN_MODAL]({
      commit
    }, payload) {
      commit(USERS_MODAL_SET_IS_OPEN_MODAL, payload);
    },

    [ACTION_SET_LOADING]({
      commit
    }, isLoading) {
      commit(USERS_MODAL_SET_LOADING, isLoading);
    },

    [ACTION_INSERT_USER]({
      dispatch,
      commit
    }, user) {
      apiInsertUser(
        user,
        (result) => {
          commit(USERS_MODAL_INSERT_USER_SUCCESS, AppConfig.comInsertNoSuccess);

          dispatch(ACTION_RELOAD_GET_USER_LIST, null, {
            root: true
          });
          dispatch(ACTION_SET_LOADING, false);
          dispatch(ACTION_CLOSE_MODAL);
        },
        (errors) => {
          commit(USERS_MODAL_INSERT_USER_FAILED, AppConfig.comInsertNoFail);

          dispatch(ACTION_SET_LOADING, false);
        }
      )
    },

    [ACTION_RESET_NOTIFICATION_INFO]({
      commit
    }, values) {
      commit(USERS_MODAL_UPDATE_USER_SUCCESS, values)
    }
  }
}