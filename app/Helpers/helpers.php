<?php
define('DB_PREFIX', 'pc_');
define('APP_TOOL_MM_FILE_MANAGER_UPLOAD_HELPER_CLASS', 'App\Helpers\UploadLocalFilesystem');
define('APP_TOOL_MM_FILE_MANAGER_PATH', 'Image/NewPicture');
define('APP_TOOL_MM_FILE_MANAGER_THUMB_DIR', '.tmb');
define('APP_TOOL_MM_FILE_MANAGER_THUMB_SIZE', 'thumb');

if (!function_exists('fn_mysql_escape')) {
    function fn_mysql_escape($inp)
    {
        if (is_array($inp)) {
            return array_map(__METHOD__, $inp);
        }

        if (!empty($inp) && is_string($inp)) {
            return str_replace(array('\\', "\0", "\n", "\r", "'", '"', "\x1a"),
                array('\\\\', '\\0', '\\n', '\\r', "\\'", '\\"', '\\Z'), $inp);
        }

        return $inp;
    }
}

if (!function_exists('fn_is_admin_permission')) {
    function fn_is_admin_permission()
    {
        $admin = \Illuminate\Support\Facades\Auth::user();
        $isPermission = false;

        if (fn_is_admin_token_permission($admin->tokens())) {
            $isPermission = true;
        }
        
        return $isPermission;
    }
}

if (!function_exists('fn_is_update_permission')) {
    function fn_is_update_permission($uId)
    {
        $user = \App\Models\Admin::find($uId);
        $isPermission = true;

        if (fn_is_admin_token_permission($user->tokens())) {
            $isPermission = false;
        }
        
        return $isPermission;
    }
}

if (!function_exists('fn_is_admin_token_permission')) {
    function fn_is_admin_token_permission($tokens)
    {
        $adminToken = false;

        if ($tokens) {
            $permissions = $tokens->getResults();
            if ($permissions->count() == 1) { 
                if (($permissions[0]->name == 'allow.all') 
                    && (count($permissions[0]->abilities) == 1) 
                        && (in_array('*', $permissions[0]->abilities))) {
                    $adminToken = true;
                }
            }
        }
        
        return $adminToken;
    }
}
