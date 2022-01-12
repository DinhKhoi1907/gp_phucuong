<?php

namespace App\Http\Controllers\Api\Admin\Services;

use App\Http\Controllers\Api\Admin\Services\Contracts\BaseModel;
use App\Http\Controllers\Api\Admin\Services\Contracts\LinhMucModel;
use App\Http\Resources\LinhMucs\LinhmucResource;
use App\Http\Resources\LinhMucBangCaps\LinhMucBangCapCollection;
use App\Http\Resources\LinhMucChucThanhs\LinhMucChucThanhCollection;
use App\Http\Resources\LinhMucVanThus\LinhMucVanThuCollection;
use App\Http\Resources\LinhMucThuyenChuyens\LinhMucThuyenChuyenCollection;
use App\Models\BanChuyenTrach;
use App\Models\ChucVu;
use App\Models\CoSoGiaoPhan;
use App\Models\Dong;
use App\Models\GiaoXu;
use App\Models\Linhmuc;
use App\Models\LinhmucBangcap;
use App\Models\LinhmucBoNhiem;
use App\Models\LinhmucChucthanh;
use App\Models\LinhmucThuyenchuyen;
use App\Models\LinhmucVanthu;
use App\Models\Thanh;
use DB;

final class LinhMucService implements BaseModel, LinhMucModel
{
    /**
     * @var Linhmuc|null
     */
    private $model = null;

    /**
     * @author : dtphi .
     * AdminService constructor.
     */
    public function __construct()
    {
        $this->model = new Linhmuc();
    }

    public function apiGetList(array $options = [], $limit = 15)
    {
        // TODO: Implement apiGetList() method.
        $query = $this->apiGetLinhmucs($options);

        return $query->paginate($limit);
    }

    public function apiGetResourceCollection(array $options = [], $limit = 15)
    {
        // TODO: Implement apiGetResourceCollection() method.
        return null;
    }

    public function apiGetDetail($id = null)
    {
        // TODO: Implement apiGetDetail() method.
        $this->model = $this->model->findOrFail($id);

        return $this->model;
    }

    /**
     * @author : dtphi .
     * @param null $id
     * @return LinhmucResource
     */
    public function apiGetResourceDetail($id = null)
    {
        // TODO: Implement apiGetResourceDetail() method.
        return new LinhmucResource($this->apiGetDetail($id));
    }

    /**
     * @author : dtphi .
     * @param array $data
     * @return Admin|bool|null
     */
    public function apiInsertOrUpdate(array $data = [])
    {
        // TODO: Implement apiInsertOrUpdate() method.
        $this->model->fill($data);

        /**
         * Save user with transaction to make sure all data stored correctly
         */
        DB::beginTransaction();

        if (!$this->model->save()) {
            DB::rollBack();

            return false;
        }

        DB::commit();

        return $this->model;
    }

    public function apiGetLinhmucs($data = array(), $limit = 15)
    {
        $query = $this->model->select()
            ->orderBy('id', 'DESC');

        return $query;
    }

    public function apiInsert($data = [])
    {
        /**
         * Save user with transaction to make sure all data stored correctly
         */
        DB::beginTransaction();

        $this->model->fill($data);

        if ($this->model->save()) {
            $linhmucId = $this->model->id;

            if (isset($data['bang_caps']) && !empty($data['bang_caps'])) {
                foreach ($data['bang_caps'] as $bangcap) {
                    LinhmucBangcap::insertByLinhmucId($linhmucId, $bangcap['name'], $bangcap['type'],
                        $bangcap['ghi_chu'], $bangcap['active']);
                }
            }

            if (isset($data['chuc_thanhs']) && !empty($data['chuc_thanhs'])) {
                foreach ($data['chuc_thanhs'] as $chucThanh) {
                    LinhmucChucthanh::insertByLinhmucId($linhmucId, $chucThanh['chuc_thanh_id'],
                        $chucThanh['ngay_thang_nam_chuc_thanh'], $chucThanh['noi_thu_phong'],
                        $chucThanh['nguoi_thu_phong'],
                        $chucThanh['active'], $chucThanh['ghi_chu']);
                }
            }

            if (isset($data['thuyen_chuyens']) && !empty($data['thuyen_chuyens'])) {
                foreach ($data['thuyen_chuyens'] as $thCh) {
                    LinhmucThuyenchuyen::insertByLinhmucId($linhmucId, $thCh['from_giao_xu_id'],
                        $thCh['from_chuc_vu_id'], $thCh['from_date'], $thCh['duc_cha_id'], $thCh['to_date'],
                        $thCh['chuc_vu_id'], $thCh['giao_xu_id'], $thCh['co_so_gp_id'], $thCh['dong_id'],
                        $thCh['ban_chuyen_trach_id'], $thCh['du_hoc'], $thCh['quoc_gia'], $thCh['active'],
                        $thCh['ghi_chu']);
                }
            }

            if (isset($data['van_thus']) && !empty($data['van_thus'])) {
                foreach ($data['van_thus'] as $vanThu) {
                    LinhmucVanthu::insertByLinhmucId($linhmucId, $vanThu['title'], $vanThu['type'], $vanThu['active'],
                        $vanThu['ghi_chu']);
                }
            }

            if (isset($data['bo_nhiems']) && !empty($data['bo_nhiems'])) {
                foreach ($data['bo_nhiems'] as $boNhiem) {
                    LinhmucBoNhiem::insertByLinhmucId($linhmucId, $boNhiem['chuc_vu_id'], $boNhiem['cong_viec'],
                        $boNhiem['cong_viec_tu_ngay'], $boNhiem['cong_viec_tu_thang'], $boNhiem['cong_viec_tu_nam'],
                        $boNhiem['cong_viec_den_ngay'], $boNhiem['cong_viec_den_thang'], $boNhiem['cong_viec_den_nam'],
                        $boNhiem['active']);
                }
            }
        } else {
            DB::rollBack();

            return false;
        }

        DB::commit();

        return $this->model;
    }

