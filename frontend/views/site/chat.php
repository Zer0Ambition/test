<?php

/* @var $this yii\web\View */
$this->registerCssFile('/css/chat.css');
$this->title = 'My Websocket Chat Application';
?>
<script src="https://code.jquery.com/jquery-2.2.4.min.js"></script>
<script>
    $(function() {
        var messageNode;
        var userId = <?=$current_user->id?>;
        var chat = new WebSocket(`ws://${window.location.hostname}:8060/`);
        chat.onmessage = function(e) {
            $('#response').text('');

            var response = JSON.parse(e.data);
            if (response.type && response.type == 'chat') {
                messageNode = '';
                if (response.user_id == userId) {
                    messageNode += '<div class="form-group pull-right pb-chat-labels-right">';
                    messageNode += '<span class="label label-primary pb-chat-labels pb-chat-labels-primary">' + response.message + '</span><span class="fa fa-lg fa-user pb-chat-fa-user"> </span>';
                    messageNode += '<span class="time_date"> ' + response.date + '   |   ' + response.from + '</span>'
                    messageNode += '</div><div class="clearfix"></div><hr>';
                }
                else {
                    messageNode += '<div class="form-group">';
                    messageNode += '<span class="fa fa-lg fa-user pb-chat-fa-user"></span><span class="label label-default pb-chat-labels pb-chat-labels-left">' + response.message + '</span>';
                    messageNode += '<span class="time_date"> '+ response.date +'    |   ' + response.from + '</span>'
                    messageNode += '</div><div class="clearfix"></div><hr>';
                }
                $('#chat').append(messageNode);
                scrollBottom($('#chat'), 200);
            } else if (response.message) {
                $('#response').text(response.message);
            }
        };
        chat.onopen = function(e) {
            $('#response').text("Connection established!");
            chat.send( JSON.stringify({'action' : 'connect', 'user_id' : userId}) );
        };
        chat.onerror = function(e) {
            alert('Problem connecting to the server!');
        }
        
        $('#btnSend').click(function() {
            if ($('#message').val()) {
                chat.send( JSON.stringify({'action' : 'chat', 'message' : $('#message').val(), 'user_id' : userId}) );
                $('#message').val('');
            } else {
                alert('Enter the message');
            }
        });

        $('#message').on('keypress', function(event) {
        if ((event.ctrlKey) && ((event.keyCode == 0xA) || event.keyCode == 0xD)) {
            $('#btnSend').click();
        }
        });

        function scrollBottom(el, duration = 500) {
        var top = el.prop("scrollHeight");
        el.animate({
            scrollTop: top
        }, duration);
}

    })
</script>
<div class="site-index">

    <div class="body-content">
        <div  class="col-md-8 col-xl-6 chat">    
            <div id="chat">
                <div id="response" class="to-bottom"></div>
            </div>
            <div class="input group">
                <textarea name="" id="message" class="form-control type_msg" placeholder="Type your message..."></textarea>
                <span class="input-group-btn">
                    <button class="btn btn-primary btn-sm-2 pull-right" style="margin-top: 10px" id="btnSend">Send</button>
                </span>
            </div>
        </div>
    </div>
</div>