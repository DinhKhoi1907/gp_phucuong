<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Http\Common\Tables;
use DB;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\GiaoXu;

class GiaoPhanHatXu extends BaseModel
{
    use SoftDeletes;

    /**
     * @var string
     */
    protected $table = DB_PREFIX . 'giaophan_hat_xus';

    public function giaoXu()
    {
        return $this->hasOne(GiaoXu::class, $this->primaryKey, 'giao_xu_id');
    }

    public function getNameAttribute($value) {
        return $this->giaoXu->name;
    }

    public static function insertByGiaoHatId(
        $giaoPhanId = null,
        $giaoHatId = null,
        $giaoXuId = null,
        $active = 1
    ) {
        $gpId = (int)$giaoPhanId;
        $hatId = (int)$giaoHatId;
        $xuId = (int)$giaoXuId;
        $active = (int)$active;

        if ($gpId && $hatId && $xuId) {
            DB::insert('insert into ' . Tables::$giaophan_hat_xus . ' (giao_phan_id, giao_hat_id, giao_xu_id, active) values (?, ?, ?, ?)',
                [$gpId, $hatId, $xuId, $active]);
        }
    }

    public static function fcDeleteByGiaoPhanId($giaoPhanId = null)
    {
        $giaoPhanId = (int)$giaoPhanId;

        if ($giaoPhanId) {
            return DB::delete("delete from " . Tables::$giaophan_hat_xus . " where giao_phan_id = '" . $giaoPhanId . "'");
        }
    }
}
