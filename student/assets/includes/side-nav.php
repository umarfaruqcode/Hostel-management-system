<!-- /. NAV TOP  -->
<nav class="navbar-default navbar-side" role="navigation">
    <div class="sidebar-collapse">
        <ul class="nav" id="main-menu">

            <li <?php if(!isset($pgname) || (isset($pgname) && $pgname=="dashboard")) { echo 'class="active-link"'; } ?>>
                <a href="index.php" ><i class="fa fa-desktop "></i>Dashboard</a>
            </li>
            <li>
                <a href="book.php" <?php if(isset($pgname) && $pgname=="book") { echo 'class="active-link"'; } ?>><i class="fa fa-cart-plus"></i>Book Hostel</a>
            </li>
            <li>
                <a href="settings.php" <?php if(isset($pgname) && $pgname=="settings") { echo 'class="active-link"'; } ?>><i class="fa fa-cogs"></i>Settings</a>
            </li>
        </ul>
      </div>
</nav>
<!-- /. NAV SIDE  -->