<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Helpers\Helper;
use App\Models\NewsGroup;
use Illuminate\Http\Request;

/**
 * Class NewsGroupController
 *
 * @package App\Http\Controllers\Api\Admin
 */
class NewsGroupController extends Controller
{
    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @api GET /v1/admin/stores
     */
    public function index(Request $request)
    {
        $data = [
            'sort'      => 'stores.code',
            'direction' => 'desc',
            'page'      => $request['page'] ?: 1
        ];

        if (isset($request['q']) && !empty($request['q'])) {
            $data['search'] = $request['q'];
        }
        if (isset($request['sort']) && !empty($request['sort'])) {
            $data['sort'] = 'stores.' . $request['sort'];
        }
        if (isset($request['direction']) && !empty($request['direction'])) {
            $data['direction'] = $request['direction'];
        }

        $newsGroups = NewsGroup::getNewsGroups($data);
        $newsGroupTrees = $this->generateTree($newsGroups['data']);

        return Helper::successResponse([ 
            'newsGroups' => $newsGroups ,
            'newsGroupTrees' => $newsGroupTrees
        ]);
    }

    /**
     * [generateTree description]
     * @param  [type]  $data   [description]
     * @param  integer $parent [description]
     * @param  integer $depth  [description]
     * @return [type]          [description]
     */
    public function generateTree($data, $parent = -1, $depth = 0)
    {
        $newsGroupTree = [];
        
        for ($i = 0, $ni = count($data); $i < $ni; $i++) {
            if ($data[$i]['father_id'] == $parent) {
                $newsGroupTree[$data[$i]['id']]['name'] = $data[$i]['newsgroupname'];
                $newsGroupTree[$data[$i]['id']]['children'] = $this->generateTree($data, $data[$i]['id'], $depth + 1);
            }
        }

        return $newsGroupTree;
    }

    public function show(Request $request, $id = null) {
        return $request->user();
    }

    public function store (Request $request) {
        return $request->user();
    }

    public function update (Request $request, $id = null) {
        return $request->user();
    }

    public function destroy (Request $request, $id = null) {
        return $request->user();
    }
}
