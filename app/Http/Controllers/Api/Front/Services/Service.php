<?php

namespace App\Http\Controllers\Api\Front\Services;

use DB;
use App\Models\Dong;
use App\Models\Thanh;
use App\Models\ChucVu;
use App\Models\GiaoXu;
use App\Models\NgayLe;
use App\Models\GiaoHat;
use App\Models\Linhmuc;
use App\Models\Category;
use App\Models\GiaoPhan;
use App\Http\Common\Tables;
use App\Models\GiaoPhanHat;
use App\Models\Information;
use App\Models\LinhmucTemp;
use Illuminate\Support\Arr;
use App\Models\CoSoGiaoPhan;
use App\Models\GiaoPhanHatXu;
use App\Models\LinhmucVanthu;
use App\Models\BanChuyenTrach;
use App\Models\LinhmucBangcap;
use yii\console\widgets\Table;
use App\Models\LinhmucChucthanh;
use App\Models\LinhmucThuyenchuyen;
use App\Models\LinhmucThuyenchuyenTemp;
use App\Http\Resources\LinhMucs\LinhmucResource;
use App\Http\Controllers\Api\Front\Services\Contracts\BaseModel;

class Service implements BaseModel
{
	/**
	 * [$modelNewGroup description]
	 * @var null
	 */
	private $modelNewGroup = null;

	/**
	 * @author : dtphi .
	 * AdminService constructor.
	 */
	public function __construct()
	{
		$this->modelInfo     = new Information();
		$this->modelNewGroup = new Category();
		$this->modelGiaoXu   = new GiaoXu();
    $this->modelLinhMucChucThanh = new LinhMucChucThanh();
		$this->modelGiaoPhan = new GiaoPhan();
		$this->modelGiaoHat = new GiaoHat();
		$this->modelChucVu = new ChucVu();
		$this->modelLinhMucThuyenChuyen = new LinhmucThuyenchuyen();
		$this->modelNgayLe = new NgayLe();
		$this->modelPhanHatXu = new GiaoPhanHatXu();
    $this->modelThanh = new Thanh();
    $this->modelDong = new Dong();
    $this->modelLinhmucTemp = new LinhmucTemp();
    $this->modelThuyenChuyen = new LinhmucThuyenchuyen();
    $this->modelThuyenChuyenTemp = new LinhmucThuyenchuyenTemp();
	}

	/**
	 * @author: dtphi .
	 * @param array $options
	 * @param int $limit
	 * @return AdminCollection
	 */
	public function apiGetList(array $options = [], $limit = 15)
	{
	}

	/**
	 * author : dtphi .
	 * @param array $options
	 * @param int $limit
	 * @return InformationCollection
	 */
	public function apiGetResourceCollection(array $options = [], $limit = 15)
	{
		// TODO: Implement apiGetResourceCollection() method.
	}

	/**
	 * @author : dtphi .
	 * @return array
	 */
	public function apiGetNewsGroupTrees()
	{
		// TODO: Implement apiGetNewsGroupTrees() method.
		$query = $this->modelNewGroup
			->select('id', 'father_id', 'newsgroupname', 'displays', 'sort')
			->orderBySortAsc();

		return [
			'total' => $query->count(),
			'data'  => $query->get()->toArray()
		];
	}

	public function getMenuCategories($parentId = 0)
	{
		$query = $this->modelNewGroup->select()
			->lfJoinDescription()
			->filterParentId($parentId)
			->filterActiveStatus()
			->orderByAscSort()
			->orderByAscParentId();

		return $query->get();
	}

	public function apiGetCategoryById($categoryId = 0)
	{
		$query = $this->modelNewGroup->select()
			->lfJoinDescription()
			->filterById($categoryId)
			->filterActiveStatus();

		return $query->first();
	}

	public function apiGetMenuCategoryIds($parentId = 0)
	{
		$query = DB::table('pc_categorys')->select('category_id')
			->where('pc_categorys.parent_id', (int)$parentId)
			->where('pc_categorys.status', '1');

		return $query->get()->toArray();
	}