    private function _addBoNhiem($linhmucId, $boNhiem)
    {
        LinhmucBoNhiem::insertByLinhmucId($linhmucId, $boNhiem['chuc_vu_id'], $boNhiem['cong_viec'],
            $boNhiem['cong_viec_tu_ngay'], $boNhiem['cong_viec_tu_thang'], $boNhiem['cong_viec_tu_nam'],
            $boNhiem['cong_viec_den_ngay'], $boNhiem['cong_viec_den_thang'], $boNhiem['cong_viec_den_nam'],
            $boNhiem['active']);
    }

    private function _removeBoNhiem($linhmucId, $boNhiem)
    {
        LinhmucBoNhiem::fcDeleteById($linhmucId, $boNhiem['id']);
    }

    private function _updateLinhMuc($model, $data)
    {
        $linhmucId = $model->id;

        LinhmucBangcap::fcDeleteByLinhmucId($linhmucId);
        if (isset($data['bang_caps']) && !empty($data['bang_caps'])) {
            foreach ($data['bang_caps'] as $bangcap) {
                LinhmucBangcap::insertByLinhmucId($linhmucId, $bangcap['name'], $bangcap['type'],
                    $bangcap['ghi_chu'], $bangcap['active']);
            }
        }

        LinhmucChucthanh::fcDeleteByLinhmucId($linhmucId);
        if (isset($data['chuc_thanhs']) && !empty($data['chuc_thanhs'])) {
            foreach ($data['chuc_thanhs'] as $chucThanh) {
                LinhmucChucthanh::insertByLinhmucId($linhmucId, $chucThanh['chuc_thanh_id'],
                    $chucThanh['ngay_thang_nam_chuc_thanh'], $chucThanh['noi_thu_phong'],
                    $chucThanh['nguoi_thu_phong'],
                    $chucThanh['active'], $chucThanh['ghi_chu']);
            }
        }

        LinhmucThuyenchuyen::fcDeleteByLinhmucId($linhmucId);
        if (isset($data['thuyen_chuyens']) && !empty($data['thuyen_chuyens'])) {
            foreach ($data['thuyen_chuyens'] as $thCh) {
                LinhmucThuyenchuyen::insertByLinhmucId($linhmucId, $thCh['from_giao_xu_id'],
                    $thCh['from_chuc_vu_id'], $thCh['from_date'], $thCh['duc_cha_id'], $thCh['to_date'],
                    $thCh['chuc_vu_id'], $thCh['giao_xu_id'], $thCh['co_so_gp_id'], $thCh['dong_id'],
                    $thCh['ban_chuyen_trach_id'], $thCh['du_hoc'], $thCh['quoc_gia'], $thCh['active'],
                    $thCh['ghi_chu']);
            }
        }

        LinhmucVanthu::fcDeleteByLinhmucId($linhmucId);
        if (isset($data['van_thus']) && !empty($data['van_thus'])) {
            foreach ($data['van_thus'] as $vanThu) {
                LinhmucVanthu::insertByLinhmucId($linhmucId, $vanThu['title'], $vanThu['type'], $vanThu['active'],
                    $vanThu['ghi_chu']);
            }
        }
    }

    public function apiUpdate($model, $data = [])
    {
        /**
         * Save user with transaction to make sure all data stored correctly
         */
        DB::beginTransaction();
        
        if ($data['action'] == 'add.bo.nhiem') {
            $this->_addBoNhiem($data['id'], $data['bo_nhiem']);
        } elseif ($data['action'] == 'remove.bo.nhiem') {
            $this->_removeBoNhiem($data['id'], $data['bo_nhiem']);
        } else {
            $model->fill($data);
            if ($model->save()) {
                $this->_updateLinhMuc($model, $data);
            } else {
                DB::rollBack();

                return false;
            }
        }

        DB::commit();

        return $model;
    }

