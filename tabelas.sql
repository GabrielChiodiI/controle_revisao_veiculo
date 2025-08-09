CREATE TABLE clientes (
    id_cliente SERIAL PRIMARY KEY,
    nome VARCHAR(50),
    sobrenome VARCHAR(50),
    cpf VARCHAR(11),
    telefone VARCHAR(20),
    email VARCHAR(50),
    data_nascimento DATE,
    sexo VARCHAR(20)
);

CREATE TABLE veiculos (
    placa VARCHAR(15) PRIMARY KEY,
    marca VARCHAR(20),
    modelo VARCHAR(20),
    ano INTEGER,
    fk_cliente_id_cliente INTEGER,
    FOREIGN KEY (fk_cliente_id_cliente) REFERENCES clientes(id_cliente) ON DELETE CASCADE
);

CREATE TABLE servicos (
    id_servico SERIAL PRIMARY KEY,
    descricao VARCHAR(200),
    valor_mao_de_obra DOUBLE PRECISION
);

CREATE TABLE pecas (
    codigo SERIAL PRIMARY KEY,
    descricao VARCHAR(200),
    quantidade INTEGER,
    preco DOUBLE PRECISION
);

CREATE TABLE revisoes (
    id_revisao SERIAL PRIMARY KEY,
    data_inicio TIMESTAMP,
    data_fim TIMESTAMP,
    quilometragem INTEGER
);

CREATE TABLE realizas (
    fk_veiculo_placa VARCHAR(15),
    fk_revisao_id_revisao INTEGER,
    PRIMARY KEY (fk_veiculo_placa, fk_revisao_id_revisao),
    FOREIGN KEY (fk_veiculo_placa) REFERENCES veiculos(placa) ON DELETE CASCADE,
    FOREIGN KEY (fk_revisao_id_revisao) REFERENCES revisoes(id_revisao) CASCADE
);

CREATE TABLE contems (
    fk_servico_id_servico INTEGER,
    fk_revisao_id_revisao INTEGER,
    PRIMARY KEY (fk_servico_id_servico, fk_revisao_id_revisao),
    FOREIGN KEY (fk_servico_id_servico) REFERENCES servicos(id_servico) ON DELETE RESTRICT,
    FOREIGN KEY (fk_revisao_id_revisao) REFERENCES revisoes(id_revisao) ON DELETE CASCADE
);

CREATE TABLE precisas (
    fk_peca_codigo INTEGER,
    fk_servico_id_servico INTEGER,
    PRIMARY KEY (fk_peca_codigo, fk_servico_id_servico),
    FOREIGN KEY (fk_peca_codigo) REFERENCES pecas(codigo) ON DELETE SET NULL,
    FOREIGN KEY (fk_servico_id_servico) REFERENCES servicos(id_servico) ON DELETE SET NULL
);
