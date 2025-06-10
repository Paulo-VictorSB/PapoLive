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
                        Send
                        <i class="bi bi-box-arrow-in-right"></i>
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

<div class="modal fade" id="roomPasswordModal" tabindex="-1" aria-labelledby="roomPasswordModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content rounded-3 shadow">
            <div class="modal-header">
                <h5 class="modal-title" id="roomPasswordModalLabel">Enter Room Password</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="roomPasswordForm">
                    <div class="mb-3">
                        <label for="roomPasswordInput" class="form-label">Password</label>
                        <input type="password" class="form-control" id="roomPasswordInput" placeholder="Enter password" required>
                    </div>
                    <div class="text-end">
                        <button type="submit" class="btn btn-primary">Join Room</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    const sessionStorage = window.sessionStorage;
    sessionStorage.setItem('user', "<?= $_SESSION['username'] ?>");

    let password_field = document.querySelector('#password_field');
    let roomPrivate = document.querySelector('#roomPrivate');
    let container_chat = document.querySelector('#container-chat');
    let chatC = document.getElementById('chat');
    let currentMessageInterval = null;
    let send_message_frm = document.querySelector('#send_message');
    let autoScroll = true;
    let roomPasswordMessage = document.querySelector("#roomPasswordMessage");

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
    
</script>