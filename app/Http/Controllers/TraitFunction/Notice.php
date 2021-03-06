<?php

namespace App\Http\Controllers\TraitFunction;

use App\Classes\Sms\SmsSender;
use App\Model\Order;
use App\Model\User;

trait Notice
{
    /**
     * 发送短信
     * @param $phone 发送手机号
     * @param $templId 短信模板id
     * @param $params 模板参数
     * @param $notice_msg
     * @return array
     */
    public function sms( $phone, $templId, $params, $notice_msg )
    {
        $appid = 1400058384;
        $appkey = "ea50aedaecf4b8821410bb4822b71d20";
        $sms = new SmsSender($appid, $appkey);
        $result = $sms->sendWithParam("86", $phone, $templId, $params);
        $arr = json_decode($result, true);
        //发送成功后记录到文件中
        if ( $arr[ 'errmsg' ] == 'OK' ) {
            $status = "发送成功【{$notice_msg}】";
            rwLog($phone, $status, 'notice_');
            return ['state' => 0, 'msg' => '发送成功'];
        } else {
            $status = "发送失败【{$notice_msg}】";
            rwLog($phone, $status, 'notice_');
            return ['state' => 401, 'msg' => '发送失败'];
        }
    }

    /**
     * @param bool $bool
     * @return array
     */
//    public function extension($me_user, $bool = false) : array
//    {
//        $order = 0; //订单数
//        $order_price_tot = 0; //订单价格总计
//        $integral_price_tot = 0; //获得佣金合计
//        $extension_user = 0; //下级用户总数
//        $extension_order_tot = 0; //下级订单数
//        $extension_order_price = 0; //下级订单价格总计
//        $extension_integral_price_tot = 0; //获得下级佣金合计
//        $users = User::select('id', 'integral_scale')->where('extension_id', $me_user->id)->when($bool, function($query) {
//            return $query->whereDate('created_at', date('Y-m-d', time()));
//        })->get();
//        foreach ($users as $user) {
//            $data = ['state' => 1, 'refund_state' => 0];
//            $where = array_merge($data, ['uid' => $user->id]);
//            $order += Order::where($where)->count();
//            $order_price = Order::where($where)->sum('price');
//            $integral_price = $order_price * ($me_user->integral_scale ? $me_user->integral_scale/100 : 0.3);
//            $order_price_tot += floor($order_price);
//            $integral_price_tot += floor($integral_price);
//
//
//            $ex_users = User::select('id')->where('extension_id', $user->id)->when($bool, function ($query) {
//                return $query->whereDate('created_at', date('Y-m-d', time()));
//            })->get();
//            $extension_user += count($ex_users);
//            foreach ($ex_users as $e_user) {
//                $where = array_merge($data, ['uid' => $e_user->id]);
//                $extension_order_tot += Order::where($where)->count();
//                $extension_price = Order::where($where)->sum('price');
//                $extension_integral_price = $extension_price * 0.1;
//                $extension_order_price += floor($extension_price);
//                $extension_integral_price_tot += floor($extension_integral_price);
//            }
//        }
//
//        return [count($users), $order, $order_price_tot, $integral_price_tot, $extension_user, $extension_order_tot, $extension_order_price, $extension_integral_price_tot];
//    }

    public function extension($me_user, $bool = false) : array
    {
        $data = [
            'state' => 1,
            'refund_state' => 0
        ];
        $users = User::where('extension_id', $me_user->id)->when($bool, function($query) {
            return $query->whereDate('created_at', date('Y-m-d', time()));
        })->count();
        $where = array_merge($data, ['superior' => $me_user->id]);
        $order = Order::where($where)->when($bool, function($query) {
            return $query->whereDate('created_at', date('Y-m-d', time()));
        })->count();
        $order_price_tot = Order::where($where)->when($bool, function($query) {
            return $query->whereDate('created_at', date('Y-m-d', time()));
        })->sum('price_int');
        $integral_price_tot = floor($order_price_tot * ($me_user->integral_scale ? $me_user->integral_scale/100 : 0.3));
        $extension_user = User::where('extension_up', $me_user->id)->when($bool, function($query) {
            return $query->whereDate('created_at', date('Y-m-d', time()));
        })->count();
        $where = array_merge($data, ['superior_up' => $me_user->id]);
        $extension_order_tot = Order::where($where)->when($bool, function($query) {
            return $query->whereDate('created_at', date('Y-m-d', time()));
        })->count();
        $extension_order_price = Order::where($where)->when($bool, function($query) {
            return $query->whereDate('created_at', date('Y-m-d', time()));
        })->sum('price_int');
        $extension_integral_price_tot = floor($extension_order_price * 0.1);

        return [$users, $order, $order_price_tot, $integral_price_tot, $extension_user, $extension_order_tot, $extension_order_price, $extension_integral_price_tot];

    }

}