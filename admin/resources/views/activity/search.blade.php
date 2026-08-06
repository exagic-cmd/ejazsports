<div class="table-responsive" >
<table id="myTable" class="table table-hover">
    <thead>
    <tr>
        <th>#Sr</th>
        <th scope="col">Log Name</th>
        <th scope="col">Description</th>
        <th scope="col">Causer Type</th>
        <th scope="col">Causer Id</th>
        <th scope="col">Date</th>

    </tr>
    </thead>
    <tbody><?php $sr = 1;?>
    @foreach($activities as $activity)
        @if($activity->log_name == 'Create')
            <tr     style="background-color: #d8f1e5;">
        @elseif($activity->log_name == 'Update')
            <tr style="background-color: #f1e5d8;">
        @elseif($activity->log_name == 'Delete')
            <tr style="background-color: #f1d8dd">
        @else
            <tr>
                @endif
                <td>{{$sr++}}</td>
                <td><b>{{$activity->log_name}}</b></td>
                <td><?php echo $activity->description;?></td>
                <td>{{$activity->causer_type}}</td>

                <td>{{$activity->causer ? $activity->causer->name  : ''}}</td>
                <td>{{date('M d, Y h:i',strtotime($activity->created_at))}}</td>
            </tr>
            @endforeach

    </tbody>
</table>
</div>