	public function getMenuCategoriesToLayout($layoutId = 1)
	{
		$query = $this->modelNewGroup->select()
			->lfJoinDescription()
			->lfJoinToLayout()
			->filterLayoutId($layoutId)
			->filterActiveStatus()
			->orderByAscSort()
			->orderByAscParentId();

		return $query->get();
	}

	public function apiGetLatestInfos($limit = 5)
	{
		$query = $this->modelInfo->select('information_id')
			->orderByDescDateAvailable()
			->limit($limit);

		return $query->get();
	}

	public function apiGetPopularInfos($limit = 5)
	{
		$query = $this->modelInfo->select('information_id')
			->orderByDescViewed()
			->limit($limit);

		return $query->get();
	}

	public function apiGetInfoListByIds($data = array())
	{
		$infoType = 1;
		if (isset($data['infoType'])) {
			$infoType = (int)$data['infoType'];
		}

		$query = DB::table(Tables::$informations)->select(
			[
				'date_available',
				'sort_description',
				'image',
				Tables::$informations . '.information_id',
				'information_type',
				'name',
				'name_slug',
				'viewed',
				'vote',
				'tag'
			]
		)
			->leftJoin(
				Tables::$information_descriptions,
				Tables::$informations . '.information_id',
				'=',
				Tables::$information_descriptions . '.information_id'
			)
			->where('status', '=', '1')
			->where('information_type', '=', $infoType);

		if (isset($data['information_ids'])) {
			$query->whereIn(Tables::$informations . '.information_id', $data['information_ids']);
		}

		$limit = 20;
		if (isset($data['limit'])) {
			$limit = (int)$data['limit'];
		}

		$query->orderByDesc('sort_order');
		$query->orderByDesc('date_available');

		return $query->get();
	}

	public function apiGetNgayLeList($data = array(), $limit = 5)
	{
		$query = $this->modelNgayLe	
			->whereNotNull('hanh')->Where('hanh', '!=', '');
		return $query->paginate($limit);
	}

	public function apiGetDetailNgayLe($id = null)
	{
		return NgayLe::findOrFail($id);
	}

	public function apiGetGiaoXuList($data = array(), $limit = 5)
	{
		$query = $this->modelGiaoXu->with(['linhmucthuyenchuyens' => function ($q) {
			$q->where('chuc_vu_active', 1)->select('linh_muc_id', 'chuc_vu_id', 'giao_xu_id')->with('linhMuc', 'chucVu')->orderBy('chuc_vu_id', 'asc');
		}]);

		return $query->paginate($limit);
	}

	public function apiGetDetailGiaoXu($id)
	{
		return GiaoXu::findOrFail($id);
	}

	public function apiGetLinhMucListByGiaoXuId($giaoXuId = null)
	{
		$linhMucs = LinhmucThuyenchuyen::where(Tables::$linhmuc_thuyenchuyens . '.giao_xu_id', $giaoXuId)
			->orderBy('from_date', 'asc')->get();

		return $linhMucs;
	}

	public function apiGetLinhMucChanhXuByGiaoXuId($giaoXuId = null)
	{
		$linhMuc = LinhmucThuyenchuyen::where(Tables::$linhmuc_thuyenchuyens . '.giao_xu_id', $giaoXuId)
		->where(Tables::$linhmuc_thuyenchuyens . '.chuc_vu_id', 1)
		->where(Tables::$linhmuc_thuyenchuyens . '.chuc_vu_active', 1)
		->orderByDesc('from_date')->get();
		return $linhMuc;
	}

	public function apiGetLinhMucPhoXuByGiaoXuId($giaoXuId = null)
	{
		$linhMuc = LinhmucThuyenchuyen::where(Tables::$linhmuc_thuyenchuyens . '.giao_xu_id', $giaoXuId)
			->where(Tables::$linhmuc_thuyenchuyens . '.chuc_vu_id', 4)
			->where(Tables::$linhmuc_thuyenchuyens . '.chuc_vu_active', 1)
			->orderByDesc('from_date')->limit(3)
			->get();

		return $linhMuc;
	}

