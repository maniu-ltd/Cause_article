<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0,minimum-scale=1.0,maximum-scale=1.0,user-scalable=no"/>
    <meta name="format-detection" content="telephone=no,email=no,date=no,address=no">
    <title>增员海报</title>
    @include('index.public.css')
</head>
<body>
<div id="posters" class="flexv wrap">
    <div class="flexitemv mainbox">
        <div class="flex centerv edit"><i class="flex center bls bls-amend"></i><span>修改个人信息</span></div>
        <div class="flexv posterbox">
            <p class="flex center">图片已生成，长按保存后即可分享到朋友圈</p>
            <div class="flex poster">
                <i class="flex center bls bls-zjt_"></i>
                <img class="img" src="" style="width: 23rem;">
                <i class="flex center bls bls-zjt_"></i>
            </div>
            <button class="flex center share-btn" type="button" id="share">去分享</button>
        </div>
        <div class="listbox">
            <div class="between title">
                <div class="flex center text"></div>
                <div class="flex centerv change"><i class="flex center bls bls-change"></i>换一批</div>
            </div>
            <ul class="fwrap list" id="rand_list">
                @foreach($rand_photo as $value)
                    <li class="item">
                        <a href="{{ route('extension_poster', $value->id) }}" class="flex link center" style="height: 15rem">
                            <img data-original="{{ $value->url }}" src="/index/image/loading.gif" class="lazy">
                        </a>
                        <p class="flex center name">{{ $value->name }}</p>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>

    <!--弹窗-->
    <div class="flex center win">
        <div class="flexv center content">
            <i class="flex center bls bls-win"></i>
            <p class="tex1">美图海报已通过公众号发送</p>
            <p class="tex2">马上分享朋友圈</p>
            <p class="tex3">让更多人了解您的事业</p>
            <button class="flex center know-btn" type="button">知道了</button>
        </div>
    </div>

    <!--提示-->
{{--    @includeWhen(!$user->brand_id && !$user->phone, 'index.public.perfect_information')--}}

    <img src="{{ $head }}" class="hidden user">
    <img src="{{ $pic }}" class="hidden qrcode">
    <form class="flex center alert" id="form" action="{{route('perfect_information', session('user_id'))}}" style="display: none !important;">
        {{csrf_field()}}
        {{--<input type="hidden" name="id" value="{{session()->get('user_id')}}">--}}
        <div class="mask"></div>
        <div class='content user-info'>
            <i class="flex center bls bls-cuo cuo"></i>
            <h3 class="flex center title">您的信息不完整</h3>
            <p class="flex center tis">立刻完善资料，让客户找到您</p>
            <div class="flex center input">
                <span class="flex centerv">姓名</span>
                <input type="text" name="wc_nickname" class="flexitem" value="{{ $user->wc_nickname }}" data-rule="*" data-errmsg="请填写您的姓名">
            </div>
            <div class="flex center input">
                <span class="flex centerv">手机号</span>
                <input type="text" name="phone" class="flexitem" @if($user->phone) value="{{ $user->phone }}" @endif data-rule="m" data-errmsg="手机号码格式错误">
            </div>
            <div class="flex centerv input brands">
                <span class="flex centerv">品牌</span>
                <input type="text" readonly="readonly" class="flexitem cenk brand_name" placeholder="选择品牌" @if($user->brand_id) value="{{ $user->brand->name }}" @endif data-rule="*" data-errmsg="请选择您的品牌" onfocus="this.blur()">
                <input type="hidden" name="brand_id" class="brand_id" @if($user->brand_id) value="{{ $user->brand->id }}" @endif>
                <i class="flex smtxt"></i>
                <i class="flex center bls bls-xia brand"></i>
            </div>
            <a href="javascript:;" class="flex center button" id="submit">保存修改</a>
        </div>
    </form>
    <!--品牌-->
    <div id="brand" class="flexitemv" data-id="{{ $user->brand ? $user->brand_id : '' }}" data-name="{{ $user->brand ? $user->brand->name : '' }}"></div>
