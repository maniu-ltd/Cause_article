<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/9/8 0008
 * Time: 下午 5:35
 */

namespace App\Http\Controllers\Index;

use App\Jobs\templateMessage;
use App\Model\{
    Article, Banner, Brand, ExtensionArticle, Report, User, UserArticles, ArticleType
};
use App\Model\FamilyMessage;
use Carbon\Carbon;
use EasyWeChat\Foundation\Application;
use Illuminate\Http\Request;

class IndexController extends CommonController
{
    public function test(Request $request, Application $app)
    {
//        dd($app->material->lists('news'));

    }

    /**
     * 首页
     * @param ArticleType $atype
     * @param int $type
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\JsonResponse|\Illuminate\View\View
     * @throws \Throwable
     */
    public function index(ArticleType $atype, $type = 0)
    {
        //banner图
//        $banner_list = Cache::remember('banner', 30, function (){
//            $ret = Banner::all();
//            return $ret;
//        });

        //文章分类
        $article_type = $atype->lists();

        $user = User::with('brand')->where('id', session('user_id'))->first();

        $types = [$article_type->first()->id, 0];
        if ($type) $types = [ $type, 0 ];

        $list = Article::orderBy('id', 'desc')->whereIn('type', $types)
            ->when($user->brand_id, function ( $query ) use ( $user ) {
            //根据用户选择的品牌显示该品牌文章
            return $query->whereIn('brand_id', [ 0, $user->brand_id ]);
        })->paginate(7);

        if(\request()->ajax()) {
            $html = view('index.template.__index', compact('list'))->render();
            return response()->json(['html' => $html]);
        }

        //微信分享配置
        $package = wecahtPackage();

        return view('index.index', compact('banner_list', 'article_type', 'list', 'user', 'package'));
    }

    /**
     * 推荐好文章
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function extensionArticle(Request $request)
    {
        $this->validate($request, [
            'url' => 'required|url',
        ]);

        if(str_contains($request->url, 'bw.eyooh.com')) {
            return redirect()->back()->with('err', '请勿提交本站文章');
        }

        ExtensionArticle::create(['user_id' => session('user_id'), 'url' => $request->url]);

        return redirect()->route('index.index');
    }

    /**
     * @title  品牌列表接口
     * @param Brand $brand
     * @return string
     */
    public function brandList(Brand $brand)
    {
        $brand_list = $brand->cacheList();

        return response()->json(['state'=>0, 'brand_list'=>$brand_list]);
    }

    /**
     * @title 完善资料
     * @param Request $request
     * @param User $user
     * @return \Illuminate\Http\JsonResponse
     */
    public function perfectInformation(Request $request, User $user)
    {
        $data = $request->all();
        //判断品牌是否为用户自定义
        if(!intval($request->brand_id)) {
            $add_brand = Brand::create(['name' => $request->brand_id, 'type' => 1]);
            $data['brand_id'] = $add_brand->id;
        }
        if($user->update($data)){

            return response()->json(['state' => 0, 'msg' => '完善资料成功']);
        } else {
            return response()->json(['state' => 500, 'msg' => '完善资料出错']);
        }
    }

    /**
     * @title 举报页面
     * @param $article_id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function report($article_id, $atype)
    {
        return view('index.report');
    }

    /**
     * @title 填写举报内容
     * @param $article_id
     * @param $type
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function reportText($article_id, $atype, $type)
    {
        return view('index.report_text');
    }

    /**
     * @title 提交举报
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function reportPost(Request $request)
    {
        $data = [
            'uid' => session()->get('user_id'),
            'message'   =>  $request->message,
//            'phone' => $request->phone,
            'type'  =>  $request->type,
            'created_at' => date('Y-m-d H:i:s', time())
        ];
        if($request->atype == 1) {
            $data['aid'] = $request->aid;
            $url = route('article_details',['id'=>$request->aid]);
        } elseif($request->atype == 2) {
            $ua = UserArticles::where('id',$request->aid)->select('aid')->first();
            $data['aid'] = $ua->aid;
            $url = route('user_article_details',['id'=>$request->aid]);
        }
        if (Report::create($data)) {
            return response()->json(['state'=>0, 'errormsg'=>'已举报改文章','url'=>$url]);
        } else {
            return response()->json(['state'=>401, 'errormsg'=>'举报文章失败','url'=>$url]);
        }
    }

    /**
     * 家庭保障测评入口页面
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function familyAppraisal($uid)
    {
        $count = FamilyMessage::get()->count();

        return view('index.family_appraisal', compact('count'));
    }

    /**
     * 开始测试家庭保障
     * @param Request $request
     * @param User $user
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function familyAppraisalBegin(Request $request, User $user)
    {
        if($request->post()) {
            $data = $request->all();
            $data['sub_uid'] = session('user_id');
            $add = FamilyMessage::create($data);
            $user = User::where('id', $request->user_id)->select('openid', 'membership_time', 'subscribe')->first();
            if($user->subscribe) {
                if ( Carbon::parse($user->membership_time)->gt(Carbon::parse('now')) ) {
                    $msg = [
                        "first"    => $request->name . "有一个【{$request->type}】的需求向您咨询，快打开看看吧~。",
                        "keyword1" => $request->name,
                        "keyword2" => $request->phone,
                        "keyword3" => str_random(10),
                        "remark"   => "点击查看详情。"
                    ];
                } else {
                    $msg = [
                        "first"    => mb_substr($request->name, 0, 1, 'utf-8') . "**有一个{$request->type}的需求向您咨询，快打开看看吧~。",
                        "keyword1" => mb_substr($request->name, 0, 1, 'utf-8') . "**",
                        "keyword2" => substr($request->phone, 0, 3) . "********",
                        "keyword3" => str_random(10),
                        "remark"   => "点击查看详情。"
                    ];
                }
                dispatch(new templateMessage($user->openid, $msg, config('wechat.template_id.consult_message'), route('message_list', 2)));
            }

            return redirect()->route('family_appraisal_result', $add->id);
        }

        return view('index.family_health', compact('user'));
    }

    /**
     * 家庭保障测试结果
     * @param FamilyMessage $message
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function familyAppraisalResult(FamilyMessage $message)
    {
        return view('index.family_result', compact('message'));
    }
}