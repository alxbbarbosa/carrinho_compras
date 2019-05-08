create table itens_carrinho (
	id SERIAL PRIMARY KEY,
	carrinho_id INT not NULL,
	produto_id INT not NULL,
	quantidade numeric default 1,
	CONSTRAINT fk_itens_carrinho FOREIGN KEY (carrinho_id) REFERENCES carrinho(id) ON DELETE CASCADE,
	CONSTRAINT fk_itens_produto FOREIGN KEY (produto_id) REFERENCES produto(id) ON DELETE CASCADE
);