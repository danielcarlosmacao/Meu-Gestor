@extends('layouts.header')
@section('title', 'Mural de Ideias')

@section('content')
<style>
    #mural-area {
        width: 100%;
        height: 80vh;
        border: 2px dashed #ccc;
        position: relative;
        overflow: hidden;
        cursor: crosshair;
    }

    .postit {
        padding: 10px;
        border: 1px solid #ccc;
        border-radius: 8px;
        box-shadow: 2px 2px 10px rgba(0,0,0,0.2);
        position: absolute;
        overflow: auto;
        resize: both;
        min-width: 100px;
        min-height: 100px;
        box-sizing: border-box;
    }

    .text-area {
        width: 100%;
        height: 100%;
        overflow: auto;
        outline: none;
        cursor: text;
    }

    .close-btn {
        position: absolute;
        top: 5px;
        right: 8px;
        cursor: pointer;
        color: red;
        font-weight: bold;
        user-select: none;
        z-index: 10;
    }

    .drag-icon {
        position: absolute;
        bottom: 5px;
        right: 8px;
        cursor: move;
        user-select: none;
        z-index: 10;
    }

    #postitModal {
        display: none;
        position: fixed;
        z-index: 9999;
        left: 0; top: 0; width: 100%; height: 100%;
        background-color: rgba(0,0,0,0.5);
        justify-content: center; align-items: center;
    }

    #postitModal .modal-content {
        background: white;
        padding: 20px;
        border-radius: 8px;
        width: 300px;
        text-align: center;
    }

    .color-option {
        width: 30px;
        height: 30px;
        display: inline-block;
        border: 1px solid #ccc;
        margin: 0 5px;
        cursor: pointer;
        border-radius: 4px;
        vertical-align: middle;
    }

    .color-option.selected {
        border: 3px solid #333;
    }

    #modal-buttons {
        margin-top: 15px;
        display: flex;
        justify-content: center;
        gap: 20px;
    }

    #modal-buttons button {
        padding: 8px 20px;
        border: none;
        border-radius: 5px;
        cursor: pointer;
    }

    #btnCancel {
        background: #ccc;
    }

    #btnOk {
        background: #28a745;
        color: white;
    }

    #justificationBox {
        margin-top: 10px;
        display: none;
    }
</style>

<script>
    const loggedUserId = {{ Auth::id() }};
</script>

<div class="container mb-1 mb-md-3 mt-1 mt-md-3">
    <h2 class="text-center">Meu Mural</h2>
</div>

<div id="mural-area">
    @foreach($postits as $postit)
        <div class="postit" data-id="{{ $postit->id }}"
             style="left: {{ $postit->pos_x }}px; top: {{ $postit->pos_y }}px; 
                    background-color: {{ $postit->color }};
                    width: {{ $postit->width }}px; height: {{ $postit->height }}px;">
            <div class="text-area" contenteditable="true">{{ $postit->content }}</div>
            <span class="close-btn">X</span>
            <span class="drag-icon">🖐️</span>
        </div>
    @endforeach
</div>

