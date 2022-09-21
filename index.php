<?php
/* Function Always First*/
require 'src/functions.php';

/* Middleware always Second */
require 'src/middlewares/global.php';

/*Router Always Last*/
require 'src/router.php';
?>