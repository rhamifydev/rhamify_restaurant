@extends('admin.layouts.master')

@section('content')
<section class="section">
    <div class="section-header">
        <h1>Chat Box</h1>
        <div class="section-header-breadcrumb">
            <div class="breadcrumb-item active"><a href="#">Dashboard</a></div>
            <div class="breadcrumb-item"><a href="#">Components</a></div>
            <div class="breadcrumb-item">Chat Box</div>
        </div>
    </div>

    <div class="section-body">


        <div class="row align-items-center justify-content-center">
            <div class="col-12 col-sm-6 col-lg-3">
                <div class="card" style="height: 70vh">
                    <div class="card-header">
                        <h4>Who's Online?</h4>
                    </div>
                    <div class="card-body">
                        <ul class="list-unstyled list-unstyled-border">
                            @foreach ($senders as $sender)
                            @php
                                $chatUser = \App\Models\User::find($sender->sender_id);
                                $unseenMessages = \App\Models\Chat::where(['sender_id' => $chatUser->id, 'receiver_id' => auth()->user()->id, 'seen' => 0])->count();

                            @endphp
                            <li class="media fp_chat_user" data-name="{{ $chatUser->name }}" data-user="{{ $chatUser->id }}" style="cursor: pointer">
                                <img alt="image" class="mr-3 rounded-circle " width="50"
                                    src="{{ $chatUser->avatar_url }}" style="width: 50px;height: 50px; object-fit: cover;">
                                <div class="media-body">
                                    <div class="mt-0 mb-1 font-weight-bold">{{ $chatUser->name }}</div>
                                    <div class="text-warning text-small font-600-bold got_new_message">
                                        @if ($unseenMessages > 0)
                                        <i class="beep"></i>new message
                                        @endif
                                    </div>
                                </div>
                            </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-lg-9">
                <div class="card chat-box" id="mychatbox" data-inbox="" style="height: 70vh">
                    <div class="card-header">
                        <h4 id="chat_header"></h4>
                    </div>
                    <div class="card-body chat-content">

                    </div>

                    <div class="card-footer chat-form">
                        <form id="chat-form">
                            @csrf
                            <input type="text" class="form-control fp_send_message" placeholder="Type a message" name="message">
                            <input type="hidden" name="receiver_id" id="receiver_id" value="">
                            <input  type="hidden"  name="msg_temp_id" class="msg_temp_id" value="">


                            <button class="btn btn-primary">
                                <i class="far fa-paper-plane"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>
@endsection

