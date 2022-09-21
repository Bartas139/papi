<?php

switch (requestUri()['uri']) {
    case '':
    case '/api/user':
        require 'controllers/users.php';
        break;

    default:
        response(404, 'AHOJ');
        break;
}
?>