<?php

namespace Abbarbosa\Gadgets\CarrinhoCompras\Contracts;

interface iCarrinhoProduto
{
    public function encontrePorSKU(string $sku): iCarrinhoProduto;

    public function getId(): int;

    public function getSKU(): string;

    public function getPreco(): float;

    public function getDescricao(): string;

    public function getImagem(): string;
}
