    {{Form::model($leavetype,array('route' => array('leavetype.update', $leavetype->id), 'method' => 'PUT', 'class'=>'needs-validation', 'novalidate')) }}
    <div class="modal-body">

        <div class="col-md-12">
            <div class="form-group">
                {{Form::label('title',__('Description'),['class'=>'form-label'])}}
                {{Form::text('description',null,array('class'=>'form-control','placeholder'=>__('Enter Leave Type Name')))}}
            </div>
        </div>

    </div>
    </div>

    {{Form::close()}}