	/// LINH MUC
	private function __apiGetLinhmucs($data = array(), $limit = 15)
	{
		$query = DB::table(Tables::$linhmucs)->leftJoin(
			Tables::$thanhs,
			Tables::$linhmucs . '.ten_thanh_id',
			'=',
			Tables::$thanhs . '.id'
		)->leftJoin(Tables::$giao_xus, Tables::$linhmucs . '.giao_xu_id', '=', Tables::$giao_xus . '.id')
			->leftJoin(Tables::$giao_hats, Tables::$giao_xus . '.giao_hat_id', '=', Tables::$giao_hats . '.id')
			->leftJoin(
				DB::raw("(select `t1`.`id`, `t1`.`linh_muc_id`, `t1`.`chuc_thanh_id`, `t1`.`ngay_thang_nam_chuc_thanh`
						from `pc_linhmuc_chucthanhs` t1
						INNER JOIN (
								SELECT max(`pc_linhmuc_chucthanhs`.`id`) as id
								FROM `pc_linhmuc_chucthanhs`
								GROUP BY `pc_linhmuc_chucthanhs`.`linh_muc_id`
						) t2
						ON `t1`.`id` = `t2`.`id`) max_linhmuc_chucthanhs"),
				'pc_linhmucs.id',
				'=',
				'max_linhmuc_chucthanhs.linh_muc_id'
			)
			->leftJoin(
				DB::raw("(select `t1`.`id`, `t1`.`linh_muc_id`,`chuc_vu_id`
						from `pc_linhmuc_thuyenchuyens` t1
						INNER JOIN (
								SELECT max(`pc_linhmuc_thuyenchuyens`.`id`) as id
								FROM `pc_linhmuc_thuyenchuyens`
								GROUP BY `pc_linhmuc_thuyenchuyens`.`linh_muc_id`
						) t2
						ON `t1`.`id` = `t2`.`id`) max_linhmuc_thuyenchuyens"),
				'pc_linhmucs.id',
				'=',
				'max_linhmuc_thuyenchuyens.linh_muc_id'
			)
			->leftJoin(Tables::$chuc_vus, 'max_linhmuc_thuyenchuyens' . '.chuc_vu_id', '=', Tables::$chuc_vus . '.id')
			->select(
				Tables::$chuc_vus . '.name as cvht_name',
				Tables::$linhmucs . '.id',
				Tables::$linhmucs . '.giao_xu_id',
				Tables::$linhmucs . '.ten',
				Tables::$linhmucs . '.image',
				Tables::$linhmucs . '.ngay_thang_nam_sinh',
				Tables::$giao_xus . '.name as gx_name',
				Tables::$giao_xus . '.dia_chi',
				Tables::$giao_hats . '.name as gh_name',
				Tables::$thanhs . '.name as th_name',
				'max_linhmuc_chucthanhs' . '.ngay_thang_nam_chuc_thanh',
			)->orderBy(Tables::$linhmucs . '.id', 'DESC');
		return $query;
	}

	public function apiGetLinhmucs($data = [])
	{
		$query = $this->modelLinhMuc->select() 
			->orderBy('id', 'DESC');
		return $query;
	}

	public function apiGetListLinhMuc($data = array(), $limit = 5)
	{
		// TODO: Implement apiGetList() method.
		$query = $this->apiGetLinhmucs($data);

		return $query->paginate($limit);
	}

	public function apiGetDetailLinhMuc($id = null)
	{
    $model = $this->modelLinhmucTemp->find($id);
    if(is_null($model)) {
      $model = $this->modelLinhMuc->find($id);
    }
		return $model;
	}

