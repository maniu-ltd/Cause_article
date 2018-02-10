<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width,initial-scale=1.0,minimum-scale=1.0,maximum-scale=1.0,user-scalable=no"/>
	<meta name="format-detection" content="telephone=no">
	<meta name="referrer" content="never">
	<title>{{$res->article['title']}}</title>
	@include('index.public.css')
	<style>
		.box img{ width: 100% !important; height:auto !important; }
		.box iframe{ width: 100% !important; height:auto !important; }
	</style>
</head>
<body>
<div @if($res->article->type == 3) id="listen" @else id="article" @endif class="flexv wrap">
	<div class="flexitemv mainbox contents" @if($res->article->type == 3) style="padding:1.2rem" @endif>
		@if($res->article->type == 3)
			<div class="info">
				<h1>{{ $res->article->title }}</h1>
				<div class="bottom"><span>{{$res->created_at->toDateString()}}</span><a href="javascript:;">{{$res->user['wc_nickname']}}</a></div>
			</div>
			<div id="audio">
				<div class="flex centerv inner">
					<div class="flex center icon" data-src="{{ json_decode($res->article->audio, true)['src'] }}"></div>
					<div class="flexitemv media">
						<h3 class="flexv centerh">{{ json_decode($res->article->audio, true)['title'] }}</h3>
						<p class="flexv centerh">{{ json_decode($res->article->audio, true)['desc'] }}</p>
						<div class="flex progress"><em></em><span class="flex"></span></div>
						<div class="flex centerv duration">
							<span class="flexitem">00:00</span>
							<em class="flex">00:00</em>
						</div>
					</div>
				</div>
			</div>
			<div class="body">
				{!! $res->article->details !!}
			</div>
		@else
			<div class="title max">
				<h2 class="flex">{{$res->article['title']}}</h2>
				<div class="flex subhead">
					<span class="date">{{$res->created_at->toDateString()}}</span>
					<span class="name">{{$res->user['wc_nickname']}}</span>
					<a href="{{route('index.index')}}" class="site">事业头条</a>
				</div>
				<div class="box">
					{!! $res->article['details'] !!}
				</div>
			</div>

			<div class="flex center unfold">
				<div class="flex center unfoldbox">
					<p>展开全文</p>
					<i class="flex center bls bls-bottom"></i>
				</div>
			</div>
		@endif
		
		<div class="flexv centerv user-info">
			<div class="userimg">
				<img src="{{$res->user['head']}}" class="fitimg" style="border-radius: 50%;overflow: hidden;">
			</div>
			<p class="flex center name">{{$res->user['wc_nickname']}}</p>
			<div class="flex centerh mesg">
				<span>健康顾问</span>
				<span>@if($member_time){{$res->user['phone']}}@else{{substr( $res->user['phone'], 0, 3 )}}********@endif</span>
			</div>
			<div class="button">
				<a @if($member_time)href="tel:{{$res->user['phone']}}"@else href='javascript:;' id='phone'@endif class="flex center phone">
					<i class="flex center bls bls-dh"></i>
					<span>给我电话</span>
				</a>
				<a href="javascript:;" class="flex center book" style="background:#07BD13">
					<i class="flex center bls bls-weixin"></i>
					<span>加微信</span>
				</a>
			</div>
			<span class="row"></span>
			<span class="col"></span>
			<span class="row last"></span>
			<span class="col last"></span>
		</div>
		
		{{--<div class="flexv center qrcode">--}}
			{{--<div class="img">--}}
				{{--@if($member_time)--}}
					{{--@if($res->user['id'] == session()->get('user_id'))--}}
						{{--@if($res->user['qrcode'] != '')--}}
						{{--<img src="{{$res->user['qrcode']}}" class="fitimg">--}}
						{{--@else--}}
						{{--<img src="/index/image/upload_qrcode.jpg" class="fitimg">--}}
						{{--<input type="file" accept="image/jpg,image/png,image/jpeg" id="put">--}}
						{{--@endif--}}
					{{--@else--}}
						{{--@if($res->user['qrcode'] != '')--}}
							{{--<img src="{{$res->user['qrcode']}}" class="fitimg">--}}
						{{--@else--}}
							{{--<a href="{{route('chatroom',['id'=>$res->id])}}">--}}
								{{--<img src="/index/image/callme.jpg" class="fitimg">--}}
							{{--</a>--}}
						{{--@endif--}}
					{{--@endif--}}
				{{--@else--}}
					{{--<a href="{{route('chatroom',['id'=>$res->id])}}">--}}
						{{--<img src="/index/image/callme.jpg" class="fitimg">--}}
					{{--</a>--}}
				{{--@endif--}}
			{{--</div>--}}
			{{--<p>马上加我微信沟通</p>--}}

		{{--</div>--}}

		<a href="{{ route('chatroom', $res->user->id) }}" class="flex center bls bls-kefu service"></a>
		
		<div class="flexv center text-box">
			<p>本文为 <span>{{$res->user['wc_nickname']}}</span> 发布，不代表事业头条立场</p>
			<p>若内容不规范或涉及违规，可立即 <a href="{{ route('report',['article_id'=>$res->id,'type'=>2]) }}">举报/报错</a></p>
		</div>

		{{--<a href="javascript:;" id="cut" class="flex center cut">免费换成我的名片 >></a>--}}
	</div>

	@if(session()->get('user_id') != $res->uid)
		<div class="flex center fixed-btn">
			<a href="javascript:;" id="cut" class="flex center cut">免费换成我的名片 >></a>
		</div>
	@endif
	
	<!--提示-->
	<div class="flex center hint">
		<div class="mask"></div>
		<div class='content'>
			<h3 class="flex center">加我免费咨询</h3>
			<div class="qrcode">
				<img src="{{ $res->user->qrcode }}" class="fitimg">
			</div>
			<p class="flex center">长按识别二维码</p>
		</div>
	</div>
	<!--提示-->
	<form class="flex center alert" id="form" action="{{route('perfect_information', session('user_id'))}}">
		{{csrf_field()}}

		<input type="hidden" name="id" value="{{session('user_id')}}">
		<div class="mask"></div>
		<div class='content'>
			<i class="flex center bls bls-cuo cuo"></i>
			<h3 class="flex center title">您的信息不完整</h3>
			<p class="flex center tis">立刻完善资料，让客户找到您</p>
			<div class="flex center input">
				<span class="flex centerv">姓名</span>
				<input type="text" name="wc_nickname" class="flexitem" value="{{ $res->user->wc_nickname }}" data-rule="*" data-errmsg="请填写您的姓名">
			</div>
			<div class="flex center input">
				<span class="flex centerv">手机号</span>
				<input type="text" name="phone" class="flexitem" value="" data-rule="m" data-errmsg="手机号码格式错误">
			</div>
			<div class="flex centerv input brands">
				<span class="flex centerv">品牌</span>
				<input type="text" readonly="readonly" class="flexitem cenk" placeholder="选择品牌" value="{{ $brand->name }}" data-rule="*" data-errmsg="请选择您的品牌" onfocus="this.blur()">
				<input type="hidden" name="brand_id" class="brand_id" value="{{ $brand->id }}">
				<i class="flex smtxt"></i>
				<i class="flex center bls bls-xia brand"></i>
			</div>
			<a href="javascript:;" class="flex center button" id="submit">保存修改</a>
		</div>
	</form>
	<!--品牌-->
	<div id="brand" class="flexv dialog_box">
		<div class="flex center head">
			<a href="javascript:;" class="bls bls-zjt"></a>
			<h1 class="flexitem center" style="margin-left: -2rem;">选择品牌</h1>
		</div>
		<ul class="flexitemv mainbox company" style="padding-top: 20px">

		</ul>
		<ul class="lettrt">
			<li><a href="#">#</a></li>
			<li><a href="#A">A</a></li>
			<li><a href="#B">B</a></li>
			<li><a href="#C">C</a></li>
			<li><a href="#D">D</a></li>
			<li><a href="#E">E</a></li>
			<li><a href="#F">F</a></li>
			<li><a href="#G">G</a></li>
			<li><a href="#H">H</a></li>
			<li><a href="#I">I</a></li>
			<li><a href="#J">J</a></li>
			<li><a href="#K">K</a></li>
			<li><a href="#L">L</a></li>
			<li><a href="#M">M</a></li>
			<li><a href="#N">N</a></li>
			<li><a href="#O">O</a></li>
			<li><a href="#P">P</a></li>
			<li><a href="#Q">Q</a></li>
			<li><a href="#R">R</a></li>
			<li><a href="#S">S</a></li>
			<li><a href="#T">T</a></li>
			<li><a href="#U">U</a></li>
			<li><a href="#V">V</a></li>
			<li><a href="#W">W</a></li>
			<li><a href="#X">X</a></li>
			<li><a href="#Y">Y</a></li>
			<li><a href="#Z">Z</a></li>
		</ul>
	</div>
