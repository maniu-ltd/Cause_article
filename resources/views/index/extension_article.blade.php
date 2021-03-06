<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0,minimum-scale=1.0,maximum-scale=1.0,user-scalable=no"/>
    <meta name="format-detection" content="telephone=no">
    <title>推荐文章</title>
    @include('index.public.css')
</head>
<body id="text" class="flexv warp">
    <form action="{{ route('extension_article_post') }}" method="post">
        {{ csrf_field() }}
        <div class="report-text-area">
            <div class="text-input-wrap">
                <textarea name="url" id="tip_content" class="text-input" cols="30" rows="10" maxlength="150" placeholder="推荐文章时请填写推荐的文章链接，文章添加后会通知您" data-rule="*" data-errmsg="请填下文章链接"></textarea>
                <div class="text-num-tip">
                    <span class="now-num">0</span>/<span class="max-num">150</span>
                </div>
            </div>
            @if (count($errors) > 0)
                <div style="height: 30px;line-height: 30px;font-size: 16px;color: red;font-weight: bold;text-align: center;">文章链接不能为空或不是正确的链接地址</div>
            @endif
            @if (Session::has('err'))
                <div style="height: 30px;line-height: 30px;font-size: 16px;color: red;font-weight: bold;text-align: center;">{{ Session::get('err') }}</div>
            @endif
            <button id="button-alt">提交</button>
        </div>
    </form>
</body>
<script src="https://cdn.bootcss.com/jquery/3.2.1/jquery.min.js"></script>
<script type="text/javascript">
    //字数统计
    var $textInput = $('.text-input');
    var $nowNumSpan = $('.now-num');
    var timer = null;
    var beforeLen = 0;
    $textInput.on('focus', function(e) {
        timer = window.setInterval(function() {
            var text = $textInput.val();
            var nowLen = text.length;
            if(nowLen !== beforeLen) {
                beforeLen = nowLen;
                $nowNumSpan.text(nowLen)
            }
        }, 200)
    }).on('blur', function() {
        timer && window.clearInterval(timer)
    });
</script>
</html>