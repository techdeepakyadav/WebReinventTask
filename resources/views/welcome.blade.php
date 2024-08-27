<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>Todo Task</title>
        <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
        <script src="https://code.jquery.com/jquery-3.1.1.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    </head>
    <body>
        <div class="container mt-3">
            <div class="card bg-light m-5">
                <div class="card-header">
                    <div class="row g-3">
                        <div class="col-auto">
                            <button type="button" class="btn btn-primary btn-block" id="add_todo"> Add Task </button>
                        </div>
                        <div class="col-auto">
                            <button type="submit"  class="btn btn-secondary btn-block" id="showAllTask">Show All Task</button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <table class="table table-bordered mt-3" id="secondTable">
                        <thead>
                            <tr>
                                <th width="50">#</th>
                                <th>Name</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody id="showAllRecords">
                            <!-- Existing data -->
                        </tbody>
                    </table>

                    <table class="table table-bordered mt-3" id="firstTable">
                        <thead>
                            <th  width="50">#</th>
                            <th>Name</th>
                            <th>Status</th>
                            <th width="100">Action</th>
                        </thead>
                        <tbody id="list_todo">
                            @foreach($todos as $todo)
                                @if($todo->status == 'Pending')
                                <tr id="row_todo_{{$todo->id}}">
                                    <td>{{$todo->id}}</td>
                                    <td>{{$todo->name}}</td>
                                    <td>{{$todo->status}}</td>
                                    <td>
                                        <button id="status" data-id="{{$todo->id}}" class="btn btn-sm btn-success"><i class="fa-regular fa-square-check"></i></button>
                                        <button id="delete_todo" data-id="{{$todo->id}}" class="btn btn-sm btn-danger ml-1"><i class="fa-regular fa-trash-can"></i></button>
                                    </td>
                                </tr>
                                @endif
                            @endforeach
                        </tbody>
                    </table>
        
                </div>
            </div>            

            <div class="modal fade" id="modal_todo">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <form action="" id="form_todo" method="post">
                            <div class="modal-header">
                                <h4 class="modal-title" id="modal_title"></h4>
                                <button type="button" class="close" data-dismiss="modal" id="dismissBtn">x</button>
                            </div>
                            <div class="modal-body">
                                <!-- Error Messase -->
                                <div class="alert alert-danger alert-dismissible fade show print-error-msg" role="alert" style="display:none">
                                    <ul></ul>
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <!-- ./Error Messase -->
                                <input type="hidden" name="id" id="id">
                                <input type="text" name="name" id="name_todo" class="form-control" placeholder="Title" autofocus />
                                <span class="text-danger error-text title_error" style="font-size: 13px"></span>
                            </div>

                            <div class="modal-footer">
                                <button type="submit" class="btn btn-success btn-sm">Submit</button>
                                <button class="btn btn-danger btn-sm" data-dismiss="modal" id="closeBtn">Close</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <script>
            $(document).ready(function(){
                $("#secondTable").css("display", "none");
                $.ajaxSetup({
                    headers:{
                        'x-csrf-token' : $('meta[name="csrf-token"]').attr('content')
                    }
                })
            });

            $("#add_todo, #closeBtn, #dismissBtn").on('click', function(){
                $("#form_todo").trigger('reset');
                $("#modal_title").html('Add Todo');
                $("#modal_todo").modal('show');
                $(".print-error-msg").css('display','none');
            });

            $("form").on('submit',function(e){
                e.preventDefault();
                $.ajax({
                    url:"todo/store",
                    data: $("#form_todo").serialize(),
                    type:'POST'
                }).done(function(res){
                    if($.isEmptyObject(res.error)){
                        var row = '<tr id="row_todo_'+res.id+'">';
                        row += '<td>' + res.id + '</td>';
                        row += '<td>' + res.name + '</td>';
                        row += '<td>' + "Pending" + '</td>';
                        row += '<td>' + '<button id="status" data-id="'+res.id+'" class="btn btn-sm btn-success"><i class="fa-regular fa-square-check"></i></button>' +'<button id="delete_todo" data-id="'+res.id+'" class="btn btn-sm btn-danger ml-2"><i class="fa-regular fa-trash-can"></i></button>'+'</td>';

                        if($('#id').val()){
                            $("#row_todo_"+res.id).replaceWith(row)
                        }else{
                            $("#firstTable").show();
                            $("#secondTable").hide();
                            $("#list_todo").prepend(row)
                        }
                        $("#form_todo").trigger('reset');
                        $("#modal_todo").modal('hide');
                    }else{
                        printErrorMsg(res.error);
                    }
                    
                });
            });

            // Status update
            $("body").on('click','#status',function(){
                var id = $(this).data('id');
                if(confirm('Are you sure want to change status?')) {
                    $.ajax({
                    type:'POST',
                    url: "todos/update/" + id,
                    }).done(function(res){
                        $("#row_todo_" + id).remove();
                    });
                }
            });

            // Delete Record
            $("body").on('click','#delete_todo',function(){
                var id = $(this).data('id');
                if(confirm('Are you sure want to delete?')) {
                    $.ajax({
                    type:'DELETE',
                    url: "todos/delete/" + id,
                    }).done(function(res){
                        $("#row_todo_" + id).remove();
                    });
                }
            });

            // Show all task
            $("body").on('click','#showAllTask',function(){
                $.ajax({
                type:'GET',
                url: "todos",
                }).done(function(response){
                    var tableRows = '';
                    $.each(response, function(key, value) {
                        tableRows +=
                            '<tr>' +
                                '<td>' + value.id + '</td>' +
                                '<td>' + value.name + '</td>' +
                                '<td>' + value.status + '</td>' +
                            '</tr>';
                    });

                    $("#firstTable").hide();
                    $("#secondTable").show();
                    
                    $('#showAllRecords').html(tableRows);
                }).fail(function(jqXHR, textStatus, errorThrown) {
                    console.error("Error: " + textStatus, errorThrown);
                });
                    
            });

            function printErrorMsg (msg) {
                $(".print-error-msg").find("ul").html('');
                $(".print-error-msg").css('display','block');
                $.each( msg, function( key, value ) {
                    $(".print-error-msg").find("ul").append('<li>'+value+'</li>');
                });
            }
        </script>
    </body>
</html>
