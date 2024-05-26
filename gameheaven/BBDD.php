<?php
    $conex1 = new mysqli("localhost","andreassdmin","12345","tfg_gameheaven");
    if(mysqli_connect_errno()){
        echo "Conexion fallida %s\n",mysqli_connect_errno();
        exit();
    }