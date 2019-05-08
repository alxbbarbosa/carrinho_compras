<?php

require __DIR__ . '/../bootstrap.php';

use \Abbarbosa\Gadgets\CarrinhoCompras\Classes\Carrinho as Carrinho;
use \App\Models\Produto as Produto;

$carrinho = new Carrinho;

for ($i = 1; $i <= 20; $i++) {
    $produto = new Produto;

    $produto->descricao = "Produto {$i}";
    $produto->preco = (float) rand(rand(0, 9), rand(0, 9));
    $produto->sku = rand(rand(111111111, 999999999), rand(111111111, 999999999));
    $produto->detalhes = "Este é o produto {$i} que foi salvo no banco de dados, o Model está perfeito!";

    $produto = $produto->save();
    //var_dump($produto);
}
//var_dump($produto->delete());

$produtos = Produto::all();
//$produtos = Produto::find(1);
//var_dump($produtos);



foreach ($produtos as $p) {
    $carrinho->adicionar($p, rand(rand(1, 99), rand(1, 99)));
}
//var_dump($carrinho);
$carrinho->setParcelas(24);
$carrinho->setTaxaDeJuros(2.5);

$i = 1;

?>
<h1>Classe Carrinho 2019</h1>
<hr>
<table width="100%" border="1">
	<thead>
		<tr>
			<th>item</th>
			<th>SKU</th>
			<th>Descrição</th>
			<th>Quantidade</th>
			<th>Preço Unitário</th>
		</tr>
	</thead>
	<tbody>
		<?php
        foreach ($carrinho->getItens() as $key => $item) {
            ?>
			<tr>
				<td><?= $i++ ?></td>
				<td><?= $item->produto->getSKU() ?></td>
				<td><?= $item->produto->getDescricao() ?></td>
				<td><?= $item->quantidade ?></td>
				<td>R$ <?= number_format($item->produto->getPreco(), 2, ',', '.') ?></td>
			</tr>
			<?php
        }
        ?>
	</tbody>
</table>
<?php



echo 'Valor total: R$ ' . number_format($carrinho->getTotal(), 2, ',', '.');
echo '<br>Valor total parcelado: R$ ' . number_format($carrinho->getTotalParcelado(), 2, ',', '.');
echo '<br>Parcelamento em ' . $carrinho->getNumeroParcelas() . 'x de R$ ' . number_format($carrinho->getValorParcela(), 2, ',', '.');


var_dump(Produto::find(1960));

$produtos = Produto::all();
foreach ($produtos as $p) {
    $p->delete();
    //var_dump($p->delete());
}
$p->truncate();
$produtos = Produto::all();
var_dump($produtos);
