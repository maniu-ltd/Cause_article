<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width,initial-scale=1.0,minimum-scale=1.0,maximum-scale=1.0,user-scalable=no"/>
	<meta name="format-detection" content="telephone=no">
	<title>开通谁查看我功能</title>
	@include('index.public.css')
</head>
<body>
<div id="open" class="flexv wrap">
	<div class="flexitemv mainbox">
		@if(\Carbon\Carbon::parse('now')->gt(\Carbon\Carbon::parse($user->membership_time)))
			<div class="state">状态：<span>未开通</span></div>
		@else
			<div class="state">有效期至：<span>{{\Carbon\Carbon::parse($user->membership_time)->toDateString()}}</span></div>
		@endif
		<div class="bjimg">
			<img src="/index/image/dredge1.jpg" class="fitimg">
			<img src="/index/image/dredge2.jpg" class="fitimg">
			<img src="/index/image/dredge3.jpg" class="fitimg">

			<div class="flex center optionbox">
				<div class="flex center option">
					<div class="discount">
						<div class="flex list">
							<div class="flex centerv price">
								<span class="flex">1个月</span>
								<em>&yen;49.9</em>
							</div>
							<div class="flexitem endh ">
								<a href="javascript:;" class="flex center discounts" data-price="49.9" data-type="1" data-title="爆文1个月会员">购买</a>
							</div>
						</div>
						<div class="flex list">
							<div class="flexv">
								<div class="flex centerv price">
									<span class="flex">12个月</span>
									<em>&yen;99</em>
								</div>
								<div class="cost">
									<i class="original">原价 199元</i>
									<span>立省100元</span>
								</div>
							</div>
							<div class="flexitem endh">
								<a href="javascript:;" class="flex center discounts" data-price="99" data-type="2" data-title="爆文12个月会员">优惠抢购</a>
							</div>
							<div class="flex center cornu">优惠</div>
						</div>

						<div class="flex list">
							<div class="flexv">
								<div class="flex centerv price">
									<span class="flex">2年</span>
									<em>&yen;158</em>
								</div>
								<div class="cost">
									<i class="original">原价 498元</i>
									<span>立省340元</span>
								</div>
							</div>
							<div class="flexitem endh ">
								<a href="javascript:;" class="flex center discounts" data-price="158" data-type="3" data-title="爆文2年会员">购买</a>
							</div>
							<div class="flex center cornu">优惠</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="flex center once">
		<a href="javascript:;" class="flex center">立即开通</a>
	</div>
	<!--弹框-->
	<div class="flex center hint">
		<div class="mask"></div>
		<div class='content'>
			<h3 class="flex center">开通成功</h3>
			<div class="img">
				<img src="/index/image/dredge_bj.png" class="fitimg">
			</div>
			<div class="flex center indate">
				<div>有效期至：</div>
				<span>2017-09-15 10:42:20</span>
			</div>
			<div class="button">
				<a href="{{route('read_share', 1)}}" class="flex center">去看看谁查看了我的头条</a>
			</div>
		</div>
	</div>
</div>
</body>
<script type="text/javascript" src="https://cdn.bootcss.com/zepto/1.2.0/zepto.min.js"></script>
<script src="https://cdn.bootcss.com/jquery/3.2.1/jquery.min.js"></script>
<script src="https://cdn.bootcss.com/moment.js/2.18.1/moment.min.js"></script>
<script type="text/javascript" src="/index/js/functions.js"></script>

