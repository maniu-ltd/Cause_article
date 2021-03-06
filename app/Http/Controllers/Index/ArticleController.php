<?php
/**
 * 公共文章控制器
 */

namespace App\Http\Controllers\Index;

use App\Model\Article;
use App\Model\Footprint;
use App\Model\User;
use App\Model\UserArticles;
use Carbon\Carbon;
use EasyWeChat\Foundation\Application;
use Wxpay\Wechat;

class ArticleController extends CommonController
{
    /**
     * @title 搜索文章列表页
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function searchArticle()
    {
        $key = request()->key;
        $list = Article::where('title','like',"%$key%")->orderBy('id','desc')->paginate(7);

        $user = User::with('brand')->where('id', session('user_id'))->first();

        if(request()->ajax()) {
            $html = view('index.template.__article_search', compact('list'))->render();

            return response()->json(['html' => $html]);
        }
        return view('index.article_search',compact('list', 'user'));
    }

    /**
     * @title 公共文章详情
     * @param $article
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function articleDetails(Article $article)
    {
        //文章浏览数+1
        $article->increment('read',1);

        $user_id = session('user_id');
        if ($uarticle = UserArticles::where(['uid' => $user_id, 'aid' => $article->id])->first()) {
            return redirect(route('user_article_details', ['id' => $uarticle->id]));//跳到个人此文章详细页
        } else {
            $add = UserArticles::create(['uid' => $user_id, 'aid' => $article->id]);

            return redirect(route('user_article_details', ['id' => $add->id]));//跳到个人此文章详细页
        }

//        $res = Article::with('brand')->where('id', $article->id)->first();
//        //文章浏览数+1
//        $article->increment('read',1);
//        //微信分享配置
//        $package = wecahtPackage();
//
//        $user = User::with('brand')->where('id', session('user_id'))->first();
//
//        return view('index.article_details',compact('res','package','user_article', 'user'));
    }

    /**
     * 事业夜听喜欢数量+1
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function articleLike( $id, $type )
    {
        if($type == 1) {
            Article::where('id', $id)->increment('like');
        } elseif($type == 2){
            UserArticles::where('id', $id)->increment('like');
        }

        return response()->json(['state' => 0]);
    }

    /**
     * @title 公共文章分享数+1
     * @param $article
     */
    public function articleShare(Article $article)
    {
        $article->increment('share',1);
    }

    /**
     * 使公共文章变为我的文章
     * @param $article_id   '分享文章id'
     * @param $pid          '分享用户id'
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|\Illuminate\View\View
     */
    public function becomeMyArticle($article_id, $pid = 0)
    {
        $user_id = session('user_id');
        $user = User::where('id', $user_id)->select('head', 'wc_nickname', 'subscribe')->first();
        if($user->subscribe == 1) {
            if ($uarticle = UserArticles::where(['uid' => $user_id, 'aid' => $article_id])->first()) {
                return redirect(route('user_article_details', ['id' => $uarticle->id]));//跳到个人此文章详细页
            } else {
                $add = UserArticles::create(['uid' => $user_id, 'aid' => $article_id]);

                return redirect(route('user_article_details', ['id' => $add->id]));//跳到个人此文章详细页
            }
        } else {
            //创建临时二维码（参数为str类型）
            $app = new Application(config('wechat'));
            $qrcode = $app->qrcode;
            $result = $qrcode->temporary("$user_id|$article_id|$pid");
            $imgurl = $qrcode->url($result->ticket);

            //微信分享配置
            $app = new Application(config('wechat'));
            $js = $app->js;

            return view('index.become_my_article', compact('imgurl', 'js', 'user'));//显示扫二维码关注公众号才能使文章变成自己的页面
        }
    }

}