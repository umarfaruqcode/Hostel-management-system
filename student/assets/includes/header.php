 <div class="navbar navbar-inverse navbar-fixed-top">
    <div class="adjust-nav">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".sidebar-collapse">
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="#">
                <!-- <img src="assets/img/logo.png" /> -->
                <h2 class="" style="color: white; margin: 10px 0px; font-weight: 600;"><img src="../images/unilorin-logo.png" width="45" style="float: left;" /> &nbsp;University of Ilorin</h2>
            </a>
            
        </div>
        <br>
        <span class="logout-spn" style="font-size: 110%;">
            <?= $surname.", ".$othernames; ?> | 
          <a href="<?= APP_ROOT ?>logout.php?role=student&r=1" style="color:#fff;"><i class="fa fa-sign-out"></i> LOGOUT</a>
        </span>
    </div>
</div>