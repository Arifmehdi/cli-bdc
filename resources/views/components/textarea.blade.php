  @props([

    'placeholder' => $placeholder,
    'label' => $label,
    'rows' => $rows,
    'name' => $name ?? null,
    'value' => $value ?? null
    // 'clases' => $class ?? null,
  ])

  <div class="col-md-12">
                <div class="form-group">
                    <label class="form-label">{{$label}}</label>
                    <textarea rows="{{ $rows}}" name="{{$name}}" class="form-control" placeholder="{{$placeholder}}">{{$value}}</textarea>
                </div>
            </div>
