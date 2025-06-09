import { setupLogin } from "./modules/login.js";

// Login
const formLogin = document.querySelector('#formLogin');
let username = document.querySelector('#username');
let error_message = document.querySelector('#error_message');

if (formLogin) setupLogin(formLogin, username, error_message);