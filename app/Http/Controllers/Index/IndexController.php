<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/9/8 0008
 * Time: 下午 5:35
 */

namespace App\Http\Controllers\Index;

use App\Jobs\mondaySlug;
use App\Model\{Article,Banner,Brand,Footprint,Report,User,UserArticles};
use App\Model\ArticleType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class IndexController extends CommonController
{
    /**
     * @title 首页
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request, $type = 0)
    {
        //banner图
        if(Cache::has('banner')) {
            $banner_list = Cache::get('banner');
        } else {
            $banner_list = Banner::all();
            Cache::put('banner', $banner_list, 30);
        }

        $user = User::with('brand')->where('id', session('user_id'))->first();
        $article_type = ArticleType::orderBy('sort', 'asc')->get();
        $types = [$article_type->first()->id, 0];
        if ($type) $types = [ $type, 0 ];
        $list = Article::orderBy('id', 'desc')->whereIn('type', $types)
            ->when($user->brand_id, function ( $query ) use ( $user ) {
            //根据用户选择的品牌显示文章
            return $query->whereIn('brand_id', [ 0, $user->brand_id ]);
        })->paginate(7);

        if(\request()->ajax()) {
            $html = view('index._index_template', compact('list'))->render();
            return response()->json(['html' => $html]);
        }

        return view('index.index', compact('banner_list', 'article_type', 'list', 'user'));
    }

    /**
     * @title  品牌列表接口
     * @return string
     */
    public function brandList()
    {
        $brand_list = Brand::select('id','name','domain')->where('id', '<>', 0)->orderBy('domain','asc')->get();
        return response()->json(['state'=>0, 'brand_list'=>$brand_list->toArray()]);
    }

    /**
     * @title 完善资料
     * @param Request $request
     * @param User $user
     * @return \Illuminate\Http\JsonResponse
     */
    public function perfectInformation(Request $request, User $user)
    {
        if($user->update($request->all())){
//            session(['phone' => $request->phone, 'nickname' => $request->wc_nickname]);

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
            'uid'   =>  session()->get('user_id'),
            'message'   =>  $request->message,
            'type'  =>  $request->type,
            'created_at'    =>  date('Y-m-d H:i:s', time())
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
}