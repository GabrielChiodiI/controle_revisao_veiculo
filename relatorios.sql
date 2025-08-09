--Retorna todos os veiculos
SELECT marca, modelo, COUNT(*) AS quantidade
FROM veiculos
GROUP BY marca, modelo
ORDER BY quantidade DESC;

--Retorna todos os veículos por pessoas, em apenas duas colunas
SELECT 
  c.nome || ' ' || c.sobrenome AS pessoa, 
  v.marca || ' ' || v.modelo AS veiculo
FROM veiculos v
JOIN clientes c ON v.fk_cliente_id_cliente = c.id_cliente;

--Retorna qual sexo possui mais veículos, em caso de empate, retorna ambos
WITH totais AS (
  SELECT sexo, COUNT(v.placa) AS total
  FROM clientes c
  JOIN veiculos v ON c.id_cliente = v.fk_cliente_id_cliente
  GROUP BY sexo
)
SELECT sexo, total
FROM totais
WHERE total = (SELECT MAX(total) FROM totais);

--Retorna as marcas e a quantididade de veiculos ordenados pela quantidade
SELECT marca, COUNT(*) AS total
FROM veiculos
GROUP BY marca
ORDER BY total DESC;

--Retorna marca, quantidade e sexo, ordenados primeiro por sexo e depois por quantidade de veículos
SELECT 
  c.sexo AS "Sexo", 
  v.marca AS "Marca", 
  COUNT(*) AS "Quantidade"
FROM veiculos v
JOIN clientes c ON v.fk_cliente_id_cliente = c.id_cliente
GROUP BY c.sexo, v.marca
ORDER BY c.sexo ASC, COUNT(*) DESC;

--Retorna nome e sobrenome de todas as pessoas
SELECT nome || ' ' || sobrenome AS Pessoas FROM clientes;

--Retorna a quantidade de pessoas por sexo e media de idade
SELECT 
  sexo, 
  COUNT(*) AS total, 
  AVG(EXTRACT(YEAR FROM age(data_nascimento))) AS media_idade
FROM clientes
GROUP BY sexo;

--Retorna cada marca e o total de revisões, do maior para o menor
SELECT v.marca, COUNT(*) AS total
FROM revisoes r
JOIN realiza s ON r.id_revisao = s.fk_revisao_id_revisao
JOIN veiculos v ON s.fk_veiculo_placa = v.placa
GROUP BY v.marca
ORDER BY total DESC;

--Retorna id e datas de revisões, nome e sobrenome do cliente, e marca e modelo do veículo de todas as revisões realizadas em um determinado periodo
SELECT
  CONCAT(TO_CHAR(r.data_inicio, 'DD/MM/YYYY'), ' - ', TO_CHAR(r.data_fim, 'DD/MM/YYYY')) AS periodo,
  r.id_revisao,
  CONCAT(c.nome, ' ', c.sobrenome) AS cliente,
  CONCAT(v.marca, ' ', v.modelo) AS veiculo
FROM revisoes r
JOIN realizas rz ON r.id_revisao = rz.fk_revisao_id_revisao
JOIN veiculos v ON rz.fk_veiculo_placa = v.placa
JOIN clientes c ON v.fk_cliente_id_cliente = c.id_cliente
WHERE r.data_inicio BETWEEN '2025-01-01' AND '2025-08-07';

--Retorna o nome e sobrenome do cliente e a media de tempo entre revisões
SELECT 
  c.nome || ' ' || c.sobrenome AS pessoa,
  COUNT(*) AS total
FROM revisoes r
JOIN realiza s ON r.id_revisao = s.fk_revisao_id_revisao
JOIN veiculos v ON s.fk_veiculo_placa = v.placa
JOIN clientes c ON v.fk_cliente_id_cliente = c.id_cliente
GROUP BY c.id_cliente, c.nome, c.sobrenome
ORDER BY total DESC;

WITH revisoes_ordenadas AS (
    SELECT
        c.id_cliente,
        c.nome || ' ' || c.sobrenome AS cliente,
        r.data_inicio,
        r.data_fim,
        ROW_NUMBER() OVER (PARTITION BY c.id_cliente ORDER BY r.data_inicio) AS rn
    FROM revisoes r
    JOIN realizas s ON r.id_revisao = s.fk_revisao_id_revisao
    JOIN veiculos v ON s.fk_veiculo_placa = v.placa
    JOIN clientes c ON v.fk_cliente_id_cliente = c.id_cliente
    WHERE r.data_fim IS NOT NULL
),
pares_revisao AS (
    SELECT
        r1.id_cliente,
        r1.cliente,
        (r2.data_inicio::date - r1.data_fim::date) AS dias
    FROM revisoes_ordenadas r1
    JOIN revisoes_ordenadas r2
        ON r1.id_cliente = r2.id_cliente AND r2.rn = r1.rn + 1
),
clientes_media AS (
    SELECT
        c.id_cliente,
        c.nome || ' ' || c.sobrenome AS cliente,
        COALESCE(ROUND(AVG(p.dias)::numeric, 2), 0) AS "MediaTempoDias"
    FROM clientes c
    LEFT JOIN pares_revisao p ON c.id_cliente = p.id_cliente
    GROUP BY c.id_cliente, cliente
)
SELECT cliente AS "Cliente", "MediaTempoDias"
FROM clientes_media
ORDER BY cliente;

--Retorna o nome e sobrenome do cliente e data estimada para a próxima revisão com base na média das ultimas
WITH revisoes_ordenadas AS (
    SELECT
        c.id_cliente,
        c.nome || ' ' || c.sobrenome AS cliente,
        r.data_inicio,
        r.data_fim,
        ROW_NUMBER() OVER (PARTITION BY c.id_cliente ORDER BY r.data_inicio) AS rn,
        COUNT(*) OVER (PARTITION BY c.id_cliente) AS total_revisoes
    FROM revisoes r
    JOIN realizas s ON r.id_revisao = s.fk_revisao_id_revisao
    JOIN veiculos v ON s.fk_veiculo_placa = v.placa
    JOIN clientes c ON v.fk_cliente_id_cliente = c.id_cliente
    WHERE r.data_fim IS NOT NULL
),
pares_revisao AS (
    SELECT
        r1.id_cliente,
        r1.cliente,
        (r2.data_inicio::date - r1.data_fim::date) AS dias
    FROM revisoes_ordenadas r1
    JOIN revisoes_ordenadas r2
        ON r1.id_cliente = r2.id_cliente AND r2.rn = r1.rn + 1
),
medias_ultima_revisao AS (
    SELECT
        ro.id_cliente,
        ro.cliente,
        MAX(ro.data_fim::date) AS ultima_data_fim,
        COALESCE(ROUND(AVG(pr.dias)::numeric), 0) AS media_dias,
        MAX(ro.total_revisoes) AS total_revisoes
    FROM revisoes_ordenadas ro
    LEFT JOIN pares_revisao pr ON ro.id_cliente = pr.id_cliente
    GROUP BY ro.id_cliente, ro.cliente
)
SELECT
    cliente AS "Cliente",
    CASE 
        WHEN total_revisoes < 2 THEN NULL
        ELSE (ultima_data_fim + (media_dias || ' days')::interval)::date
    END AS "ProximaRevisao"
FROM medias_ultima_revisao
ORDER BY cliente;
