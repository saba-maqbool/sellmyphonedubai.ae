<?php
    if (!isset($conn)) {
        require_once(__DIR__ . "/../include/db-connect.php");
    }
    $current = basename($_SERVER['PHP_SELF']);

    $custom_pages_nav = [];
    $nav_res = mysqli_query($conn, "SELECT id, title, slug FROM pages ORDER BY created_at DESC");
    if ($nav_res) {
        while ($nav_row = mysqli_fetch_assoc($nav_res)) {
            $custom_pages_nav[] = $nav_row;
        }
    }
    $editing_page_id = isset($_GET['edit']) ? (int) $_GET['edit'] : 0;
?>

<button class="sidebar-toggle-btn" id="sidebarToggleBtn" onclick="toggleSidebar()">
    <i class="fa-solid fa-bars"></i>
</button>
<div class="sidebar-overlay" id="sidebarOverlay" onclick="closeSidebar()"></div>

<div class="sidebar" id="adminSidebar">
    <a href="dashboard.php" class="sidebar-brand">
        <i class="fa-solid fa-mobile-button"></i>
        <span>SellMyPhone</span>
    </a>

    <ul class="sidebar-nav">
        <li><a href="dashboard.php" class="<?php echo $current == 'dashboard.php' ? 'active' : ''; ?>"><i class="fa-solid fa-house"></i> Dashboard</a></li>

        <?php
            $page_group_files = ['home-sections.php', 'about-sections.php', 'contact.php', 'blogs.php', 'pages.php'];
            $page_group_active = in_array($current, $page_group_files);
        ?>
        <li class="sidebar-group <?php echo $page_group_active ? 'open' : ''; ?>">
            <button type="button" class="sidebar-group-toggle <?php echo $page_group_active ? 'active' : ''; ?>" onclick="toggleSidebarGroup(this)">
                <i class="fa-solid fa-file-lines"></i>
                <span>Pages</span>
                <i class="fa-solid fa-chevron-down sidebar-group-caret"></i>
            </button>
            <ul class="sidebar-subnav">
                <li><a href="home-sections.php" class="<?php echo $current == 'home-sections.php' ? 'active' : ''; ?>"><i class="fa-solid fa-window-restore"></i> Home Page</a></li>
                <li><a href="about-sections.php" class="<?php echo $current == 'about-sections.php' ? 'active' : ''; ?>"><i class="fa-solid fa-circle-info"></i> About Page</a></li>
                <li><a href="blogs.php" class="<?php echo $current == 'blogs.php' ? 'active' : ''; ?>"><i class="fa-solid fa-newspaper"></i> Blogs</a></li>
                <li><a href="contact.php" class="<?php echo $current == 'contact.php' ? 'active' : ''; ?>"><i class="fa-solid fa-address-book"></i> Contact Page</a></li>

                <?php if (!empty($custom_pages_nav)): ?>
                    <li class="sidebar-subnav-divider">Custom Pages</li>
                    <?php foreach ($custom_pages_nav as $cp): ?>
                        <li><a href="pages.php" class="<?php echo ($current == 'pages.php') ? 'active' : ''; ?>"><i class="fa-solid fa-file"></i> <?php echo htmlspecialchars($cp['title']); ?></a></li>
                    <?php endforeach; ?>
                <?php endif; ?>

                <li><a href="pages.php" class="<?php echo $current == 'pages.php' ? 'active' : ''; ?>"><i class="fa-solid fa-plus"></i> Manage / Add Page</a></li>
            </ul>
        </li>

        <?php
            $brand_pages = ['iphone-page.php', 'samsung-page.php'];
            $brand_group_active = in_array($current, $brand_pages);
        ?>
        <li class="sidebar-group <?php echo $brand_group_active ? 'open' : ''; ?>">
            <button type="button" class="sidebar-group-toggle <?php echo $brand_group_active ? 'active' : ''; ?>" onclick="toggleSidebarGroup(this)">
                <i class="fa-solid fa-mobile-screen-button"></i>
                <span>Brands</span>
                <i class="fa-solid fa-chevron-down sidebar-group-caret"></i>
            </button>
            <ul class="sidebar-subnav">
                <li><a href="iphone-page.php" class="<?php echo $current == 'iphone-page.php' ? 'active' : ''; ?>"><i class="fa-brands fa-apple"></i> Sell iPhone Page</a></li>
                <li><a href="samsung-page.php" class="<?php echo $current == 'samsung-page.php' ? 'active' : ''; ?>"><i class="fa-solid fa-mobile"></i> Sell Samsung Page</a></li>
            </ul>
        </li>

        <li><a href="leads.php" class="<?php echo $current == 'leads.php' ? 'active' : ''; ?>"><i class="fa-solid fa-list"></i>Leads</a></li>

        <?php
            $catalog_pages = ['pricing.php', 'models.php'];
            $catalog_group_active = in_array($current, $catalog_pages);
        ?>
        <li class="sidebar-group <?php echo $catalog_group_active ? 'open' : ''; ?>">
            <button type="button" class="sidebar-group-toggle <?php echo $catalog_group_active ? 'active' : ''; ?>" onclick="toggleSidebarGroup(this)">
                <i class="fa-solid fa-layer-group"></i>
                <span>Catalog</span>
                <i class="fa-solid fa-chevron-down sidebar-group-caret"></i>
            </button>
            <ul class="sidebar-subnav">
                <li><a href="pricing.php" class="<?php echo $current == 'pricing.php' ? 'active' : ''; ?>"><i class="fa-solid fa-tag"></i> Pricing</a></li>
                <li><a href="models.php" class="<?php echo $current == 'models.php' ? 'active' : ''; ?>"><i class="fa-solid fa-mobile-screen"></i> Models</a></li>
            </ul>
        </li>

        <li><a href="admin-users.php" class="<?php echo $current == 'admin-users.php' ? 'active' : ''; ?>"><i class="fa-solid fa-user-shield"></i> Admin Users</a></li>
        <li><a href="site-settings.php" class="<?php echo $current == 'site-settings.php' ? 'active' : ''; ?>"><i class="fa-solid fa-gear"></i> Site Settings</a></li>
        <li><a href="page-meta.php" class="<?php echo $current == 'page-meta.php' ? 'active' : ''; ?>"><i class="fa-solid fa-magnifying-glass-chart"></i> Page SEO</a></li>
    </ul>

    <a href="logout.php" class="sidebar-logout">
        <i class="fa-solid fa-arrow-right-from-bracket"></i> Logout
    </a>
</div>