	private function __apiGetDetailLinhMuc($id = null)
	{
		/*lấy thông tin chi tiết linh mục*/
		$query = DB::table(Tables::$linhmucs)
			->leftJoin(Tables::$linhmuc_thuyenchuyens, Tables::$linhmuc_thuyenchuyens . '.linh_muc_id', '=', Tables::$linhmucs . '.id')
			->leftJoin(Tables::$thanhs, Tables::$thanhs . '.id', '=', Tables::$linhmucs . '.ten_thanh_id')
			->leftJoin(Tables::$giao_xus, Tables::$linhmucs . '.giao_xu_id', '=', Tables::$giao_xus . '.id')
			->leftJoin(
				DB::raw("(select `t1`.`id`, `t1`.`linh_muc_id`,`chuc_vu_id`
						from `pc_linhmuc_thuyenchuyens` t1
						INNER JOIN (
								SELECT max(`pc_linhmuc_thuyenchuyens`.`id`) as id
								FROM `pc_linhmuc_thuyenchuyens`
								GROUP BY `pc_linhmuc_thuyenchuyens`.`linh_muc_id`
						) t2
						ON `t1`.`id` = `t2`.`id`) max_linhmuc_thuyenchuyens"),
				'pc_linhmucs.id',
				'=',
				'max_linhmuc_thuyenchuyens.linh_muc_id'
			)
			->leftJoin(Tables::$chuc_vus, 'max_linhmuc_thuyenchuyens' . '.chuc_vu_id', '=', Tables::$chuc_vus . '.id')
			->leftJoin(Tables::$linhmuc_chucthanhs, Tables::$linhmuc_chucthanhs . '.linh_muc_id', '=', Tables::$linhmucs . '.id')
			->leftJoin(Tables::$giaophan_hat_xus, Tables::$giaophan_hat_xus . '.giao_xu_id', '=', Tables::$linhmucs . '.giao_xu_id')
			->leftJoin(Tables::$giaophans, Tables::$giaophans . '.id', '=', Tables::$giaophan_hat_xus . '.giao_phan_id')
			->where(Tables::$linhmucs . '.id', '=', $id)
			->select(
				Tables::$linhmucs . '.id',
				Tables::$linhmucs . '.ten',
				Tables::$thanhs . '.name as th_name',
				Tables::$linhmucs . '.image',
				Tables::$linhmucs . '.ngay_thang_nam_sinh as ngay_sinh',
				Tables::$linhmucs . '.noi_sinh',
				Tables::$linhmucs . '.ho_ten_cha',
				Tables::$linhmucs . '.ho_ten_me',
				Tables::$linhmucs . '.ngay_rua_toi',
				Tables::$linhmucs . '.ngay_them_suc',
				Tables::$linhmucs . '.so_cmnd',
				Tables::$linhmucs . '.ngay_cap_cmnd',
				Tables::$linhmucs . '.noi_cap_cmnd',
				Tables::$giao_xus . '.name as ten_xu',
				Tables::$chuc_vus . '.name as cvht_name',
				Tables::$linhmuc_chucthanhs . '.ngay_thang_nam_chuc_thanh as ngay_nhan_chuc',
				Tables::$giaophans . '.name as gp_name',
			)->first();

		/*Lấy ds chucvu thuộc linh mục hiện tại*/
		$query_other = DB::table(Tables::$linhmuc_thuyenchuyens)
			->leftJoin(Tables::$chuc_vus, Tables::$chuc_vus . '.id', '=', Tables::$linhmuc_thuyenchuyens . '.chuc_vu_id')
			->where(Tables::$linhmuc_thuyenchuyens . '.linh_muc_id', '=', $id)
			->whereColumn(Tables::$chuc_vus . '.id', '=', Tables::$linhmuc_thuyenchuyens . '.chuc_vu_id')
			->select(
				Tables::$chuc_vus . '.name as chucvu_name',
				Tables::$linhmuc_thuyenchuyens . '.from_date',
				Tables::$linhmuc_thuyenchuyens . '.to_date',
			);

		/* Lay ds linhmuc_chucthanh */
		$query_other_chucthanhs = DB::table(Tables::$linhmuc_chucthanhs)
			->where(Tables::$linhmuc_chucthanhs . '.linh_muc_id', '=', $id)
			->select(
				Tables::$linhmuc_chucthanhs . '.chuc_thanh_id',
				Tables::$linhmuc_chucthanhs . '.ngay_thang_nam_chuc_thanh',
				Tables::$linhmuc_chucthanhs . '.noi_thu_phong',
			);

		if ($query) {
			$query->ds_chuc_vu = ($query_other) ? $query_other->get() : [];
			$query->ds_chuc_thanh = ($query_other_chucthanhs) ? $query_other_chucthanhs->get() : [];
		}
		return $query;
	}

