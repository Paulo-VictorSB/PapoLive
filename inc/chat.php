<?php

if (!isset($_SESSION['username'])) {
    header("Location: ?route=index");
}

?>

<div class="container mt-5">
    <div class="row">
        <div class="col-md-4 mb-3">
            <h4>New Room</h4>
            <div class="row mb-3">
                <div class="col"><button class="btn btn-primary w-100" data-bs-toggle="modal" data-bs-target="#modalCreateRoom">Create</button></div>
                <div class="col text-end"><a href="?route=logout" class="btn btn-danger">Exit</a></div>
            </div>
            <h4>Rooms</h4>
            <div class="list-group shadow" style="border: 1px solid black; max-height: 400px; overflow-y: auto;" id="rooms">
            </div>
        </div>

        <div class="col-md-8 d-flex flex-column p-3 shadow" id="container-chat" style="border: 1px solid black">
            <!-- title -->
            <h4 id="room_title" class="d-none"></h4>
            <!-- chat -->
            <div class="border rounded p-3 mb-3 flex-grow-1 overflow-auto" id="chat" style="height: 400px;">
            </div>
            <!-- form -->
            <form class="d-none" id="send_message">
                <div class="input-group">
                    <input
                        type="text"
                        class="form-control"
                        placeholder="Enter your message..."
                        aria-label="Enter your message"
                        id="message_input">
                    <button
                        type="submit"
                        class="btn btn-primary ms-3">
                        Submit
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="modalCreateRoom" tabindex="-1" aria-labelledby="modalCreateRoomLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title" id="modalCreateRoomLabel">New Room</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form id="enter_room">
                <div class="modal-body">

                    <div class="mb-3">
                        <label for="roomName" class="form-label">Name</label>
                        <input type="text" class="form-control" id="roomName" placeholder="Enter the name of the room" required>
                    </div>

                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" value="" id="roomPrivate">
                        <label class="form-check-label" for="roomPrivate">
                            Private
                        </label>
                    </div>

                    <span class="text-danger mb-3 d-none" id="error_message"></span>

                    <div class="mb-3 d-none" id="password_field">
                        <label for="roomPassword" class="form-label">Senha</label>
                        <input type="password" class="form-control" id="roomPassword" placeholder="Senha da sala">
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Create </button>
                </div>

            </form>

        </div>
    </div>
</div>

<script>
    // Salas
    RenderRoom();
    setInterval(RenderRoom, 1000);

    let password_field = document.querySelector('#password_field');
    let roomPrivate = document.querySelector('#roomPrivate');
    let container_chat = document.querySelector('#container-chat');
    let chatC = document.getElementById('chat');
    let currentMessageInterval = null;
    let send_message_frm = document.querySelector('#send_message');
    let autoScroll = true;

    roomPrivate.addEventListener('change', () => {
        password_field.classList.toggle('d-none', !roomPrivate.checked);
    });

    const observer = new MutationObserver(() => {
        if (autoScroll) {
            chatC.scrollTop = chatC.scrollHeight;
        }
    });

    observer.observe(chatC, {
        childList: true,
        subtree: true
    });

    chatC.addEventListener('mouseenter', () => {
        autoScroll = false;
    });

    chatC.addEventListener('mouseleave', () => {
        autoScroll = true;
        chatC.scrollTop = chatC.scrollHeight;
    });

    let enter_room = document.querySelector('#enter_room');
    enter_room.addEventListener('submit', (e) => {
        e.preventDefault();
        let roomName = document.querySelector('#roomName').value;
        let error_message = document.querySelector('#error_message');
        var modalElement = document.getElementById('modalCreateRoom');
        var modal = bootstrap.Modal.getInstance(modalElement);

        let body = {
            room: roomName,
        };

        if (roomPrivate.checked) {
            let roomPassword = document.querySelector('#roomPassword').value;
            if (roomPassword.trim() !== "") {
                body.password = roomPassword;
            }
        }

        fetch('http://localhost/PapoLive/api/enter_room/', {
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

                modal.hide();
                RenderRoom();

                roomName.innerHTML = ''
                error_message.innerHTML = ''
                error_message.classList.toggle('d-none')
                roomPassword.innerHTML = ''
            })
            .catch(error => {
                error_message.classList.remove('d-none');
                error_message.innerHTML = error.message;
            })
    })

    function RenderRoom() {
        const roomsContainer = document.querySelector('#rooms');

        fetch('http://localhost/PapoLive/api/get_all_rooms/')
            .then(response => response.json())
            .then(data => {
                const fetchedRooms = data.data;

                const existingRoomElements = Array.from(roomsContainer.querySelectorAll('.rooms'));
                const existingRoomIds = existingRoomElements.map(el => el.id);

                const fetchedRoomIds = fetchedRooms.map(room => room.uid);

                fetchedRooms.forEach(room => {
                    if (!existingRoomIds.includes(room.uid)) {
                        const a = document.createElement('a');
                        a.setAttribute('href', '#');
                        a.setAttribute('id', room.uid);
                        a.classList.add('rooms', 'list-group-item', 'list-group-item-action');
                        a.innerText = room.name;

                        a.addEventListener('click', (e) => {
                            document.querySelectorAll('.rooms').forEach(r => r.classList.remove('active'));
                            e.target.classList.add('active');
                            openRoom(room.uid);
                        });

                        roomsContainer.appendChild(a);
                    }
                });

                existingRoomElements.forEach(el => {
                    if (!fetchedRoomIds.includes(el.id)) {
                        el.remove();
                    }
                });
            });
    }

    function openRoom(room) {
        chatC.innerHTML = '';

        const title = document.querySelector('#room_title');
        const user = "<?= $_SESSION['username']; ?>";
        const formSendMessage = document.querySelector('#send_message');

        fetch(`http://localhost/PapoLive/api/get_user/?user=${user}`)
            .then(res => res.json())
            .then(userData => {
                const myUserUid = userData.data[0];

                return fetch(`http://localhost/PapoLive/api/get_room/?room_uid=${room}`)
                    .then(res => res.json())
                    .then(roomData => {
                        if (roomData.status === "error") throw new Error(roomData.error_message);

                        title.innerHTML = `Room - ${roomData.data[0].name}`;
                        title.classList.remove('d-none');
                        formSendMessage.classList.remove('d-none');

                        const newHandler = (e) => {
                            e.preventDefault();

                            let message_input = document.querySelector('#message_input');

                            let body = {
                                user_uid: myUserUid.uid,
                                room_uid: room,
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

                        return myUserUid;
                    });
            })
            .then(myUserUid => {
                const currentRoom = document.querySelector('.active')?.id;

                if (currentMessageInterval !== null) {
                    clearInterval(currentMessageInterval);
                }

                renderMessages(myUserUid, room, chatC);

                currentMessageInterval = setInterval(() => {
                    const activeRoom = document.querySelector('.active')?.id;

                    if (activeRoom == !room) {
                        clearInterval(currentMessageInterval);
                        currentMessageInterval = null;
                    }

                    renderMessages(myUserUid, room, chatC);
                }, 500);
            })
            .catch(err => {
                const noMessages = document.querySelector('#noMessages');
                if (noMessages) {
                    noMessages.innerText = err.message;
                } else {
                    console.error(err);
                }
            });
    }

    // Messagens
    function renderMessages(user, room, chat) {
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
                    noMessages.innerText = "Nenhuma mensagem encontrada nesta sala.";
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
</script>