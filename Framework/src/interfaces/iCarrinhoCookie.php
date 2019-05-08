<?php

namespace Abbarbosa\Gadgets\CarrinhoCompras\Contracts;

use Abbarbosa\Gadgets\CarrinhoCompras\Contracts\iCarrinho;

interface iCarrinhoCookie
{
    public function setCarrinho(iCarrinho $carrinho): iCarrinhoCookie;

    public function setModelProdutos(iCarrinhoProduto $produto): iCarrinhoCookie;

    public function store(iCarrinho $carrinho): iCarrinhoCookie;

    public function restore(): iCarrinho;
}
