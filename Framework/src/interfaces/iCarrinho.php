<?php

namespace Abbarbosa\Gadgets\CarrinhoCompras\Contracts;

use Abbarbosa\Gadgets\CarrinhoCompras\Contracts\iCarrinhoProduto;

interface iCarrinho
{
    public function getId(): ?int;

    public function getClienteId(): ?int;

    public function getDataCompra(): ?string;

    public function getFrete(): ?float;

    public function getDescontoValor(): ?float;

    public function getDescontoPorcento(): ?float;

    public function getTaxaDeJuros(): ?float;

    public function adicionar(iCarrinhoProduto $produto, int $quantidade): iCarrinho;

    public function existeNoCarrinho(iCarrinhoProduto $produto): int;

    public function remover(int $item): iCarrinho;

    public function getQuantidadeItens(): int;

    public function getItens(): array;

    public function getTotal(): float;

    public function getValorParcela(): float;

    public function getNumeroParcelas(): int;

    public function getTotalParcelado(): float;

    public function setFrete(float $valor): iCarrinho;

    public function setDescontoPorcentagem(float $valor): iCarrinho;

    public function setDescontoValor(float $valor): iCarrinho;

    public function setTaxaDeJuros(float $juros): iCarrinho;

    public function setParcelas(int $numero): iCarrinho;

    public function setId(int $id): iCarrinho;
}
