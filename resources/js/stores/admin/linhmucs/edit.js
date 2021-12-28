import AppConfig from 'api@admin/constants/app-config'
import { v4 as uuidv4, } from 'uuid'
import { apiUpdateInfo, apiGetInfoById, } from 'api@admin/linhmuc'
import {
  INFOS_MODAL_SET_LOADING,
  INFOS_MODAL_INSERT_INFO_SUCCESS,
  INFOS_MODAL_INSERT_INFO_FAILED,
  SET_ERROR,
  INFOS_FORM_SET_MAIN_IMAGE,
} from '../types/mutation-types'
import {
  ACTION_SET_LOADING,
  ACTION_INSERT_INFO,
  ACTION_INSERT_INFO_BACK,
  ACTION_SET_IMAGE,
  ACTION_GET_INFO_BY_ID,
  ACTION_RESET_NOTIFICATION_INFO,
} from '../types/action-types'
import { fnCheckProp, } from '@app/common/util'

const defaultState = () => {
  return {
    info: {
      image: '',
      is_duc_cha: false,
      ten_thanh_id: null,
      ten_thanh_name: '',
      ten: '',
      ngay_thang_nam_sinh: null,
      noi_sinh: '',
      giao_xu_id: null,
      giao_xu_name: '',
      ho_ten_cha: '',
      ho_ten_me: '',
      noi_rua_toi: '',
      ngay_rua_toi: null,
      noi_them_suc: '',
      ngay_them_suc: null,
      tieu_chung_vien: null,
      ngay_tieu_chung_vien: null,
      dai_chung_vien: null,
      ngay_dai_chung_vien: null,
      so_cmnd: null,
      noi_cap_cmnd: '',
      ngay_cap_cmnd: null,
      trieu_dong: null,
      ten_dong_id: '',
      ten_dong_name: '',
      ngay_trieu_dong: null,
      ngay_khan: null,
      ngay_rip: null,
      rip_giao_xu_id: null,
      rip_giaoxu_name: '',
      rip_ghi_chu: '',
      ghi_chu: '',
      code: '',
      phone: '',
      email: '',
      password: '',
      sort_id: 0,
      active: 1,

      bang_caps: [],
      chuc_thanhs: [],
      thuyen_chuyens: [],
      van_thus: [],
    },
    isImgChange: true,
    loading: false,
    insertSuccess: false,
    errors: [],
  }
}