</div>
</body>
<script src="https://cdn.bootcss.com/zepto/1.2.0/zepto.min.js"></script>
<script type="text/javascript" src="http://res.wx.qq.com/open/js/jweixin-1.2.0.js"></script>
<script src="https://cdn.bootcss.com/Swiper/3.4.2/js/swiper.min.js"></script>
<script type="text/javascript" src="/index/js/checkform.js"></script>
<script type="text/javascript" src="/index/js/functions.js"></script>
<script src="https://cdn.bootcss.com/lodash.js/4.17.4/lodash.min.js"></script>
<script src="https://cdn.bootcss.com/clipboard.js/1.5.15/clipboard.min.js"></script>
<script src="http://res.wx.qq.com/open/js/jweixin-1.0.0.js"></script>
<script type="text/javascript">
    $('#cut').click(function () {
		@if(!$user->brand_id && !$user->phone)
			$(".alert").css({"display":"block"});
			$(".alert").find(".content").addClass('trans');
			//  品牌
			@include('index.public._brand_list')
		@else
			window.location.href = "{{ route('become_my_article',['user_id'=>session()->get('user_id'),'article_id'=>$res->article['id'],'pid'=>$res->uid]) }}";
		@endif
    });

    //	关闭
    $(".cuo").click(function(){
        $(".alert").css({"display":"none"});
    });

	@if(!$user->brand_id && !$user->phone)
		new checkForm({
			form : '#form',
			btn : '#submit',
			error : function (ele,err){showMsg(err);},
			complete : function (ele){
				var url = $(ele).attr('action'),post = $(ele).serializeArray();
				showProgress('正在提交');
				console.log(post);
				$.post(url,post,function (ret){
					hideProgress();
					if(ret.state == 0) {
						showMsg('完善资料成功', 1, 2000);
						setTimeout(function () {
							window.location.href = "{{ route('become_my_article',['user_id'=>session('user_id'),'article_id'=>$res->article['id'],'pid'=>$res->uid]) }}";
						}, 2000);
					} else {
						showMsg('完善资料失败');
					}
				},'json');
			}
		});
    @endif