	public function apiGetListGiaoPhan()
	{ // List Giao Phan
		$query = $this->modelGiaoPhan->select()
			->orderBy('id', 'ASC')->get();

		return $query;
	}

	public function apiGetListGiaoHat($params)
	{ // List Giao Hat apiGetListGiaoXu

		if ($params != -1) {
			$id_giaophan = $this->modelGiaoPhan->find($params);
			$giao_hats = $id_giaophan->hats->load('giaohats');
		} else {
			$giao_hats = $this->modelGiaoHat->select('id', 'name')->orderBy('id', 'ASC')->get();
		}

		return $giao_hats;
	}

	public function apiGetListGiaoXu($request, $limit = 5)
	{ // List Giao Xu By Id
		if ($request->input('params') == null) {
			$list_giaoxu = $this->modelGiaoXu->where('type', 'giaoxu')->name($request)->with(['linhmucthuyenchuyens' => function ($q) {
				$q->where('chuc_vu_active', 1)->select('linh_muc_id', 'chuc_vu_id', 'giao_xu_id')->with('linhMuc', 'chucVu')->orderBy('chuc_vu_id', 'asc');
			}]);
		} else if ($request->input('query') == null) {
			$list_giaoxu = $this->modelPhanHatXu->where('giao_hat_id', $request->input('params'))->with(['giaoXu.linhmucthuyenchuyens' => function ($q) {
				$q->where('chuc_vu_active', 1)->select('linh_muc_id', 'chuc_vu_id', 'giao_xu_id')->with('linhMuc', 'chucVu')->orderBy('chuc_vu_id', 'asc');
			}]);
		} else {
			$list_giaoxu_query = $this->modelGiaoXu->where('type', 'giaoxu')->name($request)->orderBy('id', 'ASC')->pluck('id')->toArray();
			$list_giaoxu_params = $this->modelPhanHatXu->where('giao_hat_id', $request->input('params'))->pluck('giao_xu_id')->toArray();
			$list_giaoxu_merge = array_unique(array_merge($list_giaoxu_query, $list_giaoxu_params));

			$list_giaoxu = $this->modelGiaoXu->select()->whereIn('id', $list_giaoxu_merge)->orderBy('id', 'ASC')->with(['linhmucthuyenchuyens' => function ($q) {
				$q->where('chuc_vu_active', 1)->select('linh_muc_id', 'chuc_vu_id', 'giao_xu_id')->with('linhMuc', 'chucVu')->orderBy('chuc_vu_id', 'asc');
			}]);
		}
		return $list_giaoxu->paginate($limit);
	}
  public function getSubListGiaoHat() 
  {
    $query = $this->modelGiaoHat->select()->orderBy('id', 'ASC')->get();
    return $query;
  }

	public function apiGetListNgayLe($data = array(), $limit = 5)
	{ // List Ngay Le By Id
		$query = $this->modelNgayLe->select('id','ten_le','hanh');
		
		return $query->paginate($limit);
	}

	public function apiGetListChucVu()
	{
		$query = $this->modelChucVu->select('id', 'name')
			->orderBy('id', 'ASC')->get();
		return $query;
	}

