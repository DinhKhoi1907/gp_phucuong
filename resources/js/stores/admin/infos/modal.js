import axios from 'axios';
import {
  apiGetInfoById, 
  apiUpdateInfo, 
  apiInsertInfo
} from 'api@admin/information';
import { 
  INFOS_MODAL_SET_OPEN_MODAL, 
  INFOS_MODAL_SET_CLOSE_MODAL, 
  INFOS_MODAL_SET_IS_OPEN_MODAL,
  INFOS_MODAL_SET_INFO_ID,
  INFOS_MODAL_SET_INFO,
  INFOS_MODAL_SET_LOADING,
  INFOS_MODAL_INSERT_INFO_SUCCESS,
  INFOS_MODAL_UPDATE_INFO_SUCCESS,
  INFOS_MODAL_INSERT_INFO_FAILED,
  INFOS_MODAL_UPDATE_INFO_FAILED,
  INFOS_MODAL_SET_ERROR 
} from '../types/mutation-types';
import {
  ACTION_GET_INFO_BY_ID,
  ACTION_SET_LOADING,
  ACTION_SHOW_MODAL,
  ACTION_SHOW_MODAL_EDIT,
  ACTION_CLOSE_MODAL,
  ACTION_IS_OPEN_MODAL,
  ACTION_INSERT_INFO,
  ACTION_UPDATE_INFO
} from '../types/action-types';

export default {
    namespaced: true,
    state: {
      isOpen: false,
      action: null,
      classShow: 'modal fade',
      styleCss: '',
      info: null,
      infoId: 0,
      loading: false,
      updateSuccess: false,
      errors: []
    },
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
      info(state) {
      	return state.info
      },
      loading(state) {
      	return state.loading
      },
      updateSuccess(state) {
        return state.updateSuccess
      },
      errors(state) {
        return state.errors
      },
      isError(state) {
        return state.errors.length
      }
    },

    mutations: {
    		[INFOS_MODAL_SET_OPEN_MODAL](state, payload) {
    			state.action = payload;
    			state.classShow = 'modal fade show';
    			state.styleCss = 'display:block';
          state.updateSuccess = false;
    		},

    		[INFOS_MODAL_SET_CLOSE_MODAL](state) {
    			if (state.action === 'add') {
    				state.action = null;
	    			state.classShow = 'modal fade';
	    			state.styleCss = 'display:none';
    			} else {
    				state.action = null;
	    			state.classShow = 'modal fade';
	    			state.styleCss = 'display:none';
	    			state.infoId = 0;
	    			state.info = null;
    			}
    		},

        [INFOS_MODAL_SET_IS_OPEN_MODAL](state, payload) {
            state.isOpen = payload
        },

        [INFOS_MODAL_SET_INFO_ID](state, payload) {
        	state.infoId = payload
        },

        [INFOS_MODAL_SET_INFO](state, payload) {
        	state.info = payload
        },

        [INFOS_MODAL_SET_LOADING](state, payload) {
        	state.loading = payload
        },

        [INFOS_MODAL_INSERT_INFO_SUCCESS](state, payload) {
          state.updateSuccess = payload
        },

        [INFOS_MODAL_INSERT_INFO_FAILED](state, payload) {
          state.updateSuccess = payload
        },

        [INFOS_MODAL_UPDATE_INFO_SUCCESS](state, payload) {
        	state.updateSuccess = payload
        },

        [INFOS_MODAL_UPDATE_INFO_FAILED](state, payload) {
          state.updateSuccess = payload
        },

        [INFOS_MODAL_SET_ERROR](state, payload) {
          state.errors = payload
        }
    },

    actions: {
        [ACTION_SHOW_MODAL] ({ dispatch, commit }, actionName) {console.log(actionName)
        	commit(INFOS_MODAL_SET_OPEN_MODAL, actionName);

        	dispatch(ACTION_IS_OPEN_MODAL, true);
        },

        [ACTION_SHOW_MODAL_EDIT] ({ dispatch, commit }, infoId) {
        	commit(INFOS_MODAL_SET_INFO_ID, infoId);
        	commit(INFOS_MODAL_SET_OPEN_MODAL, 'edit');

        	dispatch(ACTION_GET_INFO_BY_ID, infoId);
        },

        [ACTION_GET_INFO_BY_ID] ({dispatch, commit}, infoId) {
          dispatch(ACTION_SET_LOADING, true);
          apiGetUserById(
            infoId,
            (result) => {
              commit(INFOS_MODAL_SET_INFO, result.data);

              dispatch(ACTION_SET_LOADING, false);
              dispatch(ACTION_IS_OPEN_MODAL, true);
            }
          );
        },

        [ACTION_CLOSE_MODAL] ({ dispatch, commit }) {
        	commit(INFOS_MODAL_SET_CLOSE_MODAL);

        	dispatch(ACTION_IS_OPEN_MODAL, false);
        },

        [ACTION_IS_OPEN_MODAL] ({commit}, payload) {
        	commit(INFOS_MODAL_SET_IS_OPEN_MODAL, payload);
        },

        [ACTION_SET_LOADING] ({commit} , isLoading) {
        	commit(INFOS_MODAL_SET_LOADING, isLoading);
        },

        [ACTION_INSERT_INFO] ({ dispatch, commit }, info) {
          apiInsertUser(
            info,
            (result) => {
              commit(INFOS_MODAL_INSERT_INFO_SUCCESS, true);
              
              dispatch(ACTION_SET_LOADING, false);
              dispatch(ACTION_CLOSE_MODAL);
            },
            (errors) => {
              commit(INFOS_MODAL_INSERT_INFO_FAILED, false)

              dispatch(ACTION_SET_LOADING, false);
            }
          )
        },

        [ACTION_UPDATE_INFO] ({ dispatch, commit }, info) {
          apiUpdateUser(info,
            (result) => {
              commit(INFOS_MODAL_UPDATE_INFO_SUCCESS, true);
              
              dispatch(ACTION_SET_LOADING, false);
              dispatch(ACTION_CLOSE_MODAL);
            },
            (errors) => {
              commit(INFOS_MODAL_UPDATE_INFO_FAILED, false)

              dispatch(ACTION_SET_LOADING, false);
            }
          )
        }
    }
}