<!-- Modal -->
<div id="postitModal">
    <div class="modal-content">
        <h3>Escolha a cor do post-it</h3>
        <div id="modalColors">
            <div class="color-option" data-color="#fffa65" style="background:#fffa65"></div>
            <div class="color-option" data-color="#ffb347" style="background:#ffb347"></div>
            <div class="color-option" data-color="#ff6961" style="background:#ff6961"></div>
            <div class="color-option" data-color="#77dd77" style="background:#77dd77"></div>
            <div class="color-option" data-color="#aec6cf" style="background:#aec6cf"></div>
        </div>

        <!-- Select de usuários -->
        <div style="margin-top: 15px;">
            <label for="user_id">Atribuir para:</label>
            <select id="user_id" class="form-control" style="width: 100%; margin-top: 5px;">
                @foreach($users as $user)
                    <<option value="{{ $user->id }}" {{ $user->id == Auth::id() ? 'selected' : '' }}>{{ $user->name }}</option>
                @endforeach
            </select>
        </div>

        <!-- Justificativa -->
        <div id="justificationBox">
            <label for="userComment">Justificativa:</label>
            <textarea id="userComment" class="form-control" style="width: 100%; margin-top: 5px;" rows="3"></textarea>
        </div>

        <div id="modal-buttons">
            <button id="btnCancel" type="button">Cancelar</button>
            <button id="btnOk" type="button">OK</button>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(function(){
    let selectedColor = '#fffa65';
    let clickX = 0, clickY = 0;

    // Mostrar/esconder justificativa
    $('#user_id').on('change', function() {
        const selectedUser = $(this).val();
        if (selectedUser != loggedUserId) {
            $('#justificationBox').show();
        } else {
            $('#justificationBox').hide();
            $('#userComment').val('');
        }
    });

    // Abrir modal ao clicar no mural
    $('#mural-area').click(function(e){
        if ($(e.target).closest('.postit').length > 0) return;

        clickX = e.pageX - $(this).offset().left;
        clickY = e.pageY - $(this).offset().top;

        $('#postitModal').css('display', 'flex');

        $('#modalColors .color-option').removeClass('selected');
        $('#modalColors .color-option[data-color="'+selectedColor+'"]').addClass('selected');
    });

    $('#modalColors').on('click', '.color-option', function(){
        $('#modalColors .color-option').removeClass('selected');
        $(this).addClass('selected');
        selectedColor = $(this).data('color');
    });

    $('#btnCancel').click(function(){
        $('#postitModal').hide();
    });

    $('#btnOk').click(function(){
        const userId = $('#user_id').val();
        const contentText = userId != loggedUserId ? $('#userComment').val() : 'Digite sua ideia...';

        $.ajax({
            url: '{{ route('postits.store') }}',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                pos_x: clickX,
                pos_y: clickY,
                color: selectedColor,
                content: contentText,
                width: 200,
                height: 200,
                user_id: userId
            },
            success: function(){
                $('#postitModal').hide();
                location.reload();
            },
            error: function(xhr){
                alert('Erro ao criar post-it');
                console.error(xhr.responseText);
            }
        });
    });

    // Exclusão
    $('#mural-area').on('click', '.close-btn', function(e) {
        e.stopPropagation();
        const postit = $(this).closest('.postit');
        const id = postit.data('id');

        if (confirm('Tem certeza que deseja excluir este post-it?')) {
            $.ajax({
                url: '/postits/' + id,
                type: 'DELETE',
                data: {_token: '{{ csrf_token() }}'},
                success: function() {
                    postit.remove();
                }
            });
        }
    });

    // Editar texto
    $('#mural-area').on('blur', '.text-area', function() {
        const postit = $(this).closest('.postit');
        const id = postit.data('id');
        const content = $(this).text();

        $.ajax({
            url: '/postits/' + id,
            type: 'PUT',
            data: {
                _token: '{{ csrf_token() }}',
                content: content
            }
        });
    });

    // Drag
    let dragging = null, offsetX = 0, offsetY = 0;

    $('#mural-area').on('mousedown', '.drag-icon', function(e) {
        e.stopPropagation();
        dragging = $(this).closest('.postit');
        offsetX = e.pageX - dragging.position().left;
        offsetY = e.pageY - dragging.position().top;

        $(document).on('mousemove.dragPostit', function(e2) {
            if (dragging) {
                dragging.css({
                    left: e2.pageX - offsetX + 'px',
                    top: e2.pageY - offsetY + 'px'
                });
            }
        });

        $(document).on('mouseup.dragPostit', function() {
            if (dragging) {
                const id = dragging.data('id');
                $.ajax({
                    url: '/postits/' + id,
                    type: 'PUT',
                    data: {
                        _token: '{{ csrf_token() }}',
                        pos_x: parseInt(dragging.css('left')),
                        pos_y: parseInt(dragging.css('top'))
                    }
                });

                $(document).off('.dragPostit');
                dragging = null;
            }
        });
    });

    // Resize
    $('#mural-area').on('mouseup', '.postit', function() {
        const postit = $(this);
        const id = postit.data('id');
        const width = postit.width();
        const height = postit.height();

        $.ajax({
            url: '/postits/' + id,
            type: 'PUT',
            data: {
                _token: '{{ csrf_token() }}',
                width: width,
                height: height
            }
        });
    });
});
</script>
@endsection
