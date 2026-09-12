<style>
    #hp-chat-toggle{
        position: fixed;
        bottom: 24px;
        right: 24px;
        width: 58px;
        height: 58px;
        border-radius: 50%;
        background: #E8622A;
        color: #fff;
        border: none;
        font-size: 26px;
        cursor: pointer;
        box-shadow: 0 6px 20px rgba(232,98,42,0.45);
        z-index: 9999;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: transform 0.2s;
    }
    #hp-chat-toggle:hover{ transform: scale(1.08); }

    #hp-chat-window{
        position: fixed;
        bottom: 95px;
        right: 24px;
        width: 320px;
        max-width: 90vw;
        height: 420px;
        background: #fff;
        border-radius: 14px;
        box-shadow: 0 10px 40px rgba(0,0,0,0.25);
        display: none;
        flex-direction: column;
        overflow: hidden;
        z-index: 9999;
        font-family: Arial, sans-serif;
    }
    #hp-chat-header{
        background: #1B2340;
        color: #fff;
        padding: 14px 16px;
        font-weight: 600;
        font-size: 14px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    #hp-chat-close{
        cursor: pointer;
        font-size: 18px;
        background: none;
        border: none;
        color: #fff;
    }
    #hp-chat-messages{
        flex: 1;
        overflow-y: auto;
        padding: 14px;
        background: #f8f9fa;
    }
    .hp-msg{
        max-width: 85%;
        padding: 9px 13px;
        border-radius: 12px;
        margin-bottom: 10px;
        font-size: 13px;
        line-height: 1.5;
    }
    .hp-msg-bot{
        background: #eee;
        color: #333;
        border-bottom-left-radius: 3px;
    }
    .hp-msg-user{
        background: #E8622A;
        color: #fff;
        margin-left: auto;
        border-bottom-right-radius: 3px;
    }
    #hp-chat-input-area{
        display: flex;
        border-top: 1px solid #eee;
        padding: 8px;
    }
    #hp-chat-input{
        flex: 1;
        border: 1px solid #ddd;
        border-radius: 20px;
        padding: 8px 14px;
        font-size: 13px;
        outline: none;
    }
    #hp-chat-send{
        background: #E8622A;
        color: #fff;
        border: none;
        border-radius: 20px;
        padding: 8px 16px;
        margin-left: 6px;
        cursor: pointer;
        font-size: 13px;
    }
    .hp-typing{ color: #999; font-style: italic; font-size: 12px; }
</style>

<button id="hp-chat-toggle" onclick="hpToggleChat()">chat</button>

<div id="hp-chat-window">
    <div id="hp-chat-header">
        <span>Hira Property Assistant</span>
        <button id="hp-chat-close" onclick="hpToggleChat()">x</button>
    </div>
    <div id="hp-chat-messages">
        <div class="hp-msg hp-msg-bot">Hello! I'm the Hira Property assistant. Ask me anything about properties, bookings, or any other questions.</div>
    </div>
    <div id="hp-chat-input-area">
        <input type="text" id="hp-chat-input" placeholder="Type your question..." onkeypress="if(event.key==='Enter') hpSendMessage();">
        <button id="hp-chat-send" onclick="hpSendMessage()">Send</button>
    </div>
</div>

<script>
function hpToggleChat(){
    var win = document.getElementById('hp-chat-window');
    win.style.display = (win.style.display === 'flex') ? 'none' : 'flex';
}

function hpSendMessage(){
    var input = document.getElementById('hp-chat-input');
    var question = input.value.trim();
    if(question === '') return;

    var messages = document.getElementById('hp-chat-messages');

    var userMsg = document.createElement('div');
    userMsg.className = 'hp-msg hp-msg-user';
    userMsg.textContent = question;
    messages.appendChild(userMsg);

    input.value = '';
    messages.scrollTop = messages.scrollHeight;

    var typing = document.createElement('div');
    typing.className = 'hp-msg hp-msg-bot hp-typing';
    typing.id = 'hp-typing-indicator';
    typing.textContent = 'Typing...';
    messages.appendChild(typing);
    messages.scrollTop = messages.scrollHeight;

    fetch('chatbot.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ question: question })
    })
    .then(function(res){ return res.json(); })
    .then(function(data){
        var typingEl = document.getElementById('hp-typing-indicator');
        if(typingEl) typingEl.remove();

        var botMsg = document.createElement('div');
        botMsg.className = 'hp-msg hp-msg-bot';
        botMsg.textContent = data.reply;
        messages.appendChild(botMsg);
        messages.scrollTop = messages.scrollHeight;
    })
    .catch(function(err){
        var typingEl = document.getElementById('hp-typing-indicator');
        if(typingEl) typingEl.remove();

        var botMsg = document.createElement('div');
        botMsg.className = 'hp-msg hp-msg-bot';
        botMsg.textContent = 'Sorry, connection error. Please try again.';
        messages.appendChild(botMsg);
    });
}
</script>
