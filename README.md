# Carrinho de Compras em MVC

Conjunto de classes em PHP construidas com base na arquitetura MVC para Carrinho de compras. Não foi utilizando nenhum framework. A intenção é apresentar um pouco do meu estilo de linhas de código. Então é um principio de framework!
A classes contém métodos para cálculos de Parcelas com Juros. Utilizam inclusive alguns padrões de projetos.


## Getting Started

Neste projeto as classes Models e Controllers já estão em pleno funcionamento. Os Models estão herdando da classe Model que foi produzida com base no padrão de projetos Active Record. Além disso, a classe de conexão segue o padrão de projeto Singleton. O carrinho usa sessão para salvar o estado e pode ser salvo e recuperado do banco de dados a qualquer momento.

### Prerequisites

Você precisa ter instalado PHP e Postgres ou MySQL Server. Se estiver utilizando Linux, muitas vezes o LAMP lhe apresentará todo ambiente perfeito. No Windows, muitos costumam utilizar o XAMP.
O script foi desenvolvido utilizando Postgres e pode ser que haja alguma operação pouco compatível com MySQL.

Não faz parte desde documento, apresentar as etapas de instalação de cada elemento do ambiente.

### Installing

Após baixar o código, se estiver compactado, extrai-os e coloque-os no diretório de sua preferencia para rodar com o servidor web embutido no php.

Você deve ter acesso ao seu servidor MySQL e executar os scripts que estão no diretório App\Others para gerar as tabelas utilizadas pelos Models.

São eles:

* carrinho.sql
* itens_carrinho.sql
* produtos.sql

Além disso, você precisa configurar as definições do seu servidor no arquivo bootstrap.php, que fica na linha 11:

```php
 Connection::setCredentials('postgres', 'P@ssw0rd');
```

O que você definir em seu ambiente, deverá refletir aí. Então na sequencia, defina o usuário e senha que você utiliza, subistituindo os dados pré-existentes.

Certifique-se de rodar o composer para gerar o autoload e instalar dependencias:

```
 composer install
 
 composer dump-autoload
```


Após isso, você deve estar certo de que o script esteja em um diretório que possa ser lido pelo servidor web local ou, que tenha permissões suficiente de acesso ao diretório para utilizar o servidor embutido do php. 
Em geral para se utilizar o servidor embutido, utiliza-se o seguinte comando no diretório do projeto:

```
php -S localhost:8080

```

Após inicia o servidor embutido, será possível invocar o programa no browser através de um endereço URL como:

```
http://localhost:8080

```

Esteja atento ao detalhe que o ponto de partida do programa é no diretório public, local onde está o index.php.
Se desejar, há testes de assetions desenvolvidos no diretório testes. Desta maneira você poderá ter um melhor entendimento do funcionamento das classes.

## Usage

A melhor maneira de entender o funcionamento das classes é por analisar o funcionamento implementado no arquivo index.php:

Note que primeiramente um carrinho é instanciado:

```php
$carrinho = new Carrinho;

```

Depois, antes de adicionar ao carrinho, claro, alguns produtos são criados (Note a beleza do padrão Active Record!):

```php

for ($i = 1; $i <= 20; $i++) {
    $produto = new Produto;
    $produto->descricao = "Produto {$i}";
    $produto->preco = (float) rand(rand(0, 9), rand(0, 9));
    $produto->sku = rand(rand(111111111, 999999999), rand(111111111, 999999999));
    $produto->detalhes = "Este é o produto {$i} que foi salvo no banco de dados, o Model está perfeito!";
    $produto = $produto->save();
    //var_dump($produto);
}
```

Alguns testes e comentários foram mantidos no código:

```php

//var_dump($produto->delete());
$produtos = Produto::all();
//$produtos = Produto::find(1);
//var_dump($produtos);

```

Você pode entender como funcionam as operações sobre o banco de dados, como encontrar um produto ou excluí-lo. Além disso, o método all lhe devolve todos os produtos na tabela.

Possuíndo os produtos, podemos adicioná-los no carrinho:

```php

foreach ($produtos as $p) {
    $carrinho->adicionar($p, rand(rand(1, 99), rand(1, 99)));
}

```

Você pode analizar como os itens foram armazenados:

```php

//var_dump($carrinho);

```

Para definir meios de pagamentos, podemos configurar as parcelas e a taxa de juros:

```php

$carrinho->setParcelas(24);
$carrinho->setTaxaDeJuros(2.5);

```

Veja que há linhas abaixo que apresentam o resultado do cáculo:

```php

echo 'Valor total: R$ ' . number_format($carrinho->getTotal(), 2, ',', '.');
echo '<br>Valor total parcelado: R$ ' . number_format($carrinho->getTotalParcelado(), 2, ',', '.');
echo '<br>Parcelamento em ' . $carrinho->getNumeroParcelas() . 'x de R$ ' . number_format($carrinho->getValorParcela(), 2, ',', '.');

```

Outras situações foram testadas, quando não existe um produto:

```php

var_dump(Produto::find(1960));

```

E quando queremos excluir todos os produtos da tabela:

```php

$produtos = Produto::all();
foreach ($produtos as $p) {
    $p->delete();
    //var_dump($p->delete());
}

```

Também foi implementado um método para limpar a tabela:

```php

$p->truncate();
$produtos = Produto::all();
var_dump($produtos);

```


O ideal será você dar uma olhada nos testes e comentários na classe de testes:

tests/Framework/src/classes/CarrinhoTest.php



## Authors

* **Alexandre Bezerra Barbosa**
