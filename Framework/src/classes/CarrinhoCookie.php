<?php

namespace Abbarbosa\Gadgets\CarrinhoCompras\Classes;

use Abbarbosa\Gadgets\CarrinhoCompras\Contracts\iCarrinho;
use Abbarbosa\Gadgets\CarrinhoCompras\Contracts\iCarrinhoCookie;

class CarrinhoCookie implements iCarrinhoCookie
{
    protected $carrinho;
    protected $produtos;

    public function setCarrinho(iCarrinho $carrinho): iCarrinhoCookie
    {
        $this->carrinho = $carrinho;
        return $this;
    }

    public function setModelProdutos(iCarrinhoProduto $produto): iCarrinhoCookie
    {
        $this->produtos = $produtos;
        return $this;
    }

    public function store(iCarrinho $carrinho): iCarrinhoCookie
    {
        $data = array_map(function ($item) {
            return ['sku' => $item->produto->getSKU(), 'quantidade' => $item->quantidade];
        }, $carrinho->getItens());

        $_COOKIES['carrinho'] = serialize($data);
        return $this;
    }

    public function restore(): iCarrinho
    {
        $carrinho = clone $this->carrinho;

        foreach (unserialize($_COOKIES['carrinho']) as $key => $item) {
            $carrinho->adicionar($this->produtos->encontrePorSKU($_COOKIES['sku']), $item['quantidade']);
        }

        return $carrinho;
    }
}
