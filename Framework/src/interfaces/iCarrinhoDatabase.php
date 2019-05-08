<?php

namespace Abbarbosa\Gadgets\CarrinhoCompras\Contracts;

use Abbarbosa\Gadgets\CarrinhoCompras\Contracts\iCarrinho;

interface iCarrinhoDatabase
{
    public function setModels(iModel $carrinho, iModel $produto, iModel $itens): iCarrinhoDatabase;

    public function save(iCarrinho $carrinho): ?iCarrinho;

    public function load(int $id, iCarrinho $carrinho): ?iCarrinho;

    public function delete(int $id): bool;
}