	public function apiGetListLinhMucById($request)
	{
		if ($request->input('query') == null) {
			if ($request->input('id_giaohat') == null && $request->input('id_chucvu') != null) {
				$list_linhmuc_chucvu = $this->modelLinhMucThuyenChuyen->where('chuc_vu_id', $request->input('id_chucvu'))->orderBy('linh_muc_id', 'ASC')->distinct()->pluck('linh_muc_id')->toArray();
				$list_linhmucs = $this->modelLinhMuc->select()->whereIn('id', $list_linhmuc_chucvu)->orderBy('id', 'ASC')->paginate(5);
			} else if ($request->input('id_chucvu') == null && $request->input('id_giaohat') != null) {
				$linhmuc_by_giaohat = $this->modelGiaoHat->find($request->input('id_giaohat'))->giaoxus->load('linhmucs');
				$array_id_linhmuc_by_giaohat = [];
				foreach ($linhmuc_by_giaohat->toArray() as $key => $value) {

					foreach ($value['linhmucs'] as $key1 => $item) {

						$array_id_linhmuc_by_giaohat[] = $item['id'];
					}
				}
				$list_linhmucs = $this->modelLinhMuc->select()->whereIn('id', $array_id_linhmuc_by_giaohat)->orderBy('id', 'ASC')->paginate(5);
			} else {
				$array_id_linhmuc_by_chucvu = $this->modelLinhMucThuyenChuyen->where('chuc_vu_id', $request->input('id_chucvu'))->orderBy('linh_muc_id', 'ASC')->distinct()->pluck('linh_muc_id')->toArray();
				$linhmuc_by_giaohat = $this->modelGiaoHat->find($request->input('id_giaohat'))->giaoxus->load('linhmucs');
				$array_id_linhmuc_by_giaohat = [];
				foreach ($linhmuc_by_giaohat->toArray() as $key => $value) {
					foreach ($value['linhmucs'] as $key1 => $item) {
						$array_id_linhmuc_by_giaohat[] = $item['id'];
					}
				}
				$array_id_linhmuc = array_unique(array_merge($array_id_linhmuc_by_chucvu, $array_id_linhmuc_by_giaohat));
				$list_linhmucs = $this->modelLinhMuc->select()->whereIn('id', $array_id_linhmuc)->paginate(5);
			}
		} else {
			$linhmuc_query = $this->modelLinhMuc->query()->name($request)->pluck('id')->toArray();

			if (!$request->input('id_giaohat') && $request->input('id_chucvu')) {
				$list_linhmuc_chucvu = $this->modelLinhMucThuyenChuyen->where('chuc_vu_id', $request->input('id_chucvu'))->orderBy('linh_muc_id', 'ASC')->distinct()->pluck('linh_muc_id')->toArray();
				$merge_chucvu_query = array_unique(array_merge($list_linhmuc_chucvu, $linhmuc_query));

				$list_linhmucs = $this->modelLinhMuc->select()->whereIn('id', $merge_chucvu_query)->orderBy('id', 'ASC')->paginate(5);
			} else if (!$request->input('id_chucvu') && $request->input('id_giaohat')) {
				$linhmuc_by_giaohat = $this->modelGiaoHat->find($request->input('id_giaohat'))->giaoxus->load('linhmucs');
				$array_id_linhmuc_by_giaohat = [];
				foreach ($linhmuc_by_giaohat->toArray() as $key => $value) {

					foreach ($value['linhmucs'] as $key1 => $item) {

						$array_id_linhmuc_by_giaohat[] = $item['id'];
					}
				}
				$merge_giaohat_query = array_unique(array_merge($array_id_linhmuc_by_giaohat, $linhmuc_query));
				$list_linhmucs = $this->modelLinhMuc->select()->whereIn('id', $merge_giaohat_query)->orderBy('id', 'ASC')->paginate(5);
			} else {
				if ($request->input('id_chucvu')) {
					$array_id_linhmuc_by_chucvu = $this->modelLinhMucThuyenChuyen->where('chuc_vu_id', $request->input('id_chucvu'))->orderBy('linh_muc_id', 'ASC')->distinct()->pluck('linh_muc_id')->toArray();
				} else {
					$array_id_linhmuc_by_chucvu = [];
				}
				$array_id_linhmuc_by_giaohat = [];
				if ($request->input('id_giaohat')) {
					$linhmuc_by_giaohat = $this->modelGiaoHat->find($request->input('id_giaohat'))->giaoxus->load('linhmucs');
					foreach ($linhmuc_by_giaohat->toArray() as $key => $value) {
						foreach ($value['linhmucs'] as $key1 => $item) {
							$array_id_linhmuc_by_giaohat[] = $item['id'];
						}
					}
				}

				$array_id_linhmuc = array_unique(array_merge($array_id_linhmuc_by_chucvu, $array_id_linhmuc_by_giaohat, $linhmuc_query));
				$list_linhmucs = $this->modelLinhMuc->select()->whereIn('id', $array_id_linhmuc)->paginate(5);
			}
		}
		return $list_linhmucs;
	}

