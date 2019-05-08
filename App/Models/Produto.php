<?php

namespace App\Models;

use \Abbarbosa\Gadgets\CarrinhoCompras\Classes\Model;
use \Abbarbosa\Gadgets\CarrinhoCompras\Contracts\iCarrinhoProduto;

class Produto extends Model implements iCarrinhoProduto
{
    protected $table = 'produto';

    public function encontrePorSKU(string $sku): iCarrinhoProduto
    {
        return $this->findFirst(['sku', $sku]);
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getSKU(): string
    {
        return $this->sku;
    }

    public function getPreco(): float
    {
        return $this->preco;
    }

    public function getDescricao(): string
    {
        return $this->descricao;
    }

    public function getImagem(): string
    {
        return $this->imagem;
    }
}