</script>

<script type="text/javascript">
	//  展示全部
	$(".unfold").click(function () {
        $(".title").removeClass('max');
        $(".unfold").text('');
    });

    $('#phone').click(_.throttle(function () {
        showMsg('该用户未开通此服务');
        $.get("{{ route('tip_user_qrcode', $res->user->id) }}", function () {});
    }, 3000, { 'trailing': false }));

	//	加微信
	@if(\Carbon\Carbon::parse($res->user->membership_time)->gt(\Carbon\Carbon::now()))
		@if($res->user->qrcode)
			$(".book").click(function () {
				$(".hint").css({"display":"block"});
				$(".hint").find(".content").addClass('trans');
			});
		@else
			@if($res->uid == session('user_id'))
				$(".book").click(function () {
					showMsg('您尚未上传二维码', 0, 1500);
				});
			@else
				$(".book").click(function () {
					showMsg('该用户尚未上传二维码', 0, 1500);
				});
			@endif
		@endif
        $(".mask").click(function(){
            $(".hint").css({"display":"none"});
        });
	@else
		$(".book").click(_.throttle(function () {
			showMsg('该用户未开通此服务');
			$.get("{{ route('tip_user_qrcode', $res->user->id) }}", function () {});
		}, 3000, { 'trailing': false }));
	@endif




	//上传个人二维码
	$("#put").change(function (event) {
		var file = event.target.files[0];
		if(file){
			var reader = new FileReader();
			reader.onload=function (event) {
				var image = event.target.result;
                $(".qrcode .img img").attr('src',image);
				$.post("{{route('upload_qrcode')}}",{url:image, _token:"{{csrf_token()}}"}, function (ret) {
					if(ret.state == 0){
					    showMsg(ret.errormsg, 1);
					} else {
					    showMsg(ret.errormsg);
					}
                });
			};
			reader.readAsDataURL(file);
		}
	});

	wx.config({
        debug: false, // 开启调试模式,调用的所有api的返回值会在客户端alert出来，若要查看传入的参数，可以在pc端打开，参数信息会通过log打出，仅在pc端时才会打印。
        appId: '{{$package['appId']}}', // 必填，公众号的唯一标识
        timestamp: {{$package['timestamp']}}, // 必填，生成签名的时间戳
        nonceStr: '{{$package['nonceStr']}}', // 必填，生成签名的随机串
        signature: '{{$package['signature']}}',// 必填，签名，见附录1
        jsApiList: ['onMenuShareTimeline', 'onMenuShareAppMessage'] // 必填，需要使用的JS接口列表，所有JS接口列表见附录2
    });

