import AppConfig from 'api@admin/constants/app-config';
import { v4 as uuidv4 } from 'uuid';
import {
  apiInsertInfo,
  apiGetDropdownInfos
} from 'api@admin/information';
import {
  INFOS_MODAL_SET_LOADING,
  INFOS_MODAL_INSERT_INFO_SUCCESS,
  INFOS_MODAL_INSERT_INFO_FAILED,
  INFOS_MODAL_SET_ERROR,
  INFOS_FORM_ADD_INFO_TO_CATEGORY_LIST,
  INFOS_FORM_ADD_INFO_TO_CATEGORY_DISPLAY_LIST,
  INFOS_FORM_ADD_INFO_TO_RELATED_LIST,
  INFOS_FORM_ADD_INFO_TO_RELATED_DISPLAY_LIST,
  INFOS_FORM_SET_MAIN_IMAGE,
  INFOS_FORM_SET_DROPDOWN_RELATED_LIST,
  INFOS_FORM_GET_DROPDOWN_RELATED_SUCCESS,
  INFOS_FORM_GET_DROPDOWN_RELATED_FAILED,
  INFOS_FORM_SELECT_DROPDOWN_INFO_TO_RELATED,
} from '../types/mutation-types';
import {
  ACTION_SET_LOADING,
  ACTION_INSERT_INFO,
  ACTION_RELOAD_GET_INFO_LIST,
  ACTION_ADD_INFO_TO_CATEGORY_LIST,
  ACTION_REMOVE_INFO_TO_CATEGORY_LIST,
  ACTION_ADD_INFO_TO_RELATED_LIST,
  ACTION_REMOVE_INFO_TO_RELATED_LIST,
  ACTION_INSERT_INFO_BACK,
  ACTION_SET_IMAGE,
  ACTION_GET_DROPDOWN_RELATED_LIST,
  ACTION_SELECT_DROPDOWN_RELATED_INFO
} from '../types/action-types';

const defaultState = () => {
  return {
    isOpen: false,
    action: null,
    classShow: 'modal fade',
    styleCss: '',
    info: {
      image: {
        basename: "",
        dirname: "",
        extension: "",
        filename: "",
        path: "",
        size: 0,
        thumb: "", //url thumb
        timestamp: null,
        type: null
      },
      name: '',
      khaiquat: '',
      sort_id: '',
      active: 1,
      updateuser: '',

      giao_phan_hats: [],
      giao_phan_dongs: [],
      giao_phan_cosos: [],
      giao_phan_banchuyentrachs: []
    },
    isImgChange: true,
    infoId: 0,
    loading: false,
    insertSuccess: false,
    errors: []
  }
}

