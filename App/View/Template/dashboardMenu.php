<nav id="menu_admin">
    <ul>
        Administrer le site :
        <?php if(isset($_SESSION['role']) && $_SESSION['role']> 2): ?>
        <li>
            <a href="<?=$this->route->generateURL('admin_article_index');?>"">Articles	</a>
        </li>
        <li>
            <a href="<?=$this->route->generateURL('admin_serie_index');?>">Séries</a>
        </li>
        <?php endif ?>
        <?php if(isset($_SESSION['role']) && $_SESSION['role']> 3): ?>
        <li>
            <a href="<?=$this->route->generateURL('admin_utilisateur_index');?>">Utilisateurs</a>
        </li>
        <?php endif ?>
        <li>
            <a href="<?=$this->route->generateURL('admin_control_panel');?>">Tableau de bord</a>
        </li>
    </ul>
</nav>