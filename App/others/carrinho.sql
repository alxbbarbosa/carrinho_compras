SET DATESTYLE TO ISO;
SET timezone = 'America/Sao_Paulo';
CREATE TABLE carrinho (
 	id  SERIAL PRIMARY KEY,
	cliente_id INT DEFAULT NULL,
	data_compra TIMESTAMP WITH TIME ZONE,
	juros NUMERIC(3, 2) DEFAULT 0.0,
	parcelas NUMERIC(3) DEFAULT 0,
	total NUMERIC(10, 2) DEFAULT 0.0,
	total_com_parcelas NUMERIC(10, 2) DEFAULT 0.0,
	valor_parcela NUMERIC(10, 2) DEFAULT 0.0,
	frete  NUMERIC(10, 2) DEFAULT 0.0,
	desconto_valor NUMERIC(10, 2) DEFAULT 0.0,
	desconto_porcento NUMERIC(3, 2) DEFAULT 0.0
);