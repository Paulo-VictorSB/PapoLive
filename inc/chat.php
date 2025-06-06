<?php

if (!isset($_SESSION['username'])) {
    header("Location: ?route=index");
}

?>

<div class="container mt-5">
    <div class="row">
        <div class="col-md-4">
            <h4>New Room</h4>
            <div class="row mb-3">
                <div class="col-9"><button class="btn btn-outline-primary w-100" data-bs-toggle="modal" data-bs-target="#modalCreateRoom">Create</button></div>
                <div class="col-2"><button class="btn btn-outline-primary">Exit</button></div>
            </div>
            <h4>Rooms</h4>
            <div class="list-group shadow" style="border: 1px solid black; max-height: 400px; overflow-y: auto;" id="rooms">
            </div>
        </div>

        <div class="col-md-8 d-flex flex-column p-3 shadow" style="border: 1px solid black">
            <h4>Room - [Name]</h4>

            <div class="border rounded p-3 mb-3 flex-grow-1 overflow-auto" style="height: 400px;">
                <div class="d-flex mb-2">
                    <div class="bg-light rounded px-3 py-2">
                        <small class="text-muted">Ana Carla ~20:15h</small>
                        <p class="mb-0">Olá, boa noite!</p>
                    </div>
                </div>

                <div class="d-flex justify-content-end mb-2">
                    <div class="bg-primary text-white rounded px-3 py-2">
                        <small>Paulo Victor ~20:20h</small>
                        <p class="mb-0">Boa noite!</p>
                    </div>
                </div>
            </div>

            <form>
                <div class="input-group">
                    <input type="text" class="form-control" placeholder="Enter your message..." aria-label="Enter your message">
                    <button class="btn btn-primary" type="submit">Submit</button>
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
    setInterval(RenderRoom, 36000);

    let password_field = document.querySelector('#password_field');
    let roomPrivate = document.querySelector('#roomPrivate');

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

    // # enviar uma mensagem

    // # carregar de 2 em 2 segundos todas as salas
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
                    a.classList.add('list-group-item', 'list-group-item-action');
                    a.innerText = room.name;

                    rooms.append(a)
                });

            })
    }
</script>