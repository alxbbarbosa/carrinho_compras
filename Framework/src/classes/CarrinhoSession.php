<?php

namespace Abbarbosa\Gadgets\CarrinhoCompras\Classes;

use Abbarbosa\Gadgets\CarrinhoCompras\Contracts\iCarrinho;
use Abbarbosa\Gadgets\CarrinhoCompras\Contracts\iCarrinhoSession;

class CarrinhoSession implements iCarrinhoSession
{
    protected $carrinho;
    protected $produtos;

    public function setCarrinho(iCarrinho $carrinho): iCarrinhoSession
    {
        $this->carrinho = $carrinho;
        return $this;
    }

    public function setModelProdutos(iCarrinhoProduto $produto): iCarrinhoSession
    {
        $this->produtos = $produtos;
        return $this;
    }

    public function store(iCarrinho $carrinho)
    {
        $_SESSION['carrinho'] = serialize($this->carrinho);
        return $this;
    }

    public function restore(): iCarrinho
    {
        $carrinho =  unserialize($_SESSION['carrinho']);
        
        return $carrinho;
    }
}