//----------------记录阅读时间-------------------
logid = "{{ $footid }}";
logTime();

function logTime() {
    if (logid) {
        //1-5随机数
        var v_random = Math.floor(Math.random() * 5 + 1);
        var c_random = Math.floor(Math.random() * 29 + 1);
        //调用函数
        function logReadTime(time, random) {
			$.ajax({
				url: "{{route('user_article_time')}}",
				data: {
                    id: logid,
                    time: time + random,
					_token:"{{csrf_token()}}"
                },
				dataType: "json",
				type: "post",
				success: function (res) {

				},
				error: function () {}
			});
        }

        function setTime(second) {
            var a5 = setInterval(function () {
                logReadTime(second, v_random);
            }, second * 1000);
            setInterval(function () {
                clearInterval(a5);
            }, second * 1000 + 10);
        }

        function setTime_later(second) {
            var a6 = setInterval(function () {
                logReadTime(second, c_random);
            }, second * 1000);
            setInterval(function () {
                clearInterval(a6);
            }, second * 1000 + 10);
        }
        //-------1分钟前记录--------
        for (var i = 5; i < 61; i++) {
            setTime(i);
            i = i + 4;
        }
        //-------1分钟后记录--------
        for (var j = 90; j < 601; j++) {
            setTime_later(j);
            j = j + 29;
        }
    }
}
/*******************/
	@if(strstr($res->user['head'],'http'))
	var head = "{{ $res->user['head'] }}";
	@else
	var head = 'http://bw.eyooh.com{{ $res->user['head'] }}';
	@endif
    wx.ready(function(){
        //分享微信好友
        wx.onMenuShareAppMessage({
            title: '{{$res->article['title']}}', // 分享标题1
            desc: '{!! subtext(preg_replace('/&[a-z]+;/i',"", str_replace("\n","",preg_replace('/<\/?[^>]+>/i',"",$res->article['details']))),80) !!}', // 分享描述
            link: '{{route('user_article_details',['id'=>$res->id, 'ex_id'=>session('user_id')])}}', // 分享链接，该链接域名或路径必须与当前页面对应的公众号JS安全域名一致
            imgUrl: head, // 分享图标
            success: function () {
                // 用户确认分享后执行的回调函数
                $.get("{{route('user_article_share',['id'=>$res->id, 'ex_id'=>session('user_id')])}}",function (ret) {

                });
				return false;
            }
        });

        //分享朋友圈
        wx.onMenuShareTimeline({
            title: '{{$res->article['title']}}', // 分享标题
            link: '{{route('user_article_details',['id'=>$res->id, 'ex_id'=>session('user_id')])}}', // 分享链接，该链接域名或路径必须与当前页面对应的公众号JS安全域名一致
            imgUrl: head, // 分享图标
            success: function () {
                // 用户确认分享后执行的回调函数
                $.get("{{route('user_article_share',['id'=>$res->id, 'ex_id'=>session('user_id')])}}",function (ret) {

                });
                return false;
            }
        });
    });

	@includeWhen($res->article->type == 3, 'index.public._audi')
</script>
</html>