  public function apiGetThanhs()
  {
    $query = $this->modelThanh->select('id', 'name')
    ->orderBy('name', 'asc')->get();
    return $query;
  }

  public function apiGetDongs()
  {
    $query = $this->modelDong->select('id', 'name')
    ->orderBy('name', 'asc')->get();
    return $query;
  }

  public function apiUpdateLinhMucTemp($data = []){
      $linhmuc_temp = LinhmucTemp::updateOrCreate(
        ['id' => $data['id']],
        [
          'id' => $data['id'],
          'ten_thanh_id' => $data['ten_thanh_id'],
          'ten' => $data['ten'],
          'ngay_thang_nam_sinh' => $data['ngay_thang_nam_sinh'],
          'noi_sinh' => $data['dia_chi'],
          'giao_xu_id' => $data['giao_xu'],
          'ho_ten_cha' => $data['ho_ten_cha'],
          'ho_ten_me' => $data['ho_ten_me'],
          'noi_rua_toi' => $data['noi_rua_toi'],
          'ngay_rua_toi' => $data['ngay_rua_toi'],
          'noi_them_suc' => $data['noi_them_suc'],
          'ngay_them_suc' => $data['ngay_them_suc'],
          'tieu_chung_vien' => $data['tieu_chung_vien'],
          'ngay_tieu_chung_vien' => $data['ngay_tieu_chung_vien'],
          'dai_chung_vien' => $data['dai_chung_vien'],
          'ngay_dai_chung_vien' => $data['ngay_dai_chung_vien'],
          'image' => $data['image'],
          'so_cmnd' => $data['so_cmnd'],
          'ngay_cap_cmnd' => $data['ngay_cap_cmnd'],
          'noi_cap_cmnd' => $data['noi_cap_cmnd'],
          'phone' => $data['phone'],
          'email' => $data['email'],
          'trieu_dong' => $data['trieu_dong'],
          'ten_dong_id' => $data['ten_dong_id'],
          'ngay_trieu_dong' => $data['ngay_trieu_dong'],
          'ngay_khan' => $data['ngay_khan'],
          'is_duc_cha' => $data['is_duc_cha'],
          'cham_ngon' => $data['cham_ngon'],
          'sinh_giao_xu' => $data['sinh_giao_xu'],
        ]);
  }

