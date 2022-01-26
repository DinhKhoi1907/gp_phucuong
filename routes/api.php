<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Broadcast;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('/test', function (Request $request) {
  return ['test' => 'ok'];
  die;
});

Route::namespace('App\Http\Controllers\Api\Front')
  ->middleware('web')
  ->group(function () {
    Route::any('/app/get-setting', 'Base\ApiController@getSetting');
    Route::apiResource('/homes/get-list', 'HomeController');
    Route::get('/app/info/get-information-list', 'NewsController@list');
    Route::get('/app/info/get-information', 'NewsController@detail');
    Route::get('/app/get-ngay-les', 'NewsController@indexNgayLe');
    Route::get('/app/info/get-latest-information', 'NewsController@showLastedList');
    Route::get('/app/info/get-popular-information', 'NewsController@showPopularList');
    Route::get('/app/info/get-related-information', 'NewsController@showRelatedList');
    Route::get('/app/info/get-special-information', 'NewsController@showSpecialModuleList');
    Route::get('/app/get-data-module', 'ModuleController@showDataList');
    Route::get('/app/get-data-giao-xu', 'Base\ApiController@getGiaoXuList');
    Route::get('/app/get-data-giao-phan', 'Base\ApiController@getGiaoPhanList');
    Route::post('/app/get-data-giao-hat', 'Base\ApiController@getGiaoHatList');
    Route::post('/app/get-data-giao-xu-by-id', 'Base\ApiController@getGiaoXuListById');
    Route::get('/app/get-data-giao-xu/{id}', 'Base\ApiController@getGiaoXuDetail');
		Route::get('/app/get-data-linh-muc', 'Base\ApiController@getLinhMucList');
    Route::get('/app/get-data-chuc-vu', 'Base\ApiController@getChucVuList');
    Route::post('/app/get-data-linh-muc-by-id', 'Base\ApiController@getLinhMucListById');
		Route::get('/app/get-detail-linh-muc/{id}', 'Base\ApiController@getLinhMucDetail');
    Route::apiResource('/email_sub/create', 'EmailController'); 
  });

Route::namespace('App\Http\Controllers\Api\Admin')
  ->middleware(['web', 'secip'])
  ->group(function () {
    Route::post('login', 'AuthController@login');
    Route::post('logout', 'AuthController@logout');
});

Route::namespace('App\Http\Controllers\Api\Admin')
  ->middleware(['app.version', 'auth:sanctum', 'secip'])
  ->group(function () { 
    Route::get('/user', function (Request $request) {
      $user = $request->user();
      $user->isAdmin = fn_is_admin_permission();
      return $request->user();
    });
});

Route::namespace('App\Http\Controllers\Api\Admin')
  ->middleware(['auth:sanctum', 'secip'])
  ->group(function () {
    Route::apiResource('users', 'AdminController');
    Route::get('/search-user', 'AdminController@search');
    Route::apiResource('news-groups', 'NewsGroupController');
    Route::get('/news-categories/dropdowns', 'NewsGroupController@dropdown');
    Route::apiResource('news', 'InformationController');
    Route::get('/informations/dropdowns', 'InformationController@dropdown');
    Route::apiResource('settings', 'SettingController');

    Route::apiResource('linh-mucs', 'LinhMucController');
    Route::apiResource('linh-muc-bang-caps', 'LinhMucBangCapController');
    Route::apiResource('linh-muc-chuc-thanhs', 'LinhMucChucThanhController');
    Route::apiResource('linh-muc-van-thus', 'LinhMucVanThuController');
    Route::apiResource('linh-muc-thuyen-chuyens', 'LinhMucThuyenChuyenController');
    Route::post('change-status-thuyen-chuyens','LinhMucThuyenChuyenController@changeStatus');

    Route::apiResource('giao-phans', 'GiaoPhanController');
    Route::apiResource('giao-hats', 'GiaoHatController');
    Route::apiResource('giao-xus', 'GiaoXuController');
    Route::get('giao-hats-by-giao-xus', 'GiaoXuController@listGiaoHats');
    Route::get('giao-xus-by-id-giao-hat/{id}', 'GiaoXuController@listGiaoXuByIdGiaoHat');

    Route::apiResource('giao-diems', 'GiaoDiemController');
    Route::apiResource('cong-doan-tu-sis', 'CongDoanTuSiController');
    Route::apiResource('co-sos', 'CoSoController');
    Route::apiResource('dongs', 'DongController');
    Route::apiResource('thanhs', 'ThanhController');
    Route::apiResource('chuc-vus', 'ChucVuController');
    Route::apiResource('le-chinhs', 'LeChinhController');
    Route::apiResource('ngay-les', 'NgayLeController');
    Route::apiResource('giao-phan/danh-mucs', 'GiaoPhanDanhMucController');
    Route::get('/danh-mucs/dropdowns', 'GiaoPhanDanhMucController@dropdown');
    Route::apiResource('giao-phan/tin-tucs', 'GiaoPhanTinTucController');
    Route::get('/tin-tucs/dropdowns', 'GiaoPhanTinTucController@dropdown');
    Route::apiResource('restrict-ips', 'RestrictIpController');
    Route::get('search-ips','RestrictIpController@search');
    Route::post('change-status-ips','RestrictIpController@changeStatus');

    Route::apiResource('albums', 'AlbumsController'); 
    Route::get('search-albums','AlbumsController@search');
    Route::post('change-status-albums','AlbumsController@changeStatus');

    Route::apiResource('group-albums', 'GroupAlbumsController'); 
    Route::get('search-group-albums','GroupAlbumsController@search');
    Route::post('change-status-group-albums','GroupAlbumsController@changeStatus');

    Route::any('/mmedia/{any}', function () {
    });
  });