export default {
  namespaced: true,
  state: defaultState(),
  getters: {
    info(state) {
      return state.info
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
    },
  },

  mutations: {
    update_dropdown_thuyen_chuyen(state, payload) {
      _.forEach(state.info.thuyen_chuyens, function(item, idx) {
        if (fnCheckProp(item, 'id') && item.id == payload.id) {
          state.info.thuyen_chuyens[idx] = payload
        }
      })
    },
    update_thuyen_chuyen(state, payload) {
      state.info.thuyen_chuyens = payload
    },
    update_van_thu(state, payload) {
      state.info.van_thus = payload
    },
    update_chuc_thanh(state, payload) {
      state.info.chuc_thanhs = payload
    },
    update_bang_cap(state, payload) {
      state.info.bang_caps = payload
    },
    [INFOS_MODAL_SET_LOADING](state, payload) {
      state.loading = payload
    },
    [INFOS_MODAL_INSERT_INFO_SUCCESS](state, payload) {
      state.insertSuccess = payload
    },
    [INFOS_MODAL_INSERT_INFO_FAILED](state, payload) {
      state.insertSuccess = payload
    },
    [SET_ERROR](state, payload) {
      state.errors = payload
    },
    [INFOS_FORM_SET_MAIN_IMAGE](state, payload) {
      state.info.image = payload
      state.isImgChange = true
    },
    LINH_MUCS_SET_INFO(state, payload) {
      state.info = payload
    },
  },

  actions: {
    ACTION_UPDATE_DROPDOWN_DONG({ state, }, params) {
      state.info.ten_dong_id = params.dong.id
      state.info.ten_dong_name = params.dong.name
    },
    ACTION_UPDATE_DROPDOWN_RIP_GIAO_XU({ state, }, params) {
      state.info.rip_giao_xu_id = params.giaoXu.id
      state.info.rip_giaoxu_name = params.giaoXu.name
    },
    ACTION_UPDATE_DROPDOWN_GIAO_XU({ state, }, params) {
      state.info.giao_xu_id = params.giaoXu.id
      state.info.giao_xu_name = params.giaoXu.name
    },
    ACTION_UPDATE_DROPDOWN_TEN_THANH_LIST({ state, }, params) {
      state.info.ten_thanh_id = params.tenThanh.id
      state.info.ten_thanh_name = params.tenThanh.name
    },
    addBangCaps({ dispatch, state, commit, }, params) {
      let bangCaps = state.info.bang_caps
      if (
        fnCheckProp(params, 'action') &&
                fnCheckProp(params, 'info') &&
                params.action === 'create.update.bang.cap.db'
      ) {
        let bangCap = params.info
        dispatch(ACTION_SET_LOADING, true)
        //implement
        bangCap['linhMucId'] = state.info.id
        bangCap['action'] = params.action
        apiUpdateInfo(
          bangCap,
          (result) => {
            commit('update_bang_cap', result.data.data.results)
            commit(SET_ERROR, [])
            commit(
              INFOS_MODAL_INSERT_INFO_SUCCESS,
              AppConfig.comInsertNoSuccess
            )
            dispatch(ACTION_SET_LOADING, false)
          },
          (errors) => {
            commit(
              INFOS_MODAL_INSERT_INFO_FAILED,
              AppConfig.comInsertNoFail
            )
            commit(SET_ERROR, errors)
            dispatch(ACTION_SET_LOADING, false)
          }
        )
      } else {
        bangCaps.push({
          id: uuidv4(),
          isCheck: false,
          isEdit: 0,
          name: '',
          type: 0,
          ghi_chu: '',
          active: 1,
        })
        commit('update_bang_cap', bangCaps)
      }
    },
    removeBangCap({ commit, state, }, params) {
      let bangCaps = state.info.bang_caps
      const data = params.item
      commit(
        'update_bang_cap',
        _.remove(bangCaps, (item) => {
          return !(item.id == data.id)
        })
      )
    },
    checkAllBangCap({ state, }, check) {
      _.forEach(state.info.bang_caps, (item) => {
        _.update(item, 'isCheck', (isCheck) => {
          isCheck = check

          return isCheck
        })
      })
    },
    addChucThanhs({ dispatch, commit, state, }, params) {
      let chucThanhs = state.info.chuc_thanhs
      if (
        fnCheckProp(params, 'action') &&
                fnCheckProp(params, 'info') &&
                params.action === 'create.update.chuc.thanh.db'
      ) {
        let chucThanh = params.info
        dispatch(ACTION_SET_LOADING, true)
        //implement
        chucThanh['linhMucId'] = state.info.id
        chucThanh['action'] = params.action
        apiUpdateInfo(
          chucThanh,
          (result) => {
            commit('update_chuc_thanh', result.data.data.results)
            commit(SET_ERROR, [])
            commit(
              INFOS_MODAL_INSERT_INFO_SUCCESS,
              AppConfig.comInsertNoSuccess
            )
            dispatch(ACTION_SET_LOADING, false)
          },
          (errors) => {
            commit(
              INFOS_MODAL_INSERT_INFO_FAILED,
              AppConfig.comInsertNoFail
            )
            commit(SET_ERROR, errors)
            dispatch(ACTION_SET_LOADING, false)
          }
        )
      } else {
        chucThanhs.push({
          id: uuidv4(),
          isCheck: false,
          isEdit: 0,
          chuc_thanh_id: 1,
          ngay_thang_nam_chuc_thanh: null,
          noi_thu_phong: '',
          nguoi_thu_phong: '',
          ghi_chu: '',
          active: 1,
        })
        commit('update_chuc_thanh', chucThanhs)
      }
    },
    removeChucThanh({ commit, state, }, params) {
      let chucThanhs = state.info.chuc_thanhs
      const data = params.item
      commit(
        'update_chuc_thanh',
        _.remove(chucThanhs, (item) => {
          return !(item.id == data.id)
        })
      )
    },
    checkAllChucThanh({ state, }, check) {
      _.forEach(state.info.chuc_thanhs, (item) => {
        _.update(item, 'isCheck', (isCheck) => {
          isCheck = check

          return isCheck
        })
      })
    },
    addVanThus({ dispatch, commit, state, }, params) {
      let vanThus = state.info.van_thus
      if (
        fnCheckProp(params, 'action') &&
                fnCheckProp(params, 'info') &&
                params.action === 'create.update.van.thu.db'
      ) {
        let vanThu = params.info
        dispatch(ACTION_SET_LOADING, true)
        //implement
        vanThu['linhMucId'] = state.info.id
        vanThu['action'] = params.action
        apiUpdateInfo(
          vanThu,
          (result) => {
            commit('update_van_thu', result.data.data.results)
            commit(SET_ERROR, [])
            commit(
              INFOS_MODAL_INSERT_INFO_SUCCESS,
              AppConfig.comInsertNoSuccess
            )
            dispatch(ACTION_SET_LOADING, false)
          },
          (errors) => {
            commit(
              INFOS_MODAL_INSERT_INFO_FAILED,
              AppConfig.comInsertNoFail
            )
            commit(SET_ERROR, errors)
            dispatch(ACTION_SET_LOADING, false)
          }
        )
      } else {
        vanThus.push({
          id: uuidv4(),
          isCheck: false,
          isEdit: 0,
          parent_id: 0,
          title: null,
          type: '',
          ghi_chu: '',
          active: 1,
        })
        commit('update_van_thu', vanThus)
      }
    },
    removeVanThu({ commit, state, }, params) {
      let vanThus = state.info.van_thus
      const data = params.item

      commit(
        'update_van_thu',
        _.remove(vanThus, (item) => {
          return !(item.id == data.id)
        })
      )
    },
    checkAllVanThu({ state, }, check) {
      _.forEach(state.info.van_thus, (item) => {
        _.update(item, 'isCheck', (isCheck) => {
          isCheck = check

          return isCheck
        })
      })
    },
    ACTION_UPDATE_DROPDOWN_THUYEN_CHUYEN_BAN_CHUYEN_TRACH(
      { commit, },
      params
    ) {
      let thuyenChuyen = params.thuyenChuyen
      thuyenChuyen.banchuyentrachName = params.banChuyenTrach.name
      thuyenChuyen.ban_chuyen_trach_id = params.banChuyenTrach.id
      commit('update_dropdown_thuyen_chuyen', thuyenChuyen)
    },
    ACTION_UPDATE_DROPDOWN_THUYEN_CHUYEN_DONG({ commit, }, params) {
      let thuyenChuyen = params.thuyenChuyen
      thuyenChuyen.dongName = params.dong.name
      thuyenChuyen.dong_id = params.dong.id
      commit('update_dropdown_thuyen_chuyen', thuyenChuyen)
    },
    ACTION_UPDATE_DROPDOWN_CO_SO_GIAO_PHAN({ commit, }, params) {
      let thuyenChuyen = params.thuyenChuyen
      thuyenChuyen.cosogpName = params.coso.name
      thuyenChuyen.co_so_gp_id = params.coso.id
      commit('update_dropdown_thuyen_chuyen', thuyenChuyen)
    },
    ACTION_UPDATE_DROPDOWN_FROM_GIAO_XU({ commit, }, params) {
      let thuyenChuyen = params.thuyenChuyen
      thuyenChuyen.fromgiaoxuName = params.giaoXu.name
      thuyenChuyen.from_giao_xu_id = params.giaoXu.id
      commit('update_dropdown_thuyen_chuyen', thuyenChuyen)
    },
    ACTION_UPDATE_DROPDOWN_TO_GIAO_XU({ commit, }, params) {
      let thuyenChuyen = params.thuyenChuyen
      thuyenChuyen.giaoxuName = params.giaoXu.name
      thuyenChuyen.giao_xu_id = params.giaoXu.id
      commit('update_dropdown_thuyen_chuyen', thuyenChuyen)
    },
    ACTION_UPDATE_DROPDOWN_FROM_CHUC_VU({ commit, }, params) {
      let thuyenChuyen = params.thuyenChuyen
      thuyenChuyen.fromchucvuName = params.chucVu.name
      thuyenChuyen.from_chuc_vu_id = params.chucVu.id
      commit('update_dropdown_thuyen_chuyen', thuyenChuyen)
    },
    ACTION_UPDATE_DROPDOWN_TO_CHUC_VU({ commit, }, params) {
      let thuyenChuyen = params.thuyenChuyen
      thuyenChuyen.chucvuName = params.chucVu.name
      thuyenChuyen.chuc_vu_id = params.chucVu.id
      commit('update_dropdown_thuyen_chuyen', thuyenChuyen)
    },
    ACTION_UPDATE_DROPDOWN_FROM_DUC_CHA({ commit, }, params) {
      let thuyenChuyen = params.thuyenChuyen
      thuyenChuyen.ducchaName = params.ducCha.name
      thuyenChuyen.duc_cha_id = params.ducCha.id
      commit('update_dropdown_thuyen_chuyen', thuyenChuyen)
    },
    addThuyenChuyen({ dispatch, commit, state, }, params) {
      let thuyenChuyens = state.info.thuyen_chuyens
      if (
        fnCheckProp(params, 'action') &&
                fnCheckProp(params, 'info') &&
                params.action === 'create.update.thuyen.chuyen.db'
      ) {
        let thuyenChuyen = params.info
        dispatch(ACTION_SET_LOADING, true)
        //implement
        thuyenChuyen['linhMucId'] = state.info.id
        thuyenChuyen['action'] = params.action
        apiUpdateInfo(
          thuyenChuyen,
          (result) => {
            commit('update_thuyen_chuyen', result.data.data.results)
            commit(SET_ERROR, [])
            commit(
              INFOS_MODAL_INSERT_INFO_SUCCESS,
              AppConfig.comInsertNoSuccess
            )
            dispatch(ACTION_SET_LOADING, false)
          },
          (errors) => {
            commit(
              INFOS_MODAL_INSERT_INFO_FAILED,
              AppConfig.comInsertNoFail
            )
            commit(SET_ERROR, errors)
            dispatch(ACTION_SET_LOADING, false)
          }
        )
      } else {
        thuyenChuyens.push({
          id: uuidv4(),
          isCheck: false,
          isEdit: 0,
          from_giao_xu_id: null,
          fromgiaoxuName: '',
          from_chuc_vu_id: null,
          fromchucvuName: '',
          from_date: null,
          duc_cha_id: null,
          ducchaName: '',
          to_date: null,
          chuc_vu_id: null,
          chucvuName: '',
          giao_xu_id: null,
          giaoxuName: '',
          co_so_gp_id: null,
          cosogpName: '',
          dong_id: null,
          dongName: '',
          ban_chuyen_trach_id: null,
          banchuyentrachName: '',
          du_hoc: null,
          quoc_gia: null,
          ghi_chu: '',
          active: 1,
        })
        commit('update_thuyen_chuyen', thuyenChuyens)
      }
    },
    removeThuyenChuyen({ commit, state, }, params) {
      let thuyenChuyens = state.info.thuyen_chuyens
      const data = params.item
      commit(
        'update_thuyen_chuyen',
        _.remove(thuyenChuyens, (item) => {
          return !(item.id == data.id)
        })
      )
    },
    checkAllThuyenChuyen({ state, }, check) {
      _.forEach(state.info.thuyen_chuyens, (item) => {
        _.update(item, 'isCheck', (isCheck) => {
          isCheck = check

          return isCheck
        })
      })
    },
    [ACTION_SET_LOADING]({ commit, }, isLoading) {
      commit(INFOS_MODAL_SET_LOADING, isLoading)
    },
    [ACTION_INSERT_INFO]({ dispatch, commit, }, info) {
      dispatch(ACTION_SET_LOADING, true)
      apiUpdateInfo(
        info,
        (result) => {
          if (result) {
            commit(
              INFOS_MODAL_INSERT_INFO_SUCCESS,
              AppConfig.comInsertNoSuccess
            )
            commit(SET_ERROR, [])
            dispatch(ACTION_GET_INFO_BY_ID, info.id)
          }
        },
        (errors) => {
          commit(
            INFOS_MODAL_INSERT_INFO_FAILED,
            AppConfig.comInsertNoFail
          )
          commit(SET_ERROR, errors)
          dispatch(ACTION_SET_LOADING, false)
        }
      )
    },
    [ACTION_INSERT_INFO_BACK]({ dispatch, commit, }, info) {
      apiUpdateInfo(
        info,
        (result) => {
          if (result) {
            commit(
              INFOS_MODAL_INSERT_INFO_SUCCESS,
              AppConfig.comInsertNoSuccess
            )
            dispatch('ACTION_RELOAD_INFO_LIST', 'page', {
              root: true,
            })
          }
        },
        (errors) => {
          commit(
            INFOS_MODAL_INSERT_INFO_FAILED,
            AppConfig.comInsertNoFail
          )
          commit(SET_ERROR, errors)
          dispatch(ACTION_SET_LOADING, false)
        }
      )
    },
    [ACTION_GET_INFO_BY_ID]({ dispatch, commit, }, infoId) {
      dispatch(ACTION_SET_LOADING, true)
      apiGetInfoById(
        infoId,
        (result) => {
          commit('LINH_MUCS_SET_INFO', result.data.linhmuc)
          dispatch(ACTION_SET_LOADING, false)
        },
        (errors) => {
          commit(SET_ERROR, Object.values(errors))
          dispatch(ACTION_SET_LOADING, false)
        }
      )
    },
    [ACTION_RESET_NOTIFICATION_INFO]({ commit, }, values) {
      commit(INFOS_MODAL_INSERT_INFO_SUCCESS, values)
    },
    [ACTION_SET_IMAGE]({ commit, }, imgFile) {
      commit(INFOS_FORM_SET_MAIN_IMAGE, imgFile)
    },
  },
}