export default {
  namespaced: true,
  state: defaultState(),
  getters: {
    isOpen(state) {
      return state.isOpen
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
    [INFOS_FORM_SELECT_DROPDOWN_INFO_TO_RELATED](state, payload) {
      state.infoRelated = payload;
    },

    [INFOS_FORM_SET_DROPDOWN_RELATED_LIST](state, payload) {
      state.dropdownsRelateds = payload;
    },

    [INFOS_FORM_GET_DROPDOWN_RELATED_SUCCESS](state, payload) {

    },
    [INFOS_FORM_GET_DROPDOWN_RELATED_FAILED](state, payload) {

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

    [INFOS_MODAL_SET_ERROR](state, payload) {
      state.errors = payload
    },

    [INFOS_FORM_ADD_INFO_TO_CATEGORY_LIST](state, payload) {
      state.info.categorys = payload
    },

    [INFOS_FORM_ADD_INFO_TO_CATEGORY_DISPLAY_LIST](state, payload) {
      state.listCategorysDisplay = payload
    },

    [INFOS_FORM_ADD_INFO_TO_RELATED_LIST](state, payload) {
      state.info.relateds = payload
    },

    [INFOS_FORM_ADD_INFO_TO_RELATED_DISPLAY_LIST](state, payload) {
      state.listRelatedsDisplay = payload
    },

    [INFOS_FORM_SET_MAIN_IMAGE](state, payload) {
      state.info.image = payload;
      state.isImgChange = true;
    }
  },

  actions: {
    addHatGiaoPhan({state}, params) {
      state.info.giao_phan_hats.push({
        id: uuidv4(),
        giao_hat_id: null,
        active: 1,
        giao_xus: [],
        cong_doan_tu_sis: []
      })
    },
    removeHatGiaoPhan({state}, params) {
      let giaoPhanHats = state.info.giao_phan_hats;
      const data = params.item;

      state.info.giao_phan_hats = _.remove(giaoPhanHats, function(item) {
        return !(item.id == data.id);
      })
    },
    addDongGiaoPhan({state}, params) {
      state.info.giao_phan_dongs.push({
        id: uuidv4(),
        dong_id: null,
        active: 1
      })
    },
    removeDongGiaoPhan({state}, params) {
      let giaoPhanDongs = state.info.giao_phan_dongs;
      const data = params.item;

      state.info.giao_phan_dongs = _.remove(giaoPhanDongs, function(item) {
        return !(item.id == data.id);
      })
    },
    addCoSoGiaoPhan({state}, params) {
      state.info.giao_phan_cosos.push({
        id: uuidv4(),
        co_so_giao_phan_id: null,
        active: 1
      })
    },
    removeCoSoGiaoPhan({state}, params) {
      let giaoPhanCoSos = state.info.giao_phan_cosos;
      const data = params.item;

      state.info.giao_phan_cosos = _.remove(giaoPhanCoSos, function(item) {
        return !(item.id == data.id);
      })
    },
    addBanChuyenTrachGiaoPhan({state}, params) {
      state.info.giao_phan_banchuyentrachs.push({
        id: uuidv4(),
        ban_chuyen_trach_id: null,
        active: 1
      })
    },
    removeBanChuyenTrachGiaoPhan({state}, params) {
      let giaoPhanBanChuyenTrachs = state.info.giao_phan_banchuyentrachs;
      const data = params.item;

      state.info.giao_phan_banchuyentrachs = _.remove(giaoPhanBanChuyenTrachs, function(item) {
        return !(item.id == data.id);
      })
    },
    update_special_carousel({state}, specialCarousel) {
      state.info.special_carousels = specialCarousel;
    },

    [ACTION_SET_LOADING]({
      commit
    }, isLoading) {
      commit(INFOS_MODAL_SET_LOADING, isLoading);
    },

    [ACTION_INSERT_INFO]({
      dispatch,
      commit
    }, info) {
      apiInsertInfo(
        info,
        (result) => {
          commit(INFOS_MODAL_INSERT_INFO_SUCCESS, AppConfig.comInsertNoSuccess);
          commit(INFOS_MODAL_SET_ERROR, []);

          dispatch(ACTION_SET_LOADING, false);
        },
        (errors) => {
          commit(INFOS_MODAL_INSERT_INFO_FAILED, AppConfig.comInsertNoFail);
          commit(INFOS_MODAL_SET_ERROR, errors);

          dispatch(ACTION_SET_LOADING, false);
        }
      )
    },

    [ACTION_INSERT_INFO_BACK]({
      dispatch,
      commit
    }, info) {
      apiInsertInfo(
        info,
        (result) => {
          commit(INFOS_MODAL_INSERT_INFO_SUCCESS, AppConfig.comInsertNoSuccess);

          dispatch(ACTION_RELOAD_GET_INFO_LIST, 'page', {
            root: true
          });
        },
        (errors) => {
          commit(INFOS_MODAL_INSERT_INFO_FAILED, AppConfig.comInsertNoFail);
          commit(INFOS_MODAL_SET_ERROR, errors);

          dispatch(ACTION_SET_LOADING, false);
        }
      )
    },

    [ACTION_ADD_INFO_TO_CATEGORY_LIST]({
      commit,
      state
    }, category) {
      const categorys = state.info.categorys;
      const listCateShow = state.listCategorysDisplay;

      if (typeof category === "object" && Object.keys(category).length) {
        if ((categorys.indexOf(category.category_id) === -1) && (parseInt(category.category_id) > 0)) {
          categorys.push(category.category_id);
          listCateShow.push(category);
        }
      }

      commit(INFOS_FORM_ADD_INFO_TO_CATEGORY_LIST, categorys);
      commit(INFOS_FORM_ADD_INFO_TO_CATEGORY_DISPLAY_LIST, listCateShow);
    },

    [ACTION_REMOVE_INFO_TO_CATEGORY_LIST]({
      state,
      commit
    }, category) {
      const categorys = state.info.categorys;
      const listCateShow = state.listCategorysDisplay;

      commit(INFOS_FORM_ADD_INFO_TO_CATEGORY_LIST, _.remove(categorys, function(cateId) {
        return (cateId - category.category_id !== 0);
      }));
      commit(INFOS_FORM_ADD_INFO_TO_CATEGORY_DISPLAY_LIST, _.remove(listCateShow, function(item) {
        return (item.category_id - category.category_id !== 0);
      }));
    },

    [ACTION_SET_IMAGE]({
      commit
    }, imgFile) {
      commit(INFOS_FORM_SET_MAIN_IMAGE, imgFile);
    },

    [ACTION_ADD_INFO_TO_RELATED_LIST]({
      state,
      commit
    }, related) {
      const relateds = state.info.relateds;
      const listRelatedShow = state.listRelatedsDisplay;

      if (typeof related === "object" && Object.keys(related).length) {
        if ((relateds.indexOf(related.information_id) === -1) && (parseInt(related.information_id) > 0)) {
          relateds.push(related.information_id);
          listRelatedShow.push(related);
        }
      }

      commit(INFOS_FORM_ADD_INFO_TO_RELATED_LIST, relateds);
      commit(INFOS_FORM_ADD_INFO_TO_RELATED_DISPLAY_LIST, listRelatedShow);
    },

    [ACTION_REMOVE_INFO_TO_RELATED_LIST]({
      state,
      commit
    }, related) {
      const relateds = state.info.relateds;
      const listRelatedShow = state.listRelatedsDisplay;

      commit(INFOS_FORM_ADD_INFO_TO_RELATED_LIST, _.remove(relateds, function(infoId) {
        return (infoId - related.information_id !== 0);
      }));
      commit(INFOS_FORM_ADD_INFO_TO_RELATED_DISPLAY_LIST, _.remove(listRelatedShow, function(item) {
        return (item.information_id - related.information_id !== 0);
      }));
    },

    [ACTION_GET_DROPDOWN_RELATED_LIST]({
      commit
    }, filterName) {
      const params = {
        filter_name: filterName
      }
      apiGetDropdownInfos(
        (result) => {
          commit(INFOS_FORM_GET_DROPDOWN_RELATED_SUCCESS, 'Success');

          commit(INFOS_FORM_SET_DROPDOWN_RELATED_LIST, result);
        },
        (errors) => {
          commit(INFOS_FORM_GET_DROPDOWN_RELATED_FAILED, 'Failed');
        },
        params
      );
    },

    [ACTION_SELECT_DROPDOWN_RELATED_INFO]({
      commit
    }, information) {
      commit(INFOS_FORM_SELECT_DROPDOWN_INFO_TO_RELATED, information);
    }
  }
}