</div>
</body>
<script src="https://cdn.bootcss.com/zepto/1.2.0/zepto.min.js"></script>
@include('index.public.perfect_js')
<script src="/index/js/canvas.js"></script>
<script src="/index/js/lazyload.js"></script>
<script src="https://cdn.bootcss.com/lodash.js/4.17.4/lodash.min.js"></script>
<script type="text/javascript" src="/index/js/checkform.js"></script>
<script type="text/javascript" src="/index/js/functions.js"></script>
<script type="text/javascript" src="/index/js/brand_new.js"></script>
<script type="text/javascript">
    $.get("{{ route('brand_list') }}", function (ret) {
        var brands = ret.brand_list;
        var brandContainer = $('#brand');
        var selectedBrand = '{{ $user->brand }}' ? [brandContainer.data('id'), brandContainer.data('name')] : null;
        // 插入品牌
        InfoBrand('#brand','.brand_name','.brand_name', '.brand_id', brands, selectedBrand);
    });

    //图片延迟加载
    $(".lazy").lazyload({
        event: "scrollstop",
        effect : "fadeIn",
        load:function ($e) {
            $e.css({"width":"100%","height":"100%"});
        }
    });

    @if($user->brand_id && $user->phone)
        $('.content.user-info').prepend('<i class="flex center bls bls-cuo cuo"></i>');
    @else
        $(".alert").show();
        $(".cuo").hide();
    @endif
    $(".flex.center.title").hide();
    $(".tis").hide();

    var img =  document.querySelector(".img"),
        user = document.querySelector(".user"),
        qrcode = document.querySelector(".qrcode"),
        src = "{{ $photo->url }}",
        name = "{{ str_limit($user->wc_nickname, 10) }}",
        phone = "{{ $user->phone }}";

    @if($user->brand)
        var brand = "{{ $user->brand->name }}";
    @else
        var brand = "";
    @endif

    poster(src,brand,name,phone,qrcode);

    //修改个人信息
    $(".edit>span").click(function () {
        $(".alert").show();
    });

    //左
    $(".poster>i").first().click(function () {
        showProgress('切换中..');
        var url = "{{ route('rand_photo', ['count'=>1, 'type'=>2]) }}";
        $.get(url, function (ret) {
            hideProgress();
            poster(ret.photo,brand,name,phone,qrcode);
        })
    });

    //右
    $(".poster>i").last().click(function () {
        showProgress('切换中..');
        var url = "{{ route('rand_photo', ['count'=>1, 'type'=>2]) }}";
        $.get(url, function (ret) {
            hideProgress();
            poster(ret.photo,brand,name,phone,qrcode);
        })
    });

    //换一批
    $(".change").click(function () {
        showProgress('切换中..');
        var url = "{{ route('rand_photo', ['count'=>6, 'type'=>3]) }}";
        $.get(url, function (ret) {
            hideProgress();
            $('#rand_list').html(ret.view);
        })
    });

    function poster(src,brand,name,phone) {
        var can = document.createElement("canvas"),ctx = can.getContext("2d");
        var imgs = new Image();
        imgs.src = src;
        imgs.onload = function(){
            console.log(imgs.width, imgs.height);
            //设置画布尺寸
            can.width = imgs.width;
            can.height = imgs.height;
            //绘制背景图
            ctx.drawImage(imgs, 0, 0);
            ctx.fillStyle = 'rgba(0,0,0,0.5)';
            ctx.fillRect(0, can.height-160, can.width, can.height-160);
            ctx.drawImage(qrcode, can.width - 135, can.height - 140, 120, 120);
            //绘制用户头像
            ctx.save();
            ctx.strokeStyle = '#ccc';
            ctx.lineWidth = 2;
            ctx.arc(85, can.height - 80, 60, 0, 2 * Math.PI);
            ctx.stroke();
            ctx.clip();
            ctx.drawImage(user, 0, 0, user.width, user.height, 25, can.height - 140, 120, 120);
            ctx.restore();
            //绘制信息
            ctx.font = '32px Arial';
            ctx.fillStyle = '#fff';
            ctx.fillText(brand + name, 170, can.height - 100);
            ctx.fillText(phone, 170, can.height - 40);
            img.src = can.toDataURL('image/jpeg');
        }
    }

    new checkForm({
        form : '#form',
        btn : '#submit',
        error : function (ele,err){showMsg(err);},
        complete : _.throttle(function (ele){
            var url = $(ele).attr('action'),post = $(ele).serializeArray(),
                brand = $('input[readonly=readonly]').val(),
                name = $('input[name=wc_nickname]').val(),
                phone = $('input[name=phone]').val();
            showProgress('正在提交');
            console.log(post);
            $.post(url,post,function (){
                hideProgress();
                $(".alert").css({"display":"none"});
                poster(src,brand,name,phone,qrcode);
            },'json');
        }, 3000, { 'trailing': false })
    });

    $('#share').click(_.throttle(function () {
        var base64 = $('.img').attr('src'),
            url = "{{ route('get_share_photo') }}";
        showProgress('正在发送图片');
        $.post(url, {img: base64, _token: "{{ csrf_token() }}"}, function (ret) {
            console.log(ret);
            hideProgress();
            $(".win").show();
        });
    }, 3000, { 'trailing': false }));

    $('.flex.center.know-btn').click(function () {
        $(".win").hide();
    });

</script>
</html>