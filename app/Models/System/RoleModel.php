<?php

namespace App\Models\System;
use App\Libraries\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * 角色模型
 * Class RoleModel
 * @package App\Models\System
 */
class RoleModel extends BaseModel
{
    use SoftDeletes;
    protected $table = 'platform_role';
    protected $fillable = [
        'id',
        'dept_id',
        'role_name',
        'created_at'
    ];
}
