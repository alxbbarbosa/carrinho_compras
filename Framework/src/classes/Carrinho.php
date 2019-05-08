<?php

namespace Abbarbosa\Gadgets\CarrinhoCompras\Classes;

use Abbarbosa\Gadgets\CarrinhoCompras\Contracts\iCarrinho;
use Abbarbosa\Gadgets\CarrinhoCompras\Contracts\iCarrinhoProduto;
use Abbarbosa\Gadgets\CarrinhoCompras\Contracts\iCarrinhoDatabase;
use Abbarbosa\Gadgets\CarrinhoCompras\Contracts\iModel;

class Carrinho implements iCarrinho
{
    protected $id;
    protected $itens              = [];
    protected $data_compra;
    protected $cliente_id;
    protected $juros              = 0.0;
    protected $parcelas           = 0;
    protected $total              = 0.0;
    protected $total_com_parcelas = 0.0;
    protected $valor_parcela      = 0.0;
    protected $frete              = 0.0;
    protected $desconto_valor     = 0.0;
    protected $desconto_porcento  = 0.0;

    public function __construct(int $id = null, int $cliente_id = 0,
                                $data_compra = null)
    {
        $this->id         = $id;
        $this->cliente_id = $cliente_id;

        if (preg_match("/^([19|20][0-9]{2})\-([0][1-9]|1[12])\-(0[1-9]|[12][0-9]|3[01]) ([01][0-9]|2[0123])\:([0-5][0-9])\:([0-5][0-9])$/",
                $data_compra)) {
            $this->data_compra = $data_compra;
        } else {
            $this->data_compra = date('Y-m-d H:i:s');
        }
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDataCompra(): ?string
    {
        return $this->data_compra;
    }

    public function getClienteId(): ?int
    {
        return $this->cliente_id;
    }

    public function getFrete(): ?float
    {
        return $this->frete;
    }

    public function getDescontoValor(): ?float
    {
        return $this->desconto_valor;
    }

    public function getDescontoPorcento(): ?float
    {
        return $this->desconto_porcento;
    }

    public function getTaxaDeJuros(): ?float
    {
        return $this->juros;
    }

    public function adicionar(iCarrinhoProduto $produto, int $quantidade): iCarrinho
    {
        $proximo = count($this->itens) + 1;
        $posicao = $this->existeNoCarrinho($produto);
        if ($posicao) {
            $this->itens[$posicao]->quantidade += $quantidade;
        } else {
            $this->itens[$proximo] = (object) ['produto' => $produto, 'quantidade' => $quantidade];
        }
        $this->recalcular();
        return $this;
    }

    public function existeNoCarrinho(iCarrinhoProduto $produto): int
    {
        foreach ($this->itens as $key => $item) {
            if ($item->produto->getId() == $produto->getId()) {
                return $key;
            }
        }
        return 0;
    }

    public function remover(int $item): iCarrinho
    {
        if (isset($this->itens[$item])) {
            unset($this->itens[$item]);
        }
        $this->recalcular();
        return $this;
    }

    public function getQuantidadeitens(): int
    {
        return count($this->itens);
    }

    public function getitens(): array
    {
        return $this->itens;
    }

    public function getTotal(): float
    {
        return $this->total;
    }

    public function getValorParcela(): float
    {
        return $this->valor_parcela;
    }

    public function getNumeroParcelas(): int
    {
        return $this->parcelas;
    }

    public function getTotalParcelado(): float
    {
        return $this->total_com_parcelas;
    }

    public function setId(int $id): iCarrinho
    {
        $this->id = $id;
        return $this;
    }

    public function setFrete(float $valor): iCarrinho
    {
        $this->frete = $valor;
        $this->recalcular();
        return $this;
    }

    public function setTaxaDeJuros(float $juros): iCarrinho
    {
        $this->juros = $juros;
        $this->recalcular();
        return $this;
    }

    public function setParcelas(int $numero): iCarrinho
    {
        $this->parcelas = $numero;
        $this->recalcular();
        return $this;
    }

    public function setDescontoPorcentagem(float $valor): iCarrinho
    {
        $this->desconto_porcento = $valor;
        $this->recalcular();
        return $this;
    }

    public function setDescontoValor(float $valor): iCarrinho
    {
        $this->desconto_valor = $valor;
        $this->recalcular();
        return $this;
    }

    protected function calcularParcelas()
    {
        if ($this->parcelas > 0) {
            if ($this->juros == 0.0) {
                $this->valor_parcela = (float) $this->total / $this->parcelas;
            } else {
                $I                   = $this->juros / 100.00; // Taxa
                $this->valor_parcela = $this->total * $I * pow((1 + $I),
                        $this->parcelas) / (pow((1 + $I), $this->parcelas) - 1);
            }
            $this->total_com_parcelas = ($this->valor_parcela * $this->parcelas);
        }
    }

    protected function calculaDesconto(float $valor): float
    {
        return (float) ($this->desconto_porcento * $valor) / 100;
    }

    protected function recalcular(): bool
    {
        $total = 0.0;
        foreach ($this->itens as $key => $item) {
            $total += $item->produto->getPreco() * $item->quantidade;
        }
        if ($this->desconto_valor > 0.0) {
            $total -= $this->desconto_valor;
        }
        if ($this->desconto_porcento > 0.0) {
            $total -= $this->calculaDesconto($total);
        }
        if ($this->frete > 0.0) {
            $total += $this->frete;
        }
        $this->total = $total;
        $this->calcularParcelas();
        return true;
    }

    public function salvaEmCookies(iCarrinhoSession $handle)
    {
        return $handle->store($this);
    }

    public function restauraDeCookies(iCarrinhoSession $handle,
                                      iCarrinhoProduto $modelProduto)
    {
        return $handle->setCarrinho($this)->setModelProdutos($modelProduto)->restore();
    }

    public function salvaEmSessao(iCarrinhoSession $handle)
    {
        return $handle->store($this);
    }

    public function restauraDaSessao(iCarrinhoSession $handle)
    {
        return $handle->setCarrinho($this)->restore();
    }

    public function salvarEmBancoDeDados(iCarrinhoDatabase $handle)
    {
        return $handle->save($this);
    }

    public function __clone()
    {
        $object                     = clone $this;
        $object->id                 = null;
        $object->itens              = [];
        $object->data_compra        = date('Y-m-d H:i:s');
        $object->cliente_id         = 0;
        $object->juros              = 0.0;
        $object->parcelas           = 0;
        $object->total              = 0.0;
        $object->total_com_parcelas = 0.0;
        $object->valor_parcela      = 0.0;
        $object->frete              = 0.0;
        $object->desconto_valor     = 0.0;
        $object->desconto_porcento  = 0.0;
        return $object;
    }
}