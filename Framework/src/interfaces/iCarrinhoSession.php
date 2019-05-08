<?php

namespace Abbarbosa\Gadgets\CarrinhoCompras\Contracts;

use Abbarbosa\Gadgets\CarrinhoCompras\Contracts\iCarrinho;

interface iCarrinhoSession
{
    public function setCarrinho(iCarrinho $carrinho): iCarrinhoSession;

    public function setModelProdutos(iCarrinhoProduto $produto): iCarrinhoSession;

    public function store(iCarrinho $carrinho): iCarrinhoSession;

    public function restore(): iCarrinho;
}
