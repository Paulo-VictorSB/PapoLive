import { setupLogin } from "./modules/login.js";

// Login
const formLogin = document.querySelector('#formLogin');
const username = document.querySelector('#username');
const error_message = document.querySelector('#error_message');

if (formLogin) setupLogin(formLogin, username, error_message);