<script type="text/javascript">
    //  点击回到底部
    window.onload = function () {
        // 点击回到底部
        var h = $(".bjimg").height() - $(document).height() + $(".state").innerHeight(), opbox = $('.optionbox').height(), boxh;
        $(".once a").click(function () {
            $(".mainbox").animate({scrollTop: h}, 200);
        });
        // 手动滚动显/隐购买按钮
        $('.mainbox').scroll(function () {
            boxh = $(".mainbox").scrollTop();
            if (boxh >= h - opbox) {
                $(".once").hide();
            } else {
                $(".once").show();
            }
        });
    }

	var time;
	@if(\Carbon\Carbon::parse('now')->gt(\Carbon\Carbon::parse($user->membership_time)))
	time = moment("{{date('Y-m-d H:i:s',time())}}","YYYY-MM-DD HH:mm:ss");
	@else
	time = moment("{{$user->membership_time}}","YYYY-MM-DD HH:mm:ss");
	@endif
    wx.config(<?php echo $js->config(array('chooseWXPay', 'onMenuShareTimeline', 'onMenuShareAppMessage'), false) ?>);
    wx.error(function(res){
        alert(JSON.stringify(res));
    });

    wx.ready(function(){
        //分享微信好友
        wx.onMenuShareAppMessage({
            title: '{{ $user->wc_nickname }}邀请您一起开通使用事业爆文展业利器！', // 分享标题1
            desc: '超多精彩爆文，每日更新推送，赶紧来开通吧！', // 分享描述
            link: "{{ route('open_member', session('user_id')) }}", // 分享链接，该链接域名或路径必须与当前页面对应的公众号JS安全域名一致
            imgUrl: 'http://bw.eyooh.com/logo.jpg', // 分享图标
            success: function () {}
        });

        //分享朋友圈
        wx.onMenuShareTimeline({
            title: '{{ $user->wc_nickname }}邀请您一起开通使用事业爆文展业利器！', // 分享标题
            link: "{{ route('open_member', session('user_id')) }}", // 分享链接，该链接域名或路径必须与当前页面对应的公众号JS安全域名一致
            imgUrl: 'http://bw.eyooh.com/logo.jpg', // 分享图标
            success: function () {}
        });
    });

	$('.discounts').click(function () {
	    var price = $(this).attr('data-price'),
			type  = $(this).attr('data-type'),
			title = $(this).attr('data-title');
		$.post("{{route('submit_order')}}",{title:title,price:price,type:type,_token:"{{csrf_token()}}"},function (ret) {
		    if (ret.state == 0){
                pay(ret.data,function () {
                    if(type == 1){
                        var newtime = time.add(1, 'months')
					} else if(type == 2){
                        var newtime = time.add(12, 'months')
					} else if(type == 3){
                        var newtime = time.add(24, 'months')
                    }
                    $(".indate span").text(newtime.format("YYYY-MM-D k:mm:ss"));
                    $(".hint").css({"display":"block"});
                    $(".hint").find(".content").addClass('trans');
                });
			} else {
		        showMsg('支付出错啦');
			}
        });
        var pay = function ($config,callback) {
            wx.chooseWXPay({
                timestamp:  $config['timestamp'] ,
                nonceStr: $config.nonceStr,
                package: $config.package,
                signType: $config.signType,
                paySign: $config.paySign, // 支付签名
                success: function (res) {
                    // 支付成功后的回调函数
                    callback && callback(res);
                }, cancel: function (){
                    showMsg('取消了支付');
                }, error: function (res){
                    alert(JSON.stringify(res));
                }
            });
        };
    });

    $(".mask").click(function(){
        $(".hint").css({"display":"none"});
        window.location.reload();
    });

    (function () {
        if (typeof WeixinJSBridge == "object" && typeof WeixinJSBridge.invoke == "function") {
            handleFontSize();
        } else {
            if (document.addEventListener) {
                document.addEventListener("WeixinJSBridgeReady", handleFontSize, false);
            } else if (document.attachEvent) {
                document.attachEvent("WeixinJSBridgeReady", handleFontSize);
                document.attachEvent("onWeixinJSBridgeReady", handleFontSize);
            }
        }
        function handleFontSize() {
            /*设置网页字体为默认大小*/
            WeixinJSBridge.invoke('setFontSizeCallback', {
                'fontSize': 0
            });
            /*重写设置网页字体大小的事件*/
            WeixinJSBridge.on('menu:setfont', function () {
                WeixinJSBridge.invoke('setFontSizeCallback', {
                    'fontSize': 0
                });
            });
        }
    })();
</script>
</html>