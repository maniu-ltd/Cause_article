<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0,minimum-scale=1.0,maximum-scale=1.0,user-scalable=no"/>
    <meta name="format-detection" content="telephone=no,email=no,date=no,address=no">
    <title>美图列表</title>
    @include('index.public.css')
</head>
<body>
<div id="more" class="flexv wrap">
    <div class="flex tex-t">
        @if(session('brand_id'))
            <a href="{{ route('extension_photo_list') }}" class="flex center @if(!request()->type) current @endif">
                {{ session('brand_name') }}
            </a>
            @foreach($types as $value)
                <a href="{{ route('extension_photo_list', $value->id) }}" class="flex center @if(request()->type) @if($value->id == request()->type) current @endif @endif">
                    {{ $value->name }}
                </a>
            @endforeach
        @else
            @foreach($types as $value)
                <a href="{{ route('extension_photo_list', $value->id) }}" class="flex center @if(request()->type) @if($value->id == request()->type) current @endif @else @if($loop->first) current @endif @endif">
                    {{ $value->name }}
                </a>
            @endforeach
        @endif


    </div>
    <div class="flexitemv mainbox">
        <div class="fwrap listbox">
            @foreach($photos as $photo)
                <a href="{{ route('extension_poster', $photo->id) }}" class="flexv imgbox">
                    <div class="flex center">
                        <img data-original="{{ $photo->url }}" src="/index/image/loading.gif" class="lazy">
                    </div>
                    <div class="flexv center tit">{{ $photo->name }}</div>
                </a>
            @endforeach
        </div>
    </div>
</div>
</body>
<script src="https://cdn.bootcss.com/zepto/1.2.0/zepto.min.js"></script>
<script src="/index/js/lazyload.js"></script>
<script type="text/javascript">
    $(".lazy").lazyload({
        event: "scrollstop",
        effect : "fadeIn",
        container: $(".fwrap.listbox"),
        load:function ($e) {
            $e.css({"width":"100%","height":"100%"});
        }
    });
</script>
</html>