ALTER TABLE projetstaches ADD ( visible INT );

UPDATE projetstaches set visible = 1;

ALTER TABLE projets ADD ( colorclass varchar(10),
						  devisarelancer INT );

UPDATE projets set devisarelancer = 0;

-- --------------------------------------------------
ALTER TABLE itemsdevis ADD ( json VARCHAR(2000) );
ALTER TABLE itemsdevis ADD ( typeligne VARCHAR(20) );

ALTER TABLE devis ADD (jsonEntete VARCHAR(2000) );