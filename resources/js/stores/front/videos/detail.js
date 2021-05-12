import {
  apiGetDetail,
} from '@app/api/front/videos';
import {
  INIT_LIST,
  SET_ERROR,
} from '@app/stores/front/types/mutation-types';
import {
  GET_DETAIL
} from '@app/stores/front/types/action-types';

export default {
  namespaced: true,
  state: {
    mainMenus: [],
    pageLists: {},
    errors: []
  },
  getters: {
    mainMenus(state) {
      return state.mainMenus
    },
    pageLists(state) {
      return state.pageLists;
    }
  },

  mutations: {
    MAIN_MENU(state, value) {
      state.mainMenus = value
    },
    INIT_LIST(state, payload) {
      state.pageLists = payload;
    },
    SET_ERROR(state, payload) {
      state.errors = payload;
    }
  },

  actions: {
    [GET_DETAIL]({
      commit
    }, routeParams) {
      if (routeParams.hasOwnProperty('slug')) {
        apiGetDetail(
          routeParams.slug,
          (result) => {
            console.log(result)
            commit(INIT_LIST, result.data.results);
          },
          (errors) => {
            console.log(errors)
          },
          routeParams
        )
      }
    },
  }
}
