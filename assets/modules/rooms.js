import { openPasswordModal } from "./modals.js";
import { closePasswordModal } from "./modals.js";

import { renderMessages } from "./messages.js";
import { renderMessageVerifieds } from "./messages.js";
import { startMessageRendering } from "./messages.js";

export function RenderRoom(roomsContainer, user, title, formSendMessage) {
    fetch('https://pbarbosaprojetos.byethost31.com/api/get_all_rooms/')
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
                        openRoom(room.uid, user, title, formSendMessage);
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

export function EnterRoom(modalCreateRoom, roomsContainer, user, title, formSendMessage) {
    const roomNameInput = document.querySelector('#roomName');
    const roomPasswordInput = document.querySelector('#roomPassword');
    const error_message = document.querySelector('#error_message');
    const modal = bootstrap.Modal.getInstance(modalCreateRoom);

    let body = {
        room: roomNameInput.value,
    };

    const roomPrivate = document.querySelector('#roomPrivate');
    if (roomPrivate && roomPrivate.checked) {
        if (roomPasswordInput.value.trim() !== "") {
            body.password = roomPasswordInput.value;
        }
    }

    fetch('https://pbarbosaprojetos.byethost31.com/api/enter_room/', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        credentials: 'same-origin',
        body: JSON.stringify(body)
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === "error") throw new Error(data.error_message);

        modal.hide();
        RenderRoom(roomsContainer, user, title, formSendMessage);

        roomNameInput.value = '';
        roomPasswordInput.value = '';
        error_message.innerHTML = '';
        error_message.classList.add('d-none');
    })
    .catch(error => {
        error_message.classList.remove('d-none');
        error_message.innerHTML = error.message;
    });
}

function openRoom(room, user, title, formSendMessage) {
    fetch(`https://pbarbosaprojetos.byethost31.com/api/get_user/?user=${user}`)
    .then(res => res.json())
    .then(userData => {
        const myUserUid = userData.data[0];

        return fetch(`https://pbarbosaprojetos.byethost31.com/api/get_room/?room_uid=${room}`)
            .then(res => res.json())
            .then(roomData => {
                if (roomData.status === "error") throw new Error(roomData.error_message);

                if (roomData.data[0].password != null) {
                    openPasswordModal();

                    title.innerHTML = `Room - ${roomData.data[0].name}`;
                    title.classList.remove('d-none');

                    formSendMessage.classList.add('d-none');

                    chatC.innerHTML = `<p class="text-muted">Waiting for password..</p>`;

                    const form = document.getElementById('roomPasswordForm');

                    const newForm = form.cloneNode(true);
                    form.parentNode.replaceChild(newForm, form);


                    newForm.addEventListener('submit', function (e) {
                        e.preventDefault();

                        const password = document.getElementById('roomPasswordInput').value;

                        let body = {
                            room_uid: room,
                            password: password
                        }

                        fetch("https://pbarbosaprojetos.byethost31.com/api/private_room/", {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json'
                            },
                            credentials: 'same-origin',
                            body: JSON.stringify(body)
                        })
                            .then(response => response.json())
                            .then(data => {
                                if (data.status === "error") throw new Error(data.error_message);

                                closePasswordModal();
                                renderMessageVerifieds(roomData, myUserUid, title, formSendMessage);
                                startMessageRendering(myUserUid, room);
                            })
                            .catch(err => {
                                alert(err.message)
                            })
                    });
                } else {
                    renderMessageVerifieds(roomData, myUserUid, title, formSendMessage);
                    startMessageRendering(myUserUid, room);
                }
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