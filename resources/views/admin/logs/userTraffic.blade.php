@extends('admin.layouts')
@section('css')
    <link href="/assets/global/vendor/bootstrap-table/bootstrap-table.min.css" rel="stylesheet">
@endsection
@section('content')
    <div class="page-content container-fluid">
        <div class="panel">
            <div class="panel-heading">
                <h3 class="panel-title">流量变动记录</h3>
            </div>
            <div class="panel-body">
                <form class="form-row">
                    <div class="form-group col-lg-4 col-sm-6">
                        <input type="text" class="form-control" name="email" value="{{Request::query('email')}}" placeholder="用户账号"/>
                    </div>
                    <div class="form-group col-lg-2 col-sm-6 btn-group">
                        <button type="submit" class="btn btn-primary">搜 索</button>
                        <a href="{{route('admin.log.flow')}}" class="btn btn-danger">{{trans('common.reset')}}</a>
                    </div>
                </form>
                <table class="text-md-center" data-toggle="table" data-mobile-responsive="true">
                    <thead class="thead-default">
                    <tr>
                        <th> #</th>
                        <th> 用户账号</th>
                        <th> 订单</th>
                        <th> 变动前流量</th>
                        <th> 变动后流量</th>
                        <th> 描述</th>
                        <th> 发生时间</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($userTrafficLogs as $log)
                        <tr>
                            <td> {{$log->id}} </td>
                            <td>
                                @if(empty($log->user))
                                    【{{trans('common.deleted_item', ['attribute' => trans('common.account')])}}】
                                @else
                                    <a href="{{route('admin.log.flow', ['email' => $log->user->email])}}"> {{$log->user->email}} </a>
                                @endif
                            </td>
                            <td>
                                @if ($log->order_id)
                                    @if($log->order)
                                        @can('admin.order')
                                            <a href="{{route('admin.order', ['id' => $log->order_id])}}"></a>
                                        @else
                                            {{$log->order->goods->name}}
                                        @endcan
                                    @else
                                        【订单已删除】
                                    @endif
                                @endif
                            </td>
                            <td> {{$log->before}} </td>
                            <td> {{$log->after}} </td>
                            <td> {{$log->description}} </td>
                            <td> {{$log->created_at}} </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
            <div class="panel-footer">
                <div class="row">
                    <div class="col-sm-4">
                        共 <code>{{$userTrafficLogs->total()}}</code> 条记录
                    </div>
                    <div class="col-sm-8">
                        <nav class="Page navigation float-right">
                            {{$userTrafficLogs->links()}}
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('javascript')
    <script src="/assets/global/vendor/bootstrap-table/bootstrap-table.min.js"></script>
    <script src="/assets/global/vendor/bootstrap-table/extensions/mobile/bootstrap-table-mobile.min.js"></script>
@endsection