    public function apiGetGiaoXuList($data = [])
    {
        $model = new GiaoXu();

        $query = $model->select()
            ->where('type', 'giaoxu')
            ->orderBy('name', 'DESC');

        return $query->get();
    }

    public function apiGetThanhList($data = [])
    {
        $model = new Thanh();

        $query = $model->select()
            ->orderBy('name', 'DESC');

        return $query->get();
    }

    public function apiGetChucVuList($data = [])
    {
        $model = new ChucVu();

        $query = $model->select()
            ->orderBy('name', 'DESC');

        return $query->get();
    }

    public function apiGetDucChaList($data = [])
    {
        $model = new Linhmuc();
        $query = $model->select()
            ->where('is_duc_cha', '=', 1)
            ->orderBy('ten', 'DESC');

        return $query->get();
    }

    public function apiGetCoSoGiaoPhanList($data = [])
    {
        $model = new CoSoGiaoPhan();
        $query = $model->select()
            ->orderBy('name', 'DESC');

        return $query->get();
    }

    public function apiGetDongList($data = [])
    {
        $model = new Dong();
        $query = $model->select()
            ->orderBy('name', 'DESC');

        return $query->get();
    }

    public function apiGetBanChuyenTrachList($data = [])
    {
        $model = new BanChuyenTrach();
        $query = $model->select()
            ->orderBy('name', 'DESC');

        return $query->get();
    }

    public function apiUpdateBangCap($data = []) 
    {
        $hat = LinhMucBangCap::updateOrCreate(
            [
                'id' => $data['id'], 
                'linh_muc_id' => $data['linhMucId']
            ],
            [
                'name' => $data['name'],
                'type' => $data['type'],
                'active' => $data['active'],
                'ghi_chu' => $data['ghi_chu']
            ]
        );

        return new LinhMucBangCapCollection(LinhMucBangCap::where('linh_muc_id', $data['linhMucId'])->get());
    }

    public function apiUpdateChucThanh($data = []) 
    {
        $hat = LinhMucChucThanh::updateOrCreate(
            [
                'id' => $data['id'], 
                'linh_muc_id' => $data['linhMucId']
            ],
            [
                'chuc_thanh_id' => $data['chuc_thanh_id'],
                'ngay_thang_nam_chuc_thanh' => $data['ngay_thang_nam_chuc_thanh'],
                'noi_thu_phong' => $data['noi_thu_phong'],
                'nguoi_thu_phong' => $data['nguoi_thu_phong'],
                'active' => $data['active'],
                'ghi_chu' => $data['ghi_chu']
            ]
        );

        return new LinhMucChucThanhCollection(LinhMucChucThanh::where('linh_muc_id', $data['linhMucId'])->get());
    }

    public function apiUpdateVanThu($data = []) 
    {
        $hat = LinhMucVanThu::updateOrCreate(
            [
                'id' => $data['id'], 
                'linh_muc_id' => $data['linhMucId']
            ],
            [
                'title' => $data['title'],
                'type' => $data['type'],
                'active' => $data['active'],
                'ghi_chu' => $data['ghi_chu']
            ]
        );

        return new LinhMucVanThuCollection(LinhMucVanThu::where('linh_muc_id', $data['linhMucId'])->get());
    }

    public function apiUpdateThuyenChuyen($data = []) 
    {
        $hat = LinhMucThuyenChuyen::updateOrCreate(
            [
                'id' => $data['id'], 
                'linh_muc_id' => $data['linhMucId']
            ],
            [
                'from_giao_xu_id' => $data['from_giao_xu_id'],
                'from_chuc_vu_id' => $data['from_chuc_vu_id'],
                'from_date' => $data['from_date'],
                'duc_cha_id' => $data['duc_cha_id'],
                'to_date' => $data['to_date'],
                'chuc_vu_id' => $data['chuc_vu_id'],
                'giao_xu_id' => $data['giao_xu_id'],
                'co_so_gp_id' => $data['co_so_gp_id'],
                'dong_id' => $data['dong_id'],
                'ban_chuyen_trach_id' => $data['ban_chuyen_trach_id'],
                'du_hoc' => $data['du_hoc'],
                'quoc_gia' => $data['quoc_gia'],
                'active' => $data['active'],
                'ghi_chu' => $data['ghi_chu']
            ]
        );

        return new LinhMucThuyenChuyenCollection(LinhMucThuyenChuyen::where('linh_muc_id', $data['linhMucId'])->get());
    }
}
