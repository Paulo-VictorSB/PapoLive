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
                <div class="col text-end"><button class="btn btn-danger">Exit</button></div>
            </div>
            <h4>Rooms</h4>
            <div class="list-group shadow" style="border: 1px solid black; max-height: 400px; overflow-y: auto;" id="rooms">
            </div>
        </div>

        <div class="col-md-8 d-flex flex-column p-3 shadow" id="container-chat" style="border: 1px solid black"></div>
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
    setInterval(RenderRoom, 300000);

    let password_field = document.querySelector('#password_field');
    let roomPrivate = document.querySelector('#roomPrivate');
    let container_chat = document.querySelector('#container-chat');

    roomPrivate.addEventListener('change', () => {
        password_field.classList.toggle('d-none', !roomPrivate.checked);
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
        let rooms = document.querySelector('#rooms');
        rooms.innerHTML = ''
        fetch('http://localhost/PapoLive/api/get_all_rooms/')
            .then(response => response.json())
            .then(data => {

                data.data.forEach(room => {
                    let a = document.createElement('a');
                    a.setAttribute('href', '#');
                    a.setAttribute('id', room.uid);
                    a.addEventListener('click', () => openRoom(room.uid));
                    a.classList.add('list-group-item', 'list-group-item-action');
                    a.innerText = room.name;

                    rooms.append(a)
                });

            })
    }

    function openRoom(room) {
        container_chat.innerHTML = '';

        const existingTitle = document.querySelector('#roomChatName');
        if (existingTitle) existingTitle.remove();

        const user = "<?= $_SESSION['username']; ?>";

        fetch(`http://localhost/PapoLive/api/get_user/?user=${user}`)
            .then(res => res.json())
            .then(userData => {
                const myUserUid = userData.data[0];

                return fetch(`http://localhost/PapoLive/api/get_room/?room_uid=${room}`)
                    .then(res => res.json())
                    .then(roomData => {
                        if (roomData.status === "error") throw new Error(roomData.error_message);

                        const roomChatName = document.createElement('h4');
                        roomChatName.id = "roomChatName";
                        roomChatName.innerText = `Room - ${roomData.data[0].name}`;
                        container_chat.appendChild(roomChatName);

                        return myUserUid;
                    });
            })
            .then(myUserUid => {
                return fetch(`http://localhost/PapoLive/api/get_all_messages_from_room/?room_uid=${room}`)
                    .then(res => res.json())
                    .then(messageData => {
                        if (messageData.status === "error") throw new Error(messageData.error_message);

                        const messages = messageData.data;

                        if (!messages || messages.length === 0) {
                            const noMessages = document.createElement('p');
                            noMessages.id = 'noMessages';
                            noMessages.innerText = "Nenhuma mensagem encontrada nesta sala.";
                            container_chat.appendChild(noMessages);
                            return;
                        }

                        const chat = document.createElement('div');
                        chat.classList.add('border', 'rounded', 'p-3', 'mb-3', 'flex-grow-1', 'overflow-auto');
                        chat.style.height = "400px";
                        container_chat.appendChild(chat);

                        messages.forEach(msg => {
                            const isMine = msg.user_uid === myUserUid.uid;
                            console.log(isMine)

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

                        const form = document.createElement('form');
                        const inputGroup = document.createElement('div');
                        inputGroup.classList.add('input-group');

                        const input = document.createElement('input');
                        input.type = "text";
                        input.classList.add('form-control');
                        input.placeholder = "Enter your message...";
                        input.setAttribute('aria-label', 'Enter your message');

                        const button = document.createElement('button');
                        button.type = "submit";
                        button.classList.add('btn', 'btn-primary', 'ms-3');
                        button.innerText = "Submit";

                        inputGroup.appendChild(input);
                        inputGroup.appendChild(button);
                        form.appendChild(inputGroup);
                        container_chat.appendChild(form);
                    });
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
</script>