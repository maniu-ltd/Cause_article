<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/1/31 0031
 * Time: 上午 11:57
 */

namespace App\Http\Controllers\Index;


use App\Http\Controllers\Controller;
use App\Jobs\extensionphoto;
use App\Model\Brand;
use App\Model\Photo;
use App\Model\PhotoType;
use App\Model\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Http\Request;

class PhotoController extends Controller
{
    /**
     * 美图列表
     * @param string $type
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\JsonResponse|\Illuminate\View\View
     * @throws \Throwable
     */
    public function index($type = '')
    {
        $types = PhotoType::orderBy('sort', 'asc')->get();

        $user = User::with('brand')->where('id', session('user_id'))->first();

        if($user->brand_id) {
            if($type) {
                $photos = Photo::where('type_id', $type)->paginate(12);
            }else {
                $photos = Photo::where('brand_id', $user->brand_id)->paginate(12);
            }
        } else {
            if ( $type ) {
                $photos = Photo::where('type_id', $type)->paginate(12);
//                dump($photos);
            } else {
                foreach ( $types as $value ) {
                    $photos = Photo::where('type_id', $value->id)->paginate(12);
                    break;
                }
            }
        }
        if(\request()->ajax()) {
            $view = view('index.template.__photo_list', compact('photos'))->render();
            return response()->json(['html'=>$view]);
        }
//        dump($photos);

        return view('index.photo_list', compact('types', 'user', 'photos'));
    }

    /**
     * 推广图
     * @param Photo $photo
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function poster(Photo $photo)
    {
        $user = User::with('brand')->where('id', session('user_id'))->first();

        $pic = Cache::remember('user_qrcode'.$user->openid, 60 * 24 * 29, function () use($user) {
            $url = app(User::class)->createQrcode($user);
            //二维码转base64位
            $pic = "data:image/jpeg;base64," . base64_encode(file_get_contents($url));

            return $pic;
        });

        $head = Cache::remember('user_head'.$user->openid, 60 * 24 * 30, function () {
            //头像转base64
            $head = session('head_pic');
            if(str_contains($head, 'qlogo.cn')) {
//            if(strstr(session('head_pic'), "wx.qlogo.cn", true) == 'http://') {
                $content = file_get_contents($head);
                $head =  'data:image/jpeg;base64,' . base64_encode($content);
            } else {
                $content = file_get_contents(config('app.url').$head);
                $head =  'data:image/jpeg;base64,' . base64_encode($content);
            }
            return $head;
        });

        $rand_photo = $this->randPhoto(6, 1);

        //弹窗显示品牌信息
        $brand = Brand::find($user->brand_id);

        return view('index.photo_poster', compact('user', 'photo', 'pic', 'head', 'rand_photo', 'brand'));
    }

    /**
     * 随机获取图片
     * @param $count
     * @param $type
     * @return \Illuminate\Http\JsonResponse
     * @throws \Throwable
     */
    public function randPhoto( $count, $type )
    {
        $brand_id = User::where('id', session('user_id'))->value('brand_id');
        if($brand_id) {
            $brand = Brand::where('id', '<>', $brand_id)->get();
            $photo = Photo::whereNotIn('brand_id', $brand->pluck('id')->all())->get()->random($count);
        } else {
            $photo = Photo::get()->random($count);
        }
        if($type == 1) {
            return $photo->all();
        } elseif ($type == 2) {
            return response()->json(['photo'=>$photo[0]['url']]);
        } elseif ($type == 3) {
            $view = view('index.rand_photo_list', compact('photo'))->render();
            return response()->json(['view'=>$view]);
        }
    }

    /**
     * 推送海报到公众号
     * @param Request $request
     * @return mixed
     */
    public function photoShare( Request $request )
    {
        $user = User::find(session('user_id'));
        dispatch(new extensionphoto($user, $request->img));

        return response()->json(['state' => '0']);
    }
}