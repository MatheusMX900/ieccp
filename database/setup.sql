CREATE TABLE admins (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    usuario TEXT NOT NULL UNIQUE,
    senha TEXT NOT NULL,
    session_token TEXT,
    ultimo_acesso DATETIME
    ultimo_ip TEXT,
    user_agent TEXT,
    tentativas_falhas INTEGER DEFAULT 0
);

-- índice no token para otimizar a busca
CREATE INDEX idx_session_token ON admins(session_token);