  public function apiGetThuyenChuyen($infoId = null)
  {
    $model = LinhmucThuyenchuyenTemp::where('linh_muc_id', $infoId)->first();
    if(!is_null($model)) {
      $query = $this->modelThuyenChuyenTemp->select()
      ->where('linh_muc_id', $infoId)
      ->where('is_bo_nhiem', '!=', 1)
      ->orderBy('from_date', 'DESC')
      ->get();
    }else {
        $model_linhmucs = LinhmucThuyenchuyen::where('linh_muc_id', $infoId);
        $array_linhmucs = $model_linhmucs->get()->toArray();
        foreach ($array_linhmucs as $info) {
          LinhmucThuyenchuyenTemp::insert($info);
        $query = $this->modelThuyenChuyenTemp->select()
        ->where('linh_muc_id', $infoId)
        ->where('is_bo_nhiem', '!=', 1)
        ->orderBy('from_date', 'DESC')
        ->get();
      }
     
    }
    return $query;
}

  // DROPDOWN_CATEGORIES
  public function apiGetGiaoXusList($data = [])
  {
    $model = new GiaoXu();
    $query = $model->select()
    ->where('type', 'giaoxu')
    ->orderBy('name', 'ASC');
    return $query->get();
  }
  public function apiGetThanhsList($data = [])
  {
    $model = new Thanh();
    $query = $model->select()
      ->orderBy('name', 'ASC');
    return $query->get();
  }
  public function apiGetChucVusList($data = [])
  {
    $model = new ChucVu();
    $query = $model->select()
      ->orderBy('sort_id', 'ASC');
    return $query->get();
  }
  public function apiGetDucChasList($data = [])
  {
    $model = new Linhmuc();
    $query = $model->select()
      ->where('is_duc_cha', '=', 1)
      ->orderBy('ten', 'ASC');
    return $query->get();
  }
  public function apiGetCoSoGiaoPhansList($data = [])
  {
    $model = new CoSoGiaoPhan();
    $query = $model->select()
      ->where('coso_giaophan', '=', 1)
      ->orderBy('name', 'ASC');
    return $query->get();
  }
  public function apiGetDongsList($data = [])
  {
    $model = new Dong();
    $query = $model->select()
      ->orderBy('name', 'ASC');
    return $query->get();
  }
  public function apiGetBanChuyenTrachsList($data = [])
  {
    $model = new BanChuyenTrach();
    $query = $model->select()
      ->orderBy('name', 'ASC');
    return $query->get();
  }
  public function apiGetCongDoanNgoaiGiaoPhansList($data = [])
  {
    $model = new CoSoGiaoPhan();
    $query = $model->select()
      ->where('active', 1)
      ->where('coso_giaophan', '=', 0)
      ->orderBy('name', 'ASC');
    return $query->get();
  }
  public function apiAddThuyenChuyen($id = null, $data) 
  {
    if ($data->dia_diem_tu_nam == null || $data->dia_diem_tu_thang == null || $data->dia_diem_tu_ngay == null) {
      $data->from_date = null;
    } else {
      $data->from_date = $data->dia_diem_tu_nam . '-' . $data->dia_diem_tu_thang . '-' . $data->dia_diem_tu_ngay;
    }

    if ($data->dia_diem_den_nam == null || $data->dia_diem_den_thang == null || $data->dia_diem_den_ngay == null) {
      $data->to_date = null;
    } else {
      $data->to_date = $data->dia_diem_den_nam . '-' . $data->dia_diem_den_thang . '-' . $data->dia_diem_den_ngay;
    }

    $hat = LinhmucThuyenchuyenTemp::create(
      [
        'linh_muc_id' => $id,
        'giao_xu_id' => $data->thuyenChuyen->select_giao_xu,
        'chuc_vu_id' => $data->select_chuc_vu,
        'from_giao_xu_id' => 0,
        'from_chuc_vu_id' => 0,
        'from_date' => $data->from_date,
        'to_date' => $data->to_date,
        'duc_cha_id' => '',
        'co_so_gp_id' => $data->thuyenChuyen->select_csgp,
        'dong_id' => $data->thuyenChuyen->select_dong,
        'ban_chuyen_trach_id' => $data->thuyenChuyen->select_bct,
        'du_hoc' => 0,
        'chuc_vu_active' => $data->select_status,
      ]
    );
  }
}
