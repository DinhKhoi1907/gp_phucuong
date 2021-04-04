<?php

namespace App\Http\Controllers\Api\Admin\Services;

use App\Http\Controllers\Api\Admin\Services\Contracts\BaseModel;
use App\Http\Controllers\Api\Admin\Services\Contracts\NewsGroupModel;
use App\Http\Resources\NewsGroups\NewsGroupCollection;
use App\Http\Resources\NewsGroups\NewsGroupResource;
use App\Models\NewsGroup;
use App\Models\Category;
use App\Models\CategoryDescription;
use App\Models\CategoryPath;

use DB;

final class NewsGroupService implements BaseModel, NewsGroupModel
{
    /**
     * @var Admin|null
     */
    private $model = null;

    private $modelDes = null;

    private $modelPath = null;

    /**
     * @author : dtphi .
     * AdminService constructor.
     */
    public function __construct()
    {
        $this->model = new Category();
        $this->modelDes = new CategoryDescription();
        $this->modelPath = new CategoryPath();
    }

    /**
     * @author : dtphi .
     * @param array $options
     * @param int $limit
     * @return mixed
     */
    public function apiGetList(array $options = [], $limit = 15)
    {
        // TODO: Implement apiGetList() method.
        $query = $this->apiGetNewsGroupTrees($options, $limit);

        return $query->paginate($limit);
    }

    /**
     * author : dtphi .
     * @param array $options
     * @param int $limit
     * @return NewsGroupCollection
     */
    public function apiGetResourceCollection(array $options = [], $limit = 15)
    {
        // TODO: Implement apiGetResourceCollection() method.
        return new NewsGroupCollection($this->apiGetList($options, $limit));
    }

    /**
     * @author : dtphi .
     * @param null $id
     * @return NewsGroup|null
     */
    public function apiGetDetail($id = null)
    {
        // TODO: Implement apiGetDetail() method.
        $query = DB::table(DB_PREFIX . 'categorys AS c')->select('*', DB::raw("(
        SELECT
            GROUP_CONCAT(
                cd1.`name`
                ORDER BY
                    `level` SEPARATOR '&nbsp;&nbsp;&gt;&nbsp;&nbsp;'
            )
        FROM
            pc_category_paths cp
        LEFT JOIN pc_category_descriptions cd1 ON (
            cp.path_id = cd1.category_id
            AND cp.category_id != cp.path_id
        )
        WHERE
            cp.category_id = c.category_id
        GROUP BY
            cp.category_id
    ) AS path"))
        ->distinct()
    ->leftJoin(DB_PREFIX . 'category_descriptions AS cd2', 'c.category_id', '=', 'cd2.category_id')
    ->where('c.category_id', $id);

        return $query->first();
    }

    /**
     * @author : dtphi .
     * @param null $id
     * @return NewsGroupResource
     */
    public function apiGetResourceDetail($id = null)
    {
        // TODO: Implement apiGetResourceDetail() method.
    
        return new NewsGroupResource($this->apiGetDetail($id));
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

        try{
            if($this->model->save()) {
                $data['category_id'] = $this->model->category_id;
                $this->modelDes->fill($data);
                
                $this->modelDes->save();

                /*MySQL Hierarchical Data Closure Table Pattern*/
                $level = 0;
                $resultPaths = $this->modelPath->where('category_id', $data['parent_id'] )
                ->orderBy('level', 'ASC')->get();

                foreach ($resultPaths as $key => $resultPath) {
                    DB::insert('insert into ' . DB_PREFIX . 'category_paths (category_id, path_id, level) values (?, ?, ?)', [$data['category_id'], $resultPath['path_id'], $level]);

                    $level++;
                }

                DB::insert('insert into ' . DB_PREFIX . 'category_paths (category_id, path_id, level) values (?, ?, ?)', [$data['category_id'], $data['category_id'], $level]);

                if (isset($data['layout_id'])) {
                    DB::insert('insert into ' . DB_PREFIX . 'category_to_layouts (category_id, layout_id) values (?, ?)', [$data['category_id'], $data['layout_id']]);
                }

            } else {
                DB::rollBack();

                return false;
            }

        } catch(\Exceptions $e) {
            DB::rollBack();

            return false;
        }

        DB::commit();

        return $this->model;
    }

    public function apiGetNewsGroupTrees($data = array()) {

        $query = $this->modelPath->select(DB_PREFIX.'category_paths.category_id AS category_id', 'cate1.parent_id', 'cate1.sort_order', DB::raw("group_concat(cd1.`name` ORDER BY pc_category_paths.level SEPARATOR ' ->> ') AS category_name"))->groupBy(DB_PREFIX.'category_paths.category_id')
        ->leftJoin(DB_PREFIX . 'categorys AS cate1', DB_PREFIX.'category_paths.category_id', '=', 'cate1.category_id')
        ->leftJoin(DB_PREFIX . 'categorys AS cate2', DB_PREFIX.'category_paths.path_id', '=', 'cate2.category_id')
        ->leftJoin(DB_PREFIX . 'category_descriptions AS cd1', DB_PREFIX.'category_paths.path_id', '=', 'cd1.category_id')
        ->leftJoin(DB_PREFIX . 'category_descriptions AS cd2', DB_PREFIX.'category_paths.category_id', '=', 'cd2.category_id');

        /*if (!empty($data['filter_name'])) {
            $sql .= " AND cd2.name LIKE '" . $data['filter_name'] . "%'";
        }*/

        $sort_data = array(
            'name',
            'sort_order'
        );

        $sqlSort = '';
        if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
            $sqlSort .= 'cate1.' . $data['sort'];
        } else {
            $sqlSort .= 'cate1.sort_order';
        }

        if (isset($data['order']) && ($data['order'] == 'DESC')) {
            $query->orderBy($sqlSort, 'DESC');
        } else {
            $query->orderBy($sqlSort,'ASC');
        }

        /*if (isset($data['start']) || isset($data['limit'])) {
            if ($data['start'] < 0) {
                $data['start'] = 0;
            }

            if ($data['limit'] < 1) {
                $data['limit'] = 20;
            }

            $sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
        }*/
        //dd($query->toSql());
        return  $query;
    }
}
