CREATE TABLE IF NOT EXISTS presente_diario (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            data_publicacao TEXT UNIQUE NOT NULL,
            titulo TEXT,
            referencia_bilbica TEXT,
            versiculo_chave TEXT,
            conteudo TEXT,
            autor TEXT,
            frase_destaque TEXT,
            importado_em DATETIME DEFAULT CURRENT_TIMESTAMP
        )"