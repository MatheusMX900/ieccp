<div class="menu-admin">
    <span class="brand">Painel IECCP</span>
    <nav>
        <a href="painel" class="<?= basename($_SERVER['PHP_SELF']) == 'painel.php' ? 'active' : '' ?>">📰 Notícias</a>
        <a href="painel_pastoral" class="<?= basename($_SERVER['PHP_SELF']) == 'painel_pastoral.php' ? 'active' : '' ?>">🐑 Pastoral</a>
        <a href="painel_agenda" class="<?= basename($_SERVER['PHP_SELF']) == 'painel_agenda.php' ? 'active' : '' ?>">📅 Agenda</a>
    </nav>
    <a href="logout" class="btn-logout">Sair</a>
</div>

<style>
    .menu-admin {
        background: #2c3e50;
        padding: 1rem 1.5rem;
        border-radius: 8px;
        margin-bottom: 2rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 1rem;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .brand {
        color: #fff;
        font-weight: 700;
        font-size: 1.2rem;
        border-right: 1px solid #4a6278;
        padding-right: 1.5rem;
    }

    .menu-admin nav a {
        color: #bdc3c7;
        text-decoration: none;
        padding: 0.5rem 1rem;
        border-radius: 4px;
        transition: all 0.2s;
        font-weight: 500;
    }

    .menu-admin nav a:hover {
        background: #34495e;
        color: #fff;
    }

    .menu-admin nav a.active {
        background: #27ae60;
        color: #fff;
    }

    .btn-logout {
        color: #e74c3c;
        font-weight: 700;
        text-decoration: none;
        border: 1px solid #e74c3c;
        padding: 0.4rem 1rem;
        border-radius: 4px;
        transition: 0.2s;
    }

    .btn-logout:hover {
        background: #e74c3c;
        color: #fff;
    }
</style>