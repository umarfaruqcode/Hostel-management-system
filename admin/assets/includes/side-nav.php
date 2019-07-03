<!-- /. NAV TOP  -->
<nav class="navbar-default navbar-side" role="navigation">
    <div class="sidebar-collapse">
        <ul class="nav" id="main-menu">

            <li <?php if(!isset($pgname) || (isset($pgname) && $pgname=="dashboard")) { echo 'class="active-link"'; } ?>>
                <a href="index.php" ><i class="fa fa-desktop "></i>Dashboard</a>
            </li>
            <li>
                <a href="bookings.php" <?php if(isset($pgname) && $pgname=="bookings") { echo 'class="active-link"'; } ?>><i class="fa fa-cart-plus"></i>Bookings</a>
            </li><!-- 
            <li>
                <a href="cbookings.php" <?php if(isset($pgname) && $pgname=="cbookings") { echo 'class="active-link"'; } ?>><i class="fa fa-check-square-o"></i>Confirmed Bookings</a>
            </li> -->
            <li>
                <a href="hostels.php" <?php if(isset($pgname) && $pgname=="hostels") { echo 'class="active-link"'; } ?>><i class="fa fa-building-o"></i>Manager Hostels</a>
            </li>

            <li>
                <a href="students.php" <?php if(isset($pgname) && $pgname=="students") { echo 'class="active-link"'; } ?>><i class="fa fa-user"></i>Students</a>
            </li>
            <li>
                <a href="settings.php" <?php if(isset($pgname) && $pgname=="settings") { echo 'class="active-link"'; } ?>><i class="fa fa-cogs"></i>Settings</a>
            </li>
        </ul>
      </div>
</nav>
<!-- /. NAV SIDE  -->