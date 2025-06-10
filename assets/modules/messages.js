export function renderMessageVerifieds(room, myUserUid, title, formSendMessage) {
    title.innerHTML = `Room - ${room.data[0].name}`;
    title.classList.remove('d-none');
    formSendMessage.classList.remove('d-none');

    const newHandler = (e) => {
        e.preventDefault();

        let message_input = document.querySelector('#message_input');

        let body = {
            user_uid: myUserUid.uid,
            room_uid: room.data[0].uid,
            content: message_input.value
        };

        fetch('http://localhost/PapoLive/api/enter_message/', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            credentials: 'same-origin',
            body: JSON.stringify(body)
        })
            .then(response => response.json())
            .then(data => {
                if (data.status === "error") {
                    throw new Error(data.error_message);
                }

                message_input.value = '';
                message_input.innerText = '';
            })
            .catch(error => {
                console.error(error.message);
            });
    };

    let newForm = send_message_frm.cloneNode(true);
    send_message_frm.parentNode.replaceChild(newForm, send_message_frm);
    send_message_frm = newForm;
    send_message_frm.addEventListener('submit', newHandler);
}

export function startMessageRendering(myUserUid, room) {
    const currentRoom = document.querySelector('.active')?.id;

    if (currentMessageInterval !== null) {
        clearInterval(currentMessageInterval);
    }

    renderMessages(myUserUid, room, chatC);

    currentMessageInterval = setInterval(() => {
        const activeRoom = document.querySelector('.active')?.id;

        if (activeRoom != room) {
            clearInterval(currentMessageInterval);
            currentMessageInterval = null;
            return;
        }

        renderMessages(myUserUid, room, chatC);
    }, 500);
}

export function renderMessages(user, room, chat) {
    return fetch(`http://localhost/PapoLive/api/get_all_messages_from_room/?room_uid=${room}`)
        .then(res => res.json())
        .then(messageData => {

            if (chat) {
                chat.innerHTML = '';
            }

            if (messageData.status === "error") throw new Error(messageData.error_message);

            const messages = messageData.data;

            if (!messages || messages.length === 0) {
                const noMessages = document.createElement('p');
                noMessages.id = 'noMessages';
                noMessages.innerText = "No messages found in this room, be the first to send one!";
                chat.appendChild(noMessages);
                return;
            }

            messages.forEach(msg => {
                const isMine = msg.user_uid === user.uid;

                const msgContainer = document.createElement('div');
                msgContainer.classList.add('d-flex', 'mb-2');
                if (isMine) msgContainer.classList.add('justify-content-end');

                const msgBox = document.createElement('div');
                msgBox.classList.add('rounded', 'px-3', 'py-2');
                if (isMine) {
                    msgBox.classList.add('bg-primary', 'text-white');
                } else {
                    msgBox.classList.add('bg-light');
                }

                const small = document.createElement('small');
                if (!isMine) {
                    small.classList.add('text-muted');
                }
                const time = new Date(msg.created_at).toLocaleTimeString([], {
                    hour: '2-digit',
                    minute: '2-digit'
                });
                small.innerText = `${msg.user} ~ ${time}`;

                const p = document.createElement('p');
                p.classList.add('mb-0');
                p.innerText = msg.content;

                msgBox.appendChild(small);
                msgBox.appendChild(p);
                msgContainer.appendChild(msgBox);
                chat.appendChild(msgContainer);
            });
        });
}