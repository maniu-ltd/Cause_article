<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use EasyWeChat\Foundation\Application;

class User extends Model
{
    protected $guarded = ['membership_time', 'extension_num', 'extension_type', '_token', '_method'];

    public function getWcNickNameAttribute($value)
    {
        return str_limit($value, 14);
    }

    /**
     * @title  所属品牌
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function brand()
    {
        return $this->belongsTo(Brand::class,'brand_id');
    }

    /**
     * 用户订单
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function orderList()
    {
        return $this->hasMany(Order::class, 'uid');
    }

    /**
     * 所属后台员工
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function admin()
    {
        return $this->belongsTo(Admin::class);
    }

    /**
     * @title  推广人
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function extension()
    {
        return $this->belongsTo(User::class, 'extension_id');
    }

    /**
     * @title  经销商
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function dealer()
    {
        return $this->belongsTo(User::class, 'dealer_id');
    }

    /**
     * @title  我的文章
     * @return \Illuminate\Database\Eloquent\Relations\hasMany
     */
    public function user_article()
    {
        return $this->hasMany(UserArticles::class, 'uid');
    }

    /**
     * @title  谁查看我的头条
     * @return \Illuminate\Database\Eloquent\Relations\hasMany
     */
    public function user_foot()
    {
        return $this->hasMany(Footprint::class, 'uid');
    }

    /**
     * 关联用户提现信息
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function user_account()
    {
        return $this->hasOne(UserAccount::class, 'user_id');
    }

    /**
     * 关联销售表
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }

    /**
     * 生成微信二维码
     * @param $user
     * @return string
     */
    public function createQrcode($user)
    {
        //创建永久二维码(已改为临时)
        $app = new Application(config('wechat'));
        $qrcode = $app->qrcode;
        if($user->type == 2) {
            $result = $qrcode->forever($user->id);
        } else {
            $result = $qrcode->temporary($user->id, 29 * 24 * 3600);
        }
        $ticket = $result->ticket; // 或者 $result['ticket']
        return $qrcode->url($ticket);
    }
}
