ALTER TABLE projetstaches ADD ( visible INT );

UPDATE projetstaches set visible = 1;

ALTER TABLE projets ADD ( colorclass varchar(10),
						  devisarelancer INT );

UPDATE projets set devisarelancer = 0;

-- --------------------------------------------------
