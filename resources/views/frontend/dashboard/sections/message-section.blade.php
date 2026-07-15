<div class="tab-pane fade" id="v-pills-message" role="tabpanel"
    aria-labelledby="v-pills-message-tab">
    <div class="fp_dashboard_body fp__change_password">
        <div class="fp__message">
            <h3>Message</h3>
            <div class="fp__chat_area">
                <div class="fp__chat_body">

                </div>
                <form class="fp__single_chat_bottom chat_input">
                    @csrf

                    <input  type="hidden"  name="msg_temp_id" class="msg_temp_id" value="">
                    <input type="text" placeholder="Type a message..." name="message" class="fp_send_message">
                    <input type="hidden" name="receiver_id" value="1">

                    <button class="fp__massage_btn" type="submit"><i class="fas fa-paper-plane"
                            aria-hidden="true"></i></button>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        $(document).ready(function(){
            var userId = "{{ auth()->user()->id }}";
            function escapeHtml(value) {
                return $('<div>').text(value ?? '').html();
            }

            function scrollToBootom(){
                let chatContent = $('.fp__chat_body');
                chatContent.scrollTop(chatContent.prop("scrollHeight"));
            }

            function appendChatMessage(message, avatarUrl, senderId) {
                let avatar = escapeHtml(avatarUrl || "{{ asset('/uploads/avatar.png') }}");
                let chatMessage = escapeHtml(message);
                let html = `<div class="fp__chating ${senderId == userId ? 'tf_chat_right' : ''}"><div class="fp__chating_img"><img src="${avatar}"
                                class="img-fluid w-100" style="border-radius: 50%;">
                        </div><div class="fp__chating_text">
                            <p>${chatMessage}</p>
                        </div>
                    </div>`;

                $('.fp__chat_body').append(html);
                scrollToBootom();
            }

            function setUnreadMessageCount(count) {
                $('.sunseen-message-count').text(Math.max(Number(count) || 0, 0));
            }

            function incrementUnreadMessageCount() {
                let currentCount = Number($('.sunseen-message-count').first().text()) || 0;
                setUnreadMessageCount(currentCount + 1);
            }

            // fetch conversations
            $('.fp_chat_message').on('click', function(){
                let senderId = 1;

                $.ajax({
                    method: 'GET',
                    url: '{{ route("chat.get-conversation", ":senderId") }}'.replace(":senderId", senderId),
                    beforeSend: function() {

                    },
                    success: function(response) {
                        $('.fp__chat_body').empty();

                        $.each(response, function(index, message){
                            appendChatMessage(message.message, message.sender.avatar_url, message.sender_id);
                            setUnreadMessageCount(0);

                        })

                        scrollToBootom()

                    },
                    error: function(xhr, status, error) {

                    }

                })
            })

            if (window.location.hash === '#v-pills-message') {
                $('#v-pills-message-tab').trigger('click');
            }

            if (window.Echo) {
                window.Echo.private(`chat.${userId}`).listen('ChatEvent', function(e) {
                    appendChatMessage(e.message, e.avatar, e.senderId);

                    if ($('#v-pills-message').hasClass('active')) {
                        setUnreadMessageCount(0);
                    } else {
                        incrementUnreadMessageCount();
                    }
                });
            }

            // Send Message
            $('.chat_input').on('submit', function(e){
                e.preventDefault();
                let messageInput = $('.fp_send_message');
                let message = messageInput.val().trim();

                if (!message) {
                    toastr.error('Please type a message.');
                    return;
                }

                var msgId = Math.floor(Math.random() * (1 - 10000 + 1)) + 10000;
                $('.msg_temp_id').val(msgId)

                messageInput.val(message);

                let formData = $(this).serialize();
                $.ajax({
                    method: 'POST',
                    url: "{{ route('chat.send-message') }}",
                    data: formData,
                    beforeSend: function(){
                    $('.fp__massage_btn').prop('disabled', true);
                    let chatMessage = escapeHtml(message);
                    let html =  `
                        <div class="fp__chating tf_chat_right">
                            <div class="fp__chating_img">
                                <img src="{{ auth()->user()->avatar_url }}" alt="person"
                                    class="img-fluid w-100" style="border-radius: 50%;">
                            </div>
                            <div class="fp__chating_text">
                                <p>${chatMessage}</p>
                                <span class="msg_sending ${msgId}">sending...</span>
                            </div>
                        </div>`

                        $('.fp__chat_body').append(html);
                        messageInput.val("");
                        scrollToBootom();
                    },
                    success: function(response){
                        if($('.msg_temp_id').val() == response.msgId){
                            $('.'+msgId).remove();
                        }
                    },
                    error: function(xhr, status, error){
                        $('.'+msgId).text('failed');
                        messageInput.val(message);

                        let errors = xhr.responseJSON?.errors;

                        if (errors) {
                            $.each(errors, function(key, value){
                                toastr.error(value);
                            })
                        } else {
                            toastr.error(xhr.responseJSON?.message || 'Message could not be sent.');
                        }
                    },
                    complete: function(){
                        $('.fp__massage_btn').prop('disabled', false);
                    }
                })
            })
        })
    </script>
@endpush
