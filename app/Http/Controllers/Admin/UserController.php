<?php

namespace App\Http\Controllers\Admin;

use App\Model\Admin;
use App\Model\Integral;
use App\Model\User;
use Illuminate\Http\Request;

class UserController extends CommonController
{
    /**
     * 前台用户管理
     * @普通用户列表
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $type = $request->type;
        $key = $request->key;
        $value = $request->value;
        $where = [];
        switch ($type) {
            case '1':
                array_push($where, ['phone', '=', ""]);
                break;
            case '2':
                array_push($where, ['phone', '<>', ""]);
                break;
            case '3':
                array_push($where, ['membership_time', '>', time()]);
                break;
            default:
                break;
        }
        switch($key){
            case 'wc_nickname':
                array_push($where, ['wc_nickname', 'like', "%$value%"]);
                break;
            case 'phone':
                array_push($where, ['phone', 'like', "%$value%"]);
                break;
        }
        array_push($where, ['type', 1]);
        $list = User::with(['extension'=>function($query){
            $query->select('id','wc_nickname');
        },'dealer'=>function($query){
            $query->select('id','wc_nickname');
        },'brand'])->where($where)->orderBy('id','desc')->paginate(15);

//        $list->transform(function ($value) {
//            $new = collect($value);
//            $commission = app(Integral::class)->commission($value->id);
//            $new->put('commission', $commission);
//            return $new;
//        });

        //招商员工列表
        $admin = Admin::whereIn('gid', [14, 21])->get();

        $menu = $this->menu; $active = $this->active;

        return view('admin.user.index',compact('list','menu','active', 'admin'));
    }

    /**
     * 经销商列表
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function dealerList(Request $request)
    {
        $key = $request->key;
        $value = $request->value;
        $where = [];
        switch($key){
            case 'wc_nickname':
                array_push($where, ['wc_nickname', 'like', "%$value%"]);
                break;
            case 'phone':
                array_push($where, ['phone', 'like', "%$value%"]);
                break;
        }
        array_push($where, ['type', 2]);
        $list = User::with(['dealer' => function($query){
            $query->select('id','wc_nickname');
        },'brand','admin' => function($query){
            $query->select('id','account');
        }])->where($where)->orderBy('id','desc')->paginate(15);

        $list->transform(function ($value) {
            $new = collect($value);
            $commission = app(Integral::class)->commission($value->id);
            $new->put('commission', $commission);
            return $new;
        });

        $menu = $this->menu;
        $active = $this->active;

        return view('admin.user.dealer_index',compact('list','menu','active'));
    }

    /**
     * 把用户变更为经销商并成为后台员工下级
     * @param Request $request
     */
    public function adminExtension(Request $request)
    {
        foreach ($request->user_id as $user) {
            User::where('id', $user)->update(['type' => 2, 'extension_id' => 0,'admin_id' => $request->admin_id, 'admin_type' => 1]);
        }

        return redirect()->back();
    }

    /**
     * 成为经销商
     * @param $user
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function be_dealer(User $user)
    {
        $update = $user->update(['type' => 2]);
        //该用户推广的所有用户更新关联关系
        $user->where('extension_id', $user->id)->update(['extension_id' => 0, 'dealer_id' => $user->id]);
        if($update){
            return redirect(route('admin.user'));
        }
    }

    /**
     * @title 查看佣金
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function seeIntegral($id)
    {
        $history_integral = number_format(Integral::where('user_id',$id)->sum('price'), 2);
        return response()->json(['history'=>$history_integral]);
    }

    /**
     * 设置佣金比例
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function setIntegral(Request $request, User $user)
    {
        $user->update(['integral_scale'=>$request->integral_scale]);

        return response()->json(['state' => 0, 'error' => '设置成功']);
    }

    public function setMemberTime( Request $request )
    {
        User::where('id', $request->user_id)->update(['membership_time' => $request->membership_time]);

        return response()->json(['state' => 0, 'error' => '设置成功']);
    }
}
