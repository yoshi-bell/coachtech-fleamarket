// スクロールを一番下に
document.addEventListener('DOMContentLoaded', function() {
    const chatMessages = document.getElementById('chat-messages');
    if (chatMessages) {
        chatMessages.scrollTop = chatMessages.scrollHeight;
    }
});

window.openRatingModal = function() {
    document.getElementById('rating-modal').style.display = 'flex';
};

// FN009: 入力情報保持機能
document.addEventListener('DOMContentLoaded', function() {
    const chatMessageInput = document.getElementById('chat-message-input');
    // chatMessageInput が存在する場合のみ処理を実行
    if (chatMessageInput) {
        // soldItemIdとuserIdをHTMLのdata属性から取得
        const chatContainer = document.getElementById('chat-container');
        const soldItemId = chatContainer ? chatContainer.dataset.soldItemId : null;
        const userId = chatContainer ? chatContainer.dataset.userId : null;

        if (soldItemId && userId) {
            const chatForm = chatMessageInput.closest('form');
            const localStorageKey = `chat_message_for_${soldItemId}_by_user_${userId}`;

            // ページロード時にlocalStorageからメッセージを復元
            const savedMessage = localStorage.getItem(localStorageKey);
            if (savedMessage) {
                chatMessageInput.value = savedMessage;
            }

            // 入力欄の変更をlocalStorageに保存
            chatMessageInput.addEventListener('input', function() {
                localStorage.setItem(localStorageKey, chatMessageInput.value);
            });

            // メッセージ送信時にlocalStorageをクリア
            chatForm.addEventListener('submit', function() {
                localStorage.removeItem(localStorageKey);
            });
        }
    }
});


// FN010: メッセージ編集機能のUI切り替え
document.addEventListener('DOMContentLoaded', function() {
    // 編集ボタンの処理
    document.querySelectorAll('.chat-message__edit-button').forEach(button => {
        button.addEventListener('click', function() {
            const content = this.closest('.chat-message__content');
            const bubble = content.querySelector('.chat-message__bubble');
            const editForm = content.querySelector('.chat-message__edit-form');
            const actions = content.querySelector('.chat-message__actions');

            bubble.style.display = 'none';
            actions.querySelector('.chat-message__delete-form').style.display = 'none';
            this.style.display = 'none';
            editForm.style.display = 'block';
        });
    });

    // キャンセルボタンの処理
    document.querySelectorAll('.chat-message__cancel-button').forEach(button => {
        button.addEventListener('click', function() {
            const content = this.closest('.chat-message__content');
            const bubble = content.querySelector('.chat-message__bubble');
            const editForm = content.querySelector('.chat-message__edit-form');
            const actions = content.querySelector('.chat-message__actions');

            bubble.style.display = ''; // or 'flex' if it was
            actions.querySelector('.chat-message__delete-form').style.display = '';
            actions.querySelector('.chat-message__edit-button').style.display = '';
            editForm.style.display = 'none';
        });
    });
});