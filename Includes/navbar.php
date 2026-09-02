<?php
$current_page = $_GET['page'] ?? 'home';
require_once(__DIR__ . "/get-site-settings.php");
?>

<nav class="navbar navbar-expand-lg navbar-light ">
  <div class="container-fluid">

    <a href="./">
      <img class="navbar-img" src="<?php echo htmlspecialchars($site_settings['logo']); ?>?v=<?= file_exists(__DIR__ . '/../' . $site_settings['logo']) ? filemtime(__DIR__ . '/../' . $site_settings['logo']) : time(); ?>" alt="<?php echo htmlspecialchars($site_settings['logo_alt'] ?: $site_settings['site_title']); ?>">
    </a>

    <button class="navbar-toggler" type="button"
      data-bs-toggle="collapse"
      data-bs-target="#navbarNavDropdown"
      aria-controls="navbarNavDropdown"
      aria-expanded="false"
      aria-label="Toggle navigation">

      <span class="navbar-toggler-icon"></span>

    </button>

    <div class="collapse navbar-collapse" id="navbarNavDropdown">

      <ul class="navbar-nav">

        <li class="nav-item">
          <a class="nav-link <?= ($current_page == 'home') ? 'active' : ''; ?>"
            href="./">
            <?php echo htmlspecialchars($site_settings['nav_home_label']); ?>
          </a>
        </li>

        <li class="nav-item">
          <a class="nav-link <?= ($current_page == 'about') ? 'active' : ''; ?>"
            href="about">
            <?php echo htmlspecialchars($site_settings['nav_about_label']); ?>
          </a>

        </li>

                <li class="nav-item">
          <a class="nav-link <?= ($current_page == 'apple') ? 'active' : ''; ?>"
            href="apple">
            Sell Apple
          </a>
        </li>

                <li class="nav-item">
          <a class="nav-link <?= ($current_page == 'samsung') ? 'active' : ''; ?>"
            href="samsung">
            Sell Samsung
          </a>
        </li>
        
        <li class="nav-item">
          <a class="nav-link <?= ($current_page == 'testimonials') ? 'active' : ''; ?>"
            href="./#valuation-step">
            <?php echo htmlspecialchars($site_settings['nav_quote_label']); ?>
          </a>
        </li>

        <li class="nav-item">
          <a class="nav-link <?= ($current_page == 'blogs') ? 'active' : ''; ?>"
            href="blog">
            <?php echo htmlspecialchars($site_settings['nav_blogs_label']); ?>
          </a>
        </li>

        <li class="nav-item">
          <a class="nav-link <?= ($current_page == 'testimonials') ? 'active' : ''; ?>"
            href="./#testimonials">
            <?php echo htmlspecialchars($site_settings['nav_testimonials_label']); ?>
          </a>
        </li>

        <li class="nav-item">
          <a class="nav-link <?= ($current_page == 'contact') ? 'active' : ''; ?>"
            href="contact">
            <?php echo htmlspecialchars($site_settings['nav_contact_label']); ?>
          </a>
        </li>
           <li class="nav-item">
          <a class="nav-link nav-whatsapp-link"
            href="https://wa.me/<?php echo preg_replace('/\D/', '', $site_settings['footer_whatsapp']); ?>"
            target="_blank" rel="noopener" aria-label="Chat on WhatsApp">
            <i class="fab fa-whatsapp"></i>
            <?php echo htmlspecialchars($site_settings['footer_whatsapp']); ?>
          </a>
        </li>
      </ul>
    </div>

  </div>
</nav>