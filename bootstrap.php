<?php

require __DIR__ . '/vendor/autoload.php';

use \Abbarbosa\Gadgets\CarrinhoCompras\Classes\Connection as Connection;
use \App\Models\Produto as Produto;

try {
    session_start();

    Connection::setCredentials('postgres', 'P@ssw0rd');
    Produto::setConnection(Connection::getInstance('carrinho_compras'));
} catch (\Exception $e) {
    echo $e->getMessage();
}
