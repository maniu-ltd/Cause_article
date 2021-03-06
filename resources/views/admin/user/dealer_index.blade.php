@extends('admin.layout')
@section('content')
<div class="col-xs-12">
    <div class="page-header">
        <h1> {{v('headtitle')}} </h1>
    </div>
    <form class="form-inline" style="margin-bottom: 15px" action="{{route('admin.dealerlist')}}" method="get">
        <select class="form-control" name="key" style="width: 140px">
            <option value="wc_nickname" @if(request()->key == 'wc_nickname') selected @endif>昵称</option>
            <option value="phone" @if(request()->key == 'phone') selected @endif>手机号</option>
        </select>
        <input type="text" name="value" class="input" value="{{request()->value}}">
        <button class="btn btn-sm btn-info" type="submit">&nbsp;搜索&nbsp;</button>
    </form>
    <div class="table-responsive">
        <table id="sample-table-1" class="table table-striped table-bordered table-hover">
            <thead>
            <tr>
                <th>id</th>
                <th>微信昵称</th>
                <th>手机号</th>
                <th>从事品牌</th>
                <th>从业地区</th>
                <th>会员到期时间</th>
                <th>佣金比例</th>
                <th>所属部门员工</th>
                <th>员工推广链接</th>
                <th>推广分成</th>
                <th>创建时间</th>
                <th>操作</th>
            </tr>
            </thead>

            <tbody>
            @foreach($list as $value)
            <tr>
                <td>{{ $value['id'] }}</td>
                <td>{{ $value['wc_nickname'] }}</td>
                <td>{{ $value['phone'] }}</td>
                <td>@if($value['brand']) {{ $value['brand']['name'] }} @endif</td>
                <td>{{ $value['employed_area'] }}</td>
                <td>{{ $value['membership_time'] }}</td>
                <td>@if($value['integral_scale']) {{ $value['integral_scale'] }}% @else 默认比例 @endif</td>
                <td>@if($value['admin'])<color style="color:green">{{ $value['admin']['account'] }}</color>@endif</td>
                <td>
                    @if($value['admin'])
                        <a class="btn btn-xs btn-info" onclick="dealer_url({{ $value['admin_id'] }});">查看</a>
                    @endif
                </td>
                <td>{{ $value['commission'] }} 元</td>
                <td>{{ $value['created_at'] }}</td>
                <td>
                    <div class="visible-md visible-lg hidden-sm hidden-xs btn-group">
                        <a class="btn btn-xs btn-info" onclick="see_commis('{{ route('see_integral', $value['id']) }}');">查看推广金</a>
                        <a class="btn btn-xs btn-info" onclick="set_integral(this, {{ $value['integral_scale'] }});" data-url="{{ route('set_integral_scale', $value['id']) }}">佣金比例设置</a>
                        @if(has_menu($menu,'admin/setMemberTime'))
                            <a href="javascript:;" class="btn btn-xs btn-danger" onclick="setMembertime(this, {{ $value['id'] }}, '{{date('Y-m-d', strtotime($value['membership_time']))}}')" data-url="{{ route('admin.set_member_time') }}">设置会员时间</a>
                        @endif
                    </div>
                </td>
            </tr>
            @endforeach
            </tbody>
        </table>
        <div style="text-align: center">
            {{$list->appends(['type'=>request()->type,'key'=>request()->key,'value'=>request()->value])->links()}}
        </div>
    </div><!-- /.table-responsive -->
</div><!-- /span -->
<script type="text/javascript" src="https://cdn.bootcss.com/jquery/2.2.0/jquery.min.js"></script>
<script type="text/javascript" src="/admin/layer/layer.js"></script>
<script>
    function see_commis(url) {
        $.get(url, function (ret) {
            var content = '<form class="form-horizontal" style="margin-top: 20px">' +
                '<div class="form-group"><label class="col-sm-5 control-label no-padding-right"> 历史推广佣金 </label>' +
                '<label class="col-sm-5 control-label no-padding-left"> ' + ret.history + '元 </label>' +
                '</div>' +
//                '<div class="form-group"><label class="col-sm-5 control-label no-padding-right"> 可用推广佣金 </label>' +
//                '<label class="col-sm-5 control-label no-padding-left"> ' + 1 + '元 </label>' +
//                '</div>' +
                '</form>';
            layer.open({
                type: 1,
                skin: 'layui-layer-rim', //加上边框
                area: ['340px', '370px'], //宽高
                content: content
            });
        });
    }

    function setMembertime(th, id, time) {
        var content = '<form class="form-horizontal" style="margin-top: 20px">' +
            '<div class="form-group">' +
            '<label class="col-sm-3 control-label no-padding-right" style="margin: 4px 10px 0 0"> 会员时间：</label>' +
            '<input type="date" value="'+time+'" class="member-time" >' +
            '</div>' +
            '</form>';
        layer.confirm(content, {
            btn: ['确定','取消'],
            skin: 'layui-layer-rim',
            area: ['370px', '220px']
        }, function(){
            var time = $('.member-time').val(),
                url = $(th).attr('data-url');
            $.post(url, {user_id:id, membership_time:time, _token:"{{ csrf_token() }}"}, function(ret){
                console.log(ret);
                if(ret.state == 0) {
                    layer.msg(ret.error, {icon: 1});
                    setTimeout(function(){
                        window.location.reload();
                    }, 1000)
                }
            });
        });
    }



    function dealer_url(id) {
        var url = "{{ config('app.url') }}user/become_dealer/"+id+"/1";
        var content = '<div class="form-group"><label class="col-sm-2 control-label no-padding-right"> 推广链接： </label>' +
            '<input type="text" class="col-xs-10 col-sm-8" value="' + url + '"/>' +
            '</div>';
        layer.open({
            type: 1,
            skin: 'layui-layer-rim', //加上边框
            area: ['540px', '150px'], //宽高
            content: content
        });
    }

    function set_integral(th, scale) {
        var content = '<form class="form-horizontal" style="margin-top: 20px">' +
            '<div class="form-group">' +
            '<label class="col-sm-4 control-label no-padding-right" style="margin: 4px 10px 0 0"> 佣金百分比：</label>' +
            '<input type="text" value="'+scale+'" class="scale" >' +
            '</div>' +
            '</form>';
        layer.confirm(content, {
            btn: ['确定','取消'],
            skin: 'layui-layer-rim',
            area: ['370px', '220px']
        }, function(){
            var scale = $('.scale').val(),
                url = $(th).attr('data-url');
            $.post(url, {integral_scale:scale, _token:"{{ csrf_token() }}"}, function(ret){
                if(ret.state == 0) {
                    layer.msg(ret.error, {icon: 1});
                    setTimeout(function(){
                        window.location.reload();
                    }, 1000)
                }
            });
        });
    }
</script>
@endsection