import { closePasswordModal } from "./modules/modals.js";
import { closeRoomModal } from "./modules/modals.js";

import { RenderRoom } from "./modules/rooms.js";
import { EnterRoom } from "./modules/rooms.js";

// Modals
const roomPasswordModal = document.querySelector("#roomPasswordModal");
const modalCreateRoom = document.querySelector("#modalCreateRoom");

roomPasswordModal.addEventListener('hidden.bs.modal', () => {
    closePasswordModal();
});
modalCreateRoom.addEventListener('hidden.bs.modal', () => {
    closeRoomModal();
});

// Rooms
const roomsContainer = document.querySelector('#rooms');
const title = document.querySelector('#room_title');
const formSendMessage = document.querySelector('#send_message');
const user = sessionStorage.getItem("user");
const enter_room = document.querySelector('#enter_room');

RenderRoom(roomsContainer, user, title, formSendMessage);
setInterval(() => { RenderRoom(roomsContainer, user, title, formSendMessage) }, 30000);

enter_room.addEventListener('submit', (form) => {
    form.preventDefault();
    EnterRoom(modalCreateRoom, roomsContainer, user, title, formSendMessage);
})