@push('scripts')
    <script>
        $(document).ready(function(){
            var userId = "{{ auth()->user()->id }}"
            var avatar = "{{ auth()->user()->avatar_url }}";
            $('#receiver_id').val("");
            function escapeHtml(value) {
                return $('<div>').text(value ?? '').html();
            }

            function scrollToBootom(){
                let chatContent = $('.chat-content');
                chatContent.scrollTop(chatContent.prop("scrollHeight"));
            }

            function appendChatMessage(message, avatarUrl, senderId) {
                let chatMessage = escapeHtml(message);
                let safeAvatar = escapeHtml(avatarUrl || "{{ asset('/uploads/avatar.png') }}");
                let itemClass = senderId == userId ? 'chat-right' : 'chat-left';
                let html = `
                    <div class="chat-item ${itemClass}" style=""><img style="width:50px;height:50px;object-fit:cover;" src="${safeAvatar}"><div class="chat-details"><div class="chat-text">${chatMessage}</div></div></div>
                `;

                $('.chat-content').append(html);
                scrollToBootom();
            }

            function markSenderHasNewMessage(senderId, senderName, avatarUrl) {
                let senderItem = $(`.fp_chat_user[data-user="${senderId}"]`);

                if (!senderItem.length) {
                    let safeName = escapeHtml(senderName || 'Customer');
                    let safeAvatar = escapeHtml(avatarUrl || "{{ asset('/uploads/avatar.png') }}");
                    let html = `
                        <li class="media fp_chat_user" data-name="${safeName}" data-user="${senderId}" style="cursor: pointer">
                            <img alt="image" class="mr-3 rounded-circle" width="50" src="${safeAvatar}" style="width: 50px;height: 50px; object-fit: cover;">
                            <div class="media-body">
                                <div class="mt-0 mb-1 font-weight-bold">${safeName}</div>
                                <div class="text-warning text-small font-600-bold got_new_message"><i class="beep"></i>new message</div>
                            </div>
                        </li>
                    `;
                    $('.list-unstyled-border').prepend(html);
                    return;
                }

                senderItem.find('.got_new_message').html('<i class="beep"></i>new message');
                $('.list-unstyled-border').prepend(senderItem);
            }

            // fetch conversations
            $('body').on('click', '.fp_chat_user', function(){
                let senderId = $(this).data('user');
                let senderName = $(this).data('name')
                let clickedElemnt = $(this);
                $('#mychatbox').attr('data-inbox', senderId);
                $('#receiver_id').val(senderId);
                $.ajax({
                    method: 'GET',
                    url: '{{ route("admin.chat.get-conversation", ":senderId") }}'.replace(":senderId", senderId),
                    beforeSend: function() {
                        $('.chat-content').empty();
                        $('#chat_header').text("Chat With "+senderName);
                    },
                    success: function(response) {
                        $('.chat-content').empty();

                        $.each(response, function(index, message){
                            appendChatMessage(message.message, message.sender.avatar_url, message.sender_id);

                        })

                        clickedElemnt.find(".got_new_message").html("")

                        scrollToBootom()

                    },
                    error: function(xhr, status, error) {

                    }

                })
            })

            if (window.Echo) {
                window.Echo.private(`chat.${userId}`).listen('ChatEvent', function(e) {
                    let activeInbox = $('#mychatbox').attr('data-inbox');

                    if (String(activeInbox) === String(e.senderId)) {
                        appendChatMessage(e.message, e.avatar, e.senderId);
                        $(`.fp_chat_user[data-user="${e.senderId}"]`).find('.got_new_message').html('');
                    } else {
                        markSenderHasNewMessage(e.senderId, e.senderName, e.avatar);
                        $('.message-envelope').addClass('beep');
                    }
                });
            }

            // send message
            $('#chat-form').on('submit', function(e){
                e.preventDefault();
                var msgId = Math.floor(Math.random() * (1 - 10000 + 1)) + 10000;
                $('.msg_temp_id').val(msgId)

                let formData = $(this).serialize();
                $.ajax({
                    method: 'POST',
                    url: "{{ route('admin.chat.send-message') }}",
                    data: formData,
                    beforeSend: function(){
                    let message = $('.fp_send_message').val();
                    let chatMessage = escapeHtml(message);
                    let html = `
                            <div class="chat-item chat-right" style=""><img src="${avatar}"><div class="chat-details"><div class="chat-text">${chatMessage}</div><div class="chat-time ${msgId}">sending...</div></div></div>
                            `

                        $('.chat-content').append(html);
                        $('.fp_send_message').val("");
                        scrollToBootom()

                        // remove beep notification

                        $(".fp_chat_user").each(function(){
                            let senderId = $(this).data('user');

                            if($('#mychatbox').attr('data-inbox') == senderId) {
                                $(this).find(".got_new_message").html("");
                            }
                        })
                    },
                    success: function(response){
                        if($('.msg_temp_id').val() == response.msgId){
                            console.log($('.'+msgId))
                            $('.'+msgId).remove();
                        }
                    },
                    error: function(xhr, status, error){
                        let errors = xhr.responseJSON.errors;
                        $.each(errors, function(key, value){
                            toastr.error(value);
                        })
                    }
                })
            })
        })
    </script>
@endpush
