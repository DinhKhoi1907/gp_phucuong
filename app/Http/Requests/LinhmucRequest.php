<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Hash;
use Auth;
use Illuminate\Support\Carbon;

class LinhmucRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * @author : dtphi .
     * Get data to be validated from the request.
     *
     * @return array
     */
    public function validationData()
    {
        $formData = $this->all();

        $formData['ten'] = isset($formData['ten']) ? $formData['ten']: null;
        $formData['ten_thanh_id'] = isset($formData['ten_thanh_id'])?$formData['ten_thanh_id']: null;
        $formData['ngay_thang_nam_sinh'] = isset($formData['ngay_thang_nam_sinh'])?$formData['ngay_thang_nam_sinh']: '';
        if (!empty($formData['ngay_thang_nam_sinh'])) {
            $formData['ngay_thang_nam_sinh'] = new Carbon($formData['ngay_thang_nam_sinh']);
        } else {
            $formData['ngay_thang_nam_sinh'] = NULL;
        }
        $formData['noi_sinh'] = isset($formData['noi_sinh'])?$formData['noi_sinh']:null;
        $formData['giao_xu_id'] = isset($formData['giao_xu_id'])?$formData['giao_xu_id']:null;
        $formData['ho_ten_cha'] = isset($formData['ho_ten_cha'])?$formData['ho_ten_cha']:null;
        $formData['ho_ten_me'] = isset($formData['ho_ten_me'])?$formData['ho_ten_me']:null;
        $formData['noi_rua_toi'] = isset($formData['noi_rua_toi'])?$formData['noi_rua_toi']:null;
        $formData['ngay_rua_toi'] = isset($formData['ngay_rua_toi'])?$formData['ngay_rua_toi']:'';
        if (!empty($formData['ngay_rua_toi'])) {
            $formData['ngay_rua_toi'] = new Carbon($formData['ngay_rua_toi']);
        } else {
            $formData['ngay_rua_toi'] = NULL;
        }
        $formData['noi_them_suc'] = isset($formData['noi_them_suc'])?$formData['noi_them_suc']:null;
        $formData['ngay_them_suc'] = isset($formData['ngay_them_suc'])?$formData['ngay_them_suc']: '';
        if (!empty($formData['ngay_them_suc'])) {
            $formData['ngay_them_suc'] = new Carbon($formData['ngay_them_suc']);
        } else {
            $formData['ngay_them_suc'] = NULL;
        }
        $formData['tieu_chung_vien'] = isset($formData['tieu_chung_vien'])?$formData['tieu_chung_vien']:null;
        $formData['ngay_tieu_chung_vien'] = isset($formData['ngay_tieu_chung_vien'])?$formData['ngay_tieu_chung_vien']: '';
        if (!empty($formData['ngay_tieu_chung_vien'])) {
            $formData['ngay_tieu_chung_vien'] = new Carbon($formData['ngay_tieu_chung_vien']);
        } else {
            $formData['ngay_tieu_chung_vien'] = NULL;
        }
        $formData['dai_chung_vien'] = isset($formData['dai_chung_vien'])?$formData['dai_chung_vien']:null;
        $formData['ngay_dai_chung_vien'] = isset($formData['ngay_dai_chung_vien'])?$formData['ngay_dai_chung_vien']:'';
        if (!empty($formData['ngay_dai_chung_vien'])) {
            $formData['ngay_dai_chung_vien'] = new Carbon($formData['ngay_dai_chung_vien']);
        } else {
            $formData['ngay_dai_chung_vien'] = NULL;
        }
        $formData['image'] = isset($formData['image'])?$formData['image']:null;
        $formData['so_cmnd'] = isset($formData['so_cmnd'])?$formData['so_cmnd']:null;
        $formData['noi_cap_cmnd'] = isset($formData['noi_cap_cmnd'])?$formData['noi_cap_cmnd']:null;
        $formData['ngay_cap_cmnd'] = isset($formData['ngay_cap_cmnd'])?$formData['ngay_cap_cmnd']:'';
        if (!empty($formData['ngay_cap_cmnd'])) {
            $formData['ngay_cap_cmnd'] = new Carbon($formData['ngay_cap_cmnd']);
        } else {
            $formData['ngay_cap_cmnd'] = NULL;
        }
        $formData['trieu_dong'] = isset($formData['trieu_dong'])?$formData['trieu_dong']:0;
        $formData['ten_dong'] = isset($formData['ten_dong'])?$formData['ten_dong']:null;
        $formData['ngay_trieu_dong'] = isset($formData['ngay_trieu_dong'])?$formData['ngay_trieu_dong']:'';
        if (!empty($formData['ngay_trieu_dong'])) {
            $formData['ngay_trieu_dong'] = new Carbon($formData['ngay_cap_cmnd']);
        } else {
            $formData['ngay_trieu_dong'] = NULL;
        }
        $formData['ngay_khan'] = isset($formData['ngay_khan'])?$formData['ngay_khan']:'';
        if (!empty($formData['ngay_khan'])) {
            $formData['ngay_khan'] =  new Carbon($formData['ngay_cap_cmnd']);
        } else {
            $formData['ngay_khan'] = NULL;
        }
        $formData['ngay_rip'] = isset($formData['ngay_rip'])?$formData['ngay_rip']:'';
        if (!empty($formData['ngay_rip'])) {
            $formData['ngay_rip'] =  new Carbon($formData['ngay_cap_cmnd']);
        } else {
            $formData['ngay_rip'] = NULL;
        }
        $formData['rip_giao_xu_id'] = isset($formData['rip_giao_xu_id'])?$formData['rip_giao_xu_id']:null;
        $formData['rip_ghi_chu'] = isset($formData['rip_ghi_chu'])?$formData['rip_ghi_chu']:'';
        $formData['ghi_chu'] = isset($formData['ghi_chu'])?$formData['ghi_chu']:'';
        $formData['is_duc_cha'] = isset($formData['is_duc_cha'])?(int)$formData['is_duc_cha']:0;
        $formData['code'] = isset($formData['code'])?$formData['code']:'';
        $formData['phone'] = isset($formData['phone'])?$formData['phone']:'';
        $formData['email'] = isset($formData['email'])?$formData['email']:'';
        $formData['password'] = isset($formData['password'])?trim($formData['password']):'123789';
        $formData['password'] = Hash::make($formData['password']);
        $formData['update_user'] = isset(Auth::user()->id) ? Auth::user()->id : 0;

        $formData['bang_caps'] = isset($formData['bang_caps'])?$formData['bang_caps']:'';
        $formData['chuc_thanhs'] = isset($formData['chuc_thanhs'])?$formData['chuc_thanhs']:'';
        if (!empty($formData['chuc_thanhs'])) {
            $chucThanhs = [];
            foreach($formData['chuc_thanhs'] as $chucThanh) {
                $ngayChucThanh = isset($chucThanh['ngay_thang_nam_chuc_thanh'])?$chucThanh['ngay_thang_nam_chuc_thanh']:'';
                if (!empty($ngayChucThanh)) {
                    $ngayChucThanh =  new Carbon($ngayChucThanh);
                } else {
                    $ngayChucThanh = NULL;
                }

                $chucThanhs[] = [
                    'chuc_thanh_id' => $chucThanh['chuc_thanh_id'],
                    'ngay_thang_nam_chuc_thanh' => $ngayChucThanh,
                    'noi_thu_phong' => $chucThanh['noi_thu_phong'],
                    'nguoi_thu_phong' => $chucThanh['nguoi_thu_phong'],
                    'active' => $chucThanh['active'],
                    'ghi_chu' => $chucThanh['ghi_chu']
                ];
            }

            $formData['chuc_thanhs'] = $chucThanhs;
        }
        $formData['thuyen_chuyens'] = isset($formData['thuyen_chuyens'])?$formData['thuyen_chuyens']:'';
        if (!empty($formData['thuyen_chuyens'])) {
            $thuyenChuyens = [];
            foreach($formData['thuyen_chuyens'] as $thuyenChuyen) {
                $fromDate = isset($thuyenChuyen['from_date'])?$thuyenChuyen['from_date']:'';
                if (!empty($fromDate)) {
                    $fromDate =  new Carbon($fromDate);
                } else {
                    $fromDate = NULL;
                }

                $toDate = isset($thuyenChuyen['to_date'])?$thuyenChuyen['to_date']:'';
                if (!empty($toDate)) {
                    $toDate =  new Carbon($toDate);
                } else {
                    $toDate = NULL;
                }

                $thuyenChuyens[] = [
                    'from_giao_xu_id' => $thuyenChuyen['from_giao_xu_id'],
                    'from_chuc_vu_id'=> $thuyenChuyen['from_chuc_vu_id'],
                    'from_date' => $fromDate,
                    'duc_cha_id' => $thuyenChuyen['duc_cha_id'],
                    'to_date' => $toDate,
                    'chuc_vu_id' => $thuyenChuyen['chuc_vu_id'],
                    'giao_xu_id' => $thuyenChuyen['giao_xu_id'],
                    'co_so_gp_id' => $thuyenChuyen['co_so_gp_id'],
                    'dong_id' => $thuyenChuyen['dong_id'],
                    'ban_chuyen_trach_id' => $thuyenChuyen['ban_chuyen_trach_id'],
                    'du_hoc' => $thuyenChuyen['du_hoc'],
                    'quoc_gia' => $thuyenChuyen['quoc_gia'],
                    'active' => $thuyenChuyen['active'],
                    'ghi_chu' => $thuyenChuyen['ghi_chu']
                ];
            }
        }
        $formData['van_thus'] = isset($formData['van_thus'])?$formData['van_thus']:'';

        $this->merge($formData);

        return $this->all();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'ten'       => 'required|max:50',
            'ten_thanh_id' => 'required',
            'ngay_thang_nam_sinh' => 'required',
            'ngay_rua_toi' => 'required'
        ];
    }
}
